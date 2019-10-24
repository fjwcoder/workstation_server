<?php
/*
 * @Descripttion: Keepalive 逻辑层
 * @Author: fqm
 * @Date: 2019-09-02 09:39:08
 */

namespace app\api\logic;
use app\api\error\Common as CommonError;
use think\Db;

class Keepalive extends ApiBase
{

    /**
     * 冰箱保活接口,规定心跳,向服务器发送 当前冰箱unique_code,时间戳,温度
     * by fqm in 19.9.2 修改键名，优化添加/修改数据时减少重复对不必要字段的操作
     */
    public function fridgeHeartBeat($param = [])
    {

        $unique_code = rsa_decrypt($param['uc'],'pub');

        $fridge_info = $this->modelMPositionFridge->getInfo(['unique_code'=>$unique_code], 'id,position_id');

        // 当天日期
        $date = date("Y-m-d");

        $where = [
            'unique_code' => $unique_code,
            'current_date' => $date,
        ];
        $heart_beat_info = $this->modelFridgeTemperature->getInfo($where);

        // 查询保活数据 并 加入最新的时间和温度
        $temp_list = !empty($heart_beat_info['temp_list']) ? json_decode($heart_beat_info['temp_list'], true) : [];

        $temp_list[] = [$param['time'],$param['temp']];

        $temp_list = json_encode($temp_list);

        $data = [
            'id' => !empty($heart_beat_info['id']) ? $heart_beat_info['id'] : 0,
            'last_time' => $param['time'], // 最后操作时间
            'temp_list' => $temp_list, // 保活数据
        ];
        
        if(empty($heart_beat_info['id'])){
            $data['current_date'] = !empty($heart_beat_info['current_date']) ? $heart_beat_info['current_date'] : $date; // 当天时间
            $data['position_id'] = $fridge_info['position_id'];// 接种点id
            $data['fridge_id'] = $fridge_info['id'];// 冰箱id
            $data['unique_code'] = $unique_code;// 冰箱唯一码
        }

        $result = $this->modelFridgeTemperature->setInfo($data);

        return $result ? true : CommonError::$editFailed;

    }

}
