<?php
namespace app\common\model;

use think\Db;
use \think\Model;
use think\facade\Session;
//use api\Upload;
use app\api\Controller\Upload;

class Collects extends BaseModel
{
    private $uploads;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->uploads = new Upload();
    }

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


    /**采集
     * @param $url
     * @return array|bool
     */
    function get_file_article($url)
    {

        $file = get_url($url);
        if(!$file){
            Log::error('错误信息'.'url错误');
            return false;
        }
        // 内容
        preg_match('/<div class="rich_media_content " id="js_content">[\s\S]*?<\/div>/',$file,$content);
        if (empty($content[0]))
            return false;
        // 标题
        preg_match('/<title>([\s\S]*?)<\/title>/',$file,$title);
        $title = $title?$title[1]:'';
        $title =  htmlspecialchars_decode(trim($title));
        //微信公众号
        preg_match('/var nickname = "([\s\S]*?)"/',$file,$wxgzh);
        $wxgzh = $wxgzh?$wxgzh[1]:'';
        //封面图片
        preg_match('/var msg_cdn_url = "([\s\S]*?)"/',$file,$picture);
        $picture = $picture?$picture[1]:'';
        $picture = $this->upload->qiniuFetch($picture);



        // 图片
        preg_match_all('/<img.*?data-src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg|\.?]))[\'|\"].*?[\/]?>/',$content[0],$images);
        // 储存原地址和下载后地址
        $old = array();
        $new = array();
        // 去除重复图片地址
        $images = array_unique($images[1]);

        if($images){
            foreach($images as $v){

                $filename = $this->upload->qiniuFetch($v);
                if($filename){
                    // 图片保存成功 替换地址
                    $old[] = $v;
                    $new[] = $filename;
                }else{
                    // 失败记录日志
                    Log::error('错误信息'.$v);

                }
            }
            $old[] = 'data-src';
            $new[] = 'src';
            $content = str_replace($old,$new,$content[0]);

        }


        $data = array('content'=>$content,'title'=>$title,'wxgzh' =>$wxgzh,'picture' => $picture);
        //return json_encode(array('data'=>$data,'status'=>200,'msg'=>'ok'));
        return $data;
    }
}