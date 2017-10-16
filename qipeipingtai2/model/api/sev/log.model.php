<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5
 * Time: 10:33
 */
class ApiSevLogModel extends Model
{
    //来访记录写入                                      1pc_web 2移动端
    public function visitToLog($myFirmId,$toFirmId,$visit_type=1){
        $res = $this->table('firms_visit_log')->insert(array('firms_id'=>$myFirmId,'to_firms_id'=>$toFirmId,'visit_type'=>$visit_type,'is_show'=>1,'create_time'=>date('Y-m-d H:i:s')));
        return $res;
    }
    //拨打记录写入                                   1电话· 2QQ
    public function callToLog($myFirmId,$toFirmId,$call_type=1,$userType=1,$plat=1){
        if($userType==1){
            $res = $this->table('firms_call_log')->insert(array('firms_id'=>$myFirmId,'to_firms_id'=>$toFirmId,'call_type'=>$call_type,'create_time'=>date('Y-m-d H:i:s')));
        }else{
            $res = $this->table('sales_call_log')->insert(array('sales_user_id'=>$myFirmId,'firms_id'=>$toFirmId,'visit_type'=>$plat,'call_type'=>$call_type,'create_time'=>date('Y-m-d H:i:s'),'is_show'=>1));
        }

        return $res;
    }

