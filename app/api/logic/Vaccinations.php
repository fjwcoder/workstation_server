<?php
/**
 * Vaccinations 接种流水API逻辑层
 */

namespace app\api\logic;
use think\Db;

class Vaccinations extends ApiBase
{


    /**
     * 获取待接种队列
     */
    public function getWaitingInjectList()
    {

        $where_v = [
            'v.RegistrationFinishTime'=>['like', '%'.NOW_DATE.'%'],
            'v.Number'=>['like', '%V%'],
            'v.State'=>1,
        ];

        $where_a = [
            'v.RegistrationFinishTime'=>['like', '%'.NOW_DATE.'%'],
            'v.Number'=>['like', '%A%'],
            'v.State'=>1,
        ];

        $field = 'v.Id, v.Number, v.RegistrationFinishTime, c.Name, c.Sex';

        $where['v.Number'] = ['Number',];

        $dataVList = Db::name('vaccinations')->alias('v')->join('childs c','c.Id = v.ChildId','LEFT')->where($where_v)->field($field)->order('v.RegistrationFinishTime asc')->select();

        $dataAList = Db::name('vaccinations')->alias('v')->join('childs c','c.Id = v.ChildId','LEFT')->where($where_a)->field($field)->order('v.RegistrationFinishTime asc')->select();

        $dataList = array_merge($dataVList,$dataAList);

        return $dataList;

    }


    /**
     * 查看待接种详情
     */
    public function getWaitingInjectInfo($param = [])
    {

        if(empty($param['Id'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '查询失败'];

        $where = [
            'VaccinationId' => $param['Id']
        ];

        $field = 'y.ShortName';

        $this->modelVaccinationdetails->alias('d');

        $this->modelVaccinationdetails->join = [
            [SYS_DB_PREFIX . 'vaccines y', 'd.VaccineId = y.Id','LEFT'],
        ];

        $data = $this->modelVaccinationdetails->getList($where, $field, '', false);

        return $data;


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