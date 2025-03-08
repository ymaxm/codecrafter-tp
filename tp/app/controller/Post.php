<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Session;

class Post extends BaseController
{
    public function addPost($title = "",$text = "",){
        if(!limitSpeed(4,10,60*60))
        {
            $return = array(
                "code" => 2,
                "msg" => "操作速度过快!请稍后再试"
            );
            return json($return);
        }
        if(!Session::has("username"))
        {
            $return = array(
                "code" => 2,
                "msg" => "您尚未登录"
            );
            return json($return);
        }
        $permission = Db::table("user")->where(session("userid"))->value("permission");
        if($permission < 1)
        {
            $return = array(
                "code" => 2,
                "msg" => "权限不足"
            );
            return json($return);
        }
        if($title == "" or $text == "")
        {
            $return = array(
                "code" => 2,
                "msg" => "标题或内容不能为空"
            );
            return json($return);
        }
        $result = Db::table("post")->insert([
            "title" => $title,
            "text" => $text,
            "uid" => getUID(),
            "user" => session("userid")
        ]);
        if($result){
            $return = array(
                "code" => 1,
                "msg" => "发布成功"
            );
            return json($return);
        }
        else{
            $return = array(
                "code" => 2,
                "msg" => "发生意外错误"
            );
            return json($return);
        }
    }
    public function getPostList($page = 1){
        if($page <= 0)
        {
            $page = 1;
        }
        $result = Db::table("post")->limit(($page - 1) * 10,10)->select();
        if($result <> "[]")
        {
            $result = $result->toArray();
            $return = array(
                "code" => 1,
                "msg" => $result
            );
            return json($return);
        }
        else{
            $return = array(
                "code" => 2,
                "msg" => "没有数据"
            );
            return json($return);
        }
    }
}