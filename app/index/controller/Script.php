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

use think\Db;
use \rsa\RSA;
use think\Cache;
/**
 * 此控制器是专门用于编写测试方法和测试脚本使用 add by fjw 
 */
class Script extends IndexBase
{

    public function test3(){
        $where = [
            'IsDeleted' => 0,
            'VaccinationDate' => ['like','%'.NOW_DATE.'%'],
            'State' => 0,
            // 'Id' => ['<',1854],
        ];

        dump($where);

        // $queueLength1 = $this->modelVaccinations->stat($where);
        $queueLength2 = Db::name('vaccinations')->where($where)->count();
        // dump($queueLength1);
        dump($queueLength2);
        die;
    }

    public function test2(){

        $data = '{"attachments":[{"content":"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","name":"head.JPG"},{"content":"data:image/jpg;base64,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","name":"fingerprint.JPG"}],"number":"A019"}';

        $result = httpsPost('http://192.168.0.120:21022/api/Queue/Push',$data);

        $result = json_decode($result,true);

        dump($result);
    }

    public function test1(){
        $top = base64_encode('rt:123Qwe');
        dump($top); die;
        // 'http://127.0.0.1:4200/api/Queue/Push'
        $data = '
        {
            "number":"0001",
                   "attachments":
           [
           { name:"head.JPG",
           content:"af32f……….",
           },
           { name:"fingerprint.JPG",
           content:"af32f……….",
           }
           ]
           }
        ';

        $header = [
            'Authenticate'=>'Basic realm='.$top
        ];

        // header ( 'WWW-Authenticate: Basic realm="Test Authentication System"' );

        $result1 = httpsPost('http://127.0.0.1:21022/api/Queue/Push',$data,$header);

        $result2 = httpsPost('http://127.0.0.1:4200/api/Queue/Push',$data,$header);

        dump($result1);

        dump($result2);
    }

    public function djt(){

        $data = '{
            "deviceId":1,
            "data":{
                "number":"A001",
                "writingDesk":"登记台1"
            }
        }';

        $result = httpsPost('http://192.168.0.249:9500',$data);

