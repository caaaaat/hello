<?php
/**
 *
 * 服务端 首页
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/19
 * Time: 15:11
 */

class ApiSevIndexController extends Controller{

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

    //获取首页banner
    public function banner(){
       //获取banner
        $bannerMo = model('web.banner','mysql');
        $top_banners = $bannerMo->getTopBanner();//顶部banner
        $yao_banners = $bannerMo->getYaoBanner();//腰部banner

        $return['top'] = $top_banners;
        $return['yao'] = $yao_banners;

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    //获取getBaseIni
    public function getBaseIni(){
        $iniMo = model('web.ini','mysql');
        $return = $iniMo->getQQ();//
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //轿车商家 车系
    public function getCarGroup(){
        //获取车系
        $cateMo = model('api.sev.category');
        //轿车商家
        $data['cate_1'] = $cateMo->getCarCateByLevel(1,1);//轿车 一级分类
        $data['cate_2'] = $cateMo->getCarCateByLevel(1,2);//轿车 二级分类
        $return['status'] = 200;
        $return['data']   = $data;
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //货车商家 车系
    public function getTrackGroup(){
        //获取车系
        $cateMo = model('api.sev.category');
        //轿车商家
        $data['cate_1'] = $cateMo->getCarCateByLevel(2,1);//货车 一级分类
        $data['cate_2'] = $cateMo->getCarCateByLevel(2,2);//货车 二级分类
        $return['status'] = 200;
        $return['data']   = $data;
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    //物流商家 车系
    public function getWuLiuGroup(){
        //获取车系
        $cateMo = model('api.sev.category');
        //物流商家
        $data['cate_1'] = $cateMo->getCarCateByLevel(3,1);//物流 一级分类
        $data['cate_2'] = $cateMo->getCarCateByLevel(3,2);//物流 二级分类
        $return['status'] = 200;
        $return['data']   = $data;
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    //获取商家 轿车 物流 货车车系
    public function getStoreGroup(){
        //获取车系
        $cateMo = model('api.sev.category');
        //轿车商家
        $data['car']['cate_1'] = $cateMo->getCarCateByLevel(1,1);//轿车 一级分类
        $data['car']['cate_2'] = $cateMo->getCarCateByLevel(1,2);//轿车 二级分类
        //货车商家
        $data['track']['cate_1'] = $cateMo->getCarCateByLevel(2,1);//货车 一级分类
        $data['track']['cate_2'] = $cateMo->getCarCateByLevel(2,2);//货车 二级分类
        //物流商家
        $data['wuLiu']['cate_1'] = $cateMo->getCarCateByLevel(3,1);//物流 一级分类
        $data['wuLiu']['cate_2'] = $cateMo->getCarCateByLevel(3,2);//物流 二级分类
        $return['status'] = 200;
        $return['data']   = $data;
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //获取商家
    public function getFirms(){
        $keywords       = $this->getRequest('keyword','');
        //1经销商 2修理厂
        $type           = $this->getRequest('type','');
        //企业分类 (和type字段相关)
        //经销商：1.轿车商家 2.货车商家 3.物流货运
        //汽修厂：4.修理厂    5.快修保养 6.美容店
        $classification = $this->getRequest('type_2','');
        $cate_1 = $this->getRequest('car_cate_1',0);
        $cate_2 = $this->getRequest('car_cate_2',0);
        $cityIni = $this->getRequest('cityIni','');
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


        //获取商家
        $firmMo = model('web.firms','mysql');
        $return  = $firmMo->getFirms($type,$classification,$business,$categorise,$keywords,$page,$pageSize,$cityIni);
        $return['status'] = 200;

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    //获取商家详情
    public function getFirmDetail(){
        $EnterpriseID = $this->getRequest('EnterpriseID','');
        $id    = 0;
        $userType = $this->userType;
        if($this->user['status']==200){
            $id = $this->user['data']['id'];
        }


        if($EnterpriseID){
            $firmMo = model('api.sev.index','mysql');

            $data   = $firmMo->getFirmInfoByEnID($EnterpriseID,$id,$userType);

            //销毁敏感数据
            unset($data['password']);
            //预处理数据
            $linkPhoneArr = array();//联系手机
            $linkTelArr   = array();//联系座机
            $qqArr        = array();//联系qq

            if($data['linkPhone']){
                $linkPhoneArr = explode(',',$data['linkPhone']);
            }

            if($data['linkTel']){
                $linkTelArr = explode(',',$data['linkTel']);
            }

            if($data['qq']){
                $qqArr = explode(',',$data['qq']);
            }

            $data['linkPhoneArr'] = $linkPhoneArr;
            $data['linkTelArr']   = $linkTelArr;
            $data['qqArr']        = $qqArr;

            $return['data']   = $data;
            $return['status'] = 200;
        }else{
            $return['status'] = 101;
            $return['msg']    = '提交数据有误';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    //获取促销或者清仓产品分类
    public function getProductGroup(){

        //获取促销或者清仓产品分类
        $indexMo = model('api.sev.index','mysql');
        $return  = $indexMo->getProductGroup();
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    //获取促销或清仓产品
    public function getProducts(){

        $keyword = $this->getRequest('keyword','');//关键字
        $type    = $this->getRequest('type','1');//产品 促销 清仓
        $groupId = $this->getRequest('groupId','');//分类Id
        $cityIni = $this->getRequest('cityIni','');//分类Id
        $page    = $this->getRequest('page',1);
        $pageSize= $this->getRequest('pageSize',10);

        $indexMo = model('api.sev.index','mysql');
        $return  = $indexMo->getProducts($type,$groupId,$keyword,$page,$pageSize,$cityIni);

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    //获取促销或清仓产品详情
    public function getProductDetail(){

        $proId = $this->getRequest('proId','');//关键字
        $userType = $this->userType;
        $id    = 0;
        if($this->user['status']==200){
            $id = $this->user['data']['id'];
        }

        if($proId){
            $indexMo = model('api.sev.index','mysql');
            $return  = $indexMo->getProductDetail($proId,$id,$userType);
        }else{
            $return['status'] = 101;
            $return['status'] = '提交数据有误';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //收藏产品
    public function collectProduct(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $productId = $this->getRequest('productId','');
        $type   = $this->getRequest('type','');

        $msg = $type==1?'收藏':'取消收藏';

        if($token){

            if($this->user['status']==200){
                $id = $this->user['data']['id'];
                $userType = $this->userType;
                $collectMo = model('web.collect','mysql');
                $proInfo = $collectMo->table('product_list')->where(array('id'=>$productId));
                if($proInfo){
                    $res = $collectMo->collectProduct($userType,$id,$productId,$type);
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


    //获取经销商促销或清仓产品
    public function getFirmProducts(){

        $EnterpriseID   = $this->getRequest('EnterpriseID','');
        if($EnterpriseID){
            $pro_type   = $this->getRequest('type','');
            $pro_cate_1 = $this->getRequest('cate_1','');
            $pro_cate_2 = $this->getRequest('cate_2','');
            $page    = $this->getRequest('page',1);
            $pageSize= $this->getRequest('pageSize',10);

            $indexMo = model('api.sev.index','mysql');
            $return  = $indexMo->getFirmProducts($EnterpriseID,$pro_type,$pro_cate_1,$pro_cate_2,$page,$pageSize);
        }else{
            $return = array('list'=>array(),'page'=>1,'count'=>0,'pageSize'=>10,'status'=>200);
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    //配件求购
    public function getMountings(){

        $page    = $this->getRequest('page',1);
        $pageSize= $this->getRequest('pageSize',10);
        $cityIni = $this->getRequest('cityIni','');//分类Id
        $indexMo = model('api.sev.index','mysql');
        $return  = $indexMo->getMountings($page,$pageSize,$cityIni);

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //求购详情
    public function getWantDetail(){
        $wantId  = $this->getRequest('wantId','');

        $indexMo = model('api.sev.index','mysql');
        $return  = $indexMo->getWantDetail($wantId);

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取消息
     */
    public function getUnReadMsgNum(){
        if($this->user['status']==200){
            $indexMo = model('api.sev.index','mysql');
            $data  = $indexMo->getUnReadMsgNum($this->userType,$this->user['data']['id']);
            $data['status'] = 200;
        }else{
            $data = array('list'=>array(),'page'=>1,'count'=>0,'pageSize'=>10,'status'=>200);
        }
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取未读消息记录 通知 新闻 促销
     */
    public function getMsgTip(){
        //用户数据请求成功
        if($this->user['status']==200){
            $id = $this->user['data']['id'];
            $userType = $this->userType;
            $collectMo = model('web.collect','mysql');
            $return = $collectMo->getMsgTip($userType,$id);
            $return['status'] = 200;
        }else{
            $return = array('list'=>array(),'page'=>1,'count'=>0,'pageSize'=>10,'status'=>200);
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取消息
     */
    public function getMsg(){
        if($this->user['status']==200){
            $page = $this->getRequest('page',1);
            $city = $this->getRequest('cityIni','');
            $msgMo = model('web.msg','mysql');
            $data  = $msgMo->getMsg( $this->userType,$this->user['data']['id'],$page,10,$city);
            $data['status'] = 200;
        }else{
            $data = array('list'=>array(),'page'=>1,'count'=>0,'pageSize'=>10,'status'=>200);;
        }
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取通知公告
     */
    public function getNotice(){
        //获取提交的数据
        $token    = $this->getRequest('token','');
        $userType = $this->getRequest('userType','');
        $city = $this->getRequest('cityIni','');

        $isQx = 0;

        if($userType!=2){
            if($token){
                $userMo = model('api.sev.user','mysql');
                $user = $userMo ->loginIs($token);

                if($user['status']==200){
                   if($user['data']['vip']==1&&$user['data']['type']==1){
                       $isQx = 1;
                   }
                }
            }
        }

        $page = $this->getRequest('page',1);
        $msgMo = model('web.msg','mysql');
        $data  = $msgMo->getNotice($isQx,$page,10,$city);
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 获取刷新点配置
     */
    public function getRefreshIni(){

        if($this->user['status']==200){
            $indexMo = model('api.sev.index','mysql');
            $return  = $indexMo->getRefreshIni();
        }else{
            $return = array('status'=>101,'msg'=>'加载失败');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 获取vip配置
     */
    public function getVipIni(){

        if($this->user['status']==200){
            $indexMo = model('api.sev.index','mysql');
            $return  = $indexMo->getVipIni();
        }else{
            $return = array('status'=>101,'msg'=>'加载失败');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 获取配置城市数据
     */
    public function getCityIni(){
        $key     = $this->getRequest('key','');
        $indexMo = model('api.sev.index','mysql');
        $return  = $indexMo->getCityIni($key);
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 搜索vin
     */
    public function getVin(){

        $type = $this->getRequest('type','1');//查询的类型 1 通过关键字  2 通过图片base64
        $key  = $this->getRequest('key','');

        if($key){
            header("content-type:text/html;charset=utf-8");

            $url = "http://service.vin114.net/req?wsdl";
            $method = "LevelData";
            $appKey = 'fc93d445cc35decd';
            $appsecret = '206bec36ebcf48f39e1dda63532f500c';
            $fun = 'level.vehicle.vin.get';

            if($type==1){//关键字
                $data = "<root><appkey>$appKey</appkey><appsecret>$appsecret</appsecret><method>$fun</method><requestformat>json</requestformat><vin>$key</vin></root>";
            }else{
                $data = "<root><appkey>$appKey</appkey><appsecret>$appsecret</appsecret><method>$fun</method><requestformat>json</requestformat><imgbase64>$key</imgbase64></root>";
            }

            $client = new SoapClient($url);
            $addResult = $client->__soapCall($method,array(array('xmlInput'=>$data)));

            $LevelDataResult = json_decode($addResult->LevelDataResult);
            $info = $LevelDataResult->Info;
            $success = $info->Success;

            if($success==false){
                $return['status'] = 201;
                $return['msg']    =  $info->Desc;
            }else{
                $return['status'] = 200;
                $return['msg']    = '获取成功';
                $return['list']   = $LevelDataResult->Result;
            }

        }else{

            $return['status'] = 101;
            $return['msg']    = '您提交的数据有误';

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }




}