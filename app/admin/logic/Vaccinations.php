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
    public function getWaitingList($where = [], $field = true, $order = '', $paginate = 1, $limit = 15)
    {

        $where['VaccinationDate'] = ['like', '%'.NOW_DATE.'%'];
        $where['State'] = 0;

        $where['Number'] = ['like', '%V%'];
        // $Vlist = $this->modelVaccinations->getList($where, $field, $order, $paginate);
        $Vlist = Db::name('vaccinations')->where($where)->field($field)->order($order)->limit($limit)->page($paginate)->select();
        // dump($Vlist);

        $where['Number'] = ['like', '%A%'];
        // $Alist = $this->modelVaccinations->getList($where, $field, $order, $paginate);
        $Alist = Db::name('vaccinations')->where($where)->field($field)->order($order)->limit($limit)->page($paginate)->select();
        // dump($Alist);

        $list = array_merge($Vlist,$Alist);

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

        // $registerInfo['pic'] = $this->modelVaccinationattachments->getList(, );

        $head_fingerprint_pic = Db::name('vaccinationattachments')->where(['VaccinationId'=>$param['Id']])->field('Name,DiplayName,Path')->select();

        $head_fingerprint_path = [];

        if($head_fingerprint_pic !== null){
            foreach ($head_fingerprint_pic as $k => $v) {
                $head_fingerprint_path[$v['Name']] = $v['Path'];
            }
        }

        $registerInfoDefilds = $this->logicVaccinationdetails->getVIdObVdetail(['VaccinationId' => $registerInfo['Id']]);
        
        // 所有疫苗列表
        $vaccineList = $this->modelVaccines->getList(['IsDeleted'=>0],'Id,ShortName,IsFree','Id asc',false);
        // 登记台数量
        $writingdeskcount = $this->modelSettings->getValue(['Name'=>'App.WritingDeskCount'],'Value');
        // 当前登记台缓存
        $WritingDesk = cache('WritingDesk');
        // 进行登记叫号
        $this->callNumber(['number'=>$registerInfo['Number'],'WritingDesk'=>$WritingDesk]);

        $data = [
            'WritingDesk'=>$WritingDesk,
            'vaccines' => $vaccineList,
            'registerInfo' => $registerInfo,
            'registerInfoDefilds'=>$registerInfoDefilds,
            'writingdeskcount' => $writingdeskcount,
            'head_fingerprint_path' => $head_fingerprint_path,
        ];

        return $data;

    }

    
    /**
     * 登记资料时，修改登记的信息
     */
    public function setVaccinationsInfo($param = [])
    {

        $result = false;

        $time = date("Y-m-d H:i:s");

        if(empty($param['ChildInfo'])){
            return ['code'=>400,'msg'=>'请先填写儿童信息'];
        }
        if(empty($param['WritingDesk'])){
            return ['code'=>400,'msg'=>'请先选择登记台'];
        }

        if(empty($param['vaccineDate'])){
            return ['code'=>400,'msg'=>'请添加要接种的疫苗'];
        }

        // 启动事务
        Db::startTrans();
        try{
            // 添加/修改孩子信息
            if(!empty($param['ChildInfo']['Id'])){
                $param['ChildInfo']['LastModificationTime'] = $time;
                $param['ChildInfo']['LastModifierUserId'] = MEMBER_ID;
                Db::name('childs')->where('Id',$param['ChildInfo']['Id'])->update($param['ChildInfo']);
                $ChildId = $param['ChildInfo']['Id'];
            }else{
                $param['ChildInfo']['CreationTime'] = $time;
                $param['ChildInfo']['CreatorUserId'] = MEMBER_ID;
                $param['ChildInfo']['IsDeleted'] = 0;
                $param['ChildInfo']['No'] = '';
                $ChildId = Db::name('childs')->where('Id',$param['ChildInfo']['Id'])->insertGetId($param['ChildInfo']);
                
            }

            $v_data = [
                'Id' => $param['Id'],
                'ChildId' => $ChildId,
                'WritingDesk' => $param['WritingDesk'],
                'LastModificationTime' => $time,
                'LastModifierUserId' => MEMBER_ID,
                'RegistrantId' => MEMBER_ID,
                'RegistrationFinishTime' => $time,
                'RegistrantId' => MEMBER_ID,
                'RegistrantId' => MEMBER_ID,
                'State'=>1
            ];

            $app_result = $this->editOrderInfo($param['Id'], 2);
            if(!$app_result){
                Db::rollback();
            }

            Db::name('vaccinations')->where('Id',$param['Id'])->update($v_data);

            $where_v = [
                'VaccinationId' => $param['Id'],
                'VaccinationDate' => ['like','%'.NOW_DATE.'%']
            ];
    
            foreach ($param['vaccineDate'] as $k => $v) {
                $data_v = [
                    'CreationTime'=>$time,
                    'CreatorUserId'=>MEMBER_ID,
                    'LastModificationTime'=>$time,
                    'LastModifierUserId'=>MEMBER_ID,
                    'VaccinationDate'=>$time,
                    'IsDeleted'=>0,
                    'VaccinationId'=>$param['Id'],
                ];
    
                // 验证疫苗信息是否添加
                $where_v['VaccineId'] = empty($v['VaccineId']) ? 0: $v['VaccineId'];
    
                if(empty($v['VaccineId'])){
                    return ['code'=>400,'msg'=>'请选择需要接种的疫苗'];
                }
        
                $vaccineInfo = $this->modelVaccinationdetails->getInfo($where_v);

                if($vaccineInfo && empty($v['Id'])){
                    Db::rollback();
                    return ['code'=>400,'msg'=>'请勿重复登记'];
                }
    
                $data_v['Id'] = !empty($v['Id']) ? $v['Id'] : 0;
                $data_v['VaccineId'] = $v['VaccineId'];
                $data_v['Times'] = !empty($v['Times']) ? $v['Times'] : 1;
                $data_v['LotNumber'] = !empty($v['LotNumber']) ? $v['LotNumber'] :'';
                $data_v['Company'] = !empty($v['Company'])? $v['Company']:'';
                $data_v['VaccinationPosition'] = !empty($v['VaccinationPosition']) ?$v['VaccinationPosition']:'';
                $data_v['IsFree'] = $v['IsFree'];
                // $data['VaccinationPlace'] = $v['VaccinationPlace'];
                if(empty($data_v['Id'])){
                    Db::name('vaccinationdetails')->insert($data_v);
                }else{
                    Db::name('vaccinationdetails')->update($data_v);
                }
            }

            $nextId = '';
            if($param['noNext'] == 2){
                $nextId = $this->nextNumber(['Id'=>$param['Id'],'State'=>0]);
            }

            // 提交事务
            Db::commit();
            $result = true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        return $result ? ['code'=>200,'msg'=>'操作成功','data'=>$nextId] : ['code'=>400,'msg'=>'操作失败','data'=>''];



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

        $app_result = $this->editOrderInfo($param['Id'], 2);

        if($app_result){
            $result = $this->modelVaccinations->setInfo($data);
        }

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

        $head_fingerprint_pic = Db::name('vaccinationattachments')->where(['VaccinationId'=>$param['Id']])->field('Name,DiplayName,Path')->select();

        $head_fingerprint_path = [];

        if($head_fingerprint_pic !== null){
            foreach ($head_fingerprint_pic as $k => $v) {
                $head_fingerprint_path[$v['Name']] = $v['Path'];
            }
        }

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

        // 当前接种室缓存
        $VaccinationDesk = cache('VaccinationDesk');

        // 进行接种叫号
        $this->callInjectNumber(['Number'=>$registerInfo['Number'],'Name'=>$childInfo['Name'],'WritingDesk'=>$VaccinationDesk]);


        $data = [
            'VaccinationDesk'=>$VaccinationDesk,
            'childInfo' => $childInfo,
            'todayInjectList' => $todayInjectList,
            'registerInfo' => $registerInfo,
            'vaccinationDeskCount' => $vaccinationDeskCount,
            'head_fingerprint_path' => $head_fingerprint_path,
        ];

        return $data;


    }


    /**
     * 留观队列
     */
    public function observationList($where = [])
    {
        $where['v.RegistrationFinishTime'] = ['like', '%'.NOW_DATE.'%'];
        $where['v.State'] = ['>=',2];
        
        $d_time = date('Y-m-d H:i:s',time() - 1800);
        $where['v.VaccinationFinishTime'] = ['>=',$d_time];

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
        // dump($where);die;

        $this->modelVaccinations->alias('v');

        $this->modelVaccinations->join = [
            [SYS_DB_PREFIX . 'childs c', 'v.ChildId = c.Id','LEFT'],
        ];

        $field = 'v.Id, v.Number, v.ChildId, v.ConsultationRoom, v.RegistrationFinishTime, v.WritingDesk, c.Name as child_name, c.CardNo, c.BirthDate, c.ParentName, c.Mobile, c.Sex, c.Address';

        // 当前点击的号码的信息
        $registerInfo = $this->modelVaccinations->getInfo($where, $field);

        $head_fingerprint_pic = Db::name('vaccinationattachments')->where(['VaccinationId'=>$where['v.Id']])->field('Name,DiplayName,Path')->select();

        $head_fingerprint_path = [];

        if($head_fingerprint_pic !== null){
            foreach ($head_fingerprint_pic as $k => $v) {
                $head_fingerprint_path[$v['Name']] = $v['Path'];
            }
        }

        $registerInfoDefilds = $this->logicVaccinationdetails->getVIdObVdetail(['VaccinationId' => $registerInfo['Id']]);
        
        // 当前点击的号码的上一个号码，下一个号码
        $peevNext = $this->prevNextNum($registerInfo['Id'], $where_np);

        $data = [
            'next' => json_encode($peevNext['next']),
            'prev' => json_encode($peevNext['prev']),
            'registerInfo' => $registerInfo,
            'registerInfoDefilds'=>$registerInfoDefilds,
            'head_fingerprint_path' => $head_fingerprint_path,
        ];

        return $data;

    }


    /**
     * 设置接种疫苗完成
     */
    public function setInjectVaccineComplete($param = [])
    {

        $result = false;

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

        $app_result = $this->editOrderInfo($param['Id'], 3);

        if($app_result){
            $result = $this->modelVaccinations->setInfo($data);
            $nextId = '';
            if($param['noNext'] == 2){
                $nextId = $this->nextNumber(['Id'=>$param['Id'],'State'=>1]);
            }
        }

        return $result ? ['code'=>200,'msg'=>'接种成功','data'=>$nextId] : ['code'=>400,'msg'=>'操作失败','data'=>''];


    }


    /**
     * 上一个/下一个
     */
    public function prevNextNum($id, $where)
    {

        $field = 'Id, Number, ChildId, VaccinationDate';

        $where_n = $where_p = $where;
        
        // $n_data = Db::name('vaccinations')->where(['Id'=>$id])->find();

        $Number = $this->modelVaccinations->getValue(['Id'=>$id], 'Number');
        
        if(strtoupper($Number[0]) == 'V'){
            $where_n['Number'] = ['like','%V%'];
            $where_p['Number'] = ['like','%V%'];
        }else{
            $where_n['Number'] = ['like','%A%'];
            $where_p['Number'] = ['like','%A%'];
        }

        $where_n['Id'] = ['>', $id];
        $next = Db::name('vaccinations')->where($where_n)->field($field)->order('VaccinationDate asc')->find();
        if($next == null){
            unset($where_n['Number']);
            $next = Db::name('vaccinations')->where($where_n)->field($field)->order('VaccinationDate asc')->find();
        }

        $where_p['Id'] = ['<', $id];
        $prev = Db::name('vaccinations')->where($where_p)->field($field)->order('VaccinationDate desc')->find();
        if($next == null){
            unset($where_p['Number']);
            $prev = Db::name('vaccinations')->where($where_p)->field($field)->order('VaccinationDate asc')->find();
        }

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

        if($result === false){
            return ['code'=>400,'msg'=>'连接超时'];
        }

        $result = json_decode($result,true);

        if($result['sucess'] == true){
            return ['code'=>200,'msg'=>'叫号成功'];
        }else{
            return ['code'=>400,'msg'=>'叫号失败'];
        }

    }

    /**
     * 接种台叫号
     */
    public function callInjectNumber($param = [])
    {
        
        $data = [
            'deviceId'=>2,
            'data' =>[
                'number'=>$param['Number'],
                'childName'=>$param['Name'],
                'consultingRoom'=>$param['WritingDesk']
            ]
        ];

        $url = $this->modelSettings->getValue(['Name'=>'App.QueueServerAddress'],'Value');

        $result = httpsPost($url, json_encode($data));

        if($result === false){
            return ['code'=>400,'msg'=>'连接超时'];
        }

        $result = json_decode($result,true);

        if($result['sucess'] == true){
            return ['code'=>200,'msg'=>'叫号成功'];
        }else{
            return ['code'=>400,'msg'=>'叫号失败'];
        }

    }


    /**
     * 登记资料 / 接种完成 时  判断是不是在线预约的订单
     * 是 修改小程序数据库信息， 不是 不操作
     */
    public function editOrderInfo($id,$step)
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


    /**
     * 上一位
     */
    public function prevNumber($param = [])
    {
        if(empty($param['Id'])) return ['code'=>400,'msg'=>'参数错误'];

        $vInfo = $this->modelVaccinations->getInfo(['Id'=>$param['Id']],'Id,Number,State');
        $where = [
            'VaccinationDate' => ['like', '%'.NOW_DATE.'%'],
            'State'=>$param['State'],
        ];
        
        if(strtoupper($vInfo['Number'][0]) == 'V'){
            $where['Number'] = ['like','%V%'];
        }else{
            $where['Number'] = ['like','%A%'];
        }

        $field = 'Id';

        $where['Id'] = ['<', $param['Id']];
        $prev = Db::name('vaccinations')->where($where)->order('VaccinationDate desc')->value('Id');
        if($prev == null){
            unset($where['Number']);
            $prev = Db::name('vaccinations')->where($where)->order('VaccinationDate asc')->value('Id');
        }

        return $prev ? ['code'=>200,'msg'=>$prev] : ['code'=>400,'msg'=>'已经到第一位了!!!'];



    }

    /**
     * 下一位
     */
    public function nextNumber($param = [])
    {
        if(empty($param['Id'])) return ['code'=>400,'msg'=>'参数错误'];

        $vInfo = $this->modelVaccinations->getInfo(['Id'=>$param['Id']],'Id,Number,State');
        $where = [
            'VaccinationDate' => ['like', '%'.NOW_DATE.'%'],
            'State'=>$param['State'],
        ];

        if(strtoupper($vInfo['Number'][0]) == 'V'){
            $where['Number'] = ['like','%V%'];
        }else{
            $where['Number'] = ['like','%A%'];
        }

        $field = 'Id';

        $where['Id'] = ['>', $param['Id']];
        $next = Db::name('vaccinations')->where($where)->order('VaccinationDate asc')->value('Id');
        if($next == null){
            unset($where['Number']);
            $next = Db::name('vaccinations')->where($where)->order('VaccinationDate asc')->value('Id');
        }

        return $next ? ['code'=>200,'msg'=>$next] : ['code'=>400,'msg'=>'已经到最后一位了!!!'];
    }



    
    


}