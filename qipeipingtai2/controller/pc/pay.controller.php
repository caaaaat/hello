<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/26
 * Time: 16:51
 */
class PcPayController extends Controller
{
    private $user = array();

    public function __construct()
    {
        $loginMo    = model('web.login','mysql');
        $this->user = $user = $loginMo->loginIs(false);
    }
    //创建vip订单
    public function toPayOfVip(){
        if($this->user){
            $payType = $this->getRequest('payType','');
            $month   = $this->getRequest('month','');
            $payMo   = model('web.pay');
            $return  = $payMo->toVipOrder($payType,$month,$this->user);
        }else{
            $return = array('status'=>0,'msg'=>'还未登陆，请登录再试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    //创建刷新点订单
    public function toPayOfRefresh(){
        if($this->user){
            $payType     = $this->getRequest('payType','');
            $refreshPoint= $this->getRequest('refreshPoint',0);
            $shuMoney    = $this->getRequest('shuMoney',0);
            $payMo   = model('web.pay');
            $return  = $payMo->toRefreshOrder($payType,$refreshPoint,$shuMoney,$this->user);
        }else{
            $return = array('status'=>0,'msg'=>'还未登陆，请登录再试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //调起支付
    public function toPayQROfVip(){
        if($this->user){
            $coder = $this->getRequest('order','');
            if($coder){
                $payMo = model('web.pay');
                $order = $payMo->getOderByCoder($coder);
                if($order){
                    if($order['status']==2){
                        if($order['payway']==1){//微信支付
                            $QR = $payMo->WeChatQRPay($order);
                            $this->assign('QRData',$QR);
                            $this->template('pc.layout.pay.wxQRPay');
                        }elseif($order['payway']==2){//支付宝支付
                            $payMo->toAliPay($order);


                        }else{
                            echo "<h1 style='text-align: center;color: red'>参数有误</h1>";
                        }
                    }else{
                        echo "<h1 style='text-align: center'>该订单已支付</h1>";
                    }
                }else{
                    echo "<h1 style='text-align: center;color: red'>订单不存在</h1>";
                }
            }else{
                echo "<h1 style='text-align: center'>参数有误</h1>";
            }
        }else{
            echo "<h1 style='text-align: center'>请先登录</h1>";
        }
    }

    //微信查询支付是否成功
    public function checkWXPay(){
        $return = array('status'=>0,'msg'=>'');
        if($this->user){
            $coder = $this->getRequest('orderCoder','');
            if($coder){
                $payMo = model('web.pay');
                $return = $payMo->checkWXPay($coder);
            }
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    
}