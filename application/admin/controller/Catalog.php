<?php

namespace app\admin\Controller;

use app\common\controller\CmsBase;
use app\common\model\Catalogs;
use think\Request;

class Catalog extends CmsBase
{
    private $model ;
    private $page_limit;
    public function __construct()
    {
        parent::__construct();
        $this->model = new Catalogs();
        $this->page_limit = config('app.CMS_PAGE_SIZE');
    }

    /**
     * 获取文章列表数据
     * @param Request $request
     * @return \think\response\View
     */
/*    public function index(Request $request){
        $search = $request->param('str_search');
        $catalogs = $this->model->getCatalogsForPage(1,$this->page_limit,$search);
        $record_num = $this->model->getCatalogsCount();
        $data = [
            'catalogs' => $catalogs,
            'record_num' => $record_num,
            'page_limit' => $this->page_limit,
        ];
        return view('index',$data);
    }*/


    public function index(Request $request){

        $catalogs = $this->model->getCatalogTree();
        $record_num = $this->model->getCatalogsCount();
        $data = [
            'catalogs' => $catalogs,
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
        if($request->isPost()){
            $input = $request->param();
            $opRes = $this->model->addCatalog($input);
            return showMsg($opRes['tag'],$opRes['message']);
        }else{
            $data['catalogs'] = $this->model->getOneLevelCatalog();
            return view('add',$data);
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
            $opRes = $this->model->updateCatalog( $request->param());
            return showMsg($opRes['tag'],$opRes['message']);
        }else{
            $catalog = $this->model->getCatalogByID($id);

            $data =
                [
                    'catalog'=>$catalog,

                ];
            $data['catalogs'] = $this->model->getOneLevelCatalog();
            return view('edit',$data);
        }
    }


    /**
     * 获取二级菜单
     * @param Request $request
     */
    public function ajaxGetCatalogByPid(Request $request){
        if ($request->isPost()){
            $pid = $request->post('pid');
            $list = $this->model->getCatalogByPid($pid);
            $html = '';
            foreach ($list as $k => $v)
            {
                $html .= '<option value="' . $v["id"] . '"';
                $html .= '>' . $v["name"] . '</option>';
            }



            return $html;
            //return showMsg(1,'success',$list);
        }else{
            return showMsg(0,'sorry，请求不合法');
        }

    }
}
