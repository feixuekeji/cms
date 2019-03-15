文章系统

## 使用

后台URL

~~~
http://test.com/index.php/admin/index/index
~~~



初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application                        应用目录
│  ├─common                         公共模块目录
│  │  ├─controller                 控制器目录
│  │  │  ├─Base.php               基类
│  │  │  ├─BaseCms.php            权限验证
│  │  │  ├─Catalog.php            目录控制器
│  │  ├─model                     模型目录
│  │  │  ├─AdminRoles.php         管理员角色
│  │  │  ├─Admins.php             管理员
│  │  │  ├─Articles.php           文章
│  │  │  ├─BaseModel.php          数据验证基类
│  │  │  ├─Catalogs.php           分类
│  │  ├─validata                  验证器
│  ├─api             
│  │  ├─controller                控制器目录
│  │  │  ├─Shenjianshou.php      神箭手接口
│  │  │  ├─Token.php             token获取验证接口
│  │  │  ├─Upload.php            上传接口

│  ├─admin                        后台模块
│  │  ├─common.php               模块函数文件
│  │  ├─controller               控制器目录
│  │  │  ├─Admin.php            管理员控制器
│  │  │  ├─Article.php          文章控制器
│  │  │  ├─Catalog.php          目录控制器
│  │  │  ├─Collect.php          微信文章采集
│  │  │  ├─Index.php            首页控制器
│  │  │  ├─Login.php            登录控制器
│  │  │  ├─NavMenu.php          菜单控制器
│  │  │  ├─NewArticle.php       公众号最新文章
│  │  │  ├─Subscription.php      公众号管理
│  │  ├─model                    模型目录
│  │  │  ├─NewArticles.php      公众号最新文章
│  │  │  ├─Subscriptions.php      公众号
│  │  ├─view                      视图目录
│  │  └─ ...                     更多类库目录
│  │
│  ├─index             前台接口
│  │  ├─behavior      行为
│  │  │  ├─CORS.php      跨域处理
│  │  ├─controller      控制器目录
│  │  │  ├─Base.php      权限验证基类
│  │  │  ├─Index.php     前台数据接口
│  ├─command.php        命令行定义文件
│  ├─common.php         公共函数文件
│  └─tags.php           应用行为扩展定义文件
│
├─config                应用配置目录
│  ├─module_name        模块配置目录
│  │  ├─database.php    数据库配置
│  │  ├─cache           缓存配置
│  │  └─ ...            
│  │
│  ├─app.php            应用配置
│  ├─cache.php          缓存配置
│  ├─cookie.php         Cookie配置
│  ├─database.php       数据库配置
│  ├─log.php            日志配置
│  ├─session.php        Session配置
│  ├─template.php       模板引擎配置
│  └─trace.php          Trace配置
│
├─route                 路由定义目录
│  ├─route.php          路由定义
│  └─...                更多
│
├─public                WEB目录（对外访问目录）
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重写
│
├─thinkphp              框架系统目录
│  ├─lang               语言文件目录
│  ├─library            框架类库目录
│  │  ├─think           Think类库包目录
│  │  └─traits          系统Trait目录
│  │
│  ├─tpl                系统模板目录
│  ├─base.php           基础定义文件
│  ├─console.php        控制台入口文件
│  ├─convention.php     框架惯例配置文件
│  ├─helper.php         助手函数文件
│  ├─phpunit.xml        phpunit配置文件
│  └─start.php          框架入口文件
│
├─extend                扩展类库目录
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
~~~

