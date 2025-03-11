<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | Workerman设置 仅对 php think worker:server 指令有效
// +----------------------------------------------------------------------
$connect_user = array();
return [
    // 扩展自身需要的配置
    'protocol'       => 'websocket', // 协议 支持 tcp udp unix http websocket text
    'host'           => '0.0.0.0', // 监听地址
    'port'           => 2345, // 监听端口
    'socket'         => '', // 完整监听地址
    'context'        => [], // socket 上下文选项
    'worker_class'   => '', // 自定义Workerman服务类名 支持数组定义多个服务

    // 支持workerman的所有配置参数
    'name'           => 'thinkphp',
    'count'          => 4,
    'daemonize'      => false,
    'pidFile'        => '',

    // 支持事件回调
    // onWorkerStart
    'onWorkerStart'  => function ($worker) {

    },
    // onWorkerReload
    'onWorkerReload' => function ($worker) {

    },
    // onConnect
    'onConnect'      => function ($connection) {
        $connection->send("success");
    },
    // onMessage
    'onMessage'      => function ($connection, $data) {
        global $connect_user;
        $get_data = json_decode($data, true);
        if(!isset($get_data['user_login']) or !isset($get_data['user']) or !isset($get_data['msg']) or !isset($get_data['to'])){
            $connection->send('{"code":2,"msg":"参数不能为空","data":[]}');
        }
        else {
            $login_token = \think\facade\Db::table("user")->where("uid", $get_data['user'])->value("login");
            if ($login_token <> $get_data['user_login']) {
                $connection->send('{"code":2,"msg":"登录信息有误,请尝试重新登录","data":[]}');
            } else {
                //判断用户登录信息已正确
                $connect_user[$get_data['user']] = $connection;
                //开始判断被发送用户是否存在
                $to_id = \think\facade\Db::table("user")->where("uid", $get_data['to'])->value("id",0);
                if($to_id == 0)
                {
                    //被发送用户不存在
                    $connection->send('{"code":2,"msg":"被发送者不存在","data":[]}');
                }
                else {
                    //被发送的用户存在
                    if(!isset($connect_user[$get_data['to']]))
                    {
                        $myid = \think\facade\Db::table("user")->where("uid", $get_data['user'])->value("id");
                        //被发送者未上线
                        $result = \think\facade\Db::table("message")->insert(
                            [
                                "uid" => getUID(),
                                "send" => $myid,
                                "sendto" => $to_id,
                                "msg" => $get_data['msg'],
                            ]
                        );
                        if($result)
                        {
                            $connection->send('{"code":1,"msg":"发送成功,离线发送","data":[]}');
                        }
                        else{
                            $connection->send('{"code":2,"msg":"发送时出现意外错误","data":[]}');
                        }
                    }
                    else{
                        //被发送者已上线
                        $connect_user[$get_data['to']]->send($get_data['msg']);
                        $myid = \think\facade\Db::table("user")->where("uid", $get_data['user'])->value("id");
                        $result = \think\facade\Db::table("message")->insert(
                            [
                                "uid" => getUID(),
                                "send" => $myid,
                                "sendto" => $to_id,
                                "msg" => $get_data['msg'],
                            ]
                        );
                        if($result)
                        {
                            $connection->send('{"code":1,"msg":"发送成功,在线发送","data":[]}');
                        }
                        else{
                            $connection->send('{"code":2,"msg":"发送时出现意外错误","data":[]}');
                        }
                    }


                }
            }
        }
    },
    // onClose
    'onClose'        => function ($connection) {
        global $connect_user;

        $key = array_search($connection, $connect_user);
        if ($key !== false) {
            unset($connect_user[$key]);
        }

    },
    // onError
    'onError'        => function ($connection, $code, $msg) {
        echo "error [ $code ] $msg\n";
    },
];
