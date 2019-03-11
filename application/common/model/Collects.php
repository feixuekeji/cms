<?php
namespace app\common\model;
use think\Db;
use \think\Model;
use think\facade\Session;


class Collects extends BaseModel
{

    /**
     * 获取所有的文章
     * @return array
     */
    public function getArticleList(){
        $data = $this
            ->select()
            ->toArray();
        return $data;
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
                ->where('id',$id)
                ->find();
        }
        isset($res)?$res->toArray():[];
        return $res;
    }

    /**
     * 后台获取文章数据列表
     * @param $curr_page
     * @param int $limit
     * @param null $search
     * @return array
     */
    public function getArticlesForPage($curr_page,$limit = 1,$search = null){
        $res = $this
            ->alias('c')
            ->field('c.*,a.user_name')
            ->leftJoin('admins a','c.admin_id = a.id')
            ->whereLike('c.title','%'.$search.'%')
            ->order(['c.id'=>'desc'])
            ->limit($limit*($curr_page - 1),$limit)
            ->select();
        foreach ($res as $key => &$v){
            $v['time'] = date('Y-m-d H:i', $v['time']);

        }

        return $res->toArray();
    }

    /**
     * 后台获取文章总数
     * @param null $search
     * @return int|string
     */
    public function getArticlesCount($search = null){
        $count = $this
            ->whereLike('title','%'.$search.'%')
            ->count();
        return $count;
    }


    /**
     * 更新文章内容
     * @param $input
     * @return array
     */
    public function updateArticleData($input){

        $id = $input['id'];
        $opTag = isset($input['tag']) ? $input['tag'] : 'edit';
        if ($opTag == 'del') {
            $this->where('id',$id)
                ->delete();
            $validateRes = ['tag' => 1, 'message' => '删除成功'];
        } else {
            $saveData = [
                'title' => $input['title'],
                'content' => isset($input['content'])?$input['content']:'',
            ];

                $saveTag = $this
                    ->where('id', $id)
                    ->update($saveData);
                $validateRes['tag'] = $saveTag;
                $validateRes['message'] = $saveTag ? '修改成功' : '数据无变动';

        }
        return $validateRes;
    }

    /**
     * 进行添加操作
     * @param $data
     * @return array
     */

    public function addArticle($data){

        $addData = [
            'title' => $data['title'],
            'content' => isset($data['content'])?$data['content']:'',
            'wxgzh' => $data['wxgzh'],
            'picture' => $data['picture'],
            'admin_id' => Session::get('AID'),
            'time' => time(),
        ];
            $tag = $this->insert($addData);
            $validateRes['tag'] = $tag;
            $validateRes['message'] = $tag ? '添加成功' : '添加失败';
        return $validateRes;
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
            ->order(['id'=>'desc'])
            ->limit($limit*($curr_page - 1),$limit)
            ->select();

        return $res->toArray();
    }
}