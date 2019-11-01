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

        if(empty($param['Oid'])){

            $queueLength = $this->addSceneOrder($param);
        }else{
            
            $queueLength = $this->addAppointmentOrder($param);
        }

        // $url = 'http://xiaoai.fjwcoder.com/api.php/Injectqueue/urlToShort';

        $result = [
            'result'=>[
                'queueLength'=>$queueLength,
                'qrCodeImageUrl'=>'',
            ],
            'targetUrl'=>Null,
            'success'=>True,
            'error'=>Null,
            'unAuthorizedRequest'=>False,
            '__abp'=>True
        ];

        return json_encode($result);


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
        ];

        $requestData = [
            'oid' => $param['Oid'],
            'info' => 1,
        ];

        $refrigeratorUrl = $this->modelSettings->getValue(['Name'=>'App.refrigeratorUrl'], 'Value');

        $baby_order = httpsPost($refrigeratorUrl . '/scaptmtqrcode', $requestData);

        $baby_order = json_decode($baby_order, true);

        $babyOrder = $baby_order['data'];

        $child_info = $this->modelChilds->getInfo(['CardNo'=>$babyOrder['card_no']]);

        // 有孩子信息
        if($child_info){
            $data['ChildId'] = $child_info['Id'];
            
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
            ];

            $data['ChildId'] = Db::name('childs')->insertGetId($childInfo);

        }

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

            $file_name = $param['number'].'-'.$v['name'];

            $file_path = saveImg($v['content'], $file_name);

            $attachmentsData = [
                'CreationTime'=>$time,
                'IsDeleted'=>0,
                'VaccinationId'=>$VaccinationId,
                'Name'=>$file_name,
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