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

namespace app\Common\logic;


use think\Cache;

class Oss extends LogicBase 
{
    /**
     * 上传后缓存中存的是上传失败的文件路径,没有则表示全部成功
     * @param null $path
     * @return int|mixed
     */
    public function uploadStaticFile($path = null)
    {
        $path = str_replace("\\","/",$path);
        $files = $this->logicFile->getFileByPath($path);
        $this->serviceStorage->driverAliyun->uploadStaticFile($files);
        $errors = Cache::pull("upload_error");
        if(!empty($errors))
        {
            $errors = 0;
        }
        return is_array($files) ? json_decode($errors) : $errors;
    }
}
