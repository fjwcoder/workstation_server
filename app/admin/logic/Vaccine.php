<?php
/*
 * @Descripttion: Vaccine逻辑层
 * @Author: fqm
 * @Date: 2019-08-20 11:18:41
 */

namespace app\admin\logic;

class Vaccine extends AdminBase
{

    /**
     * 获取疫苗列表
     */
    public function getVaccineList($where = [])
    {

        $field = true;
        $order = 'id desc';

        return $this->modelMVaccine->getList($where, $field, $order, 10);

    }

    /**
     * 获取单条疫苗信息
     */
    public function get_vaccine_info($id)
    {
        return $this->modelMVaccine->getInfo(['id'=>$id]);
    }

    /**
     * 疫苗信息 编辑 / 添加
     */
    public function vaccineEdit($param = [])
    {

        $validate_result = $this->validateVaccine->check($param);
        
        if (!$validate_result) {
         
            return [RESULT_ERROR, $this->validateVaccine->getError()];
        }

        $url = url('vaccineList');

        $result = $this->modelMVaccine->setInfo($param);
        
        if($param['id'] == 0 ){
            $text = '新增';
        }else{
            $text = '编辑';
        }

        $result && action_log($text, $text.'疫苗，name：' . $param['zh_name_abbr']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelMVaccine->getError()];

    }

    /**
     * 获取疫苗列表搜索条件
     */
    public function getWhere($param = [])
    {
        $where = [];
        
        !empty($param['search_data']) && $where['code_abbr|code_full|zh_name_abbr|zh_name_full|en_name_abbr|en_name_full'] = ['like', '%'.$param['search_data'].'%'];
        
        return $where;
    }
 




}
