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

namespace app\common\service\vod;

use app\common\service\BaseInterface;

/**
 * 云存储服务驱动
 */
interface Driver extends BaseInterface
{
    
    /**
     * 获取驱动参数
     */
    public function getDriverParam();
    
    /**
     * 获取基本信息
     */
    public function driverInfo();
    
    /**
     * 配置信息
     */
    public function config();
    
    /**
     * 初始化客户端
     */
    public function initVodClient();
    
    /**
     * 获取视频上传地址和凭证
     */
    public function createUploadVideo($title, $file_name, $description, $cover_url, $tags);
    
    /**
     * 上传视频文件
     */
    public function uploadVideo($object, $local_file_name);
}
