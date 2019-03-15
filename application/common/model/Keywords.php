<?php
namespace app\common\model;
use think\Db;
use \think\Model;


class Keywords extends BaseModel
{


    //判断是否存在
    public function isExist($keyword)
    {

        $res = $this
            ->where('keyword',$keyword)
            ->find();
        if ($res)
        {
            $this->addNum($res['id']);
            return true;
        }
        return false;
    }


    /**增加搜索次数
     * @param $id
     * @return int|true
     */
    public function addNum($id){
        $data['id'] = $id;
        $data['update_time'] = time();
        $this->update($data);
        $res = $this->where('id',$id)->setInc('num',1);

        return $res;
    }

    /**添加搜索词
     * Notes:
     * User: xxf
     * Date: 2019/3/15
     * Time: 11:54
     */
    public function addKeyword($keyword)
    {

        if (!$this->isExist($keyword))
        {
            $data['keyword'] = $keyword;
            $data['num'] = 1;
            $data['create_time'] = time();
            $data['update_time'] = time();
            $tag = $this->insert($data);
            $validateRes['tag'] = $tag;
            return $validateRes;
        }

    }


    /**
     * Notes:热搜词列表
     * User: xxf
     * Date: 2019/3/15
     * Time: 14:03
     * @param int $limit
     * @return array
     */
    public function getKeywordList($limit = 5){
        $res = $this
            ->order(['num'=>'desc'])
            ->limit($limit)
            ->select();

        return $res->toArray();
    }




}