<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/3
 * Time: 9:30
 */


class ApiSevAlipayController extends Controller{
public function payItem(){
    header('Access-Control-Allow-Origin: *');
    header('Content-type: text/plain');
    // 获取支付金额
    $amount='';
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $amount=$_POST['total'];
    }else{
        $amount=$_GET['total'];
    }
    $total = floatval($amount);
    if(!$total){
        $total = 1;
    }
    // 支付宝合作者身份ID，以2088开头的16位纯数字
    $partner = "2088102170075207";
    // 支付宝账号
   $seller_id = 'ixumei@qq.com';
    // 商品网址
    $base_path = urlencode('http://app.7pqun.com');
    // 异步通知地址
    $notify_url = urlencode('http://app.7pqun.com/api.sev.alipay/notify');
// 订单标题
    $subject = 'DCloud项目捐赠';
// 订单详情
    $body = 'DCloud致力于打造HTML5最好的移动开发工具，包括终端的Runtime、云端的服务和IDE，同时提供各项配套的开发者服务。';
// 订单号，示例代码使用时间值作为唯一的订单ID号
    $out_trade_no = date('YmdHis', time());

    $parameter = array(
        'service'        => 'mobile.securitypay.pay',   // 必填，接口名称，固定值
        'partner'        => $partner,                   // 必填，合作商户号
        '_input_charset' => 'UTF-8',                    // 必填，参数编码字符集
        'out_trade_no'   => $out_trade_no,              // 必填，商户网站唯一订单号
        'subject'        => $subject,                   // 必填，商品名称
        'payment_type'   => '1',                        // 必填，支付类型
        'seller_id'      => $seller_id,                 // 必填，卖家支付宝账号
        'total_fee'      => $total,                     // 必填，总金额，取值范围为[0.01,100000000.00]
        'body'           => $body,                      // 必填，商品详情
        'it_b_pay'       => '1d',                       // 可选，未付款交易的超时时间
        'notify_url'     => $notify_url,                // 可选，服务器异步通知页面路径
        'show_url'       => $base_path                  // 可选，商品展示网站
    );
    //生成需要签名的订单
    $orderInfo = $this->createLinkstring($parameter);
    //签名
    $sign = $this->rsaSign($orderInfo);

    //生成订单
    echo $orderInfo.'&sign="'.$sign.'"&sign_type="RSA"';

//    exit();

}

    // 对签名字符串转义
    public function createLinkstring($para) {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            $arg.=$key.'="'.$val.'"&';
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);
        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
        return $arg;
    }
    // 签名生成订单信息
    public function rsaSign($data) {
        $priKey = "-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAxnOIZKvvR4EauuxPe8+lMmNTRU5aqith+mQaFg/gR5gBvk1og//CTJlYjuXAgxxgO0M+I/IVgTtCmcqbXgjb7U4gQcyVTpfFLpsfLYz2v8jeiadYbeT4W/IMXcvKmXp1cOxH3OBv4Xltz6vFy4ss1cJ/gQyMMYBIHN4IGXXw8bHCyEYpoDnyNjahbvvrad0RSB9Akvm91MJDQEmufZ2BB9HWiX64zXnAXHIQbXIO93AffqN5KPQHub/4xMwqDvzF+bEb6Vi/yR6aNA78Y0C+kKPEIZlHtInBsLHMhqkrPPkCgKFL8jyHOyXxZx7dEOm+FEwlWpn7ZXpkGI+Y3w2cPwIDAQABAoIBAQDFKU1t71/n432CDnsdX/wZJpM5fRIYlMdf9Anyt0008/FvdwqKchRA8+0G834jBJMa7cCUB9STsyOFFcTsVNLjXkYv+Sixj5mopxb/s1gGzHNDwY3aiKyy9LSSj4C2oPKDAUyYRicBlRmjRF5bzeb6bKUuuh+ioneCrpjPaty50gjVuUowYgOdCdAAhbjN4qzzPd1CGt5x9BpyEvjZ4Fq6hhMYIc1M0br2l6ywgVhk/odT+tnHODXGmvi6N5mD1eWzxsZ5EVZ2Sl+ZpjccrdjFeZb65wNqxKXAwqBm5KCmGUMZSGUPE8ZRiCavbSdlzdoFrXoBw+6RPXfe0l8CcgzxAoGBAOc947npy/OVPbK4SxGSZxMHOWSi/bIdmCt4XqCiOzKxPD+9t4JMx5jgGVk5fMcOSSk/tzMWW+rGqxkUrFyp2bUfKKXrTaNi4IjvGreH8YqQ5YjlaZXMsIdciZ71h3ZN/9DBcPl+YIxVjylneUOqzvr52S1WuULjAa2XcddA50bXAoGBANuy444oTNWUxkGK3V5r9QpWCiSsxVVVj4G+qEML1oqz670zVDotLHbBoBdqgc5N7VdDeqiyQS+kTFVtZrRfKLq33Gwh1LaPi81uTxvnbVyd2lt5NwS+l6v5ayiaXwQszO0Om61WRQXjh+URVB1MHl9FLmmEEJemv+eVTgfuD/DZAoGAdu+SLZFe4U4licLYeZU/hr30exqKOg6WseUbZquKnywhvPcrZ81t6+d3oji7QPbMEnc/FvutEzhT0HadoJuL6mi4U36PVDYLHuM8bqFxTr/wD1VP1UiOk1C5SBUpM2Qy64BTR0AFEKkBFV6vNGqqQtQ3K+arKwfvWQXH+9raGckCgYBRYU5RVjQ/2UAm/x1I4IyAK6bONwFRvsPNt6X0T+pErqjgCKdmdV1HECoRAm7a0Jrd/CzvWDg1QZLVAhVNMwKPR5Pqqg11Im8SxY2gNHWaHQ7JW3k51K+yEE3VWHlhvoaaORMJfi9LIyEvhN+3in6lo6axhy3uPuJPEks5PMHC4QKBgQDF6x7qFR+sraIfy6AINoXcqrqnYrVFE5TQQuuhHqWHOEAzLlPkFSyizgqdTuepLL+Sfucd8wlsbKrCP7nCFEsUX4gdfUvoyNZ/Lr4aPJdqQi1ECywmQsmkg28m5bZxsnd/+/NC2SCj/ioRrVw2I2Ut0mPOWDEnW37QHiso85fFJA==
-----END RSA PRIVATE KEY-----";
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);
        return $sign;
    }


}





?>