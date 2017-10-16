<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/14
 * Time: 18:11
 */

class LoginController extends Controller
{
    //登陆
    public function index(){
        $loginMo = model('web.login','mysql');
        $user = $loginMo->loginIs(false);
        if($user){
            controller('def','index');
        }else{
            //dump(1);
            $last_page = cookie('last_page');
            $accounts  = isset($_COOKIE['remember']) ? $_COOKIE['remember'] : '' ;
            $this->assign('accounts',$accounts) ;
            $this->assign('last_page',$last_page) ;
            $this->template('pc/login/login');
        }

    }
    //登陆
    public function login(){
        $phone    = $this->getRequest('phone','');
        $password = $_POST['password'];
        $code     = $this->getRequest('code'    , '');
        $remember = $this->getRequest('remember', '');
        $loginMo = model('web.login','mysql');
        $rst = $loginMo->code($code);
        if($rst['massageCode'] === 'success'){
            if($phone && $password){
                //if(preg_match("/^1[34578]{1}\d{9}$/",$phone)) {
                    $res = $loginMo->doLogin($phone,$password);
                    if($res['massageCode'] === 'success' ){
                        if($remember=='true'){
                            cookie('remember_F',$phone);
                        }else{
                            cookie('remember_F','');
                        }
                        $return = array('status'=>1,'msg'=>'登录成功');
                    }else{
                        $return = array('status'=>2,'msg'=>$res['massage']);
                    }
                //}else{
                //    $return = array('status'=>2,'msg'=>'请输入正确的手机号');
                //}
            }else{
                $return = array('status'=>2,'msg'=>'请刷新页面稍后重试');
            }
        }else{
            $return = array('status'=>2,'msg'=>'验证码错误请重新输入');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //登出
    public function logout(){
        cookie('userToken_F','');
        header("Location:/login");
    }

    //登出
    public function logout2(){
        cookie('userToken_F','');
        $return = array('massageCode'=>'success','msg'=>'退出成功');
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //注册
    public function register(){
        $step = $this->getRequest('step','one');
        $phone = $this->getRequest('phone','');
        switch ($step){
            case 'one'://注册步骤一
                if($phone){
                    $code= $this->getRequest('code','');
                    //验证手机号
                    if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
                        $loginMo = model('web.login','mysql');
                        $rst      = $loginMo->code($code);
                        //检测验证码
                        if($rst['massageCode'] === 'success') {
                            $res = $loginMo->checkPhone($phone);
                            //检测手机号是否存在
                            if ($res) {
                                $this->assign('msg', '手机号已存在');
                            } else {
                                $articleMo = model('web.article','mysql');
                                $xieyi = $articleMo->getFuWuXieYi();
                                $this->assign('xieyi', $xieyi);
                                $this->assign('phone', $phone);
                                $this->template('pc.login.registerTwo');
                                exit;
                            }
                        }else{
                            $this->assign('msg', $rst['massage']);
                        }
                    }else{
                        $this->assign('msg','请输入正确的手机号');
                    }
                }
                $this->assign('phone', $phone);
                $this->template('pc.login.registerOne');
                break;
            case 'two'://注册步骤二
                if($phone){
                    $password   = $this->getRequest('password','7777777');
                    $repassword = $this->getRequest('repassword','1111111');
                    $smsCode = $this->getRequest('smsCode','');
                    $len = strlen($password);
                    if($smsCode){
                        //判断密码长度
                        if($len>=6 && $len<=16){
                            //两次密码是否相同
                            if($password===$repassword){
                                $loginMo = model('web.login','mysql');
                                //短信验证码是否正确
                                $res = $loginMo->checkSmsCode($smsCode,$phone);
                                if($res['status']){
                                    //获取车系
                                    $cateMo = model('web.category');
                                    //轿车商家
                                    $car_cate['cate_1'] = $cateMo->getCarCateByLevel(1,1);//轿车 一级分类
                                    $car_cate['cate_2'] = $cateMo->getCarCateByLevel(1,2);//轿车 二级分类
                                    //货车商家
                                    $van_cate['cate_1'] = $cateMo->getCarCateByLevel(2,1);//货车 一级分类
                                    $van_cate['cate_2'] = $cateMo->getCarCateByLevel(2,2);//货车 二级分类
                                    //物流运输
                                    $tra_cate['cate_1'] = $cateMo->getCarCateByLevel(3,1);//物流 一级分类
                                    $tra_cate['cate_2'] = $cateMo->getCarCateByLevel(3,2);//物流 二级分类

                                    $this->assign('car_cate',$car_cate);
                                    $this->assign('van_cate',$van_cate);
                                    $this->assign('tra_cate',$tra_cate);
                                    $this->assign('phone',$phone);
                                    $this->assign('psd',$password);
                                    $this->template('pc.login.registerThree');
                                    exit;
                                }else{
                                    $this->assign('msg',$res['msg']);
                                }
                            }else{
                                $this->assign('msg','两次输入的密码不一致');
                            }
                        }else{
                            $this->assign('msg','请输入6至16位的数字，字母或符号');
                        }
                    }else{
                        $this->assign('msg','请输入短信验证码');
                    }


                    $articleMo = model('web.article','mysql');
                    $xieyi = $articleMo->getFuWuXieYi();
                    $this->assign('xieyi', $xieyi);
                    $this->assign('phone',$phone);
                    $this->assign('smsCode',$smsCode);
                    $this->template('pc.login.registerTwo');
                }else{
                    header("Location:/login/register?step=one");
                    exit;
                }
                break;
            case 'three'://注册步骤三,数据处理
                //$password = $this->getRequest('passwd','');
                $password = $_POST['password'];
                if($phone && $password){
                    $companyType    = $this->getRequest('companyType','');   //公司类型
                    $classification = $this->getRequest('classification','');//企业分类
                    $business       = $this->getRequest('business','');//经营范围
                    $companyname    = $this->getRequest('companyname','');//企业名称
                    $province       = $this->getRequest('province','');
                    $city           = $this->getRequest('city','');
                    $district       = $this->getRequest('district','');
                    $address        = $this->getRequest('address','');
                    $coordinate     = $this->getRequest('coordinate','');
                    $longitude      = $this->getRequest('longitude','');
                    $latitude       = $this->getRequest('latitude','');
                    $face_pic       = $this->getRequest('face_pic',''); //封面
                    $major          = $this->getRequest('major','');    //主营
                    $linkMan        = $this->getRequest('linkMan','');  //联系人
                    $linkPhone      = $this->getRequest('linkPhone','');//手机
                    $linkTel        = $this->getRequest('linkTel','');  //座机
                    $qq             = $this->getRequest('qq','');       //QQ

                    if(preg_match("/^1[34578]{1}\d{9}$/",$phone)) {
                        $return = array('status' => 2, 'msg' => '创建厂商失败，请重试');
                        $loginMo = model('web.login', 'mysql');
                        $check = $loginMo->checkPhone($phone);
                        if($check){
                            $return = array('status' => 1, 'msg' => '该账号已注册，请登录');
                        }else{
                            if($companyType){
                                if($classification){
                                    if($classification==1){
                                        if(!$business){
                                            $return = array('status' => 2, 'msg' => '请选择经营范围');
                                            exit(json_encode($return,JSON_UNESCAPED_UNICODE));
                                        }
                                    }else{
                                        $business = '';
                                    }

                                    if($companyname){
                                        if($province && $city && $district){
                                            if($longitude && $latitude){
                                                if($address){
                                                    $data = array(
                                                                 'phone'=>$phone,
                                                                 'uname'=>$companyname,
                                                          'EnterpriseID'=>$loginMo->makeID(time()),
                                                              'password'=>$loginMo->psdToEn($password),
                                                                  'type'=>$companyType,
                                                        'classification'=>$classification,
                                                              'business'=>$business,
                                                           'companyname'=>$companyname,
                                                              'province'=>$province,
                                                                  'city'=>$city,
                                                              'district'=>$district,
                                                               'address'=>$address,
                                                            'coordinate'=>$coordinate,
                                                             'longitude'=>$longitude,
                                                              'latitude'=>$latitude,
                                                             'face_pic '=>$face_pic ,
                                                                 'major'=>$major,
                                                               'linkMan'=>$linkMan,
                                                             'linkPhone'=>$linkPhone,
                                                               'linkTel'=>$linkTel,
                                                                    'qq'=>$qq,
                                                           'create_time'=>date('Y-m-d H:i:s',time()),
                                                                'status'=>1,
                                                                'is_vip'=>2,
                                                              'is_check'=>1,
                                                         'refresh_point'=>0,
                                                         'is_showfactry'=>2,
                                                           'invite_code'=>$loginMo->makeYQ()

                                                    );

                                                    $data['QR_pic'] = model('web.firms','mysql')->getQRStore($data['EnterpriseID'],$companyname,$companyType);

                                                    $res = $loginMo->createNewFirm($data);
                                                    if($res){
                                                        $msgMo=model('web.msg','mysql');
                                                        $msgMo->toSaveMsg(2,$data['EnterpriseID'],'“'.$data['companyname'].'”入驻成功',0,0,$city);
                                                        $return = array('status' => 1, 'msg' => '创建成功，请登陆');
                                                    }else{
                                                        $return = array('status' => 2, 'msg' => '创建失败，请稍后重试');
                                                    }
                                                }else{
                                                    $return = array('status' => 2, 'msg' => '请输入企业详细地址');
                                                }
                                            }else{
                                                $return = array('status' => 2, 'msg' => '请获取企业坐标');
                                            }
                                        }else{
                                            $return = array('status' => 2, 'msg' => '请选择所属地区');
                                        }
                                    }else{
                                        $return = array('status' => 2, 'msg' => '请输入企业名称');
                                    }
                                }else{
                                    $return = array('status' => 2, 'msg' => '请选择企业分类');
                                }
                            }else{
                                $return = array('status' => 2, 'msg' => '请选择企业类型');
                            }
                        }
                    }else{
                        $return = array('status' => 3, 'msg' => '请重新注册');
                    }
                    exit(json_encode($return,JSON_UNESCAPED_UNICODE));
                }else{
                    header("Location:/login/register?step=one");
                    exit;
                }
                break;
            default:
                header("Location:/login/register?step=one");
                exit;
        }
    }

    //忘记密码
    public function forget(){
        $step = $this->getRequest('step','one');
        $phone = $this->getRequest('phone','');
        switch ($step){
            case 'one':
                if($phone){
                    $code= $this->getRequest('code','');
                    //验证手机号
                    if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
                        $loginMo = model('web.login','mysql');
                        $rst      = $loginMo->code($code);
                        //检测验证码
                        if($rst['massageCode'] === 'success') {
                            $res = $loginMo->checkPhone($phone);
                            //检测手机号是否存在
                            if ($res) {
                                $this->assign('phone', $phone);
                                $this->template('pc.login.forgetTwo');
                                exit;
                            } else {
                                $this->assign('msg', '手机号不存在');
                            }
                        }else{
                            $this->assign('msg', $rst['massage']);
                        }
                    }else{
                        $this->assign('msg','请输入正确的手机号');
                    }
                }
                $this->assign('phone',$phone);
                $this->template('pc.login.forgetOne');
                break;
            case 'two':
                $return = array('status'=>2,'msg'=>'请稍后重试');
                if($phone) {
                    if(preg_match("/^1[34578]{1}\d{9}$/",$phone)) {
                        $password = $this->getRequest('password', '7777777');
                        $repassword = $this->getRequest('repassword', '1111111');
                        $smsCode = $this->getRequest('smsCode', '');
                        $len = strlen($password);
                        if ($smsCode) {
                            //判断密码长度
                            if ($len >= 6 && $len <= 16) {
                                //两次密码是否相同
                                if ($password === $repassword) {
                                    $loginMo = model('web.login', 'mysql');
                                    //短信验证码是否正确
                                    $res = $loginMo->checkSmsCode($smsCode,$phone);
                                    if ($res['status']) {
                                        $rst = $loginMo->changePassword($phone,$password);
                                        if($rst){
                                            $return = array('status' => 1, 'msg' => '密码修改成功，请登录');
                                        }else{
                                            $return = array('status' => 2, 'msg' => '密码修改失败，请稍后再试');
                                        }

                                    } else {
                                        $return = array('status' => 2, 'msg' => $res['msg']);
                                    }
                                } else {
                                    $return = array('status' => 2, 'msg' => '两次输入的密码不一致');
                                }
                            } else {
                                $return = array('status' => 2, 'msg' => '请输入6至16位的数字，字母或符号');
                            }
                        } else {
                            $return = array('status' => 2, 'msg' => '请输入短信验证码');
                        }
                    }
                }
                exit(json_encode($return,JSON_UNESCAPED_UNICODE));
                break;
            default:
                header("Location:/login/forget?step=one");
        }
    }

    public function sendSmsCode(){
        $phone = $this->getRequest('phone','');
        if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
            $mo  = model('api.sev.register','mysql');
            $res = $mo->sendCode($phone);
            if($res['status']=='200'){
                $return = array('status'=>1,'msg'=>'验证码发送成功','time'=>$res['time']);
            }elseif($res['status']=='300'){
                $return = array('status'=>0,'msg'=>'验证码已发送','time'=>$res['time']);
            }else{
                $return = array('status'=>2,'msg'=>'验证码发送失败','time'=>60);
            }


        }else{
            $return = array('status'=>2,'msg'=>'手机号不正确');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
}