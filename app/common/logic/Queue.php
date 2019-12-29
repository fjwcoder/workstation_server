<?php
/**
 * 队列
 */
namespace app\common\logic;

use think\queue\Job;
use think\Db;

class Queue {


    // 添加到队列
    public function addQueue($queueClassName, $queueName, $queueData)
    {
        if($queueData['to_uid'] == 1001){
            $queue_date['to_uid'] = 1001;
            $queue_date['data'] = '请'.$queueData['name'].'到登记台'.$queueData['deskRoom'];
            $lastExecuteTime = cache('lastRegistET');
        }elseif($queueData['to_uid'] == 1002){
            $queue_date['to_uid'] = 1002;
            $queue_date['data'] = '请'.$queueData['name'].'到接种室'.$queueData['deskRoom'];
            $lastExecuteTime = cache('lastInjectET');
        }

        // 叫号时间间隔
        $max_interval = Db::name('settings')->where('Name','App.CallNumberTime')->value('Value');
        $max_interval = (int)$max_interval;

        $list = Db::name('queue')->where(['queue'=>$queueName])->count();
        
        if($list == 0){
            // 如果缓存时间为空 或者 小于当前时间减去叫号间隔时间 可以立即叫号
            if($lastExecuteTime == null || $lastExecuteTime == false || ($lastExecuteTime+$max_interval) < time() ){
                $delayTime = 1;
            }else{
                $delayTime = $lastExecuteTime+$max_interval-time();
            }

            // 前边没有延迟一秒直接进行叫号
            // $isPushed = \think\Queue::later(1, $queueClassName, $queue_date, $queueName);

        }else{

            // 最后一个的执行时间
            $p_time = Db::name('queue')->where(['queue'=>$queueName])->order('available_at desc')->value('available_at');

            if(time() > $p_time+$max_interval){
                $delayTime = $max_interval;
            }else{
                $delayTime = $p_time + $max_interval - time();
            }
            
            // $isPushed = \think\Queue::later($delayTime, $queueClassName, $queue_date, $queueName);
        }

        // 执行时间添加到执行内容中
        $queue_date['reserved_time'] = time() + $delayTime;

        $isPushed = \think\Queue::later($delayTime, $queueClassName, $queue_date, $queueName);

        if($isPushed !== false){
            return true;
        }else{
            return false;
        }

    }


    // 叫号
    public function callNumber(Job $job,$data)
    {

        // 当天0点时间戳
        $dateStr = strtotime(date('Y-m-d'));

        // 如果执行时间比当天0点时间早 删除此条数据，并返回
        if($data['reserved_time'] < $dateStr){
            $job->delete();
            return;
        }

        $isJobDone = $this->doCallNumber($data);
        // $job->delete();
        if ($isJobDone) {
            if($data['to_uid'] == 1001){
                cache('lastRegistET',time());
            }elseif($data['to_uid'] == 1002){
                cache('lastInjectET',time());
            }
            //任务执行成功， 删除任务
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

    // 进行叫号
    private function doCallNumber($data) {


        // 指明给谁推送，为空表示向所有在线用户推送
        $to_uid = $data['to_uid'];

        // 推送的url地址，使用自己的服务器地址
        $push_api_url = "http://localhost:2121/";
        $post_data = array(
        "type" => "publish",
        "content" => $data['data'],
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


}