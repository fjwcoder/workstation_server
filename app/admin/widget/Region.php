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
 * 区域选择小物件
 */
class Region extends WidgetBase
{

    /**
     * 显示小图标选择视图
     */
    public function index($name = '', $province = '', $city = '', $county = '')
    {
        
        $this->assign('widget_data', compact('name', 'province', 'city', 'county'));

        return $this->fetch('admin@widget/region/index');
    }
}
