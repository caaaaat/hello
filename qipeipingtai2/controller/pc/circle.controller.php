<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/23
 * Time: 23:11
 */
class PcCircleController extends Controller
{
    private $user = array();

    public function __construct()
    {
        $loginMo    = model('web.login','mysql');
        $this->user = $user = $loginMo->loginIs(false);
    }
    //圈子数据
    public function getCircleDateList(){
        $do      = $this->getRequest('dos','home');
        $keyword = $this->getRequest('keyword','');
        $page    = $this->getRequest('page',1);
        $circleMo= model('web.circle','mysql');
        switch ($do){
            case 'home':
                $return = $circleMo->getCircleData($keyword,0,$page,6,$this->user,1);
                break;
            case 'collect':
                $return = $circleMo->getCollectCircle($keyword,$this->user?$this->user['id']:'-1',$page,6);
                break;
            case 'mine':
                $return = $circleMo->getCircleData($keyword,$this->user?$this->user['id']:'-1',$page,6,$this->user,1);
                break;
            default:
                $return = array('list'=>'','page'=>1,'count'=>0,'pageSize'=>6);
        }
        //dump($return);
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //厂商发布圈子
    public function release(){
        if($this->user){
            $content = $this->getRequest('content','');
            $img     = $this->getRequest('img','');
            $area     = $this->getRequest('area','');
            if($content || $img){
                $circleMo = model('web.circle','mysql');
                $img = trim($img,',');
                $res      = $circleMo->firmsToCircle($this->user['id'],$content,$img,$area);
                if($res){
                    $return = array('status'=>1,'msg'=>'发布成功');
                }else{
                    $return = array('status'=>2,'msg'=>'发布失败，请稍后再试');
                }
            }else{
                $return = array('status'=>2,'msg'=>'请填写内容或上传图片');
            }

        }else{
            $return = array('status'=>0,'msg'=>'您还没有登录，请先登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //分享邀请码到圈子
    public function inviteToCircle(){
        if($this->user){
            $content = $this->getRequest('content','');
            if($content){
                $circleMo = model('web.circle','mysql');
                $res      = $circleMo->inviteToCircle($this->user['id'],$this->user['EnterpriseID'],$content);
                if($res){
                    $return = array('status'=>1,'msg'=>'发布成功');
                }else{
                    $return = array('status'=>2,'msg'=>'发布失败，请稍后再试');
                }
            }else{
                $return = array('status'=>2,'msg'=>'请填写内容或上传图片');
            }

        }else{
            $return = array('status'=>0,'msg'=>'您还没有登录，请先登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //厂商删除自己的圈子圈子
    public function delSelfCircle(){
        if($this->user){
            $id = $this->getRequest('id',0);
            $fd = $this->getRequest('fd','');
            if($fd===$this->user['id']){
                $circleMo = model('web.circle','mysql');
                $res      = $circleMo->table('circle')->where(array('type'=>1,'id'=>$id,'fu_id'=>$fd))->update(array('is_delete'=>1));
                if($res){
                    $return = array('status'=>1,'msg'=>'删除成功');
                }else{
                    $return = array('status'=>2,'msg'=>'删除失败，请稍后再试');
                }
            }else{
                $return = array('status'=>2,'msg'=>'不能删除别人的圈子');
            }

        }else{
            $return = array('status'=>0,'msg'=>'您还没有登录，请先登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //厂商删除自己的圈子评论
    public function delSelfCircleComment()
    {
        if ($this->user) {
            $id = $this->getRequest('id', 0);
            $circleMo = model('web.circle', 'mysql');
            $res = $circleMo->table('circle')->where(array('id' => $id))->update(array('is_delete' => 1));
            if ($res) {
                $return = array('status' => 1, 'msg' => '删除成功');
            } else {
                $return = array('status' => 2, 'msg' => '删除失败，请稍后再试');
            }
        } else {
            $return = array('status' => 0, 'msg' => '您还没有登录，请先登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //收藏圈子
    public function collectCircle(){
        if($this->user){
            $cId = $this->getRequest('id','');
            $type   = $this->getRequest('type','');
            $collectMo = model('web.collect','mysql');
            $proInfo = $collectMo->table('circle')->where(array('id'=>$cId));
            if($proInfo){
                $res = $collectMo->collectCircle(1,$this->user['id'],$cId,$type);
                if($res){
                    $return = array('status'=>1,'msg'=>'操作成功');
                }else{
                    $return = array('status'=>2,'msg'=>'操作失败，请刷新重试');
                }
            }else{
                $return = array('status'=>2,'msg'=>'圈子不存在，请刷新重试');
            }
        }else{
            $return = array('status'=>0,'msg'=>'还没有登录，请先登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //厂商回复圈子
    public function firmComment(){
        if($this->user){
            $id      = $this->getRequest('id','');
            $content = $this->getRequest('content','');
            if($content){
               $circleMo = model('web.circle','mysql');
                $circle  = $circleMo->getJustOneCircleById($id);
                if($circle){
                    $return = $circleMo->commentCircle(1,$this->user['id'],$id,$content);
                }else{
                    $return = array('status'=>2,'msg'=>'该动态不存在或已被删除，请刷新重试');
                }
            }else{
                $return = array('status'=>2,'msg'=>'请输入内容');
            }
        }else{
            $return = array('status'=>0,'msg'=>'还没有登录，请先登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //厂商回复评论
    public function replyComment(){
        if($this->user){
            $id      = $this->getRequest('id','');
            $content = $this->getRequest('content','');
            if($content){
                $circleMo = model('web.circle','mysql');
                $circle  = $circleMo->getJustOneCircleById($id);
                if($circle){
                    $return = $circleMo->replyComment(1,$this->user['id'],$id,$content);
                }else{
                    $return = array('status'=>2,'msg'=>'该评论不存在或已被删除，请刷新重试');
                }
            }else{
                $return = array('status'=>2,'msg'=>'请输入内容');
            }
        }else{
            $return = array('status'=>0,'msg'=>'还没有登录，请先登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

}