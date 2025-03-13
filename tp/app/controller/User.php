<?php

namespace app\controller;


use app\BaseController;
use think\facade\Db;
use think\facade\Session;

class User extends BaseController
{
    public function getCode()
    {
        $email = request()->post('email');
        if(!limitSpeed(2,5,60*10))
        {
            $return = array(
                "code" => 2,
                "msg" => "操作速度过快!请稍后再试",
                "data" => []
            );
            return json($return);
        }
        if($email == "")
        {
            $return = array(
                "code" => 2,
                "msg" => "邮箱不能为空",
                "data" => []
            );
            return json($return);
        }
        if(Session::has("code"))
        {
            $return = array(
                "code" => 2,
                "msg" => "您已获取过验证码",
                "data" => []
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
                "msg" => $rand,
                "data" => []
            )
        );
    }
    public function register(){
        $username = request()->param("username");
        $password = request()->param("password");
        $email = request()->param("email");
        $code = request()->param("code");
        $username = str_replace(" ","",$username);
        $email = str_replace(" ","",$email);
        if(!limitSpeed(3,10,60*60))
        {
            $return = array(
                "code" => 2,
                "msg" => "操作速度过快!请稍后再试",
                "data" => []
            );
            return json($return);
        }
        if($username == "" and $password == "" and $email == ""){
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "用户名,密码,邮箱不能为空",
                "data" => []
            );
            return json($return);
        }
        if($code <> session("code"))
        {
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "验证码错误",
                "data" => []
            );
            return json($return);
        }
        if(session("code_email") <> $email)
        {
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "验证码错误",
                "data" => []
            );
            return json($return);
        }
        if(mb_strlen($username) > 20 or mb_strlen($username) < 2){
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "用户名必须大于2字小于20字",
                "data" => []
            );
            return json($return);
        }
        if(strstr($email, '@') == null){
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "邮箱格式不正确",
                "data" => []
            );
            return json($return);
        }
        if($username == $password or $password == $email)
        {
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "用户名或邮箱不能等于密码",
                "data" => []
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
                "msg" => "该用户名或邮箱已被注册,或您的ip已被注册",
                "data" => []
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
                "msg" => "注册成功",
                "data" => []
            );
            return json($return);
        }
        else{
            Session::delete("code");
            $return = array(
                "code" => 2,
                "msg" => "发生意外错误",
                "data" => []
            );
            return json($return);
        }
    }
    public function login()
    {
        $username = request()->param("username");
        $password = request()->param("password");
        if(!limitSpeed(5,10,60*10))
        {
            $return = array(
                "code" => 2,
                "msg" => "操作速度过快!请稍后再试",
                "data" => []
            );
            return json($return);
        }
        if(Session::has("username"))
        {
            $return = array(
                "code" => 2,
                "msg" => "您已登录",
                "data" => []
            );
            return json($return);
        }

        if($username == "" or $password == "")
        {
            $return = array(
                "code" => 2,
                "msg" => "用户名和密码不能为空",
                "data" => []
            );
            return json($return);
        }

        $result = Db::table("user")->where("username",$username)->where("password",md5($password))->find();
        if($result <> null)
        {
            $userid = Db::table("user")->where("username",$username)->value("id");
            Db::table("user")->where("id",$userid)->update(
                ["login" => getUID()]
            );
            Session::set('userid', $userid);
            Session::set('username', $username);
            $return = array(
                "code" => 1,
                "msg" => "登录成功",
                "data" => []
            );
            return json($return);
        }
        else{
            $return = array(
                "code" => 2,
                "msg" => "用户名或密码错误",
                "data" => []
            );
            return json($return);
        }
    }
    public function logout(){
        if(!Session::has("username"))
        {
            $return = array(
                "code" => 2,
                "msg" => "您未登录",
                "data" => []
            );
            return json($return);
        }
        Session::clear();
        $return = array(
            "code" => 1,
            "msg" => "登出成功",
            "data" => []
        );
        return json($return);
    }
    public function resetPassword(){
        $email = request()->post("email");
        $new_password = request()->post("new_password");
        $code = request()->post("code");
        if($new_password == "")
        {
            Session::delete("code");
            Session::delete("code_email");
            $return = array(
                "code" => 2,
                "msg" => "密码不能为空",
                "data" => []
            );
            return json($return);
        }
        if($email <> session("code_email"))
        {
            Session::delete("code");
            Session::delete("code_email");
            $return = array(
                "code" => 2,
                "msg" => "验证码错误",
                "data" => []
            );
            return json($return);
        }
        if($code <> session("code"))
        {
            Session::delete("code");
            Session::delete("code_email");
            $return = array(
                "code" => 2,
                "msg" => "验证码错误",
                "data" => []
            );
            return json($return);
        }
        $userid = Db::table("user")->where("email",$email)->value("id",0);
        $userpsw = Db::table("user")->where("email",$email)->value("password","");
        if($userid == 0)
        {
            Session::delete("code");
            Session::delete("code_email");
            $return = array(
                "code" => 2,
                "msg" => "用户不存在",
                "data" => []
            );
            return json($return);
        }
        else{
            if(md5($new_password) == $userpsw)
            {
                Session::delete("code");
                Session::delete("code_email");
                $return = array(
                    "code" => 2,
                    "msg" => "密码不能与原密码一致",
                    "data" => []
                );
                return json($return);
            }
            $result = Db::table("user")->where("id",$userid)->update(
                [
                    "password" => md5($new_password),
                    "login" => getUID()
                ]
            );
            if($result){
                Session::delete("code");
                Session::delete("code_email");
                $return = array(
                    "code" => 1,
                    "msg" => "修改成功",
                    "data" => []
                );
                return json($return);
            }
            else{
                Session::delete("code");
                Session::delete("code_email");
                $return = array(
                    "code" => 2,
                    "msg" => "修改失败",
                    "data" => []
                );
                return json($return);
            }
        }
    }
    public function getUserInfo($user_uid = ''){
        if($user_uid == '')
        {
            if(Session::has("userid"))
            {
                $user_uid = Db::table("user")->where("id",session("userid"))->value("uid");
                $result = Db::table("user")->where("uid",$user_uid)->withoutField(["id","password"])->select()->toArray();
            }
            else{
                $return = array(
                    "code" => 2,
                    "msg" => "您尚未登录",
                    "data" => []
                );
                return json($return);
            }
        }
        else{
            $result = Db::table("user")->where("uid",$user_uid)->withoutField(["id","ip","login","password"])->select()->toArray();
        }
        $return = array(
            "code" => 1,
            "msg" => "成功",
            "data" => $result
        );

        return json($return);

    }
    public function checkLogin(){
        if(Session::has("userid"))
        {
            $result = Db::table("user")->where("id",session("userid"))->withoutField(["id","password"])->select()->toArray();
            $return = array(
                "code" => 1,
                "msg" => "已登录",
                "data" => $result
            );
            return json($return);
        }
        else{
            $return = array(
                "code" => 2,
                "msg" => "未登录",
                "data" => []
            );
            return json($return);
        }
    }
}