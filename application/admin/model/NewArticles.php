<?php

namespace app\admin\model;

use app\common\model\BaseModel;
use app\api\controller\Shenjianshou;
use think\facade\Log;
use think\Db;
use think\Model;
use think\Session;

class NewArticles extends BaseModel
{
    protected $validate;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->shenjianshou = new Shenjianshou();

    }

    /**
     * 获取所有
     * @return array
     */
    public function getList()
    {
        $data = $this
            ->where('id', '>', 0)
            ->select()
            ->toArray();
        return $data;
    }


    public function getNewList()
    {
        $threeDayAgo = time() - 3 * 24 * 60 * 60;
        $data = $this
            ->where('publish_time', '>',$threeDayAgo)
            ->field('id,url,update_time')
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


    //判断是否存在
    public function isExist($title)
    {

        $res = $this
            ->where('title', $title)
            ->find();
        if ($res)
            return true;
        return false;


    }


    //判断是否存在
    public function isUpdated()
    {
        $today =strtotime(date('Y-m-d',time()));//零点时间戳
        $res = $this
            ->where('update_time','>', $today)
            ->find();
        if ($res)
            return true;
        return false;


    }


    /**
     * 后台获取数据列表
     * @param $curr_page
     * @param int $limit
     * @param null $search
     * @return array
     */
    public function getForPage($curr_page, $limit = 1, $search = null)
    {
        $res = $this
            ->whereLike('title', '%' . $search . '%')
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
    public function getCount($search = null)
    {
        $count = $this
            ->whereLike('title', '%' . $search . '%')
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

    public function add($weixin_id,$data)
    {

        $addData = [
            'weixin_id' => $weixin_id,
            'url' => $data['article_url'],
            'title' => $data['article_title'],
            'publish_time' => $data['article_publish_time'],
            'create_time' => time(),
        ];

            $tag = $this->insert($addData);
            $validateRes['tag'] = $tag;
        return $validateRes;
    }


    /**
     * 更新阅读量点赞数
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateViewAndAgree($id, $data)
    {


        $saveData = [
            'article_idx' => $data['article_idx'],
            'view_count' => $data['article_view_count'],
            'agree_count' => $data['article_agree_count'],
            'update_time' => time(),
        ];

        $saveTag = $this
            ->where('id', $id)
            ->update($saveData);
        $validateRes['tag'] = $saveTag;

        return $validateRes;
    }

    /**
     * 神箭手API获取文章链接及url
     * @param $list
     * @return int
     */
    public function getUrlAndTitle($list)
    {
        static $num = 0;
        foreach ($list as $k => $v)
        {

            $info = $this->shenjianshou->getNewArticle($v['weixin_id']);
            if ($info['error_code'] == 0)
            {
                $articleList =  $info['data']['articles'];
                foreach ($articleList AS $item => $value)
                {
                    if (!$this->isExist($value['article_title']))
                    {
                        $this->add($v['weixin_id'],$value);
                        $num++;
                    }

                }
            }
            else{
                    Log::info('最新文章API异常：',$info);

            }

        }
        return $num;
    }


    /**万维API
     * @param $list
     * @return int
     */
    public function getUrlAndTitle1($list)
    {
        static $num = 0;
        foreach ($list as $k => $v)
        {

            $info = $this->shenjianshou->getNew($v['weixin_id']);
            if ($info['showapi_res_code'] == 0 && $info['showapi_res_body']['ret_code'] == 0)
            {
                $articleList =  $info['showapi_res_body']['list'];
                foreach ($articleList AS $item => $value)
                {
                    if (!$this->isExist($value['title']))
                    {
                        $data['article_url'] = $value['url'];
                        $data['article_title'] = $value['title'];
                        $data['article_publish_time'] = strtotime($value['time']);
                        $this->add($v['weixin_id'],$data);
                        $num++;
                    }

                }
            }
            else{
                Log::info('最新文章API异常：'.json_encode($info));

            }

        }
        return $num;
    }

    public function  getViewAndAgree()
    {
        if ($this->isUpdated())
        {
            $data['msg'] = "已经更新过";
            return $data;
        }

        $list = $this->getNewList();
        static $num = 0;
        foreach ($list as $k => $v)
        {
            $info = $this->shenjianshou->getView($v['url']);
            if ($info['error_code'] == 0)
            {
                $this->updateViewAndAgree($v['id'],$info['data']);
                $num ++;
            }
            else{
                Log::info('阅读量API异常'.json_encode($info));
            }
        }
        $data['msg'] = "总更新".$num;
        return $data;
    }




}
