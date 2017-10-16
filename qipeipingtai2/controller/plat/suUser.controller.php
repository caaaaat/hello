<?php

/**
 * 管理员管理控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10
 * Time: 11:16
 */
class PlatSuUserController extends Controller
{
    public function lists(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.suUser';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){

            $this->template('plat.sys.suUser.list');

        }else{
            dump('没有相关权限');

        }
    }

    /**
     * 获取管理员列表
     */
    public function getSuUser(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.suUser';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){

            $page    = $this->getRequest('page','1');
            $pageSize= $this->getRequest('pageSize','5');
            $status  = $this->getRequest('status','');
            $province= $this->getRequest('province','');
            $keywords= $this->getRequest('keywords','');

            $userMo = model('plat.sys.suUser','mysql');
            $return = $userMo->getSuUser($page,$pageSize,$status,$province,$keywords);

            if($return['massageCode'] === 'success'){
                $list = $return['list'] ;
                foreach ($list as $k => $v){
                    if ($v['id'] == $user['id']){
                        $list[$k]['is_me'] = 'true' ;
                    }else{
                        $list[$k]['is_me'] = 'false' ;
                    }
                }
                $return['list'] = $list ;
            }
            //dump($return);
        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 启用/停用管理员
     */
    public function changeStatus(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.suUser';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){

            $status = $this->getRequest('status','');
            $uId    = $this->getRequest('userId','');
            $userMo = model('plat.sys.suUser','mysql');
            $result = $userMo->changeStatus($uId,$status);

            if($result){//判断是否保存成功
                $return['massageCode'] = 'success';
                $return['massage']     = $status == 1 ? '启用成功' : '停用成功' ;
            }else{
                $return['massage']     = $status == 1 ? '启用失败' : '停用失败' ;
            }

        }else{
            $return['massage']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 重置密码
     */
    public function resetPassword(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.suUser';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){

            $uId    = $this->getRequest('userId','');
            $userMo = model('plat.sys.suUser','mysql');
            $result = $userMo->resetPassword($uId);

            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = '重置成功' ;
            }else{
                $return['massage']     = '重置失败' ;
            }

        }else{
            $return['massage']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 添加子管理员
     */
    public function resetSupp(){
        $mo       = model('suAdmin','mysql');
        $supper   = $mo->loginIs();

        $suId   = $this->getRequest('userId','');
        $userMo = model('plat.sys.suUser','mysql');
        $result = $userMo->getSupperInfo($suId);
        $this->assign('info',$result) ;
        $this->assign('me',$supper) ;
        $this->template('plat.sys.suUser.supper');
    }

    public function addSuper(){
        $mo       = model('suAdmin','mysql');
        $supper   = $mo->loginIs();
        //$supper   = G('user') ;
        //$suppProv = @$supper['province'] ;
        $this->assign('me',$supper) ;
        $this->template('plat.sys.suUser.addSuper');
    }

    /**
     * 添加/编辑 子管理员
     */
    public function saveSu(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.suUser';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data   = $this->getRequest('data','');
            $userMo = model('plat.sys.suUser','mysql');
            $result = $userMo->saveSu($data);

            $id     = isset($d['id'])     ? $d['id']     : '' ;
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = $id ? '编辑成功' : '添加成功' ;
            }else{
                $return['massage']     = $id ? '编辑失败' : '添加失败' ;
            }

        }else{
            $return['massage']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 编辑权限
     */
    public function superAuth(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        $uId    = $this->getRequest('id','');
        $suMo   = model('plat.sys.suUser','mysql');
        $myAuth = $mo->getMyAuth($user['id']);

        /*$myAuthItem = array() ;
        foreach ($myAuth as $k =>$v){
            $myAuthItem[$v['modName']][] = $v ;
        }*/
        $userAuth     = $mo->getMyAuth($uId);
        $suProv       = $suMo->getSupperInfo($uId);//被编辑管理员的区域
        $userAuthItem = array_column($userAuth,'id') ;
        //dump($myAuth);
        //dump($userAuthItem);
        $this->assign('uId'  ,$uId) ;
        $this->assign('myAuth'  ,$myAuth) ;
        $this->assign('userAuthItem',$userAuthItem) ;
        $this->assign('suProv',$suProv) ;
        $this->template('plat.sys.suUser.superAuth');
    }

    /**
     * 保存权限
     */
    public function saveAuth(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.suUser';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){

            $uId  = $this->getRequest('uId','');
            $auth = $this->getRequest('auth'  ,'');
            $prov = $this->getRequest('prov'  ,'');
            $userMo = model('plat.sys.suUser','mysql');
            $result = $userMo->saveAuth($uId,$auth,$prov);

            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = '编辑成功' ;
            }else{
                $return['massage']     = '编辑失败' ;
            }

        }else{
            $return['massage']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 编辑权限
     */
    public function upPassword(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        $uId        = $this->getRequest('id','');
        $suMo       = model('suAdmin','mysql');
        $myAuth     = $suMo->getMyAuth($user['id']);

        $myAuthItem = array() ;
        foreach ($myAuth as $k =>$v){
            $myAuthItem[$v['modName']][] = $v ;
        }
        $userAuth     = $suMo->getMyAuth($uId);
        $userAuthItem = array_column($userAuth,'id') ;
        //dump($userAuthItem);
        $this->assign('uId'  ,$uId) ;
        $this->assign('myAuthItem'  ,$myAuthItem) ;
        $this->assign('userAuthItem',$userAuthItem) ;
        $this->template('plat.sys.suUser.superAuth');
    }


}