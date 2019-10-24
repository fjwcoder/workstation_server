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
            'CreationTime'=>['like', '%'.NOW_DATE.'%'],
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
        $where['State'] = 3;
        $data['observerNumber'] = $this->modelVaccinations->stat($where,'count','Id');

        // 今日完成
        $where['State'] = 4;
        $data['finishedNumber'] = $this->modelVaccinations->stat($where,'count','Id');

        return $data;


    }





}