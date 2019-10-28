<?php
/**
 * create by fjw in 19.
 */

 /**
  * 是否存在需要引入的文件
  */
if(file_exists(__DIR__ . '/../vendor/workerman/workerman/Autoloader.php')){
    require_once __DIR__ . '/../vendor/workerman/workerman/Autoloader.php';
}else{
    echo 'no'; 
    echo PHP_EOL; die;
}

use Workerman\Worker;
use Workerman\Lib\Timer;

/**
 * 数据库连接
 */
$db_servername = "192.168.0.250";
$db_username = "root";
$db_password = "ruitong2019";
$db_name = "rtdb";
 
// 创建连接
$db_conn = new mysqli($db_servername, $db_username, $db_password, $db_name);
// Check connection
if ($db_conn->connect_error) {
    die("连接失败: " . $db_conn->connect_error);
} 

/**
 * 初始化显示屏参数配置
 */
$WritingDeskScreenRows = 10; // 登记台显示行数
$WritingDeskScreenInterval = 30; // 登记台屏幕刷新频率 单位：s
$VaccinationDeskScreenRows = 10; //接种台显示行数
$VaccinationDeskScreenInterval = 30; // 接种台屏幕刷新频率 单位：s
$ObseverRoomScreenRows = 10; // 留观台显示行数
$ObseveScreenRefreshRate = 30; // 留观台屏幕刷新频率 单位：s
$QueueServerAddress = null; // 显示队列发送地址

$sql = "SELECT `Name`, `Value` FROM settings 
        WHERE `Name`='App.WritingDeskScreenRows' 
        or  `Name`='App.WritingDeskScreenInterval' 
        or `Name`='App.VaccinationDeskScreenRows' 
        or `Name`='App.VaccinationDeskScreenInterval' 
        or `Name`='App.ObseverRoomScreenRows' 
        or `Name`='App.ObseveScreenRefreshRate' 
        or `Name`='App.QueueServerAddress'";

$result = $db_conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        switch($row['Name']){
            case 'App.WritingDeskScreenRows':
                $WritingDeskScreenRows = $row['Value'];
            break;
            case 'App.WritingDeskScreenInterval':
                $WritingDeskScreenInterval = $row['Value'];
            break;
            case 'App.VaccinationDeskScreenRows': 
                $VaccinationDeskScreenRows = $row['Value'];
            break;
            case 'App.VaccinationDeskScreenInterval':
                $VaccinationDeskScreenInterval = $row['Value'];
            break;
            case 'App.ObseverRoomScreenRows':
                $ObseverRoomScreenRows = $row['Value'];
            break;
            case 'App.ObseveScreenRefreshRate':
                $ObseveScreenRefreshRate = $row['Value'];
            break;
            case 'App.QueueServerAddress':
                $QueueServerAddress = $row['Value'];
            break;
            default:  break;
        }
    }

} else {
    echo "0 结果";
}
// echo $QueueServerAddress; die;
// 登记台刷新频率
/**
 * 查询数据
 */
function selectData(){
    // 1. 查询待登记

    // 2. 查询待


    echo $ObseveScreenRefreshRate;
    echo PHP_EOL;
    echo date('Y-m-d H:i:s');
    echo PHP_EOL;
}

// 普通的函数
function send_mail($QueueServerAddress)
{
    // selectData();
    $array = [
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

    echo $QueueServerAddress;
    echo PHP_EOL;

    $send_result = httpsPost($QueueServerAddress, json_encode($array, 320));

    var_export($send_result);
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
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

$task = new Worker();
$task->count = 1;
// echo date('Y-m-d H:i:s');
// echo PHP_EOL; 
// die;
$task->onWorkerStart = function($task){
    

    // 10秒后执行发送邮件任务，最后一个参数传递false，表示只运行一次
    Timer::add(5, 'send_mail', array($QueueServerAddress), true);
};
// 运行worker
Worker::runAll();