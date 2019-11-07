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

/**
 * 应用公共（函数）文件
 */

use think\Db;
use think\Response;
use think\exception\HttpResponseException;
use app\common\logic\File as LogicFile;



// +---------------------------------------------------------------------+
// | 系统相关函数
// | 
// | 
// | 
// +---------------------------------------------------------------------+


/**
 * add by fjw in 19.7.29
 * 返回数据库模型/表模型
 * 如果在config中定义了另一个数据库配置，可以通过此方法返回数据库模型
 */
function dbModel($table_name = '', $db_config = ''){
    if($db_config == ''){
        return Db::name($table_name);
    }else{
        if($table_name == ''){
            return Db::connect($db_config);
        }else{
            return Db::connect($db_config)->name($table_name);
        }
    }
}


/**
 * add by fjw in 19.7.29
 * 检测前台用户是否登录
 * 
 * @return integer 0-未登录，大于0-当前登录用户ID
 * 
 */
function user_is_login()
{
    
    $user = session('user_auth');
    
    if (empty($user)) {
        
        return DATA_DISABLE;
    } else {
        
        return session('user_auth_sign') == data_auth_sign($user) ? $user['user_id'] : DATA_DISABLE;
    }
}
/**
 * 检测后台用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * 
 */
function member_is_login()
{
    
    $member = session('member_auth');
    
    if (empty($member)) {
        
        return DATA_DISABLE;
    } else {
        
        return session('member_auth_sign') == data_auth_sign($member) ? $member['member_id'] : DATA_DISABLE;
    }
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @param  string $key 盐
 * @return string 
 */
function data_md5($str, $key = 'MamiTianshi')
{
    
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 使用上面的函数与系统加密KEY完成字符串加密
 * @param  string $str 要加密的字符串
 * @return string 
 */
function data_md5_key($str, $key = '')
{
    
    if (is_array($str)) {
        
        ksort($str);

        $data = http_build_query($str);
        
    } else {
        
        $data = (string) $str;
    }
    
    return empty($key) ? data_md5($data, SYS_ENCRYPT_KEY) : data_md5($data, $key);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    
    // 数据类型检测
    if (!is_array($data)) {
        
        $data = (array)$data;
    }
    
    // 排序
    ksort($data);
    
    // url编码并生成query字符串
    $code = http_build_query($data);
    
    // 生成签名
    $sign = sha1($code);
    
    return $sign;
}

/**
 * 检测当前后台用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($member_id = null)
{
    
    $return_id = is_null($member_id) ? member_is_login() : $member_id;
    
    return $return_id && (intval($return_id) === SYS_ADMINISTRATOR_ID);
}

/**
 * 获取单例对象
 */
function get_sington_object($object_name = '', $class = null)
{

    $request = request();
    
    $request->__isset($object_name) ?: $request->bind($object_name, new $class());
    
    return $request->__get($object_name);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name = '')
{
    
    $lower_name = strtolower($name);
    
    $class = SYS_ADDON_DIR_NAME. SYS_DS_CONS . $lower_name . SYS_DS_CONS . $name;
    
    return $class;
}

/**
 * 钩子
 */
function hook($tag = '', $params = [])
{
    
    \think\Hook::listen($tag, $params);
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 */
function addons_url($url, $param = array())
{

    $parse_url  =  parse_url($url);
    $addons     =  $parse_url['scheme'];
    $controller =  $parse_url['host'];
    $action     =  $parse_url['path'];

    /* 基础参数 */
    $params_array = array(
        'addon_name'      => $addons,
        'controller_name' => $controller,
        'action_name'     => substr($action, 1),
    );

    $params = array_merge($params_array, $param); //添加额外参数
    
    return url('addon/execute', $params);
}

/**
 * 插件对象注入
 */
function addon_ioc($this_class, $name, $layer)
{
    
    !str_prefix($name, $layer) && exception('逻辑与模型层引用需前缀:' . $layer);

    $class_arr = explode(SYS_DS_CONS, get_class($this_class));

    $sr_name = sr($name, $layer);

    $class_logic = SYS_ADDON_DIR_NAME . SYS_DS_CONS . $class_arr[DATA_NORMAL] . SYS_DS_CONS . $layer . SYS_DS_CONS . $sr_name;

    return get_sington_object(SYS_ADDON_DIR_NAME . '_' . $layer . '_' . $sr_name, $class_logic);
}

/**
 * 抛出响应异常
 */
function throw_response_exception($data = [], $type = 'json')
{
    
    $response = Response::create($data, $type);

    throw new HttpResponseException($response);
}

/**
 * 获取访问token
 */
function get_access_token()
{

    return md5('MamiTianshi' . date('Ymd') . API_KEY);
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        
        $size /= 1024;
    }
    
    return round($size, 2) . $delimiter . $units[$i];
}


// +---------------------------------------------------------------------+
// | 数组相关函数
// +---------------------------------------------------------------------+

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0)
{
    
    // 创建Tree
    $tree = [];
    
    if (!is_array($list)) {
        
        return false;
    }
    
    // 创建基于主键的数组引用
    $refer = [];

    foreach ($list as $key => $data) {

        $refer[$data[$pk]] =& $list[$key];
    }

    foreach ($list as $key => $data) {

        // 判断是否存在parent
        $parentId =  $data[$pid];

        if ($root == $parentId) {

            $tree[] =& $list[$key];

        } else if (isset($refer[$parentId])){

            is_object($refer[$parentId]) && $refer[$parentId] = $refer[$parentId]->toArray();
            
            $parent =& $refer[$parentId];

            $parent[$child][] =& $list[$key];
        }
    }
    
    return $tree;
}

/**
 * 分析数组及枚举类型配置值 格式 a:名称1,b:名称2
 * @return array
 */
function parse_config_attr($string)
{
    
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    
    if (strpos($string, ':')) {
        
        $value = [];
        
        foreach ($array as $val) {
            
            list($k, $v) = explode(':', $val);
            
            $value[$k] = $v;
        }
        
    } else {
        
        $value = $array;
    }
    
    return $value;
}

/**
 * 解析数组配置
 */
function parse_config_array($name = '')
{
    
    return parse_config_attr(config($name));
}

/**
 * 将二维数组数组按某个键提取出来组成新的索引数组
 */
function array_extract($array = [], $key = 'id')
{
    
    $count = count($array);
    
    $new_arr = [];
     
    for($i = 0; $i < $count; $i++) {
        
        if (!empty($array) && !empty($array[$i][$key])) {
            
            $new_arr[] = $array[$i][$key];
        }
    }
    
    return $new_arr;
}

/**
 * 根据某个字段获取关联数组
 */
function array_extract_map($array = [], $key = 'id'){
    
    
    $count = count($array);
    
    $new_arr = [];
     
    for($i = 0; $i < $count; $i++) {
        
        $new_arr[$array[$i][$key]] = $array[$i];
    }
    
    return $new_arr;
}

/**
 * 页面数组提交后格式转换 
 */
function transform_array($array)
{

    $new_array = array();
    $key_array = array();

    foreach ($array as $key=>$val) {

        $key_array[] = $key;
    }

    $key_count = count($key_array);

    foreach ($array[$key_array[0]] as $i => $val) {
        
        $temp_array = array();

        for( $j=0;$j<$key_count;$j++ ){

            $key = $key_array[$j];
            $temp_array[$key] = $array[$key][$i];
        }

        $new_array[] = $temp_array;
    }

    return $new_array;
}

/**
 * 页面数组转换后的数组转json
 */
function transform_array_to_json($array)
{
    
    return json_encode(transform_array($array));
}

/**
 * 关联数组转索引数组
 */
function relevance_arr_to_index_arr($array)
{
    
    $new_array = [];
    
    foreach ($array as $v)
    {
        
        $temp_array = [];
        
        foreach ($v as $vv)
        {
            $temp_array[] = $vv;
        }
        
        $new_array[] = $temp_array;
    }
    
    return $new_array;
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 */
function arr2str($arr, $glue = ',')
{
    
    return implode($glue, $arr);
}

/**
 * 数组转字符串二维
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 */
function arr22str($arr)
{
    
    $t ='' ;
    $temp = [];
    foreach ($arr as $v) {
        $v = join(",",$v);
        $temp[] = $v;
    }
    foreach ($temp as $v) {
        $t.=$v.",";
    }
    $t = substr($t, 0, -1);
    return $t;
}


// +---------------------------------------------------------------------+
// | 字符串相关函数
// +---------------------------------------------------------------------+

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 */
function str2arr($str, $glue = ',')
{
    
    return explode($glue, $str);
}

/**
 * 字符串替换
 */
function sr($str = '', $target = '', $content = '')
{
    
    return str_replace($target, $content, $str);
}

/**
 * 字符串前缀验证
 */
function str_prefix($str, $prefix)
{
    
    return strpos($str, $prefix) === DATA_DISABLE ? true : false;
}

// +---------------------------------------------------------------------+
// | 文件相关函数
// +---------------------------------------------------------------------+

/**
 * 获取目录下所有文件
 */
function file_list($path = '')
{
    
    $file = scandir($path);
    
    foreach ($file as $k => $v) {
        
        if (is_dir($path . SYS_DS_PROS . $v)) {
            
            unset($file[$k]);
        }
    }
    
    return array_values($file);
}

/**
 * 获取目录列表
 */
function get_dir($dir_name)
{
    
    $dir_array = [];
    
    if (false != ($handle = opendir($dir_name))) {
        
        $i = 0;
        
        while (false !== ($file = readdir($handle))) {
            
            if ($file != "." && $file != ".."&&!strpos($file,".")) {
                
                $dir_array[$i] = $file;
                
                $i++;
            }
        }
        
        closedir($handle);
    }
    
    return $dir_array;
}

/**
 * 获取文件根目录
 */
function get_file_root_path()
{
    
    $root_arr = explode(SYS_DS_PROS, URL_ROOT);
    
    array_pop($root_arr);
    
    $root_url = arr2str($root_arr, SYS_DS_PROS);
    
    return $root_url . SYS_DS_PROS;
}

/**
 * 获取图片url
 */
function get_picture_url($id = 0)
{

    $fileLogic = get_sington_object('fileLogic', LogicFile::class);
    
    return $fileLogic->getPictureUrl($id);
}

/**
 * 获取文件url
 */
function get_file_url($id = 0)
{
    
    $fileLogic = get_sington_object('fileLogic', LogicFile::class);
    
    return $fileLogic->getFileUrl($id);
}

/**
 * 删除所有空目录 
 * @param String $path 目录路径 
 */
function rm_empty_dir($path)
{
    
    if (!(is_dir($path) && ($handle = opendir($path))!==false)) {
        
        return false;
    }
      
    while(($file = readdir($handle))!==false)
    {

        if (!($file != '.' && $file != '..')) {
            
           continue;
        }
        
        $curfile = $path . SYS_DS_PROS . $file;// 当前目录

        if (!is_dir($curfile)) {
            
           continue;  
        }

        rm_empty_dir($curfile);

        if (count(scandir($curfile)) == 2) {
            
            rmdir($curfile);
        }
    }

    closedir($handle); 
}


// +---------------------------------------------------------------------+
// | 时间相关函数
// +---------------------------------------------------------------------+

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 */
function format_time($time = null, $format='Y-m-d H:i:s')
{
    
    if (null === $time) {
        
        $time = TIME_NOW;
    }
    
    return date($format, intval($time));
}

/**
 * 获取指定日期段内每一天的日期
 * @param Date $startdate 开始日期
 * @param Date $enddate  结束日期
 * @return Array
 */
function get_date_from_range($startdate, $enddate)
{
    
  $stimestamp = strtotime($startdate);
  $etimestamp = strtotime($enddate);
  
  // 计算日期段内有多少天
  $days = ($etimestamp-$stimestamp)/86400+1;
  
  // 保存每天日期
  $date = [];
  
  for($i=0; $i<$days; $i++) {
      
      $date[] = date('Y-m-d', $stimestamp+(86400*$i));
  }
  
  return $date;
}

// +---------------------------------------------------------------------+
// | 调试函数
// +---------------------------------------------------------------------+

/**
 * 将数据保存为PHP文件，用于调试
 */
function sf($arr = [], $fpath = './test.php')
{
    
    $data = "<?php\nreturn ".var_export($arr, true).";\n?>";
    
    file_put_contents($fpath, $data);
}

/**
 * dump函数缩写
 */
function d($arr = [])
{
    dump($arr);
}

/**
 * dump与die组合函数缩写
 */
function dd($arr = [])
{
    dump($arr);die;
}


// +---------------------------------------------------------------------+
// | 其他函数
// +---------------------------------------------------------------------+

/**
 * 通过类创建逻辑闭包
 */
function create_closure($object = null, $method_name = '', $parameter = [])
{
    
    $func = function() use($object, $method_name, $parameter) {
        
                return call_user_func_array([$object, $method_name], $parameter);
            };
            
    return $func;
}

/**
 * 通过闭包控制缓存
 */
function auto_cache($key = '', $func = null, $time = 3)
{
    
    $result = cache($key);
    
    if (empty($result)) {
        
        $result = $func();
        
        !empty($result) && cache($key, $result, $time);
    }
    
    return $result;
}

/**
 * 通过闭包列表控制事务
 */
function closure_list_exe($list = [])
{
    
    Db::startTrans();
    
    try {
        
        foreach ($list as $closure) {
            
            $closure();
        }
        
        Db::commit();
        
        return true;
    } catch (\Exception $e) {
        
        Db::rollback();
        
        throw $e;
    }
}

/**
 * 自动封装事务
 */
function trans($parameter = [], $callback = null)
{
    
    try {

        Db::startTrans();

        $backtrace = debug_backtrace(false, 2);

        array_shift($backtrace);

        $class = $backtrace[0]['class'];

        $result = (new $class())->$callback($parameter);

        Db::commit();

        return $result;

    } catch (Exception $ex) {

        Db::rollback();

        throw new Exception($ex->getMessage());
    }
}

/**
 * 更新缓存版本
 */
function update_cache_version($obj = null)
{
    
    $ob_auto_cache = cache('ob_auto_cache');

    is_string($obj) ? $ob_auto_cache[$obj]['version']++ : $ob_auto_cache[$obj->getTable()]['version']++;

    cache('ob_auto_cache', $ob_auto_cache);
}


/**+---------------------------------------------------------------------------------
 * | add by fjw in 19.7.29
 * | 以下是增加一些公共方法
 * | 
 * | 
 * +---------------------------------------------------------------------------------
 */

#获取订单号
function setOrderID(){
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return $orderSn;
}

/**
 * 获取唯一的机器码/激活码
 */
function setActCode(){
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $actSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d');
    $actSn .= '-'.substr(time(), -3).$yCode[rand(0,25)].'-';
    $actSn .= $yCode[rand(0,25)].substr(microtime(), 2, 3).'-';
    $actSn .= $yCode[rand(0,25)].sprintf('%02d', rand(0, 99)).$yCode[rand(0,25)];
    return $actSn;
}

//获取当前客户端的IP地址
function clientIP() { 
    if(getenv('HTTP_CLIENT_IP')){ 
        $client_ip = getenv('HTTP_CLIENT_IP'); 
    } elseif(getenv('HTTP_X_FORWARDED_FOR')) { 
        $client_ip = getenv('HTTP_X_FORWARDED_FOR'); 
    } elseif(getenv('REMOTE_ADDR')) {
        $client_ip = getenv('REMOTE_ADDR'); 

    } else {
        $client_ip = $_SERVER['REMOTE_ADDR'];
    } 
    return $client_ip; 
}

// +----------------------------------------------------
// |判断是否是手机端登录
// |
// |
// +----------------------------------------------------
function isMobile(){  
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';  
        $mobile_browser = '0';  
    if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))  
        $mobile_browser++;  
    if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))  
        $mobile_browser++;  
    if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  
        $mobile_browser++;  
    if(isset($_SERVER['HTTP_PROFILE']))  
        $mobile_browser++;  
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));  
    $mobile_agents = array(  
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
        'wapr','webc','winw','winw','xda','xda-' 
    );  
    if(in_array($mobile_ua, $mobile_agents))
        $mobile_browser++;  
    if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)  
        $mobile_browser++;  
    // Pre-final check to reset everything if the user is on Windows  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)  
        $mobile_browser=0;  
    // But WP7 is also Windows, with a slightly different characteristic  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)  
        $mobile_browser++; 
    if($mobile_browser>0)  
        return true;  
    else 
        return false; 
    
}

