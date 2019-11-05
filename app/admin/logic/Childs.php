<?php
/**
 * Childs 孩子逻辑层
 * by fqm in 19.9.30
 */

namespace app\admin\logic;

class Childs extends AdminBase
{

    /**
     * 获取孩子列表
     */
    public function getChildsList($where = [], $field = true, $order = 'Id desc', $paginate = 15)
    {

        $childsList = $this->modelChilds->getList($where, $field, $order, $paginate);
        
        return $childsList;
    }


    /**
     * 根据卡号获取孩子信息
     */
    public function cardNoGetInfo($param = [])
    {

        $where = [
            'CardNo' => $param['CardNo'],
        ];

        $field = 'Id, ParentName, Name, Mobile, BirthDate, Sex, Address';

        $childInfo = $this->modelChilds->getInfo($where, $field);

        if($childInfo){
            return $childInfo;
        }else{
            return ['code'=>0,'msg'=>'请填写儿童信息'];
        }

        
    }

    /**
     * 获取孩子信息
     */
    public function getChildsInfo($param = [])
    {

        $where = [];

        !empty($param['Id']) ? $where['Id'] = $param['Id']:'';

        $field = 'Id,Name,CardNo,Sex,BirthDate,ParentName,Address,Mobile';

        return $this->modelChilds->getInfo($where,$field);
    }

    /**
     * 修改孩子信息
     */
    public function editChildsInfo($param = [])
    {
        
        $validate_result = $this->validateChilds->scene('add')->check($param);
        
        if (!$validate_result) {
            
            return ['code'=>400, 'msg'=>$this->validateChilds->getError()];
        }

        $IsCardNo = $this->modelChilds->getInfo(['CardNo'=>$param['CardNo']]);

        if($IsCardNo){
            $param['Id'] = $IsCardNo['Id'];
        }

        $time = date("Y-m-d H:i:s");

        $data = [
            'Id'=>empty($param['Id']) ? 0 : $param['Id'],
            'Name'=>$param['Name'],
            'Sex'=>$param['Sex'],
            'CardNo'=>$param['CardNo'],
            'BirthDate'=>$param['BirthDate'],
            'ParentName'=>$param['ParentName'],
            'Address'=>$param['Address'],
            'Mobile'=>$param['Mobile'],
            'LastModificationTime'=>$time,
            'LastModifierUserId'=>MEMBER_ID,
        ];

        if(empty($data['Id'])){
            $data['CreationTime'] = $time;
            $data['CreatorUserId'] = MEMBER_ID;
            $data['IsDeleted'] = 0;
            $data['No'] = '';
            
        }

        $url = url('childsList');

        $result = $this->modelChilds->setInfo($data);

        $childId = 0;

        if(empty($data['Id'])){
            $childId = $result;
        }else{
            $childId = $data['Id'];
        }

        return $result ? ['code'=>200, 'msg'=>'操作成功', 'childId'=>$childId] : ['code'=>400, 'msg'=>$this->modelChilds->getError()];
        
        // return $result ? [RESULT_SUCCESS, '操作成功', $url, $childId] : [RESULT_ERROR, $this->modelChilds->getError()];
    }

    /**
     * 删除孩子
     */
    public function delChilds($param = [])
    {

        $time = date("Y-m-d H:i:s");

        $data = [
            'IsDeleted' => 1,
            'DeleterUserId' => MEMBER_ID,
            'DeletionTime' => $time,
            'LastModificationTime' => $time,
            'LastModifierUserId' => MEMBER_ID,
        ];

        $result = $this->modelChilds->updateInfo(['Id'=>$param['Id']],$data);

        return $result ? ['code'=>200, 'msg'=>'操作成功'] : ['code'=>400, 'msg'=>$this->modelChilds->getError()];
        
        // return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $this->modelChilds->getError()];

    }







}