<?php
/*
 * @Descripttion: Vaccine 疫苗api逻辑层
 * @Author: fqm
 * @Date: 2019-08-20 14:28:13
 */

namespace app\api\logic;

class Vaccine extends ApiBase
{

    /**
     * 获取疫苗列表
     */
    public function get_vaccine_list($param = [])
    {
        $where = [
            'status' => 1,
        ];

        $field = 'id, code_abbr, code_full, zh_name_abbr, zh_name_full, en_name_abbr, en_name_full, code_factory_abbr, code_factory_full, zh_factory_name_full, en_factory_name_full, ch_gnj, measure_abbr, measure_code, measure_num, measure_unit, cate_nation_code, inject_date, inject_week, vaccine_text, affect, introduce, status, category';

        $oeder = 'id desc';

        !empty($param['list_rows']) ? $paginate = $param['list_rows'] : $paginate = 15;

        return $this->modelMVaccine->getList($where, $field, $oeder, $paginate);
    }

    /**
     * 获取疫苗详情
     */
    public function get_vaccine_info($param = [])
    {
        $where = ['id'=>$param['id']];

        $field = 'id, code_abbr, code_full, zh_name_abbr, zh_name_full, en_name_abbr, en_name_full, code_factory_abbr, code_factory_full, zh_factory_name_full, en_factory_name_full, ch_gnj, measure_abbr, measure_code, measure_num, measure_unit, cate_nation_code, inject_date, inject_week, vaccine_text, affect, introduce, status, category';

        return $this->modelMVaccine->getInfo($where, $field);

    }
}