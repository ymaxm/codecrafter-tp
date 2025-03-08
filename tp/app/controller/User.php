<?php

namespace app\controller;


use app\BaseController;
use think\facade\Db;
use think\facade\Session;

class User extends BaseController
{
    public function getCode($email = "")
    {
        if(!limitSpeed(2,5,60*10))
        {
            $return = array(
                "code" => 2,
                "msg" => "操作速度过快!请稍后再试"
            );
            return json($return);
        }
        if($email == "")
        {
            $return = array(
                "code" => 2,
                "msg" => "邮箱不能为空"
            );
            return json($return);
        }
        if(Session::has("code"))
        {
            $return = array(
                "code" => 2,
                "msg" => "您已获取过验证码"
            );
            return json($return);
        }
        $randStr = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand = substr($randStr,0,6);
        session("code",$rand);
        session("code_email",$email);
        return json(
            array(
                "code" => 1,
                "msg" => $rand
            )
        );
    }
    public function register($username = "",$password = "",$email = "",$code = ""){
        if(!limitSpeed(3,10,60*60))
        {
            $return = array(
                "code" => 2,
                "msg" => "操作速度过快!请稍后再试"
            );
            return json($return);
        }
        if($username == "" and $password == "" and $email == ""){
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "用户名,密码,邮箱不能为空"
            );
            return json($return);
        }
        if($code <> session("code"))
        {
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "验证码错误"
            );
            return json($return);
        }
        if(session("code_email") <> $email)
        {
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "验证码错误"
            );
            return json($return);
        }
        if(mb_strlen($username) > 20 or mb_strlen($username) < 2){
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "用户名必须大于2字小于20字"
            );
            return json($return);
        }
        if(strstr($email, '@') == null){
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "邮箱格式不正确"
            );
            return json($return);
        }
        if($username == $password or $password == $email)
        {
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "用户名或邮箱不能等于密码"
            );
            return json($return);
        }

        //判断用户是否被注册
        $result = Db::table("user")->where("username",$username)->whereOr("email",$email)->whereOr("ip",get_client_ip())->find();
        if($result <> null)
        {
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "该用户名或邮箱已被注册,或您的ip已被注册"
            );
            return json($return);
        }

        $result = Db::table("user")->insert([
            "uid" => getUID(),
            "username" => $username,
            "password" => md5($password),
            "email" => $email,
            "login" => getUID(),
            "ip" => get_client_ip()
        ]);
        if($result){
            Session::delete("code");
            $return = array(
                "code" => 1,
                "msg" => "注册成功"
            );
            return json($return);
        }
        else{
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "发生意外错误"
            );
            return json($return);
        }
    }
    public function login($username = "",$password = "")
    {
        if(!limitSpeed(5,10,60*10))
        {
            $return = array(
                "code" => 2,
                "msg" => "操作速度过快!请稍后再试"
            );
            return json($return);
        }
        if(Session::has("username"))
        {
            $return = array(
                "code" => 2,
                "msg" => "您已登录"
            );
            return json($return);
        }

        if($username == "" or $password == "")
        {
            $return = array(
                "code" => 2,
                "msg" => "用户名和密码不能为空"
            );
            return json($return);
        }

        $result = Db::table("user")->where("username",$username)->where("password",md5($password))->find();
        if($result <> null)
        {
            $userid = Db::table("user")->where("username",$username)->value("id");
            Session::set('userid', $userid);
            Session::set('username', $username);
            $return = array(
                "code" => 1,
                "msg" => "登录成功"
            );
            return json($return);
        }
        else{
            $return = array(
                "code" => 2,
                "msg" => "用户名或密码错误"
            );
            return json($return);
        }
    }
    public function logout(){
        if(!Session::has("username"))
        {
            $return = array(
                "code" => 2,
                "msg" => "您未登录"
            );
            return json($return);
        }
        Session::clear();
        $return = array(
            "code" => 1,
            "msg" => "登出成功"
        );
        return json($return);
    }
    public function checkLogin(){
        if(Session::has("userid"))
        {
            $return = array(
                "code" => 1,
                "msg" => "已登录"
            );
            return json($return);
        }
        else{
            $return = array(
                "code" => 2,
                "msg" => "未登录"
            );
            return json($return);
        }
    }
}