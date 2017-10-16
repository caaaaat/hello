<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10
 * Time: 1:08
 */
class PlatWantController  extends Controller
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
            $this->template('plat.want.list');
        }else{
            dump('没有相关权限');

        }
    }

    public function getLists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.want';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data       = $this->getRequest('data' ,'');
            $activityMo = model('plat.want.buy','mysql');
            $res        = $activityMo->getLists($data)  ;
            $return     = $res ;
        }else{
            $return['massage'] = '没有相关权限' ;

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    public function delWant(){
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.want';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $activityMo = model('plat.want.buy','mysql');
            $result   = $activityMo->delWant($data);
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

    public function checkStatus(){
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.want';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $activityMo = model('plat.want.buy','mysql');
            $result   = $activityMo->checkStatus($data);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = '下架成功' ;
            }else{
                $return['massage']     = '下架失败'  ;
            }
        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

}