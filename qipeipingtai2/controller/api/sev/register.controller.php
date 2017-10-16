<?php
/**
 *
 * 人员注册
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/26
 * Time: 22:38
 */

class ApiSevRegisterController extends Controller{

    //注册第一步
    public function registerOne(){
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
                        $return['status'] = 104;
                        $return['msg']    = '该手机号已被注册，请检查后重试';
                    } else {
                        $return['status'] = 200;
                        $return['msg']    = '验证通过，前往密码设置';
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
            $return['msg']    = '请输入您要注册的手机号码';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    //注册第二步
    public function registerTwo(){
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
                        $return['status'] = 104;
                        $return['msg']    = '该手机号已被注册，请检查后重试';
                    } else {
                        $return = $registerMo->register($phone,$pwd);
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

    //保存企业信息
    public function saveMaterial(){

        $firmsId        = $this->getRequest('firmsId','');   //公司id

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

        if($firmsId){
            if($companyType){
                if($classification){

                    if($classification==1){
                        if(empty($business)){
                            $return = array('status' => 2, 'msg' => '请选择经营范围');
                            exit(json_encode($return,JSON_UNESCAPED_UNICODE));
                        }
                    }else{
                        $business = '';
                    }

                    if($companyname){
                        if($province && $city){
                            if($longitude && $latitude){
                                if($address){
                                    $registerMo = model('api.sev.register','mysql');

                                        //检查之前是否已经保存过
                                        $companyInfo = $registerMo->table('firms')->where(array('id'=>$firmsId))->getOne();

                                        if($companyInfo['companyname']){
                                            $userToken  = authcode($firmsId,'ENCODE');
                                            //记录日志
                                            $registerMo->table('firms_login_log')->insert(array('firm_id'=>$firmsId,'create_time'=>date('Y-m-d H:i:s',time())));

                                            $return = array('status' => 200, 'msg' => '正在为您登录','token'=>$userToken);
                                        }else{
                                            $invite_code = $registerMo->makeYQ();
                                            $data = array(
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
                                                'update_time'=>date('Y-m-d H:i:s',time()),
                                                'status'=>1,
                                                'is_vip'=>2,
                                                'is_check'=>2,
                                                'refresh_point'=>0,
                                                'is_showfactry'=>2,
                                                'invite_code'=>$invite_code
                                            );
                                            if($companyInfo['EnterpriseID']){
                                                $data['QR_pic'] = model('web.firms','mysql')->getQRStore($companyInfo['EnterpriseID'],$companyname,$companyType);
                                            }

                                            $return = $registerMo->saveMaterial($data,$firmsId);
                                            if($return['status']===200){
                                                $msgMo=model('web.msg','mysql');
                                                $msgMo->toSaveMsg(2,$companyInfo['EnterpriseID'],'“'.$data['companyname'].'” 入驻成功',0,0,$city);
                                            }
                                        }

                                }else{
                                    $return = array('status' => 102, 'msg' => '请输入企业详细地址');
                                }
                            }else{
                                $return = array('status' => 102, 'msg' => '请获取企业坐标');
                            }
                        }else{
                            $return = array('status' => 102, 'msg' => '请选择所属地区');
                        }
                    }else{

                        $return = array('status' => 102, 'msg' => '请输入企业名称');
                    }
                }else{
                    $return = array('status' => 102, 'msg' => '请选择企业分类');
                }
            }else{
                $return = array('status' => 102, 'msg' => '请选择企业类型');
            }
        }else{
            $return['status'] = 101;
            $return['msg']    = '提交数据有误，请重试';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }



    //获取手机短信验证码
    public function sendCode(){
        $phone = $this->getRequest('phone' , '');
        if($phone){
            //验证手机号
            if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
                //注册模型
                $registerMo = model('api.sev.register','mysql');
                $return = $registerMo->sendCode($phone);
            }else{
                $return['status'] = 102;
                $return['msg']    = '您的手机号码输入有误，请检查后重试';
            }
        }else{
            $return['status'] = 101;
            $return['msg']  = '手机号码输入有误';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 验证厂商注册号码是否已被使用
     */
    public function isUseTel(){
        $tel = $this->getRequest('tel','');
        if($tel){
            $rst = model('api.sev.register')->isUseTel($tel);
            if($rst){
                $return['status'] = 102;
                $return['msg']  = '该手机号码已被注册';
            }else{
                $return['status'] = 200;
                $return['msg']  = '该手机号码未被注册';
            }
        }else{
            $return['status'] = 101;
            $return['msg']  = '获取手机号码失败';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 业务员注册厂商
     */
    public function yeWuCompany(){
        $data = [];
        $data['type']            = $this->getRequest('companyType','');                 //企业类型
        $data['classification'] = $this->getRequest('classification','');             //企业分类
        $data['business']        = $this->getRequest('business','');                   //经营范围
        $data['companyname']     = $this->getRequest('companyname','');               //企业名称
        $data['province']        = $this->getRequest('province','');                  //省
        $data['city']            = $this->getRequest('city','');                       //市
        $data['district']        = $this->getRequest('district','');                  //区
        $data['address']         = $this->getRequest('address','');                   //详细地址
        $data['coordinate']      = $this->getRequest('coordinate','');               //坐标名称
        $data['longitude']       = $this->getRequest('longitude','');                //经度
        $data['latitude']        = $this->getRequest('latitude','');                 //纬度
        $data['face_pic']        = $this->getRequest('face_pic','');                 //封面图片
        $data['major']           = $this->getRequest('major','');                     //主营
        $data['linkMan']         = $this->getRequest('linkMan','');                  //联系人
        $data['linkPhone']       = $this->getRequest('linkPhone','');                //联系人手机号码
        $data['linkTel']         = $this->getRequest('linkTel','');                  //联系人座机号码
        $data['qq']               = $this->getRequest('qq','');                       //联系人QQ
        $data['scale']            = $this->getRequest('scale','');                   //企业规模
        $data['phone']            = $this->getRequest('phone','');                   //厂商电话
        if(!$data['type'] || !$data['classification'] || !$data['companyname'] || !$data['province'] || !$data['city'] || !$data['district'] || !$data['longitude'] || !$data['latitude'] || !$data['address']){
            $return = array('status'=>101,'msg'=>'注册信息填写不完整，请重新填写');
        }else{
            if($data['type']==1 && !$data['business'] || $data['type']==2 && !$data['scale']){
                $return = array('status'=>102,'msg'=>'注册信息填写不完整，请重新填写');
            }else{
                $token = $this->getRequest('token','');
                if(!$token){
                    $return = array('status'=>103,'msg'=>'数据丢失，请重试');
                }else{
                    $return = model('api.sev.register')->yeWuCompany($data,$token);
                }
            }
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


}