// 是否是手机号码
function isMobileNumber($phonenumber){
    if(preg_match("/^1[34578]{1}\d{9}$/",$phonenumber)){  
        return true; 
    }else{  
        return false; 
    } 
}

# php socket udp
// function send_udp_message($host, $port, $message)
// {
//     $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
//     @socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array(
//         "sec" => 2,
//         "usec" => 0
//     ));

//     // @socket_connect($socket, $host, $port);
//     // 发送命令
//     @socket_sendto($socket, $message, strlen($message), 0, $host, $port);
    
//     @socket_recvfrom($socket, $buffer, 1024, 0, $host, $port);
//     // 关闭连接
//     socket_close($socket);

//     dump($buffer);

//     if (!empty($buffer)) {
//         return $buffer;
//     } else {
//         echo "fail";
//     }
// }


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
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);     // curl 时间超时   
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}


#https   GET请求处理函数
function httpsGet($url){
    $oCurl = curl_init();
    if(stripos($url, 'https://')!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus['http_code'])==200){
        return $sContent;
    }else{
        return false;
    }
}

/**
 * 模拟form表单post
 */
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
 * xml 转 数组  将 xml数据转换为数组格式。
 */

function xmlToArray($xml){
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches)){
        $count = count($matches[0]);
        for($i = 0; $i < $count; $i++){
        $subxml= $matches[2][$i];
        $key = $matches[1][$i];
            if(preg_match( $reg, $subxml )){
                $arr[$key] = xmlToArray( $subxml );
            }else{
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}

/**
 * 在源文件路径下边，生成缩略图
 */
function createThumb($src = '', $width = 410, $height = 410, $type = 1, $replace = false) {
    $src = './'.$src;
    if(is_file($src) && file_exists($src)) {
        $ext = pathinfo($src, PATHINFO_EXTENSION);
        $name = basename($src, '.'.$ext);
        $dir = dirname($src);
        
        if(in_array($ext, array('gif','jpg','jpeg','bmp','png'))) {
            $name = $name.'_thumb_'.$width.'_'.$height.'.'.$ext;
            $file = $dir.'/'.$name;
            if(!file_exists($file) || $replace == TRUE) {
                $image = \think\Image::open($src);
                $image->thumb($width, $height, $type);
                $image->save($file);
            }
            $file=str_replace("\\","/",$file);
            $file = '/'.trim($file,'./');
            return $file;
        }
    }else{
        return '路径错误/文件不存在'; die;
    }
    $src=str_replace("\\","/",$src);
    $src = '/'.trim($src,'./');
    return $src;
}

function getBetweenStr($input, $start, $end) {
    $start = strlen($start) + strpos($input, $start);
    $end = (strlen($input) - strpos($input, $end))*(-1);
    $substr = substr($input, $start, $end);
    return $substr; 
     
}


/**
 * phpqrcode
 */
// function createPHPQrcode($url='地址为空', $is_logo=false){
//     Vendor('phpqrcode.phpqrcode');  
//     $level = 'L';
//     $size = 5;
//     //容错级别  
//     $errorCorrectionLevel = intval($level);  
//     //生成图片大小  
//     $matrixPointSize = intval($size);  
//     //生成二维码图片  
//     $object = new \QRcode();  
    
//     // if($is_logo){
//     //     $logo = $this->logo_path;//准备好的logo图片 
//     //     $QR = 'qrcode.png';//已经生成的原始二维码图 
//     //     // $object->png($url, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2);  
//     // }else{
//         ob_start(); 
//         //第二个参数false的意思是不生成图片文件，如果你写上‘picture.png’则会在根目录下生成一个png格式的图片文件  
//         $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
//         $imageString = base64_encode(ob_get_contents());  
//         ob_end_clean();  
//         return 'data:image/png;base64,'.$imageString;// 该图片可以直接加入img的src
//         // echo '<img src="data:image/png;base64,'.$imageString.'">';
//     // }
    
// }

/**
 * 保存base64图片
 */
function saveImg($base64, $file_name)
{

    $top = 'data:image/jpg;base64,';

    // 判断目录是否存在，不存在创建
    if(!file_exists(PATH_DATE)){
        mkdir(PATH_DATE, 0777, true);
    }

    $base64_img = trim($top . $base64);

    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){

        $type = $result[2];

        $new_file = PATH_DATE . $file_name; // 存放位置

        if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))){
            // return $new_file;
            return DS . 'upload' . DS . 'Attachments' . DS . NOW_DATE . DS . $file_name ;
        }

    }

}



/**
 * 判断 number 的第一位 如果是 A 返回 A ， 如果是 V 返回 V
 */
function reNumO($number)
{
    if(strtoupper($number[0]) == 'V'){
        return ['like','%V%'];
    }else{
        return ['like','%A%'];
    }
}

/**
 * 判断 number 的第一位 如果是 A 返回 V ， 如果是 V 返回 A
 */
function reNumT($number)
{
    if(strtoupper($number[0]) == 'V'){
        return ['like','%A%'];
    }else{
        return ['like','%V%'];
    }
}



