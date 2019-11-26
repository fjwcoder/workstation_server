<?php
/**
 * add by fqm in 19.10.23
 */

namespace app\api\logic;
use think\Db;

class Queue extends ApiBase
{



    /**
     * 取号时，添加数据
     */
    public function push($param = [])
    {
        $param = json_decode($param, true);

        if(empty($param['number'])){
            $result = [
                'result'=>[
                    'queueLength'=>'',
                    'qrCodeImageUrl'=>'',
                ],
                'targetUrl'=>Null,
                'success'=>False,
                'error'=>'取号失败',
                'unAuthorizedRequest'=>False,
                '__abp'=>True
            ];

            return json_encode($result, 320);
        }

        if(isset($param['oid']) || isset($param['Oid'])){

            $queueLength = $this->addAppointmentOrder($param);
            
        }else{
            
            $queueLength = $this->addSceneOrder($param);
        }

        $shortUrl = '';
        // 请求短链
        $positionId = $this->modelSettings->getValue(['Name'=>'App.InjectPositionId'],'Value');
        $url = $this->modelSettings->getValue(['Name'=>'App.shortUrl'],'Value');
        $re_data = [
            'uc'=>$positionId,
            'no'=>$param['number'],
        ];
        $requestData = formPost($url, $re_data);
        $requestData = json_decode($requestData, true);
        if($requestData['data']['errcode'] == 0){
            $shortUrl = $requestData['data']['short_url'];
        }

        if($queueLength === false){
            $result = [
                'result'=>[
                    'queueLength'=>'',
                    'qrCodeImageUrl'=>'',
                ],
                'targetUrl'=>Null,
                'success'=>False,
                'error'=>'取号失败',
                'unAuthorizedRequest'=>False,
                '__abp'=>True
            ];

        }else{
            $result = [
                'result'=>[
                    'queueLength'=>$queueLength,
                    'qrCodeImageUrl'=>$shortUrl,
                ],
                'targetUrl'=>Null,
                'success'=>True,
                'error'=>Null,
                'unAuthorizedRequest'=>False,
                '__abp'=>True
            ];

        }
        

        return json_encode($result, 320);


    }



    /**
     * 对预约订单取号进行操作
     */
    public function addAppointmentOrder($param = [])
    {

        $time = date("Y-m-d H:i:s");

        $data = [
            'IsDeleted'=>0,
            'Number'=>$param['number'],
            'CreationTime'=>$time,
            'VaccinationDate'=>$time,
            'State'=>0,
            'status'=>1,
            'appointment_order' => $param['oid'],
        ];


        $requestData = [
            'oid' => $param['oid'],
            'info' => 1,
        ];

        $appUrl = $this->modelSettings->getValue(['Name'=>'App.appUrl'], 'Value');

        $baby_order = httpsPost($appUrl . '/scaptmtqrcode', $requestData);

        $baby_order = json_decode($baby_order, true);

        if(empty($baby_order['data']) || $baby_order['data'] == null){
            return false;
        }

        $babyOrder = $baby_order['data'];

        $child_info = $this->modelChilds->getInfo(['CardNo'=>$babyOrder['card_no']]);
        // dump($child_info); die;
        // 有孩子信息
        if($child_info){
            $data['ChildId'] = $child_info['Id'];

            $child_new_data = [
                'Name'=>$babyOrder['baby_name'],
                // 'CardNo'=>$babyOrder['card_no'],
                'Sex'=>$babyOrder['baby_sex'],
                'BirthDate'=>$babyOrder['baby_birth_date'],
                'ParentName'=>$babyOrder['father_name'],
                'Address'=>$babyOrder['address_detail'],
                'Mobile'=>$babyOrder['mobile'],
            ];

            Db::name('childs')->where('Id',$data['ChildId'])->update($child_new_data);
            
        }else{
        // 没有孩子信息
            $childInfo = [
                'CreationTime'=>$time,
                'IsDeleted'=>0,
                'Name'=>$babyOrder['baby_name'],
                'CardNo'=>$babyOrder['card_no'],
                'Sex'=>$babyOrder['baby_sex'],
                'BirthDate'=>$babyOrder['baby_birth_date'],
                'ParentName'=>$babyOrder['father_name'],
                'Address'=>$babyOrder['address_detail'],
                'Mobile'=>$babyOrder['mobile'],
                'status'=>1,
                'No'=>''
            ];

            $data['ChildId'] = Db::name('childs')->insertGetId($childInfo);

        }
// dump($data); die;
        $VaccinationId = Db::name('vaccinations')->insertGetId($data);

        $where = [
            'IsDeleted' => 0,
            'VaccinationDate' => ['like','%'.NOW_DATE.'%'],
            'State' => 0,
            'Id' => ['<',$VaccinationId],
        ];

        $queueLength = $this->modelVaccinations->stat($where);

        return $queueLength;

    }

    /**
     * 对现场取号进行操作
     */
    public function addSceneOrder($param = [])
    {

        $time = date("Y-m-d H:i:s");

        $data = [
            'IsDeleted'=>0,
            'Number'=>$param['number'],
            'CreationTime'=>$time,
            'VaccinationDate'=>$time,
            'State'=>0,
        ];

        $VaccinationId = Db::name('vaccinations')->insertGetId($data);

        // 循环保存图片 插入数据
        foreach ($param['attachments'] as $k => $v) {
            // 修改保存文件名称，避免覆盖
            $file_name = $param['number'].'_'.$v['name'];
            $file_path = saveImg($v['content'], $file_name);

            $attachmentsData = [
                'CreationTime'=>$time,
                'IsDeleted'=>0,
                'VaccinationId'=>$VaccinationId,
                'Name'=>$v['name'],
                'DiplayName'=>$file_name,
                'Path'=>$file_path,
            ];

            Db::name('vaccinationattachments')->insert($attachmentsData);

        }

        $where = [
            'IsDeleted' => 0,
            'VaccinationDate' => ['like','%'.NOW_DATE.'%'],
            'State' => 0,
            'Id' => ['<',$VaccinationId],
        ];

        $queueLength = $this->modelVaccinations->stat($where);

        return $queueLength;

    }









}