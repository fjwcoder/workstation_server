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
        $result = false;

        $param = [
            'Id'=>1908,
            'VaccinationUserId'=>1
        ];

        if(empty($param['Id'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '操作失败'];
        
        if(empty($param['VaccinationUserId'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '操作失败'];

        // if(empty($param['ConsultationRoom'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '操作失败'];

        $time = date('Y-m-d H:i:s');

        $where = [
            'Id'=>$param['Id'],
        ];

        $data = [
            'VaccinationFinishTime'=>$time,
            'VaccinationUserId'=>$param['VaccinationUserId'],
            'ConsultationRoom'=>!empty($param['ConsultationRoom'])? $param['ConsultationRoom']:'',
            'State'=>4,
        ];

        $app_result = $this->editOrderInfo($param['Id'], 3);

        if($app_result){
            $result = $this->modelVaccinations->updateInfo($where, $data);

            $childId = $this->modelVaccinations->getValue($where,'ChildId');

            $vaccine_list = [];

            $v_where = ['VaccinationId'=>$param['Id']];

            $field = 'VaccineId,Times,VaccinationDate,VaccinationPlace,LotNumber,Company,IsFree,VaccinationPosition,Flag';

            $vaccine_list = Db::name('vaccinationdetails')->where($v_where)->field($field)->select();

            $vaccine_json = json_encode($vaccine_list);
            // dump($dasdas);die;

            foreach ($vaccine_list as $k => $v) {
                $vaccine_list[$k]['CreationTime']=$time;
                $vaccine_list[$k]['IsDeleted']=0;
                $vaccine_list[$k]['ChildId']=$childId;
                $vaccine_list[$k]['CreatorUserId']=$param['VaccinationUserId'];
            }

            // Db::name('childvaccines')->insertAll($vaccine_list);

            $childInfo = $this->modelChilds->getInfo(['Id'=>$childId]);
            // dump($childInfo);die;

            $parent_info = [
                'parent_name'=>$childInfo['ParentName'],
                'phone'=>$childInfo['Mobile'],
                'address'=>$childInfo['Address']
            ];

            $position_id = $this->modelSettings->getValue(['Name'=>'App.InjectPositionId'], 'Value');

            $r_data = [
                'card_no' => $childInfo['CardNo'],
                'name' => $childInfo['Name'],
                'vaccine_list'=>$vaccine_json,
                'sex'=>$childInfo['Sex'],
                'birth_date' => $childInfo['BirthDate'],
                'parent_info' => json_encode($parent_info),
                'position_id'=>$position_id,
                'room_name'=>$data['ConsultationRoom'],
            ];

            $refrigeratorUrl = $this->modelSettings->getValue(['Name'=>'App.refrigeratorUrl'], 'Value');
            

            httpsPost($refrigeratorUrl . '/setInjectCompleteInfo', $r_data);

        }

        return $result ? [API_CODE_NAME => 200, API_MSG_NAME => '接种成功'] : [API_CODE_NAME => 40002, API_MSG_NAME => '接种失败'];
        
        

        


    }

    /**
     * 判断是不是在线预约的订单
     * 是 修改小程序数据库信息， 不是 不操作
     */
    public function editOrderInfo($id, $step = 3)
    {
        $orderId = $this->modelVaccinations->getValue(['Id'=>$id], 'appointment_order');

        if(!empty($orderId)){

            $data = [
                'order_id'=>$orderId,
                'step'=>$step,
            ];

            $appUrl = $this->modelSettings->getValue(['Name'=>'App.appUrl'], 'Value');

            $app_result = httpsPost($appUrl . '/editorderstep', $data);

            $app_result = json_decode($app_result, true);

            if($app_result['code'] == 200){
                return true;
            }else{
                return false;
            }

        }else{
            return true;
        } 
    }
     


}