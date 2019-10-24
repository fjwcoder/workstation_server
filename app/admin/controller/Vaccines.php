<?php
/**
 * Vaccines 疫苗控制器
 */

namespace app\admin\controller;

class Vaccines extends AdminBase
{



    /**
     * 疫苗列表
     */
    public function vaccinesList()
    {

        // $where = [];
        
        // !empty($this->param['search_data']) && $where['ShortName'] = ['like', '%'.(string)$this->param['search_data'].'%'];

        // $this->assign('list',$this->logicVaccines->vaccinesList($where));

        return $this->fetch();
    }

    /**
     * 获取疫苗列表
     */
    public function getVaccinesList()
    {

        $data = $this->param;

        $where = [
            'IsDeleted' => 0,
        ];
        
        !empty($data['search_data']) && $where['ShortName'] = ['like', '%'.(string)$data['search_data'].'%'];

        $paginate = 15;

        !empty($data['limit']) && $paginate = $data['limit'];

        $field = 'Id, FullName, ShortName, EShortName, Times, Property, CountryCode, Category, IsFree';

        $order = 'Id desc';

        return $this->logicVaccines->getVaccinesList($where, $field, $order, $paginate);


    }

    /**
     * 删除疫苗
     */
    public function delVaccine()
    {
        // $this->jump($this->logicVaccines->delVaccine($this->param));
        return $this->logicVaccines->delVaccine($this->param);
    }

    /**
     * 获取疫苗信息
     */
    public function getVaccineInfo()
    {
        return $this->logicVaccines->getVaccineInfo($this->param);
    }

    /**
     * 编辑添加疫苗
     */
    public function editVaccineInfo()
    {
        return $this->logicVaccines->editVaccineInfo($this->param);
    }




}