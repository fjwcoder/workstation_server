<?php
/**
 * Vaccinationdetails 接种流水逻辑层
 */

namespace app\admin\logic;

class Vaccinationdetails extends AdminBase
{



    /**
     * 根据接种流水ID获取接种流水详情
     */
    public function getVIdObVdetail($param = [])
    {

        $where = [
            'a.VaccinationId' => $param['VaccinationId'],
            'a.IsDeleted'=>0,
        ];

        $this->modelVaccinationdetails->alias('a');

        $this->modelVaccinationdetails->join = [
            [SYS_DB_PREFIX . 'vaccines v', 'a.VaccineId = v.Id','LEFT'],
        ];

        $field = 'a.Id, a.VaccinationId, a.VaccineId, a.Times, a.VaccinationDate, a.LotNumber, a.Company, a.IsFree, a.VaccinationPosition, a.VaccinationPlace, v.ShortName';

        $vDetail = $this->modelVaccinationdetails->getList($where, $field, 'Id asc', false);

        return $vDetail;


    }


    /**
     * 登记资料时，设置接种疫苗的接种流水
     */
    public function setVaccinationDetail($param = [])
    {

        $time = date("Y-m-d h:i:s");

        // $where = [
        //     'VaccinationDate' => ['like'.'%'.NOW_DATE.'%'],
        //     'VaccineId' => '',
        // ];

        // $data = $this->modelVaccinationdetails->where($where)->getInfo($where);
        if(empty($param['vaccineDate'])){
            return ['code'=>400,'msg'=>'请先选择要接种的疫苗'];
        }

        $where = [
            'VaccinationId' => $param['VaccinationId'],
            'VaccinationDate' => ['like','%'.NOW_DATE.'%']
        ];

        foreach ($param['vaccineDate'] as $k => $v) {
            $data = [
                'CreationTime'=>$time,
                'CreatorUserId'=>MEMBER_ID,
                'LastModificationTime'=>$time,
                'LastModifierUserId'=>MEMBER_ID,
                'VaccinationDate'=>$time,
                'IsDeleted'=>0,
                'VaccinationId'=>$param['VaccinationId'],
            ];

            // 是否验证疫苗信息


            $where['VaccineId'] = empty($v['VaccineId']) ? 0: $v['VaccineId'];

            if(empty($v['VaccineId'])){
                return ['code'=>400,'msg'=>'请选择需要接种的疫苗'];
            }

    
            $vaccineInfo = $this->modelVaccinationdetails->getInfo($where);

            if($vaccineInfo && empty($v['Id'])){
                return ['code'=>400,'msg'=>'请勿重复登记'];
            }

            $data['Id'] = $v['Id'];
            $data['VaccineId'] = $v['VaccineId'];
            $data['Times'] = $v['Times'];
            $data['LotNumber'] = $v['LotNumber'];
            $data['Company'] = $v['Company'];
            $data['VaccinationPosition'] = $v['VaccinationPosition'];
            $data['IsFree'] = $v['IsFree'];
            // $data['VaccinationPlace'] = $v['VaccinationPlace'];

            $this->modelVaccinationdetails->setInfo($data);
        }
        

        return ['code'=>200,'msg'=>'登记成功'];

    }


    /**
     * 删除接种流水
     */
    public function delVaccinationDetail($param = [])
    {

        $where = [
            'Id'=>$param['Id'],
        ];

        $time = date("Y-m-d h:i:s");

        $data = [
            'LastModificationTime' => $time,
            'LastModifierUserId' => MEMBER_ID,
            'IsDeleted' => 1,
            'DeleterUserId' => MEMBER_ID,
            'DeletionTime' => $time,
        ];


        $result = $this->modelVaccinationdetails->updateInfo($where,$data);
        return $result;
    }





}
