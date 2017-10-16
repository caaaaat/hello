<?php

/**
 * 操作日志模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/11
 * Time: 10:48
 */
class ActionLogModel extends Model
{

    public function getLogs($time1,$time2,$keywords,$page,$pageSize){
        //起始条数
        $pages = ($page-1)* $pageSize;

        $find = 'id<>0';

        if($time1){//时间段
            $find  .= ' and time >"'.$time1.'"';
        }
        if($time2){//时间段
            $find  .= ' and ( time <"'.$time2.'" or time like "'.$time2.'%")';
        }

        if($keywords){//关键字
            $findKey = '"%'.$keywords.'%"';
            $find   .= " and (`code` like $findKey or `user` like $findKey  or `action` like $findKey )";
        }

        $field      = 'user,code,action,time,ip';

        $count      = $this->table('su_action_log')->where($find)->count();
        $lists      = $this->table('su_action_log')->field($field)->where($find)->order(array('time'=>'desc'))->limit($pages,$pageSize)->get();
        //writeLog($this->lastSql());

        if($lists){
            //搜索条件
            $search  = array('status'=>$lists,'keywords'=>$keywords);
            $data    = array('list'=>$lists,'search'=>$search,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的操作日志');
        }

        return $data;
    }



    /**
     * 记录操作日志
     * @param $userId 操作人id
     * @param $name   操作人名称
     * @param $code   操作人帐号
     * @param $action 操作
     * @param string $result 结果
     */
    public function actionLog($userId,$name,$code,$action,$result = '成功'){
        //记录日志
        $ip   = getIp() ;
        $time = date('Y-m-d H:i:s',time()) ;
        $logArr = array('userId'=>$userId,'user'=>$name,'code'=>$code,'action'=>$action,'result'=>$result,'time'=>$time,'ip'=>$ip);
        $this->table('su_action_log')->insert($logArr);
    }

}