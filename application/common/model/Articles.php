<?php
namespace app\common\model;
use app\common\validate\Article;
use think\Db;
use \think\Model;


class Articles extends BaseModel
{
    // 设置当前模型对应的完整数据表名称
    protected $autoWriteTimestamp = 'datetime';
    protected $validate;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->validate = new Article();
    }

    /**
     * 获取所有的文章
     * @return array
     */
    public function getArticleList(){
        $data = $this
            ->where('id','>',0)
            ->where('status',1)
            ->select()
            ->toArray();
        //$data = array_merge($data,$data,$data,$data,$data,$data,$data);
        return $data;
    }


    public function getSimilarList($title){
        $all_list = $this
            ->field('id,title')
            ->where('id','>',0)
            ->where('status',1)
            ->select()
            ->toArray();
        foreach ($all_list AS $k => $v)
        {
            $similar[$k]  = similar_text($v['title'], $title, $persent);
        }
        array_multisort($similar, SORT_DESC, $all_list);
        $output = array_slice($all_list, 0,6);
        return $output;
    }

    /**
     * 获取所要推荐的文章
     * @return array
     */
    public function getHotArticleList(){
        $res = $this
            ->order('view','desc')
            ->limit(10)
            ->select()
            ->toArray();
        return $res;
    }




    /**增加阅读量
     * @param $id
     * @return int|true
     */
    public function addView($id){
        $res = $this
            ->where('id', $id)
            ->setInc('view', 1);
        return $res;
    }

    /**
     * 获取所要推荐的文章
     * @return array
     */
    /*public function getRecommendList(){
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
    }*/

    /**
     * 获取所有的文章首页图片
     * @return array
     */
    public function getPhotos(){
        $res = Db::name('photos')
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
                ->where('id = '.$id)
                ->find();
        }

        isset($res)?$res->toArray():[];
        return $res;
    }


    public function getPreAndNext($id,$catalog = 0)
    {

        if ($catalog)
            $where[] = array('catalog1|catalog2','=', $catalog);

        // 下一页查询条件

        $where[] = array('id','>', $id);

        $next_topic = $this->order('id asc')->where($where)->limit(1)->select()->toArray();
        if ($next_topic) {
            $next_topic = $next_topic[0];
        } else {
            $next_topic = null;
        }

        // 上一篇查询条件
        array_pop($where);
        $where[] = array('id','<', $id);
        $prev_topic = $this->order('id desc')->where($where)->limit(1)->select()->toArray();
        if ($prev_topic) {
            $prev_topic = $prev_topic[0];
        } else {
            $prev_topic = null;
        }
        $data['pre'] = $prev_topic;
        $data['next'] = $next_topic;
        return $data;
    }

    /**
     * 后台获取文章数据列表
     * @param $curr_page
     * @param int $limit
     * @param null $search
     * @return array
     */
    public function getCmsArticlesForPage($curr_page,$limit = 1,$search = null){
        $res = $this
            ->where("status",1)
            ->whereLike('title','%'.$search.'%')
            ->order(['list_order'=>'desc','id'=>'desc'])
            ->limit($limit*($curr_page - 1),$limit)
            ->select();
        foreach ($res as $key => $v){
            if ($v['status'] == 1){
                $res[$key]['status_tip'] = "<span class=\"layui-badge layui-bg-blue\">正常</span>";
            }else{
                $res[$key]['status_tip'] = "<span class=\"layui-badge layui-bg-cyan\">删除</span>";
            }
        }
        return $res->toArray();
    }

    /**
     * 后台获取文章总数
     * @param null $search
     * @return int|string
     */
    public function getCmsArticlesCount($search = null){
        $count = $this
            ->where("status",1)
            ->whereLike('title','%'.$search.'%')
            ->count();
        return $count;
    }
    /**
     * 根据文章ID 获取文章内容
     * @param $id
     * @return array
     */
    public function getCmsArticleByID($id){
        $res = $this
            ->where('id',$id)
            ->find()
            ->toArray();
        return $res;
    }

    /**
     * 更新文章内容
     * @param $input
     * @return array
     */
    public function updateCmsArticleData($input){

        $id = $input['id'];
        $opTag = isset($input['tag']) ? $input['tag'] : 'edit';
        if ($opTag == 'del') {
           $this
                ->where('id', $id)
                ->update(['status' => 0]);
            $validateRes = ['tag' => 1, 'message' => '删除成功'];
        } else {
            $saveData = [
                'title' => $input['title'],
                'list_order' => $input['list_order'],
                'content' => isset($input['content'])?$input['content']:'',
                'updated_at' => date('Y-m-d H:m:s', time()),
                'picture' => $input['picture'] ? $input['picture'] : '',
                'abstract' => $input['abstract'],
                'status' => $input['status'],
                'catalog1' =>  $input['catalog1'],
                'catalog2' =>  $input['catalog2'],
                'istop' =>  $input['istop'],
                'ishot' =>  $input['ishot'],
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
     * 进行新文章的添加操作
     * @param $data
     * @return array
     */

    public function addArticle($data){

        $addData = [
            'title' => $data['title'],
            'list_order' => $data['list_order'],
            'content' => isset($data['content'])?$data['content']:'',
            'user_id' => 1,
            'catalog1' =>  $data['catalog1'],
            'catalog2' =>  $data['catalog2'],
            'created_at' => date('Y-m-d H:m:s', time()),
            'updated_at' => date('Y-m-d H:m:s', time()),
            'picture' => $data['picture'],
            'abstract' => $data['abstract'],
            'status' => $data['status'],
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



    /**
     * 前台获取文章数据列表
     * @param $curr_page
     * @param int $limit
     * @param null $search
     * @return array
     */
    public function getArticlesForPage($curr_page,$limit = 10,$keyword = null,$type = 0){
        $where['status'] = 1;
        if ($type > 0)
            $where['catalog1|catalog2'] = $type;
        $res = $this
            ->field('id,title,updated_at,picture,abstract,view,status,istop,ishot')
            ->where($where)
            ->whereLike('title','%'.$keyword.'%')
            ->order(['istop'=>'desc','list_order'=>'desc','id'=>'desc'])
            ->limit($limit*($curr_page - 1),$limit)
            ->select();
        return $res->toArray();
    }

    /**
     * 前台获取文章总数
     * @param null $search
     * @return int|string
     */
    public function getArticlesCount($keyword = null,$type = 0){
        $where['status'] = 1;
        if ($type > 0)
            $where['catalog1|catalog2'] = $type;
        $count = $this
            ->where($where)
            ->whereLike('title','%'.$keyword.'%')
            ->count();
        return $count;
    }
}