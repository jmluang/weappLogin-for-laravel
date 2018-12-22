# laravel-weapp
A weapp login logic Laravel warpper

本仓库从 [wafer2开发套件](https://github.com/tencentyun/wafer2-quickstart-php) 中提取并封装了微信小程序的登陆逻辑并转移到Laravel中，不仅降低开发者的学习成本，而且能快速完成小程序的登陆功能  ***甚至连数据库都不需要担心，因为插件已经包装好数据库的操作了***。您只需要导入相关的[表](https://github.com/jmluang/laravel-weapp/blob/master/src/database/cSessionInfo.sql)到数据库中即可。
若需要使用自己的数据库和用户的逻辑操作，只需要继承相应的接口和提供 Facade 类就可以了。详情可以查看[使用自己的数据库和逻辑](#使用自己的数据库和逻辑)


# 特点
 - 使用了 Guzzlehttp 来发送请求
 - 使用了 Laravel 的 Eloquent ORM 封装了数据库操作，只需要导入表到数据库中即可。当然了，您也可以使用您自己的逻辑

# 注意
 - 请配合开发者工具和js-sdk使用
 
# 安装
只需要五步即可完成安装部署。

1. 通过 composer 安装:
` composer require jmluang/weapp:2.* `

2. 添加 Provider 到` config/app.php `中
```php
 'providers' =>[
     // Laravel Framework Service Providers
     // ...
	 jmluang\\weapp\\WeappLoginServiceProvider::class,
 ]
```

3. 发布配置文件
```php
php artisan vendor:publish --provider="jmluang\weapp\WeappLoginServiceProvider"
```
参数如下：

| 参数 | 值 | 说明|
|--|--|--|
| appid | 你的 AppID | 必须 |
| secret | 你的 AppSecret | 必须 |
| code2session_url | 默认url | 不用改变 |
| WxLoginExpires | 7200（秒） | 选填，填写前先取消备注 |
| NetworkTimeout | 3000（毫秒） | 选填，填写前先取消备注 |

配置参数有两种方法，一种是直接写到` weapp.php `文件中，另一种是写到` .env `文件中，使用哪种方法都可以。但是有一点需要注意，若你的项目会发布到开源社区，则不推荐使用第一种方法，因为这样做存在泄露信息的风险。

`WxLoginExpires` 和 `NetworkTimeout` 都使用了默认的参数，如果你有特殊的需求需要改这两个参数，只要取消备注并填写即可。 

4. 添加数据库 Facade 到` config/app.php `中
```php
'aliases' => [
    // Laravel Framework Facades
    // ...
	'WeappUserRepository' => jmluang\weapp\Facades\WeappUser::class,
]
```
若重写了数据库逻辑，则这里应该使用你自己的 Facade 类：
```php
'aliases' => [
    // Laravel Framework Facades
    // ...
	'WeappUserRepository' => path\to\your\FacadeClass::class,
```

5. 迁移数据库
```php
php artisan migrate
```
若重写了数据库逻辑，则可以忽略这一步

# 使用方法
1. Laravel 配置
安装完成后，下面创建一个控制器和路由规则
路由文件：
```php
// filepath routes/web.php
<?php 
Route::get('/weapp/login',"LoginController@login");
Route::get('/weapp/user',"LoginController@user");
```

控制器：
```php 
// filepath app/Http/Controllers/LoginController.php
<?php

namespace App\Http\Controllers;
use jmluang\weapp\Constants;
use jmluang\weapp\WeappLoginInterface as LoginInterface;

class LoginController extends Controller
{
    /**
     * 首次登陆
     * @param LoginInterface $login
     * @return array
     */
    public function login(LoginInterface $login)
    {
        $result = $login::login();

        if ($result['loginState'] === Constants::S_AUTH) {
            return [
                'code' => 0,
                'data' => $result['userinfo']
            ];
        } else {
            return [
                'code' => -1,
                'error' => $result['error']
            ];
        }
    }

    /**
     * 登陆过就使用这个接口
     * @param LoginInterface $login
     * @return array
     */
    public function user(LoginInterface $login)
    {
        $result = $login::check();

        if ($result['loginState'] === Constants::S_AUTH) {
            return [
                'code' => 0,
                'data' => $result['userinfo']
            ];
        } else {
            return [
                'code' => -1,
                'data' => []
            ];
        }
    }
}
```

2. 微信小程序中
首先在小程序中引入 js-skd，然后就可以写相关的逻辑了
```
// 目录结构
project
├── app.js
├── app.json
├── app.wxss
├── ...
└── vendor
    └── weapp-login
        ├── lib
        │   ├── constants.js
        │   ├── login.js
        │   ├── request.js
        │   ├── session.js
        │   └── utils.js
        └── weapp.js
```

```javascript
// filepath app.js
var login = require('./vendor/weapp')

App({
    onLaunch: function(){
        // 设置登陆url，对应上面Controll的Login方法
        login.setLoginUrl("https://localhost/weapp/login")
        login.login({
            success(result) {
                if (result) {
                  // 首次登陆
                  console.log("登陆成功", result)
                } else {
                  // 二次登陆，请求Controller的User方法
                  login.request({
                    url: "https://localhost/weapp/user",
                    login: true,
                    success(result) {
                      console.log("登陆成功", result.data.data)
                    },
                    fail(error) {
                      console.log("登录失败", error)
                    }
                  })
                }
            },
            fail(error) { console.log("登录失败", error) }
        })
    }
})
```


# 使用自己的数据库和逻辑

要使用自己的数据库逻辑，只需要简单的三步操作
1. 继承接口类` jmluang\weapp\WeappUserInterface ` 并实现` storeUserInfo ` 和 ` findUserBySKey `方法
2. 创建Facade类
3. 在 ` config\app.php `的`aliases数组`中使用您的Facade类覆盖`jmluang\weapp\Facades\WeappUser::class`
```php
'aliases' => [
    // Laravel Framework Facades
    // ...
    // 'WeappUserRepository' => jmluang\weapp\Facades\WeappUser::class,
    'WeappUserRepository' => path\to\your\FacadeClass::class,
]
```
Done! 

----------



**若本仓库对您有所帮助，欢迎Start**

**若发现问题或需要帮助，欢迎提交Issue**

感谢！

# LICENSE
MIT


> Written with [StackEdit](https://stackedit.io/).