    //邀请记录
    public function getInviteLog($firmID,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('invite_log as a') ->where(array('a.type'=>1,'a.fu_id'=>$firmID))->count();
        $res = $this->table('invite_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.head_pic,b.face_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,a.create_time as invitime')
            ->where(array('a.type'=>1,'a.fu_id'=>$firmID))
            ->jion('left join firms as b on a.firms_id=b.id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }

    //来电记录
    public function getFirmsToCallLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_call_log as a')->field('count(DISTINCT a.firms_id) as num') ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')->getOne();
        $res   = $this->table('firms_call_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.head_pic,b.face_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,max(a.create_time) as invitime,a.call_type')
            ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')
            ->jion('left join firms as b on a.firms_id=b.id')
            ->order('a.create_time desc')
            ->group('a.firms_id')
            ->limit($start,$pageSize)->get();

        //预处理数据
        foreach($res as $key=>$item){
            $res[$key]['isVip']    = 0;
            //判断vip是否过期
            if(strtotime($item['vip_time'])>time()){
                $res[$key]['isVip']    = 1;
            }


            if($item['linkPhone']){
                $linkPhone         = explode(',',$item['linkPhone']);
                $res[$key]['linkPhone'] = $linkPhone[0];
            }
        }
        $count = $count?$count:0;
        return array('list'=>$res,'count'=>$count['num'],'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }

    //来访记录
    public function getFirmsToVisitLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_visit_log as a')->field('count(DISTINCT a.firms_id) as num') ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')->getOne();
        $res = $this->table('firms_visit_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.head_pic,b.face_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,max(a.create_time) as invitime,a.visit_type')
            ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')
            ->jion('left join firms as b on a.firms_id=b.id')
            ->order('a.create_time desc')
            ->group('a.firms_id')
            ->limit($start,$pageSize)->get();
        //预处理数据
        foreach($res as $key=>$item){
            $res[$key]['isVip']    = 0;
            //判断vip是否过期
            if(strtotime($item['vip_time'])>time()){
                $res[$key]['isVip']    = 1;
            }

            if($item['linkPhone']){
                $linkPhone         = explode(',',$item['linkPhone']);
                $res[$key]['linkPhone'] = $linkPhone[0];
            }
        }
        $count = $count?$count:0;
        return array('list'=>$res,'count'=>$count['num'],'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }

    /**
     * 拨打记录
     * @param $myFirmId
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function getCallToFirmsLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_call_log as a')->field('count(DISTINCT to_firms_id) as num')->where(array('a.firms_id'=>$myFirmId,'is_show'=>1))->getOne();
        $res = $this->table('firms_call_log as a')
            ->field('count(a.to_firms_id) as num,a.to_firms_id,a.id as nid,b.classification,b.id,b.uname,b.type,b.EnterpriseID,b.companyname,b.major,b.head_pic,b.face_pic,b.wechat_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,max(a.create_time) as invitime,a.call_type')
            ->where(array('a.firms_id'=>$myFirmId,'a.is_show'=>1))
            ->jion('left join firms as b on a.to_firms_id=b.id')
            ->group('a.to_firms_id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();

        //预处理数据
        foreach($res as $key=>$item){
            $res[$key]['isVip']    = 0;
            $res[$key]['invitime'] = date("m-d H:i",strtotime($item['invitime']));

            //判断vip是否过期
            if(strtotime($item['vip_time'])>time()){
                $res[$key]['isVip']    = 1;
            }

        }

        return array('list'=>$res,'count'=>$count['num'],'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }




    /**
     * 拨打记录时间
     * @param $userId
     * @param $toFirmsId
     * @param $page
     * @param $pageSize
     * @return array
     */
    public function getCallToFirmsDateLog($userId,$toFirmsId,$page,$pageSize){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_call_log')->where(array('firms_id'=>$userId,'to_firms_id'=>$toFirmsId,'is_show'=>1))->count();
        $res = $this->table('firms_call_log')->where(array('firms_id'=>$userId,'to_firms_id'=>$toFirmsId,'is_show'=>1))
            ->order('create_time desc')
            ->limit($start,$pageSize)->get();

        //预处理数据
        foreach($res as $key=>$item){
            $res[$key]['create_time'] = date("Y-m-d H:i",strtotime($item['create_time']));
        }

        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }

    /**
     * 访问记录
     * @param $myFirmId
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function getVisitToFirmsLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_visit_log as a')->field('count(DISTINCT to_firms_id) as num')->where(array('a.firms_id'=>$myFirmId,'is_show'=>1))->getOne();
        $res = $this->table('firms_visit_log as a')
            ->field('count(a.to_firms_id) as num,a.to_firms_id,a.id as nid,b.classification,b.id,b.uname,b.type,b.EnterpriseID,b.companyname,b.major,b.head_pic,b.face_pic,b.wechat_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,max(a.create_time) as invitime,a.visit_type')
            ->where(array('a.firms_id'=>$myFirmId,'a.is_show'=>1))
            ->jion('left join firms as b on a.to_firms_id=b.id')
            ->group('a.to_firms_id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();


        //预处理数据
        foreach($res as $key=>$item){
            $res[$key]['isVip']    = 0;
            $res[$key]['invitime'] = date("m-d H:i",strtotime($item['invitime']));

            //判断vip是否过期
            if(strtotime($item['vip_time'])>time()){
                $res[$key]['isVip']    = 1;
            }

        }

        //dump($this->lastSql());
        return array('list'=>$res,'count'=>$count['num'],'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }

    /**
     * 访问记录 时间
     * @param $userId
     * @param $toFirmsId
     * @param $page
     * @param $pageSize
     * @return array
     */
    public function getVisitToFirmsDateLog($userId,$toFirmsId,$page,$pageSize){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_visit_log')->where(array('firms_id'=>$userId,'to_firms_id'=>$toFirmsId,'is_show'=>1))->count();
        $res = $this->table('firms_visit_log')->where(array('firms_id'=>$userId,'to_firms_id'=>$toFirmsId,'is_show'=>1))
            ->order('create_time desc')
            ->limit($start,$pageSize)->get();

        //预处理数据
        foreach($res as $key=>$item){
            $res[$key]['create_time'] = date("Y-m-d H:i",strtotime($item['create_time']));
        }
        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }

    /**
     * 清除记录
     * @param $userId
     * @return array
     */
    public function clearLog($userId){

        $this->table('firms_visit_log')->where(array('firms_id'=>$userId))->update(array('is_show'=>2));//访问记录
        $this->table('firms_call_log')->where(array('firms_id'=>$userId))->update(array('is_show'=>2));//拨打记录

        return array('status'=>200,'msg'=>'清除记录成功');
    }


    //获取某厂商拨打一厂商详情
    public function getOneCallToFirmLog($firmID,$myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_call_log as a')->where(array('a.firms_id'=>$myFirmId,'is_show'=>1,'b.EnterpriseID'=>$firmID))->jion('left join firms as b on a.to_firms_id=b.id')->count();
        $res = $this->table('firms_call_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.major,b.head_pic,b.face_pic,b.wechat_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,a.create_time as invitime,a.call_type')
            ->where(array('a.firms_id'=>$myFirmId,'a.is_show'=>1,'b.EnterpriseID'=>$firmID))
            ->jion('left join firms as b on a.to_firms_id=b.id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }
    //获取某厂商访问一厂商详情
    public function getOneVisitToFiirmLog($firmID,$myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_visit_log as a')->where(array('a.firms_id'=>$myFirmId,'is_show'=>1,'b.EnterpriseID'=>$firmID))->jion('left join firms as b on a.to_firms_id=b.id')->count();
        $res = $this->table('firms_visit_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.major,b.head_pic,b.face_pic,b.wechat_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,a.create_time as invitime,a.visit_type')
            ->where(array('a.firms_id'=>$myFirmId,'a.is_show'=>1,'b.EnterpriseID'=>$firmID))
            ->jion('left join firms as b on a.to_firms_id=b.id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        //dump($this->lastSql());
        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }

}