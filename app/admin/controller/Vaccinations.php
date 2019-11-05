<?php
/**
 * Vaccinations 接种流水控制器
 */

namespace app\admin\controller;

class Vaccinations extends AdminBase
{


    /**
     * 登记队列
     */
    public function writingList()
    {
        // $where = [];

        // $data = $this->param;

        // !empty($data['search_data']) && $where['Number'] = ['like', '%'.$data['search_data'].'%'];

        // $this->assign('list',$this->logicVaccinations->writingList($where));

        return $this->fetch();
    }

    /**
     * 获取登记队列
     */
    public function getWaitingList()
    {
        $where = [];

        $data = $this->param;

        !empty($data['search_data']) && $where['Number'] = ['like', '%'.$data['search_data'].'%'];

        $paginate = !empty($data['page']) ? $data['page'] : 1;
        $limit = !empty($data['limit']) ? $data['limit'] : 15;

        // !empty($data['limit']) && $paginate = $data['limit'];

        $field = 'Id, VaccinationDate, Number';

        $order = 'VaccinationDate asc';

        return $this->logicVaccinations->getWaitingList($where, $field, $order, $paginate, $limit);
    }

    
    /**
     * 获取登记队列某一条的信息进行登记
     */
    public function getWaitingInfo()
    {
        
        $this->assign('info',$this->logicVaccinations->getWaitingInfo($this->param));

        return $this->fetch('waiting_info');

    }


    /**
     * 登记资料时，修改登记的信息
     */
    public function setVaccinationsInfo()
    {
        return $this->logicVaccinations->setVaccinationsInfo($this->param);
    }


    /**
     * 接种队列
     */
    public function inoculationList()
    {
        // $where = [];
        // $this->assign('list',$this->logicVaccinations->inoculationList($where));

        return $this->fetch();
    }

    /**
     * 获取接种队列
     */
    public function getInoculationList()
    {
        $where = [];

        $data = $this->param;

        !empty($data['search_data']) && $where['Number'] = ['like', '%'.$data['search_data'].'%'];

        $paginate = 15;

        !empty($data['limit']) && $paginate = $data['limit'];

        $field = 'v.Id, v.Number, v.ChildId, v.ConsultationRoom, v.RegistrationFinishTime, c.Name as child_name';

        $order = 'v.RegistrationFinishTime asc';

        return $this->logicVaccinations->getInoculationList($where, $field, $order, $paginate);

    }

    
    /**
     * 获取接种队列中某一条的信息
     */
    public function getInoculationInfo()
    {
        $this->assign('info',$this->logicVaccinations->getInoculationInfo($this->param));

        return $this->fetch('inoculation_info');
    }


    /**
     * 留观队列
     */
    public function observerList()
    {
        $where = [];

        $data = $this->param;

        !empty($data['search_data']) && $where['Number'] = ['like', '%'.$data['search_data'].'%'];

        $this->assign('list',$this->logicVaccinations->observationList($where));

        return $this->fetch();

    }


    /**
     * 完成队列
     */
    public function finishedList()
    {
        // $where = [];

        // $data = $this->param;

        // !empty($data['search_data']) && $where['Number'] = ['like', '%'.$data['search_data'].'%'];

        // $this->assign('list',$this->logicVaccinations->finishedList($where));

        return $this->fetch();
    } 

    /**
     * 获取完成队列
     */
    public function getFinishedList()
    {
        $where = [
            'v.RegistrationFinishTime' => ['like', '%'.NOW_DATE.'%'],
        ];

        $data = $this->param;

        !empty($data['search_data']) && $where['c.Name|c.CardNo'] = ['like', '%'.$data['search_data'].'%'];

        $paginate = 15;

        !empty($data['limit']) && $paginate = $data['limit'];

        $field = 'v.Id, v.Number, v.ChildId, v.ConsultationRoom, v.VaccinationFinishTime,v.State, c.Name as child_name,c.CardNo';

        $order = 'v.VaccinationFinishTime asc';

        return $this->logicVaccinations->getFinishedList($where, $field, $order, $paginate);
        
    }

    /**
     * 查看接种完成的孩子资料
     */
    public function checkInVaInfo()
    {

        $data = $this->param;

        $where = [];

        !empty($data['Id']) && $where['v.Id'] = $data['Id'];

        !empty($data['search_data']) && $where['v.Number'] = ['like', '%'.$data['search_data'].'%'];

        $where_np = [
            'VaccinationDate' => ['like', '%'.NOW_DATE.'%'],
            'State'=>['in','2,3,4']
        ];

        $this->assign('info',$this->logicVaccinations->checkInVaInfo($where, $where_np));

        return $this->fetch();
    }


    /**
     * 接种疫苗完成
     */
    public function setInjectVaccineComplete()
    {
        return $this->logicVaccinations->setInjectVaccineComplete($this->param);
    }


    /**
     * 登记台叫号
     */
    public function callNumber()
    {
        return $this->logicVaccinations->callNumber($this->param);
    }
    



}