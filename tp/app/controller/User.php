<?php

namespace app\controller;


use app\BaseController;
use think\facade\Db;
use think\facade\Session;

class User extends BaseController
{
    public function getCode($email = "")
    {
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
        $result = Db::table("user")->where("username",$username)->whereOr("email",$email)->whereOr("ip",$this->get_client_ip())->find();
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
            "uid" => md5(uniqid(rand(), true)),
            "username" => $username,
            "password" => md5($password),
            "email" => $email,
            "login" => md5(uniqid(rand(), true)),
            "ip" => $this->get_client_ip()
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
    function get_client_ip($type = 0) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if(isset($_SERVER['HTTP_X_REAL_IP'])){//nginx 代理模式下，获取客户端真实IP
            $ip=$_SERVER['HTTP_X_REAL_IP'];
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
        }else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}