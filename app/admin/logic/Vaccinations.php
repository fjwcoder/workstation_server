<?php
/**
 * Vaccinations 接种流水逻辑层 
 * by fqm in 19.9.30
 */

namespace app\admin\logic;
use think\Db;

class Vaccinations extends AdminBase
{

    /**
     * 获取登记队列
     */
    public function getWaitingList($where = [], $field = true, $order = '', $paginate = 15)
    {

        $where['VaccinationDate'] = ['like', '%'.NOW_DATE.'%'];
        $where['State'] = 0;

        $list = $this->modelVaccinations->getList($where, $field, $order, $paginate);

        return $list;
    }


    /**
     * 获取登记队列某一条的信息进行登记
     */
    public function getWaitingInfo($param = [])
    {

        $this->modelVaccinations->alias('v');

        $this->modelVaccinations->join = [
            [SYS_DB_PREFIX . 'childs c', 'v.ChildId = c.Id','LEFT'],
        ];

        $field = 'v.Id, v.Number, v.ChildId, v.ConsultationRoom, v.RegistrationFinishTime, c.Name as child_name, c.CardNo, c.BirthDate, c.ParentName, c.Mobile, c.Sex, c.Address';

        // 当前点击的号码的信息
        $registerInfo = $this->modelVaccinations->getInfo(['v.Id'=>$param['Id']],$field);

        $registerInfoDefilds = $this->logicVaccinationdetails->getVIdObVdetail(['VaccinationId' => $registerInfo['Id']]);
        
        // 当前点击的号码的上一个号码，下一个号码
        $peevNext = $this->prevNextNum($registerInfo['Id'], ['VaccinationDate' => ['like', '%'.NOW_DATE.'%'],'State'=>['in','0']]);
        // 所有疫苗列表
        $vaccineList = $this->modelVaccines->getList(['IsDeleted'=>0],'Id,ShortName,IsFree','Id asc',false);
        // 登记台数量
        $writingdeskcount = $this->modelSettings->getValue(['Name'=>'App.WritingDeskCount'],'Value');

        $data = [
            'next' => json_encode($peevNext['next']),
            'prev' => json_encode($peevNext['prev']),
            'vaccines' => $vaccineList,
            'registerInfo' => $registerInfo,
            'registerInfoDefilds'=>$registerInfoDefilds,
            'writingdeskcount' => $writingdeskcount,
        ];

        return $data;

    }

    
    /**
     * 登记资料时，修改登记的信息
     */
    public function setVaccinationsInfo($param = [])
    {

        if(empty($param['ChildId'])){
            return ['code'=>400,'msg'=>'请先填写孩子信息'];
        }
        if(empty($param['WritingDesk'])){
            return ['code'=>400,'msg'=>'请先选择登记台'];
        }
        
        $time = date("Y-m-d h:i:s");

        $data = [
            'Id' => $param['Id'],
            'ChildId' => $param['ChildId'],
            'WritingDesk' => $param['WritingDesk'],
            'LastModificationTime' => $time,
            'LastModifierUserId' => MEMBER_ID,
            'RegistrantId' => MEMBER_ID,
            'RegistrationFinishTime' => $time,
            'RegistrantId' => MEMBER_ID,
            'RegistrantId' => MEMBER_ID,
            'State'=>1
        ];
        
        $result = $this->modelVaccinations->setInfo($data);

        return $result ? ['code'=>200,'msg'=>'操作成功'] : ['code'=>400,'msg'=>'操作失败'];
    }

    
    /**
     * 获取接种队列
     */
    public function getInoculationList($where = [], $field = true, $order = '', $paginate = 15)
    {

        $where['v.RegistrationFinishTime'] = ['like', '%'.NOW_DATE.'%'];
        $where['v.State'] = 1;

        $this->modelVaccinations->alias('v');

        $this->modelVaccinations->join = [
            [SYS_DB_PREFIX . 'childs c', 'v.ChildId = c.Id','LEFT'],
        ];

        return $this->modelVaccinations->getList($where, $field, $order, $paginate);

    }


    /**
     * 获取接种队列中某一条的信息
     */
    public function getInoculationInfo($param = [])
    {

        if(empty($param['Id'])){
            return [RESULT_ERROR,'请选择需要接种的号码'];
        }

        $field = 'Id, Number, ChildId, VaccinationDate';
        // 当前点击的号码的信息
        $registerInfo = $this->modelVaccinations->getInfo(['Id'=>$param['Id']],$field);

        // 当前点击的号码的上一个号码，下一个号码
        $peevNext = $this->prevNextNum($registerInfo['Id'], ['VaccinationDate' => ['like', '%'.NOW_DATE.'%'],'State'=>['in','1']]);

        // 当前号码的孩子信息
        $childInfo = $this->modelChilds->getInfo(['Id'=>$registerInfo['ChildId']]);

        // 当前号码当天需要接种的疫苗列表
        $where = [
            'a.VaccinationId'=>$registerInfo['Id'],
            'a.VaccinationDate'=>['like', '%'.NOW_DATE.'%'],
            'a.IsDeleted'=>0
        ];

        $v_field = 'a.VaccinationId,a.VaccineId,a.Times,a.VaccinationDate,a.VaccinationPlace,a.LotNumber,a.Company,a.IsFree,a.VaccinationPosition,a.Flag,v.ShortName';

        $this->modelVaccinationdetails->alias('a');

        $this->modelVaccinationdetails->join = [
            [SYS_DB_PREFIX . 'vaccines v', 'a.VaccineId = v.Id','LEFT'],
        ];

        $todayInjectList = $this->modelVaccinationdetails->getList($where,$v_field,'',false);

        // 诊室数量
        $vaccinationDeskCount = $this->modelSettings->getValue(['Name'=>'App.VaccinationDeskCount'],'Value');

        $data = [
            'next' => json_encode($peevNext['next']),
            'prev' => json_encode($peevNext['prev']),
            'childInfo' => $childInfo,
            'todayInjectList' => $todayInjectList,
            'registerInfo' => $registerInfo,
            'vaccinationDeskCount' => $vaccinationDeskCount,
        ];

        return $data;


    }


