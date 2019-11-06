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

    public function test(){
        $data = '
        {
                "number":"0001",
                "oid": "V4B12AIB01690239084080"
        }';
        $url = 'http://workstation.server/api/queue/push';
        $response = httpsPost($url, $data);
        dump($response);
    }
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
        $inoculabilityTime = '2019-11-05 11:44:00'; //substr($v['VaccinationFinishTime'], -15, 8);
        $remainingTime = 30 - floor((time()-strtotime($inoculabilityTime))%86400/60);
        echo $remainingTime;
        // $url = 'https://vaccine.mamitianshi.com/fridgeheartbeat?accesstoken=ed333de121fd2ba6cb6a489fef5c521b';
        // $url .= '&time=123456&temp=987654';

        // for($i=0; $i<600; $i++){
        //     $response = httpsPost($url, '');
        //     $len = strlen($response);

        //     if($len > 100){
        //         file_put_contents('response.txt', $response);
        //         dump($response);
        //         break;
        //     }
        // }
        


    }

    public function formPost($url, $data, $headers = []){
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent:PostmanRuntime/7.18.0'
        ];
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
        // curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        // echo($result);
        return $result;
    }

    public function jiezhong(){



        // $url = 'http://192.168.1.249:9500';

        // $array12 = [
        //     'deviceId'=>12,
        //     'data'=>[
        //         ['number'=>'A001', 'childName'=>'登记台1', 'consultingRoom'=>'诊室1']
        //     ]
        // ];
        // $json12 = json_encode($array12, 320);
        // $response12 = httpsPost($url, $json12);
        // dump($response12);
        
    }
    

    public function liuguan(){
        $url = 'http://192.168.1.249:9500';

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
