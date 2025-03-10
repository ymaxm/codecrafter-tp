<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Session;

class Post extends BaseController
{
    public function addPost($title = "",$text = "",$plate = 0){
        if(!limitSpeed(4,10,60*60))
        {
            $return = array(
                "code" => 2,
                "msg" => "操作速度过快!请稍后再试",
                "data" => []
            );
            return json($return);
        }
        if(!Session::has("username"))
        {
            $return = array(
                "code" => 2,
                "msg" => "您尚未登录",
                "data" => []
            );
            return json($return);
        }
        $permission = Db::table("user")->where(session("userid"))->value("permission");
        if($permission < 1)
        {
            $return = array(
                "code" => 2,
                "msg" => "权限不足",
                "data" => []
            );
            return json($return);
        }
        if($title == "" or $text == "" or $plate == 0)
        {
            $return = array(
                "code" => 2,
                "msg" => "标题或内容或板块不能为空",
                "data" => []
            );
            return json($return);
        }
        $result = Db::table("post")->insert([
            "title" => $title,
            "text" => $text,
            "uid" => getUID(),
            "user" => session("userid"),
            "plate" => $plate
        ]);
        if($result){
            $return = array(
                "code" => 1,
                "msg" => "发布成功",
                "data" => []
            );
            return json($return);
        }
        else{
            $return = array(
                "code" => 2,
                "msg" => "发生意外错误",
                "data" => []
            );
            return json($return);
        }
    }
    public function getPostList($page = 1){
        if($page <= 0)
        {
            $page = 1;
        }
        $user_info = getUser();
        if($user_info == null)
        {
            $permission = 0;
        }
        else{
            $permission = $user_info["permission"];
        }

        $where = array();
        $where = Db::table("plate")->where("permission","<=",$permission)->column("id");

        $result = Db::table("post")->limit(($page - 1) * 10,10)->whereIn("plate",$where)->withoutField("id")->select()->toArray();
        if(!empty($result))
        {
            $return = [
                "code" => 1,
                "msg" => "成功",
                "data" => $result
            ];
            return json($return);
        }
        else{
            $return = array(
                "code" => 2,
                "msg" => "没有数据",
                "data" => []
            );
            return json($return);
        }
    }
    public function getPlateList(){
        $user_info = getUser();
        if($user_info == null)
        {
            $permission = 0;
        }
        else{
            $permission = $user_info["permission"];
        }
        $result = Db::table("plate")->where("permission","<=",$permission)->select()->toArray();
        if(empty($result))
        {
            $return = array(
                "code" => 2,
                "msg" => "无数据",
                "data" => []
            );
            return json($return);
        }
        else {
            $return = array(
                "code" => 1,
                "msg" => "成功",
                "data" => $result
            );
            return json($return);
        }
    }
    public function searchPost($keyword){
        $permission = getUserPermission();
        $where = array();
        $where = Db::table("plate")->where("permission","<=",$permission)->column("id");

        $result = Db::table("post")->where("title","like","%".$keyword."%")->whereIn("plate",$where)->select()->toArray();

        if(!empty($result))
        {
            $return = array(
                "code" => 1,
                "msg" => "成功",
                "data" => $result
            );
            return json($return);
        }
        else{
            $return = array(
                "code" => 2,
                "msg" => "无数据",
                "data" => []
            );
            return json($return);
        }
    }
    public function getPost($uid){
        $result = Db::table("post")->where("uid",$uid)->find();
        if($result == null)
        {
            $return = array(
                "code" => 2,
                "msg" => "无数据",
                "data" => []
            );
            return json($return);
        }
        return 'Test API';
    }
}