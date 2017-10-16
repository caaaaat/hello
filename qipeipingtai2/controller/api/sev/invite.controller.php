<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/6
 * Time: 21:40
 */

class ApiSevInviteController extends Controller{

    /**
     * 获取邀请信息
     */
    public function getInviteInfo(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        if($token){
            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $inviteMo = model('api.sev.invite','mysql');

                $return = $inviteMo ->getInInviteMe($userId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }



    //获取邀请列表
    public function getMyInvite(){
        //获取提交的数据
        $token   = $this->getRequest('token','');
        $page    = $this->getRequest('page',1);
        $pageSize= $this->getRequest('pageSize',10);
        if($token){
            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){

                $userId = $return['data']['id'];
                $inviteMo = model('api.sev.invite','mysql');

                $return = $inviteMo ->getMyInvite($userId,$page,$pageSize);
            }

        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 获取邀请信息
     */
    public function bindInviteId(){
        //获取提交的数据
        $token      = $this->getRequest('token','');
        $inviteCode = $this->getRequest('inviteCode','');

        if($inviteCode){
            if($token){
                $userMo = model('api.sev.user','mysql');
                $return = $userMo ->loginIs($token);
                //用户数据请求成功
                if($return['status']==200){
                    //获取充值vip记录
                    $userId = $return['data']['id'];
                    $inviteMo = model('api.sev.invite','mysql');

                    $return = $inviteMo ->bindInviteId($userId,$inviteCode);
                }

            }else{
                $return = array('status'=>102,'msg'=>'您还未登录，请登录后重试');
            }
        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，请检查后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

}