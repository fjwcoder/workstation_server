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

namespace app\admin\logic;

/**
 * 友情链接逻辑
 */
class Blogroll extends AdminBase
{
    
    /**
     * 获取友情链接列表
     */
    public function getBlogrollList($where = [], $field = true, $order = '', $paginate = 0)
    {
        
        return $this->modelObBlogroll->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 友情链接信息编辑
     */
    public function blogrollEdit($data = [])
    {
        
        $validate_result = $this->validateBlogroll->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateBlogroll->getError()];
        }
        
        $url = url('blogrollList');
        
        $result = $this->modelObBlogroll->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '友情链接' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelObBlogroll->getError()];
    }

    /**
     * 获取友情链接信息
     */
    public function getBlogrollInfo($where = [], $field = true)
    {
        
        return $this->modelObBlogroll->getInfo($where, $field);
    }
    
    /**
     * 友情链接删除
     */
    public function blogrollDel($where = [])
    {
        
        $result = $this->modelObBlogroll->deleteInfo($where);
        
        $result && action_log('删除', '友情链接删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelObBlogroll->getError()];
    }
}
