<?php
/**
 *用户登录相关
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/20
 * Time: 22:49
 */

class ApiSevLoginController extends Controller{

    /**
     * 获取登录令牌
     */
    public function getToken(){

        $phone    = $this->getRequest('userTel','');
        $password = $_POST['userPwd'];

        $loginMo = model('api.sev.login','mysql');

        if($phone && $password){
            if(preg_match("/^1[34578]{1}\d{9}$/",$phone)) {

                $res = $loginMo->doLogin($phone,$password);

                if(isset($res['status'])&&$res['status'] == 200 ){

                    if($res['isMaterial']==0){
                        $return = array('status'=>102,'massage'=>'您的资料不全，请完善资料','firmId'=>$res['firmId']);
                    }else{
                        $return = array('status'=>200,'massage'=>'登录成功','token'=>$res['userToken']);
                    }

                }else{

                    $return = array('status'=>101,'massage'=>$res['massage']);
                }
            }else{
                $return = array('status'=>101,'massage'=>'请输入正确的手机号');
            }

        }else{
            $return = array('status'=>101,'massage'=>'输入的数据有误请重新输入');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 验证手机号码
     */
    public function viPhone(){

        $phone = $this->getRequest('phone','');
        if($phone){
            $code= $this->getRequest('code','');
            //验证手机号
            if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
                $loginMo = model('web.login','mysql');
                $rst     = $loginMo->code($code);
                //检测验证码
                if($rst['massageCode'] === 'success') {
                    $res = $loginMo->checkPhone($phone);
                    //检测手机号是否存在
                    if ($res) {
                        $return['status'] = 200;
                        $return['msg']    = '验证通过，前往密码重设';
                    } else {
                        $return['status'] = 104;
                        $return['msg']    = '该手机号未注册，请检查后重试';
                    }
                }else{
                    $return['status'] = 103;
                    $return['msg']    = $rst['massage'];
                }
            }else{
                $return['status'] = 102;
                $return['msg']    = '您的手机号码输入有误，请检查后重试';
            }
        }else{
            $return['status'] = 101;
            $return['msg']    = '请输入您的手机号码';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 重设密码
     */
    public function resetPwd(){
        $phone  = $this->getRequest('phone','');
        $smsCode= $this->getRequest('smsCode','');
        $pwd    = $this->getRequest('pwd','');
        if($phone&&$pwd){
            //验证手机号
            if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
                $registerMo = model('api.sev.register','mysql');

                $hasKey = $phone;
                $hasMod = 'smsCode';
                $codeMsg = $registerMo -> checkCode($hasKey,$hasMod,$smsCode);
                //检测验证码
                if($codeMsg['status'] == '200') {
                    $loginMo = model('web.login','mysql');
                    $res = $loginMo->checkPhone($phone);
                    //检测手机号是否存在
                    if ($res) {
                        $loginApiMo = model('api.sev.login','mysql');
                        $return     = $loginApiMo->resetPwd($phone,$pwd);
                    } else {
                        $return['status'] = 104;
                        $return['msg']    = '该手机号未注册，请检查后重试';
                    }
                }else{
                    $return['status'] = 103;
                    $return['msg']    = $codeMsg['msg'];
                }
            }else{
                $return['status'] = 102;
                $return['msg']    = '您的手机号码输入有误，请检查后重试';
            }
        }else{
            $return['status'] = 101;
            $return['msg']    = '提交数据有误，请刷新后重试';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 业务员登录
     */
    public function salesmanLogin(){
        $uId = $this->getRequest('uId','');
        $pwd = $this->getRequest('userPwd','');
        if($uId && $pwd){
            $pwd = md5(sha1($pwd).'sw');
            $yeWu= model('web.log')->salesmanLogin($uId,$pwd);
            if($yeWu){
                if($yeWu['status']==2){     //禁用状态
                    $return['status'] = 0;
                    $return['msg']    = '该账号已被禁用。';
                }else{
                    $userToken  = authcode($yeWu['id'],'ENCODE');
                    $return['status'] = 1;
                    $return['token']  = $userToken;
                    $return['msg']    = '登录成功';
                    $return['info']   = $yeWu;
                    model('web.log')->salesmanLastLogin($uId);  //更改最后登录时间
                }
            }else{
                $return['status'] = 0;
                $return['msg']    = '业务员ID或者密码错误';
            }
        }else{
            $return['status'] = 0;
            $return['msg']    = '参数不齐，操作失败!';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
}