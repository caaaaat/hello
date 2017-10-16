<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/22
 * Time: 11:47
 */
class PcFirmController extends Controller
{
    private $user = array();

    public function __construct()
    {
        $loginMo    = model('web.login','mysql');
        $this->user = $user = $loginMo->loginIs(false);
    }
    //修改厂商昵称
    public function changeUname(){
        $return = array('status'=>2,'msg'=>'请刷新页面，稍后重试');
        if($this->user){
            $id = $this->user['id'];
            $data['uname']    = $this->getRequest('uname','');
            $data['head_pic'] = $this->getRequest('head_pic','');
            $firmsMo = model('web.firms','mysql');
            $res = $firmsMo->changeFirm($id,$data);
            if($res){
                $return = array('status'=>1,'msg'=>'修改成功');
            }else{
                $return = array('status'=>2,'msg'=>'修改失败，稍后重试');
            }
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //修改厂商密码
    public function changePwd(){
        $return = array('status'=>2,'msg'=>'请刷新页面，稍后重试');
        if($this->user){
            $id = $this->user['id'];
            $oldPassword = $this->getRequest('oldPassword','');
            $newPassword = $this->getRequest('newPassword','7777777');
            $repPassword = $this->getRequest('repPassword','1111111');
            $firmsMo = model('web.firms','mysql');
            $res     = $firmsMo->getFirmInfo($id);
            $loginMo = model('web.login', 'mysql');
            $psd     = $loginMo->psdToEn($oldPassword);
            if($res['password']===$psd){
                $len = strlen($newPassword);
                //判断密码长度
                if($len>=6 && $len<=16){
                    //两次密码是否相同
                    if($newPassword===$repPassword){
                        $newPsd  = $loginMo->psdToEn($newPassword);
                        if($res['password']===$newPsd){
                            $return = array('status'=>2,'msg'=>'新密码与原密码一致');
                        }else{
                            $rst = $firmsMo->changeFirm($id,array('password'=>$newPsd));
                            if($rst){
                                $return = array('status'=>1,'msg'=>'修改成功');
                            }else{
                                $return = array('status'=>2,'msg'=>'密码修改失败');
                            }
                        }
                    }else{
                        $return = array('status'=>2,'msg'=>'两次输入的密码不一致');
                    }
                }else{
                    $return = array('status'=>2,'msg'=>'请输入6至16位的数字，字母或符号');
                }
            }else{
                $return = array('status'=>2,'msg'=>'原密码错误，请重新输入');
            }
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
                $rst      = $loginMo->checkSmsCode($smsCode,$phone);
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
    //获取商家
    public function getFirms(){
        $keywords       = $this->getRequest('keyword','');
        //1经销商 2修理厂
        $type           = $this->getRequest('type','');
        //企业分类 (和type字段相关)
        //经销商：1.轿车商家 2.货车商家 3.用品商家
        //汽修厂：4.修理厂    5.快修保养 6.美容店
        $classification = $this->getRequest('type_2','');
        $cate_1 = $this->getRequest('car_cate_1',0);
        $cate_2 = $this->getRequest('car_cate_2',0);
        //经营范围
        //$business       = $this->getRequest('van_cate','');
        $page           = $this->getRequest('page',1);
        $pageSize       = $this->getRequest('pageSize',3);

        if($cate_2){
            $business = $cate_2;
            $categorise     = array();
        }else{
            if($cate_1){
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
        //当前城市
        $currentCity = cookie('currentCity')?cookie('currentCity'):'成都市';

        //获取商家
        $firmMo = model('web.firms','mysql');
        $firms  = $firmMo->getFirms($type,$classification,$business,$categorise,$keywords,$page,$pageSize,$currentCity);

        exit(json_encode($firms,JSON_UNESCAPED_UNICODE));
    }
    //绑定邀请人
    public function bingInvite(){
        if($this->user){
            $inviteCoder = $this->getRequest('coder','');
            $firmMo = model('web.firms','mysql');
            if($inviteCoder){
                $return = $firmMo->bindInviteId($this->user['id'],$inviteCoder);
            }else{
                $return = array('status'=>0,'msg'=>'请输入邀请码');
            }
        }else{
            $return = array('status'=>0,'msg'=>'还没有登录，请刷新重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //收藏店铺
    public function collectStore(){
        if($this->user){
            $firmId = $this->getRequest('storeId','');
            $type   = $this->getRequest('type','');
            $firmMo = model('web.firms','mysql');
            $firmInfo = $firmMo->getFirmInfo($firmId);
            if($firmInfo){
                $collectMo = model('web.collect','mysql');
                $res = $collectMo->collectStore(1,$this->user['id'],$firmId,$type);
                if($res){
                    $return = array('status'=>1,'msg'=>'操作成功');
                }else{
                    $return = array('status'=>2,'msg'=>'操作失败，请刷新重试');
                }
            }else{
                $return = array('status'=>2,'msg'=>'厂商不存在，请刷新重试');
            }
        }else{
            $return = array('status'=>0,'msg'=>'还没有登录，请先登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //收藏店铺
    public function collectProduct(){
        if($this->user){
            $productId = $this->getRequest('productId','');
            $type   = $this->getRequest('type','');
            $collectMo = model('web.collect','mysql');
            $proInfo = $collectMo->table('product_list')->where(array('id'=>$productId));
            if($proInfo){
                $res = $collectMo->collectProduct(1,$this->user['id'],$productId,$type);
                if($res){
                    $return = array('status'=>1,'msg'=>'操作成功');
                }else{
                    $return = array('status'=>2,'msg'=>'操作失败，请刷新重试');
                }
            }else{
                $return = array('status'=>2,'msg'=>'产品不存在，请刷新重试');
            }
        }else{
            $return = array('status'=>0,'msg'=>'还没有登录，请先登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //访问日志写入
    public function visitLog(){
        $firmsId = $this->getRequest('storeId','');
        $type    = $this->getRequest('type','');
        //写入访问记录
        if($firmsId&&$type){
            if($this->user){
                if($this->user['id']!=$firmsId){
                    $logMo = model('web.log','mysql');
                    $logMo->visitToLog($this->user['id'],$firmsId,$type);
                }
            }
        }

    }
    //获取收藏的厂商
    public function getMyCollectStore(){
        if($this->user){
            $keywords       = $this->getRequest('keyword','');
            //经销商：1.轿车商家 2.货车商家 3.用品商家
            $classification  = $this->getRequest('type','');
            $cate_1 = $this->getRequest('cate_1',0);
            $cate_2 = $this->getRequest('cate_2',0);
            $page   = $this->getRequest('page',1);
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
            $return = $collectMo->collectStoreList(1,$this->user['id'],$classification,$business,$categorise,$keywords,$page,10);
        }else{
            $return = array('list'=>array(),'count'=>0,'page'=>1,'pageSize'=>10);
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 编辑店铺信息
     */
    public function editFirm(){
        $user = $this->user;
        $data = $this->getRequest('data','');
        $rst  = model('web.firms')->editFirm($data,$user['id']);
        if($rst > 0){
            $return['status'] = 1;
        }else{
            $return['status'] = 0;
            $return['msg'] = '操作失败';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 查询所有汽修厂店铺信息
     */
    public function showCarMend(){
        $user = $this->user;
        $page = $this->getRequest('page',1);
        $pageSize = $this->getRequest('pageSize',3);
        $wordsKey = $this->getRequest('wordsKey','');
        $province = $this->getRequest('province','');
        $city     = $this->getRequest('city','');
        $district = $this->getRequest('district','');
        $classification = $this->getRequest('classification','');
        $data = model('web.firms')->showCarMend($page,$pageSize,$wordsKey,$province,$city,$district,$classification);
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 查询所有汽修厂店铺信息
     */
    public function showCarMend2(){
        $user = $this->user;
        $page = $this->getRequest('page',1);
        $pageSize = $this->getRequest('pageSize',3);
        $wordsKey = $this->getRequest('wordsKey','');
        $province = $this->getRequest('province','');
        $city     = $this->getRequest('city','');
        $district = $this->getRequest('district','');
        $nowCity  = $this->getRequest('nowCityName','');
        $classification = $this->getRequest('classification','');
        $data = model('web.firms')->showCarMend2($page,$pageSize,$wordsKey,$province,$city,$district,$classification,$nowCity);
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 返回所有修理厂坐标
     */
    public function getAllZuoBiao(){
        $data = model('web.firms')->getAllZuoBiao();
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取消息
     */
    public function getMsg(){
        if($this->user){
            $page = $this->getRequest('page',1);
            $msgMo = model('web.msg','mysql');
            $data  = $msgMo->getMsg(1,$this->user['id'],$page,6);
        }else{
            $data = array('list'=>array());
        }
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }
    //消息读取
    public function doReadMsg(){
        if($this->user){
            $msgId = $this->getRequest('msgId','');
            if($msgId){
                model('web.msg','mysql')->readMsg($msgId,1,$this->user['id']);
            }
        }
    }

    public function bindSaleMan(){
        if($this->user){
            $uId = $this->getRequest('uId','');
            $firmMo = model('web.firms','mysql');
            $return = $firmMo->bindSaleUser($uId,$this->user['id']);
        }else{
            $return = array('status'=>3,'msg'=>'请刷新页面，重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
}