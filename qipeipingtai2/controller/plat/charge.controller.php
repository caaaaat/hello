<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10
 * Time: 1:09
 */
class PlatChargeController  extends Controller
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
        $mod    = 'plat.charge';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $this->template('plat.charge.list');
        }else{
            dump('没有相关权限');
        }
    }
    //====================充值==================
    /**
     * 选择经销商
     */
    public function choiceFirm(){
        $type = $this->getRequest('type' ,'');

        $this->assign('type',$type) ;
        $this->template('plat.charge.choiceFirm') ;
    }

    public function getChoiceFirm(){
        $data       = $this->getRequest('data'    ,'');
        $chargeMo   = model('plat.charge.charge','mysql');
        $choiceFirm = $chargeMo->getChoiceFirm($data) ;
        exit(json_encode($choiceFirm,JSON_UNESCAPED_UNICODE));
    }

    public function chargeMoney(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.charge';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data       = $this->getRequest('data' ,'');
            $chargeMo   = model('plat.charge.charge','mysql');
            $res        = $chargeMo->chargeMoney($data)  ;
            if ($res){
                $return['massageCode'] = 'success' ;
                $return['massage'] = '充值成功';
            }else{
                $return['massage'] = '充值失败';
            }
        }else{
            $return['massage'] = '没有相关权限' ;

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //====================充值记录==================
    /**
     * 获取列表统一入口
     */
    public function getLists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.charge';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data       = $this->getRequest('data' ,'');
            $chargeMo   = model('plat.charge.charge','mysql');
            $res        = $chargeMo->getLists($data)  ;
            $return     = $res ;
        }else{
            $return['massage'] = '没有相关权限' ;

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    public function exportToExcel(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.charge';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){

            $data['style']    = '2';
            $data['type']     = $this->getRequest('type'     ,'');
            $data['firm']     = $this->getRequest('firm'     ,'');
            $data['keywords'] = $this->getRequest('keywords' ,'');
            $data['page']     = $this->getRequest('page'     ,'');
            $data['pageSize'] = $this->getRequest('pageSize' ,'');
            $chargeMo = model('plat.charge.charge','mysql');
            $charge  = $chargeMo->getLists($data);

            $fileName = '充值收费记录_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('充值类型', '企业ID', '企业名称', '企业类型', '金额（元）', '详情', '收费人', '收费人账号', '收费时间');
            $fTypeArr = array(0=>'',1=>'经销商',2=>'汽修厂' );
            $typeArr  = array( 0=>'',1=>'充值VIP',2=>'充值刷新点',3=>'其他' );
            $csvArr   = array();//数据
            //dump($charge['list']);die;
            if($charge['massageCode']==='success'){
                foreach ($charge['list'] as $key => $item) {
                    $firm_type = $item['firm_type'] == null ? '' : $fTypeArr[$item['firm_type']] ;
                    $csvArr[] = array(
                        $typeArr[$item['type']],
                        $item['EnterpriseID']."\n",
                        $item['companyname']."\n",
                        $firm_type,
                        $item['money'],
                        $item['content'] ,
                        $item['name'],
                        $item['code'],
                        $item['create_time']."\n",
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