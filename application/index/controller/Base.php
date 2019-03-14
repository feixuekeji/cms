<?php
/**
 * Created by PhpStorm.
 * User: moTzxx
 * Date: 2018/1/17
 * Time: 19:02
 */

namespace app\index\controller;

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
        //$this->checkToken();
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
        //$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NTI1Mzk3NDAsIm5iZiI6MTU1MjUzOTc0MCwic2NvcGVzIjoicm9sZV9hY2Nlc3MiLCJleHAiOjE1NTI1NDY5NDAsImRhdGEiOnsiaXAiOiIxMjcuMC4wLjEifX0.WEBP-MBOIOM86Au1Nnq-3plevfpK7OA3NDx3RiK7-E1c";
        $checkToken = action('api/token/checkToken',['jwt'=>$token]);
        if ($checkToken['data']['ip']!= request()->ip())
        {
            $res['code']="202";
            $res['msg']="ip不一致";
            $res['data']="";//返回的数据
            exit(json_encode($res));
        }

    }

}