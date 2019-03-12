<?php

namespace app\admin\model;

use app\admin\validate\Subscription;
use app\common\model\BaseModel;
use think\Db;
use think\Model;
use think\Session;

class Subscriptions extends BaseModel
{
    protected $validate;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->validate = new Subscription();
    }

    /**
     * 获取所有
     * @return array
     */
    public function getWeixinList()
    {
        $data = $this
            ->where('id', '>', 0)
            ->select()
            ->toArray();
        return $data;
    }


    /**
     * 根据ID获取详情
     * @param $id
     * @return array
     */
    public function getInfoByID($id)
    {
        $res = [];
        if (is_numeric($id)) {
            $res = $this
                ->where('id = ' . $id)
                ->find();
        }

        isset($res) ? $res->toArray() : [];
        return $res;
    }


    /**
     * 后台获取数据列表
     * @param $curr_page
     * @param int $limit
     * @param null $search
     * @return array
     */
    public function getWeixinForPage($curr_page, $limit = 1, $search = null)
    {
        $res = $this
            ->whereLike('weixin_nickname', '%' . $search . '%')
            ->order(['id' => 'desc'])
            ->limit($limit * ($curr_page - 1), $limit)
            ->select();
        return $res->toArray();
    }

    /**
     * 后台获取文章总数
     * @param null $search
     * @return int|string
     */
    public function getWeixinCount($search = null)
    {
        $count = $this
            ->whereLike('weixin_nickname', '%' . $search . '%')
            ->count();
        return $count;
    }


    /**
     * 更新文章内容
     * @param $input
     * @return array
     */
    public function updateData($input)
    {

        $id = $input['id'];
        $opTag = isset($input['tag']) ? $input['tag'] : 'edit';
        if ($opTag == 'del') {
            $this
                ->where('id', $id)
                ->delete();
            $validateRes = ['tag' => 1, 'message' => '删除成功'];
        } else {
            $saveData = [
                'weixin_nickname' => $input['weixin_nickname'],
                'weixin_id' => $input['weixin_id'],
                'time' => time(),
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
     * 添加操作
     * @param $data
     * @return array
     */

    public function add($data)
    {

        $addData = [
            'weixin_nickname' => $data['weixin_nickname'],
            'weixin_id' => $data['weixin_id'],
            'time' => time(),
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


}
