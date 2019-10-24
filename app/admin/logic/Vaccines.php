<?php
/**
 * Vaccines 疫苗逻辑层
 */

namespace app\admin\logic;

class Vaccines extends AdminBase
{


    /**
     * 获疫苗列表
     */
    public function getVaccinesList($where = [], $field = true, $order = 'Id desc', $paginate = 15)
    {   

        return $this->modelVaccines->getList($where, $field, $order, $paginate);

    }

    /**
     * 删除疫苗
     */
    public function delVaccine($param = [])
    {

        $data = [
            'IsDeleted' => 1,
            'DeleterUserId' => session('member_info')['id'],
            'DeletionTime' => date("Y-m-d h:i:s"),
        ];
        
        $result = $this->modelVaccines->updateInfo(['Id'=>$param['Id']],$data);

        return $result ? ['code'=>200, 'msg'=>'操作成功'] :['code'=>400, 'msg'=>'操作失败'];

        // return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $this->modelVaccines->getError()];
    }

    /**
     * 获取疫苗信息
     */
    public function getVaccineInfo($param = [])
    {
        $field = 'Id, FullName, ShortName, EShortName, Times, Property, CountryCode, IsFree';

        return $this->modelVaccines->getInfo(['Id'=>$param['Id']],$field);
    }

    /**
     * 添加/编辑疫苗
     */
    public function editVaccineInfo($param = [])
    {
    
        if(empty($param['Id'])){
            
            $param['CreationTime'] = date("Y-m-d h:i:s");
            $param['IsDeleted'] = 0;
        }else{
            $param['LastModificationTime'] = date("Y-m-d h:i:s");
        }

        $result = $this->modelVaccines->setInfo($param);

        return $result ? ['code'=>200, 'msg'=>'操作成功'] :['code'=>400, 'msg'=>'操作失败'];
    }


}