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
     * 查询一条登记记录并且叫号
     */
    public function getWaitingInfo()
    {
        
        $this->assign('info',$this->logicVaccinations->getWaitingInfo($this->param));

        return $this->fetch('waiting_info');

    }


    /**
     * 修改登记资料信息
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

        !empty($data['search_data']) && $where['Number'] = ['like', '%'.trim($data['search_data']).'%'];

        $paginate = !empty($data['page']) ? $data['page'] : 1;
        $limit = !empty($data['limit']) ? $data['limit'] : 15;

        $field = 'v.Id, v.Number, v.ChildId, v.ConsultationRoom, v.RegistrationFinishTime, c.Name as child_name';

        $order = 'v.RegistrationFinishTime asc';

        return $this->logicVaccinations->getInoculationList($where, $field, $order, $paginate);

    }

    
    /**
     * 查询一条接种记录并叫号
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

        !empty($data['search_data']) && $where['v.Number|c.Name'] = ['like', '%'.trim($data['search_data']).'%'];

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
            'v.State' => ['>=', 2],
        ];

        $data = $this->param;

        !empty($data['search_data']) && $where['v.Number|c.Name|c.CardNo'] = ['like', '%'.trim($data['search_data']).'%'];

        $paginate = 15;

        !empty($data['limit']) && $paginate = $data['limit'];

        $field = 'v.Id, v.Number, v.ChildId, v.ConsultationRoom, v.VaccinationFinishTime,v.State, c.Name as child_name,c.CardNo';

        $order = 'v.VaccinationFinishTime asc';

        return $this->logicVaccinations->getFinishedList($where, $field, $order, $paginate);
        
    }

    /**
     * 查看留观/完成队列一条记录
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
     * 设置接种完成
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

    /**
     * 接种台叫号
     */
    public function callInjectNumber()
    {
        return $this->logicVaccinations->callInjectNumber($this->param);
    }

    /**
     * 上一位
     */
    public function prevNumber()
    {
        return $this->logicVaccinations->prevNumber($this->param);
    }

    /**
     * 下一位
     */
    public function nextNumber()
    {
        return $this->logicVaccinations->nextNumber($this->param);
    }

    /**
     * 选择登记台后进行缓存
     * 
     */
    public function setCacheWritingDesk()
    {
        $data = $this->param;

        cache('WritingDesk',$data['WritingDesk'],43200);

        $result = $this->logicVaccinations->callNumber(['number'=>$data['number'],'WritingDesk'=>$data['WritingDesk']]);

        return $result;

    }

    /**
     * 选择接种台后进行缓存
     */
    public function setCacheVaccinationDesk()
    {
        $data = $this->param;

        cache('VaccinationDesk',$data['WritingDesk'],43200);

        $result = $this->logicVaccinations->callInjectNumber(['Number'=>$data['Number'],'Name'=>$data['Name'],'WritingDesk'=>$data['WritingDesk']]);

        return $result;
    }
    



}