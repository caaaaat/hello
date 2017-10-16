<?php
/**
 * 用户信息
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/21
 * Time: 17:32
 */

class ApiSevUserController extends Controller{



    private $user = array();
    private $userType = 1;

    public function __construct()
    {
        //获取提交的数据
        $token    = $this->getRequest('token','');
        $userType = $this->getRequest('userType','');
        if($userType==2){
            $this->userType = 2;
        }else{
            $this->userType = 1;
        }
        if($token){
            $userMo = model('api.sev.user','mysql');
            if($userType==2){
                $this->user = $userMo->loginYeWuIs($token);
            }else{
                $this->user = $userMo ->loginIs($token);
            }
        }else{
            $this->user = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
    }


    /**
     * 通过token获取用户信息
     */
    public function userInfo($token){

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }

            return $return;
    }


    /**
     * 获取用户数据
     */
    public function getUserInfo(){

        //获取提交的数据
        $token = $this->getRequest('token','');

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //获取客服qq tel
            $indexMo = model('api.sev.index','mysql');
            $telQq = $indexMo ->getServerTelQq();
            $return['telQq'] = $telQq;
        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 获取用户数据
     */
    public function getYeWuUserInfo(){

        //获取提交的数据
        $token = $this->getRequest('token','');

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginYeWuIs($token);

        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 修改业务员昵称
     */
    public function changeYeWuUname(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $data['uname'] = $this->getRequest('uname','');
        if($token && $data['uname']){
            $rst = model('api.sev.user','mysql')->editYeWu($token,$data);
            if($rst>0){
                $return = array('status'=>200,'msg'=>'修改成功');
            }else{
                $return = array('status'=>101,'msg'=>'修改昵称失败');
            }
        }else{
            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户认证状态
     */
    public function getCompanyAuth(){

        //获取提交的数据
        $token     = $this->getRequest('token','');
        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){

                $userId = $return['data']['id'];
                $return = $userMo ->getCompanyAuth($userId);
            }

        }else{

            $return = array('status'=>101,'msg'=>'你还未登录，登录后重试');

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 保存用户认证信息
     */
    public function saveAuth(){

        //获取提交的数据
        $token     = $this->getRequest('token','');

        $firmsName = $this->getRequest('firmsName','');
        $firmsMan  = $this->getRequest('firmsMan','');
        $firmsTel  = $this->getRequest('firmsTel','');
        $province  = $this->getRequest('province','');
        $city      = $this->getRequest('city','');
        $district  = $this->getRequest('district','');
        $address   = $this->getRequest('address','');

        $licence_pic   = $this->getRequest('licence_pic','');
        $taxes_pic     = $this->getRequest('taxes_pic','');
        $field_pic     = $this->getRequest('field_pic','');
        $brand_pic     = $this->getRequest('brand_pic','');
        $agents_pic    = $this->getRequest('agents_pic','');

        if($token){

            if($firmsName&&$firmsMan&&$firmsTel&&$province&&$address){

                $userMo = model('api.sev.user','mysql');
                $return = $userMo ->loginIs($token);

                if($licence_pic||$taxes_pic||$field_pic||$brand_pic||$agents_pic){
                    //用户数据请求成功
                    if($return['status']==200){

                        $userId = $return['data']['id'];

                        $data = array();

                        $data['firms_id'] = $userId;

                        $data['firmsName'] = $firmsName;
                        $data['firmsMan'] = $firmsMan;
                        $data['firmsTel'] = $firmsTel;
                        $data['province'] = $province;
                        $data['city']     = $city;
                        $data['district'] = $district;
                        $data['address']  = $address;

                        $data['licence_pic'] = $licence_pic;
                        $data['taxes_pic']   = $taxes_pic;
                        $data['field_pic']   = $field_pic;
                        $data['brand_pic']   = $brand_pic;
                        $data['agents_pic']  = $agents_pic;

                        $nowTime = date("Y-m-d H:i:s");
                        $data['create_time']  = $nowTime;
                        $data['update_time']  = $nowTime;
                        $data['status']       = 1;

                        $return = $userMo ->saveAuth($data);
                    }
                }else{
                    $return = array('status'=>103,'msg'=>'请至少上传一张资质图片');
                }
            }else{
                $return = array('status'=>102,'msg'=>'提交数据有误，请检查后重试');
            }

        }else{

            $return = array('status'=>101,'msg'=>'您还未登录，登录后重试');

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }



    /**
     * 获取用户vip充值数据
     */
    public function getVipHistory(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];

                $return = $userMo ->getVipHistory($userId,$p,$pageSize);

            }

        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }



    /**
     * 获取用户刷新点数据
     */
    public function getRefreshHistory(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];

                $return = $userMo ->getRefreshHistory($userId,$p,$pageSize);

            }

        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 获取用户拨打记录数据
     */
    public function getCallToFirmsLog(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $logMo = model('api.sev.log','mysql');

                $return = $logMo ->getCallToFirmsLog($userId,$p,$pageSize);

            }

        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 获取用户被拨打记录数据
     */
    public function getFirmsToCallLog(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $logMo = model('api.sev.log','mysql');

                $return = $logMo ->getFirmsToCallLog($userId,$p,$pageSize);

            }

        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 获取用户拨打记录数据-时间详情
     */
    public function getCallToFirmsDateLog(){

        //获取提交的数据
        $token     = $this->getRequest('token','');
        $toFirmsId = $this->getRequest('toFirmsId','');
        $p         = $this->getRequest('page','1');
        $pageSize  = $this->getRequest('pageSize','10');

        if($token&&$toFirmsId){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $logMo = model('api.sev.log','mysql');

                $return = $logMo ->getCallToFirmsDateLog($userId,$toFirmsId,$p,$pageSize);
            }

        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 获取用户访问记录数据
     */
    public function getVisitToFirmsLog(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $logMo = model('api.sev.log','mysql');

                $return = $logMo ->getVisitToFirmsLog($userId,$p,$pageSize);

            }

        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 获取用户被访问记录数据
     */
    public function getFirmsToVisitLog(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $logMo = model('api.sev.log','mysql');

                $return = $logMo ->getFirmsToVisitLog($userId,$p,$pageSize);

            }

        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 获取用户访问记录数据-时间详情
     */
    public function getVisitToFirmsDateLog(){

        //获取提交的数据
        $token     = $this->getRequest('token','');
        $toFirmsId = $this->getRequest('toFirmsId','');
        $p         = $this->getRequest('page','1');
        $pageSize  = $this->getRequest('pageSize','10');

        if($token&&$toFirmsId){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $logMo = model('api.sev.log','mysql');

                $return = $logMo ->getVisitToFirmsDateLog($userId,$toFirmsId,$p,$pageSize);
            }

        }else{

            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 清空用户访问日志
     */
    public function clearLog(){

        //获取提交的数据
        $token     = $this->getRequest('token','');
        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $logMo = model('api.sev.log','mysql');

                $return = $logMo ->clearLog($userId);
            }

        }else{

            $return = array('status'=>101,'msg'=>'你还未登录，登录后重试');

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 查询汽修厂或经销商获取业务员
     */
    public function salesMan(){

        //获取提交的数据
        $token     = $this->getRequest('token','');
        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){

                $userId = $return['data']['id'];

                $return = $userMo ->isSalesMan($userId);
            }

        }else{

            $return = array('status'=>101,'msg'=>'你还未登录，登录后重试');

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 绑定业务员
     */
    public function bindSales(){

        //获取提交的数据
        $token      = $this->getRequest('token','');
        $salesUserId= $this->getRequest('salesUserId','');
        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //检查是否已经绑定
                $userId = $return['data']['id'];
                $res = $userMo ->isSalesMan($userId);
                if($res['data']['isSalesMan']==true){//已经绑定
                    $return = array('status'=>200,'msg'=>'已绑定业务员');
                }else{//未绑定 前往绑定
                    $return = $userMo ->bindSales($userId,$salesUserId);
                }
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，登录后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 修改用户头像
     */
    public function  saveHeader(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $data['head_pic'] = $this->getRequest('headerImg','');

        if($token){
            $userInfo = $this->userInfo($token);

            if($userInfo['status']==200){
                    $id = $userInfo['data']['id'];

                    $firmsMo = model('web.firms','mysql');
                    $res = $firmsMo->changeFirm($id,$data);

//                return 1;
                    if($res){
                        $return = array('status'=>200,'msg'=>'头像修改成功');
                    }else{
                        $return = array('status'=>103,'msg'=>'修改失败，请稍后重试');
                    }

            }else{
                $return = array('status'=>102,'msg'=>'修改失败，请刷新页面后重试');
            }

        }else{
            $return = array('status'=>101,'msg'=>'修改失败，请刷新页面后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 修改用户昵称
     */
    public function  changeUname(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $data['uname'] = $this->getRequest('uname','');

        if($token&&$data['uname']){
            $userInfo = $this->userInfo($token);

            if($userInfo['status']==200){
                $id = $userInfo['data']['id'];
                $oldUname = $userInfo['data']['uname'];

                if($data['uname']==$oldUname){
                    $return = array('status'=>104,'msg'=>'您还没有对昵称进行修改');
                }else{
                    $firmsMo = model('web.firms','mysql');
                    $res = $firmsMo->changeFirm($id,$data);

                    if($res){
                        $return = array('status'=>200,'msg'=>'昵称修改成功');
                    }else{
                        $return = array('status'=>103,'msg'=>'修改失败，请稍后重试');
                    }
                }


            }else{
                $return = array('status'=>102,'msg'=>'修改失败，请刷新页面后重试');
            }

        }else{
            $return = array('status'=>101,'msg'=>'修改失败，请刷新页面后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    //修改厂商密码
    public function changePwd(){
        //获取提交的数据
        $token = $this->getRequest('token','');

        if($token){

            $userInfo = $this->userInfo($token);

            if($userInfo['status']==200){
                $id = $userInfo['data']['id'];

                $oldPassword = $this->getRequest('oldPwd','');
                $newPassword = $this->getRequest('newPwd','7777777');
                $repPassword = $this->getRequest('repPassword','1111111');
                $firmsMo = model('web.firms','mysql');
                $res = $firmsMo->getFirmInfo($id);
                $loginMo = model('web.login', 'mysql');
                $psd     = $loginMo->psdToEn($oldPassword);
                if($res['password']===$psd){
                    $len = strlen($newPassword);
                    //判断密码长度
                    if($len>=6 && $len<=16){
                        //两次密码是否相同
                        if($newPassword===$repPassword){

                            $newPsd  = $loginMo->psdToEn($newPassword);

                            if($newPsd===$res['password']){
                                $return = array('status'=>106,'msg'=>'新密码与旧密码一致，修改失败');
                            }else{
                                $rst = $firmsMo->changeFirm($id,array('password'=>$newPsd));
                                if($rst){
                                    $return = array('status'=>200,'msg'=>'修改成功');
                                }else{
                                    $return = array('status'=>107,'msg'=>'密码修改失败');
                                }
                            }
                        }else{
                            $return = array('status'=>105,'msg'=>'两次输入的密码不一致');
                        }
                    }else{
                        $return = array('status'=>104,'msg'=>'请输入6至16位的数字，字母或符号');
                    }
                }else{
                    $return = array('status'=>103,'msg'=>'原密码错误，请重新输入');
                }

            }else{
                $return = array('status'=>102,'msg'=>'修改失败，请刷新页面后重试');
            }

        }else{
            $return = array('status'=>101,'msg'=>'修改失败，请刷新页面后重试');

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /*绑定手机第一步*/
    public function bindPhone(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $code  = $this->getRequest('code','');
        $phone = $this->getRequest('phone','');

        $userInfo = $this->userInfo($token);

        if($token&&$userInfo['status']==200){

            if($phone){
                if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
                    $loginMo = model('web.login','mysql');
                    $rst      = $loginMo->code($code);
                    //检测验证码
                    if($rst['massageCode'] === 'success') {

                        $oldPhone = $userInfo['data']['phone'];

                        //检查手机号是否存在
                        if($phone!=$oldPhone){

                            $id = $userInfo['data']['id'];

                            $res = $loginMo->checkPhoneNoIncludeId($id,$phone);
                            if($res){
                                $return = array('status'=>104,'msg'=>'该手机号已被使用');
                            }else{
                                $return = array('status'=>200,'msg'=>'手机号码验证成功，前往下一步');
                            }
                        }else{
                            $return = array('status'=>103,'msg'=>'该号码是当前绑定的手机号');
                        }
                    }else{
                        $return = array('status'=>102,'msg'=>'验证码有误');
                    }
                }else{
                    $return = array('status'=>101,'msg'=>'手机号码输入有误');
                }
            }else{
                $return = array('status'=>105,'msg'=>'该手机号已被使用');
            }

        }else{
            $return = array('status'=>101,'msg'=>'修改失败，请刷新页面后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //修改厂商绑定手机
    public function changePhone(){
        $smsCode = $this->getRequest('smsCode','');
        $phone = $this->getRequest('phone','');
        $return = array('status'=>2,'msg'=>'请刷新页面重试');
        if($phone){
            if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
                $loginMo = model('web.login','mysql');
                $rst      = $loginMo->checkSmsCode($smsCode);
                //检测验证码
                if($rst['status']) {
                    //检查手机号是否存在
                    if($phone!=$this->user['phone']){
                        $firmsMo = model('web.firms','mysql');
                        $res = $firmsMo->changeFirm($this->user['id'],array('phone'=>$phone));
                        if($res){
                            $return = array('status'=>1,'msg'=>'绑定成功');
                        }else{
                            $return = array('status'=>2,'msg'=>'绑定失败，请稍后再试');
                        }
                    }else{
                        $return = array('status'=>1,'msg'=>'绑定失败，该号码是现绑定的手机号');
                    }
                }else{
                    $return = array('status'=>2,'msg'=>'手机验证码错误');
                }
            }else{
                $return = array('status'=>2,'msg'=>'请刷新页面，重新输入手机号');
            }
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    //绑定手机号码
    public function setPhone(){

        //用户数据请求成功
        if($this->user['status']==200){

            $phone  = $this->getRequest('phone','');
            $smsCode= $this->getRequest('smsCode','');

            if($phone&&$smsCode){

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
                            $return['status'] = 104;
                            $return['msg']    = '该手机号已被使用，请检查后重试';
                        } else {
                            $id = $this->user['data']['id'];
                            $return = $registerMo->setPhone($phone,$id);
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

        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登陆后重试');
        }


        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    //获取联系信息  电话  qq
    public function getLinkInfo(){
        $userMo = model('api.sev.user','mysql');
        $return = $userMo ->getLinkInfo();
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //获取公司简介
    public function getMyInfo(){
        $userMo = model('api.sev.user','mysql');
        $return = $userMo ->getMyInfo();
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //获取公司简介
    public function getFuWuXieYi(){
        $userMo = model('api.sev.user','mysql');
        $return = $userMo ->getFuWuXieYi();
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //获取app版本
    public function getAppVersion(){
        $userMo = model('api.sev.user','mysql');
        $return = $userMo ->getAppVersion();
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /*——————————————
     *店铺管理
     *——————————————*/

    //获取店铺基础信息
    public function getStoreInfo(){

        //获取提交的数据
        $token = $this->getRequest('token','');

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $return = $userMo ->getStoreInfo($userId);
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 编辑店铺信息
     */
    public function saveStore(){

        //获取提交的数据
        $token     = $this->getRequest('token','');

        $faceImg    = $this->getRequest('faceImg','');
        $wechatPic  = $this->getRequest('wechatPic','');
        $bannerPic  = $this->getRequest('bannerPic','');

        $coordinate = $this->getRequest('coordinate','');
        $longitude  = $this->getRequest('longitude','');
        $latitude   = $this->getRequest('latitude','');
        $address    = $this->getRequest('address','');

        $major   = $this->getRequest('major','');
        $linkMan = $this->getRequest('linkMan','');
        $info    = $this->getRequest('info','');
        $phones  = $this->getRequest('phones','');
        $qqs     = $this->getRequest('qqs','');
        $tels    = $this->getRequest('tels','');

        if($token){

            if($faceImg&&$longitude&&$major&&$linkMan&&$phones&&$qqs&&$tels){

                $userMo = model('api.sev.user','mysql');
                $return = $userMo ->loginIs($token);
                //用户数据请求成功
                if($return['status']==200){

                    $userId = $return['data']['id'];

                    $data = array();

                    $data['info']     = $info;
                    $data['address']  = $address;

                    $data['coordinate'] = $coordinate;
                    $data['longitude']  = $longitude;
                    $data['latitude']   = $latitude;
                    $data['face_pic']   = $faceImg;
                    $data['wechat_pic'] = $wechatPic;
                    $data['major']   = $major;
                    $data['linkMan'] = $linkMan;
                    $data['linkPhone']  = $phones;
                    $data['linkTel']  = $tels;
                    $data['qq']  = $qqs;

                    $nowTime = date("Y-m-d H:i:s");
                    $data['update_time']  = $nowTime;

                    $return = $userMo ->saveStore($data,$userId,$bannerPic);
                }

            }else{
                $return = array('status'=>102,'msg'=>'提交数据有误，请检查后重试');
            }

        }else{

            $return = array('status'=>101,'msg'=>'您还未登录，登录后重试');

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 获取店铺车系 - 二级分类信息
     */
    public function getRange(){

        //获取提交的数据
        $token = $this->getRequest('token','');

        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);

            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $return = $userMo ->getRange($userId);
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));


    }

    /**
     * 获取店铺车系 - 四级分类信息 - 个人
     */
    public function getStoreSeries(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $cid   = $this->getRequest('cid','');
        if($token){

            if($cid){
                $userMo = model('api.sev.user','mysql');
                $return = $userMo ->loginIs($token);
                //用户数据请求成功
                if($return['status']==200){

                    $userId = $return['data']['id'];
                    $return = $userMo ->getStoreSeries($userId,$cid);


                }
            }else{
                $return = array('status'=>102,'msg'=>'提交数据有误，请检查后重试');
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));


    }

    /**
     * 保存四级车系
     */
    public function saveRange(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $ranges= $this->getRequest('ranges','');
        if($token){
            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                $userId = $return['data']['id'];
                $return = $userMo ->saveRange($userId,$ranges);
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 获取店铺产品
     */
    public function getProducts(){

        //获取提交的数据
        $token    = $this->getRequest('token','');
        $pro_type = $this->getRequest('pro_type','');
        $pro_cate_1 = $this->getRequest('pro_cate_1','');
        $pro_cate_2 = $this->getRequest('pro_cate_2','');
        $pro_status = $this->getRequest('pro_status','');
        $keyword  = $this->getRequest('keyword','');
        $page     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');

        if($token){
            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                $userId = $return['data']['id'];
                model('api.sev.salesmanSouCang','mysql')->resetRefresh($userId);  //统计支付表刷新点，修改产品当日刷新点

                $return = $userMo ->getProducts($userId,$pro_type,$pro_cate_1,$pro_cate_2,$pro_status,$keyword,$page,$pageSize);
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 产品下架
     */
    public function productOffSale(){

        //获取提交的数据
        $token      = $this->getRequest('token','');
        $productId  = $this->getRequest('productId','');

        if($token&&$productId){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];

                $return = $userMo ->productOffSale($userId,$productId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 产品上架
     */
    public function productSale(){

        //获取提交的数据
        $token      = $this->getRequest('token','');
        $productId  = $this->getRequest('productId','');

        if($token&&$productId){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];

                $return = $userMo ->productSale($userId,$productId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 产品删除
     */
    public function delProduct(){

        //获取提交的数据
        $token      = $this->getRequest('token','');
        $productId  = $this->getRequest('productId','');

        if($token&&$productId){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];

                $return = $userMo ->delProduct($userId,$productId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 获取产品刷新点
     */
    public function getRefresh(){
        //获取提交的数据
        $token      = $this->getRequest('token','');
        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取
                $userId = $return['data']['id'];
                $return = $userMo ->getRefresh($userId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 刷新一条产品
     */
    public function refreshProduct(){

        //获取提交的数据
        $token      = $this->getRequest('token','');
        $productId  = $this->getRequest('productId','');

        if($token&&$productId){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                $userId = $return['data']['id'];
                //获取刷新点
                $return = $userMo ->getRefresh($userId);
                if($return['status']==200){
                    writeLog(1111111111);
                    if($return['refresh_point'] && $return['refresh_point']>0){
                        $return = $userMo ->refreshProduct($productId,$userId);
                    }else{
                        $return = array('status'=>102,'msg'=>'刷新点不足，操作失败');
                    }

                }

            }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 企业名片
     */
    public function getCardInfo(){

        //获取提交的数据
        $token      = $this->getRequest('token','');
        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                $userId = $return['data']['id'];
                $return = $userMo ->getCardInfo($userId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登陆后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 企业名片
     */
    public function getCardInfoByEnterpriseID(){

        //获取提交的数据
        $EnterpriseID      = $this->getRequest('EnterpriseID','');
        if($EnterpriseID){
            $userMo = model('api.sev.user','mysql');
             $return = $userMo ->getCardInfoByEnterpriseID($EnterpriseID);
        }else{
            $return = array('status'=>101,'msg'=>'提交信息有误');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 企业名片模板信息
     */
    public function getCardTplInfo(){

        //获取提交的数据
        $token      = $this->getRequest('token','');
        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                $userId = $return['data']['id'];
                $return = $userMo ->getCardTplInfo($userId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登陆后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 创建名片
     */
    public function ceartCard(){
        $token  = $this->getRequest('token','');
        if($token){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                $userId = $return['data']['id'];

                $base64 = $this->getRequest('base64','');
                $data   = $this->getRequest('data','');
                $result = $userMo->base64Save($base64);
                if($result['status'] == 200){
                    $data['path'] = $result['path'];        //保存的图片路径
                    $return = $userMo->ceartCard($data,$userId);
                }else{
                    $return = $result;
                }
            }

        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登陆后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 返回所有修理厂坐标
     */
    public function getAllZuoBiao(){
        $classification   = $this->getRequest('classification','');
        $wordsKey = $this->getRequest('searchKey','');
        $province = $this->getRequest('province','');
        $city     = $this->getRequest('city','');
        $district = $this->getRequest('district','');
        $userMo = model('api.sev.user','mysql');

        $data = $userMo->getAllZuoBiao($classification,$wordsKey,$province,$city,$district);
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 查询所有汽修厂店铺信息
     */
    public function showCarMend(){
        $page = $this->getRequest('page',1);
        $pageSize = $this->getRequest('pageSize',10);
        $wordsKey = $this->getRequest('searchKey','');
        $province = $this->getRequest('province','');
        $city     = $this->getRequest('city','');
        $district = $this->getRequest('district','');
        $classification = $this->getRequest('classification','');
        $lat = $this->getRequest('lat','');
        $lng = $this->getRequest('lng','');

        $userMo = model('api.sev.user','mysql');

        $data = $userMo->showCarMend($page,$pageSize,$wordsKey,$province,$city,$district,$classification,$lat,$lng);
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 修改业务员密码
     */
    public function changeYeWuPwd(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        if($token) {
            $data = [];
            $data['password'] = $this->getRequest('oldPwd', '');
            $data['newPwd'] = $this->getRequest('newPwd', '7777777');
            $repPassword['repPassword'] = $this->getRequest('repPassword', '1111111');
            if ($data['newPwd'] && $repPassword['repPassword']) {
                if ($data['newPwd'] == $repPassword['repPassword']) {
                    $rst = model('api.sev.user')->yeWuYuanYanZheng($token, $data['password']);  //查看密码是否正确
                    if ($rst) {
                        $edit = model('api.sev.user')->editPasswordYeWu($token, $repPassword['repPassword']);
                        if ($edit > 0) {
                            $return = array('status' => 200, 'msg' => '密码修改成功');
                        } else {
                            $return = array('status' => 109, 'msg' => '修改密码失败，请重试');
                        }
                    } else {
                        $return = array('status' => 108, 'msg' => '旧密码填写不正确');
                    }
                } else {
                    $return = array('status' => 107, 'msg' => '请将两次密码填写一致');
                }
            } else {
                $return = array('status' => 106, 'msg' => '请将修改密码填写完整');
            }
        }else{
            $return = array('status' => 105, 'msg' => '数据丢失，操作失败');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 收藏店铺
     */
    public function collectFirms(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $firmsId = $this->getRequest('firmsId','');
        $type   = $this->getRequest('type','1');

        $msg = $type==1?'收藏':'取消收藏';

        if($token){

            if($this->user['status']==200){

                $id = $this->user['data']['id'];
                $userType = $this->userType;

                $collectMo = model('web.collect','mysql');
                $proInfo = $collectMo->table('firms')->where(array('id'=>$firmsId));
                if($proInfo){
                    $res = $collectMo->collectStore($userType,$id,$firmsId,$type);
                    if($res){
                        $return = array('status'=>200,'msg'=>$msg.'成功');
                    }else{
                        $return = array('status'=>104,'msg'=>$msg.'失败，请检查后重试');
                    }
                }else{
                    $return = array('status'=>103,'msg'=>$msg.'失败，请检查后重试');
                }
            }else{
                $return = array('status'=>102,'msg'=>$msg.'失败，请检查后重试');
            }
        }else{
            $return = array('status'=>101,'msg'=>$msg.'失败，请先登录');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 获取收藏的厂商
     */
    public function getMyCollectStore(){
            //用户数据请求成功
            if($this->user['status']==200){
                $id = $this->user['data']['id'];
                $userType = $this->userType;
                $keywords       = $this->getRequest('keyword','');
                //经销商：1.轿车商家 2.货车商家 3.物流货运
                $classification  = $this->getRequest('classification','');
                $cate_1 = $this->getRequest('cate_1',0);
                $cate_2 = $this->getRequest('cate_2',0);
                $page   = $this->getRequest('page',1);
                $pageSize = $this->getRequest('pageSize',10);

                if($cate_2){
                    $business = $cate_2;
                    $categorise     = array();
                }else{
                    if($classification){
                        $business = 0;
                        $cateMo = model('web.category','mysql');
                        $cate   = $cateMo->getCarCateChild($classification ,$cate_1);
                        $categorise = array(0);
                        foreach ($cate as $v){
                            $categorise[] = $v['id'];
                        }
                    }else{
                        $business = 0;
                        $categorise = array();
                    }
                }
                $collectMo = model('web.collect','mysql');
                $return = $collectMo->collectStoreList($userType,$id,$classification,$business,$categorise,$keywords,$page,10);
                $return['status'] = 200;
            }else{
                $return = array('list'=>array(),'page'=>1,'count'=>0,'pageSize'=>10,'status'=>200);
            }
            exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 获取收藏的产品
     */
    public function getCollectProduct(){
        //用户数据请求成功
        if($this->user['status']==200){
            $id = $this->user['data']['id'];
            $userType = $this->userType;
            $pro_type   = $this->getRequest('type','');
            $pro_cate_1 = $this->getRequest('cate_1','');
            $pro_cate_2 = $this->getRequest('cate_2','');
            $keyword    = $this->getRequest('keywords','');
            $page       = $this->getRequest('page',1);

            $collectMo = model('web.collect','mysql');
            $return = $collectMo->collectProductList($userType,$id,$pro_type,$pro_cate_1,$pro_cate_2,$keyword,$page,10);
            $return['status'] = 200;
        }else{
            $return = array('list'=>array(),'page'=>1,'count'=>0,'pageSize'=>10,'status'=>200);
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     *访问记录
     */
    public function visitLog(){
        $firmsId = $this->getRequest('firmsId','');
        $token   = $this->getRequest('token','');
        //写入访问记录
        if($firmsId&&$token){
            //if($this->user){
            if($this->user['data']['id']!=$firmsId){
                $logMo = model('web.log','mysql');
                $logMo->visitToLog($this->user['data']['id'],$firmsId,2);
            }
            //}
        }
    }


    /**
     *拨打记录
     */
    public function callLog(){
        $firmsId = $this->getRequest('firmsId','');
        $token   = $this->getRequest('token','');
        $callType= $this->getRequest('callType','1');
        $plat    = $this->getRequest('phoneType','3');
        //写入拨打记录
        if($firmsId&&$token){
            //if($this->user){
            if($this->user['data']['id']!=$firmsId){
                $userType = $this->userType;
                $userMo   = model('api.sev.log','mysql');
                $userMo->callToLog($this->user['data']['id'],$firmsId,$callType,$userType,$plat);
            }
            //}
        }
    }

}