<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/6
 * Time: 22:15
 */


class ApiSevPayModel extends Model{

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
     * 获取订单支付记录
     * @param $coder
     */
    public function getPayStatus($coder){

        $res   = $this->table('pay_history')->where(array('coder'=>$coder))->getOne();
        if($res){
            return array('status'=>200,'msg'=>'查询支付状态成功','payStatus'=>$res['status']);
        }else{
            return array('status'=>201,'msg'=>'查询支付状态失败');
        }

    }


    /**
     * 保存vip订单
     * @param $payType
     * @param $month
     * @param $user
     * @return array
     */
    public function toVipOrder($payType,$month,$userId){

        switch ($payType){
            case 'wxpay':  $payway = 1;break;
            case 'alipay': $payway = 2;break;
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
                    'firms_id'=>$userId,'money'=>$money,
                    'create_time'=>date('Y-m-d H:i:s',time()),
                    'coder'=>$this->toOrderCoder()
                );
                $res = $this->table('pay_history')->insert($data);
                if($res){
                    return array('status'=>200,'msg'=>'订单创建成功','coder'=>$data['coder']);
                }else{
                    return array('status'=>0,'msg'=>'订单创建失败，请稍后再试');
                }
            }else{
                return array('status'=>0,'msg'=>'参数错误，请重试');
            }
        }else{
            return array('status'=>0,'msg'=>'暂时不能充值');
        }
    }

    /**
     * 保存刷新点订单
     */
    public function toRefreshOrder($payType,$moneyType,$shuMoney,$userId){

        switch ($payType){
            case 'wxpay':  $payway = 1;break;
            case 'alipay': $payway = 2;break;
            default:return array('status'=>0,'msg'=>'请选择支付方式');
        }
        $refreshRule = $this->getRefreshPointRule();
        if($refreshRule){
            if($moneyType=='radio'){
                if(isset($refreshRule['select'])){
                    $money = 0;
                    foreach ($refreshRule['select'] as $v){
                        if($v['money']==$shuMoney){
                            $money=$v['money'];
                            $refreshPoint=$v['number'];
                        }
                    }

                    if($money){
                        $data = array(
                            'type'=>2,'status'=>2,'info'=>'充值刷新点'.$refreshPoint.'个',
                            'payway'=>$payway,'refresh_point'=>$refreshPoint,
                            'firms_id'=>$userId,'money'=>$money,
                            'create_time'=>date('Y-m-d H:i:s',time()),
                            'coder'=>$this->toOrderCoder()
                        );
                        $res = $this->table('pay_history')->insert($data);
                        if($res){
                            return array('status'=>200,'msg'=>'订单创建成功，请支付','coder'=>$data['coder']);
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
                                'firms_id'=>$userId,'money'=>$shuMoney,
                                'create_time'=>date('Y-m-d H:i:s',time()),
                                'coder'=>$this->toOrderCoder()
                            );

                            $res = $this->table('pay_history')->insert($data);
                            if($res){
                                return array('status'=>200,'msg'=>'订单创建成功，请支付','coder'=>$data['coder']);
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
     * 支付宝
     * @param $order
     * @return array
     */
    public function toAliPay($order){
        if($order['type']==1){
            $title = "充值vip{$order['vip_month']}个月";
            $body= "订单号:[{$order['coder']}],充值vip{$order['vip_month']}个月";
        }elseif($order['type']==2){
            $title = "充值刷新点{$order['refresh_point']}点";
            $body= "订单号:[{$order['coder']}],充值刷新点{$order['refresh_point']}个";
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

    /**
     * 支付宝移动网站
     * @param $order
     * @return array
     */
    public function toAliWapPay($order){
        if($order['type']==1){
            $title = "充值vip{$order['vip_month']}个月";
            $body= "订单号:[{$order['coder']}],充值vip{$order['vip_month']}个月";
        }elseif($order['type']==2){
            $title = "充值刷新点{$order['refresh_point']}点";
            $body= "订单号:[{$order['coder']}],充值刷新点{$order['refresh_point']}个";
        }else{
            return array('status'=>0,'return_msg'=>'参数有误请重试');
        }
        $out_trade_no = $order['coder'];
        $money     = $order['money'];
        $productID = $order['coder'];

        $res = $this->table('pay_history')->where(array('coder'=>$order['coder']))->update(array('out_trade_no'=>$out_trade_no));
        if($res){

            $result = $this->AliWapPay($body,$title,$out_trade_no,$money,$order['type']);

            $return = array('info'=>$result,'status'=>200,'msg'=>'订单提交成功');

        }else{
            $return = array('msg'=>'请稍候重试','status'=>201);
        }

        return $return;
    }

    /**
     * 微信
     * @param $order
     * @return array
     */
    public function WeChatPay($order){

        if($order['type']==1){
            $title = "充值vip{$order['vip_month']}个月";
//            $title= "订单号:[{$order['coder']}],充值vip{$order['vip_month']}个月";
        }elseif($order['type']==2){
            $title = "充值刷新点{$order['refresh_point']}";
//            $title= "订单号:[{$order['coder']}],充值刷新点{$order['refresh_point']}个";
        }else{
            return array('status'=>0,'return_msg'=>'参数有误请重试');
        }
        $out_trade_no = $order['coder'];
        $money     = $order['money']*100;

        $this->wxPay($title,$out_trade_no,$money);

    }



    /**
     * 根据订单号获取订单
     * @param $coder
     * @return mixed
     */
    public function getOrderByCoder($coder){
        $res = $this->table('pay_history')->where(array('coder'=>$coder))->getOne();
        return $res;
    }

    /**
     * 微信支付
     * @param $subject
     * @param $out_trade_no
     * @param $money
     */
    public function wxPay($subject,$out_trade_no,$money){

        header('Access-Control-Allow-Origin: *');
        header('Content-type: text/plain');

        require_once APPROOT."/wxpayv3/WxPay.Api.php";
        require_once APPROOT."/wxpayv3/WxPay.Data.php";

        $unifiedOrder = new WxPayUnifiedOrder();
        $unifiedOrder->SetBody($subject);//商品或支付单简要描述
        $unifiedOrder->SetOut_trade_no($out_trade_no);
        $unifiedOrder->SetTotal_fee($money);
        $unifiedOrder->SetTrade_type("APP");
        $result = WxPayApi::unifiedOrder($unifiedOrder);
        if (is_array($result)) {
            echo json_encode($result);
        }

    }


    /**
     * 微信客户端支付
     * @param $order
     * @return array
     */
    public function WeChatWapPay($order){

        if($order['type']==1){
            $title = "充值vip{$order['vip_month']}个月";
//            $title= "订单号:[{$order['coder']}],充值vip{$order['vip_month']}个月";
        }elseif($order['type']==2){
            $title = "充值刷新点{$order['refresh_point']}";
//            $title= "订单号:[{$order['coder']}],充值刷新点{$order['refresh_point']}个";
        }else{
            return array('status'=>0,'return_msg'=>'参数有误请重试');
        }
        $out_trade_no = $order['coder'];
        $money     = $order['money']*100;

        $result = $this->wxWapPay($title,$out_trade_no,$money,$order['type']);

        if($result){

            $return = array('info'=>$result,'status'=>200,'msg'=>'订单提交成功');

        }else{
            $return = array('msg'=>'请稍候重试','status'=>201);
        }

        return $return;

    }

    /**
     * 微信客户端支付返回数据
     * @param $subject
     * @param $out_trade_no
     * @param $money
     * @param $type
     * @return mixed
     */
    public function wxWapPay($subject,$out_trade_no,$money,$type){

        require_once APPROOT."/wxpay/lib/WxPay.Api.php";
        require_once APPROOT.'/wxpay/lib/WxPay.Notify.php';
        require_once APPROOT.'/wxpay/example/log.php';

        $input  = new WxPayUnifiedOrder();
        //$input->SetAppid($Appid);//公众账号ID
        //$input->SetMch_id($Mch_id);//商户号
        //$input->SetNonce_str($Nonce_str);//随机字符串
        $input->SetBody($subject);//商品描述
        $input->SetAttach($subject);//附加数据
        $input->SetOut_trade_no($out_trade_no);//商户订单号
        $input->SetTotal_fee($money);//订单总金额
        $input->SetTime_start(date("YmdHis"));//交易起始时间
        $input->SetTime_expire(date("YmdHis", time() + 1800));//交易结束时间
        //$input->SetGoods_tag("test");//订单优惠标记
        $input->SetNotify_url("http://app.7pqun.com/pay/wxnotify");
        $input->SetTrade_type("MWEB");
        writeLog($input);
        $result = WxPayApi::unifiedOrder($input);
        writeLog($result);
        $return = array('status'=>0,'return_msg'=>$result['return_msg']);
        if($result['return_code']=='SUCCESS'){
            if($result['result_code']=='SUCCESS'){
                $res = $this->table('pay_history')->where(array('coder'=>$out_trade_no))->update(array('out_trade_no'=>$out_trade_no));
                if($res){

                    if($type==1){//vip充值
                        $mweb_url = $result["mweb_url"].'&redirect_url='.urlencode('http://app.7pqun.com/weixin/view/person/VIP/payResult.html?coder='.$out_trade_no);
                    }else{//刷新点
                        $mweb_url = $result["mweb_url"].'&redirect_url='.urlencode('http://app.7pqun.com/weixin/view/person/refresh/payResult.html?coder='.$out_trade_no);
                    }

                    $return = array('status'=>200,'return_msg'=>$result['return_msg'],'mweb_url'=>$mweb_url,'orderCoder'=>$out_trade_no);

                }
            }
        }
        return $return;

    }





    /**
     * 阿里移动网站支付
     */
    public function AliWapPay($body,$subject,$out_trade_no,$money,$type){

        header('Access-Control-Allow-Origin: *');
        header('Content-type: text/plain');

        require_once APPROOT.'/alipayrsa2/service/AlipayTradeService.php';
        require_once APPROOT.'/alipayrsa2/buildermodel/AlipayTradeWapPayContentBuilder.php';


        if($type==1){//vip充值

            $return_url = 'http://app.7pqun.com/weixin/view/person/VIP/payResult.html?coder='.$out_trade_no;

        }else{//刷新点

            $return_url = 'http://app.7pqun.com/weixin/view/person/refresh/payResult.html?coder='.$out_trade_no;

        }

        $config = array (
            //应用ID,您的APPID。
            'app_id' => "2017060907455218",

            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => "MIIEpAIBAAKCAQEAxnOIZKvvR4EauuxPe8+lMmNTRU5aqith+mQaFg/gR5gBvk1og//CTJlYjuXAgxxgO0M+I/IVgTtCmcqbXgjb7U4gQcyVTpfFLpsfLYz2v8jeiadYbeT4W/IMXcvKmXp1cOxH3OBv4Xltz6vFy4ss1cJ/gQyMMYBIHN4IGXXw8bHCyEYpoDnyNjahbvvrad0RSB9Akvm91MJDQEmufZ2BB9HWiX64zXnAXHIQbXIO93AffqN5KPQHub/4xMwqDvzF+bEb6Vi/yR6aNA78Y0C+kKPEIZlHtInBsLHMhqkrPPkCgKFL8jyHOyXxZx7dEOm+FEwlWpn7ZXpkGI+Y3w2cPwIDAQABAoIBAQDFKU1t71/n432CDnsdX/wZJpM5fRIYlMdf9Anyt0008/FvdwqKchRA8+0G834jBJMa7cCUB9STsyOFFcTsVNLjXkYv+Sixj5mopxb/s1gGzHNDwY3aiKyy9LSSj4C2oPKDAUyYRicBlRmjRF5bzeb6bKUuuh+ioneCrpjPaty50gjVuUowYgOdCdAAhbjN4qzzPd1CGt5x9BpyEvjZ4Fq6hhMYIc1M0br2l6ywgVhk/odT+tnHODXGmvi6N5mD1eWzxsZ5EVZ2Sl+ZpjccrdjFeZb65wNqxKXAwqBm5KCmGUMZSGUPE8ZRiCavbSdlzdoFrXoBw+6RPXfe0l8CcgzxAoGBAOc947npy/OVPbK4SxGSZxMHOWSi/bIdmCt4XqCiOzKxPD+9t4JMx5jgGVk5fMcOSSk/tzMWW+rGqxkUrFyp2bUfKKXrTaNi4IjvGreH8YqQ5YjlaZXMsIdciZ71h3ZN/9DBcPl+YIxVjylneUOqzvr52S1WuULjAa2XcddA50bXAoGBANuy444oTNWUxkGK3V5r9QpWCiSsxVVVj4G+qEML1oqz670zVDotLHbBoBdqgc5N7VdDeqiyQS+kTFVtZrRfKLq33Gwh1LaPi81uTxvnbVyd2lt5NwS+l6v5ayiaXwQszO0Om61WRQXjh+URVB1MHl9FLmmEEJemv+eVTgfuD/DZAoGAdu+SLZFe4U4licLYeZU/hr30exqKOg6WseUbZquKnywhvPcrZ81t6+d3oji7QPbMEnc/FvutEzhT0HadoJuL6mi4U36PVDYLHuM8bqFxTr/wD1VP1UiOk1C5SBUpM2Qy64BTR0AFEKkBFV6vNGqqQtQ3K+arKwfvWQXH+9raGckCgYBRYU5RVjQ/2UAm/x1I4IyAK6bONwFRvsPNt6X0T+pErqjgCKdmdV1HECoRAm7a0Jrd/CzvWDg1QZLVAhVNMwKPR5Pqqg11Im8SxY2gNHWaHQ7JW3k51K+yEE3VWHlhvoaaORMJfi9LIyEvhN+3in6lo6axhy3uPuJPEks5PMHC4QKBgQDF6x7qFR+sraIfy6AINoXcqrqnYrVFE5TQQuuhHqWHOEAzLlPkFSyizgqdTuepLL+Sfucd8wlsbKrCP7nCFEsUX4gdfUvoyNZ/Lr4aPJdqQi1ECywmQsmkg28m5bZxsnd/+/NC2SCj/ioRrVw2I2Ut0mPOWDEnW37QHiso85fFJA==",

            //异步通知地址
            'notify_url' => "http://app.7pqun.com/pay/alinotify",

            //同步跳转
            'return_url' => $return_url,

            //编码格式
            'charset' => "UTF-8",

            //签名方式
            'sign_type'=>"RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnDsADRzZYjynNILO8pqGYQfpVpXVYWU4nOAlvz7b37gtof3eB96Rmspl4/9Fu+W9kKv3MIM6Vn076akHo9WPGItosMndGNQWW2eFnZ+L/qh4uqm8TAntQLT0ikRXJyTYBdfj64ij+FeDEwGhVz/pjBPyvYaHruHCVoUqLyhqCVC63bZdawIah4uvXBTrmHWK2KAtWZbxgGq5DaDzmph6waRFc4ppHWBI3kHc62vRmfgU24DMETNapM5KAVf3vqlRk6Q96oHTn0MPA+NAf3Tgbp7iISo2lBsXmdojz3tpulcM1s4stsASp93qe9+Qrvjt4GsbF3A73+EpB2UvoGwibwIDAQAB",


        );


        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($money);
        $payRequestBuilder->setTimeExpress('1m');

        $payResponse = new AlipayTradeService($config);

        $res = $payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        return $res;
    }


      public function AliPay($body,$subject,$out_trade_no,$money){

        header('Access-Control-Allow-Origin: *');
        header('Content-type: text/plain');

        require_once APPROOT.'/alipayrsa2/aop/AopClient.php';
        require_once APPROOT.'/alipayrsa2/aop/request/AlipayTradeAppPayRequest.php';

        $aop = new AopClient;
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = "2017060907455218";
        $aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEAxnOIZKvvR4EauuxPe8+lMmNTRU5aqith+mQaFg/gR5gBvk1og//CTJlYjuXAgxxgO0M+I/IVgTtCmcqbXgjb7U4gQcyVTpfFLpsfLYz2v8jeiadYbeT4W/IMXcvKmXp1cOxH3OBv4Xltz6vFy4ss1cJ/gQyMMYBIHN4IGXXw8bHCyEYpoDnyNjahbvvrad0RSB9Akvm91MJDQEmufZ2BB9HWiX64zXnAXHIQbXIO93AffqN5KPQHub/4xMwqDvzF+bEb6Vi/yR6aNA78Y0C+kKPEIZlHtInBsLHMhqkrPPkCgKFL8jyHOyXxZx7dEOm+FEwlWpn7ZXpkGI+Y3w2cPwIDAQABAoIBAQDFKU1t71/n432CDnsdX/wZJpM5fRIYlMdf9Anyt0008/FvdwqKchRA8+0G834jBJMa7cCUB9STsyOFFcTsVNLjXkYv+Sixj5mopxb/s1gGzHNDwY3aiKyy9LSSj4C2oPKDAUyYRicBlRmjRF5bzeb6bKUuuh+ioneCrpjPaty50gjVuUowYgOdCdAAhbjN4qzzPd1CGt5x9BpyEvjZ4Fq6hhMYIc1M0br2l6ywgVhk/odT+tnHODXGmvi6N5mD1eWzxsZ5EVZ2Sl+ZpjccrdjFeZb65wNqxKXAwqBm5KCmGUMZSGUPE8ZRiCavbSdlzdoFrXoBw+6RPXfe0l8CcgzxAoGBAOc947npy/OVPbK4SxGSZxMHOWSi/bIdmCt4XqCiOzKxPD+9t4JMx5jgGVk5fMcOSSk/tzMWW+rGqxkUrFyp2bUfKKXrTaNi4IjvGreH8YqQ5YjlaZXMsIdciZ71h3ZN/9DBcPl+YIxVjylneUOqzvr52S1WuULjAa2XcddA50bXAoGBANuy444oTNWUxkGK3V5r9QpWCiSsxVVVj4G+qEML1oqz670zVDotLHbBoBdqgc5N7VdDeqiyQS+kTFVtZrRfKLq33Gwh1LaPi81uTxvnbVyd2lt5NwS+l6v5ayiaXwQszO0Om61WRQXjh+URVB1MHl9FLmmEEJemv+eVTgfuD/DZAoGAdu+SLZFe4U4licLYeZU/hr30exqKOg6WseUbZquKnywhvPcrZ81t6+d3oji7QPbMEnc/FvutEzhT0HadoJuL6mi4U36PVDYLHuM8bqFxTr/wD1VP1UiOk1C5SBUpM2Qy64BTR0AFEKkBFV6vNGqqQtQ3K+arKwfvWQXH+9raGckCgYBRYU5RVjQ/2UAm/x1I4IyAK6bONwFRvsPNt6X0T+pErqjgCKdmdV1HECoRAm7a0Jrd/CzvWDg1QZLVAhVNMwKPR5Pqqg11Im8SxY2gNHWaHQ7JW3k51K+yEE3VWHlhvoaaORMJfi9LIyEvhN+3in6lo6axhy3uPuJPEks5PMHC4QKBgQDF6x7qFR+sraIfy6AINoXcqrqnYrVFE5TQQuuhHqWHOEAzLlPkFSyizgqdTuepLL+Sfucd8wlsbKrCP7nCFEsUX4gdfUvoyNZ/Lr4aPJdqQi1ECywmQsmkg28m5bZxsnd/+/NC2SCj/ioRrVw2I2Ut0mPOWDEnW37QHiso85fFJA==';
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnDsADRzZYjynNILO8pqGYQfpVpXVYWU4nOAlvz7b37gtof3eB96Rmspl4/9Fu+W9kKv3MIM6Vn076akHo9WPGItosMndGNQWW2eFnZ+L/qh4uqm8TAntQLT0ikRXJyTYBdfj64ij+FeDEwGhVz/pjBPyvYaHruHCVoUqLyhqCVC63bZdawIah4uvXBTrmHWK2KAtWZbxgGq5DaDzmph6waRFc4ppHWBI3kHc62vRmfgU24DMETNapM5KAVf3vqlRk6Q96oHTn0MPA+NAf3Tgbp7iISo2lBsXmdojz3tpulcM1s4stsASp93qe9+Qrvjt4GsbF3A73+EpB2UvoGwibwIDAQAB';
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new AlipayTradeAppPayRequest();

        // 异步通知地址
        $notify_url = urlencode('http://app.7pqun.com/pay/alinotify');
        // 订单标题
//        $subject = '汽配群刷新点充值';
        // 订单详情
//        $body = '您正在充值刷新点，充值点数200点';
        // 订单号，示例代码使用时间值作为唯一的订单ID号
//        $out_trade_no = date('YmdHis', time());

        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent = "{\"body\":\"".$body."\","
            . "\"subject\": \"".$subject."\","
            . "\"out_trade_no\": \"".$out_trade_no."\","
            . "\"timeout_express\": \"30m\","
            . "\"total_amount\": \"".$money."\","
            . "\"product_code\":\"QUICK_MSECURITY_PAY\""
            . "}";
        $request->setNotifyUrl($notify_url);
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);

        // 注意：这里不需要使用htmlspecialchars进行转义，直接返回即可
        echo $response;

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