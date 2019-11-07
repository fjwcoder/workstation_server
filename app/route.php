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

// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],

// ];

use think\Route;

Route::rule([

    'api/queue/push'                   => '@api/queue/push',
    'callname'                         => '@api/common/callName',
    'getWaitingInjectList'             => '@api/Vaccinations/getWaitingInjectList',
    'completeInject'                   => '@api/Vaccinations/completeInject',
    'userLogin'                        => '@api/Common/userLogin',
    'getWaitingInjectInfo'             => '@api/Vaccinations/getWaitingInjectInfo',
    'nextNumber'                       => '@api/Vaccinations/nextNumber',
]);
