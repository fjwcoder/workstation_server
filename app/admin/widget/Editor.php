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

namespace app\admin\widget;

/**
 * 编辑器小物件
 */
class Editor extends WidgetBase
{
    
    /**
     * 显示编辑器
     */
    public function index($name = '', $value = '')
    {
        
        $widget_config['editor_height'] = '300px';
        $widget_config['editor_resize_type'] = 1;
        
        $this->assign('widget_config', $widget_config);
        $this->assign('widget_data', compact('name', 'value'));
        
        return $this->fetch('admin@widget/editor/index');
    }
}
