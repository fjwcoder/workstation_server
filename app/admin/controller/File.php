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

namespace app\admin\controller;

/**
 * 文件控制器
 */
class File extends AdminBase
{
    
    /**
     * 图片上传
     */
    public function pictureUpload()
    {
        
        $result = $this->logicFile->pictureUpload();

        return json($result);
    }
    
    /**
     * 文件上传
     */
    public function fileUpload()
    {
        
        $result = $this->logicFile->fileUpload();

        return json($result);
    }

    public function checkPictureExists() {
        $result = $this->logicFile->checkPictureExists($this->param);
        $return_result = [];
        if($result) {
            $return_result['code'] = 1;
            $return_result['msg'] = '该图片已存在';
            $return_result['data'] = $result;
        }else {
            $return_result['code'] = 0;
            $return_result['msg'] = '该图片不存在';
            $return_result['data'] = '';
        }
        return json($return_result);
    }

    public function checkFileExists() {
        $result = $this->logicFile->checkFileExists($this->param);
        $return_result = [];
        if($result) {
            $return_result['code'] = 1;
            $return_result['msg'] = '该文件已存在';
            $return_result['data'] = $result;
        }else {
            $return_result['code'] = 0;
            $return_result['msg'] = '该文件不存在';
            $return_result['data'] = '';
        }
        return json($return_result);
    }
}
