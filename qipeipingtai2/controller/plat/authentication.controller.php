<?php

/**
 * 认证申请 管理
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 9:41
 */
class PlatAuthenticationController extends Controller
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
            $this->template('plat.authentication.list');
        }else{
            dump('没有相关权限');

        }
    }
    public function getAuthentication(){
        $data     = $this->getRequest('data' ,'');
        $cationMo = model('plat.authentication.authentication','mysql');
        $res      = $cationMo->getAuthentication($data)  ;
        exit(json_encode($res,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 认证详情
     */
    public function getOneApply(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.authentication';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $id       = $this->getRequest('id' ,'');
            $cationMo = model('plat.authentication.authentication','mysql');
            $res      = $cationMo->getOneApply($id)  ;
            $this->assign('OneCheck',$res);
            $this->template('plat.firms.oneCheck');
        }else{
            dump('没有相关权限');

        }
    }
    /**
     * 通过/拒绝认证
     */
    public function checkStat(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.authentication';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data     = $this->getRequest('data' ,'');
            $cationMo = model('plat.authentication.authentication','mysql');
            $res      = $cationMo->checkStat($data)  ;
            if($res){
                $return['massageCode'] = 'success' ;
                $return['massage'] = $data['status'] == 2 ? '已通过申请' : '已拒绝申请' ;
            }else{
                $return['massage'] = '操作失败' ;
            }
        }else{
           $return['massage'] = '没有相关权限' ;

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

}