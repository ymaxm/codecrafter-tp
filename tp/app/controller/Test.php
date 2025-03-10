<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Test extends BaseController
{


    public function test($id){
        $result = Db::table("post")->select()->toArray();
        return json(addSingleValue($result,"test_value",$id));
    }

}