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

namespace app\api\error;

class CodeBase
{
    
    public static $success              = [API_CODE_NAME => 200,         API_MSG_NAME => '操作成功'];
    
    public static $accessTokenError     = [API_CODE_NAME => 1000001,   API_MSG_NAME => '访问Toekn错误'];
    
    public static $userTokenNull        = [API_CODE_NAME => 1000002,   API_MSG_NAME => '用户Toekn不能为空'];
    
    public static $apiUrlError          = [API_CODE_NAME => 1000003,   API_MSG_NAME => '接口路径错误'];
    
    public static $dataSignError        = [API_CODE_NAME => 1000004,   API_MSG_NAME => '数据签名错误'];
    
    public static $userTokenError       = [API_CODE_NAME => 1000005,   API_MSG_NAME => '用户Toekn解析错误'];

    public static $dataDecryptError       = [API_CODE_NAME => 1000006,   API_MSG_NAME => '数据解析错误'];
    
}
