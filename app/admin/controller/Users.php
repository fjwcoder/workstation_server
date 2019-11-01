<?php
/**
 * Users 用户控制器
 */

namespace app\admin\controller;

class Users extends AdminBase
{


    /**
     * 用户列表
     */
    public function userList()
    {

        // $where = [];

        // $data = $this->param;

        // !empty($data['search_data']) && $where['UserName|Name|EmailAddress'] = ['like', '%'.$data['search_data'].'%'];

        // $field = 'Id, UserName, EmailAddress, CreationTime, Name, IsActive';
        
        // $this->assign('list',$this->logicUsers->userList($where, $field));

        return $this->fetch();
    }

    /**
     * 获取用户列表
     */
    public function getUserList()
    {
        $where = [];

        $data = $this->param;

        !empty($data['search_data']) && $where['u.UserName|u.Name|u.EmailAddress'] = ['like', '%'.$data['search_data'].'%'];

        $field = 'u.Id, u.UserName, u.EmailAddress, u.CreationTime, u.Name, u.IsActive, u.md5_password, a.group_id';

        return $this->logicUsers->getUserList($where, $field);
    }


    /**
     * 禁用用户
     */
    public function isActiveUser()
    {

        return $this->logicUsers->isActiveUser($this->param);
        // $this->jump($this->logicUsers->isActiveUser($this->param));
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo()
    {
        $where = [
            'Id'=>$this->param['Id']
        ];

        $field = 'Id, UserName, EmailAddress, CreationTime, Name, IsActive, md5_password';
        
        return $this->logicUsers->getUserInfo($where, $field);
    }

    /**
     * 修改用户信息
     */
    public function editUserInfo()
    {
        return $this->logicUsers->editUserInfo($this->param);
    }

    /**
     * 重置用户密码
     */
    public function editUserPwd()
    {
        return $this->logicUsers->editUserPwd($this->param);
    }

    /**
     * 创建用户
     */
    public function addUserInfo()
    {
        return $this->logicUsers->addUserInfo($this->param);
    }


}