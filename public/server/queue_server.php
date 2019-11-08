<?php
/**
 * create by fjw in 19.10.30
 */

/**
 * 创建日志
 */
global $log_file;
global $db_conn;

// global $refrigeratorUrl; // 冰箱服务域名
// global $appUrl; // 小程序/app 服务域名
global $immediatelyQueueUrl;  // 实时队列发送地址
global $immediatelyQueueRate; // 实时队列发送频率，秒数
global $serverBeginTime; // 服务执行开始时间
global $serverEndTime; // 服务执行结束时间


global $InjectPositionID; // 登记台ID


$date = date('Y-m-d');
$log_path = __DIR__ . '/serverlog/queue_push/';
$log_file =  $log_path.$date.'.log';
if(!is_dir($log_path)){
    mkdir($log_path,0777,true);//创建目录
}
if(!file_exists($log_file)){
    file_put_contents($log_file, ' === 创建queue服务日志文件 === ' . "\r\n");
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





$immediatelyQueueRate = 300; // 
$immediatelyQueueUrl = 'http://xiaoai.mamitianshi.com/positionInjectQueue'; // 
$InjectPositionID = '';


$sql = "SELECT `Name`, `Value` FROM settings 
        WHERE `Name`='App.InjectPositionId' 
        or `Name`='App.refrigeratorUrl' 
        or `Name`='App.appUrl' 
        or `Name`='App.serverBeginTime' 
        or `Name`='App.serverEndTime' 
        or `Name`='App.immediatelyQueueUrl' 
        or `Name`='App.immediatelyQueueRate' 
        ";
         
        // or `Name`='App.ObseveScreenRefreshRate' 
        // or `Name`='App.QueueServerAddress'

$result = $db_conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        switch($row['Name']){
            case 'App.InjectPositionId':
                $InjectPositionID = $row['Value'];
            break;
            case 'App.refrigeratorUrl':
                $refrigeratorUrl = $row['Value'];
            break;
            case 'App.appUrl':
                $appUrl = $row['Value'];
            break;
            case 'App.serverBeginTime':
                $serverBeginTime = $row['Value'];
            break;
            case 'App.serverEndTime':
                $serverEndTime = $row['Value'];
            break;
            case 'App.immediatelyQueueUrl':
                $immediatelyQueueUrl = $row['Value'];
            break;
            case 'App.immediatelyQueueRate':
                $immediatelyQueueRate = $row['Value'];
            break;
            default:  break;
        }
    }

} else {
    serverLog('初始化数据未查询到结果');
    serverEcho("初始化数据未查询到结果");
}

// echo $InjectPositionID;
// echo PHP_EOL;
// die;


// 登记台排队队列推送频率
$task = new Worker();
$task->count = 1;
$task->onWorkerStart = function($task){
    
    // NN秒后执行发送邮件任务，最后一个参数传递false，表示只运行一次
    Timer::add($GLOBALS['immediatelyQueueRate'], 'sendCurrentQueue', array(), true);
    // Timer::add(5, 'sendCurrentQueue', array(), true);

};
// 运行worker
Worker::runAll();

// 发送当前接种点的排队队列
function sendCurrentQueue()
{
    $hour = intval(date('H'));
    if($hour >= intval($GLOBALS['serverBeginTime']) && $hour < intval($GLOBALS['serverEndTime'])){

        /**
         * condition：
         *  
         *  
         */
        $queue_list = [];
        $today = date('Y-m-d');

        // $old_time = date('Y-m-d H:i:s', intval(time())-1800);

        $sql = "SELECT `Number` 
            FROM vaccinations 
            WHERE CreationTime LIKE '$today%' and VaccinationDate LIKE '$today%' and `State` < 2 and status = 1 
            ORDER BY Id desc
        ";

        $result = $GLOBALS['db_conn']->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $queue_list[] = $row['Number'];
            }
        } else {
            serverLog('******* 数据库queue队列为空 *******');
            serverEcho("******* 数据库queue队列为空 *******");
        }
        
        if(!empty($queue_list)){
            $data = [
                'uc'=>$GLOBALS['InjectPositionID'], 'mac'=>$GLOBALS['InjectPositionID'],
                'ql'=>json_encode($queue_list)
            ];

            $send_url = explode(';', $GLOBALS['immediatelyQueueUrl']);
            foreach($send_url as $v){
                $send_response = formPost($v, $data);
                serverEcho($v.' / '.$send_response);
                serverLog($v.' / '.$send_response);

            }
        }else{
            serverEcho('队列为空，不发送');
            serverLog('队列为空，不发送');
        }

    }else{
        serverLog('未到服务开始时间');
        serverEcho('未到服务开始时间');
    }

    

}

#https POST 请求处理函数 
function formPost($url, $data, $headers = []){
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

/**
 * 日志方法
 */
function serverLog($msg){

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