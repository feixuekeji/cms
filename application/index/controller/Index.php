<?php
namespace app\index\controller;

use app\common\model\Articles;
use app\common\model\Catalogs;
use app\common\model\TodayWords;

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
            'name'=>'MoTzxx',
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

    /**
     * 文章详情页
     * @param $id 文章ID
     * @return \think\response\View
     */
    public function article($id)
    {
        $articleInfo = $this->articleModel->getInfoByID(intval($id));
        $data = [
            'name'=>'MoTzxx',
            'article'=>$articleInfo,
        ];
        return view('article',$data);
    }


}
