<?php
namespace app\common\validate;
use \think\Validate;
class Catalog extends Validate
{
    protected $rule = [
        'name'         =>  'require|max:50',
        'pid'    =>  'require|number',
        'list_order' =>  'require|number',
        '__token__'     =>  'require|token',

    ];
    protected $message  =   [
        'name.max'     =>  '标题不能超过50个字符',
        'name.require' =>   '标题不能为空',
        'pid.require'    =>  '上级分类不能为空',
        '__token__'     =>  'Token非法操作或失效',

    ];

    protected $scene = [
        'default'  =>  ['name','pid','list_order'],
        'token'    =>  ['__token__'],
    ];
}