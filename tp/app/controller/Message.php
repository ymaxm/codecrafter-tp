<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Session;

class Message extends BaseController
{
    public function getMessage()
    {
        if(!Session::has("userid")){
            $return = array(
                "code" => 2,
                "msg" => "您尚未登录",
                "data" => []
            );
            return json($return);
        }
        $result = Db::table("message")->where("send",session("userid"))->whereOr("sendto",session("userid"))->withoutField("id")->select()->toArray();

        foreach($result as $each_result){
            if($each_result['sendto']==session("userid"))
            {
                $others = $each_result['send'];
            }
            else{
                $others = $each_result['sendto'];
            }
            $others_name = Db::table("user")->where("id",$others)->value("username");
            $result1[$others_name][] = $each_result;
        }
        $return_data = array(
            "code" => 1,
            "msg" => "成功",
            "whoisme" => session("userid"),
            "data" => $result1
        );
        return json($return_data);

    }
}