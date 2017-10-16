<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/4
 * Time: 20:14
 */

class VinController extends Controller{


    /**
     * 根据提交的值获取vin数据
     */
    public function index(){

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
                $client = new SoapClient($url);
                $addResult = $client->__soapCall($method,array(array('xmlInput'=>$data)));
                var_dump( $addResult);
                var_dump( $addResult->LevelDataResult);
            }else{
               die();
                $data = "<root><appkey>".$appKey."</appkey><appsecret>".$appSecret."</appsecret><method>".$method."</method><requestformat>json</requestformat><imgbase64>".$key."</imgbase64></root>";
            }

            $client = new SoapClient($url);
            $addResult = $client->__soapCall($method,array(array('xmlInput'=>$data)));

            $return['list']   = $addResult;
            $return['status'] = 200;
            $return['msg']    = '获取成功';

        }else{

            $return['status'] = 101;
            $return['msg']    = '您提交的数据有误';

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


}