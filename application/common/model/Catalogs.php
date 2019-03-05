<?php
namespace app\common\model;
use app\common\validate\Catalog;
use think\Db;
use \think\Model;


class Catalogs extends BaseModel
{

    protected $validate;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->validate = new Catalog();
    }

    /**
     * 获取所有的文章
     * @return array
     */
    public function getCatalogList(){
        $data = $this
            ->select()
            ->toArray();
        return $data;
    }

    public function getCatalogByPid($pid)
    {
        $data = $this
            ->where('pid',$pid)
            ->select()
            ->toArray();
        return $data;
    }

    public function getOneLevelCatalog($pid=0)
    {
        $data = $this
            ->where('pid',$pid)
            ->select()
            ->toArray();
        return $data;
    }

    /**
     * 获取所要推荐的文章
     * @return array
     */
    public function getRecommendList(){
        $res = $this
            ->field('a.title,a.id')
            ->alias('a')
            ->join('article_points ap','ap.article_id = a.id')
            ->order('ap.view','desc')
            ->where('ap.status',1)
            ->limit(6)
            ->select()
            ->toArray();
        return $res;
    }

    /**
     * 获取所有的文章首页图片
     * @return array
     */
    public function getPhotos(){
        $res = Db::name('xphotos')
            ->field('picture')
            ->order("id","asc")
            ->limit(9)
            ->select();
        return $res;
    }

    /**
     * 根据文章ID 获取文章详情
     * @param $id
     * @return array
     */
    public function getInfoByID($id)
    {
        $res = [];
        if(is_numeric($id)){
            $res = $this
                ->alias('a')
                ->join('article_points ap','ap.article_id = a.id')
                ->field('a.*')
                ->where('a.id = '.$id)
                ->find();
        }
        isset($res)?$res->toArray():[];
        return $res;
    }

    /**
     * 后台获取数据列表
     * @param $curr_page
     * @param int $limit
     * @param null $search
     * @return array
     */
    public function getCatalogsForPage($curr_page,$limit = 1,$search = null){
        $res = $this
            ->limit($limit*($curr_page - 1),$limit)
            ->select();
        return $res->toArray();
    }

    /**
     * 后台获取总数
     * @param null $search
     * @return int|string
     */
    public function getCatalogsCount($search = null){
        $count = $this

            ->count();
        return $count;
    }
    /**
     * 根据ID 获取内容
     * @param $id
     * @return array
     */
    public function getCatalogByID($id){
        $res = $this
            ->where('id',$id)
            ->find()
            ->toArray();
        return $res;
    }

    /**
     * 更新
     * @param $input
     * @return array
     */
    public function updateCatalog($input){

        $id = $input['id'];
        $opTag = isset($input['tag']) ? $input['tag'] : 'edit';
        if ($opTag == 'del') {
            $this
                ->where('id', $id)
                ->delete();
            $validateRes = ['tag' => 1, 'message' => '删除成功'];
        } else {
            $saveData = [
                'name' => $input['name'],
                'pid' => $input['pid'],
                'list_order' => $input['list_order'],
            ];
            $tokenData = ['__token__' => isset($input['__token__']) ? $input['__token__'] : '',];
            $validateRes = $this->validate($this->validate, $saveData, $tokenData);
            if ($validateRes['tag']) {
                $saveTag = $this
                    ->where('id', $id)
                    ->update($saveData);

                $validateRes['tag'] = $saveTag;
                $validateRes['message'] = $saveTag ? '修改成功' : '数据无变动';
            }
        }
        return $validateRes;
    }

    /**
     * 分类添加
     * @param $data
     * @return array
     */

    public function addCatalog($data){

        $addData = [
            'pid' => $data['pid'],
            'name' => $data['name'],
            'list_order' => $data['list_order'],

        ];
        $tokenData = ['__token__' => isset($data['__token__']) ? $data['__token__'] : '',];
        $validateRes = $this->validate($this->validate, $addData, $tokenData);
        if ($validateRes['tag']) {
            $tag = $this->insert($addData);
            $validateRes['tag'] = $tag;
            $validateRes['message'] = $tag ? '添加成功' : '添加失败';
        }
        return $validateRes;
    }


    public function getCatalogTree(){
        $cateres = $this->select();
        return $this->sort($cateres);
    }

    public function sort($data,$pid=0,$level=0){
        static $arr = array();
        foreach($data as $k=>$v){
            if($v['pid'] == $pid){
                $v['level'] = $level;
                $arr[] = $v;
                $this->sort($data,$v['id'],$level+1);
            }
        }
        return $arr;
    }

}