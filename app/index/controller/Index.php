<?php
// +---------------------------------------------------------------------+
// | MamiTianshi    | [ CREATE BY WF_RT TEAM ]                           |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Fjwcoder <fjwcoder@gmail.com>                          |
// +---------------------------------------------------------------------+
// | Repository | git@github.com:fjwcoder/mamitianshi_server.git         |
// +---------------------------------------------------------------------+

namespace app\index\controller;

/**
 * 前端首页控制器
 * create by fjw in 19.9.28
 */
class Index extends IndexBase
{
    /**
     * 工作台首页
     * create by fjw in 19.9.28
     * edit by fqm in 19.9.30
     */
    public function index(){
        
        $this->assign('todayNumber',$this->logicIndex->index());

        return $this->fetch();

    }

    public function dengji(){
        $url = 'http://192.168.0.249:9500';

        $array11 = [
            'deviceId'=>11,
            'data'=>[
                ['number'=>'A001', 'writingDesk'=>'登记台1'],
                ['number'=>'A002', 'writingDesk'=>'登记台2'],
                ['number'=>'A003', 'writingDesk'=>'登记台1'],
                ['number'=>'A004', 'writingDesk'=>'登记台2'],
                ['number'=>'A005', 'writingDesk'=>'登记台1'],
                ['number'=>'A006', 'writingDesk'=>'登记台2'],
                ['number'=>'A007', 'writingDesk'=>'登记台1'],
                ['number'=>'A008', 'writingDesk'=>'登记台2'],
                ['number'=>'A009', 'writingDesk'=>'登记台1'],
                ['number'=>'A0010', 'writingDesk'=>'登记台2'],
                ['number'=>'A0011', 'writingDesk'=>'登记台1'],
                ['number'=>'A0012', 'writingDesk'=>'登记台2'],
            ]
        ];

        $json11 = json_encode($array11, 320);
        $response11 = httpsPost($url, $json11);
        dump($response11);
    }

    public function jiezhong(){
        $url = 'http://192.168.0.249:9500';

        $array12 = [
            'deviceId'=>12,
            'data'=>[
                ['number'=>'A001', 'childName'=>'登记台1', 'consultingRoom'=>'诊室1'],
                ['number'=>'A002', 'childName'=>'登记台2', 'consultingRoom'=>'诊室1'],
                ['number'=>'A003', 'childName'=>'登记台1', 'consultingRoom'=>'诊室1'],
                ['number'=>'A004', 'childName'=>'登记台2', 'consultingRoom'=>'诊室1'],
                ['number'=>'A005', 'childName'=>'登记台1', 'consultingRoom'=>'诊室1'],
                ['number'=>'A006', 'childName'=>'登记台2', 'consultingRoom'=>'诊室1'],

            ]
        ];
        $json12 = json_encode($array12, 320);
        $response12 = httpsPost($url, $json12);
        dump($response12);
        
    }
    

    public function liuguan(){
        $url = 'http://192.168.0.249:9500';

        $array13 = [
            'deviceId'=>13,
            'data'=>[
                ['number'=>'A001', 'childName'=>'留观1', 'inoculabilityTime'=>'09:39:21', 'remainingTime'=>1],
                ['number'=>'A002', 'childName'=>'留观2', 'inoculabilityTime'=>'09:39:22', 'remainingTime'=>2],
                ['number'=>'A003', 'childName'=>'留观3', 'inoculabilityTime'=>'09:39:23', 'remainingTime'=>3],
                ['number'=>'A004', 'childName'=>'留观4', 'inoculabilityTime'=>'09:39:24', 'remainingTime'=>4],

            ]
        ];
        $json13 = json_encode($array13, 320);
        $response13 = httpsPost($url, $json13);

        dump($response13);
    }


}
