<?php
/**
 * Settings 系统设置逻辑层
 */

namespace app\admin\logic;
use think\Db;

class Settings extends AdminBase
{

    /**
     * 系统设置
     */
    public function index()
    {

        $where = [];

        $field = 'Name,Value';

        $settings = $this->modelSettings->getList($where,$field,'',false);

        $data = [];

        foreach ($settings as $k => $v) {
            $data[$v['Name']] = $v['Value'];
        }

        // dump($data);die;

        return $data;
    }

    /**
     * 修改设置
     */
    public function editSettings($param = [])
    {

        $today = date('Y-m-d H:i:s');

        $uId =  session('member_info')['id'];
        
        foreach ($param as $k => $v) {
            
            $name = str_replace('_','.',$k);

            $info = Db::name('settings')->where(['Name'=>$name])->find();

            if(empty($info)){
                $data = [
                    'CreationTime'=>$today,
                    'CreatorUserId'=>$uId,
                    'LastModificationTime'=>$today,
                    'LastModifierUserId'=>$uId,
                    'Name'=>$name,
                    'Value'=>$v,
                ];
                Db::name('settings')->insert($data);
            }else{
                $data = [
                    'LastModificationTime'=>$today,
                    'LastModifierUserId'=>$uId,
                    'Value'=>$v,
                ];
                Db::name('settings')->where(['Name'=>$name])->update($data);
            }
            
        }

        return [RESULT_SUCCESS, '操作成功'];
    }

    /**
     * 在冰箱后台注册接种点
     */
    public function setSettingsInfo($param = [])
    {

        $validate_result = $this->validateSettings->scene('add')->check($param);
        
        if (!$validate_result) {
            
            return ['code'=>400, 'msg'=>$this->validateSettings->getError()];
        }

        $refrigeratorUrl = $this->modelSettings->getValue(['Name'=>'App.refrigeratorUrl'], 'Value');

        $data = [
            'id'=>$param['inject_position_id'],
            'account' => $param['account'],
            'password' => $param['password'],
            'inject_position_type' => $param['inject_position_type'],
            'province' => $param['province'],
            'city' => $param['city'],
            'district' => $param['district'],
            'address' => $param['address'],
            'phone' => $param['phone'],
            'name' => $param['name'],
            'province_city_district' => $param['province_city_district'],
        ];

        $result = httpsPost($refrigeratorUrl . '/registinpo', $data);

        // $result = json_decode($result,true);

        if(!empty($result)){
            $result = json_decode($result,true);
        }else{
            return ['code'=>400,'msg'=>'操作失败'];
        }

        if(!empty($result) && $result['code'] == 200){

            if($data['id'] == 0){
                return ['code'=>200,'msg'=>$result['data']];
            }else{
                return ['code'=>200,'msg'=>'success'];
            }
            
        }else{
            
            return ['code'=>400,'msg'=>$result['msg']];
        }

    }



}