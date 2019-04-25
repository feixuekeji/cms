<?php
namespace app\index\controller;
use think\Controller;
use think\facade\Env;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use app\index\controller\Upload;
use think\config;
use think\facade\Cache;
use Redis;




class Test extends Controller
{

    private   $wximgerr= '图片下载失败=>';
    private   $direrr  = '文件夹创建失败！';
    private   $fileerr = '资源不存在！';
    private   $dirurl  = '';
    private   $redis  = '';
    public function index()
    {
        $data = db('article')->select();
        $arr['data'] = $data;
        return view('list',$arr);
    }


    public function test1()
    {
        return view('test');
    }



    public function index1()
    {

        return view('index');
    }


    //文件上传处理
    public function uploadFile()
    {
        $request = request();    //获取所有的请求信息
        $file= request()->file('file');
        if ($file) {


            //$image = $file->getFileName();
            $filePath = $file->getRealPath();
            $url = $this->upload_qiniu($filePath);


                $arr = [
                    'errno'=>0,
                    "data" => array("$url"),
                    'url' =>"https://mmbiz.qpic.cn/mmbiz_jpg/sUFR89w6YZpnFMbXnetCTSaECEwUpsns8HEr1CaZrLJtKt2CDp7gIK8iad0FbCxZpXFyu1za4VpNwrEwMenFQfg/640?wx_fmt=jpeg",
                ];
            } else {
                $arr = [
                    //'msg' => '上传文件失败！',
                    'msg' => $file->getError(),
                    'type' => 0,
                ];
            }
        //unlink($filePath);
            echo json_encode($arr);
            exit();
        }

    public function redis(){

        Cache::store('redis')->set('sfdsf','yingying',1000000);
        $a = Cache::store('redis')->get('sfdsf');
        $this->redis = Cache::store('redis')->handler();
        $r = $this->redis->rpush('st_info','v1','v2');
        $res = $this->redis->get('st_info');

        $r = $this->redis->rpush('d1_list','v3','v4');
        $r = $this->redis->rpush('d1_list','v5','v6');
        $p = $this->redis->lpop('d1_list');
        $p = $this->redis->lrange('d1_list',0,5);
    }

    public function redis1(){

        $data['id'] = 1;
        $data['tttt'] = 2;
        Cache::store('redis')->set('sfdsf', $data,1000000);
        $a = Cache::store('redis')->get('sfdsf');
        $b = Cache::store('redis')->clear();
        var_dump($b);
    }



    public function test()
    {
        return json(['data'=>array(),'error'=>1,'message'=>'此管理员不存在']);
    }


}
