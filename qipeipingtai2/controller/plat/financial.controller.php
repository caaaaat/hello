<?php

/**
 * 工资管理控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/29
 * Time: 22:55
 */
class PlatFinancialController extends Controller
{
    private $user = array();
    public function __construct()
    {
        //检查是否登录
        $mo         = model('suAdmin','mysql');
        $this->user = $mo->loginIs();
    }
    //================== 工资配置 ====================
    public function lists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $this->template('plat.financial.list');
        }else{
            dump('没有相关权限');

        }
    }

    /**
     * 获取业务员工资配置列表
     */
    public function getSales(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data        = $this->getRequest('data' ,'');
            $finMo = model('plat.sales.financial','mysql');
            $return      = $finMo->getSales($data);

        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 导出数据
     */
    public function exportSalesToExcel(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data['province'] = $this->getRequest('province' ,'');
            $data['city']     = $this->getRequest('city'     ,'');
            $data['county']   = $this->getRequest('county'   ,'');
            $data['order']    = $this->getRequest('order'    ,'');
            $data['keywords'] = $this->getRequest('keywords' ,'');
            $data['page']     = $this->getRequest('page'     ,'');
            $data['pageSize'] = $this->getRequest('pageSize' ,'');
            $vipMo    = model('plat.sales.financial','mysql');
            $company  = $vipMo->getSales($data);

            $fileName = '业务员基本工资配置_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('业务员ID', '昵称', '姓名', '联系电话', '管辖区域', '基本工资（元）', '补贴（元）');
            $csvArr   = array();//数据
            //dump($company['list']);
            if($company['massageCode']==='success'){
                foreach ($company['list'] as $key => $item) {
                    $csvArr[] = array(
                        $item['uId']."\n",
                        $item['uname'],
                        $item['realname'],
                        $item['phone']."\n",
                        $item['area'],
                        $item['base_wage'] == null ? 0 : $item['base_wage'],
                        $item['subsidies'] == null ? 0 : $item['subsidies'],
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

    /**
     * 修改基本工资 和补贴
     */
    public function editWage(){//wageIni
        $uid     = $this->getRequest('id','');
        $type    = $this->getRequest('type','');
        $finMo  = model('plat.sales.financial','mysql');

        if($type == 1){
            $wageIni = $finMo->getWageIni($uid);//基本工资
        }else{
            $wageIni = $finMo->getOneWage($uid);//工资记录
        }
        $this->assign('type',$type);
        $this->assign('uid',$uid);
        $this->assign('wageIni',$wageIni);
        $this->template('plat.financial.wageIni');
    }
    /**
     * 保存基本工资 和补贴
     */
    public function saveWageIni(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data    = $this->getRequest('data' ,'');
            $salesMo = model('plat.sales.financial','mysql');
            $res     = $salesMo->saveWageIni($data);
            if($res){
                $return['massageCode'] = 'success';
                $return['massage']     = '配置成功';
            }else{
                $return['massage']     = '配置失败';
            }
        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //================== 工资记录 ====================
    /**
     *
     */
    public function getWage(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data    = $this->getRequest('data' ,'');
            $finMo   = model('plat.sales.financial','mysql');
            $return  = $finMo->getWage($data);
        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 保存本月工资
     */
    public function saveWage(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data    = $this->getRequest('data' ,'');
            $salesMo = model('plat.sales.financial','mysql');
            $res     = $salesMo->saveWage($data);
            if($res){
                $return['massageCode'] = 'success';
                $return['massage']     = '修改成功';
            }else{
                $return['massage']     = '修改失败';
            }
        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 工资审核
     */
    public function show_to_user(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data    = $this->getRequest('data' ,'');
            $finMo   = model('plat.sales.financial','mysql');
            $res  = $finMo->show_to_user($data);
            if ($res){
                $return['massageCode'] = 'success';
                $return['massage']     = '审核成功';
            }else{
                $return['massage']     = '审核失败';
            }
        }else{
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    public function getWageInfo(){
        $wid       = $this->getRequest('wid' ,'');
        $salesMo   = model('plat.sales.financial','mysql');
        $WageInfo  = $salesMo->getWageInfo($wid);
        //dump($WageInfo);
        $this->assign('wageinfo',$WageInfo);
        $this->template('plat.financial.wageInfo');
    }
    /**
     * 导出数据
     */
    public function exportWageToExcel(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data['uid']      = $this->getRequest('uid'      ,'');
            $data['date']     = $this->getRequest('date'     ,'');
            $data['order']    = $this->getRequest('order'    ,'');
            $data['keywords'] = $this->getRequest('keywords' ,'');
            $data['page']     = $this->getRequest('page'     ,'');
            $data['pageSize'] = $this->getRequest('pageSize' ,'');
            $vipMo    = model('plat.sales.financial','mysql');
            $company  = $vipMo->getWage($data);

            $fileName = '业务员工资记录_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('业务员ID', '昵称', '姓名', '联系电话', '月份', '基本工资（元）', '补贴（元）', '修理厂使用频率提成（元）', '汽修厂拨打数量提成（元）', '汽修厂关联提成（元）', '新增关联汽修厂提成（元）', '关联经销商充值提成（元）', '合计工资（元）');
            $csvArr   = array();//数据
            //dump($company['list']);die;
            if($company['massageCode']==='success'){
                foreach ($company['list'] as $key => $item) {
                    $csvArr[] = array(
                        $item['uId'] ."\n",
                        $item['uname'] ,
                        $item['realname'] ,
                        $item['phone']."\n" ,
                        $item['time']."\t" ,
                        $item['base_wage'] ,
                        $item['subsidies'] ,
                        $item['repair_use_money'] ,
                        $item['repair_call_money'] ,
                        $item['firm_comm'] ,
                        $item['new_firm_comm'] ,
                        $item['firm_recharge_comm'] ,
                        $item['total'] ,
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

    /**
     * 个人工资记录
     */
    public function getOneSaleWage(){
        //$data    = $this->getRequest('data' ,'');
        $this->template('plat.sales.financial.wageLog');
    }

    //================== 财务流水 ====================
    /**
     *
     */
    public function getFinancialFlow(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data    = $this->getRequest('data' ,'');
            $finMo  = model('plat.sales.financial','mysql');
            $return  = $finMo->getFinancialFlow($data);

        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 导出列表
     */
    public function exportFlowToExcel(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data['type']     = $this->getRequest('type'     ,'');
            $data['payway']   = $this->getRequest('payway'   ,'');
            $data['keywords'] = $this->getRequest('keywords' ,'');
            $data['order']    = $this->getRequest('order'    ,'');
            $data['page']     = $this->getRequest('page'     ,'');
            $data['pageSize'] = $this->getRequest('pageSize' ,'');
            $finMo = model('plat.sales.financial','mysql');
            $res  = $finMo->getFinancialFlow($data);
            $fileName = '财务流水_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('企业ID', '昵称', '手机号', '企业名称', '金额（元）', '内容', '支付方式', '时间');

            $typeArr  = array(1=>'充值VIP',2=>'充值刷新点');
            $payArr   = array(1=>'微信支付',2=>'支付宝支付',3=>'人工收费');
            $csvArr   = array();//数据
            if($res['massageCode'] == 'success'){
                foreach ($res['list'] as $key => $item) {
                    $csvArr[] = array(
                        $item['EnterpriseID']."\n",
                        $item['uname']."\n",
                        $item['phone']."\n",
                        $item['companyname']."\n",
                        $item['money'],
                        $typeArr[$item['type']],
                        $payArr[$item['payway']],
                        $item['create_time']."\n",
                    );
                }
                //dump($csvArr);die;
                $csvMo = model('tools.getCsv', 'mysql');
                echo $csvMo->array2csv($csvArr, $head, $fileName);
                unset($csvArr);
                die();
            }
        }else{
            dump('没有相关权限') ;
        }
    }

    //================== 财务统计 ====================
    /**
     *
     */
    public function getFinancialStatistics(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data    = $this->getRequest('data' ,'');
            $finMo  = model('plat.sales.financial','mysql');
            $return  = $finMo->getFinancialStatistics($data);

        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    public function getAllFinancialMoney(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data    = $this->getRequest('data' ,'');
            $finMo  = model('plat.sales.financial','mysql');
            $return  = $finMo->getAllFinancialMoney($data);
            //$return['money1'] = 0 ;
        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 导出列表
     */
    public function exportStatisticsToExcel(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.financial';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data['time1']    = $this->getRequest('time1'     ,'');
            $data['time2']    = $this->getRequest('time2'     ,'');
            $data['page']     = $this->getRequest('page'      ,'');
            $data['pageSize'] = $this->getRequest('pageSize'  ,'');
            $finMo = model('plat.sales.financial','mysql');
            $res  = $finMo->getFinancialStatistics($data);
            $fileName = '财务统计_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('日期', '新增营收（元）', '新增充值VIP（元）', '新增购买点数（元）');
            $csvArr   = array();//数据
            if($res['massageCode'] == 'success'){
                foreach ($res['list'] as $key => $item) {
                    $csvArr[] = array(
                        $item['time'],
                        $item['money3'],
                        $item['money1'],
                        $item['money2'],
                    );
                }
                //dump($csvArr);die;
                $csvMo = model('tools.getCsv', 'mysql');
                echo $csvMo->array2csv($csvArr, $head, $fileName);
                unset($csvArr);
                die();
            }
        }else{
           dump('没有相关权限') ;
        }
    }


}