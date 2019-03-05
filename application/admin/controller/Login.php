<?php

namespace app\admin\Controller;

use app\common\model\Admins;
use app\common\model\NavMenus;
use think\facade\Session;
use think\Request;

class Login
{
    private $adminModel;
    private $navMenuModel;
    public function __construct()
    {
        $this->adminModel = new Admins();
        $this->navMenuModel = new NavMenus();
    }

    /**
     * 登录页
     * @return \think\response\View
     */

    public function index(){
        if (Session::has('AID')){
            return redirect('admin/index/index');
        }else{
            return view('login');
        }
    }

    /**
     * 登出账号
     * @return \think\response\Redirect
     */
    public function logout(){
        if (Session::has('AID')){
            Session::delete('AID');
        }
        return redirect('admin/login/login');
    }
    /**
     * ajax 进行管理员的登录操作
     * @param Request $request
     */
    public function ajaxLogin(Request $request){
        if ($request->isPost()){
            $input = $request->post();
            $tagRes = $this->adminModel->adminLogin($input);
            if ($tagRes['tag']){
                Session::set('AID', $tagRes['tag']);
            }
            return showMsg($tagRes['tag'],$tagRes['message']);
        }else{
            return showMsg(0,'sorry,您的请求不合法！');
        }
    }

    public function test()
    {

    }

    /**
     * ajax 检查登录状态
     * @param Request $request
     */
    public function ajaxCheckLoginStatus(Request $request)
    {
        if ($request->isPost()){
            $cmsAID = Session::get('AID');
            $nav_menu_id = $request->param('nav_menu_id');
            //TODO 判断当前菜单是否属于他的权限内
            $checkTag = $this->navMenuModel->checkNavMenuMan($nav_menu_id,$cmsAID);
            if ($cmsAID && $checkTag){
                return showMsg(1,'正在登录状态');
            }else{
                return showMsg(0,'未在登录状态');
            }
        }else{
            return showMsg(0,'sorry,您的请求不合法！');
        }
    }
}
