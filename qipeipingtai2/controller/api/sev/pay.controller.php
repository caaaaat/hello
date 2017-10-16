<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/5
 * Time: 23:42
 */

class ApiSevPayController extends Controller{


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

    //创建vip订单
    public function createVipOrder(){
        if($this->user['status']==200){
            $payType = $this->getRequest('payType','');
            $month   = $this->getRequest('month','');
            $payMo   = model('api.sev.pay','mysql');

            $return  = $payMo->toVipOrder($payType,$month,$this->user['data']['id']);
        }else{
            $return = $this->user;
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 创建刷新点订单
     */
    public function createRefreshOrder(){

        $payType   = $this->getRequest('payType','');
        $moneyType = $this->getRequest('moneyType','');
        $amount    = $this->getRequest('amount','');

        if($this->user['status']==200){

            $payMo   = model('api.sev.pay','mysql');
            $return  = $payMo->toRefreshOrder($payType,$moneyType,$amount,$this->user['data']['id']);

        }else{
            $return = $this->user;
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }



    /**
     *充值
     */
   public function toPayOrder(){

       if($this->user['status']==200){

           $coder = $this->getRequest('coder','');

           if($coder){

               $payMo = model('api.sev.pay');
               $order = $payMo->getOrderByCoder($coder);

               if($order){
                   if($order['status']==2){

                       if($order['payway']==1){//微信支付

                           $payMo->WeChatPay($order);

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
           echo $this->user['status'];
       }

    }


    /**
     *移动网站充值
     */
    public function toPayWapOrder(){

        if($this->user['status']==200){

            $coder = $this->getRequest('coder','');

            if($coder){

                $payMo = model('api.sev.pay');
                $order = $payMo->getOrderByCoder($coder);

                if($order){
                    if($order['status']==2){

                        if($order['payway']==1){//微信支付

                            $res =  $payMo->WeChatWapPay($order);

                        }elseif($order['payway']==2){//支付宝支付

                            $res = $payMo->toAliWapPay($order);

                        }else{
                            $res = array('status'=>105,'msg'=>'参数有误');
                        }
                    }else{
                        $res = array('status'=>104,'msg'=>'该订单已支付');
                    }
                }else{
                    $res = array('status'=>103,'msg'=>'订单不存在');
                }
            }else{
                $res = array('status'=>102,'msg'=>'参数有误');
            }

        }else{
            $res = array('status'=>101,'msg'=>'未登录，请登陆后重试');
        }


        exit(json_encode($res,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 获取订单支付状态
     */
    public function getPayStatus(){

        $coder = $this->getRequest('coder','');

        if($coder){

            $payMo = model('api.sev.pay');

            $res =  $payMo->getPayStatus($coder);

        }else{
            $res = array('status'=>102,'msg'=>'参数有误');
        }


        exit(json_encode($res,JSON_UNESCAPED_UNICODE));

    }

}