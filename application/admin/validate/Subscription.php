<?php
/**
 * Created by PhpStorm.
 * User: moTzxx
 * Date: 2018/10/25
 * Time: 9:55
 */
namespace app\admin\validate;
use \think\Validate;
class Subscription extends Validate
{
    protected $rule = [
        'weixin_nickname'         =>  'require|max:30',
        'weixin_id'    =>  'require',
        '__token__'     =>  'require|token',
    ];
    protected $message  =   [
        'weixin_nickname.max'     =>  '标题不能超过30个字符',
        'weixin_nickname.require' =>   '标题不能为空',
        'weixin_id'    =>  '微信公众号不能为空',
        '__token__'     =>  'Token非法操作或失效',
    ];

    protected $scene = [
        'default'  =>  ['weixin_nickname','weixin_id'],
        'token'    =>  ['__token__'],
    ];
}