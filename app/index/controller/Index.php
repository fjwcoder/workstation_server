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

use think\queue\Job;

use think\Db;
/**
 * 前端首页控制器
 * create by fjw in 19.9.28
 */
class Index extends IndexBase
{

    public function test1()
    {
        $time2wait = strtotime('2019-12-11 09:01:01') - strtotime('now');	
        dump($time2wait);die;
        
        // 1.当前任务将由哪个类来负责处理。
        // 当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName  = 'app\index\controller\Index@fire';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName     = "dengji";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        // ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        $jobData          = rand();
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        // $time2wait = strtotime('2018-09-08 11:15:00') - strtotime('now');  // 定时执行
        $list = Db::name('queue')->where(['queue'=>$jobQueueName])->count();
        // dump( $list);die;
        if($list === 0){

            $isPushed = \think\Queue::push($jobHandlerClassName, $jobData , $jobQueueName );
            
        }else{
            $times = $list * 3;
            $isPushed = \think\Queue::later($times, $jobHandlerClassName , $jobData , $jobQueueName );
        }
        
        dump($isPushed);
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if($isPushed !== false){
            echo "成功";
        }else{
            echo '失败';
        }
        

    }

    public function fire(Job $job,$data){

        // // 如有必要,可以根据业务需求和数据库中的最新数据,判断该任务是否仍有必要执行.
        // $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        // if(!$isJobStillNeedToBeDone){
        //     $job->delete();
        //     return;
        // }

        $isJobDone = $this->doHelloJob($data);
        // $job->delete();
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
        }else{
            if($job->attempts() > 3){
                //通过这个方法可以检查这个任务已经重试了几次了
                $job->delete();
                // 也可以重新发布这个任务
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }

    }

    /**
     * 有些消息在到达消费者时,可能已经不再需要执行了
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function checkDatabaseToSeeIfJobNeedToBeDone($data){
        return true;
    }

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function doHelloJob($data) {


        // 指明给谁推送，为空表示向所有在线用户推送
        $to_uid = 1001;

        // 推送的url地址，使用自己的服务器地址
        $push_api_url = "http://localhost:2121/";
        $post_data = array(
        "type" => "publish",
        "content" => $data,
        // "content" => "这个是推送的测试数据",
        "to" => $to_uid, 
        );
        $param = [
            ['url'=>$push_api_url, 'data'=>$post_data],
            // ['url'=>$push_api_url, 'data'=>$post_data]
            
        ];
        $ret = asyncCurl($param);
        if($ret[0] == 'ok'){
            return true;
        }else{
            return false;
        }
    }











    /**
     * socket 
     */

    public function test(){
        // 指明给谁推送，为空表示向所有在线用户推送
        $to_uid = 1001;

        // 推送的url地址，使用自己的服务器地址
        $push_api_url = "http://localhost:2121/";
        $post_data = array(
        "type" => "publish",
        "content" => rand(),
        // "content" => "这个是推送的测试数据",
        "to" => $to_uid, 
        );
        $param = [
            ['url'=>$push_api_url, 'data'=>$post_data],
            ['url'=>$push_api_url, 'data'=>$post_data]
            
        ];
        $ret = asyncCurl($param);
        dump($ret);
        
        
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



        $url = 'http://192.168.1.249:9500';

        $array12 = [
            'deviceId'=>2,
            'data'=>[
                ['number'=>'A001', 'childName'=>'登记台1', 'consultingRoom'=>'诊室1']
            ]
        ];
        $json12 = json_encode($array12, 320);
        $response12 = httpsPost($url, $json12);
        dump($response12);
        
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
