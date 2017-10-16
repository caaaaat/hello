<?php
/**
 *
 * 我的求购
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/7
 * Time: 11:37
 */

class ApiSevPurchaseController extends Controller{


    //获取求列表
    public function getPurChaseList(){
        //获取提交的数据
        $token        = $this->getRequest('token','');
        $purchaseType = $this->getRequest('purchaseType','1');
        $page    = $this->getRequest('page',1);
        $pageSize= $this->getRequest('pageSize',10);
        if($token){
            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $purchaseMo = model('api.sev.purchase','mysql');

                $return = $purchaseMo ->getPurChaseList($userId,$purchaseType,$page,$pageSize);
            }

        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }



    //获取求购详情
    public function getWantDetail(){
        //获取提交的数据
        $token  = $this->getRequest('token','');
        $wantId = $this->getRequest('wantId','');
        if($token&&$wantId){
            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $purchaseMo = model('api.sev.purchase','mysql');

                $return = $purchaseMo ->getWantDetail($userId,$wantId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 下架我的求购
     */
    public function offSale(){
        //获取提交的数据
        $token      = $this->getRequest('token','');
        $purchaseId = $this->getRequest('purchaseId','');

        if($token&&$purchaseId){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $purchaseMo = model('api.sev.purchase','mysql');

                $return = $purchaseMo ->offSale($userId,$purchaseId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 删除我的求购
     */
    public function delSale(){
        //获取提交的数据
        $token      = $this->getRequest('token','');
        $purchaseId = $this->getRequest('purchaseId','');

        if($token&&$purchaseId){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $purchaseMo = model('api.sev.purchase','mysql');

                $return = $purchaseMo ->delSale($userId,$purchaseId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 发布求购，添加求购数据
     */
    public function insertShop(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $car_group_id  = $this->getRequest('car_group_id','');
        $frame_number  = $this->getRequest('frame_number','');
        $limitation    = $this->getRequest('limitation','');
        $vin_pic       = $this->getRequest('vin_pic','');
        $memo          = $this->getRequest('memo','');
        $otherp  = $this->getRequest('otherp','');
        $buyArr  = $this->getRequest('buyArr','');
        if($token&&$car_group_id&&$limitation){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $purchaseMo = model('api.sev.purchase','mysql');

                $return = $purchaseMo ->insertShop($car_group_id,$frame_number,$limitation,$vin_pic,$otherp,$buyArr,$memo,$userId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     *编辑求购数据
     */
    public function editPurchase(){

        //获取提交的数据
        $token = $this->getRequest('token','');
        $buyId = $this->getRequest('buyId','');
        $car_group_id  = $this->getRequest('car_group_id','');
        $frame_number  = $this->getRequest('frame_number','');
        $limitation    = $this->getRequest('limitation','');
        $vin_pic       = $this->getRequest('vin_pic','');
        $memo          = $this->getRequest('memo','');
        $otherp  = $this->getRequest('otherp','');
        $buyArr  = $this->getRequest('buyArr','');
        if($token&&$car_group_id&&$limitation){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                //获取充值vip记录
                $userId = $return['data']['id'];
                $purchaseMo = model('api.sev.purchase','mysql');

                $return = $purchaseMo ->editPurchase($car_group_id,$frame_number,$limitation,$vin_pic,$otherp,$buyArr,$memo,$userId,$buyId);
            }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取车系
     */
    public function getCarSeries(){
        $cid = $this->getRequest('cid','0');
        $purchaseMo = model('api.sev.purchase','mysql');

        $return = $purchaseMo ->getThreeAndFourByTwo($cid);

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取配件类别一级
     */
    public function getCategoryOne(){
        $purchaseMo = model('api.sev.purchase','mysql');

        $return = $purchaseMo ->getProductGroupOne();

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取配件类别一级
     */
    public function getCategoryTwo(){
        $pid = $this->getRequest('pid','0');
        $purchaseMo = model('api.sev.purchase','mysql');

        $return = $purchaseMo ->getProductGroupTwo($pid);

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     *根据传递的vin品牌与车系获取车系
     */
    public function getCarGroupByVin(){

        $token = $this->getRequest('token','');
        $brand = $this->getRequest('brand','');
        $salesVersion = $this->getRequest('salesVersion','');
        $typeName = $this->getRequest('typeName','');

        if($token&&$brand&&$salesVersion&&$typeName){

            $userMo = model('api.sev.user','mysql');
            $return = $userMo ->loginIs($token);
            //用户数据请求成功
            if($return['status']==200){
                $purchaseMo = model('api.sev.purchase','mysql');
                $return = $purchaseMo ->getCarGroupByVin($brand,$salesVersion,$typeName);
             }

        }else{
            $return = array('status'=>101,'msg'=>'提交数据有误，刷新后重试');
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


}