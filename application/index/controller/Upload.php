<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 17:03
 */

namespace app\index\controller;
use think\Controller;
use Config;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;




class Upload extends Controller
{
    /**
     *
     * 七牛云存储图片
     *
     */
    public function qiniu_upload($filePath){
        $config = Config::get('UPLOAD_Qiniu_CONFIG');
        $accessKey = $config['accessKey'];
        $secretKey = $config['secretKey'];
        $auth = new Auth($accessKey, $secretKey);

        $bucket = $config['bucket'];// 要上传的空间
        $token = $auth->uploadToken($bucket);// 生成上传 Token

        // 要上传文件的本地路径
        $data = file_get_contents($filePath);


            $key = md5(time().rand(10000,99999)).'.png';

        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();

        // 调用 UploadManager 的 putFile 方法进行文件的上传
        /*list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err === null) {
            $data['url'] = $config['domain'].$ret['key'];
        }*/


        // 调用 UploadManager 的 put 方法进行文件的上传
        list($ret, $err) = $uploadMgr->put($token, $key, $data);
        if ($err === null) {
            $url = $config['domain'].$ret['key'];
            return $url;
        }


    }



    /**
     * 删除图片
     * @param $delFileName 要删除的图片文件
     * @return bool
     */
    public function delFileByName($delFileName)
    {
        // 判断是否是图片
        $isImage = preg_match('/.*(\.png|\.jpg|\.jpeg|\.gif)$/', $delFileName);
        if(!$isImage){
            return false;
        }
        $config = Config::get('UPLOAD_Qiniu_CONFIG');
        $accessKey = $config['accessKey'];
        $secretKey = $config['secretKey'];
        $auth = new Auth($accessKey, $secretKey);
        $bucket = $config['bucket'];// 要上传的空间

        $config1 = new \Qiniu\Config();




        // 管理资源
        $bucketManager = new BucketManager($auth, $config1);

        // 删除文件操作
        $res = $bucketManager->delete($bucket, $delFileName);

        if (is_null($res)) {
            // 为null成功
            return true;
        }

        return false;

    }


}