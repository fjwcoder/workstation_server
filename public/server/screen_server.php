<?php
/**
 * create by fjw in 19.
 */

/**
 * 创建日志
 */
global $log_file; // 服务文件
global $db_conn; // 数据库连接对象

// global $refrigeratorUrl; // 冰箱服务域名
// global $appUrl; // 小程序/app 服务域名
global $serverBeginTime; // 服务执行开始时间
global $serverEndTime; // 服务执行结束时间

// global $WritingDeskScreenRows;
// global $WritingDeskScreenInterval;
// global $VaccinationDeskScreenRows;
// global $VaccinationDeskScreenInterval;
global $ObseverRoomScreenRows; // 留观队列屏幕显示条数
global $ObseveScreenRefreshRate; // 留观队列屏幕刷新频率
global $QueueServerAddress; // 留观队列推送推送地址
global $waitting_queue; // 留观队列


$date = date('Y-m-d');
$log_path = __DIR__ . '/serverlog/screen_push/';
$log_file =  $log_path.$date.'.log';
if(!is_dir($log_path)){
    mkdir($log_path,0777,true);//创建目录
}
if(!file_exists($log_file)){
    file_put_contents($log_file, ' === 创建推屏日志文件 === ' . "\r\n");
}

serverLog('************* 服务启动 ***************');
 /**
  * 是否存在需要引入的文件
  */
if(file_exists(__DIR__ . '/../../vendor/workerman/workerman/Autoloader.php')){
    require_once __DIR__ . '/../../vendor/workerman/workerman/Autoloader.php';
}else{
    serverLog('workerman/Autoloader.php 文件不存在');
}




use Workerman\Worker;
use Workerman\Lib\Timer;

/**
 * 数据库连接
 */
$db_config = [];
if(file_exists(__DIR__ . '/../../app/database.php')){
    $db_config = require_once __DIR__ . '/../../app/database.php';
}else{
    serverLog('数据库配置文件 database.php 文件不存在');
}
$db_servername = $db_config['hostname']; //"127.0.0.1";
$db_username = $db_config['username']; //"root";
$db_password = $db_config['password']; //"19920104";
$db_name = $db_config['database']; //"rt_workstation";


// 创建连接

$db_conn = new mysqli($db_servername, $db_username, $db_password, $db_name);
// Check connection
if ($db_conn->connect_error) {
    serverLog("数据库连接失败: " . $db_conn->connect_error);
    die("NO 数据库连接失败: " . $db_conn->connect_error);
} else{
    serverLog('数据库连接成功');
    serverEcho('YES 数据库连接成功');
}


/**
 * 初始化显示屏参数配置
 */

// $WritingDeskScreenRows = 10; // 登记台显示行数
// $WritingDeskScreenInterval = 20; // 登记台屏幕刷新频率 单位：s
// $VaccinationDeskScreenRows = 10; //接种台显示行数
// $VaccinationDeskScreenInterval = 20; // 接种台屏幕刷新频率 单位：s
$ObseverRoomScreenRows = 10; // 留观台显示行数
$ObseveScreenRefreshRate = 30; // 留观台屏幕刷新频率 单位：s
$QueueServerAddress = null; // 显示队列发送地址
$waitting_queue = [];

// $sql = "SELECT `Name`, `Value` FROM settings 
//         WHERE `Name`='App.WritingDeskScreenRows' 
//         or  `Name`='App.WritingDeskScreenInterval' 
//         or `Name`='App.VaccinationDeskScreenRows' 
//         or `Name`='App.VaccinationDeskScreenInterval' 
//         or `Name`='App.ObseverRoomScreenRows' 
//         or `Name`='App.ObseveScreenRefreshRate' 
//         or `Name`='App.QueueServerAddress'";




$sql = "SELECT `Name`, `Value` FROM settings 
        WHERE `Name`='App.ObseverRoomScreenRows' 
        or `Name`='App.ObseveScreenRefreshRate' 
        or `Name`='App.QueueServerAddress' 
        or `Name`='App.serverBeginTime' 
        or `Name`='App.serverEndTime' 
        ";

$result = $db_conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        switch($row['Name']){
            case 'App.ObseverRoomScreenRows':
                $ObseverRoomScreenRows = $row['Value'];
            break;
            case 'App.ObseveScreenRefreshRate':
                $ObseveScreenRefreshRate = $row['Value'];
            break;
            case 'App.QueueServerAddress':
                $QueueServerAddress = $row['Value'];
            break;
            // case 'App.refrigeratorUrl':
            //     $refrigeratorUrl = $row['Value'];
            // break;
            // case 'App.appUrl':
            //     $appUrl = $row['Value'];
            // break;
            case 'App.serverBeginTime': 
                $serverBeginTime = $row['Value'];
            break;
            case 'App.serverEndTime':
                $serverEndTime = $row['Value'];
            break;
            default:  break;
        }
    }

} else {
    serverLog('初始化数据未查询到结果');
    serverEcho("初始化数据未查询到结果");
}


