<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/6
 * Time: 21:47
 */
class ApiSevInviteModel extends Model{


    /**
     * 查询我邀请厂商信息
     * @param $myFirmId
     * @return mixed
     */
    public function getInInviteMe($myFirmId){
        //邀请我的
        $inviteMe = $this->table('invite_log as a')
            ->field('b.id,b.uname,b.EnterpriseID,b.companyname,b.face_pic,b.type')
            ->where(array('a.firms_id'=>$myFirmId,'a.type'=>1))
            ->jion('left join firms as b on b.id=a.fu_id')->getOne();
        //我的邀请码
        $myInviteCode = $this->table('firms')->field('invite_code')->where(array('id'=>$myFirmId))->getOne();
        $data['status']    = 200;
        $data['isInvited'] = false;

        $data['data']['inviteMe'] = $inviteMe;
        $data['data']['myInviteCode'] = $myInviteCode;
        $data['data']['isInvited'] = false;

        if($inviteMe){
            $data['isInvited'] = true;
            $data['data']['isInvited'] = true;
        }
        return $data;
    }


    /**
     * 获取我的邀请
     * @param $p
     * @param $pageSize
     * @param $myFirmId
     * @return array
     */
    public function getMyInvite($myFirmId,$p,$pageSize){

        $page = (intval($p)-1)*$pageSize;

        $count = $this->table('invite_log as a')
            ->where("a.fu_id=$myFirmId and a.type=1 and b.companyname is not null")
            ->jion('left join firms as b on b.id=a.firms_id')->count();
        //我邀请的
        $myInvite = $this->table('invite_log as a')
            ->field('a.create_time,b.id,b.uname,b.EnterpriseID,b.companyname,b.face_pic,b.type')
            ->where("a.fu_id=$myFirmId and a.type=1 and b.companyname is not null")
            ->jion('left join firms as b on b.id=a.firms_id')->limit($page,$pageSize)->get();

        return $data = array('list'=>$myInvite,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);

    }



    /**
     * 绑定邀请人
     * @param $myFirmId
     * @param $inviteCode
     * @return array
     */
    public function bindInviteId($myFirmId,$inviteCode){
        //查询是否已绑定
        $res = $this->getInInviteMe($myFirmId);
        if($res['isInvited']){
            $return = array('status'=>200,'msg'=>'已经绑定了邀请人，请刷新页面');
        }else{
            $firmId = $this->table('firms')->where(array('invite_code'=>$inviteCode))->getOne();
            if($firmId){
                if($myFirmId == $firmId['id']){
                    $return = array('status'=>203,'msg'=>'不能绑定自己的邀请码');
                }else{
                    //获取分享送刷新点配置
                    $iniMo = model('web.ini','mysql');
                    $point = $iniMo->getInviteInfo();
                    $invitation = isset($point['invitation'])?$point['invitation']:'0';
                    $invited = isset($point['invited'])?$point['invited']:'0';

                    $rst = $this->table('invite_log')->insert(array('type'=>1,'fu_id'=>$firmId['id'],'firms_id'=>$myFirmId,'create_time'=>date('Y-m-d H:i:s',time())));
                    if($rst){
                        if($invitation){
                            $sql1 = "update firms set refresh_point=refresh_point+{$invitation} where id={$firmId['id']}";
                            $res1 = $this->query($sql1);
                            if($res1){
                                $this->table('pay_history')->insert(array('type'=>4,'status'=>1,'info'=>"邀请厂商获得{$invitation}刷新点",'payway'=>6,'refresh_point'=>$invitation,'firms_id'=>$firmId['id'],'create_time'=>date('Y-m-d H:i:s',time())));
                            }
                        }
                        if($invited){
                            $sql2 = "update firms set refresh_point=refresh_point+{$invited} where id={$myFirmId}";
                            $res2 = $this->query($sql2);
                            if($res2){
                                $this->table('pay_history')->insert(array('type'=>4,'status'=>1,'info'=>"填写厂商邀请码获得{$invited}",'payway'=>6,'refresh_point'=>$invited,'firms_id'=>$myFirmId,'create_time'=>date('Y-m-d H:i:s',time())));
                            }
                        }
                        $return = array('status'=>200,'msg'=>'绑定邀请人成功');
                    }else{
                        $return = array('status'=>202,'msg'=>'绑定邀请人失败');
                    }
                }
            }else{
                $return = array('status'=>201,'msg'=>'该邀请码的厂商不存在，请重新输入邀请码');
            }
        }

        return $return;
    }





}