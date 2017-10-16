<?php

/**
 * 操作日志管理控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/11
 * Time: 10:48
 */
class PlatActionController extends Controller
{
    public function lists(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.action';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            /*$logMo    = model('actionLog','mysql');
            $return   = $logMo->getLogs('','','','1','5');
            dump($return);*/
            $this->template('plat.sys.actionLog.list');

        }else{
            dump('没有相关权限');

        }
    }


    public function getLogs(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.action';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $time1    = $this->getRequest('time1','');
            $time2    = $this->getRequest('time2','');
            $keywords = $this->getRequest('keywords','');
            $page     = $this->getRequest('page','1');
            $size     = $this->getRequest('pageSize','10');

            $logMo    = model('actionLog','mysql');
            $return   = $logMo->getLogs($time1,$time2,$keywords,$page,$size);
        }else{
            $return['massageCode']    = 0;
            $return['massage']        = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * /导出Excel
     */
    public function exportToExcel(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.action';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $time1    = $this->getRequest('time1','');
            $time2    = $this->getRequest('time2','');
            $keywords = $this->getRequest('keywords','');
            $page     = 1;
            $size     = 3000;
            $logMo    = model('actionLog','mysql');
            $logs     = $logMo->getLogs($time1,$time2,$keywords,$page,$size);

            $fileName = '操作记录_'.date('ymdHis') . ".csv";//自定义名称
            $head = array('序号', '管理员', '帐号', '操作内容', '时间', 'IP');
            $csvArr = array();//数据
            //dump($logs['list']);die;
            if($logs['massageCode']==='success'){
                foreach ($logs['list'] as $key => $item) {
                    $csvArr[] = array(
                        ++$key,
                        $item['user'],
                        $item['code'],
                        $item['action'],
                        $item['time'],
                        $item['ip'],
                    );
                }
            }

            //dump($csvArr);
            $csvMo = model('tools.getCsv', 'mysql');
            echo $csvMo->array2csv($csvArr, $head, $fileName);
            unset($csvArr);
            die();
        }else{
           dump('没有相关权限');
        }
    }

}