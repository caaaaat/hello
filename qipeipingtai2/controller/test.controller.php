<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 17:03
 */
class TestController  extends Controller
{

    public function QR(){
        $firmMo = model('web.firms','mysql');
        $firms  = $firmMo->table('firms')->get();
        foreach ($firms as $v){
            $path = $firmMo->getQRStore($v['EnterpriseID'],$v['companyname'],$v['type']);
            dump($path);
            $firmMo->table('firms')->where(array('id'=>$v['id']))->update(array('QR_pic'=>$path));
        }

    }
}