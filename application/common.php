<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function get_content_from_url($url)
{
    //初使化curl
    $ch = curl_init();
    //请求的url，由形参传入
    curl_setopt($ch, CURLOPT_URL, $url);
    //将得到的数据返回
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //不处理头信息
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //连接超过10秒超时
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //执行curl
    $output = curl_exec($ch);
    //关闭资源
    curl_close($ch);
    //返回内容
    return $output;


}

/**
 * curl获取数据
 * @param $url
 * @return mixed
 */
function get_url($url)
{
    $ifpost = 0;//是否post请求
    $datafields = '';//post数据
    $cookiefile = '';//cookie文件
    $cookie = '';//cookie变量
    $v = false;
    //构造随机ip
    $ip_long = array(
        array('607649792', '608174079'), //36.56.0.0-36.63.255.255
        array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
        array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
        array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
        array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
        array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
        array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
        array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
        array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
        array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
    );
    $rand_key = mt_rand(0, 9);
    $ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
//模拟http请求header头
    $header = array("Connection: Keep-Alive","Accept: text/html, application/xhtml+xml, */*", "Pragma: no-cache", "Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3","User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)",'CLIENT-IP:'.$ip,'X-FORWARDED-FOR:'.$ip);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, $v);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $ifpost && curl_setopt($ch, CURLOPT_POST, $ifpost);
    $ifpost && curl_setopt($ch, CURLOPT_POSTFIELDS, $datafields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $cookie && curl_setopt($ch, CURLOPT_COOKIE, $cookie);//发送cookie变量
    $cookiefile && curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);//发送cookie文件
    $cookiefile && curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);//写入cookie到文件
    curl_setopt($ch,CURLOPT_TIMEOUT,60); //允许执行的最长秒数
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $ok = curl_exec($ch);
    curl_close($ch);
    unset($ch);
    return $ok;
}

/**
 * json f返回数据
 * @param $status
 * @param string $message
 * @param array $data
 */
function showMsg($status,$message = '',$data = array()){
    $result = array(
        'status' => $status,
        'message' =>$message,
        'data' =>$data
    );
    exit(json_encode($result));
}


/**
 * Notes:返回带状态码信息
 * User: xxf
 * Date: 2019/3/18
 * Time: 16:29
 * @param array $data
 * @param int $code
 * @param string $message
 */
function sendResponse($data = [],$code = 200,$message = 'ok')
{
    $HTTP_VERSION = "HTTP/1.1";
    //输出结果
    header($HTTP_VERSION . " " . $code . " " . $message);
    header("Content-Type: application/json");
    exit(json_encode($data));
}

