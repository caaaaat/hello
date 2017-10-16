<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/23
 * Time: 23:06
 */
//require_once APPROOT."/wxpay/lib/WxPay.Config.php";
require_once APPROOT."/wxpay/lib/WxPay.Api.php";
require_once APPROOT.'/wxpay/lib/WxPay.Notify.php';
require_once APPROOT.'/wxpay/example/log.php';

class WebPayModel extends Model
{
    /**
     * 获取vip充值规则
     */
    public function getVipRule(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>1))->getOne();
        $data=array();
        if(isset($res['value'])){
            if($res['value']){
                $data   = json_decode($res['value'],true);
            }
        }
        return $data;
    }

    /**
     * 获取刷新点充值规则
     */
    public function getRefreshPointRule(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>2))->getOne();
        $data=array();
        if(isset($res['value'])){
            if($res['value']){
                $data   = json_decode($res['value'],true);
            }
        }
        return $data;
    }

    /**
     * 生成订单号
     * @return string
     */
    public function toOrderCoder(){
        $coder = date('YmdHis').rand(1000,9999);
        $res   = $this->table('pay_history')->where(array('coder'=>$coder))->getOne();
        if($res){
            $this->toOrderCoder();
        }else{
            return $coder;
        }
    }

    /**
     * 保存vip订单
     * @param $payType
     * @param $month
     * @param $user
     * @return array
     */
    public function toVipOrder($payType,$month,$user){
        switch ($payType){
            case 'wx':  $payway = 1;break;
            case 'ali': $payway = 2;break;
            default:return array('status'=>0,'msg'=>'请选择支付方式');
        }

        $vip = $this->getVipRule();
        if($vip){
            $money = 0;
            foreach ($vip as $v){
                if($v['number']===$month){
                    $money = $v['money'];
                }
            }

            if($money){
                $data = array(
                    'type'=>1,'status'=>2,'info'=>'充值VIP'.$month.'个月',
                    'payway'=>$payway,'vip_month'=>$month,
                    'firms_id'=>$user['id'],'money'=>$money,
                    'create_time'=>date('Y-m-d H:i:s',time()),
                    'coder'=>$this->toOrderCoder()
                );
                $res = $this->table('pay_history')->insert($data);
                if($res){
                    return array('status'=>1,'msg'=>'订单创建成功，请支付','coder'=>$data['coder']);
                }else{
                    return array('status'=>0,'msg'=>'订单创建失败，请稍后再试');
                }
            }else{
                return array('status'=>0,'msg'=>'参数错误，请刷新页面');
            }
        }else{
            return array('status'=>0,'msg'=>'暂时不能充值');
        }
    }

    /**
     * 保存刷新点订单
     */
    public function toRefreshOrder($payType,$refreshPoint,$shuMoney,$user){
        switch ($payType){
            case 'wx':  $payway = 1;break;
            case 'ali': $payway = 2;break;
            default:return array('status'=>0,'msg'=>'请选择支付方式');
        }
        $refreshRule = $this->getRefreshPointRule();
        if($refreshRule){
            if($refreshPoint){
                if(isset($refreshRule['select'])){
                    $money = 0;
                    foreach ($refreshRule['select'] as $v){
                        if($v['number']==$refreshPoint){
                            $money=$v['money'];
                        }
                    }

                    if($money){
                        $data = array(
                            'type'=>2,'status'=>2,'info'=>'充值刷新点'.$refreshPoint.'个',
                            'payway'=>$payway,'refresh_point'=>$refreshPoint,
                            'firms_id'=>$user['id'],'money'=>$money,
                            'create_time'=>date('Y-m-d H:i:s',time()),
                            'coder'=>$this->toOrderCoder()
                        );
                        $res = $this->table('pay_history')->insert($data);
                        if($res){
                            return array('status'=>1,'msg'=>'订单创建成功，请支付','coder'=>$data['coder']);
                        }else{
                            return array('status'=>0,'msg'=>'订单创建失败，请稍后再试');
                        }
                    }
                    return array('status'=>0,'msg'=>'参数有误，请刷新页面');
                }
                return array('status'=>0,'msg'=>'暂时不能充值');
            }else{
                //输入的金额
                if(ctype_digit($shuMoney)){
                    if($shuMoney>=1){
                        if(isset($refreshRule['proportion']['ref']) && isset($refreshRule['proportion']['money'])){
                            $mei = $refreshRule['proportion']['ref']/$refreshRule['proportion']['money'];
                            $totalFresh = round($shuMoney*$mei);
                            $data = array(
                                'type'=>2,'status'=>2,'info'=>'充值刷新点'.$totalFresh.'个',
                                'payway'=>$payway,'refresh_point'=>$totalFresh,
                                'firms_id'=>$user['id'],'money'=>$shuMoney,
                                'create_time'=>date('Y-m-d H:i:s',time()),
                                'coder'=>$this->toOrderCoder()
                            );
                            $res = $this->table('pay_history')->insert($data);
                            if($res){
                                return array('status'=>1,'msg'=>'订单创建成功，请支付','coder'=>$data['coder']);
                            }else{
                                return array('status'=>0,'msg'=>'订单创建失败，请稍后再试');
                            }
                        }
                        return array('status'=>0,'msg'=>'暂时不能充值');
                    }
                    return array('status'=>0,'msg'=>'请输入大于或等于一元的整数金额');
                }
                return array('status'=>0,'msg'=>'请输入充值金额（大于或等于一元的整数）');
            }
        }else{
            return array('status'=>0,'msg'=>'暂时不能充值');
        }
    }

    /**
     * 根据订单号获取订单
     * @param $coder
     * @return mixed
     */
    public function getOderByCoder($coder){
        $res = $this->table('pay_history')->where(array('coder'=>$coder))->getOne();
        return $res;
    }

    /**
     * 微信支付二维码
     */
    public function WeChatQRPay($order){
        //return array('status'=>1,'return_msg'=>'ok','code_url'=>'weixin://wxpay/bizpayurl?pr=0EU5WXg');

        if($order['type']==1){
            $body = "充值vip{$order['vip_month']}个月";
            $title= "订单号:[{$order['coder']}],充值vip{$order['vip_month']}个月";
        }elseif($order['type']==2){
            $body = "充值刷新点{$order['refresh_point']}";
            $title= "订单号:[{$order['coder']}],充值刷新点{$order['refresh_point']}个";
        }else{
            return array('status'=>0,'return_msg'=>'参数有误请重试');
        }
        $out_trade_no = $order['coder'];
        $money     = $order['money']*100;
        $productID = $order['coder'];
        $return = $this->wxQR($body,$title,$out_trade_no,$money,$productID,$order['coder']);
        return $return;
    }

    /**
     * 生成微信二维码
     * @param $body
     * @param $title
     * @param $out_trade_no
     * @param $money
     * @param $productID
     * @param $orderCoder
     * @return array
     */
    public function wxQR($body,$title,$out_trade_no,$money,$productID,$orderCoder){
        $notify = new NativePay();
        $input  = new WxPayUnifiedOrder();
        //$input->SetAppid($Appid);//公众账号ID
        //$input->SetMch_id($Mch_id);//商户号
        //$input->SetNonce_str($Nonce_str);//随机字符串
        $input->SetBody($body);//商品描述
        $input->SetAttach($title);//附加数据
        $input->SetOut_trade_no($out_trade_no);//商户订单号
        $input->SetTotal_fee($money);//订单总金额
        $input->SetTime_start(date("YmdHis"));//交易起始时间
        $input->SetTime_expire(date("YmdHis", time() + 1800));//交易结束时间
        //$input->SetGoods_tag("test");//订单优惠标记
        $input->SetNotify_url("http://app.7pqun.com/pay/wxnotify");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($productID);//商品ID
        $result = $notify->GetPayUrl($input);
        $return = array('status'=>0,'return_msg'=>$result['return_msg']);
        if($result['return_code']=='SUCCESS'){
            if($result['result_code']=='SUCCESS'){
                $res = $this->table('pay_history')->where(array('coder'=>$orderCoder))->update(array('out_trade_no'=>$out_trade_no));
                if($res){
                    $return = array('status'=>1,'return_msg'=>$result['return_msg'],'code_url'=>$result["code_url"],'orderCoder'=>$out_trade_no);
                }
            }
        }
        return $return;
    }

    /**
     * 微信查询订单是否已支付
     * @param $orderCoder
     * @return array
     */
    public function checkWXPay($orderCoder){
        $out_trade_no = $orderCoder;
        $input = new \WxPayOrderQuery();
        $input->SetOut_trade_no($out_trade_no);
        $result  = \WxPayApi::orderQuery($input);
        $return = array('status'=>0,'msg'=>'');
        if($result['return_code']=='SUCCESS'){
            if($result['result_code']=='SUCCESS'){
                if($result['trade_state']=='SUCCESS'){
                    $no = isset($result['transaction_id'])?$result['transaction_id']:'';
                    $res = $this->success($orderCoder,$no);
                    if($res){
                        $return = array('status'=>1,'msg'=>'支付成功');
                    }
                }
            }
        }
        return $return;
    }

    //微信支付回调处理
    public function wxPayResult($data){
        if($data->return_code == 'SUCCESS'){
            if($data->out_trade_no && $data->result_code == 'SUCCESS') {
                $no = isset($data->transaction_id)?$data->transaction_id:'';
                $this->success($data->out_trade_no,$no);
                echo 'success';
            }else{
                echo "fail";
            }
        }else{
            echo "fail";
        }
    }

    /**
     * 支付成功后修改订单相关处理
     * @param $orderCoder
     * @param $transaction_id
     * @return bool
     */
    public function success($orderCoder,$transaction_id){
        $order = $this->getOderByCoder($orderCoder);
        if($order){
            if($order['status']==2){
                $this->table('pay_history')->where(array('coder'=>$orderCoder))->update(array('status'=>1,'order_no'=>$transaction_id));
                if($order['type']==1){//vip
                    $firm = $this->table('firms')->where(array('id'=>$order['firms_id']))->getOne();
                    if($firm){
                        if(strtotime($firm['vip_time'])>time()){
                            $vipTime = date('Y-m-d H:i:s',strtotime("+{$order['vip_month']} month",strtotime($firm['vip_time'])));
                        }else{
                            $vipTime = date('Y-m-d H:i:s',strtotime("+{$order['vip_month']} month"));
                        }
                        $this->table('firms')->where(array('id'=>$order['firms_id']))->update(array('is_vip'=>1,'vip_time'=>$vipTime));
                    }
                }elseif($order['type']==2){//刷新点
                    $sql1 = "update firms set refresh_point=refresh_point+{$order['refresh_point']} where id={$order['firms_id']}";
                    $res1 = $this->query($sql1);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 支付宝
     * @param $order
     * @return array
     */
    public function toAliPay($order){
        if($order['type']==1){
            $body = "充值vip{$order['vip_month']}个月";
            $title= "订单号:[{$order['coder']}],充值vip{$order['vip_month']}个月";
        }elseif($order['type']==2){
            $body = "充值刷新点{$order['refresh_point']}";
            $title= "订单号:[{$order['coder']}],充值刷新点{$order['refresh_point']}个";
        }else{
            return array('status'=>0,'return_msg'=>'参数有误请重试');
        }
        $out_trade_no = $order['coder'];
        $money     = $order['money'];
        $productID = $order['coder'];

        $res = $this->table('pay_history')->where(array('coder'=>$order['coder']))->update(array('out_trade_no'=>$out_trade_no));
        if($res){
            $this->AliPay($body,$title,$out_trade_no,$money);
        }else{
            echo '请稍后重试';
        }

    }

    protected function AliQR(){

    }

    /**
     * 支付宝电脑网站支付
     * @param $body
     * @param $title
     * @param $out_trade_no
     * @param $money
     */
    protected function AliPay($body,$title,$out_trade_no,$money){
        require_once './alipay/config.php';
        require_once './alipay/pagepay/service/AlipayTradeService.php';
        require_once './alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

        //商户订单号，商户网站订单系统中唯一订单号，必填$out_trade_no
        //订单名称，必填
        $subject = $title;
        //付款金额，必填
        $total_amount = $money;
        //商品描述，可空$body

        //构造参数
        $payRequestBuilder = new AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new AlipayTradeService($AliConfig);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder,$AliConfig['return_url'],$AliConfig['notify_url']);

        //输出表单
        var_dump($response);
    }




}
/**
 *
 * 刷卡支付实现类
 * @author widyhu
 *
 */
class NativePay
{
    /**
     *
     * 生成扫描支付URL,模式一
     * @param BizPayUrlInput $bizUrlInfo
     */
    public function GetPrePayUrl($productId)
    {
        $biz = new WxPayBizPayUrl();
        $biz->SetProduct_id($productId);
        $values = WxpayApi::bizpayurl($biz);
        $url = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams($values);
        return $url;
    }

    /**
     *
     * 参数数组转换为url参数
     * @param array $urlObj
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            $buff .= $k . "=" . $v . "&";
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     *
     * 生成直接支付url，支付url有效期为2小时,模式二
     * @param UnifiedOrderInput $input
     */
    public function GetPayUrl($input)
    {
        if($input->GetTrade_type() == "NATIVE")
        {
            $result = WxPayApi::unifiedOrder($input);
            return $result;
        }
    }
}