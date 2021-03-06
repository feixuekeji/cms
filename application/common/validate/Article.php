<?php
/**
 * Created by PhpStorm.
 * User: moTzxx
 * Date: 2018/10/25
 * Time: 9:55
 */
namespace app\common\validate;
use \think\Validate;
class Article extends Validate
{
    protected $rule = [
        'title'         =>  'require|max:100',
        'list_order'    =>  'require|number',
        'content'       =>  'require',
        'catalog1'    =>  'require|number',
        'catalog2'    =>  'require|number',
        '__token__'     =>  'require|token',
    ];
    protected $message  =   [
        'title.max'     =>  '标题不能超过100个字符',
        'title.require' =>   '标题不能为空',
        'list_order'    =>  '排序权重为整数',
        'content'       =>  '文章内容不能为空',
        'catalog1'    =>  '分类不能为空',
        'catalog2'    =>  '分类不能为空',
        '__token__'     =>  'Token非法操作或失效',
    ];

    protected $scene = [
        'default'  =>  ['title','list_order','content','catalog1','catalog2'],
        'token'    =>  ['__token__'],
    ];
}