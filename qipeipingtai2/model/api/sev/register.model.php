<?php
/**
 *
 * 用户注册模块
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/26
 * Time: 22:38
 */

class ApiSevRegisterModel extends Model{

    /**
     * 注册账号 账号初始化
     * @param $phone
     * @param $pwd
     * @return mixed
     */
    public function register($phone,$pwd){
        $nowTime = date("Y-m-d H:i:s",time());

        $pwd = md5(sha1($pwd).'sw');//加密密码
        $data['phone']    = $phone;
        $data['password'] = $pwd;
        $data['status']   = 1;
        $data['create_time'] = $nowTime;
        $data['update_time'] = $nowTime;
        //生成企业Id
        $data['EnterpriseID'] = $this-> makeID();
        //企业邀请码
        $data['invite_code'] = $this-> makeYQ();

        //$data['QR_pic'] = model('web.firms','mysql')->getQRStore($data['EnterpriseID']);

        $res = $this->table('firms')->insert($data);//插入一条记录
        if($res){
            $return['firmsId'] = $res;
            $return['status']  = 200;
            $return['msg']     = '注册成功，请前往完善基本资料';
        }else{
            $return['status'] = 201;
            $return['msg']    = '注册失败，请重试';
        }
        $return['status']  = 200;
        $return['msg']     = '注册成功，请前往完善基本资料';
        return $return;
    }

    /**
     * 保存企业信息
     * @param $data
     * @param $firmsId
     * @return array
     */
    public function saveMaterial($data,$firmsId){

        $res = $this->table('firms')->where(array('id'=>$firmsId))->update($data);
        if($res){
            $userToken  = authcode($firmsId,'ENCODE');
            //记录登录日志
            $this ->table('firms_login_log')->insert(array('firm_id'=>$firmsId,'create_time'=>date('Y-m-d H:i:s',time())));

            $return = array('status' => 200, 'msg' => '保存成功，正在为您登录','token'=>$userToken);
        }else{
            $return = array('status' => 201, 'msg' => '请勿重复提交');
        }
        return $return;
    }


    /**
     * 修改绑定手机号
     * @param $data
     * @param $firmsId
     * @return array
     */
    public function setPhone($phone,$id){

        $res = $this->table('firms')->where(array('id'=>$id))->update(array('phone'=>$phone));
        if($res){
            $return = array('status' => 200, 'msg' => '保存成功');
        }else{
            $return = array('status' => 201, 'msg' => '修改手机号失败');
        }
        return $return;
    }

    /**
     * 发送手机验证码
     * @param $phone
     * @param int $expiredTime
     * @param int $reSendTime
     * @return array
     */
    public function sendCode($phone,$expiredTime=300,$reSendTime=60){
        //清除过期验证码
        $this->clearCode($phone);
        //生成手机验证码
        $hasKey  = $phone;
        $hasMod  = 'smsCode';
        //自定义生成验证码
        $str = "0123456789";
        $code = '';
         for ($i = 0; $i < 6; $i++) {
             $code .= $str[mt_rand(0, strlen($str)-1)];
         }
        $sendRes  = $this->saveCode($hasKey,$hasMod,$code,$expiredTime,$reSendTime);
        return $sendRes;
    }


    /**
     * 保存验证码
     * @param $has_key string 验证码查验值   *键值
     * @param $has_mod string 验证码对应模块 *手机绑定验证
     * @param $code    string 验证码
     * @param $expiredTime    integer 过期时间 30分钟
     * @param $reSendTime  integer 重发间隔 默认60s
     * @return array
     */
    public function saveCode($hasKey,$hasMod,$code,$expiredTime=300,$reSendTime=60){

        $createTime  = date('Y-m-d H:i:s');
        $expiredTime = date('Y-m-d H:i:s',time()+$expiredTime);
        $data = array('has_key'=>$hasKey,'has_mod'=>$hasMod,'create_time'=>$createTime,'expired_time'=>$expiredTime,'code'=>$code);

        //判断是否可以进行重发
        $find = array('has_key'=>$hasKey,'has_mod'=>$hasMod,'code'=>$code);
        $res = $this->table('base_verify_code')->where($find)->getOne();

        $reSend=1;//默认需要重发

        $return['status'] = 201;
        $return['msg']    = '验证码发送失败，请重试';

        if($res){//之前有记录

            $nowTime = time();
            $oldCreate = strtotime($res['create_time']);

            $limitTime = $reSendTime-($nowTime-$oldCreate);

            if($limitTime>0){
                $reSend=0;//不需要重发
                $return['time']   = $limitTime;
                $return['status'] = 200;
                $return['msg']    = '验证码已发送';
            }
        }

        if($reSend==1){//需要重新发送

            //清楚之前自身的未过期验证码
            $this->table('base_verify_code')->del("has_key='$hasKey' and has_mod='$hasMod'");
            //发送验证码到手机
            $sms = model('sms189','mysql');
            $userTel  =  $hasKey;
            $userName =  $hasKey;
            $vcoder   =  $code;
            $vtime    = '5';

            $res1 = $sms->sendVMsg($userTel,$userName,$vcoder,$vtime);

            if($res1){
                //插入新的验证码
                $this->table('base_verify_code')->insert($data);
                $return['time']   = $reSendTime;
                $return['status'] = 200;
                $return['msg']    = '验证码已发送';
            }
        }

        return $return;

    }


