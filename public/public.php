<?php
// +---------------------------------------------------------------------+
// | MamiTianshi    | [ CREATE BY WF_RT TEAM ]                           |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Fjwcoder <fjwcoder@gmail.com>                          |
// +---------------------------------------------------------------------+
// | Repository | git@github.com:fjwcoder/mamitianshi_server.git         |
// +---------------------------------------------------------------------+

// PHP版本验证需要大于5.6.0
// if (version_compare(PHP_VERSION, '5.6.0', '<')) {
    
//     die('System Require PHP > 5.6.0 !');
// }

// 定义应用目录
define('APP_PATH', __DIR__ . '/../app/');

// 检测是否安装
// if (!file_exists(APP_PATH . 'database.php')) {
    
//     header("location:./install.php");
//     exit;
// }

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';