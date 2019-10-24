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

namespace app\common\behavior;

use think\Hook;

/**
 * 初始化钩子信息行为
 */
class InitHook
{

    /**
     * 行为入口
     */
    public function run()
    {
        
        $hook  = model(SYS_COMMON_DIR_NAME . SYS_DS_PROS . ucwords(SYS_HOOK_DIR_NAME));

        $list = auto_cache('hook_list', create_closure($hook, 'column', ['id,name,addon_list']));
        
        foreach ($list as $v) {
            
          $addon_list[$v['name']] = get_addon_class($v['addon_list']);  
        }
        
        Hook::import($addon_list);
    }
}
