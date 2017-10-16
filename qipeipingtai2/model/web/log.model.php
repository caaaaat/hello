<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/23
 * Time: 21:52
 */
class WebLogModel extends Model
{
    //来访记录写入                                      1pc_web 2移动端
    public function visitToLog($myFirmId,$toFirmId,$visit_type=1){
        $res = $this->table('firms_visit_log')->insert(array('firms_id'=>$myFirmId,'to_firms_id'=>$toFirmId,'visit_type'=>$visit_type,'is_show'=>1,'create_time'=>date('Y-m-d H:i:s')));
        if($res){
            $msgMo   = model('web.msg','mysql');
            $firm    = $this->table('firms')->where(array('id'=>$myFirmId))->getOne();
            $aboutId = $firm['EnterpriseID'];
            $lxArr = array('','经销商/轿车商家','经销商/货车商家', '经销商/用品商家','汽修厂/修理厂','汽修厂/快修保养','汽修厂/美容店');
            $content = '您的店铺被该'.$lxArr[$firm['classification']].'访问';
            $msgMo->toSaveMsg(6,$aboutId,$content,9,$toFirmId);
        }

        return $res;
    }
    //拨打记录写入                                   1电话· 2QQ
    public function callToLog($myFirmId,$toFirmId,$call_type=1){
        $res = $this->table('firms_call_log')->insert(array('firms_id'=>$myFirmId,'to_firms_id'=>$toFirmId,'call_type'=>$call_type,'create_time'=>date('Y-m-d H:i:s')));
        return $res;
    }

    //邀请记录
    public function getInviteLog($firmID,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('invite_log as a') ->where(array('a.type'=>1,'a.fu_id'=>$firmID))->count();
        $res = $this->table('invite_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.head_pic,b.face_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,a.create_time as invitime,b.QR_pic')
            ->where(array('a.type'=>1,'a.fu_id'=>$firmID))
            ->jion('left join firms as b on a.firms_id=b.id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }

    //来电记录
    public function getFirmsToCallLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_call_log as a') ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')->count();
        $res = $this->table('firms_call_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.head_pic,b.face_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,a.create_time as invitime,a.call_type,b.QR_pic')
            ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')
            ->jion('left join firms as b on a.firms_id=b.id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }

    //来访记录
    public function getFirmsToVisitLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_visit_log as a') ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')->count();
        $res = $this->table('firms_visit_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.head_pic,b.face_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,a.create_time as invitime,a.visit_type,b.QR_pic')
            ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')
            ->jion('left join firms as b on a.firms_id=b.id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }

    //拨打记录
    public function getCallToFirmsLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_call_log as a')->field('count(DISTINCT to_firms_id) as num')->where(array('a.firms_id'=>$myFirmId,'is_show'=>1))->getOne();
        $res = $this->table('firms_call_log as a')
            ->field('count(a.to_firms_id) as num,a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.major,b.head_pic,b.face_pic,b.wechat_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,max(a.create_time) as invitime,a.call_type,b.QR_pic')
            ->where(array('a.firms_id'=>$myFirmId,'a.is_show'=>1))
            ->jion('left join firms as b on a.to_firms_id=b.id')
            ->group('a.to_firms_id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        return array('list'=>$res,'count'=>$count['num'],'page'=>$page,'pageSize'=>$pageSize);
    }

    //访问记录
    public function getVisitToFirmsLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_visit_log as a')->field('count(DISTINCT to_firms_id) as num')->where(array('a.firms_id'=>$myFirmId,'is_show'=>1))->getOne();
        $res = $this->table('firms_visit_log as a')
            ->field('count(a.to_firms_id) as num,a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.major,b.head_pic,b.face_pic,b.wechat_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,max(a.create_time) as invitime,a.visit_type,b.QR_pic')
            ->where(array('a.firms_id'=>$myFirmId,'a.is_show'=>1))
            ->jion('left join firms as b on a.to_firms_id=b.id')
            ->group('a.to_firms_id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        //dump($this->lastSql());
        return array('list'=>$res,'count'=>$count['num'],'page'=>$page,'pageSize'=>$pageSize);
    }

    //获取某厂商拨打一厂商详情
    public function getOneCallToFirmLog($firmID,$myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_call_log as a')->where(array('a.firms_id'=>$myFirmId,'is_show'=>1,'b.EnterpriseID'=>$firmID))->jion('left join firms as b on a.to_firms_id=b.id')->count();
        $res = $this->table('firms_call_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.major,b.head_pic,b.face_pic,b.wechat_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,a.create_time as invitime,a.call_type,b.QR_pic')
            ->where(array('a.firms_id'=>$myFirmId,'a.is_show'=>1,'b.EnterpriseID'=>$firmID))
            ->jion('left join firms as b on a.to_firms_id=b.id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }
    //获取某厂商访问一厂商详情
    public function getOneVisitToFiirmLog($firmID,$myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_visit_log as a')->where(array('a.firms_id'=>$myFirmId,'is_show'=>1,'b.EnterpriseID'=>$firmID))->jion('left join firms as b on a.to_firms_id=b.id')->count();
        $res = $this->table('firms_visit_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.major,b.head_pic,b.face_pic,b.wechat_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,a.create_time as invitime,a.visit_type,b.QR_pic')
            ->where(array('a.firms_id'=>$myFirmId,'a.is_show'=>1,'b.EnterpriseID'=>$firmID))
            ->jion('left join firms as b on a.to_firms_id=b.id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        //dump($this->lastSql());
        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }

    /**
     * @param $uId  业务员id
     * @param $pwd  业务员密码
     * 验证业务员登录
     */
    public function salesmanLogin($uId,$pwd){
        $yeWu = $this->table('sales_user')->where('uId="'.$uId.'" and password="'.$pwd.'"')->getOne();
        return $yeWu;
    }

    /**
     * @param $uId  业务员最后登录时间
     */
    public function salesmanLastLogin($uId){
        $lastTime = date("Y-m-d H:i:s",time());
        $this->table('sales_user')->where('uId='.$uId)->update(array('last_time'=>$lastTime));
    }
}