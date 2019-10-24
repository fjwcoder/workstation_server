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

namespace app\index\controller;


class Oss extends IndexBase
{
    /**
     *  上传静态文件到oss
     */
    public function uploadStaticFileToOss()
    {
        if(!IS_CLI) : return "请在cli模式下运行!防止浏览器超时";endif;
        $root = "static";
        $error = $this->logicOss->uploadStaticFile($root);
        return $error;
    }
}
