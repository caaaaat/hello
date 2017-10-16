<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 23:47
 */
class PlatVipController extends Controller
{

    //==========VIP提醒列表=====================
    public function lists(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.vip';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $this->template('plat.firms.vip');
        }else{
            dump('没有相关权限');

        }
    }

    /**
     * vip到期列表
     */
    public function getVipList(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.vip';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){

            $page    = $this->getRequest('page','1');
            $pageSize= $this->getRequest('pageSize','10');
            $status  = $this->getRequest('status','');
            $modName = $this->getRequest('modName','');
            $keywords= $this->getRequest('keywords','');

            $vipMo   = model('plat.firms.firms','mysql');
            $return  = $vipMo->getVipList($page,$pageSize,$status,$modName,$keywords);
            if($return['massageCode'] == 'success'){
                $list = $return['list'];
                foreach ($list as $k => $item){
                    if(!$item['province']){
                        $area = '';
                    }elseif($item['province']=='全部'){
                        $area = '全部';
                    }elseif($item['city'] == '' || $item['city'] == '全部'){
                        $area = $item['province'];
                    }elseif($item['district'] == '' || $item['district'] == '全部'){
                        $area = $item['province'].'/'.$item['city'];
                    }else{
                        $area = $item['province'].'/'.$item['city'].'/'.$item['district'];
                    }
                    $list[$k]['area'] = $area ;
                }
                $return['list'] = $list ;
            }

        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 导出VPI到期列表
     */
    public function exportVipToExcel(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.vip';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $status   = $this->getRequest('status','');
            $modName  = $this->getRequest('modName','');
            $keywords = $this->getRequest('keywords','');
            $page     = 1;
            $pageSize = 3000;
            $vipMo    = model('plat.firms.firms','mysql');
            $res      = $vipMo->getVipList($page,$pageSize,$status,$modName,$keywords);

            $fileName = 'VIP到期表(一个月内)_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('企业ID', '昵称', '手机号', '企业名称', '企业类型', '企业分类', '所属区域', 'VIP到期时间', '最后一次登录');
            $typeArr  = array(1=>'经销商',2=>'修理厂' );
            $classArr = array(1=>array(1=>'轿车商家',2=>'货车商家',3=>'用品商家'),2=>array(4=>'修理厂',5=>'快修保养',6=>'美容店')) ;
            $csvArr   = array();//数据
            //dump($logs['list']);die;
            if($res['massageCode']==='success'){
                foreach ($res['list'] as $key => $item) {
                    //$area = '';
                    if(!$item['province']){
                        $area = '';
                    }elseif($item['province']=='全部'){
                        $area = '全部';
                    }elseif($item['city'] == '' || $item['city'] == '全部'){
                        $area = $item['province'];
                    }elseif($item['district'] == '' || $item['district'] == '全部'){
                        $area = $item['province'].'/'.$item['city'];
                    }else{
                        $area = $item['province'].'/'.$item['city'].'/'.$item['district'];
                    }
                    $csvArr[] = array(
                        $item['EnterpriseID'],
                        $item['companyname'],
                        $item['phone'],
                        $item['companyname'],
                        $area,
                        $typeArr[$item['type']],
                        $classArr[$item['type']][$item['type']],
                        $item['vip_time'],
                        $item['last_time'],
                    );
                }
            }

            //dump($csvArr);die;
            $csvMo = model('tools.getCsv', 'mysql');
            echo $csvMo->array2csv($csvArr, $head, $fileName);
            unset($csvArr);
            die();
        }else{
            dump('没有相关权限');
        }

    }


}