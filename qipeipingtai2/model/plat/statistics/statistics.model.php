<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/16
 * Time: 10:54
 */
class PlatStatisticsStatisticsModel extends Model
{

    /**
     * 获取数据统一入口
     * @param $d
     * @return array
     */
    public function getLists($d){
        if($d['type'] == 1){
            $res = $this->getFirm($d);
        }elseif ($d['type'] == 2){
            $res = $this->getPro($d);
        }elseif ($d['type'] == 3){
            $res = $this->getBrisk($d);
        }elseif ($d['type'] == 4){
            $res = $this->getVisit($d);
        }elseif ($d['type'] == 5){
            $res = $this->getCircle($d);
        }else{
            $res = array('massageCode'=>0,'massage'=>'非法操作');
        }
        return $res ;
    }

    /**
     * 厂商统计
     * @param $d
     * @return array
     */
    private function getFirm($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = 'id<>0' ;

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        if($d['time1']){
            $where .= ' and DATE_FORMAT(create_time, \'%Y-%m-%d\')>="'.$d['time1'].'"';
        }
        if($d['time2']){
            $where .= ' and DATE_FORMAT(create_time, \'%Y-%m-%d\')<="'.$d['time2'].'"';
        }

        if($suppProv){
            $where .= ' and province ="'.$suppProv.'"';
        }

        $field  = "DATE_FORMAT(create_time, '%Y-%m-%d') AS time,";
        $field .= "SUM( CASE WHEN type = 1 THEN 1 ELSE 0 END ) AS num1,";
        $field .= "SUM( CASE WHEN type = 2 THEN 1 ELSE 0 END ) AS num2 ";

        $count  = $this->getOne("SELECT COUNT(1) AS num FROM(SELECT id FROM firms WHERE ". $where ." GROUP BY DATE_FORMAT(create_time, '%Y-%m-%d')) test");
        $count  = $count['num'] ;
        $lists  = $this->table('firms')
            ->field($field)
            ->where($where)
            ->limit($pages,$d['pageSize'])
            ->group("DATE_FORMAT(create_time, '%Y-%m-%d')")
            ->order('create_time DESC')
            ->get();
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有记录');
        }
        return $data;
    }

    /**
     * 产品统计
     * @param $d
     * @return array
     */
    private function getPro($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = 'a.id!=0' ;

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        if($d['time1']){
            $where .= ' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\')>="'.$d['time1'].'"';
        }
        if($d['time2']){
            $where .= ' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\')<="'.$d['time2'].'"';
        }

        if($suppProv){
            $where .= ' and b.province ="'.$suppProv.'"';
        }

        $field  = "DATE_FORMAT(a.create_time, '%Y-%m-%d') AS time,";
        $field .= "SUM( CASE WHEN a.pro_type = '新品促销' THEN 1 ELSE 0 END ) AS num1,";
        $field .= "SUM( CASE WHEN a.pro_type = '库存清仓' THEN 1 ELSE 0 END ) AS num2,";
        $field .= "SUM( CASE WHEN (a.pro_price = 0 or a.pro_price = '0.00') THEN 1 ELSE 0 END ) AS num3 ";

        $join   = ' LEFT JOIN firms b on a.firms_id=b.id' ;

        $count  = $this->getOne("SELECT COUNT(1) AS num FROM(SELECT a.id FROM product_list a ".$join." WHERE ". $where ." GROUP BY DATE_FORMAT(a.create_time, '%Y-%m-%d')) test");
        $count  = $count['num'] ;
        $lists  = $this->table('product_list a')
            ->jion($join)
            ->field($field)
            ->where($where)
            ->limit($pages,$d['pageSize'])
            ->group("DATE_FORMAT(create_time, '%Y-%m-%d')")
            ->order('a.create_time DESC')
            ->get();
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有记录');
        }
        return $data;
    }

    /**
     * 活跃度统计
     * @param $d
     * @return array
     */
    private function getBrisk($d){

        //writeLog(date('Y-m-d',strtotime('2017-07-17 16:49:39'))) ;
        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = 'a.id!=0' ;

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        if($d['time1']){
            $where .= ' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\')>="'.$d['time1'].'"';
        }
        if($d['time2']){
            $where .= ' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\')<="'.$d['time2'].'"';
        }
        if($suppProv){
            $where .= ' and b.province ="'.$suppProv.'"';
        }

        $join   = ' LEFT JOIN firms b on a.firm_id=b.id' ;

        $countSql = 'select count(1) as count from firms_login_log a '.$join.' WHERE '.$where .' GROUP BY DATE_FORMAT(a.create_time,\'%Y-%m-%d\')' ;
        $count  = $this->get($countSql);
        $count  = count($count) ;
        $field  = "t.date as time,";
        $field .= "SUM(CASE WHEN t.count<=2 THEN 1 ELSE 0 END) as num1,";
        $field .= "SUM(CASE WHEN (t.count>2 and t.count<=5) THEN 1 ELSE 0 END) as num2,";
        $field .= "SUM(CASE WHEN (t.count>5 and t.count<=10) THEN 1 ELSE 0 END) as num3,";
        $field .= "SUM(CASE WHEN t.count>10 THEN 1 ELSE 0 END) as num4";
        $sql    = ' SELECT '.$field .' FROM ( ' ;
        $sql   .= ' SELECT a.firm_id,COUNT(1) as count, DATE_FORMAT(a.create_time,\'%Y-%m-%d\') AS date' ;
        $sql   .= ' FROM firms_login_log a '.$join .' WHERE '.$where;
        $sql   .= ' GROUP BY a.firm_id, DATE_FORMAT(a.create_time,\'%Y-%m-%d\') ORDER BY a.create_time DESC' ;
        $sql   .= ') t GROUP BY t.date ORDER BY t.date DESC LIMIT '.$pages.','.$d['pageSize'] ;
        $lists  = $this->limit($pages,$d['pageSize'])->get($sql);

        //writeLog($this->lastSql());
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有记录');
        }
        return $data;
    }

    /**
     * 访问及拨打统计
     * @param $d
     * @return array
     */
    private function getVisit($d){
        $pages  = ($d['page']-1)* $d['pageSize'];

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $find   = '1=1' ;
        $find1  = ' and a.firms_id>0' ;
        $find2  = 'd.firms_id>0' ;
        if($d['time1']){
            $find .= ' and DATE_FORMAT(t.time, \'%Y-%m-%d\')>="'.$d['time1'].'"';
        }
        if($d['time2']){
            $find .= ' and DATE_FORMAT(t.time, \'%Y-%m-%d\')<="'.$d['time2'].'"';
        }

        if($suppProv){
            $find1.= ' and b.province ="'.$suppProv.'"';
            $find2.= ' and e.province ="'.$suppProv.'"';
        }


        $countSql  = ' SELECT COUNT(1) AS num FROM('  ;
        $countSql .= ' SELECT time FROM('  ;
        $countSql .= ' SELECT DATE_FORMAT(a.create_time, \'%Y-%m-%d\') as time' ;
        $countSql .= ' FROM firms_visit_log a LEFT JOIN firms b ON a.firms_id=b.id inner JOIN firms c ON a.to_firms_id=c.id WHERE c.type=1'.$find1;
        $countSql .= ' UNION ALL' ;
        $countSql .= ' SELECT DATE_FORMAT(d.create_time, \'%Y-%m-%d\') as time' ;
        $countSql .= ' FROM firms_call_log d LEFT JOIN firms e ON d.firms_id=e.id WHERE ' .$find2;
        $countSql .= ' ) t where '.$find.' GROUP BY time ) s' ;


        $count    = $this->getOne($countSql) ;
        //writeLog($this->lastSql());
        $count    = $count['num'] ;

        $limit    = $pages.','.$d['pageSize'] ;

        $dataSql  = ' SELECT time,SUM(num1) as num1,SUM(num2) as num2,SUM(num3) as num3 FROM('  ;
        $dataSql .= ' SELECT DATE_FORMAT(a.create_time, \'%Y-%m-%d\') as time' ;
        $dataSql .= ' ,CASE WHEN a.visit_type=1 THEN 1 ELSE 0 END AS num1' ;
        $dataSql .= ' ,CASE WHEN a.visit_type=2 THEN 1 ELSE 0 END AS num2' ;
        $dataSql .= ' ,0 as num3' ;
        $dataSql .= ' FROM firms_visit_log a LEFT JOIN firms b ON a.firms_id=b.id inner JOIN firms c ON a.to_firms_id=c.id WHERE c.type=1'.$find1;
        $dataSql .= ' UNION ALL' ;
        $dataSql .= ' SELECT DATE_FORMAT(d.create_time, \'%Y-%m-%d\') as time' ;
        $dataSql .= ' ,0 as num1, 0 as num2, 1 as num3' ;
        $dataSql .= ' FROM firms_call_log d LEFT JOIN firms e ON d.firms_id=e.id WHERE ' .$find2;
        $dataSql .= ' ) t where '.$find.' GROUP BY time order by time DESC LIMIT '.$limit ;


        $lists  = $this->get($dataSql) ;
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有记录');
        }
        return $data;
    }

    /**
     * 圈子统计
     * @param $d
     * @return array
     */
    private function getCircle($d){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = '1=1' ;

        if($d['time1']){
            $where .= ' and DATE_FORMAT(create_time, \'%Y-%m-%d\')>="'.$d['time1'].'"';
        }
        if($d['time2']){
            $where .= ' and DATE_FORMAT(create_time, \'%Y-%m-%d\')<="'.$d['time2'].'"';
        }

        if($suppProv){
            $where .= ' and ( CASE WHEN a.type=1 THEN b.province ="'.$suppProv.'"  WHEN a.type=2 THEN c.area ="'.$suppProv.'" END )';
        }

        $join   = ' LEFT JOIN firms b on a.fu_id=b.id' ;
        $join  .= ' LEFT JOIN sales_user c on a.fu_id=c.id' ;

        $field  = "DATE_FORMAT(a.create_time, '%Y-%m-%d') AS time,";
        $field .= "SUM( CASE WHEN a.level = 1 THEN 1 ELSE 0 END ) AS num1,";
        $field .= "SUM( CASE WHEN a.level>1 THEN 1 ELSE 0 END ) AS num2 ";

        $count  = $this->getOne("SELECT COUNT(1) AS num FROM(SELECT a.id FROM circle a ".$join." WHERE ". $where ." GROUP BY DATE_FORMAT(a.create_time, '%Y-%m-%d')) test");
        $count  = $count['num'] ;
        $lists  = $this->table('circle a')
            ->jion($join)
            ->field($field)
            ->where($where)
            ->limit($pages,$d['pageSize'])
            ->group("DATE_FORMAT(a.create_time, '%Y-%m-%d')")
            ->order('a.create_time DESC')
            ->get();
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有记录');
        }
        return $data;
    }

    public function getTotalData($d){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        if($d['type'] == 1){
            $sql    = 'SELECT ';
            $sql   .= 'SUM(CASE WHEN type=1 THEN (1) ELSE 0 END) AS total1,' ;
            $sql   .= 'SUM(CASE WHEN type=2 THEN (1) ELSE 0 END) AS total2,' ;
            $sql   .= 'SUM(CASE WHEN classification=1 THEN (1) ELSE 0 END) AS num1,' ;
            $sql   .= 'SUM(CASE WHEN classification=2 THEN (1) ELSE 0 END) AS num2,' ;
            $sql   .= 'SUM(CASE WHEN classification=3 THEN (1) ELSE 0 END) AS num3,' ;
            $sql   .= 'SUM(CASE WHEN classification=4 THEN (1) ELSE 0 END) AS num4,' ;
            $sql   .= 'SUM(CASE WHEN classification=5 THEN (1) ELSE 0 END) AS num5,' ;
            $sql   .= 'SUM(CASE WHEN classification=6 THEN (1) ELSE 0 END) AS num6,' ;
            $sql   .= 'SUM(CASE WHEN (type=1 and is_check=1) THEN (1) ELSE 0 END) AS num7,' ;
            $sql   .= 'SUM(CASE WHEN (type=1 and is_vip=1) THEN (1) ELSE 0 END) AS num8,' ;
            $sql   .= 'SUM(CASE WHEN (type=2 and is_check=1) THEN (1) ELSE 0 END) AS num9' ;

            $sql   .= ' FROM firms ' ;
            if($suppProv){
                $sql .= ' where province ="'.$suppProv.'"';
            }
            $count = $this->getOne($sql);

            $sum1  = $count['total1'];
            $sum2  = $count['num7']  ;
            $sum3  = $count['num1']  ;
            $sum4  = $count['num2']  ;
            $sum5  = $count['num3']  ;
            $sum6  = $count['num8']  ;
            $sum7  = $count['total2'];
            $sum8  = $count['num9']  ;
            $sum9  = $count['num4']  ;
            $sum10 = $count['num5']  ;
            $sum11 = $count['num6']  ;

            $res = array(
                'num1'=>$sum1,'num2'=>$sum2,'num3'=>$sum3,'num4'=>$sum4,'num5'=>$sum5,'num6'=>$sum6,
                'num7'=>$sum7,'num8'=>$sum8,'num9'=>$sum9,'num10'=>$sum10,'num11'=>$sum11
                ) ;
        }elseif ($d['type'] == 2){

            $sql    = 'SELECT SUM(1) AS num1,';
            $sql   .= 'SUM(CASE WHEN a.pro_type="新品促销" THEN (1) ELSE 0 END) AS num2,' ;
            $sql   .= 'SUM(CASE WHEN a.pro_type="库存清仓" THEN (1) ELSE 0 END) AS num3' ;
            $sql   .= ' FROM product_list a left join firms b on a.firms_id=b.id' ;
            if($suppProv){
                $sql .= ' where b.province ="'.$suppProv.'"';
            }
            $count = $this->getOne($sql);
            $sum1  = $count['num1']  ;
            $sum2  = $count['num2']  ;
            $sum3  = $count['num3']  ;

            $where = 'pro_price=0 or pro_price="0.00"' ;
            $sum4  = $this->table('product_list')->where($where)->count() ;
            $res = array(
                'num1'=>$sum1,'num2'=>$sum2,'num3'=>$sum3,'num4'=>$sum4,'num5'=>0,'num6'=>0,
                'num7'=>0,'num8'=>0,'num9'=>0,'num10'=>0,'num11'=>0
            ) ;

        }elseif ($d['type'] == 3){
            $res = array(
                'num1'=>0,'num2'=>0,'num3'=>0,'num4'=>0,'num5'=>0,'num6'=>0,
                'num7'=>0,'num8'=>0,'num9'=>0,'num10'=>0,'num11'=>0
            ) ;
        }elseif ($d['type'] == 4){

            $join  = ' left join firms b on a.firms_id=b.id' ;
            $join .= ' inner join firms c on a.to_firms_id=c.id' ;
            $where = 'a.visit_type=2 and c.type=1 and a.firms_id>0' ;//1PC web端  2移动端
            if($suppProv){
                $where .= ' and b.province ="'.$suppProv.'"';
            }
            $sum1  = $this->table('firms_visit_log a')->jion($join)->where($where)->count() ;


            $where = 'a.visit_type=1 and c.type=1' ;//1PC web端  2移动端
            if($suppProv){
                $where .= ' and b.province ="'.$suppProv.'"';
            }
            $sum2  = $this->table('firms_visit_log a')->jion($join)->where($where)->count() ;


            $join  = ' left join firms b on a.firms_id=b.id' ;
            $join .= ' left join firms c on a.to_firms_id=c.id' ;
            $where = ' a.firms_id>0' ;
            if($suppProv){
                $where .= ' b.province ="'.$suppProv.'"';
            }

            $sum3  = $this->table('firms_call_log a')->jion($join)->where($where)->count() ;

            //dump($this->lastSql());
            $res = array(
                'num1'=>$sum1,'num2'=>$sum2,'num3'=>$sum3,'num4'=>0,'num5'=>0,'num6'=>0,
                'num7'=>0,'num8'=>0,'num9'=>0,'num10'=>0,'num11'=>0
            ) ;

        }elseif ($d['type'] == 5){
            $supper   = G('user') ;
            $suppProv = @$supper['province'] ;

            $join   = ' LEFT JOIN firms b on a.fu_id=b.id' ;
            $join  .= ' LEFT JOIN sales_user c on a.fu_id=c.id' ;

            $where = 'a.level=1' ;

            if($suppProv){
                $where .= ' and ( CASE WHEN a.type=1 THEN b.province ="'.$suppProv.'"  WHEN a.type=2 THEN c.area ="'.$suppProv.'" END )';
            }
            $sum1  = $this->table('circle a')->jion($join)->where($where)->count() ;
            $where = 'a.level>1' ;
            if($suppProv){
                $where .= ' and ( CASE WHEN a.type=1 THEN b.province ="'.$suppProv.'"  WHEN a.type=2 THEN c.area ="'.$suppProv.'" END )';
            }
            $sum2  = $this->table('circle a')->jion($join)->where($where)->count() ;

            $res = array(
                'num1'=>$sum1,'num2'=>$sum2,'num3'=>0,'num4'=>0,'num5'=>0,'num6'=>0,
                'num7'=>0,'num8'=>0,'num9'=>0,'num10'=>0,'num11'=>0
            ) ;
        }else{
            $res = array('massageCode'=>0,'massage'=>'非法操作');
        }

        return $res ;
    }

    /**
     * 获取访问详情统一入口
     * @param $d
     * @return array
     */
    public function getStatisticsInfo($d){

        if($d['type'] == 1){
            $res = $this->getVisitInfo($d);
        }elseif ($d['type'] == 2){
            $res = $this->getVisitInfo($d);
        }elseif ($d['type'] == 3){
            $res = $this->getCallInfo($d);
        }else{
            $res = array('massageCode'=>0,'massage'=>'非法操作');
        }
        return $res ;
    }

    /**
     * 访问统计
     * @param $d
     * @return array
     */
    private function getVisitInfo($d){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = 'a.firms_id>0 and c.type =1 and a.visit_type='.$d['type'] ;

        if($suppProv){
            $where .= ' and b.province ="'.$suppProv.'"';
        }

        if($d['fType']){//类型
            $where  .= ' and b.`type` ='.$d['fType'];
        }
        if($d['classes']){//类型
            $where  .= ' and b.`classification` ='.$d['classes'];
        }
        if($d['province'] && $d['province'] != '全部'){//省
            $province = str_replace(' ','',$d['province']);
            $where  .= ' and b.province like "%'.$province.'%"';
        }
        if($d['city'] && $d['city'] != '全部'){//市
            $city   = str_replace(' ','',$d['city']) ;
            $where  .= ' and b.city like "%'.$city.'%"';
        }
        if($d['county'] && $d['county'] != '全部'){//区
            $county = str_replace(' ','',$d['county']) ;
            $where  .= ' and b.district like "%'.$county.'%"';
        }

        if($d['time1']){
            $where .= ' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\')>="'.$d['time1'].'"';
        }
        if($d['time2']){
            $where .= ' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\')<="'.$d['time2'].'"';
        }
        if($d['keywords']){//关键字
            $findKey = '"%'.$d['keywords'].'%"';
            $where .= " and (b.EnterpriseID like $findKey or b.companyname like $findKey or b.uname like $findKey or b.phone like $findKey)";
        }
        $field  = "a.create_time,";
        $field .= "b.EnterpriseID as EnterpriseID1,b.uname ,b.phone,b.companyname as companyname1,";
        $field .= "b.type,b.classification ,b.province,b.city, b.district,";
        $field .= "c.EnterpriseID as EnterpriseID2,c.companyname as companyname2";
        $field .= ",c.province as province2,c.city as city2, c.district as district2";

        $join   = ' left join firms b on a.firms_id=b.id' ;
        $join  .= ' left join firms c on a.to_firms_id=c.id' ;
        $count  =  $this->table('firms_visit_log a')->jion($join)->where($where)->count() ;
        $lists  = $this->table('firms_visit_log a')
            ->field($field)
            ->jion($join)
            ->where($where)
            ->order('a.create_time DESC')
            ->limit($pages,$d['pageSize'])
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            $classes = array('0'=>'',1=>'轿车商家',2=>'货车商家',3=>'用品商家',4=>'修理厂',5=>'快修保养',6=>'美容店') ;
            foreach ($lists as $k => $item){
                if(!$item['province']){
                    $area1 = '';
                }elseif($item['province']=='全部'){
                    $area1 = '全部';
                }elseif($item['city'] == '' || $item['city'] == '全部'){
                    $area1 = $item['province'];
                }elseif($item['district'] == '' || $item['district'] == '全部'){
                    $area1 = $item['province'].'/'.$item['city'];
                }else{
                    $area1 = $item['province'].'/'.$item['city'].'/'.$item['district'];
                }

                if(!$item['province2']){
                    $area2 = '';
                }elseif($item['province2']=='全部'){
                    $area2 = '全部';
                }elseif($item['city2'] == '' || $item['city2'] == '全部'){
                    $area2 = $item['province2'];
                }elseif($item['district2'] == '' || $item['district2'] == '全部'){
                    $area2 = $item['province2'].'/'.$item['city2'];
                }else{
                    $area2 = $item['province2'].'/'.$item['city2'].'/'.$item['district2'];
                }
                $lists[$k]['area1']  = $area1 ;
                $lists[$k]['area2']  = $area2 ;
                $lists[$k]['type1'] = $item['type'] == 1 ? '经销商' :  ($item['type'] == 2 ? '汽修厂' : '' );
                $lists[$k]['class'] = $classes[$item['classification']] ;
                $lists[$k]['call']  = '' ;
            }
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有记录');
        }
        return $data;
    }

    private function getCallInfo($d){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = 'a.firms_id>0' ;

        if($suppProv){
            $where .= ' and b.province ="'.$suppProv.'"';
        }

        if($d['fType']){//类型
            $where  .= ' and b.type ='.$d['fType'];
        }
        if($d['classes']){//类型
            $where  .= ' and b.`classification` ='.$d['classes'];
        }
        if($d['province'] && $d['province'] != '全部'){//省
            $province = str_replace(' ','',$d['province']);
            $where  .= ' and b.province like "%'.$province.'%"';
        }
        if($d['city'] && $d['city'] != '全部'){//市
            $city   = str_replace(' ','',$d['city']) ;
            $where  .= ' and b.city like "%'.$city.'%"';
        }
        if($d['county'] && $d['county'] != '全部'){//区
            $county = str_replace(' ','',$d['county']) ;
            $where  .= ' and b.district like "%'.$county.'%"';
        }

        if($d['time1']){
            $where .= ' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\')>="'.$d['time1'].'"';
        }
        if($d['time2']){
            $where .= ' and DATE_FORMAT(a.create_time, \'%Y-%m-%d\')<="'.$d['time2'].'"';
        }
        if($d['visit']){
            $where .= ' and a.call_type='.$d['visit'] ;
        }
        if($d['keywords']){//关键字
            $findKey = '"%'.$d['keywords'].'%"';
            $where  .= " and (b.EnterpriseID like $findKey or b.companyname like $findKey or b.uname like $findKey or b.phone like $findKey)";
        }

        $field  = "a.create_time,ELT(a.call_type,'QQ','电话') as `call`,";
        $field .= "b.EnterpriseID as EnterpriseID1,b.uname ,b.phone,b.companyname as companyname1,";
        $field .= "b.type,b.classification ,b.province,b.city, b.district,";
        $field .= "c.EnterpriseID as EnterpriseID2,c.companyname as companyname2";
        $field .= ",c.province as province2,c.city as city2, c.district as district2";

        $join   = ' left join firms b on a.firms_id=b.id' ;
        $join  .= ' left join firms c on a.to_firms_id=c.id' ;
        $count  =  $this->table('firms_call_log a')->jion($join)->where($where)->count() ;
        //writeLog($this->lastSql());
        $lists  = $this->table('firms_call_log a')
            ->field($field)
            ->jion($join)
            ->where($where)
            ->order('a.create_time DESC')
            ->limit($pages,$d['pageSize'])
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            $classes = array(''=>'',1=>'轿车商家',2=>'货车商家',3=>'用品商家',4=>'修理厂',5=>'快修保养',6=>'美容店') ;
            foreach ($lists as $k => $item){
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

                if(!$item['province2']){
                    $area2 = '';
                }elseif($item['province2']=='全部'){
                    $area2 = '全部';
                }elseif($item['city2'] == '' || $item['city2'] == '全部'){
                    $area2 = $item['province2'];
                }elseif($item['district2'] == '' || $item['district2'] == '全部'){
                    $area2 = $item['province2'].'/'.$item['city2'];
                }else{
                    $area2 = $item['province2'].'/'.$item['city2'].'/'.$item['district2'];
                }

                $lists[$k]['area1']  = $area ;
                $lists[$k]['area2']  = $area2 ;
                $lists[$k]['type1'] = $item['type'] == 1 ? '经销商' :  ($item['type'] == 2 ? '汽修厂' : '' );
                $lists[$k]['class'] = $classes[$item['classification']] ;
            }
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有记录');
        }
        return $data;




    }
}