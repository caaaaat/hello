<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/15
 * Time: 10:48
 */
class PersonController extends Controller
{
    /*个人基本资料*/
    public function index(){
        controller('pc.person','index');
    }

    /*修改密码*/
    public function changePWD(){
        controller('pc.person','changePWD');
    }

    /*绑定手机第一步*/
    public function bindPhone(){
        controller('pc.person','bindPhone');
    }

    /*店铺信息*/
    public function shopInfo(){
        controller('pc.person','shopInfo');
    }

    /*编辑店铺信息*/
    public function shopEidt(){
        controller('pc.person','shopEidt');
    }

    /*店铺访问明细*/
    public function shopCareful(){
        controller('pc.person','shopCareful');
    }

    /*产品管理*/
    public function shopControl(){
        controller('pc.person','shopControl');
    }

    /*产品编辑*/
    public function productEdit(){
        controller('pc.person','productEdit');
    }

    /*认证信息*/
    public function approveInfo(){
        controller('pc.person','approveInfo');
    }

    /*认证结果*/
    public function approveResult(){
        controller('pc.person','approveResult');
    }

    /*经营范围*/
    public function scope(){
        controller('pc.person','scope');
    }

    /*企业名片*/
    public function card(){
        controller('pc.person','card');
    }

    /*编辑企业名片*/
    public function cardSave(){
        controller('pc.person','cardSave');
    }

    /*汽修厂轨迹*/
    public function track(){
        controller('pc.person','track');
    }

    /*搜索汽修厂*/
    public function trackSeek(){
        controller('pc.person','trackSeek');
    }

    /*访问记录*/
    public function visit(){
        controller('pc.person','visit');
    }

    /*拨打记录*/
    public function call(){
        controller('pc.person','call');
    }

    /*vip*/
    public function vip(){
        controller('pc.person','vip');
    }

    /*vip充值*/
    public function vipRecharge(){
        controller('pc.person','vipRecharge');
    }

    /*vip充值结果*/
    public function payResult(){
        controller('pc.person','payResult');
    }

    /*vip充值记录*/
    public function payRecord(){
        controller('pc.person','payRecord');
    }

    /*我的刷新点*/
    public function myRefresh(){
        controller('pc.person','myRefresh');
    }

    /*购买刷新点*/
    public function refreshPay(){
        controller('pc.person','refreshPay');
    }

    /*刷新点记录*/
    public function refreshRecord(){
        controller('pc.person','refreshRecord');
    }

    /*认证*/
    public function approve(){
        controller('pc.person','approve');
    }

    /*求购中*/
    public function shoping(){
        controller('pc.person','shoping');
    }

    /*求购历史*/
    public function shopingHistory(){
        controller('pc.person','shopingHistory');
    }

    /*求购详情*/
    public function shopingEidt(){
        controller('pc.person','shopingEidt');
    }

    /*编辑求购*/
    public function shopBianJi(){
        controller('pc.person','shopBianJi');
    }

    /*发布求购*/
    public function sendShop(){
        controller('pc.person','sendShop');
    }

    /*收藏店铺*/
    public function collectShop(){
        controller('pc.person','collectShop');
    }

    /*收藏产品*/
    public function collectProduct(){
        controller('pc.person','collectProduct');
    }

    /*分享邀请*/
    public function invite(){
        controller('pc.person','invite');
    }

    /*发布圈子（分享邀请码）*/
    public function inviteCode(){
        controller('pc.index','inviteCode');
    }

    /*选择名片模板*/
    public function selectCard(){
        controller('pc.person','selectCard');
    }

    /*选择名片模板*/
    public function bindSaleMan(){
        controller('pc.person','bindSaleMan');
    }
}