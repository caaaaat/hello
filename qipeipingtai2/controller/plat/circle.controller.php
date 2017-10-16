<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10
 * Time: 1:09
 */
class PlatCircleController  extends Controller
{
    private $user = array();
    public function __construct()
    {
        //检查是否登录
        $mo         = model('suAdmin','mysql');
        $this->user = $mo->loginIs();
    }

    //=============圈子管理======================
    public function lists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.circle';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $this->template('plat.circle.list');
        }else{
            dump('没有相关权限');
        }
    }

    public function getLists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.circle';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data       = $this->getRequest('data' ,'');
            $activityMo = model('plat.circle.circle','mysql');
            $res        = $activityMo->getLists($data)  ;
            $return     = $res ;
        }else{
            $return['massage'] = '没有相关权限' ;

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 圈子详情
     */
    public function getOneCircle(){

        $cid     = $this->getRequest('cid', '');
        $salesMo = model('plat.sales.sales','mysql');
        $circle  = $salesMo->getOneCircle($cid);
        $this->assign('circle',$circle) ;
        $this->template('plat.sales.oneCirle');
    }
    /**
     * 删除圈子
     */
    public function delContent(){
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.circle';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $activityMo = model('plat.circle.circle','mysql');
            $result   = $activityMo->delContent($data);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = '删除成功' ;
            }else{
                $return['massage']     = '删除失败'  ;
            }
        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 评论页
     */
    public function comment(){
        $vid     = $this->getRequest('vid', '');
        $this->assign('cid',$vid) ;
        $this->template('plat.circle.comments');
    }
    /**
     * 获取圈子评论
     */
    public function getComments(){
        $cid     = $this->getRequest('cid'      , '');
        $type    = $this->getRequest('type'     , '');
        $page    = $this->getRequest('page'     ,'1');
        $pageSize= $this->getRequest('pageSize' ,'10');
        $keywords= $this->getRequest('keywords' , '');
        $salesMo = model('plat.sales.sales','mysql');
        $comments= $salesMo->getComments($cid,$type,$keywords,$page,$pageSize);

        exit(json_encode($comments,JSON_UNESCAPED_UNICODE));

    }
    public function delComment(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.circle';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth) {
            $id      = $this->getRequest('id' , '');
            $pid     = $this->getRequest('pid' , '');
            $salesMo = model('plat.sales.sales','mysql');//调用业务员管理中的方法
            $res     = $salesMo->delComment($id,$pid);
            if($res){
                $return ['massageCode'] = 'success';
                $return ['massage']     = '删除成功';
            }else{
                $return ['massage']     = '删除失败';
            }
        }else{
            $return ['massage'] = '没用相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //=============评论管理======================

}