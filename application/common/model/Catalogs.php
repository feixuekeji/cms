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
     * 获取所有
     * @return array
     */
    public function getCatalogList(){
        $data = $this
            ->order(['pid'=>'asc','list_order'=>'desc','id'=>'asc'])
            ->select()
            ->toArray();
        return $data;
    }

    public function getCatalogByPid($pid = 0,$limit = null)
    {
        if ($limit > 0)
        {
            $data = $this
                ->where('pid',$pid)
                ->order(['list_order'=>'desc'])
                ->limit($limit)
                ->select()
                ->toArray();
        }else{
            $data = $this
                ->where('pid',$pid)
                ->order(['list_order'=>'desc'])
                ->select()
                ->toArray();
        }

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
            ->find();
        isset($res)?$res->toArray():[];
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


    /**树形列表
     * @return array
     */
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


    /**
     * Notes:格式化分类
     * User: xxf
     * Date: 2019/3/14
     * Time: 17:04
     * @param $action
     * @param int $parent_id
     * @return array
     */
        private function menuFormat($action, $parent_id = 0)
    {
        $tmp = [];
        foreach ($action as $k => $v) {
            if ($v['pid'] == $parent_id) {
                $tmp[] = $v;
                unset($action[$k]);
            }
        }

        if (!empty($action)) {
            foreach ($tmp as $k => $v) {
                $children = $this->menuFormat($action, $v['id']);
                if (!empty($children)) {
                    $tmp[$k]['children'] = $children;
                }
            }
        }
        return $tmp;
    }


    /**
     * Notes:菜单列表
     * User: xxf
     * Date: 2019/3/14
     * Time: 17:03
     * @return array
     */
        public function getMenu(){

       $list = $this->getCatalogList();
        $menu = $this->menuFormat($list);
        return $menu;
    }

    public function getMenuList()
    {

        $list = $this->getCatalogByPid(0,6);
        if ($list) {
            foreach ($list as $k => $v) {
                $children_list = $this->getCatalogByPid($v['id']);
                if ($children_list) {
                    $list[$k]['children'] = $children_list;
                }
            }
        }
        return $list;
    }



}