<?php
/**
 * add by fqm in 19.10.23
 */

namespace app\api\logic;
use think\Db;

class Queue extends ApiBase
{



    /**
     * 
     */
    public function push($param = [])
    {

        header('Content-Type:application/json');

        $param = json_decode($param,true);

        $time = date("Y-m-d h:i:s");

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









}