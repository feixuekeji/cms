<?php
namespace app\admin\Controller;
use app\common\controller\CmsBase;
use app\common\model\Collects;
use app\common\model\Catalogs;
use think\Request;
use think\facade\Env;
use think\facade\Log;

class Collect extends CmsBase
{
    private $model ;
    private $page_limit;
    private $upload;
    private $catalogModel;
    public function __construct()
    {
        parent::__construct();
        $this->model = new Collects();
        $this->catalogModel = new Catalogs();
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


/*    public function url(Request $request)
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


        }else {
            return view('url');
        }
    }*/


    public function url(Request $request)
    {
        if ($request->isPost()){

            $url = $request->post('url');


                $data = $this->model->get_file_article(trim($url));
                if ($data)
                {
                    $this->model->addArticle($data);
                    $message = $data['title']."采集完成\n";
                    return showMsg(1,$message);
                }


        }else {
            return view('url');
        }
    }

    /**
     * @param Request $request
     */
    public function ajaxOpForPage(Request $request){
        if ($request->isPost()){
            $curr_page = $request->post('curr_page',1);
            $search = $request->post('str_search');
            $list = $this->model->getArticlesForPage($curr_page,$this->page_limit,$search);
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
