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

namespace app\common\model;

/**
 * 插件模型
 */
class ObAddon extends ModelBase
{
    
    /**
     * 获取插件模型层实例
     */
    public function __get($name)
    {
        
        return addon_ioc($this, $name, LAYER_MODEL_NAME);
    }
}
