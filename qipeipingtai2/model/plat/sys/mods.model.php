<?php
/**
 * 模块管理模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/5
 * Time: 9:27
 */
class PlatSysModsModel extends Model{


    /**
     * 获取权限列表
     * @param $page
     * @param $pageSize
     * @param $status
     * @param $modName
     * @param $keywords
     * @return array
     */
    public function getModsList($page,$pageSize,$status,$modName,$keywords){


        //初始化总条数
        $count = 0;
        //起始条数
        $pages = ($page-1)* $pageSize;

        $find = 'id>0';

        if($status){//权限状态
            $statusA = $status==1?1:0;
            $find   .= ' and isMenu ='.$statusA;
        }

        if($modName){//模块名称
            $find  .= " and modName ='$modName'";
        }

        if($keywords){//关键字
            $findKey = '"%'.$keywords.'%"';
            $find   .= " and (`modName` like $findKey or `funName` like $findKey )";
        }

        $field = 'modName,funName,isMenu,sort,funIco,id';

        $count = $this->table('core_auth')->where($find)->count();
        $lists = $this->table('core_auth')->field($field)->where($find)
            ->order(array('id'=>'asc','sort'=>'asc'))->limit($pages,$pageSize)->get();

        if($lists){
            //搜索条件
            $search  = array('status'=>$status,'keywords'=>$keywords);
            $data    = array('list'=>$lists,'search'=>$search,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的模块');
        }

        return $data;


    }


    /**
     * 获取模块名称
     */
    public function getModsName(){
        $result = $this->table('core_auth')->field('modName')->group('modName')->get();
        return $result;
    }

    /**
     * 获取模块
     * @param $funId
     * @return mixed
     */
    public function getOneMods($funId){
        $result['status'] = 0;
        $result['data'] = array();
        $data = $this->table('core_auth')->field('modName,funName,sort,funIco,id,isMenu')->where("id=$funId")->getOne();
        if($data){
            $result['status'] = 1;
            $result['data']   = $data;
        }
        return $result;
    }


    /**
     * 模块菜单显示
     * @param $funId
     * @param $isMenu
     * @return int
     */
    public function changeStatus($funId,$isMenu){
        $status = 0;
        $data = array();
        $data['isMenu'] = $isMenu;
        $result = $this->table('core_auth')->where(array('id'=>$funId))->update($data);
        // dump($this->lastSql());
        if($result) { $status=1; }
        return $status;
    }

    /**
     * 模块信息编辑
     * @param $funId
     * @param $field
     * @param $key
     * @return int
     */
    public function saveModInfo($funId,$field,$key){
        $status = 1;
        $data = array();
        $data[$field] = $key;
        $result = $this->table('core_auth')->where(array('id'=>$funId))->update($data);
        // dump($this->lastSql());
        if($result) { $status=1; }
        return $status;
    }


}