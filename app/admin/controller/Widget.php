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
use app\common\controller\ControllerBase;
use app\common\logic\File as LogicFile;

/**
 * Widget控制器
 */
class Widget extends ControllerBase
{
    
    /**
     * 获取区域选项信息
     */
    public function getRegionOptions($upid = 0, $level = 1, $select_id = 0)
    {
        
        return $this->logicRegion->getRegionOptions($upid, $level, $select_id);
    }
    
    /**
     * 编辑器图片上传
     */
    public function editorPictureUpload()
    {
        
        $result = get_sington_object('fileLogic', LogicFile::class)->pictureUpload('imgFile');
        
        $data  = false === $result ? [RESULT_ERROR => DATA_NORMAL, RESULT_MESSAGE => '文件上传失败'] : [RESULT_ERROR => DATA_DISABLE, RESULT_URL => get_picture_url($result['id'])];
        
        return throw_response_exception($data);
    }
}
