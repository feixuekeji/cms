<?php
namespace app\admin\controller;
use think\Controller;
use think\facade\Env;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use app\index\controller\Upload;
use think\config;




class Index extends Controller
{

    private   $wximgerr= '图片下载失败=>';
    private   $direrr  = '文件夹创建失败！';
    private   $fileerr = '资源不存在！';
    private   $dirurl  = '';
    public function index()
    {
        $data = db('article')->select();
        $arr['data'] = $data;
        return view('list',$arr);
    }




    public function index1()
    {

        return view('index');
    }

    public function url()
    {

        return view('url');
    }

    public function edit()
    {

        $id = input('id/d');
        $id = request()->param('id');
        $data = db('article')->where('id', $id)->find();

        return $this->fetch('edit',$data);
    }


    public function save()
    {
        $data = request()->post();
        db('article')->update($data);
    }
    public function geturl()
    {

        $urls = request()->get('urls');
        $urls = explode("\n",$urls);
        static $num = 0;
        foreach ($urls AS $k =>$url)
        {
            $a = $this->get_file_article($url);
            if ($a)
            {
                $data['title'] = $a['title'];
                $data['content'] = $a['content'];
                db("article")->insert($data);
                $num++;
                echo $data['title']."采集完成";
            }

        }
        echo "共采集".$num;


    }

//    public function test()
//    {
//       $url = 'https://mp.weixin.qq.com/s?src=11&timestamp=1551060001&ver=1449&signature=cYKL1OQgWU7BVfjAaSoAdcGvmatLdU5Bg3nIzNjAD-lRY1PS0jURcvj8l92GNNYyldhVBeAb0oVg8Aumnp-Hd95DX4Ldr8jqNIMnw6*1OpGBYdN64YcM7-pfff36D6oO&new=1';
//
//        $a = $this->get_file_article($url);
//
//
//        echo var_dump($a);
//    }


    function get_file_article($url)
    {
        $this->dirurl = Env::get('root_path').'public\\Uploads';

        //$file = file_get_contents($url);
        //$file = get_content_from_url($url);
        $file = get_url($url);
        if(!$file){
            $this->put_error_log($this->dirurl,$this->fileerr);
            exit(json_encode(array('msg'=>$this->fileerr,'code'=>500)));
        }
        // 内容
        preg_match('/<div class="rich_media_content " id="js_content">[\s\S]*?<\/div>/',$file,$content);
        // 标题
        preg_match('/<title>([\s\S]*?)<\/title>/',$file,$title);
        $title = $title?$title[1]:'';
        $title = trim($title);

        if (empty($content[0]))
            return false;

        // 图片
        preg_match_all('/<img.*?data-src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg|\.?]))[\'|\"].*?[\/]?>/',$content[0],$images);
        // 储存原地址和下载后地址
        $old = array();
        $new = array();
        // 去除重复图片地址
        $images = array_unique($images[1]);
        if($images){
            foreach($images as $v){
                //$filename = $this->put_file_img($this->dirurl,$v);
                $filename = $this->upload_qiniu($v);
                if($filename){
                    // 图片保存成功 替换地址
                    $old[] = $v;
                    $new[] = $filename;
                }else{
                    // 失败记录日志
                    $this->put_error_log($this->dirurl,$this->wximgerr.$v);
                }
            }
            $old[] = 'data-src';
            $new[] = 'src';
            $content = str_replace($old,$new,$content[0]);
        }


        $data = array('content'=>$content,'title'=>$title);
        //return json_encode(array('data'=>$data,'code'=>200,'msg'=>'ok'));
        return $data;
    }
    /* 抓取保存图片函数
     * return
     * $filename  string  图片地址
     */
    function put_file_img($dir='',$image='')
    {
        // 判断图片的保存类型 截取后四位地址
        $exts = array('jpeg','png','jpg');
        $filename = uniqid().time().rand(10000,99999);
        $ext = substr($image,-5);
        $ext = explode('=',$ext);
        if(in_array($ext[1],$exts) !== false){
            $filename .= '.'.$ext[1];
        }else{
            $filename .= '.gif';
        }
        $souce = file_get_contents($image);
        if(file_put_contents($dir.'\\'.$filename,$souce)){
            return  dirname($_SERVER['SCRIPT_NAME']) . '/public/Uploads/'.$filename;
        }else{
            return false;
        }
    }

    /*
     * 上传到七牛
     *
     */
    function upload_qiniu($image)
    {
        $upload = new Upload();
        return $upload->qiniu_upload($image);
    }

    function delImage()
    {
        $filename = request()->post('filename');
        $filename = explode('/',$filename);
        $filename = array_pop($filename);

        $upload = new Upload();
        $res = $upload->delFileByName($filename);
        if ($res)
        {
            return json_encode(array('code'=>200,'msg'=>'删除成功'));
        }
        else
            return json_encode(array('code'=>201,'msg'=>'删除失败'));




    }



    function put_error_log($dir,$data)
    {
        file_put_contents($dir.'/error.log',date('Y-m-d H:i:s',time()).$data.PHP_EOL,FILE_APPEND);
    }
    /* 创建文件夹
     * $dir string 文件夹路径
     */
    function put_dir($dir=''){
        $bool = true;
        if(!is_dir($dir)){
            if(!mkdir($dir,777,TRUE)){
                $bool = false;
                $this->put_error_log($dir,$this->direrr.$dir);
            }
        }
        return $bool;
    }


    public function upload()
    {
        if(request()->isPost()){
            $file = request()->file('image');
            // 要上传图片的本地路径
            $filePath = $file->getRealPath();
            $ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);  //后缀
            //获取当前控制器名称
            $controllerName=$this->getContro();
            // 上传到七牛后保存的文件名
            $key =substr(md5($file->getRealPath()) , 0, 5). date('YmdHis') . rand(0, 9999) . '.' . $ext;
            require_once APP_PATH . '/../vendor/qiniu/autoload.php';
            // 需要填写你的 Access Key 和 Secret Key
            $accessKey = config('ACCESSKEY');
            $secretKey = config('SECRETKEY');
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 要上传的空间
            $bucket = config('BUCKET');
            $domain = config('DOMAINImage');
            $token = $auth->uploadToken($bucket);
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
                return ["err"=>1,"msg"=>$err,"data"=>""];
            } else {
                //返回图片的完整URL
                return json(["err"=>0,"msg"=>"上传完成","data"=>uploadreg($domain . $ret['key'])]);
            }
        }
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



}
