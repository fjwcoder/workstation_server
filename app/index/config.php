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

// 前端配置文件

empty(STATIC_DOMAIN) ? $static = [] :  $static['__STATIC__'] = STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME;

return [
    
    'template' => ['layout_on' =>  true, 'layout_name' => 'layout'],
    
    // 视图输出字符串内容替换
    'view_replace_str' => $static,
];
