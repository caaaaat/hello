<?php
/**
 * 登录模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/20
 * Time: 23:40
 */

class ApiSevLoginModel extends Model{

    protected $_table = 'firms';

    /**
     * 用户登录
     * @param $userPhone
     * @param $userPwd
     * @return array
     */
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

                    if(!$res['companyname']){//未完善资料

                        $return['status']     = 200;
                        $return['isMaterial'] = 0;
                        $return['firmId']     = $userId;

                    }else{

                        $userToken  = authcode($userId,'ENCODE');
                        //记录日志
                        $this->table('firms_login_log')->insert(array('firm_id'=>$userId,'create_time'=>date('Y-m-d H:i:s',time())));
                        $return['isMaterial']  = 1;
                        $return['status']      = 200;
                        $return['userToken']   = $userToken;
                    }

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
     * 注册账号 密码重置
     * @param $phone
     * @param $pwd
     * @return mixed
     */
    public function resetPwd($phone,$pwd){
        $nowTime = date("Y-m-d H:i:s",time());

        $pwd = md5(sha1($pwd).'sw');//加密密码
        $data['password'] = $pwd;
        $data['update_time'] = $nowTime;

        $res = $this->table('firms')->where(array('phone'=>$phone))->update($data);//插入一条记录
        if($res){
            $return['status']  = 200;
            $return['msg']     = '密码重置成功，请前往登录';
        }else{
            $return['status'] = 201;
            $return['msg']    = '密码重置失败，请重试';
        }
        return $return;
    }





}
