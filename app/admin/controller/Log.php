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

namespace app\admin\controller;

/**
 * 行为日志控制器
 */
class Log extends AdminBase
{
    
    /**
     * 日志列表
     */
    public function logList()
    {
        
        $this->assign('list', $this->logicLog->getLogList());
        
        return $this->fetch('log_list');
    }
  
    /**
     * 日志删除
     */
    public function logDel($id = 0)
    {
        
        $this->jump($this->logicLog->logDel(['id' => $id]));
    }
  
    /**
     * 日志清空
     */
    public function logClean()
    {
        
        $this->jump($this->logicLog->logDel([DATA_STATUS_NAME => DATA_NORMAL]));
    }





    /**
     * 用户访问日志
     */
    public function accessLog()
    {

        $where = [
            'CreationTime' => ['like', '%'.NOW_DATE.'%'],
        ];

        if(MEMBER_ID == SYS_ADMINISTRATOR_ID){
            $data = $this->param;
            !empty($data['search_data']) && $where['UserNameOrEmailAddress'] = ['like', '%'.$data['search_data'].'%'];
        }else{
            $where['UserId'] = MEMBER_ID;
        }

        $field = 'Id, UserId, UserNameOrEmailAddress, ClientIpAddress, CreationTime';

        // return $this->logicUserloginattempts->getLogList($where, $field, 'CreationTime desc', 15);

        $this->assign('list',$this->logicUserloginattempts->getLogList($where, $field, 'CreationTime desc', 15));

        return $this->fetch();

    }


    /**
     * 接种日志
     */
    public function vaccinationhistory()
    {

        return $this->fetch();

    }

    /**
     * 获取接种日志
     */
    public function getVaccinationhistory()
    {

        $where = [];

        $data = $this->param;

        !empty($data['search_data']) && $where['c.Name|c.CardNo'] = ['like', '%'.$data['search_data'].'%'];

        $paginate = 15;

        !empty($data['limit']) && $paginate = $data['limit'];

        $field = 'v.Id, v.Number, v.ChildId, v.ConsultationRoom, v.VaccinationFinishTime, v.VaccinationDate, v.State, c.Name as child_name,c.CardNo';

        $where_np = [];

        return $this->logicVaccinations->getFinishedList($where, $field, 'VaccinationDate desc', $paginate, $where_np);

    }

    /**
     * 查看资料
     */
    public function checkLogInfo()
    {
        $data = $this->param;

        $where = [];

        !empty($data['Id']) && $where['v.Id'] = $data['Id'];

        // !empty($data['search_data']) && $where['v.Number'] = ['like', '%'.$data['search_data'].'%'];

        $where_np = [];

        $this->assign('info',$this->logicVaccinations->checkInVaInfo($where, $where_np));

        return $this->fetch('vaccinations/checkinvainfo');
    }
    
}
