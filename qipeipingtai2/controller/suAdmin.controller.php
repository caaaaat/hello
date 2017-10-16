<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/9
 * Time: 15:00
 */
class suAdminController extends Controller
{
    public function main(){
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();
        $auth   = $this->getAuth($user['id']) ;
        $this->assign('auth',$auth) ;
        $this->assign('user',$user) ;
        $this->template('plat.main') ;
    }

    public function _blank(){
        //将未过期的业务员写入到厂商表
        $toolMo = model('tools.sales','mysql');
        $toolMo->sales2firm() ;

        //调用生成工资
        $salesMo = model('plat.sales.financial','mysql');
        $salesMo->automaticSettlement() ;
        //调用push消息写入
        $pushMo = model('plat.push.push','mysql');
        $pushMo->addToMsg() ;

        $this->template('plat._blank') ;
    }


    public function login(){

        $accounts = isset($_COOKIE['remember']) ? $_COOKIE['remember'] : '' ;
        $this->assign('accounts',$accounts) ;
        $this->template('plat.login') ;
    }
    //用户登录
    public function doLogin(){
        $return = array('massageCode'=>0);
        $userName = $this->getRequest('name'    , '');
        $userPwd  = $this->getRequest('pwd'     , '');
        $code     = $this->getRequest('code'    , '');
        $remember = $this->getRequest('remember', '');
        $res      = $this->code($code);
        if($res['massageCode'] === 'success'){
            if($userName && $userPwd){
                $mo     = model('suAdmin','mysql');
                $return = $mo->doLogin($userName,$userPwd);
                if($return['massageCode'] == 'success' ){
                    if($remember=='true'){
                        cookie('remember',$userName);
                    }else{
                        cookie('remember','');
                    }
                }
            }else{
                $return['massage'] = '请输入帐号密码';
            }
        }else{
            $return['massage'] = '验证码错误';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //后台验证验证码
    public function checkCode(){
        $verifyCode = $this->getRequest('code' , '');
        $return = $this->code($verifyCode);
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    private function code($verifyCode){
        $return = array('massageCode'=>0);
        $verifyCode = strtolower($verifyCode);
        $imgCode    = cookie('imgCode');
        //echo $imgCode;
        if(!$imgCode){
            $return['massage']    = '验证码已过期,点击图片刷新验证码';
        }else{
            $imgCode    = authcode($imgCode,'DECODE');
            $imgCode    = strtolower($imgCode);
            if($verifyCode == $imgCode){
                $return['massageCode'] = 'success';
            }else{
                $return['massage']    = '验证码错误';
            }
        }
        return $return;
    }

    //登出
    public function logOut(){
        cookie('userToken','');
        $return = array('massageCode'=>'success','msg'=>'退出成功');
        exit(json_encode($return));
    }

    /**
     * 获取用户权限
     * @param $id
     * @return array
     */
    private function getAuth($id){
        $authMo = model('msg');
        if($id == 1){
            $auth = $authMo->table('core_auth')->order('sort asc')->where(array('isMenu'=>1))->get();
        }else{
            $auth = $authMo->table('core_user_auth a')
                ->jion('left join core_auth b on a.authId=b.id ')
                ->order('sort asc')->where(array('a.userId'=>$id,'b.isMenu'=>1))->get();
        }
        $authItem = array() ;
        //dump($auth) ;
        if($auth){
            foreach ($auth as $k => $v){
                $v['url']                   = '/'.$v['modCode'].'/'.$v['funCode'] ;
                $authItem[$v['modName']][]  = $v ;
            }
        }
        //writeLog($authItem) ;
        return $authItem ;
    }
    /**
     * 修改密码
     */
    public function updatePwd(){
        $this->template('plat.updatePwd') ;
    }
    /**
     * 验证密码
     */
    private function checkPwd($id,$pwd){
        return model('suAdmin')->table('su_user')->where(array('id'=>$id,'pwd'=>md5(sha1($pwd).'sw')))->getOne() ;
    }
    /**
     * 修改密码
     */
    public function savePwd(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();
        $return = array('massageCode'=>0);
        if($user){
            $oldPwd = $this->getRequest('oldPwd','');
            $newPwd = $this->getRequest('newPwd','');
            $rPwd   = $this->getRequest('rPwd','');
            if(!$this->checkPwd($user['id'],$oldPwd)){
                $return['massageId']    = '#oldPwd';
                $return['massage']      = '旧密码输入错误';
                $return['massageInfo']  = '<img src="/images/plat/main/u62.png" style="width: 25px; border-radius: 15px; margin-left: 5px;">';
            }elseif($newPwd != $rPwd){
                $return['massageId']    = '#rPwd';
                $return['massage']      = '两次密码不一致';
                $return['massageInfo']  = '<img src="/images/plat/main/u62.png" style="width: 25px; border-radius: 15px; margin-left: 5px;">';
            }else{

                if($mo->savePwd($user['id'],$newPwd)){
                    $return['massage']      = '密码修改成功';
                    $return['massageCode']  = 'success';
                }else{
                    $return['massageId']    = '#oldPwd';
                    $return['massageCode']  = '密码修改失败';
                    $return['massageInfo']  = '<img src="/images/plat/main/u64.png" style="width: 25px; border-radius: 15px; margin-left: 5px;">';

                }
            }
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

}