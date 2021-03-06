<?php
namespace app\admin\Controller;
use app\common\controller\CmsBase;
use app\admin\model\Subscriptions;
use think\Request;

class Subscription extends CmsBase
{
    private $model ;
    private $page_limit;
    public function __construct()
    {
        parent::__construct();
        $this->model = new Subscriptions();
        $this->page_limit = config('app.CMS_PAGE_SIZE');
    }

    /**
     * 获取公众号列表数据
     * @param Request $request
     * @return \think\response\View
     */
    public function index(Request $request){
        $search = $request->param('str_search');
        $list = $this->model->getWeixinForPage(1,$this->page_limit,$search);
        $record_num = $this->model->getWeixinCount($search);
        $data = [
            'list' => $list,
            'record_num' => $record_num,
            'page_limit' => $this->page_limit,
        ];
        return view('index',$data);
    }




    /**
     * @param Request $request
     */
    public function ajaxOpForPage(Request $request){
        if ($request->isPost()){
            $curr_page = $request->post('curr_page',1);
            $search = $request->post('str_search');
            $list = $this->model->getWeixinForPage($curr_page,$this->page_limit,$search);
            return showMsg(1,'success',$list);
        }else{
            return showMsg(0,'sorry，请求不合法');
        }

    }
    /**
     * 添加
     * @param Request $request
     * @return \think\response\View|void
     */
    public function add(Request $request){
        if($request->isPost()){
            $input = $request->param();
            $opRes = $this->model->add($input);
            return showMsg($opRes['tag'],$opRes['message']);
        }else{

            return view('add');
        }
    }

    /**
     * 更新数据
     * @param Request $request
     * @param $id
     * @return \think\response\View|void
     */
    public function edit(Request $request,$id){
        if ($request->isPost()){
            $opRes = $this->model->updateData( $request->param());
            return showMsg($opRes['tag'],$opRes['message']);
        }else{
            $weixin = $this->model->getInfoByID($id);

            $data =
                [
                    'weixin'=>$weixin,

                ];
            return view('edit',$data);
        }
    }

}
