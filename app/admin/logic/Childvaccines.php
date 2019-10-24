<?php
/**
 * Childvaccines 孩子接种疫苗历史逻辑层
 */

namespace app\admin\logic;

class Childvaccines extends AdminBase
{


    /**
     * 获取当前孩子接种疫苗历史
     */
    public function getChildvaccines($param = [])
    {

        $where = [
            'c.ChildId' => $param['ChildId'],
            'c.IsDeleted' => 0,
        ];

        $this->modelChildvaccines->alias('c');

        $this->modelChildvaccines->join = [
            [SYS_DB_PREFIX . 'vaccines v', 'c.VaccineId = v.Id', 'LEFT'],
        ];
        
        $field = 'c.Id, c.VaccineId, c.Times, c.LotNumber, c.Company, c.VaccinationPosition, c.VaccinationPlace, c.IsFree, v.FullName';

        return $this->modelChildvaccines->getList($where, $field, 'c.Id desc', false);



    }
}