<?php

namespace app\index\controller;

use app\common\model\Articles;
use app\common\model\Catalogs;
use app\common\model\Keywords;
use think\Request;

class Index extends Base
{
    private $articleModel;
    private $catalogModel;
    private $keywordModel;

    public function __construct()
    {
        parent::__construct();
        $this->articleModel = new Articles();
        $this->catalogModel = new Catalogs();
        $this->keywordModel = new Keywords();
    }

    /**
     *
     * @return \think\response\View
     */

    public function index()
    {
        return view('index');
    }

    /**
     * 文章列表页
     * @return \think\response\View
     */
/*    public function review()
    {

        $articleList = $this->articleModel->getArticleList();
        $data = [

            'List' => $articleList,
        ];
        return view('review', $data);
    }*/



    public function articleList()
    {

        return view('article');
    }


    /**
     * Notes:文章列表
     * User: xxf
     * Date: 2019/3/15
     * Time: 13:23
     * @param Request $request
     */
    public function getArticleList(Request $request)
    {

        $page = $request->param('page',1);
        $size = $request->param('size',15);
        $cate_id = $request->param('cate_id');
        $keyword = $request->param('keyword');

        $articles = $this->articleModel->getArticlesForPage($page, $size, $keyword, $cate_id);
        $num = $this->articleModel->getArticlesCount($keyword, $cate_id);
        if ($num && $keyword)
        {
            //加入搜索记录
            $this->keywordModel->addKeyword($keyword);
        }
        if ($num > 0)
        {
            $result = array(
                'code'=> 0,
                'msg' => 'success',
                'data' => $articles,
                'num' => $num,
            );
        }
        else{
            $result = array(
                'code'=> 401,
                'msg' => '没有符合条件的数据',
                'data' => '',
                'num' =>0,
            );
        }



        exit(json_encode($result));
    }



 /*   public function articleDetail(Request $request)
    {


        $data = $this->articleModel->addView(intval(input('id')));

        return view('article_detail', $data);
    }*/


    /**
     * 文章详情
     * @param $id 文章ID
     * @return \think\response\View
     */
    public function getArticleInfo(Request $request)
    {
        $id = $request->param('id');
        $this->articleModel->addView(intval($id));
        $cate_id = $request->param('cate_id');
        $articleInfo = $this->articleModel->getInfoByID(intval($id));
        if (!$articleInfo)
        {
            $result = array(
                'code' => 402,
                'msg' => '文章不存在',
                'data' => '',
            );
            exit(json_encode($result));
        }


        $preAndNext = $this->articleModel->getPreAndNext($id, $cate_id);
        $ctatalog1 = $this->catalogModel->getCatalogByID($articleInfo['catalog1']);
        $ctatalog2 = $this->catalogModel->getCatalogByID($articleInfo['catalog2']);


        $result = array(
            'code' => 0,
            'msg' => 'success',
            'data' => array(
                'detail' => $articleInfo,
                'pre' => $preAndNext['pre'],
                'next' => $preAndNext['next'],
                'catalog1' => $ctatalog1,
                'catalog2' => $ctatalog2,
            ),

        );

        exit(json_encode($result));

    }




    /**
     * 热门文章
     */
    public function getHotArticleList()
    {

        $articleInfo = $this->articleModel->getHotArticleList();
        if (!empty($articleInfo))
        {
            $result = array(
                'code'=> 0,
                'msg' => 'success',
                'data' => $articleInfo
            );
        } else {
            $result = array(
                'code' => 401,
                'msg' => '没有符合条件的数据',
                'data' => '',
            );
        }


        exit(json_encode($result));

    }


    /**
     * 分类导航
     */
    /*public function getType(Request $request)
    {
        $catalogId = intval($request->param('catalogId'));

        if ($catalogId > 0)
        {
            $catalogInfo = $this->catalogModel->getCatalogByID($catalogId);
            //一级目录方可查询二级目录
            if ($catalogInfo['pid'] !=0)
                $catalogId = 0;
        }

        $articleInfo = $this->catalogModel->getOneLevelCatalog($catalogId);


        $result = array(
            'code'=> 0,
            'msg' => 'success',
            'data' => $articleInfo
        );
        exit(json_encode($result));

    }*/


    /**
     * Notes:菜单列表
     * User: xxf
     * Date: 2019/3/14
     * Time: 17:32
     * @param Request $request
     */
    public function getCatagory(Request $request)
    {

        $list = $this->catalogModel->getMenu();

        $result = array(
            'code'=> 0,
            'msg' => 'success',
            'data' => $list
        );
        exit(json_encode($result));

    }



    /**首页分类文章列表
     * 传入$cate_id显示二级目录
     * @param Request $request
     */
    public function getArticleListByCatalog(Request $request)
    {


        $page = $request->param('page',1);
        $size = $request->param('size',6);
        $cate_id = $request->param('cate_id',0);
        $catalogs = $this->catalogModel->getOneLevelCatalog($cate_id);
        foreach ($catalogs AS $k => &$v)
        {
            $v['list'] = $this->articleModel->getArticlesForPage($page, $size, '', $v['id']);
        }
        if (!$catalogs)
        {
            $result = array(
                'code'=> 403,
                'msg' => '分类不存在',
                'data' => '',

            );
            exit(json_encode($result));
        }



        $result = array(
            'code'=> 0,
            'msg' => 'success',
            'data' => $catalogs,

        );
        exit(json_encode($result));

    }

    /**
     * 相关文章
     * @param Request $request
     */
    public function getSimilarList(Request $request)
    {
        $id = $request->param('id');
        $articleInfo = $this->articleModel->getInfoByID(intval($id));
        $list = $this->articleModel->getSimilarList($articleInfo['title']);
        $result = array(
            'code'=> 0,
            'msg' => 'success',
            'data' => $list,

        );
        exit(json_encode($result));

    }


    public function getKeywordList()
    {
        $list = $this->keywordModel->getKeywordList();
        if ($list) {
            $result = array(
                'code' => 0,
                'msg' => 'success',
                'data' => $list,

            );
        } else {
            $result = array(
                'code' => 401,
                'msg' => '没有符合条件的数据',
                'data' => '',
            );
        }
        exit(json_encode($result));
    }





}
