<?php
/***
 * index 逻辑层
 * by fqm in 19.9.30
 */

namespace app\admin\logic;

class Index extends AdminBase
{




    /**
     * index方法 获取接种数量等
     */
    public function index()
    {

        $where = [
            'VaccinationDate'=>['like', '%'.NOW_DATE.'%'],
        ];

        // 取号
        $data['Number'] = $this->modelVaccinations->stat($where,'count','Id');
        
        // 登记队列
        $where['State'] = 0;
        $data['writingNumber'] = $this->modelVaccinations->stat($where,'count','Id');

        // 接种队列
        $where['State'] = 1;
        $data['inoculationNumber'] = $this->modelVaccinations->stat($where,'count','Id');

        // 今日接种
        $where['State'] = ['>=',2];
        $data['completeNumber'] = $this->modelVaccinations->stat($where,'count','Id');

        // 留观队列
        // $where['State'] = 3;
        $d_time = date('Y-m-d H:i:s',time() - 1800);
        $where['VaccinationFinishTime'] = ['>=',$d_time];
        $data['observerNumber'] = $this->modelVaccinations->stat($where,'count','Id');

        // 今日完成
        // $where['State'] = 4;
        unset($where['VaccinationFinishTime']);
        $data['finishedNumber'] = $this->modelVaccinations->stat($where,'count','Id');

        return $data;


    }





}