<?php
/*
 * @Descripttion: Vaccine 疫苗api控制器
 * @Author: fqm
 * @Date: 2019-08-20 14:26:04
 */

namespace app\api\controller;

class Vaccine extends ApiBase
{

    /**
     * 疫苗列表
     */
    public function vaccineList()
    {
        return $this->apiReturn($this->logicVaccine->get_vaccine_list($this->param));
    }

    /**
     * 疫苗详情
     */
    public function vaccineInfo()
    {
        return $this->apiReturn($this->logicVaccine->get_vaccine_info($this->param));
    }   


}
