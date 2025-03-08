<?php
// 应用公共文件

function getUID(){
    return md5(uniqid(rand(), true));
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
function timetostr($time){
    return date("Y-m-d H:i:s",$time);
}

function limitSpeed($type,$max_count,$time)
{
    $result = \think\facade\Db::table("limitSpeed")->where("ip",get_client_ip())->where("type",$type)->find();
    if($result == null)
    {
        $result = \think\facade\Db::table("limitSpeed")->insert([
            "type" => $type,
            "ip" => get_client_ip(),
            "count" => 1,
            "end_time" => timetostr(time() + $time),
        ]);
        return true;
    }
    if(strtotime($result['end_time']) >= time())
    {
        if($result['count'] >= $max_count)
        {
            return false;
        }
        else{
            $result = \think\facade\Db::table("limitSpeed")->where("ip",get_client_ip())->where("type",$type)->update(
                [
                    "count" => $result['count'] + 1,
                    "end_time" => timetostr(time() + $time),
                ]
            );
            if($result)
            {
                return true;
            }
            else{
                return false;
            }
        }
    }
    else{
        \think\facade\Db::table("limitSpeed")->where("ip",get_client_ip())->where("type",$type)->delete();
        return true;
    }

}