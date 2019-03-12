<?php

namespace app\api\Controller;

use think\Controller;
use think\Request;
use Config;


/**神箭手相关API
 * Class Shenjianshou
 * @package app\api\Controller
 */
class Shenjianshou extends Controller
{
    private $newApiUrl;
    private $points_url;

    public function __construct()
    {
        $this->newApiUrl = "https://api.shenjian.io/?appid=3f2c8e443f9280838b8ad5d10b4f3342";//最新文章
        $this->points_url = "https://api.shenjian.io/?appid=9b26135fdfe3a1082c2f963160c55f33";//阅读量点击数
    }


    /**获取最新文章
     * @param $weixinId
     * @return mixed
     */
    public function getNewArticle($weixinId)
    {

        $url = $this->newApiUrl;
        $weixinId=urlencode($weixinId);
        $url = $url . "&weixinId=" . $weixinId;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Encoding:gzip'));

        curl_setopt($ch, CURLOPT_ENCODING, "gzip");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res,true);
        return $res;

    }


    /**获取阅读量点赞数
     * @param $weixinId
     * @return mixed
     */
    public function getView($articleUrl)
    {

        $url = $this->points_url;
        $articleUrl=urlencode($articleUrl);
        $url = $url . "&url=" . $articleUrl;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Encoding:gzip'));

        curl_setopt($ch, CURLOPT_ENCODING, "gzip");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res,true);
        return $res;

    }


    /**万维API获取
     * @param $weixinId
     * @return mixed
     */
    public function getNew($weixinId)
    {
        //md5签名方式--非简单签名

        $showapi_appid = '89229';  //替换此值,在官网的"我的应用"中找到相关值
        $showapi_secret = '91be6ea1edb240e69a6cbe39e1c9b472';  //替换此值,在官网的"我的应用"中找到相关值
        $paramArr = array(
            'showapi_appid' => $showapi_appid,
            'query' => $weixinId,
            //添加其他参数
        );

        //创建参数(包括签名的处理)
        $param = $this->createParam($paramArr, $showapi_secret);
        $url = 'http://route.showapi.com/582-8?' . $param;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Encoding:gzip'));

        curl_setopt($ch, CURLOPT_ENCODING, "gzip");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $url);
       /* curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);*/

        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res,true);
        return $res;


    }

    function createParam($paramArr, $showapi_secret)
    {
        $paraStr = "";
        $signStr = "";
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $signStr .= $key . $val;
                $paraStr .= $key . '=' . urlencode($val) . '&';
            }
        }
        $signStr .= $showapi_secret;//排好序的参数加上secret,进行md5
        $sign = strtolower(md5($signStr));
        $paraStr .= 'showapi_sign=' . $sign;//将md5后的值作为参数,便于服务器的效验
        return $paraStr;
    }





}
