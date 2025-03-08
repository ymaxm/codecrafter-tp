<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Test extends BaseController
{


    public function test(){
        return json(Db::query("SELECT * FROM post WHERE plate IN (SELECT id FROM plate WHERE permission <= ?)",[1]));

    }

}