    /**
     * 清除过期验证码
     */
    public function clearCode($tel){
        $nowTime = date('Y-m-d H:i:s');
        $where = "expired_time<='$nowTime'";
        if($tel){
            $where .= " and has_key=$tel";
        }
        $this->table('base_verify_code')->del($where);
    }



    /**
     *  检查对应验证码
     * @param $hasKey
     * @param $hasMod
     * @param $code
     * @return array
     */
    public function checkCode($hasKey,$hasMod,$code){
        $nowTime = time();
        $find = array('has_key'=>$hasKey,'has_mod'=>$hasMod,'code'=>$code);
        $res = $this->table('base_verify_code')->where($find)->getOne();
        if($res){//判断是否存在
            $expiredTime = strtotime($res['expired_time']);
            if($nowTime>$expiredTime){
                $data =array('status'=>202,'msg'=>'验证码已过期,请重新获取');
            }else{
                $data =array('status'=>200,'msg'=>'验证通过');
            }
        }else{
            $data =array('status'=>201,'msg'=>'短信验证码错误，请重新确认');
        }
        return $data;
    }

    //生成唯一的企业ID $t=time()
    public function makeID(){
        $zTime = 1495101488;
        $_time = time();
        $_time = substr($_time, 2);
        $rst = $this->table('firms')->where(array('EnterpriseID'=>$_time))->getOne();
        if($rst){
            $nTime = $zTime - mt_rand(1,10000000) - mt_rand(1,10000000);
            $this->makeID($nTime);
        }else{
            return $_time;
        }
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

    /**
     * @param $tel  电话号码
     * 根据厂商电话号码查询厂商信息
     */
    public function isUseTel($tel){
        $data = $this->table('firms')->where('phone='.$tel)->getOne();
        return $data;
    }

    /**
     * @param $data     封装数据
     * @param $token    未解析的id
     */
    public function yeWuCompany($data,$token){
        $id  = authcode($token,'DECODE');
        $id  = intval($id);
        $rst = $this->table('sales_user')->where('id='.$id)->getOne();
        if(!$rst){
            $return = array('status'=>104,'msg'=>'该业务员不存在');
        }else{
            $data['vid']           = 1;
            $data['uname']         = $data['companyname'];
            $data['EnterpriseID'] = $this->makeID();
            //企业邀请码
            $data['invite_code'] = $this-> makeYQ();
            $data['password']     = md5(sha1('7777777').'sw');
            $data['create_time']  = date('Y-m-d H:i:s');;
            $data['update_time']  = date('Y-m-d H:i:s');;
            $data['status']       = 1;
            $data['QR_pic']       = model('web.firms','mysql')->getQRStore($data['EnterpriseID'],$data['companyname'],$data['type']);
            $data['is_showfactry']= 2;
            if($data['type'] == 1){
                $data['is_check'] = 1;
            }
            $rst = $this->table('firms')->insert($data);
            if($rst > 0){
                $return = array('status'=>200,'msg'=>'注册成功');
//                $arr = array('firms_id'=>$rst,'sales_user_di'=>$id,'create_time'=>date("Y-m-d H:i:s",time()),'end_time'=>date('Y-m-d',strtotime("+3 month +1 day")));
//                $result = $this->table('firms_sales_user')->insert($arr);
//                if($result>0){
//                    model('web.msg','mysql')->toSaveMsg(2,$data['EnterpriseID'],'“'.$data['companyname'].'”入驻成功',0,0);      //添加到消息
//                    $return = array('status'=>200,'msg'=>'注册成功');
//                }else{
//                    $this->table('firms')->where('id='.$rst)->del();
//                    $return = array('status'=>106,'msg'=>'业务员关联不成功,厂商注册失败');
//                }
            }else{
                $return = array('status'=>105,'msg'=>'厂商注册失败');
            }
        }
        return $return;
    }
}