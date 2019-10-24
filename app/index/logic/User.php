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

namespace app\index\logic;

/**
 * 登记台逻辑
 */
class User extends IndexBase
{
    
    /**
     * 获取会员信息
     */
    public function getUserInfo($where = [], $field = true)
    {

        $info = $this->modelUsers->getInfo($where, $field);
        
        // $info['leader_nickname'] = $this->modelUser->getValue(['id' => $info['leader_id']], 'nickname');
        
        return $info;
    }
    
    /**
     * 获取会员列表
     */
    public function getUserList($where = [], $field = 'm.*,b.nickname as leader_nickname', $order = '', $paginate = DB_LIST_ROWS)
    {
        
        $this->modelUsers->alias('m');
        
        $join = [
                    [SYS_DB_PREFIX . 'user b', 'm.leader_id = b.id', 'LEFT'],
                ];
        
        $where['m.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        
        $this->modelUsers->join = $join;
        
        return $this->modelUsers->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 导出会员列表
     */
    public function exportUserList($where = [], $field = 'm.*,b.nickname as leader_nickname', $order = '')
    {
        
        $list = $this->getUserList($where, $field, $order, false);
        
        $titles = "昵称,用户名,邮箱,手机,注册时间,上级";
        $keys   = "nickname,username,email,mobile,create_time,leader_nickname";
        
        action_log('导出', '导出会员列表');
        
        export_excel($titles, $keys, $list, '会员列表');
    }
    
    /**
     * 获取会员列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['m.nickname|m.username|m.email|m.mobile'] = ['like', '%'.$data['search_data'].'%'];
        
        if (!is_administrator()) {
            
            $user = session('user_info');
            
            if ($user['is_share_user']) {
                
                $ids = $this->getInheritUserIds(MEMBER_ID);
                
                $ids[] = MEMBER_ID;
                
                $where['m.leader_id'] = ['in', $ids];
                
            } else {
                
                $where['m.leader_id'] = MEMBER_ID;
            }
        }
        
        return $where;
    }
    
    /**
     * 获取存在继承关系的会员ids
     */
    public function getInheritUserIds($id = 0, $data = [])
    {
        
        $user_id = $this->modelUser->getValue(['id' => $id, 'is_share_user' => DATA_NORMAL], 'leader_id');
        
        if (empty($user_id)) {
            
            return $data;
        } else {
            
            $data[] = $user_id;
            
            return $this->getInheritUserIds($user_id, $data);
        }
    }
    
    /**
     * 获取会员的所有下级会员
     */
    public function getSubUserIds($id = 0, $data = [])
    {
        
        $user_list = $this->modelUser->getList(['leader_id' => $id], 'id', 'id asc', false);
        
        foreach ($user_list as $v)
        {
            
            if (!empty($v['id'])) {
                
                $data[] = $v['id'];
            
                $data = array_unique(array_merge($data, $this->getSubUserIds($v['id'], $data)));
            }
            
            continue;
        }
            
        return $data;
    }
    
    /**
     * 会员添加到用户组
     */
    public function addToGroup($data = [])
    {
        
        $url = url('userList');
        
        if (SYS_ADMINISTRATOR_ID == $data['id']) {
            
            return [RESULT_ERROR, '天神不能授权哦~', $url];
        }
        
        $where = ['user_id' => ['in', $data['id']]];
        
        $this->modelAuthGroupAccess->deleteInfo($where, true);
        
        if (empty($data['group_id'])) {
            
            return [RESULT_SUCCESS, '会员授权成功', $url];
        }
        
        $add_data = [];
        
        foreach ($data['group_id'] as $group_id) {
            
            $add_data[] = ['user_id' => $data['id'], 'group_id' => $group_id];
        }
        
        if ($this->modelAuthGroupAccess->setList($add_data)) {
            
            action_log('授权', '会员授权，id：' . $data['id']);
        
            $this->logicAuthGroup->updateSubAuthByUser($data['id']);
            
            return [RESULT_SUCCESS, '会员授权成功', $url];
        } else {
            
            return [RESULT_ERROR, $this->modelAuthGroupAccess->getError()];
        }
    }
    
    /**
     * 会员添加
     */
    public function userAdd($data = [])
    {
        
        $validate_result = $this->validateUser->scene('add')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateUser->getError()];
        }
        
        $url = url('userList');
        
        $data['nickname']  = $data['username'];
        $data['leader_id'] = MEMBER_ID;
        $data['is_inside'] = DATA_NORMAL;
        
        $data['password'] = data_md5_key($data['password']);
        
        $result = $this->modelUser->setInfo($data);
        
        $result && action_log('新增', '新增会员，username：' . $data['username']);
        
        return $result ? [RESULT_SUCCESS, '会员添加成功', $url] : [RESULT_ERROR, $this->modelUser->getError()];
    }
    
    /**
     * 会员编辑
     */
    public function userEdit($data = [])
    {
        
        $validate_result = $this->validateUser->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateUser->getError()];
        }
        
        $url = url('userList');
        
        $result = $this->modelUser->setInfo($data);
        
        $result && action_log('编辑', '编辑会员，id：' . $data['id']);
        
        return $result ? [RESULT_SUCCESS, '会员编辑成功', $url] : [RESULT_ERROR, $this->modelUser->getError()];
    }
    
    /**
     * 修改密码
     */
    public function editPassword($data = [])
    {
        
        $validate_result = $this->validateUser->scene('password')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateUser->getError()];
        }
        
        $user = $this->getUserInfo(['id' => $data['id']]);
        
        if (data_md5_key($data['old_password']) != $user['password']) {
            
            return [RESULT_ERROR, '旧密码输入不正确'];
        }
        
        $data['id'] = MEMBER_ID;
        
        $url = url('index/index');
        
        $result = $this->modelUser->setInfo($data);
        
        $result && action_log('编辑', '会员密码修改，id：' . $data['id']);
        
        return $result ? [RESULT_SUCCESS, '密码修改成功', $url] : [RESULT_ERROR, $this->modelUser->getError()];
    }
    
    /**
     * 设置会员信息
     */
    public function setUserValue($where = [], $field = '', $value = '')
    {
        
        return $this->modelUser->setFieldValue($where, $field, $value);
    }
    
    /**
     * 会员删除
     */
    public function userDel($where = [])
    {
        
        $url = url('userList');
        
        if (SYS_ADMINISTRATOR_ID == $where['id'] || MEMBER_ID == $where['id']) {
            
            return [RESULT_ERROR, '天神和自己不能删除哦~', $url];
        }
        
        $result = $this->modelUser->deleteInfo($where);
                
        $result && action_log('删除', '删除会员，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '会员删除成功', $url] : [RESULT_ERROR, $this->modelUser->getError(), $url];
    }
}
