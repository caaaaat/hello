<?php

/**
 * 后台首页模型  主要用于 后台登录及登录验证，权限获取
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/9
 * Time: 15:04
 */
class suAdminModel extends Model
{
    private $_table = 'su_user' ;
    public function loginIs($isJump = true){
        $userToken = cookie('userToken');
        $userToken = authcode($userToken,'DECODE');
        if($userToken){
            $user      = $this->getUserInfo($userToken);
            if(empty($user)){
                if($isJump){
//                    header("Location:/?m=suadmin&a=login");
                    header("Location:/suadmin/login");
                    exit;
                }else{
                    return array();
                }
            }else{
                unset($user['pwd']) ;
                G('user',$user);
                return $user;
            }
        }else{
            if($isJump){
                header("Location:/suadmin/login");
                exit;
            }else{
                return array();
            }
        }

    }

    //用户登陆
    public function doLogin($userName,$userPwd){ //massageCode
        $password = md5(sha1($userPwd).'sw');
        $return = array('massageCode'=>0);
        $res      = $this->table($this->_table)->where('code="'.$userName.'"')->getOne();
        if(isset($res['id'])){
            $pwd  = $res['pwd'];
            if($password == $pwd){
                if($res['status'] == 1){
                    //登陆成功
                    $userId     = $res['id'];
                    $userToken  = authcode($userId,'ENCODE');
                    cookie('userToken',$userToken);
                    //记录日志
                    model('actionLog')->actionLog($res['id'],$res['name'],$res['code'],'登录系统','成功') ;
                    $return['massageCode'] = 'success';
                }else{
                    //被冻结
                    $return['massage'] = '该帐号已被冻结';
                }
            }else{
                //密码错误
                $return['massage'] = '密码错误';
            }
        }else{
            //不存在
            $return['massage'] = '不存在的帐号';
        }
        return $return;
    }


    /**
     * 获取用户基本信息
     * @param $userToken
     * @return array|mixed
     */
    public function getUserInfo($userToken)
    {
        $user = $this->table($this->_table)->where(array('id'=>$userToken))->getOne();

        return $user;
    }

    /**
     * 检测用户权限
     * @param $userId
     * @param $mod
     * @param $fun
     * @return bool|mixed
     */
    public function checkUserAuth($userId,$mod,$fun){
        if($userId == 1){
            return true ;
        }else{
            $mod      = $mod ? $mod : G('mod');
            $fun      = $fun ? $fun : G('act');
            return $this->table('core_user_auth a')
                ->jion(' left join core_auth b on a.authId=b.id')
                ->where(array('a.userId'=>$userId,'b.modCode'=>$mod,'b.funCode'=>$fun))
                ->getOne() ;
        }
    }
    //获取某个用户的权限
    public function getMyAuth($id){
        if($id == 1){
            $auth = $this->table('core_auth a')
                ->field('a.id,a.modName,a.funName')
                ->where('a.isMenu=1 and a.id>1')
                ->order('sort asc')
                ->get() ;
        }else{
            $auth = $this->table('core_user_auth a')
                ->field('b.id,b.modName,b.funName')
                ->jion(' left join core_auth b on a.authId=b.id')
                ->where('a.userId='.$id.' and b.isMenu=1 and b.id>1')
                ->order('sort asc')
                ->get() ;
        }
        return ($auth) ;

    }

    /**
     * 保存密码
     * @param $id
     * @param $pwd
     * @return mixed
     */
    public function savePwd($id,$pwd){
        return $this->table($this->_table)->where(array('id'=>$id))->update(array('pwd'=>md5(sha1($pwd).'sw'))) ;
    }
}