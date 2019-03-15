<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');

Route::rule('edit/:id','index/index/edit');

Route::get('admin/menu/index','admin/navMenu/index');
Route::any('admin/menu/add','admin/navMenu/add');
Route::any('admin/menu/edit/:id','admin/navMenu/edit');
Route::any('admin/menu/auth/:id','admin/navMenu/auth');
Route::post('admin/menu/ajaxOpForPage','admin/navMenu/ajaxOpForPage');


//前端接口
Route::any('getToken','api/token/getToken');
Route::any('getArticleList','index/index/getArticleList');
Route::any('getArticleInfo','index/index/getArticleInfo');
Route::any('getHotArticleList','index/index/getHotArticleList');
Route::any('getSimilarList','index/index/getSimilarList');
Route::any('getToken','index/index/getSimilarList');
Route::any('getCatagory','index/index/getCatagory');
Route::any('getArticleListByCatalog','index/index/getArticleListByCatalog');
Route::any('getKeywordList','index/index/getKeywordList');


return [

];
