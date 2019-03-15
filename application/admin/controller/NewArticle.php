<?php
namespace app\admin\Controller;
use app\common\controller\CmsBase;
use app\admin\model\NewArticles;
use app\admin\model\Subscriptions;
use think\Request;
use think\facade\Env;
use think\facade\Log;

class NewArticle extends CmsBase
{
    private $model ;
    private $page_limit;
    public function __construct()
    {
        parent::__construct();
        $this->model = new NewArticles();
        $this->subscriptionModel = new Subscriptions();
        $this->page_limit = config('app.CMS_PAGE_SIZE');
    }

    /**
     * 获取文章列表数据
     * @param Request $request
     * @return \think\response\View
     */
    public function index(Request $request){
        $search = $request->param('str_search');
        $articles = $this->model->getForPage(1,$this->page_limit,$search);
        $record_num = $this->model->getCount($search);
        $data = [
            'articles' => $articles,
            'search' => $search,
            'record_num' => $record_num,
            'page_limit' => $this->page_limit,
        ];
        return view('index',$data);
    }


    /**
     * 采集最新文章信息
     */

    public function getUrlAndTitle()
    {

        $weiXinList = $this->subscriptionModel->getWeixinList();//微信号列表

        if ($weiXinList)
        {
            $num = $this->model->getUrlAndTitle1($weiXinList);
        }
        return "共采集".$num;

    }


    /**
     * 更新点赞数
     * @return string
     */
    public function getViewAndAgree()
    {

        $data = $this->model->getViewAndAgree();
        return $data['msg'];

    }




    /**
     * @param Request $request
     */
    public function ajaxOpForPage(Request $request){
        if ($request->isPost()){
            $curr_page = $request->post('curr_page',1);
            $search = $request->post('str_search');
            $list = $this->model->getForPage($curr_page,$this->page_limit,$search);
            return showMsg(1,'success',$list);
        }else{
            return showMsg(0,'sorry，请求不合法');
        }

    }


    /**
     * 更新数据
     * @param Request $request
     * @param $id 文章ID
     * @return \think\response\View|void
     */
    public function edit(Request $request){
        if ($request->isPost()){
            $opRes = $this->model->updateData( $request->param());
            return showMsg($opRes['tag'],$opRes['message']);
        }
    }


    public function test(Request $request)
    {
        $token = $request->token('__token__', 'sha1');
       return view();
    }




}
