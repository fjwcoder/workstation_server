<?php
/*
 * @Descripttion: Vaccine控制器
 * @Author: fqm
 * @Date: 2019-08-20 11:15:53
 */

namespace app\admin\controller;

class Vaccine extends AdminBase
{


    /**
     * 疫苗列表
     */
    public function vaccineList()
    {
        $where = $this->logicVaccine->getWhere($this->param);

        $this->assign('list',$this->logicVaccine->getVaccineList($where));
        return $this->fetch('list');
    }

    /**
     * 疫苗添加
     */
    public function vaccineAdd()
    {
        IS_POST && $this->jump($this->logicVaccine->vaccineEdit($this->param));

        return $this->fetch('vaccine_edit');
    }

    /**
     * 疫苗编辑
     */
    public function vaccineEdit()
    {
        IS_POST && $this->jump($this->logicVaccine->vaccineEdit($this->param));

        $this->assign('info',$this->logicVaccine->get_vaccine_info($this->param['id']));

        return $this->fetch('vaccine_edit');
    }


    /**
     * 删除 / 修改状态
     */
    public function setStatus()
    {
        $this->jump($this->logicAdminBase->setStatus('MVaccine', $this->param));
    }




}