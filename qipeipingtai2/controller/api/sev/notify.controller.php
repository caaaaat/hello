<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/3
 * Time: 13:22
 */
class ApiSevNotifyController extends Controller{


    public function index(){
        header('Access-Control-Allow-Origin: *');
        header('Content-type: text/plain');
        echo 'success';
        exit();
    }

}