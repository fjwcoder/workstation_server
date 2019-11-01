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


    /**
     * 完成接种
     */
    public function completeInject($param = [])
    {

        if(empty($param['Id'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '操作失败'];
        
        if(empty($param['VaccinationUserId'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '操作失败'];

        // if(empty($param['ConsultationRoom'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '操作失败'];

        $where = [
            'Id'=>$param['Id'],
        ];

        $data = [
            'VaccinationFinishTime'=>date('Y-m-d H:i:s'),
            'VaccinationUserId'=>$param['VaccinationUserId'],
            'ConsultationRoom'=>!empty($param['ConsultationRoom'])? $param['ConsultationRoom']:'',
            'State'=>4,
        ];

        $result = $this->modelVaccinations->updateInfo($where, $data);

        return $result ? [API_CODE_NAME => 200, API_MSG_NAME => '接种成功'] : [API_CODE_NAME => 40002, API_MSG_NAME => '接种失败'];


    }
}