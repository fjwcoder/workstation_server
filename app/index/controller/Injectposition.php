<?php
/*
 * @Descripttion: Injectposition控制器
 * @Author: fqm
 * @Date: 2019-08-19 13:11:50
 */

namespace app\index\controller;

class Injectposition extends IndexBase
{

    /**
     * 注册
     */
    public function regist()
    {
        IS_POST && $this->jump($this->logicInjectposition->regist($this->param));
        
        $pid = 0;
        
        $this->assign('city',$this->logicRegion->get_city($pid));

        return $this->fetch('regist');
    }

    /**
     * 获取城市
     */
    public function get_city($pid)
    {
        return $this->logicRegion->get_city($pid);
    }

    /**
     * 检查账号是否存在
     */
    public function check_account($account)
    {
        return $this->logicInjectposition->check_account($account);
    }

    /**
     * 查看接种点信息
     */
    public function detail()
    {

        if(request()->isPost()){
            $res = $this->logicInjectposition->detail($this->param);
            empty($res['account']) ? $this->jump($res) : $this->assign('inject_position_info',$res);
        }
        
        return $this->fetch('detail');
    }

    
    



}