        dump($result);
    }


    public function jzs(){

        $data = '{
            "deviceId":2,
            "data":{
                "number":"A001",
                "childName":"小明"
            }
        }';

        $result = httpsPost('http://192.168.0.249:9500',$data);

        dump($result);
    }

    public function test11(){

        $data = '{
            "deviceId":11,
            "data":{
                "number":"A001",
                "writingDesk":"111"
            }
        }';

        // $data = [
        //     'deviceId'=>'11',
        //     'data'=>[
        //         'number'=>'0001',
        //         'writingDesk'=>'登记台1',
        //     ],
        //     'targetUrl'=>Null,
        //     'success'=>True,
        //     'error'=>Null,
        //     'unAuthorizedRequest'=>False,
        //     '__abp'=>True
        // ];

        // $data = [
        //     'deviceId'=>11,
        //     'data'=>[
        //         'number'=>'0001',
        //         'writingDesk'=>'登记台1'
        //     ],
        // ];

        // $result = httpsPost('http://192.168.0.249:9500', json_encode($data) );
        $result = httpsPost('http://192.168.0.249:9500', $data );

        dump($result);

        
    }

    public function test12(){

        $data = '{
            "deviceId":12,
            "data":{
                "number":"11",
                "childName":"22",
                "consultingRoom":"333"
            }
        }';

        // $data = [
        //     'deviceId'=>'12',
        //     'data'=>[
        //         'number'=>'A001',
        //         'childName'=>'小红',
        //         'consultingRoom'=>'诊室1',
        //     ],
        //     'targetUrl'=>Null,
        //     'success'=>True,
        //     'error'=>Null,
        //     'unAuthorizedRequest'=>False,
        //     '__abp'=>True
        // ];

        // $data = [
        //     'deviceId'=>12,
        //     'data'=>[
        //         'number'=>'0001',
        //         'childName'=>'111',
        //         'consultingRoom'=>'1111'
        //     ],
        // ];

        // dump($data);

        // dump(json_encode($data));die;

        $result = httpsPost('http://192.168.0.249:9500',$data);
        // $result = httpsPost('http://192.168.0.249:9500',json_encode($data));

        dump($result);

    }

    public function test13(){

        $data = '{
            deviceId:13,
            data:{
                number:A001,
                childName:000,
                inoculabilityTime:05:25:25,
                remainingTime:10,
            }
        }';

        // $data = [
        //     'deviceId'=>13,
        //     'data'=>[
        //         'number'=>'A001',
        //         'childName'=>'111',
        //     ],
        // ];

        // $result = httpsPost('http://192.168.0.249:9500', json_encode($data) );
        $result = httpsPost('http://192.168.0.249:9500',$data );

        dump($result);

    }







































    public function vaccine(){
        $vaccine = Db::name('vaccine') -> column('ym_id, ym_jzdate, week, ym_text, ym_yongtu, ym_jianjie');
        // dump($vaccine); die;
        foreach($vaccine as $k=>$v){
            $update = ['inject_date'=>$v['ym_jzdate'], 'inject_week'=>$v['week'], 'vaccine_text'=>$v['ym_text'],
                'affect'=>$v['ym_yongtu'], 'introduce'=>$v['ym_jianjie'], 'status'=>1];
            $up = Db::name('m_vaccine') -> where(['id'=>$v['ym_id']]) ->update($update);
            dump($up);
        }
    }
 
    public function index(){

        $pub_key = file_get_contents(rsa_file('pub'));
        $pri_key = file_get_contents(rsa_file('pri'));

        $this->assign('public_key', $pub_key);
        $this->assign('private_key', $pri_key);
        return $this->fetch();
    }

    public function formatPosition(){
        $list  = dbModel('m_inject_position') -> select();
        $region = [
            '颍州区',
            '颍东区',
            '颍泉区',
            '临泉县',
            '太和县',
            '阜南县',
            '颍上县',
            '界首市',
        ];
        foreach($list as $k=>$v){
            $district = substr($v['address'], 0, 9);
            if(in_array($district, $region)){
                $add = substr($v['address'], 9);
                Db::name('m_inject_position') -> where(['id'=>$v['id']]) -> update(['district'=>$district, 'address'=>$add]);
            }
            

        }
    }

    public function sendSms(){
        $this->logicScript->sendSms();
    }

    public function rsa(){

        $param = [
            'name'=>'fjw', 'sex'=>'男'
        ];
// 明文过长，失败
//         $param = [

//             '===私钥加密 + 公钥解密======================================',
// 'string(344) "bAOujzXYjQ8jw/Vdg8mJCs51D5WB8BOntaYlGy56GLRZQ2Rh8vNtWV39eJyh+zjZpX9gv7iUd+IqAit1klK2r5AUAuPiJbZFFj4SVT+IZkK4fnBk4MfVz9KuOffwU5tly/JdiS4hde86qZIrUwl3h90P3rMXF9Cc8mwY2DuRcAEFFKcNndjKfoLK40h2rwDrlHDN9kS10xSboYD8jF1v0VKo/L8nl5MAon3Z3V1qIX9GrPG5X1bbCy3/Z1azRGK4/+aU+9rZabsVggYhk8WdIoeSzoi4IYt41XXDV3GFDkKv5+mSzE7/3BRpNNeoiCumA5GCpdhSyGZVSEmk+1B7lQ=="',
// 'string(26) "{"name":"fjw","sex":"男"}"',
// '===公钥加密 + 私钥解密======================================',
// 'string(344) "mfQBeh3lW2z2zMcXEEnTrq5dCefugv3zWyewhBE4VxPvYhX2Z5Jj/DAzUAL8biCrIByCKo8aLwEXOkE8sv1mAQMfoh+2TiGFL44Mhqg2riHPJ3NZd2hPx+PQM4WK+t3TnZd5Ufv4QPsdm5yki/zi3QznnhrOfcLm9zWcHpcp0RQ+ZOB6KjejCAxfLwfKl1i6TmZfXIgald4MOCF++rcMkLg72+0fqp54wqucEQuyOqyxA4O3htf3Surq3UhP4En6+jVXBgQ0MW7ZzM/cgEdgwgQepU9LTdb/0xfoBjCs5lHDJez/AqU7Vtwj7erhnk3Z4me5f3wyq4/hqewBqzSevA=="',
// 'string(26) "{"name":"fjw","sex":"男"}"',
// '===私钥加密 + 私钥解密======================================',
// 'string(344) "bAOujzXYjQ8jw/Vdg8mJCs51D5WB8BOntaYlGy56GLRZQ2Rh8vNtWV39eJyh+zjZpX9gv7iUd+IqAit1klK2r5AUAuPiJbZFFj4SVT+IZkK4fnBk4MfVz9KuOffwU5tly/JdiS4hde86qZIrUwl3h90P3rMXF9Cc8mwY2DuRcAEFFKcNndjKfoLK40h2rwDrlHDN9kS10xSboYD8jF1v0VKo/L8nl5MAon3Z3V1qIX9GrPG5X1bbCy3/Z1azRGK4/+aU+9rZabsVggYhk8WdIoeSzoi4IYt41XXDV3GFDkKv5+mSzE7/3BRpNNeoiCumA5GCpdhSyGZVSEmk+1B7lQ=="',
// 'bool(false)',
// '===公钥加密 + 公钥解密======================================',
// 'string(344) "knftICJPpB9wou6FxsbG+y9pHeeMnuCDojxeV+vD2MylSlURqLR4tNnCXuf6eKoBIp/KMB7H0Zih32Fd1glNqiTk3d3iaEmyxtFmeYKz9erx5S8lZnVX5gtpZ4xw2xUe06m9CbjIFwXvA7YkBDJBThXRpfn0AV9G8b9MO43jRj4697Nh+V2hJXagagS50Td7HluAxGyFvvZj20xckFNfrgB+p/NlPzCn7csqcobesYvO3IG8rTboA7cJSfcIsP6HjLFwGD8nhq36ycy09e1Jj+odj4taWDt7BjPj4rrPlXkNgx5YY4zGoc87Eb8H1Bj1YXrh8xFqsGT8EK6N/XaI8Q=="',
// 'bool(false)',

//         ];
        // 私钥加密 + 公钥解密
        echo "===私钥加密 + 公钥解密======================================<br>";
        $encrypt = rsa_encrypt($param, 'pri');
        dump($encrypt);
        $decrypt = rsa_decrypt($encrypt, 'pub');
        dump($decrypt); 

        // 公钥加密 + 私钥解密
        echo "===公钥加密 + 私钥解密======================================<br>";
        $encrypt = rsa_encrypt($param, 'pub');
        dump($encrypt);
        $decrypt = rsa_decrypt($encrypt, 'pri');
        dump($decrypt); 

        // 私钥加密 + 私钥解密
        echo "===私钥加密 + 私钥解密======================================<br>";
        $encrypt = rsa_encrypt($param, 'pri');
        dump($encrypt);
        $decrypt = rsa_decrypt($encrypt, 'pri');
        dump($decrypt); 

        // 公钥加密 + 公钥解密
        echo "===公钥加密 + 公钥解密======================================<br>";
        $encrypt = rsa_encrypt($param, 'pub');
        dump($encrypt);
        $decrypt = rsa_decrypt($encrypt, 'pub');
        dump($decrypt); 


    }

    public function test(){
        $param = [
            'username'=>'admin',
            'password'=>'admin'
        ];
        //公钥加密
        $header = ['accesstoken'=>'8f26e27c4d2f15e32f81d50436ca19df'];
        $pub_encrypt = rsa_encrypt($param, 'pub');
        // dump($pub_encrypt); die;
        $url = 'http://mamitianshi.server/api.php/common/member_login';
        $response = httpsPost($url, json_encode(['data'=>$pub_encrypt], 320), json_encode($header, 320));
        dump($response); die;

    }

    public function fqm(){
        
        $pub_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsSULHKENMIs8FrLzuCCKJMrEZwGI6VLrBfwjfwm2P5BfsXuOyzfLnomJ1hV\/COnoMK+407U6N+WeMWPo3kyE\/2VoC3fpzkfnkCrWChBWuSbTw1tJWU+zC7BsPOV4+DKoIf5zeVV+X+hQYVaiIYVplSKfftb7hOyavfL0lNmLJJN55PI0hkOac179c8pUoCXfM8ju6saOgxGPrgE9WhOaMZvDJlZGmyoZytj\/AwSNyn\/HJth8ZqFseGoQwcxsegZDtJfaK6uWoBHYDGcNvVvHBNcpKuo\/6eR6FRSw5kcPoKKDQnu3NQf6aGBiMV\/0rXlk+n7xlJXSXg8iqg0Zp4oAswIDAQAB';
        $encrypt = 'Ms13Jnh8xy1GJz76QSeVLJpd0K1IhYKEy0w9qxi0sPCY9Dt4DlRNOkmY7Xg0Xvu+Gw5vPn9H\/BZIhFhYQhvZh6Y8hPoaF3KJcDRffOZMlowhjIsX7BvNI1S93uIOF1u5NoI6LOm4q3NsXwkKe80H2vzpv+vvEUqgnhvPjw2ZABmDyTAQDMPOCm9KDk4GtPRx+BxZvOAhdRBrtkricotTVGsd1Max5y2iaGHUNY1qHB1DOZPrvvGcVLNH\/RnaR6ZOJM83PRVPviwGMKCa2C7NOa8obttJRIfWh1Z2p8E6FKHWpu437WmAl18M3vvWobtALYJQlwEu3rbEm02cIyRZ+w==';
        
        $decrypt = rsa_decrypt($encrypt, 'pub');
        dump($decrypt); die;
    }
    
}