    /**
     * 留观队列
     */
    public function observationList($where = [])
    {
        $where['v.RegistrationFinishTime'] = ['like', '%'.NOW_DATE.'%'];
        $where['v.State'] = 3;

        $this->modelVaccinations->alias('v');

        $this->modelVaccinations->join = [
            [SYS_DB_PREFIX . 'childs c', 'v.ChildId = c.Id','LEFT'],
        ];

        $field = 'v.Id, v.Number, v.ChildId, v.ConsultationRoom, v.VaccinationFinishTime, c.Name as child_name';

        return $this->modelVaccinations->getList($where, $field, 'v.VaccinationFinishTime asc', 15);

    }


    /**
     * 获取完成队列
     */
    public function getFinishedList($where = [], $field = true, $order = 'v.VaccinationFinishTime asc', $paginate = 15)
    {

        $this->modelVaccinations->alias('v');

        $this->modelVaccinations->join = [
            [SYS_DB_PREFIX . 'childs c', 'v.ChildId = c.Id','LEFT'],
        ];

        return $this->modelVaccinations->getList($where, $field, $order, $paginate);

    } 


    /**
     * 查看接种完成的孩子资料
     */
    public function checkInVaInfo($where, $where_np)
    {

        $this->modelVaccinations->alias('v');

        $this->modelVaccinations->join = [
            [SYS_DB_PREFIX . 'childs c', 'v.ChildId = c.Id','LEFT'],
        ];

        $field = 'v.Id, v.Number, v.ChildId, v.ConsultationRoom, v.RegistrationFinishTime, v.WritingDesk, c.Name as child_name, c.CardNo, c.BirthDate, c.ParentName, c.Mobile, c.Sex, c.Address';

        // 当前点击的号码的信息
        $registerInfo = $this->modelVaccinations->getInfo($where, $field);

        $registerInfoDefilds = $this->logicVaccinationdetails->getVIdObVdetail(['VaccinationId' => $registerInfo['Id']]);
        
        // 当前点击的号码的上一个号码，下一个号码
        $peevNext = $this->prevNextNum($registerInfo['Id'], $where_np);

        $data = [
            'next' => json_encode($peevNext['next']),
            'prev' => json_encode($peevNext['prev']),
            'registerInfo' => $registerInfo,
            'registerInfoDefilds'=>$registerInfoDefilds,
        ];

        return $data;

    }


    /**
     * 设置接种疫苗完成
     */
    public function setInjectVaccineComplete($param = [])
    {

        $time = date("Y-m-d H:i:s");

        $data = [
            'Id'=>$param['Id'],
            'ConsultationRoom'=>$param['ConsultationRoom'],
            'VaccinationFinishTime'=>$time,
            'VaccinationUserId'=>MEMBER_ID,
            'LastModificationTime'=>$time,
            'LastModifierUserId'=>MEMBER_ID,
            'State'=>$param['State'],
        ];

        $result = $this->modelVaccinations->setInfo($data);

        return $result ? ['code'=>200,'msg'=>'接种成功'] : ['code'=>400,'msg'=>'操作失败'];


    }


    /**
     * 上一个/下一个
     */
    public function prevNextNum($id, $where)
    {

        $field = 'Id, Number, ChildId, VaccinationDate';

        $where_n = $where_p = $where;
        
        $where_n['Id'] = ['>', $id];

        $next = Db::name('Vaccinations')->where($where_n)->field($field)->order('VaccinationDate asc')->find();

        $where_p['Id'] = ['<', $id];

        $prev = Db::name('Vaccinations')->where($where_p)->field($field)->order('VaccinationDate desc')->find();

        $data = [
            'next'=>$next['Id'],
            'prev'=>$prev['Id'],
        ];
        

        return $data;

    }


    /**
     * 登记台叫号
     */
    public function callNumber($param = [])
    {
        if(empty($param['number'])) return ['code'=>400,'msg'=>'请选择叫号号码'];
        if(empty($param['WritingDesk'])) return ['code'=>400,'msg'=>'请选择登记台'];

        $url = $this->modelSettings->getValue(['Name'=>'App.QueueServerAddress'],'Value');

        $data = [
            'deviceId'=>1,
            'data'=>[
                'number'=>$param['number'],
                'writingDesk'=>$param['WritingDesk']
            ]

        ];

        $result = httpsPost($url, json_encode($data));

        $result = json_decode($result,true);

        if($result['sucess'] == true){
            return ['code'=>200,'msg'=>'叫号成功'];
        }else{
            return ['code'=>400,'msg'=>'叫号失败'];
        }



        
    }

    


}