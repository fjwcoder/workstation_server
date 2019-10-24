<?php
/**
 * Childs 孩子控制器
 * by fqm in 19.9.30
 */

namespace app\admin\controller;

class Childs extends AdminBase
{



    /**
     * 孩子列表
     */
    public function childsList()
    {
        // $where = [];

        // !empty($this->param['search_data']) && $where['Name|CardNo'] = ['like', '%'.(string)$this->param['search_data'].'%'];

        // $this->assign('list',$this->logicChilds->childsList($where));

        return $this->fetch();
    }

    /**
     * 获取孩子列表
     */
    public function getChildsList()
    {
        $where = [
            'IsDeleted' => 0,
        ];

        $data = $this->param;

        !empty($data['search_data']) && $where['Name|CardNo|Mobile|ParentName'] = ['like', '%'.(string)$data['search_data'].'%'];

        $paginate = 15;

        !empty($data['limit']) && $paginate = $data['limit'];

        $field = 'Id,Name,CardNo,Sex,BirthDate,ParentName,Address,Mobile';

        $order = 'Id desc';

        return $this->logicChilds->getChildsList($where, $field, $order, $paginate);

        // $this->assign('list',$this->logicChilds->childsList($where));
    }


    /**
     * 根据卡号获取孩子信息
     */
    public function cardNoGetInfo()
    {
        return $this->logicChilds->cardNoGetInfo($this->param);
    }


    /**
     * 获取孩子信息
     */
    public function getChildsInfo()
    {

        // IS_POST && $this->jump($this->logicChilds->editChildsInfo($this->param));

        $this->assign('info',$this->logicChilds->getChildsInfo($this->param));

        // $this->assign('childvaccines',$this->logicChilds->getChildVaccines($this->param));

        return $this->fetch('childs_info');

    }

    /**
     * 修改孩子信息
     */
    public function editChildsInfo()
    {

        // IS_POST && $this->jump($this->logicChilds->editChildsInfo($this->param));

        // $this->assign('info',$this->logicChilds->getChildsInfo($this->param));

        // return $this->fetch('edit_childs_info');

        return $this->logicChilds->editChildsInfo($this->param);

    }

    /**
     * 添加孩子
     */
    public function addChilds()
    {
        IS_POST && $this->jump($this->logicChilds->editChildsInfo($this->param));

        return $this->fetch('edit_childs_info');
    }

    /**
     * 登记时添加孩子信息
     */
    public function addChildInfo()
    {
        return $this->logicChilds->editChildsInfo($this->param);
    }

    /**
     * 删除孩子
     */
    public function delChilds()
    {
        return $this->logicChilds->delChilds($this->param);
        // $this->jump($this->logicChilds->delChilds($this->param));
    }



}