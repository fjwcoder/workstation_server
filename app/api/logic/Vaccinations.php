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

        if(empty($param['Id'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '操作失败'];
        
        if(empty($param['VaccinationUserId'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '操作失败'];
        // status 1完成按钮 2完成并呼叫下一个按钮
        if(empty($param['status'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '操作失败'];

        // if(empty($param['ConsultationRoom'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '操作失败'];

        $time = date('Y-m-d H:i:s');

        $where = [
            'Id'=>$param['Id'],
        ];

        // 启动事务
        Db::startTrans();
        try{
            
            // 判断是不是预约订单
            $app_result = $this->editOrderInfo($param['Id'], 3);

            if($app_result === false){
                return [API_CODE_NAME => 40002, API_MSG_NAME => '接种失败'];
            }

            // 接种流水数据 完成时间 接种用户Id 接种台名称
            $data = [
                'VaccinationFinishTime'=>$time,
                'VaccinationUserId'=>$param['VaccinationUserId'],
                'ConsultationRoom'=>!empty($param['ConsultationRoom'])? $param['ConsultationRoom']:'',
                'State'=>4,
            ];
            // 对接种流水表继续修改数据
            Db::name('vaccinations')->where($where)->update($data);

            // 当前接种流水信息
            $vInfo = Db::name('vaccinations')->where($where)->field('Id,Number,ChildId,State')->find();
            // 接种疫苗列表
            $vaccine_list = [];
            $v_where = ['VaccinationId'=>$param['Id']];
            $field = 'VaccineId,Times,VaccinationDate,VaccinationPlace,LotNumber,Company,IsFree,VaccinationPosition,Flag';
            $vaccine_list = Db::name('vaccinationdetails')->where($v_where)->field($field)->select();
            $vaccine_json = json_encode($vaccine_list);
            foreach ($vaccine_list as $k => $v) {
                $vaccine_list[$k]['CreationTime']=$time;
                $vaccine_list[$k]['IsDeleted']=0;
                $vaccine_list[$k]['ChildId']=$vInfo['ChildId'];
                $vaccine_list[$k]['CreatorUserId']=$param['VaccinationUserId'];
            }
            // 把今天接种的疫苗插入到历史接种表中
            Db::name('childvaccines')->insertAll($vaccine_list);

            // 孩子信息
            $childInfo = Db::name('childs')->where(['Id'=>$vInfo['ChildId']])->find();
            // 家长信息
            $parent_info = [
                'parent_name'=>$childInfo['ParentName'],
                'phone'=>$childInfo['Mobile'],
                'address'=>$childInfo['Address']
            ];
            // 接种点Id
            $position_id = Db::name('settings')->where(['Name'=>'App.InjectPositionId'])->value('Value');
            // 提交数据
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
            // 把当前接种信息 添加到线上数据库中
            $refrigeratorUrl = Db::name('settings')->where(['Name'=>'App.refrigeratorUrl'])->value('Value');
            $refridgeData = httpsPost($refrigeratorUrl . '/setInjectCompleteInfo', $r_data);
            $refridgeData = json_decode($refridgeData, true);
            // 提交成功
            if($refridgeData['code'] == 200){
                // 如果是完成并下一个按钮
                if($param['status'] == 2){
                    // 下一个的查询条件 
                    $n_where = [
                        'a.VaccinationDate' => ['like', '%'.NOW_DATE.'%'],
                        'a.State'=>1,
                    ];

                    if(strtoupper($vInfo['Number'][0]) == 'V'){
                        $n_where['a.Number'] = ['like','%V%'];
                    }else{
                        $n_where['a.Number'] = ['like','%A%'];
                    }

                    $n_field = 'a.Id,a.Number,c.Sex,C.Name';

                    $n_where['a.Id'] = ['>', $param['Id']];
                    $nextData = [];
                    // 查询有没有和当前取号号码开头一样的数据
                    $nextData = Db::name('vaccinations')->alias('a')->join('childs c','a.ChildId = c.Id','LEFT')->where($n_where)->order('VaccinationDate asc')->field($n_field)->find();
                    // 没有和当前取号号码一样的数据，不查询 Number 条件
                    if($nextData == null){
                        unset($where['a.Number']);
                        $nextData = Db::name('vaccinations')->alias('a')->join('childs c','a.ChildId = c.Id','LEFT')->where($n_where)->order('a.VaccinationDate asc')->field($n_field)->find();
                    }
                    // 如果有下一个
                    if($nextData !== null){
                        $nextData['vaccineList'] = Db::name('vaccinationdetails')->alias('d')->join('vaccines v','d.VaccineId = v.Id')->where(['d.VaccinationId'=>$nextData['Id']])->field('v.ShortName')->select();
                        // 进行叫号
                        $this->logicCommon->callName(['deviceId'=>2,'number'=>$nextData['Number'],'childName'=>$nextData['Name'],'consultingRoom'=>$param['ConsultationRoom']]);
                    }
                    
                }
                // 提交事务
                Db::commit();
                
                $result = true;
            }else{
                // 回滚事务
                Db::rollback();
            }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        return $result ? [API_CODE_NAME => 200, API_MSG_NAME => '接种成功','data'=>$nextData] : [API_CODE_NAME => 40002, API_MSG_NAME => '接种失败'];


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