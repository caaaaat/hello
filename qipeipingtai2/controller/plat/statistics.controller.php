<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10
 * Time: 1:10
 */
class PlatStatisticsController extends Controller
{

    private $user = array();
    public function __construct()
    {
        //检查是否登录
        $mo         = model('suAdmin','mysql');
        $this->user = $mo->loginIs();
    }

    public function lists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.statistics';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $this->template('plat.statistics.list');
        }else{
            dump('没有相关权限');
        }
    }

    public function getLists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.statistics';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data   = $this->getRequest('data' ,'');
            $staMo  = model('plat.statistics.statistics','mysql');
            $return = $staMo->getLists($data);

        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    public function getTotalData(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.statistics';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data   = $this->getRequest('data' ,'');
            $staMo  = model('plat.statistics.statistics','mysql');
            $return = $staMo->getTotalData($data);

        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    public function exportStatisticsToExcel(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.statistics';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data             = array() ;
            $data['type']     = $this->getRequest('type' ,'');
            $data['time1']    = $this->getRequest('time1' ,'');
            $data['time2']    = $this->getRequest('time2' ,'');
            $data['page']     = $this->getRequest('page' ,'');
            $data['pageSize'] = $this->getRequest('pageSize' ,'');
            $staMo  = model('plat.statistics.statistics','mysql');
            $return = $staMo->getLists($data);

            $titleArr= array(1=>'厂商统计',2=>'产品统计',3=>'活跃度统计',4=>'访问统计',5=>'圈子统计',);

            $fileName = $titleArr[$data['type']].'_'.date('ymdHis') . ".csv";//自定义名称
            $headArr  = array(
                1=>array('日期', '新增经销商数', '新增汽修厂数','',''),
                2=>array('日期', '新增发布新品促销数', '新增发布增库存清仓数', '累计发布询价数',''),
                3=>array('日期', '2次', '3-5次', '6-10次', '10次以上'),
                4=>array('日期', '新增访问经销商数（移动端）', '新增访问经销商店铺数（PC web端）', '新增来电数（移动端）'),
                5=>array('日期', '新增动态数', '新增评论数','',''),
            );

            $csvArr = array();//数据
            //dump($logs['list']);die;
            if($return['massageCode']==='success'){
                if($data['type'] == 1){
                    foreach ($return['list'] as $key => $item) {
                        $csvArr[] = array(
                            $item['time'],
                            $item['num1'],
                            $item['num2'],
                        );
                    }
                }elseif ($data['type'] == 2){
                    foreach ($return['list'] as $key => $item) {
                        $csvArr[] = array(
                            $item['time'],
                            $item['num1'],
                            $item['num2'],
                            $item['num3'],
                        );
                    }
                }elseif ($data['type'] == 3){
                    foreach ($return['list'] as $key => $item) {
                        $csvArr[] = array(
                            $item['time'],
                            $item['num1'],
                            $item['num2'],
                            $item['num3'],
                            $item['num4'],
                        );
                    }
                }elseif ($data['type'] == 4){
                    foreach ($return['list'] as $key => $item) {
                        $csvArr[] = array(
                            $item['time'],
                            $item['num2'],
                            $item['num1'],
                            $item['num3'],
                        );
                    }
                }elseif ($data['type'] == 5){
                    foreach ($return['list'] as $key => $item) {
                        $csvArr[] = array(
                            $item['time'],
                            $item['num1'],
                            $item['num2'],
                        );
                    }
                }else{}
            }

            //dump($csvArr);
            $csvMo = model('tools.getCsv', 'mysql');
            echo $csvMo->array2csv($csvArr, $headArr[$data['type']], $fileName);
            unset($csvArr);
            die();
        }else{
            dump('没有相关权限');
        }
    }

    public function getStatisticsInfo(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.statistics';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data   = $this->getRequest('data' ,'');
            $staMo  = model('plat.statistics.statistics','mysql');
            $return = $staMo->getStatisticsInfo($data);

        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }



}