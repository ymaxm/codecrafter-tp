<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Test extends BaseController
{



    public function getUser()
    {
        $result = Db::table("user")->where(1)->find();
        return json($result);
    }
}