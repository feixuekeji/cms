<?php
namespace app\admin\Controller;
use app\common\controller\CmsBase;
use app\common\model\Collects;
use app\common\model\Catalogs;
use app\api\Controller\Upload;
use think\Request;
use think\facade\Env;
use think\facade\Log;

class Collect extends CmsBase
{
    private $model ;
    private $page_limit;
    public function __construct()
    {
        parent::__construct();
        $this->model = new Collects();
        $this->catalogModel = new Catalogs();
        $this->upload = new Upload();
        $this->page_limit = config('app.CMS_PAGE_SIZE');
    }

    /**
     * 获取文章列表数据
     * @param Request $request
     * @return \think\response\View
     */
    public function index(Request $request){
        $search = $request->param('str_search');
        $articles = $this->model->getArticlesForPage(1,$this->page_limit,$search);
        $record_num = $this->model->getArticlesCount($search);
        $data = [
            'articles' => $articles,
            'search' => $search,
            'record_num' => $record_num,
            'page_limit' => $this->page_limit,
        ];
        return view('index',$data);
    }


    public function url(Request $request)
    {
        if ($request->isPost()){

            $urls = $request->post('urls');
            $urls = explode("\n",$urls);
            static $num = 0;
            foreach ($urls AS $k =>$url)
            {
                $data = $this->get_file_article(trim($url));
                if ($data)
                {
                    $this->model->addArticle($data);
                    $num++;
                    echo $data['title']."采集完成\n";
                }

            }
            echo "共采集".$num;

           /* $list['num'] = $num;
            return showMsg(1,'success',$list);*/
        }else {
            return view('url');
        }
    }


    /**采集
     * @param $url
     * @return array|bool
     */
    function get_file_article($url)
    {

        $file = get_url($url);
        if(!$file){
            Log::error('错误信息'.'url错误');
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
                $filename = $this->upload->qiniu_upload($v);
                if($filename){
                    // 图片保存成功 替换地址
                    $old[] = $v;
                    $new[] = $filename;
                }else{
                    // 失败记录日志
                    Log::error('错误信息',$v);

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

    /**
     * @param Request $request
     */
    public function ajaxOpForPage(Request $request){
        if ($request->isPost()){
            $curr_page = $request->post('curr_page',1);
            $search = $request->post('str_search');
            $list = $this->model->getCmsArticlesForPage($curr_page,$this->page_limit,$search);
            return showMsg(1,'success',$list);
        }else{
            return showMsg(0,'sorry，请求不合法');
        }

    }
    /**
     * 添加文章
     * @param Request $request
     * @return \think\response\View|void
     */
    public function add(Request $request){
        $rootCatalogs = $this->catalogModel->getOneLevelCatalog();
        if($request->isPost()){
            $input = $request->param();
            $opRes = $this->model->addArticle($input);
            return showMsg($opRes['tag'],$opRes['message']);
        }else{
            return view('add',[
            'rootCatalogs'=>$rootCatalogs,
            ]);
        }
    }

    /**
     * 更新文章数据
     * @param Request $request
     * @param $id 文章ID
     * @return \think\response\View|void
     */
    public function edit(Request $request,$id){
        if ($request->isPost()){
            $opRes = $this->model->updateArticleData( $request->param());
            return showMsg($opRes['tag'],$opRes['message']);
        }else{
            $article = $this->model->getInfoByID($id);
            $rootCatalogs = $this->catalogModel->getOneLevelCatalog();
            $comments = [];
            $data =
                [
                    'article'=>$article,
                    'comments'=> $comments,
                    'rootCatalogs'=>$rootCatalogs,
                ];
            return view('edit',$data);
        }
    }


    /**
     * 采集的文章加入正式文章列表
     * @param Request $request
     * @param $id 文章ID
     * @return \think\response\View|void
     */
    public function addToArticle(Request $request,$id){

            $article = $this->model->getInfoByID($id);
            $rootCatalogs = $this->catalogModel->getOneLevelCatalog();
            $data =
                [
                    'article'=>$article,
                    'rootCatalogs'=>$rootCatalogs,
                ];
            return view('add_to_article',$data);
    }

}
