<?php

/**
 * 业务员模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10
 * Time: 11:16
 */
class PlatSalesFinancialModel extends Model
{

    /**
     * 获取业务员工资列表
     * @param $d
     * @return array
     */
    public function getSales($d){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        //起始条数
        $pages = ($d['page']-1) * $d['pageSize'];

        $find = 'a.id>0';

        if($suppProv){
            $find .= ' and a.area="'.$suppProv.'"';
        }

        if($d['province']){//区域
            $find  .= " and a.area='".$d['province']."'";
        }

        if($d['keywords']){//关键字
            $findKey = '"%'.$d['keywords'].'%"';
            $find   .= " and (a.`uId` like $findKey or a.`uname` like $findKey or a.`realname` like $findKey or a.`phone` like $findKey )";
        }
        if($d['order']){
            $order   = array('a.last_time'=>$d['keywords']) ;
        }else{
            //$order   = 'a.id asc' ;
            $order   = array('a.id'=>'asc') ; ;
        }
        $field = 'a.id,a.uId,a.uname,a.realname,a.phone,a.area,a.base_wage,a.subsidies';

        $count = $this->table('sales_user a')->where($find)->count();
        $lists = $this->table('sales_user a')->field($field)->where($find)
            ->order($order)
            ->limit($pages,$d['pageSize'])
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            //搜索条件
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的业务员');
        }

