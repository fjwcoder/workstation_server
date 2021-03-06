# 项目介绍


* * * * *

[爱纳吉冰箱服务端]，是基于 ThinkPHP5框架、OneBase CMS，由WF_RT研发团队重新开发。
项目开始时间：2019-07-26
负责人：冯建文 fjwcoder@gmail.com


# 项目主要功能

**一、提供接种中心的冰柜的接口服务** 




# 系统架构图


* * * * *

![OneBase系统架构图](https://gitee.com/uploads/images/2017/1228/112704_2e32357d_917834.png "OneBase系统架构图.png")



# 接口模块双向数据验证流程图


* * * * *

![接口模块双向数据验证流程图](https://gitee.com/uploads/images/2018/0201/115449_8acedb9d_917834.png "ApiSafety.png")


# 全自动缓存流程图


* * * * *

![全自动缓存流程图](https://images.gitee.com/uploads/images/2019/0531/000844_df9f63b4_917834.png "全自动缓存流程图.png")


# 组织结构


* * * * *



```
project                             应用部署目录
├─addon                             插件目录
│  ├─editor                         插件
│  │  ├─controller                  插件控制器目录
│  │  ├─data                        插件数据如安装卸载脚本目录
│  │  ├─logic                       插件逻辑目录
│  │  ├─static                      插件静态资源目录
│  │  ├─view                        插件视图目录
│  │  └─Editor.php                  插件类文件
│  ├─ ...                           更多插件
│  └─AddonInterface.php             插件接口文件
├─app                               应用目录
│  ├─common                         公共模块目录
│  │  ├─behavior                    系统行为目录
│  │  │  ├─AppBegin.php             应用开始行为
│  │  │  ├─AppEnd.php               应用结束行为
│  │  │  ├─InitBase.php             应用初始化基础信息行为
│  │  │  └─InitHook.php             应用初始化钩子与插件行为
│  │  ├─controller                  系统公用控制器目录
│  │  │  ├─AddonBase.php            插件控制器基类
│  │  │  └─ControllerBase.php       系统通用控制器基类
│  │  ├─logic                       系统公用逻辑目录
│  │  ├─model                       系统公用模型目录
│  │  ├─validate                    系统公用验证目录
│  │  ├─service                     系统公用服务目录
│  │  │  ├─pay                      支付服务目录
│  │  │  ├─storage                  云存储服务目录
│  │  │  ├─BaseInterface.php        服务接口
│  │  │  ├─ServiceBase.php          服务基础类
│  │  │  ├─Pay.php                  支付服务类
│  │  │  ├─Storage.php              云存储服务类
│  │  │  └─ ...                     更多服务
│  │  └─view                        系统公用视图目录
│  ├─api                            API模块目录
│  │  ├─controller                  API接口控制器目录
│  │  ├─error                       API错误码目录
│  │  ├─logic                       API业务逻辑目录
│  │  └─...                         更多目录
│  ├─admin                          后台模块目录
│  ├─index                          前端模块目录
│  ├─install                        安装模块目录
│  ├─command.php                    命令行工具配置文件
│  ├─common.php                     应用公共（函数）文件
│  ├─config.php                     应用（公共）配置文件
│  ├─database.php                   数据库配置文件
│  ├─tags.php                       应用行为扩展定义文件
│  ├─route.php                      路由配置文件
│  └─...                            更多模块与文件
├─data                              数据库备份目录
├─extend                            扩展类库目录
├─tool                              工具目录
├─public                            Web 部署目录（对外访问目录）
│  ├─static                         静态资源存放目录(css,js,image)
│  ├─upload                         系统文件上传存放目录
│  ├─index.php                      应用前端入口文件
│  ├─api.php                        应用API接口入口文件
│  ├─admin.php                      应用后台入口文件
│  └─.htaccess                      用于 apache 的重写
├─runtime                           应用的运行时目录（可写，可设置）
├─vendor                            第三方类库目录（Composer）
├─thinkphp                          框架系统目录
│  ├─lang                           语言包目录
│  ├─library                        框架核心类库目录
│  │  ├─think                       Think 类库包目录
│  │  └─traits                      系统 Traits 目录
│  ├─tpl                            系统模板目录
│  ├─.travis.yml                    CI 定义文件
│  ├─base.php                       基础定义文件
│  ├─composer.json                  composer 定义文件
│  ├─console.php                    控制台入口文件
│  ├─convention.php                 惯例配置文件
│  ├─helper.php                     助手函数文件（可选）
│  ├─LICENSE.txt                    授权说明文件
│  ├─phpunit.xml                    单元测试配置文件
│  ├─README.md                      README 文件
│  └─start.php                      框架引导文件
├─build.php                         自动生成定义文件（参考）
├─composer.json                     composer 定义文件
├─LICENSE.txt                       授权说明文件
├─README.md                         README 文件
└─think                             命令行入口文件
```