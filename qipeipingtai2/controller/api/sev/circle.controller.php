<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/23
 * Time: 23:11
 */
class ApiSevCircleController extends Controller
{
    private $user = array();
    private $userType = 1;

    public function __construct()
    {
       //获取提交的数据
        $token    = $this->getRequest('token','');
        $userType = $this->getRequest('userType','');
        if($userType==2){
            $this->userType = 2;
        }else{
            $this->userType = 1;
        }
        if($token){
            $userMo = model('api.sev.user','mysql');
            if($userType==2){
                $this->user = $userMo->loginYeWuIs($token);
            }else{
                $this->user = $userMo ->loginIs($token);
            }
        }else{
            $this->user = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
    }
    //圈子数据
    public function getCircleDateList(){
        $do      = $this->getRequest('dos','home');
        $keyword = $this->getRequest('keyword','');
        $page    = $this->getRequest('page',1);
        $pageSize= $this->getRequest('pageSize',6);
        $circleMo= model('api.sev.circle','mysql');
        switch ($do){
            case 'home':
                $return = $circleMo->getCircleData($keyword,0,$page,$pageSize,isset($this->user['data'])?$this->user['data']:'',$this->userType);
                $return['status'] = 200;
                break;
            case 'collect':
                if($this->user['status']==200){
                    $return = $circleMo->getCollectCircle($keyword,$this->user['data']['id'],$page,$pageSize,$this->userType);
                    $return['status'] = 200;
                }else{
                    $return = $this->user;
                }
                break;
            case 'mine':
                if($this->user['status']==200){
                    $return = $circleMo->getCircleData($keyword,$this->user['data']['id'],$page,$pageSize,isset($this->user['data'])?$this->user['data']:'',$this->userType);
                    $return['status'] = 200;
                }else{
                    $return = $this->user;
                }
                break;
            default:
                $return = array('list'=>'','page'=>1,'count'=>0,'pageSize'=>6);
                $return['status'] = 200;
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //删除自己的圈子圈子
    public function delSelfCircle(){
        if($this->user['status']==200){
            $id = $this->getRequest('id',0);
            $fd = $this->getRequest('fu','');
            $ty = $this->getRequest('ty','');
            $circleMo = model('web.circle','mysql');
            if($fd==$this->user['data']['id'] && $ty==$this->userType ){

                $res      = $circleMo->table('circle')->where(array('type'=>$this->userType,'id'=>$id,'fu_id'=>$fd))->update(array('is_delete'=>1));
                if($res){
                    $return = array('status'=>200,'msg'=>'删除成功');
                }else{
                    $return = array('status'=>111,'msg'=>'删除失败，请稍后再试');
                }
            }else{
                $return = array('status'=>110,'msg'=>'不能删除别人的圈子');
            }
        }else{
            $return = $this->user;
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //收藏圈子
    public function collectCircle(){
        if($this->user['status']==200){
            $cId = $this->getRequest('id','');
            $type= $this->getRequest('type','');
            $collectMo = model('web.collect','mysql');
            $proInfo = $collectMo->table('circle')->where(array('id'=>$cId));
            if($proInfo){
                $res = $collectMo->collectCircle($this->userType,$this->user['data']['id'],$cId,$type);
                if($res){
                    $return = array('status'=>200,'msg'=>'操作成功');
                }else{
                    $return = array('status'=>111,'msg'=>'操作失败，请刷新重试');
                }
            }else{
                $return = array('status'=>110,'msg'=>'圈子不存在，请刷新重试');
            }
        }else{
            $return = $this->user;
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //获取一条圈子详情
    public function getOneCircle(){
        if($this->user['status']==200){
            $userId   = $this->user['data']['id'];
            $userType = $this->userType;
        }else{
            $userId   = 0;
            $userType = 0;
        }
        $cId = $this->getRequest('id','');
        $circleMo= model('api.sev.circle','mysql');
        $return = $circleMo->getCircleLevelOne($cId,$userId,$userType);
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //发布圈子
    public function release(){
        if($this->user['status']==200){
            $content = $this->getRequest('content','');
            $img     = $this->getRequest('imgs','');
            $area    = $this->getRequest('area','');
            $EnterpriseID = $this->getRequest('EnterpriseID','');
            $cardId = $this->getRequest('cardId','');
            $circleType   = $this->getRequest('circleType',1);
            if($content || $img){
                $circleMo= model('api.sev.circle','mysql');
                $img = trim($img,',');
                $res = $circleMo->firmsToCircle($this->userType,$this->user['data']['id'],$content,$img,$area,$circleType,$EnterpriseID,$cardId);
                if($res){
                    $return = array('status'=>200,'msg'=>'发布成功');
                }else{
                    $return = array('status'=>111,'msg'=>'发布失败，请稍后再试');
                }
            }else{
                $return = array('status'=>110,'msg'=>'请填写内容或上传图片');
            }

        }else{
            $return = $this->user;
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //圈子评论与回复
    public function firmComment(){
        if($this->user['status']==200){
            $circleId= $this->getRequest('circleId','');
            $punId   = $this->getRequest('punId',0);
            $content = $this->getRequest('content','');
            if($content){
                $circleMo= model('api.sev.circle','mysql');
                if($punId){
                    $return = $circleMo->replyToCircle($this->userType,$this->user['data']['id'],$this->user['data']['uname'],$circleId,$punId,$content);
                }else{
                    $return = $circleMo->commentToCircle($this->userType,$this->user['data']['id'],$this->user['data']['uname'],$circleId,$content);
                }
            }else{
                $return = array('status'=>110,'msg'=>'请填写内容');
            }
        }else{
            $return = $this->user;
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //删除自己的圈子评论
    public function delSelfCircleComment()
    {
        if($this->user['status']==200){
            $id = $this->getRequest('circleId', 0);
            $circleMo = model('web.circle', 'mysql');
            $res = $circleMo->table('circle')->where(array('id' => $id))->update(array('is_delete' => 1));
            if ($res) {
                $return = array('status' => 200, 'msg' => '删除成功');
            } else {
                $return = array('status' => 205, 'msg' => '删除失败，请稍后再试');
            }
        } else {
            $return = $this->user;
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
}