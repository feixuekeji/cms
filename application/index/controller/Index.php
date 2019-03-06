<?php
namespace app\index\controller;

use app\common\model\Articles;
use app\common\model\Catalogs;
use app\common\model\TodayWords;
use think\Request;

class Index
{
    private $articleModel;
    private $todayWordModel;

    public function __construct()
    {
        $this->articleModel = new Articles();
        $this->catalogModel = new Catalogs();
        $this->todayWordModel = new TodayWords();
    }

    /**
     *
     * @return \think\response\View
     */
    public function index()
    {
        $todayWordsData = $this->todayWordModel->getTodayWord();
        $articleList = $this->articleModel->getArticleList();
        $recommendList = $this->articleModel->getRecommendList();
        $photos = $this->articleModel->getPhotos();
        $catalogs = $this->catalogModel->getOneLevelCatalog();

        $data = [
            'list' => $articleList,
            'todayWord'=>$todayWordsData,
            'recommendList' => $recommendList,
            'photos' => $photos,
            'catalogs' => $catalogs,
        ];
        return view('index',$data);
    }

    /**
     * 文章列表页
     * @return \think\response\View
     */
    public function review(){

        $articleList = $this->articleModel->getArticleList();
        $data = [
            'name'=>'MoTzxx',
            'List'=>$articleList,
        ];
        return view('review',$data);
    }
    public function contact(){
        return view('contact');
    }

    public function articleList()
    {

        return view('article');
    }


    public function getArticleList(Request $request)
    {

        $page = $request->param('page');
        $size = $request->param('size');
        $type = $request->param('type');
        $keyword = $request->param('keyword');

        $articles = $this->articleModel->getArticlesForPage($page,$size,$keyword,$type);
        $num = $this->articleModel->getArticlesCount($keyword,$type);



        $result = array(
            'status' => 0,
            'message' =>0,
            'data' =>$articles,
            'num' => $num,
        );
        exit(json_encode($result));
    }

    /**
     * 文章详情页
     * @param $id 文章ID
     * @return \think\response\View
     */
   /* public function article($id)
    {
        $articleInfo = $this->articleModel->getInfoByID(intval($id));
        $data = [
            'name'=>'MoTzxx',
            'article'=>$articleInfo,
        ];
        return view('article',$data);
    }*/


    public function articleDetail()
    {
        $data['id'] = input('id');
        $this->articleModel->addView(intval(input('id')));

        return view('article_detail',$data);
    }


    /**
     * 文章详情
     * @param $id 文章ID
     * @return \think\response\View
     */
    public function ajaxGetArticle(Request $request)
    {
        $id = $request->param('id');
        $articleInfo = $this->articleModel->getInfoByID(intval($id));


        $result = array(
            'status' => 0,
            'message' =>0,
            'data' =>$articleInfo
        );
        exit(json_encode($result));

    }


    /**上一篇下一篇
     * @param $id
     * @param $cateid
     */
    public function getPreAndNext($id,$catalog1,$catalog2)
    {
        $data = $this->articleModel->getPreAndNext($id,$catalog1,$catalog2);

        $result = array(
            'status' => 0,
            'message' =>0,
            'data' =>$data
        );
        exit(json_encode($result));

    }


    /**
     * 热门文章
     */
    public function getTopArticleList()
    {

        $articleInfo = $this->articleModel->getHotArticleList();


        $result = array(
            'status' => 0,
            'message' =>0,
            'data' =>$articleInfo
        );
        exit(json_encode($result));

    }


    /**
     * 分类导航
     */
    public function getType()
    {

        $articleInfo = $this->catalogModel->getOneLevelCatalog();


        $result = array(
            'status' => 0,
            'message' =>0,
            'data' =>$articleInfo
        );
        exit(json_encode($result));

    }






}
