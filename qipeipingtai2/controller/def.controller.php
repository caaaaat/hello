<?php
class DefController extends Controller
{
    //首页
    public function index()
    {
        controller('pc.index','home');
    }
    //轿车商家
    public function cars(){
        controller('pc.index','cars');
    }
    //货车商家
    public function vans(){
        controller('pc.index','vans');
    }
    //新品促销
    public function newMarket(){
        controller('pc.index','newMarket');
    }
    //库存清仓
    public function clearance(){
        controller('pc.index','clearance');
    }
    //配件求购
    public function mountings(){
        controller('pc.index','mountings');
    }
    //vin查询
    public function vinQuery(){
        controller('pc.index','vinQuery');
    }
    //圈子
    public function circle(){
        controller('pc.index','circle');
    }
    //产品详情
    public function product(){
        controller('pc.index','product');
    }
    //求购详情
    public function buyView(){
        controller('pc.index','buyView');
    }
    //促销活动
    public function activities(){
        controller('pc.index','activities');
    }
    //新闻资讯
    public function news(){
        controller('pc.index','news');
    }
    //新手上路
    public function newbie(){
        controller('pc.index','newbie');
    }
    //消息列表
    public function notices(){
        controller('pc.index','notices');
    }
    //店铺详情
    public function store(){
        controller('pc.index','store');
    }
    //店铺产品
    public function storeProduct(){
        controller('pc.index','storeProduct');
    }
    //公司简介
    public function info(){
        controller('pc.index','info');
    }
    //服务协议
    public function xieyi(){
        controller('pc.index','xieyi');
    }
    public function daohang(){
        $x1 = $this->getRequest('x1');
        $y1 = $this->getRequest('y1');
        $x2 = $this->getRequest('x2');
        $y2 = $this->getRequest('y2');
        $this->assign('x1',$x1);
        $this->assign('y1',$y1);
        $this->assign('x2',$x2);
        $this->assign('y2',$y2);
        $this->template('pc.index.daohang');
    }

}