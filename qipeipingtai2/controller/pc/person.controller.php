<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/15
 * Time: 10:14
 */

class PcPersonController extends Controller
{
    private $user = array();
    private $keFuQQ = '';

    public function __construct()
    {
        $loginMo    = model('web.login','mysql');
        $this->user = $user = $loginMo->loginIs(true);
        $this->keFuQQ = $loginMo->getKeFuQQ();
    }
    /**
     * @param $htmlName
     * @param string $title
     * @param bool $loadSeach  是否加载搜索
     * @param bool $loadNav    是否加载导航
     */
    protected function main_template($htmlName,$title='个人中心'){
        $this->assign('fun',G('act'));
        $this ->assign('userInfo',$this->user);
        $this->assign('keFuQQ',$this->keFuQQ);
        $iniMo = model('web.ini','mysql');
        //城市配置表
        $cityIni = $iniMo->cityIni();
        $this->assign('cityIni',$cityIni);
        $currentCity =cookie('currentCity')?cookie('currentCity'):'成都市';
        $this->assign('currentCity',$currentCity);
        //电话和qq
        $qqTel = $iniMo->getQQ();
        $this->assign('qqTel',$qqTel);
        //友情链接
        $fLinks = $iniMo->getLinks();
        $this->assign('fLinks',$fLinks);

        $this->assign('title',$title);

        $this->template('pc/layout/head');
        $this->template('pc/layout/personNav');
        //主页面
        $this->template($htmlName);
        $this->template('pc/layout/personNavEnd');
        //尾部
        $this->template('pc/layout/footer');

    }

    /*个人基本资料*/
    public function index(){
        $this->main_template('pc/person/basics');
    }

    /*密码修改*/
    public function changePWD(){
        $this->main_template('pc/person/changePWD');
    }

