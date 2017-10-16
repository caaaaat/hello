<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/15
 * Time: 16:01
 */

class WebLoginModel extends Model{

    protected $_table = 'firms';

    public function loginIs($isJump = true){
        $userToken = cookie('userToken_F');
        $userToken = authcode($userToken,'DECODE');
        if($userToken){
            $user      = $this->getUserInfo($userToken);
            if(empty($user)){
                if($isJump){
                    header("Location:/login");
                    exit;
                }else{
                    return array();
                }
            }else{
                unset($user['pwd']) ;
                G('user',$user);
                $msgMo = model('web.msg','mysql');
                $user['wdxx'] = $msgMo->getUnReadMsgNum(1,$user['id']);
                return $user;
            }
        }else{
            if($isJump){
                header("Location:/login");
                exit;
            }else{
                return array();
            }
        }

    }


    public function doLogin($userPhone,$userPwd){
        $password = md5(sha1($userPwd).'sw');
        $return = array('massageCode'=>0);
        $res      = $this->table($this->_table)->where(array('phone'=>$userPhone))->getOne();
        if(isset($res['id'])){
            $pwd  = $res['password'];
            if($password == $pwd){
                if($res['status'] == 1){
                    //登陆成功
                    $this->table($this->_table)->where(array('phone'=>$userPhone))->update(array('last_time'=>date('Y-m-d H:i:s',time())));
                    $userId     = $res['id'];
                    $userToken  = authcode($userId,'ENCODE');
                    cookie('userToken_F',$userToken);

                    //记录日志
                    $this->table('firms_login_log')->insert(array('firm_id'=>$userId,'create_time'=>date('Y-m-d H:i:s',time())));
                    $return['massageCode'] = 'success';
                }else{
                    //被冻结
                    $return['massage'] = '该帐号已被禁用';
                }
            }else{
                //密码错误
                $return['massage'] = '账号或密码错误';
            }
        }else{
            //不存在
            $return['massage'] = '账号或密码错误';
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
     * 检测手机号是否存在
     * @param $phone
     * @return int
     */
    public function checkPhone($phone){
        $res = $this->table($this->_table)->where(array('phone'=>$phone))->getOne();
        if($res){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 图像验证码验证
     * @param $verifyCode
     * @return array
     */
    public function code($verifyCode){
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

    /**
     * 检测短信验证码
     * @param $verifyCode
     * @return array
     */
    public function checkSmsCode($verifyCode,$phone){

        $hasKey = $phone;
        $hasMod = 'smsCode';
        $code   = $verifyCode;
        $verMo = model('api.sev.register');

        $res = $verMo -> checkCode($hasKey,$hasMod,$code);

        if($res['status']=='200'){
            $return = array('status'=>1,'msg'=>'手机验证码正确');
        }elseif ($res['status']=='202'){
            $return = array('status'=>0,'msg'=>$res['msg']);
        }else{
            $return = array('status'=>0,'msg'=>'手机验证码错误');
        }
        return $return;
    }
    //生成唯一的邀请码
    public function makeYQ(){
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)] .strtoupper(dechex(date('m'))) .date('d').substr(time(),-5) .substr(microtime(),2,5) .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        $rst = $this->table('firms')->where(array('invite_code'=>$d))->getOne();
        if($rst){
            $this->makeYQ();
        }else{
            return $d;
        }
    }
    //生成唯一的企业ID $t=time()
    public function makeID($t){
        $zTime = 1495101488;
        $_time = $t;
        $_time = substr($_time, 2);
        $rst = $this->table('firms')->where(array('EnterpriseID'=>$_time))->getOne();
        if($rst){
            $nTime = $zTime - mt_rand(1,10000000) - mt_rand(1,10000000);
            $this->makeID($nTime);
        }else{
            return $_time;
        }
    }
    //密码加密
    public function psdToEn($password){
        return md5(sha1($password).'sw');
    }
    //创建厂商
    public function createNewFirm($data){
        $res = $this->table('firms')->insert($data);
        return $res;
    }
    //修改密码
    public function changePassword($phone,$password){
        $password = $this->psdToEn($password);
        $res = $this->table('firms')->where(array('phone'=>$phone))->update(array('password'=>$password));
        return $res;
    }

    /**
     * 检查电话号码是都存在除某厂商之外
     * @param $id
     * @param $phone
     * @return mixed
     */
    public function checkPhoneNoIncludeId($id,$phone){
        $res = $this->table('firms')->where('phone="'.$phone.'" and id<>'.$id)->getOne();
        return $res;
    }

    /**
     * 查询配置表中客服QQ
     */
    public function getKeFuQQ(){
        $return = '';
        $data = $this->table('base_ini')->where('id=4')->getOne();
        if($data){
            $value= json_decode($data['value']);
            if($value){
                if(isset($value->qq)){
                    $return   = $value->qq;
                }
            }
        }
        return $return;
    }



}