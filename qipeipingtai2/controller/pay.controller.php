<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/29
 * Time: 17:29
 */

class PayController extends Controller
{
    //微信扫码支付回调
    public function wxnotify(){
        error_reporting(0);
        //dump($_REQUEST);
        //writeLog($_REQUEST);
        //writeLog($GLOBALS['HTTP_RAW_POST_DATA']);
        //写入日志，测试
        ini_set('date.timezone','Asia/Shanghai');
        //error_reporting(E_ERROR);
        //测试xml，微信传递过来的数据
        /*$GLOBALS['HTTP_RAW_POST_DATA'] = '<xml>
  <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
  <attach><![CDATA[支付测试]]></attach>
  <bank_type><![CDATA[CFT]]></bank_type>
  <fee_type><![CDATA[CNY]]></fee_type>
  <is_subscribe><![CDATA[Y]]></is_subscribe>
  <mch_id><![CDATA[10000100]]></mch_id>
  <nonce_str><![CDATA[fblxdfq5d4wlqgcu8l01nhkg72hp9sqa]]></nonce_str>
  <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
  <out_trade_no><![CDATA[201612081722278659]]></out_trade_no>
  <result_code><![CDATA[SUCCESS]]></result_code>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
  <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
  <time_end><![CDATA[20170903131540]]></time_end>
  <total_fee>5100</total_fee>
  <trade_type><![CDATA[JSAPI]]></trade_type>
  <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
</xml>';*/
        //writeLog($GLOBALS['HTTP_RAW_POST_DATA']);
        require_once APPROOT."/wxpay/lib/WxPay.Api.php";
        require_once APPROOT.'/wxpay/lib/WxPay.Notify.php';
        require_once APPROOT.'/wxpay/example/log.php';
        $logHandler= new CLogFileHandler(APPROOT."/wxpay/logs/".date('Y-m-d').'.log');
        $log = Log::Init($logHandler, 15);
        require_once APPROOT.'/wxpay/example/notifyrel.php';
        //日志
        Log::DEBUG("begin notify");
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
        $GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents("php://input");
        //$isPayYes = isset(core::$G['weixinpay']) ? core::$G['weixinpay'] : false;
        writeLog($GLOBALS['HTTP_RAW_POST_DATA'],'./data/log/wxpay/'.date("Y-m-d").'.txt');
        $getXml    = simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);
        writeLog($getXml,'./data/log/wxpay/'.date("Y-m-d").'.txt');
        //$isPayYes  = true;
        //if($isPayYes){
        if($getXml){
            //writeLog('isPayYes:'.$isPayYes);
            //我们的回调处理
            $payMo = model('web.pay');
            $payMo->wxPayResult($getXml);
        }
        exit;
    }

    public function alinotify(){
        require_once './alipay/config.php';
        require_once './alipay/pagepay/service/AlipayTradeService.php';

        $arr=$_POST;
        writeLog($arr,'./data/log/alipay/'.date("Y-m-d").'.txt');
        $alipaySevice = new AlipayTradeService($AliConfig);
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        writeLog($result,'./data/log/alipay/'.date("Y-m-d").'.txt');
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号

            $out_trade_no = $_POST['out_trade_no'];
            writeLog($out_trade_no,'./data/log/alipay/'.date("Y-m-d").'.txt');
            //支付宝交易号

            $trade_no = $_POST['trade_no'];
            writeLog($trade_no,'./data/log/alipay/'.date("Y-m-d").'.txt');
            //交易状态
            $trade_status = $_POST['trade_status'];


            if($_POST['trade_status'] == 'TRADE_FINISHED') {

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                $payMo = model('web.pay');
                $payMo->success($out_trade_no,$trade_no);
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                $payMo = model('web.pay');
                $payMo->success($out_trade_no,$trade_no);
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success";	//请不要修改或删除
        }else {
            //验证失败
            echo "fail";

        }
    }
}