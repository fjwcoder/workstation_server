<?php
/**
 * Vaccinations 接种流水API逻辑层
 */

namespace app\api\logic;

class Vaccinations extends ApiBase
{


    /**
     * 获取待接种队列
     */
    public function getWaitingInjectList()
    {

        $where = [
            'v.RegistrationFinishTime'=>['like', '%'.NOW_DATE.'%'],
            'v.State'=>1,
        ];

        $field = 'v.Id, v.Number, v.RegistrationFinishTime, c.Name, c.Sex, y.ShortName';

        $this->modelVaccinations->alias('v');

        $this->modelVaccinations->join = [
            [SYS_DB_PREFIX . 'childs c', 'c.Id = v.ChildId','LEFT'],
            [SYS_DB_PREFIX . 'vaccinationdetails d', 'd.VaccinationId = v.Id','LEFT'],
            [SYS_DB_PREFIX . 'vaccines y', 'd.VaccineId = y.Id','LEFT'],
        ];

        $dataList = $this->modelVaccinations->getList($where, $field, 'v.RegistrationFinishTime asc' ,false);

        return $dataList;


    }
}