        return $data;


    }

    /**
     * 工资配置
     * @param $uid
     * @return mixed
     */
    public function getWageIni($uid){
        return $this->table('sales_user')->field('id,base_wage,subsidies')->where(array('id'=>$uid))->getOne() ;
    }

    /**
     * 一条工资记录
     * @param $uid
     * @return mixed
     */
    public function getOneWage($uid){
        return $this->table('sales_wage_log')->field('id,base_wage,subsidies')->where(array('id'=>$uid))->getOne() ;
    }
    public function saveWageIni($d){
        $return = array('massageCode'=>0);
        if(!isset($d['uid'])){
            $return['massage'] = '编辑失败' ;
        }else{
            $wage   = isset($d['wage'])    ? $d['wage']    : 0 ;
            $subsidy= isset($d['subsidy']) ? $d['subsidy'] : 0 ;
            $res    = $this->table('sales_user')->where(array('id'=>$d['uid']))->update(array('base_wage'=>$wage,'subsidies'=>$subsidy)) ;
            if($res){
                $suUser = G('user') ;
                $action = '配置业务员工资';
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }else{
                $return['massage'] = '编辑失败' ;
            }
        }
        return $return ;
    }

    /**
     * 工资记录
     * @param $d
     * @return array
     */
    public function getWage($d){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        //起始条数
        $pages = ($d['page']-1) * $d['pageSize'];

        $find = 'a.id>0';

        if($suppProv){
            $find .= ' and b.area="'.$suppProv.'"';
        }


        if($d['uid']){//一个业务员
            $find   .= " and (a.`sales_user_id`=".$d['uid'].")";
        }
        if($d['date']){//关键字
            $find   .= " and (DATE_FORMAT(a.`year_month`, '%Y-%m')='".$d['date']."' )";
        }
        if($d['keywords']){//关键字
            $findKey = '"%'.$d['keywords'].'%"';
            $find   .= " and (b.`uId` like $findKey or b.`uname` like $findKey or b.`realname` like $findKey )";
        }
        if($d['order']){
            $order   = array('a.last_time'=>$d['keywords']) ;
        }else{
            //$order   = 'a.id asc' ;
            $order   = array('a.`year_month`'=>'desc') ; ;
        }
        $field = 'a.id,a.`year_month`,a.base_wage,a.subsidies,a.new_firm_comm,a.firm_comm,a.repair_use_money,a.sales_user_id';
        $field.= ',a.repair_call_money,a.firm_recharge_comm,a.total,DATE_FORMAT(a.`year_month`, \'%Y-%m\') AS time,a.is_show';
        $field.= ',b.uId,b.uname,b.phone,b.realname';

        $join  = 'inner join sales_user b on a.sales_user_id=b.id' ;
        $count = $this->table('sales_wage_log a') ->jion($join)->where($find)->count();
        $lists = $this->table('sales_wage_log a')->field($field)->where($find)
            ->order($order)
            ->jion($join)
            ->limit($pages,$d['pageSize'])
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            //搜索条件
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有符合条件的工资记录');
        }
        return $data;
    }

    /**
     * 审核工资
     * @param $d
     * @return bool
     */
    public function show_to_user($d){
        $id  = isset($d['id']) ? $d['id'] : '' ;
        if($id){
            $res = $this->table('sales_wage_log')->where(array('id'=>$id))->update(array('is_show'=>1));
        }else{
            $res = false ;
        }
        if ($res){
            $suUser = G('user') ;
            $action = '审核工资';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }
    public function getWageInfo($wid){
        $field = 'a.sales_user_id,a.new_firm_num,a.new_firm_prop,a.new_firm_comm,a.firm_num,a.firm_prop,a.firm_comm,';
        $field.= 'a.firm_recharge_money,a.firm_recharge_prop,a.firm_recharge_comm,a.repair_use_money,a.repair_call_money,';
        $field.= 'a.repair_lv1_used,a.repair_lv2_used,a.repair_lv3_used,a.repair_lv1_prop,a.repair_lv2_prop,a.repair_lv3_prop,';
        $field.= 'a.repair_lv1_called,a.repair_lv2_called,a.repair_lv3_called,a.call_lv1_prop,a.call_lv2_prop,a.call_lv3_prop,';
        $field.= 'a.total,DATE_FORMAT(a.`year_month`, \'%Y-%m\') AS time,a.base_wage,a.subsidies' ;
        $info = $this->table('sales_wage_log a')->field($field)->where(array('id'=>$wid))->getOne() ;//工资基本记录

        //dump($info);
        return $info ;
    }

    /**
     * 编辑当月基本工资
     * @param $d
     * @return bool|mixed
     */
    public function saveWage($d){
        $id  = isset($d['uid']) ? $d['uid'] : '' ;
        if($id){
            $wage   = isset($d['wage'])    ? $d['wage']    : 0 ;
            $subsidy= isset($d['subsidy']) ? $d['subsidy'] : 0 ;
            //$time = date('Y-m',time()) ;
            $sql  = 'update sales_wage_log set' ;
            $sql .= ' base_wage= '.$wage.',subsidies= '.$subsidy.'' ;
            $sql .= ',total=new_firm_comm + firm_comm + repair_use_money + repair_call_money + firm_recharge_comm + '.$wage.' + '.$subsidy ;
            $sql .= ' where id='.$id ;
            $res  = $this->query($sql) ;
        }else{
            $res = false ;
        }
        if ($res){
            $suUser = G('user') ;
            $action = '修改当月基本工资';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }
    /**
     * 财务流水
     * @param $d
     * @return array
     */
    public function getFinancialFlow($d){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        //起始条数
        $pages = ($d['page']-1) * $d['pageSize'];

        $find = 'a.status=1';
        if($d['type']){
            $find .= ' and a.type='.$d['type'];
        }else{
            $find .= ' and a.type in (1,2) and payway in(1,2,3)';
        }

        if($suppProv){
            $find .= ' and b.province ="'.$suppProv.'"';
        }
        if($d['payway']){
            $find .= ' and a.payway='.$d['payway'];
        }else{
            $find .= ' and payway in(1,2,3)';
        }


        if($d['keywords']){//关键字
            $findKey = '"%'.$d['keywords'].'%"';
            $find   .= " and (b.`EnterpriseID` like $findKey or b.`uname` like $findKey or b.`phone` like $findKey )";
        }
        if($d['order']){
            $order   = array('a.last_time'=>$d['order']) ;
        }else{
            //$order   = 'a.id asc' ;
            $order   = array('a.create_time'=>'desc') ; ;
        }
        $field = 'a.type,a.payway,a.money,a.create_time';
        $field.= ',b.EnterpriseID,b.uname,b.phone,b.companyname';

        $join  = ' left join firms b on a.firms_id=b.id' ;
        //$join .= ' left join sales_user e on b.salesman_ids=e.id' ;//业务员

        $count = $this->table('pay_history a')->jion($join)->where($find)->count();
        $lists = $this->table('pay_history a')->field($field)->where($find)
            ->order($order)
            ->jion($join)
            ->limit($pages,$d['pageSize'])
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            //搜索条件
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有符合条件的流水记录');
        }
        return $data;
    }

     /**
     * 财务统计
     * @param $d
     * @return array
     */
    public function getFinancialStatistics($d){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        //起始条数
        $pages = ($d['page']-1) * $d['pageSize'];

        $find = 'a.status=1 and a.type in (1,2) and a.payway in (1,2,3) ';


        if($suppProv){
            $find .= ' and b.province ="'.$suppProv.'"';
        }
        if($d['time1']){
            $find .= ' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\')>="'.$d['time1'].'"';
        }
        if($d['time2']){
            $find .= ' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\')<="'.$d['time2'].'"';
        }

        $join  = ' left join firms b on a.firms_id=b.id' ;

        $field  = "DATE_FORMAT(a.create_time, '%Y-%m-%d') AS time,";
        $field .= "SUM( CASE WHEN a.type = 1 THEN a.money ELSE 0 END ) AS money1,";
        $field .= "SUM( CASE WHEN a.type = 2 THEN a.money ELSE 0 END ) AS money2,";
        $field .= "SUM( a.money ) AS money3 ";


//        $count  = $this->table('pay_history')->where($find)->count();
//        $count  = $this->getOne("SELECT COUNT(DISTINCT DATE_FORMAT(create_time, '%Y-%m-%d')) as num FROM `pay_history` where ".$find);
        $count  = $this->getOne("SELECT COUNT(1) AS num FROM(SELECT a.id FROM pay_history a ".$join." WHERE ". $find ." GROUP BY DATE_FORMAT(a.create_time, '%Y-%m-%d')) test");
        $count  = $count['num'] ;
        $lists  = $this->table('pay_history a')->jion($join)->field($field)->where($find)
            ->limit($pages,$d['pageSize'])
            ->group("DATE_FORMAT(a.create_time, '%Y-%m-%d')")
            ->order("a.create_time DESC")
            ->get();
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有符合条件的流水记录');
        }
        return $data;
    }

    public function getAllFinancialMoney(){
        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $find = 'a.type in (1,2) and a.payway in (1,2,3) ';

        if($suppProv){
            $find .= ' and b.province ="'.$suppProv.'"';
        }

        $join  = ' left join firms b on a.firms_id=b.id' ;

        $field  = "SUM( CASE WHEN a.type = 1 THEN a.money ELSE 0 END ) AS money1,";
        $field .= "SUM( CASE WHEN a.type = 2 THEN a.money ELSE 0 END ) AS money2,";
        $field .= "SUM( a.money ) AS money3 ";
        return $this->table('pay_history a')->jion($join)->field($field)->where($find)
            ->getOne();

    }


    /**
     * 自动计算上月工资 在首页有调用
     */
    public function automaticSettlement(){
        $day        = date('Y-m-d',time()); //当前时间
        $last_month = date('Y-m',strtotime('-1 month'));     //上月
        $last_start = date('Y-m-01',strtotime('-1 month')) ;//上月开始
        $last_end   = date('Y-m-t',strtotime('-1 month')) ;  //上月结束

        $log_path   = APPROOT . 'data/log/wage/'.date('Ym').'.txt' ;//日志记录路径
        //获取 业务员列表
        writeLog('当前时间：'.$day,$log_path);
        $sales  = $this->table('sales_user')->field('id,uname,base_wage,subsidies')->get() ;

        //writeLog($sales,$log_path);die;
        if($sales){
            //当前提成配置
            $commissionIni = $this->table('base_ini')->where('id=8')->field('`value`')->getOne() ;//业务员提成配置
            $levelIni      = $this->table('base_ini')->where('id=7')->field('`value`')->getOne() ;//汽修厂等级配置

            $de_levelIni   = (array)json_decode($levelIni['value']) ;

            $used_lv_1     = (array)$de_levelIni['lv1'] ;
            $used_lv_2     = (array)$de_levelIni['lv2'] ;
            $used_lv_3     = (array)$de_levelIni['lv3'] ;

            //关联提成配置
            $de_commissionIni       = (array)json_decode($commissionIni['value']);
            $commission             = (array)$de_commissionIni['commission'] ;
            $frequency              = (array)$de_commissionIni['frequency'] ;
            $repair_comm_prop       = $commission['relation'] ;     //汽修厂关联提成
            $new_repair_comm_prop   = $commission['new_relation'] ; //新增关联汽修厂提成
            $recharge_comm_prop     = $commission['recharge'] ;     //关联厂商充值提成

            //修理厂使用等级提成配置
            $frequency_lv1          = (array)$frequency[0] ;
            $frequency_lv1_prop     = $frequency_lv1['money'] ;
            $frequency_lv2          = (array)$frequency[1] ;
            $frequency_lv2_prop     = $frequency_lv2['money'] ;
            $frequency_lv3          = (array)$frequency[2] ;
            $frequency_lv3_prop     = $frequency_lv3['money'] ;



            //检测每个业务员是否生成工资
            writeLog('检测是否已生成过上月工资...',$log_path);
            foreach ($sales as $sk => $sale){

                $is_wage =  $this->table('sales_wage_log')->field('id')
                    ->where(' sales_user_id='.$sale['id'].' and  DATE_FORMAT(`year_month`,"%Y-%m")="'.$last_month.'"')->getOne() ;

                writeLog('检测结果：' .$sale['uname']. ($is_wage ? ':已生成' : ':未生成'),$log_path) ;
                if(!$is_wage){
                    writeLog('开始生成 "' .$sale['uname'].'" 的工资...',$log_path);
                    // $sale => 每个业务员
                    //新增关联提成：该月新增的关联厂商数量乘以单价提成
                    //上月 新增关联数量
                    $new_repair_num = $this->table('firms_sales_user a')
                        //->jion('inner join firms b on a.firms_id=b.id and b.classification=4')//b.classification=4 :修理厂
                        ->jion('left join firms b on a.firms_id=b.id and b.type=2')//b.type=2 :汽修厂
                        ->where('a.sales_user_di='.$sale['id'].' and DATE_FORMAT(a.create_time, \'%Y-%m\')="'.$last_month.'"')
                        ->count() ;
                    //writeLog('业务员'.$sale['id'].'的新增厂商',$log_path);
                    //writeLog($this->lastSql(),$log_path);
                    $new_repair_num  = $new_repair_num ? $new_repair_num : 0 ;
                    //新增提成
                    $new_repair_comm = $new_repair_num * $new_repair_comm_prop ;

                    //关联厂商充值提成，是按照该月经销商和汽修厂充值金额乘以固定系数算出的，
                    //充值包括了买VIP、充值点数和后台人工增加的充值金额（人工开通VIP或增加刷新点，需增加财务数据）
                    //关联厂商充值金额

                    //获取关联的有效厂商(每月1号至最后一天 以5月1日至5月31日为例)
                    $user_firm = $this->table('firms_sales_user a')
                        ->field('a.*,b.type')
                        ->jion(' left join firms b on a.firms_id=b.id')
                        ->where('a.sales_user_di='.$sale['id'].' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\') <="'.$last_end.'" and DATE_FORMAT(a.end_time, \'%Y-%m-%d\') >"'.$last_start.'"')
                        ->get() ;//只查询未过解绑日期的厂商 即在5月1日 还未解绑的厂商
                    //writeLog('业务员'.$sale['id'].'的有效厂商',$log_path);
                    //writeLog($this->lastSql(),$log_path);
                    //获取在有效时间内厂商充值金额
                    //$user_firm => 单个业务员绑定的所有厂商
                    $firm_recharge_money = 0 ;//初始化充值金额
                    $call_total_num      = 0 ;//初始化拨打次数
                    //使用数量
                    $repair_lv1_used     = 0 ;
                    $repair_lv2_used     = 0 ;
                    $repair_lv3_used     = 0 ;
                    //dump($user_firm);
                    if($user_firm){
                        foreach ($user_firm as $firm_v){
                            //$firm_v 绑定的每个厂商
                            if($firm_v['create_time'] > $last_start){
                                $recharge_start = date('Y-m-d H:i:s',strtotime($firm_v['create_time'])) ; //如果是在5月1日之后绑定的业务员 则计算充值提成开始时间为绑定时间
                            }else{
                                $recharge_start = $last_start ;//如果是在5月1日或之前绑定的业务员 则计算充值提成开始时间为5月1日起
                            }
                            if($firm_v['end_time'] > $last_end){
                                $recharge_end = $last_end ; //如果解绑日期在5月31日后 则计算充值提成结束时间为5月31日
                            }else{
                                $recharge_end = date('Y-m-d H:i:s',strtotime($firm_v['end_time'])) ;//如果解绑日期或在5月31日前 $firm_v['end_time']
                            }
                            //有效时间内厂商 充值金额
                            $firm_recharge = $this->table('pay_history a')
                                ->field('SUM(a.money) as total_money')
                                ->where('a.firms_id='.$firm_v['firms_id'].' and a.status=1 and a.type in (1,2,3) and a.payway in (1,2,3) and DATE_FORMAT(a.create_time, \'%Y-%m-%d\') BETWEEN "'.$recharge_start.'" and "'.$recharge_end.'"')->getOne() ;
                            //writeLog('业务员【'.$sale['id'].'】的厂商【'.$firm_v['firms_id'].'】充值金额',$log_path);
                            //writeLog($this->lastSql(),$log_path);
                            //记录每个供应商 上月充值金额 ....
                            $recharge_arr = array(
                                'uid'=>$sale['id'],
                                'fid'=>$firm_v['firms_id'],
                                'type'=>1,
                                'firm_type'=>$firm_v['type'],
                                'value'=>$firm_recharge['total_money'] ? $firm_recharge['total_money'] : 0,
                                'date'=>$last_start,
                                'create_time'=>date('Y-m-d H:i:s'),
                            );
                            $this->table('sales_wage_info')->insert($recharge_arr);


                            $firm_recharge_money +=  $firm_recharge['total_money'] ;

                            if($firm_v['type'] == 2){
                                //获取每个汽修厂的拨打数
                                //针对汽修厂关联提成，是按照该月来电数（移动端拨打电话+QQ）乘以固定系数算出的
                                $countSql  = ' SELECT SUM(num) AS num FROM(' ;
                                $countSql .= ' SELECT COUNT(1) as num FROM firms_call_log a WHERE to_firms_id='.$firm_v['firms_id'] .' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\') BETWEEN "'.$recharge_start.'" and "'.$recharge_end.'"' ;
                                $countSql .= ' UNION ALL SELECT COUNT(1) as num FROM sales_call_log c WHERE firms_id='.$firm_v['firms_id'] .' and DATE_FORMAT(c.create_time, \'%Y-%m-%d\') BETWEEN "'.$recharge_start.'" and "'.$recharge_end.'"' ;
                                $countSql .= ' ) t ' ;
                                $call_num  = $this->getOne($countSql) ;
                                //writeLog('业务员【'.$sale['id'].'】的厂商【'.$firm_v['firms_id'].'】拨打次数',$log_path);
                                //writeLog($this->lastSql(),$log_path);
                                //记录每个汽修厂拨打总数...
                                $call_arr = array(
                                    'uid'=>$sale['id'],
                                    'fid'=>$firm_v['firms_id'],
                                    'type'=>2,
                                    'firm_type'=>$firm_v['type'],
                                    'value'=>$call_num['num'] ? $call_num['num'] : 0,
                                    'date'=>$last_start,
                                    'create_time'=>date('Y-m-d H:i:s'),
                                );
                                $this->table('sales_wage_info')->insert($call_arr);
                                $call_total_num += $call_num['num']   ; //关联汽修厂总来电数

                                //针对汽修厂使用频率的提成，是统计关联后30天内，
                                //该月达到对应使用等级的关联汽修厂的数量提成，等级同验证厂商中的汽修厂等级定义，
                                //如3月1日，关联修理厂满30天的，达到等级1的汽修厂有200个，达到等级2的有60个，达到等级3的有40个，
                                //乘以对应的提成单价，已提成的修理厂就不再计入下一个月的提成；
                                //到1号时，没有关联到30天的，这部分汽修厂的提成计入到下一个月的提成

                                // 使用等级计算
                                // 一.判断绑定时间
                                // 1.30天内(保留至下月计算)
                                // 2.30天至60天内(计算期间所有使用数量[部分为上月不满30天保留下来的])
                                // 3.60天至90天(按正常计算：5月1日 - 5月31日)
                                // 4.超过90天(应计算解绑时间：开始时间为5月1日 ，结束时间为解绑时间)
                                $bind_day = ceil((strtotime($last_end) - strtotime($firm_v['create_time']))/84600) ;
                                $use_start= false;
                                $use_end  = false;
                                $repair_lv1_used = 0 ;
                                $repair_lv2_used = 0 ;
                                $repair_lv3_used = 0 ;
                                if( $bind_day > 90 ){
                                    $use_start = $last_start ;
                                    $use_end   = $firm_v['end_time'] ;
                                }elseif($bind_day >= 60 && $bind_day <= 90){
                                    $use_start = $last_start ;
                                    $use_end   = $last_end ;
                                }elseif($bind_day >= 30 && $bind_day < 60){
                                    $use_start = date('Y-m-d H:i:s',strtotime($firm_v['create_time'])) ;
                                    $use_end   = $last_end ;
                                }else{
                                    //小于30天下月统计
                                }
                                if($use_start && $use_end){
                                    //每个汽修厂使用量
                                    $used_num   = $this->table('firms_visit_log')
                                        ->where('to_firms_id='.$firm_v['firms_id'].' and DATE_FORMAT(create_time, \'%Y-%m-%d\') BETWEEN "'.$use_start.'" and "'.$use_end.'"')
                                        ->count() ;
                                    //writeLog('业务员【'.$sale['id'].'】的厂商【'.$firm_v['firms_id'].'】使用次数',$log_path);
                                    //writeLog($this->lastSql(),$log_path);
                                    //根据 配置将不同使用频率的计入不同提成等级
                                    if($used_num >= $used_lv_3['min']){
                                        $lv = 3 ;
                                        $repair_lv3_used += $used_num  ;
                                    }elseif ($used_num >= $used_lv_2['min'] && $used_num <= $used_lv_2['max']){
                                        $lv = 2 ;
                                        $repair_lv2_used += $used_num  ;
                                    }elseif ($used_num >= $used_lv_1['min'] && $used_num <= $used_lv_1['max']){
                                        $lv = 1 ;
                                        $repair_lv1_used += $used_num ;
                                    }else{
                                        $lv = 1 ;
                                    }

                                    $used_arr = array(
                                        'uid'=>$sale['id'],
                                        'fid'=>$firm_v['firms_id'],
                                        'type'=>3,
                                        'firm_type'=>$firm_v['type'],
                                        'lv'=>$lv,
                                        'value'=>$used_num,
                                        'date'=>$last_start,
                                        'create_time'=>date('Y-m-d H:i:s'),
                                    );
                                    $this->table('sales_wage_info')->insert($used_arr);
                                }
                            }
                        }
                    }

                    //关联厂商充值提成
                    $firm_recharge_comm = $firm_recharge_money * $recharge_comm_prop ;
                    //关联汽修厂提成(实际即为拨打数量提成)
                    $repair_comm        = $call_total_num * $repair_comm_prop ;
                    //关联汽修厂使用频率提成(根据访问记录数分级)
                    $repair_use_money = $repair_lv1_used * $frequency_lv1_prop + $repair_lv2_used * $frequency_lv2_prop + $repair_lv3_used * $frequency_lv3_prop ;

                    $total_money = $sale['base_wage'] + $sale['subsidies'] + $new_repair_comm + $repair_comm + $firm_recharge_comm +$repair_use_money ;

                    $wageArr = array(
                        'sales_user_id'=>$sale['id'], '`year_month`'=>$last_start, 'base_wage'=>$sale['base_wage'], 'subsidies'=>$sale['subsidies'],
                        'new_firm_num'=>$new_repair_num, 'new_firm_prop'=>$new_repair_comm_prop, 'new_firm_comm'=>$new_repair_comm,
                        'firm_num'=>$call_total_num, 'firm_prop'=>$repair_comm_prop, 'firm_comm'=>$repair_comm,
                        'firm_recharge_money'=>$firm_recharge_money, 'firm_recharge_prop'=>$recharge_comm_prop, 'firm_recharge_comm'=>$firm_recharge_comm,
                        'repair_lv1_used'=>$repair_lv1_used, 'repair_lv1_prop'=>$frequency_lv1_prop,//一级汽修厂使用数量、提成率
                        'repair_lv2_used'=>$repair_lv2_used, 'repair_lv2_prop'=>$frequency_lv2_prop,//
                        'repair_lv3_used'=>$repair_lv3_used, 'repair_lv3_prop'=>$frequency_lv3_prop,//
                        'repair_use_money'=>$repair_use_money,
                        'total'=>$total_money,
                    );
                    //dump($wageArr);
                    $old = $this->table('sales_wage_log')->field('id')->where('sales_user_id='.$sale['id'] .' and DATE_FORMAT(`year_month`, \'%Y-%m\')="'.$last_month.'"')->getOne();
                    if($old){
                        //有记录则不再变更
                        //$this->table('sales_wage_log')->where(array('id'=>$old['id']))->update($wageArr);
                    }else{
                        $wageArr['create_time'] = date('Y-m-d H:i:s',time()) ;
                        $this->table('sales_wage_log')->insert($wageArr);
                    }
                    writeLog($sale['uname'].'工资已生成',$log_path);
                }
            }
            writeLog('运行结束========================================================='."\r\n",$log_path);
        }

    }

    protected function test(){
        for ($i=0;$i<5000;$i++){

            $create_time = '2017-'.rand(5,6).'-'.rand(1,30).' '.rand(10,23).':'.rand(10,59).':'.rand(10,59);
            $f = array(9,10,11,12,13,14,15,17,18,20,21,25,29,30);
            $arr1 = array(
                'firms_id'=>rand(9,34),
                'to_firms_id'=>$f[rand(0,13)],
                'create_time'=>$create_time,
                'call_type'=>rand(1,2),
                'is_show'=>rand(1,2),
                'visit_type'=>2,
            );
            //生成拨打记录
            $this->table('firms_call_log')->insert($arr1);

            $arr2 = array(
                'firms_id'=>rand(9,34),
                'to_firms_id'=>$f[rand(0,13)],
                'create_time'=>$create_time,
                'visit_type'=>rand(1,2),
                'is_show'=>rand(1,2),
            );
            //厂商访问记录
            $this->table('firms_visit_log')->insert($arr2);

            $arr3 = array(
                'sales_user_id'=>rand(9,34),
                'firms_id'=>$f[rand(0,13)],
                'create_time'=>$create_time,
                'visit_type'=>rand(3,4),
                'is_show'=>rand(1,2),
            );
            //业务员拨打记录
            $this->table('sales_call_log')->insert($arr3);

            $create_time = '2017-'.rand(5,6).'-'.rand(1,30).' '.rand(10,23).':'.rand(10,59).':'.rand(10,59);
            $type = rand(1,2) ;
            $info = array(1=>'充值VIP',2=>'充值刷新点');
            $point_arr= array(1=>array(1,2,3,6,12),2=>array(50,100,200,300,500));
            $point= rand(0,4) ;
            $arr4 = array(
                'type'=>$type,
                'status'=>1,
                'info'  =>$info[$type],
                'payway'=>rand(1,3),
                'refresh_point'=>$type == 1 ? 0 : $point_arr[$type][$point],
                'vip_month'=>$type == 2 ? 0 : $point_arr[$type][$point],
                'firms_id'=>rand(9,34),
                'money'=>$point_arr[2][$point],
                'admin_id'=>rand(1,5),
                'create_time'=>$create_time,
            );
            //收费记录表
            $this->table('pay_history')->insert($arr4);
        }
    }
}