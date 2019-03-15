<?php
/**
 * Created by PhpStorm.
 * User: moTzxx
 * Date: 2018/1/17
 * Time: 19:02
 */

namespace app\index\controller;

use function Qiniu\explodeUpToken;
use think\Controller;
use think\facade\Session;
use think\Request;
use think\facade\Hook;


/**
 * Class Base
 * @package app\index\controller
 */
class Base extends Controller
{
    /**
     * 允许跨域请求
     * Base constructor.
     */
    public function __construct()
    {

        Hook::listen('response_send');
        $this->checkToken();
    }


    /**
     * Notes:检查token
     * User: xxf
     * Date: 2019/3/14
     * Time: 13:31
     * @return string
     */
    public function checkToken()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION']))
        {
            $res['code']="201";
            $res['msg']="no token";
            $res['data']="";//返回的数据
            exit(json_encode($res));
        }
        $token = $_SERVER['HTTP_AUTHORIZATION'];
        //$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NTI2MTMxODMsIm5iZiI6MTU1MjYxMzE4Mywic2NvcGVzIjoicm9sZV9hY2Nlc3MiLCJleHAiOjE1NTI2MjAzODMsImRhdGEiOnsiaXAiOiIxMjcuMC4wLjEifX0.iN2YO9w6TQP3g4rc2-P9KpiHRj5UhRZ8MWyrkrZLIF8";
        $checkToken = action('api/token/checkToken',['jwt'=>$token]);
        $data = (array)$checkToken['data']['data'];
        $ip = $data['ip'] ?? 0 ;
        if ($ip!= request()->ip())
        {
            $res['code']="202";
            $res['msg']="ip不一致";
            $res['data']="";//返回的数据
            exit(json_encode($res));
        }

    }

}