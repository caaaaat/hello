<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10
 * Time: 1:10
 */
class PlatPushController extends Controller
{
    private $user = array();
    public function __construct()
    {
        //检查是否登录
        $mo         = model('suAdmin','mysql');
        $this->user = $mo->loginIs();
    }

    public function lists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.authentication';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $this->template('plat.push.list');
        }else{
            dump('没有相关权限');
        }
    }

    public function getLists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.push';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data   = $this->getRequest('data' ,'');
            $pushMo = model('plat.push.push','mysql');
            $push   = $pushMo->getLists($data)  ;

        }else{
            $push   = array('massageCode'=>0,'massage'=>'没有相关权限');
        }
        exit(json_encode($push,JSON_UNESCAPED_UNICODE));
    }

    public function getContent(){
        $id         = $this->getRequest('id' ,'');
        $pushMo     = model('plat.push.push','mysql');
        $res        = $pushMo->getContent($id);
        $this->assign('content',$res) ;
        $this->template('plat.push.content') ;
    }

    public function newPush(){
        $this->template('plat.push.newPush') ;
    }

    public function savePush(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.push';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0,'massage'=>'');
        if($isAuth){
            $data   = $this->getRequest('data' ,'');
            $pushMo = model('plat.push.push','mysql');
            $res    = $pushMo->savePush($data)  ;

            if($res){
                $return['massageCode'] = 'success';
                $return['massage'] = '创建成功';
            }else{
                $return['massage'] = '创建失败';
            }
        }else{
            $return['massage'] = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

}