<?php
/*
 * @Descripttion: Injectposition逻辑层
 * @Author: fqm
 * @Date: 2019-08-19 15:38:10
 */

namespace app\index\logic;
use think\Db;

class Injectposition extends IndexBase
{

    /**
     * 注册
     */
    public function regist($param = [])
    {
        $validate_result = $this->validateInjectPosition->check($param);
        
        if(!$validate_result){
            return [RESULT_ERROR, $this->validateInjectPosition->getError()];
        }

        $url = url('detail');
        $param['status'] = 3;
        $result = $this->modelMInjectPosition->setInfo($param);

        return $result ? [RESULT_SUCCESS, '注册成功，等待审核', $url] : [RESULT_ERROR, $this->modelMInjectPosition->getError()];
        
    }

    /**
     * 检查账号是否存在
     */
    public function check_account($account)
    {
        $where = [
            'account'=>$account,
        ];
        $data = Db::name('m_inject_position')->where($where)->find();
        if($data){
            return ['code'=>1,'msg'=>'账号已存在，请重新输入账号'];
        }else{
            return ['code'=>0,'msg'=>'账号可用'];
        }
    }

    /**
     * 查看接种点信息
     */
    public function detail($param)
    {
        $where = [
            'account'=>$param['account'],
        ];
        $inject_info = $this->modelMInjectPosition->getInfo($where);

        // 不存在账号
        if(!$inject_info){
            return [RESULT_ERROR, '账号或密码不正确'];  
        }
        // 账号的密码不对应
        if($param['password'] !== $inject_info['password']){
            return [RESULT_ERROR, '账号或密码不正确'];
        }

        return $inject_info;

    }

}