// 登记台刷新频率
$task = new Worker();
$task->count = 1;
$task->onWorkerStart = function($task){
    

    // N秒后执行发送邮件任务，最后一个参数传递false，表示只运行一次
    Timer::add($GLOBALS['ObseveScreenRefreshRate'], 'sendWaitingQueue', array(), true);
    // Timer::add(5, 'serverLog', array(), true);
};
// 运行worker
Worker::runAll();

// 发送留观队列
function sendWaitingQueue()
{
    // 获取当前小时数
    $hour = intval(date('H'));
    if($hour >= intval($GLOBALS['serverBeginTime']) && $hour < intval($GLOBALS['serverEndTime'])){
        //  查询留观中
        if(empty($GLOBALS['waitting_queue'])){
            serverLog('------- 内存留观队列为空，重新查询 -------');
            /**
             * condition：
             *  State >= 2
             *  
             */
            $today = date('Y-m-d');
            $old_time = date('Y-m-d H:i:s', intval(time())-1800);

            $sql = "SELECT `Number`, `ChildId`, `Name`, `VaccinationFinishTime`   
                FROM vaccinations as a left join childs as b on a.ChildId=b.id 
                WHERE a.CreationTime LIKE '$today%'
                and `State` >= 2 
                and `VaccinationFinishTime` >= '$old_time' 
            ";

            $result = $GLOBALS['db_conn']->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    
                    $GLOBALS['waitting_queue'][$row['Number']] = $row;
        
                }
            
            } else {
                serverLog('******* 数据库留观队列为空 *******');
                serverEcho("******* 数据库留观队列为空 *******");
            }
        }

        // var_export($GLOBALS['waitting_queue']);
        $data = ['deviceId'=>13, 'data'=>[]];
        if(empty($GLOBALS['waitting_queue'])){
            serverLog('------- 内存留观队列为空 -------');
            serverEcho('------- 内存留观队列为空 -------');
            
            $send_response = httpsPost($GLOBALS['QueueServerAddress'], json_encode($data, 320));
            serverLog('空数据推送返回：'.$send_response);
        }else{
            // var_export($GLOBALS['waitting_queue']); die;
            // $send_queue = array_slice($GLOBALS['waitting_queue'], 0, 2);
            $send_queue = array_slice($GLOBALS['waitting_queue'], 0, $GLOBALS['ObseverRoomScreenRows']); // 取出前N个数据
            

            foreach($send_queue as $k=>$v){
                // $inoculabilityTime = substr($v['VaccinationFinishTime'], -15, 8);
                $inoculabilityTime = substr($v['VaccinationFinishTime'], -8);
                // echo $inoculabilityTime;
                // echo PHP_EOL;
                $remainingTime = floor((time()-strtotime($inoculabilityTime))%86400/60);
                if($remainingTime > 31){
                    continue;
                }else{
                    $remainingTime = ($remainingTime >= 30) ? 0 : (30-$remainingTime); 
                    $data['data'][] = ['number'=>$v['Number'], 'childName'=>$v['Name'], 'inoculabilityTime'=>$inoculabilityTime, 'remainingTime'=>intval($remainingTime)]; 
                }

            }
            // var_export($data);

            $send_response = httpsPost($GLOBALS['QueueServerAddress'], json_encode($data, 320));
            serverLog('队列数据推送返回：'.$send_response);
            $send_response = json_decode($send_response, true);
            if($send_response['sucess'] == true){ // 发送成功
                foreach($send_queue as $k=>$v){
                    unset($GLOBALS['waitting_queue'][$k]);
                }

            }
        }
    }else{
        serverLog('未到服务开始时间');
        serverEcho('未到服务开始时间');
    }
    



}

#https POST 请求处理函数 
function httpsPost($url, $data = null, $headers = []){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if(!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);   // tcp 握手时间超时       
    curl_setopt($curl, CURLOPT_TIMEOUT, 15);     // curl 时间超时     
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * 日志方法
 */
function serverLog($msg){
    // $date = date('Y-m-d');
    $datetime = date('Y-m-d H:i:s');

    $content = $datetime . ' ====== ：' .$msg ."\r\n";

    $log_res = file_put_contents($GLOBALS['log_file'], $content, FILE_APPEND);

    return $log_res;
}

/**
 * 重写的echo 方法
 */
function serverEcho($msg){
    
    echo $msg . "\r\n";
}