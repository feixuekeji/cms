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

        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res);
        return $res;

    }


    /**获取阅读量点赞数
     * @param $weixinId
     * @return mixed
     */
    public function getPoints($articleUrl)
    {

        $url = $this->points_url;
        $articleUrl=urlencode($articleUrl);
        $url = $url . "&url=" . $articleUrl;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Encoding:gzip'));

        curl_setopt($ch, CURLOPT_ENCODING, "gzip");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $url);

        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res);
        return $res;

    }

}
