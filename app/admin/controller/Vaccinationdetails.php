<?php
/**
 * Vaccinationdetails 接种流水控制器
 */

namespace app\admin\controller;

class Vaccinationdetails extends AdminBase
{


    /**
     * 根据接种流水ID获取接种流水详情
     */
    public function getVIdObVdetail()
    {

        return $this->logicVaccinationdetails->getVIdObVdetail($this->param);
    }

    /**
     * 登记资料时，设置接种疫苗的接种流水
     */
    public function setVaccinationDetail()
    {
        return $this->logicVaccinationdetails->setVaccinationDetail($this->param);
    }

    /**
     * 删除接种流水
     */
    public function delVaccinationDetail()
    {
        return $this->logicVaccinationdetails->delVaccinationDetail($this->param);
    }

}