<?php
// +---------------------------------------------------------------------+
// | MamiTianshi    | [ CREATE BY WF_RT TEAM ]                           |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Jack YanTC <yanshixin.com>                             |
// +---------------------------------------------------------------------+
// | Repository | git@github.com:fjwcoder/mamitianshi_server.git         |
// +---------------------------------------------------------------------+

namespace app\admin\widget;

/**
 * 文件上传小物件
 */
class File extends WidgetBase
{

    /**
     * 显示文件上传视图
     */
    public function index($name = '', $value = '', $type = '')
    {

        $this->assign('widget_data', compact('name', 'value', 'type'));

        $widget_config['maxwidth'] = '150px';

        $widget_config['allow_postfix'] = $type == 'img' ? '*.jpg; *.png; *.gif;' : '*.jpg; *.png; *.gif; *.zip; *.rar; *.tar; *.gz; *.7z; *.doc; *.docx; *.txt; *.xml; *.xlsx; *.xls;*.mp4;';

        $widget_config['max_size'] = 50 * 1024;
        
        $this->assign('widget_config', $widget_config);

        return $this->fetch('admin@widget/file/' . $type);
    }
}