    /*绑定手机第一步*/
    public function bindPhone(){
        $phone = isset($_POST['phone'])?$_POST['phone']:'';
        $code  = $this->getRequest('code','');
        $this->assign('phone','');
        if($phone){
            if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
                $loginMo = model('web.login','mysql');
                $rst      = $loginMo->code($code);
                //检测验证码
                if($rst['massageCode'] === 'success') {
                    //检查手机号是否存在
                    if($phone!=$this->user['phone']){
                        $res = $loginMo->checkPhoneNoIncludeId($this->user['id'],$phone);
                        if($res){
                            $this->assign('msg','该手机号已存在');
                        }else{
                            $this->assign('phone',$phone);
                            $this->main_template('pc/person/bindPhoneTwo');
                        }
                    }else{
                        $this->assign('msg','该号码是现绑定的手机号');
                    }
                }else{
                    $this->assign('phone',$phone);
                    $this->assign('msg','验证码错误');
                }
            }else{
                $this->assign('msg','请输入正确的手机号');
            }
        }
        $this->main_template('pc/person/bindPhone');
    }

    /*店铺信息*/
    public function shopInfo(){
        $firmsMo = model('web.firms','mysql');
        $shopInfo= $firmsMo->getFirmInfoByEnID($this->user['EnterpriseID']);
        $this->assign('shopInfo',$shopInfo);
        $this->main_template('pc/person/shopInfo');
    }

    /*编辑店铺信息*/
    public function shopEidt(){
        $firmsMo = model('web.firms','mysql');
        $shopInfo= $firmsMo->getFirmInfoByEnID($this->user['EnterpriseID']);
        if($shopInfo['linkPhone']){
            $shopInfo['linkPhone'] = explode(',',$shopInfo['linkPhone']);
        }
        if($shopInfo['linkTel']){
            $shopInfo['linkTel'] = explode(',',$shopInfo['linkTel']);
        }
        if($shopInfo['qq']){
            $shopInfo['qq'] = explode(',',$shopInfo['qq']);
        }
//        dump($shopInfo);die;
        $this->assign('shopInfo',$shopInfo);
        $this->main_template('pc/person/shopEidt');
    }

    /*店铺访问明细*/
    public function shopCareful(){
        $type = $this->getRequest('type',1);
        $page = $this->getRequest('page',1);
        $logMo = model('web.log','mysql');
        if($type==1){
            $res = $logMo->getFirmsToVisitLog($this->user['id'],$page);//来访记录
        }else{
            $res = $logMo->getFirmsToCallLog($this->user['id'],$page);  //来电记录
        }
        //分页工具
        $pageTool = model('tools.page','mysql');
        $pageHtml = $pageTool->pager($res['count'],$page,10,"type={$type}&");
        $this->assign('data',$res['list']);
        $this->assign('type07',$type);
        $this->assign('pageHtml',$pageHtml);
        $this->main_template('pc/person/shopCareful');
    }

    /*产品管理*/
    public function shopControl(){
        $oneType = model('web.product')->getOneType();
        $this->assign('oneType',$oneType);
        $this->main_template('pc/person/shopControl');
    }

    /*产品编辑*/
    public function productEdit(){
        //获取车系
        $cateMo = model('web.category');
        //轿车商家
        $car_cate['cate_1'] = $cateMo->getCarCateByLevel(1,1);//轿车 一级分类
        $car_cate['cate_2'] = $cateMo->getCarCateByLevel(1,2);//轿车 二级分类
        $car_cate['cate_3'] = $cateMo->getCarCateByLevel(1,3);//轿车 三级分类
        //货车商家
        $van_cate['cate_1'] = $cateMo->getCarCateByLevel(2,1);//货车 一级分类
        $van_cate['cate_2'] = $cateMo->getCarCateByLevel(2,2);//货车 二级分类
        $type  = model('web.product')->getOneType();          //所有的一级分类
        $proId = $this->getRequest('proId','');
        $user  = $this->user;
        if($proId){
            //编辑并回显内容
            $data = model('web.product')->getOneProduct($proId,$user['id']);
            $this->assign('id',$proId);
            $this->assign('data',$data);
        }
        $this->assign('type',$type);
        $this->assign('car_cate',$car_cate);
        $this->assign('van_cate',$van_cate);
        $this->main_template('pc/person/productEdit');
    }

    /*认证信息*/
    public function approveInfo(){
        $user = $this->user;
        $renZheng    = model('web.product')->approveSuccessInfo($user['id']);
        $renZhengIng = model('web.product')->approveIngInfo($user['id']);
        if($renZhengIng){
            $ing = 1;       //已经存在认证中
        }else{
            $ing = 2;
        }
        $this->assign('renZheng',$renZheng);
        $this->assign('ing',$ing);
        $this->main_template('pc/person/approveInfo');
    }

    /*经营范围*/
    public function scope(){
//        dump($this->user);die;
        $user = $this->user;
//        dump($user);
        $data = model('web.product')->getCarGroup($user['business']);
//        dump($data);die;
        $this->assign('data',$data);
        $this->main_template('pc/person/scope');
    }

    /*企业名片*/
    public function card(){
        $user = $this->user;
//        dump($user);die;
        $data = model('web.card')->getCardInfo($user['id']);
//        dump($data);die;
        $this->assign('data',$data);
        $this->main_template('pc/person/card');
    }

    /*编辑企业名片*/
    public function cardSave(){
        $tokenUse = $this->getRequest('tokenUse','1');
        $user = $this->user;
        if($user['type']==1){
            $ico  = model('web.card')->getErJiTuPian($user['business']);
            $this->assign('ico',$ico);
        }
        $firmsQR = model('web.firms')->getQRStore($user['EnterpriseID'],$user['companyname'],$user['type']);
        $this->assign('firmsQR',$firmsQR);
        $this->assign('tokenUse',$tokenUse);
        $this->main_template('pc/person/cardSave');
    }

    /*选择名片模板*/
    public function selectCard(){
        $user = $this->user;
        if($user['type']==1){
            $ico  = model('web.card')->getErJiTuPian($user['business']);
            $this->assign('ico',$ico);
        }
        $firmsQR = model('web.firms')->getQRStore($user['EnterpriseID'],$user['companyname'],$user['type']);
        $this->assign('firmsQR',$firmsQR);
        $this->main_template('pc/person/selectCard');
    }

    /*汽修厂轨迹*/
    public function track(){
        $this->main_template('pc/person/track');
    }

    /*搜索汽修厂*/
    public function trackSeek(){
        $this->main_template('pc/person/trackSeek');
    }

    /*访问记录*/
    public function visit(){
        $firmID = $this->getRequest('ID',0);
        $page   = $this->getRequest('page','1');
        $logMo  = model('web.log','mysql');
        if($firmID){
            $logslist = $logMo->getOneVisitToFiirmLog($firmID,$this->user['id'],1,15);
            $this->assign('logslist',$logslist['list']);
            $this->assign('funArr',array('to'=>'visit','title'=>'访问详情'));
            $this->main_template('pc/person/logDetail');
        }else{
            $logData = $logMo->getVisitToFirmsLog($this->user['id'],$page,10);
            $pageTool = model('tools.page','mysql');
            $pageHtml = $pageTool->pagerXcm($logData['count'],$page,10,"");
            $this->assign('data',$logData['list']);
            $this->assign('pageHtml',$pageHtml);

            $this->main_template('pc/person/visit');
        }
    }

    /*拨打记录*/
    public function call(){
        $firmID = $this->getRequest('ID',0);
        $page   = $this->getRequest('page','1');
        $logMo  = model('web.log','mysql');
        if($firmID){
            $logslist = $logMo->getOneCallToFirmLog($firmID,$this->user['id'],1,15);
            $this->assign('logslist',$logslist['list']);
            $this->assign('funArr',array('to'=>'call','title'=>'拨打详情'));
            $this->main_template('pc/person/logDetail');
        }else {
            $logData = $logMo->getCallToFirmsLog($this->user['id'],$page,10);
            $pageTool = model('tools.page','mysql');
            $pageHtml = $pageTool->pagerXcm($logData['count'],$page,10,"");
            $this->assign('data',$logData['list']);
            $this->assign('pageHtml',$pageHtml);
            $this->main_template('pc/person/call');
        }
    }

    /*vip*/
    public function vip(){
        $this->main_template('pc/person/vip');
    }

    /*vip充值*/
    public function vipRecharge(){
        $payMo = model('web.pay','mysql');
        $data  = $payMo->getVipRule();
        $this->assign('data',$data);
        $this->main_template('pc/person/vipRecharge');
    }

    /*vip充值结果*/
    public function payResult(){
        $coder1 = $this->getRequest('orderCoder','');
        $coder2 = $this->getRequest('out_trade_no','');
        $coder  = $coder1?$coder1:$coder2;
        if($coder){
            $payMo = model('web.pay');
            $order = $payMo->getOderByCoder($coder);
            if($order){
                if($order['status']==1){
                    $this->assign('result',array('money'=>$order['money'],'payway'=>$order['payway']==1?'微信':'支付宝'));
                }
                $this->assign('paytypefornav',$order['type']);
            }
        }
        $this->main_template('pc/person/payResult');
    }

    /*vip充值记录*/
    public function payRecord(){
        $page     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','6');
        $firmsMo  = model('web.firms','mysql');
        $vipHis   = $firmsMo->getVipHistory($this->user['id'],$page,$pageSize);
        //分页工具
        $pageTool = model('tools.page','mysql');
        $pageHtml = $pageTool->pager($vipHis['count'],$page,$pageSize,'');
        $this->assign('vipHis',$vipHis['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->main_template('pc/person/payRecord');
    }

    /*我的刷新点*/
    public function myRefresh(){

        $this->main_template('pc/person/myRefresh');
    }

    /*购买刷新点*/
    public function refreshPay(){
        $payMo = model('web.pay','mysql');
        $data  = $payMo->getRefreshPointRule();
        $this->assign('data',$data);
        $this->main_template('pc/person/refreshPay');
    }

    /*刷新点记录*/
    public function refreshRecord(){
        $page     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','6');
        $firmsMo  = model('web.firms','mysql');
        $vipHis   = $firmsMo->getRefreshHistory($this->user['id'],$page,$pageSize);
        //分页工具
        $pageTool = model('tools.page','mysql');
        $pageHtml = $pageTool->pager($vipHis['count'],$page,$pageSize,'');
        $this->assign('vipHis',$vipHis['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->main_template('pc/person/refreshRecord');
    }

    /*认证*/
    public function approve(){
        $this->main_template('pc/person/approve');
    }

    public function approveResult(){
        $user = $this->user;
        $check  = model('web.product')->approveInfo($user['id']);
        if(!$check){
            $this->main_template('pc/person/approve');
        }
        $this->assign('data',$check);
        $this->assign('result',$check['status']);
        $this->main_template('pc/person/approveResult');
    }

    /*求购中*/
    public function shoping(){
        $this->main_template('pc/person/shoping');
    }

    /*求购历史*/
    public function shopingHistory(){
        $user = $this->user;
        $data = model('web.product')->shoping($user['id'],1);
        $this->assign('data',$data);
//        dump($data);die;
        $this->main_template('pc/person/shopingHistory');
    }

    /*求购详情*/
    public function shopingEidt(){
        $id = $this->getRequest('id','');
        $user = $this->user;
        $data = model('web.product')->shopingInfo($user['id'],$id);
        $this->assign('data',$data);
        $this->main_template('pc/person/shopingEidt');
    }

    /*编辑求购*/
    public function shopBianJi(){
        $id = $this->getRequest('id','');
        $user = $this->user;
        $data = model('web.product')->shopingInfo($user['id'],$id);
        if($data && isset($data['list']) && $data['list']){
            foreach($data['list'] as &$v){
                $v['data2'] = model('web.product')->getTwoType($v['pro_cate1']);
            }
        }
        //获取车系
        $cateMo = model('web.category');
        //轿车商家
        $car_cate['cate_1'] = $cateMo->getCarCateByLevel(1,1);//轿车 一级分类
        $car_cate['cate_2'] = $cateMo->getCarCateByLevel(1,2);//轿车 二级分类
        //货车商家
        $van_cate['cate_1'] = $cateMo->getCarCateByLevel(2,1);//货车 一级分类
        $van_cate['cate_2'] = $cateMo->getCarCateByLevel(2,2);//货车 二级分类
        $oneType = model('web.product')->getOneType();       //返回所有配件一级分类
        if($oneType){
            $twoFirstType = model('web.product')->getTwoType($oneType[0]['id']);  //返回第一个配件一级分类的二级分类
            $this->assign('twoFirstType',$twoFirstType);
        }
        $this->assign('car_cate',$car_cate);
        $this->assign('van_cate',$van_cate);
        $this->assign('data',$data);
        $this->assign('oneType',$oneType);
        $this->main_template('pc/person/shopBianJi');
    }

    /*发布求购*/
    public function sendShop(){
        //获取车系
        $cateMo = model('web.category');
        //轿车商家
        $car_cate['cate_1'] = $cateMo->getCarCateByLevel(1,1);//轿车 一级分类
        $car_cate['cate_2'] = $cateMo->getCarCateByLevel(1,2);//轿车 二级分类
        //货车商家
        $van_cate['cate_1'] = $cateMo->getCarCateByLevel(2,1);//货车 一级分类
        $van_cate['cate_2'] = $cateMo->getCarCateByLevel(2,2);//货车 二级分类
        $oneType = model('web.product')->getOneType();       //返回所有配件一级分类
        if($oneType){
            $twoFirstType = model('web.product')->getTwoType($oneType[0]['id']);  //返回第一个配件一级分类的二级分类
            $this->assign('twoFirstType',$twoFirstType);
        }
        $type    = $this->getRequest('type','');       //1:向轿车商家求购   2:向货车商家求购
        $cheXi   = $this->getRequest('series','');     // 车系
        $pingPai = $this->getRequest('brand','');      //车型
        $haoMa   = $this->getRequest('num','');        //车架号
        $this->assign('type',$type);
        $this->assign('haoMa',$haoMa);
        if($type && $cheXi && $pingPai && $haoMa){
            $vin = model('web.product')->piPeiVin($pingPai,$cheXi,$type);
            $this->assign('vin',$vin);
        }
        $this->assign('oneType',$oneType);
        $this->assign('car_cate',$car_cate);
        $this->assign('van_cate',$van_cate);
        $this->main_template('pc/person/sendShop');
    }

    /*收藏店铺*/
    public function collectShop(){
        //获取车系
        $cateMo = model('web.category');
        //轿车商家
        $car_cate['cate_1'] = $cateMo->getCarCateByLevel(1,1);//轿车 一级分类
        $car_cate['cate_2'] = $cateMo->getCarCateByLevel(1,2);//轿车 二级分类
        //货车商家
        $van_cate['cate_1'] = $cateMo->getCarCateByLevel(2,1);//货车 一级分类
        $van_cate['cate_2'] = $cateMo->getCarCateByLevel(2,2);//货车 二级分类
        //物流
        //$tra_cate['cate_1'] = $cateMo->getCarCateByLevel(3,1);//物流 一级分类
        //$tra_cate['cate_2'] = $cateMo->getCarCateByLevel(3,2);//物流 二级分类

        $this->assign('car_cate',$car_cate);
        $this->assign('van_cate',$van_cate);
        //$this->assign('tra_cate',$tra_cate);

        $this->main_template('pc/person/collectShop');
    }

    /*收藏产品*/
    public function collectProduct(){
        //获取车系
        $cateMo = model('web.category');
        $cate_1 = $cateMo->getProCateByLevel(1);
        $cate_2 = $cateMo->getProCateByLevel(2);
        $this->assign('proCate',array('cate_1'=>$cate_1,'cate_2'=>$cate_2));
        $this->main_template('pc/person/collectProduct');
    }

    /*分享邀请*/
    public function invite(){
        $page     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','5');
        //获取邀请我的厂商
        $firmsMo  = model('web.firms','mysql');
        $inviteMe = $firmsMo->getInInviteMe($this->user['id']);
        //获取我的邀请记录
        $logMo    = model('web.log','mysql');
        $inviteLog= $logMo->getInviteLog($this->user['id'],$page,$pageSize);
        //分页工具
        $pageTool = model('tools.page','mysql');
        $pageHtml = $pageTool->pager($inviteLog['count'],$page,$pageSize,'');

        $this->assign('inviteMe',$inviteMe);
        $this->assign('inviteLog',$inviteLog['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->main_template('pc/person/invite');
    }

    /*绑定业务员*/
    public function bindSaleMan(){
        $firmMo = model('web.firms','mysql');
        $data   = $firmMo->getBindUser($this->user['id']);
        $this->assign('data',$data);
        $this->main_template('pc/person/bindSaleMan');
    }
}