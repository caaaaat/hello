<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/20
 * Time: 15:04
 */
class ApiSevSalesmanModel extends Model
{
    /**
     * @param $id       解密后的业务员id
     * 返回业务员信息
     * @return mixed
     */
    public function getSalesmanInfo($id){
        $id  = intval($id);
        $rst = $this->table('sales_user')->where('id='.$id)->getOne();
        return $rst;
    }

    /**
     * @param $token    未解密的业务员id
     * 返回当前业务员拨打记录数据
     * @param $dingWeiToken    如果为一，表示定位当前位置失败
     */
    public function getBoDaJiLu($token,$page,$pageSize,$lat=104.06685359181,$lng=30.655965991207,$dingWeiToken){
        $id  = authcode($token,'DECODE');
        $user= $this->getSalesmanInfo($id);
        if($user){
            $p = ($page-1)*$pageSize;
            $data= $this->table('sales_call_log')->where('sales_user_id='.$id.' and is_show=1')->group('firms_id')->limit($p,$pageSize)->get();
//            dump($data);die;
            $count= $this->getOne("select count(distinct firms_id) as nums from sales_call_log where sales_user_id=".$id." and is_show=1");
            $count= $count['nums'];
            if($data){
                $companyIds = '';
                $counts = [];
                for($i=0; $i<count($data); ++$i){
                    $counts[$i]['count']     = $this->table('sales_call_log')->where('firms_id='.$data[$i]['firms_id'].' and sales_user_id='.$id.' and is_show=1')->count();
                    $companyId = $this->table('sales_call_log')->field('firms_id')->where('firms_id='.$data[$i]['firms_id'].' and sales_user_id='.$id.' and is_show=1')->getOne();
                    $counts[$i]['companyId'] = $companyId['firms_id'];
                    $companyIds .= $data[$i]['firms_id'];
                    if($i<count($data)-1){
                        $companyIds .= ',';
                    }
                }
                $filed = 'ROUND(6378.138*2*ASIN(SQRT(POW(SIN(('.$lat.'*PI()/180-latitude*PI()/180)/2),2)+COS('.$lat.'*PI()/180)*COS(latitude*PI()/180)*POW(SIN(('.$lng.'*PI()/180-longitude*PI()/180)/2),2)))*1000) AS distance,companyname,face_pic,major,is_check,vip_time,linkPhone,qq,classification,longitude,latitude,id,type,face_pic';
                $companyInfo = $this->table('firms')->field($filed)->where('id in ('.$companyIds.')')->get();
                for($i=0; $i<count($companyInfo); ++$i){
                    $companyInfo[$i]['distance'] = $this->latlng($companyInfo[$i]['distance']);
                    $telCount = $this->table('firms_call_log')->where('to_firms_id='.$companyInfo[$i]['id'])->count();
                    $companyInfo[$i]['telCount'] = $this->countInt($telCount);
                    $qqCount  = $this->table('firms_visit_log')->where('to_firms_id='.$companyInfo[$i]['id'])->count();
                    $companyInfo[$i]['qqCount'] = $this->countInt($qqCount);
                    if($companyInfo[$i]['vip_time']>date("Y-m-d H:i:s")){
                        $companyInfo[$i]['vip'] = 1;
                    }else{
                        $companyInfo[$i]['vip'] = 0;
                    }
                    for($j=0; $j<count($counts); ++$j){
                        if($companyInfo[$i]['id'] == $counts[$j]['companyId']){
                            $companyInfo[$i]['meCount'] = $counts[$j]['count'];
                            continue;
                        }
                    }
                    if($companyInfo[$i]['qq']){
                        $companyInfo[$i]['qq'] = explode(',',$companyInfo[$i]['qq']);
                    }
                    if($companyInfo[$i]['linkPhone']){
                        $companyInfo[$i]['linkPhone'] = explode(',',$companyInfo[$i]['linkPhone']);
                    }
                    $last_time = $this->table('sales_call_log')->where('sales_user_id='.$id.' and firms_id='.$companyInfo[$i]['id'])->order('create_time desc')->getOne();
                    $last_time = date('m-d H:i',strtotime($last_time['create_time']));
                    $companyInfo[$i]['last_create_time'] = $last_time;
                }
                if($dingWeiToken){
                    //对位失败返回的信息
                    $return = array('status'=>201,'data'=>$companyInfo);
                }else{
                    $return = array('status'=>200,'data'=>$companyInfo);
                }
                 $return = array('list'=>$companyInfo,'count'=>$count,'status'=>'200','page'=>$page,'pageSize'=>$pageSize);

            }else{
                $return = array('status'=>200,'list'=>array(),'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
            }
        }else{
            $return = array('status'=>102,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * 距离转换成文字
     * @param $distance
     * @return string
     */
    public  function latlng($distance){
        $distanceStr = '';
        if($distance){
            if($distance<1){
                $distanceStr = '附近';
            }elseif($distance>99999998){
                $distanceStr = '未知';
            }
            else{
                $f=array(
                    '1'=>'m',
                    '1000'=>'km',
                );
                foreach ($f as $k=>$v){
                    $distanceNum = $distance/(int)$k;
                    $distanceNum  = round($distanceNum,1);
                    if ($distanceNum>1) {
                        $distanceStr =  $distanceNum.$v;
                    }
                }
            }
        }else{
            $distanceStr = '未知';
        }
        return $distanceStr;
    }

    /**
     * 次数转换成文字
     * @param $count
     */
    public  function countInt($count){
        if($count){
            $f=array(
                '1'=>'',
                '1000'=>'k',
            );
            $distanceStr = 0;
            foreach ($f as $k=>$v){
                $distanceNum = $count/(int)$k;
                $distanceNum  = round($distanceNum,1);
                if ($distanceNum>1) {
                    $distanceStr =  $distanceNum.$v;
                }
            }
        }else{
            $distanceStr = 0;
        }
        return $distanceStr;
    }

    /**
     * @param $token        未解密的业务员ID
     * @param $companyId    厂商ID
     * 返回业务员对某个厂商的访问明细记录
     */
    public function getOneCompanyBoDaInfo($token,$companyId,$page,$pageSize){
        $id  = authcode($token,'DECODE');
        $id  = intval($id);
        $user= $this->getSalesmanInfo($id);
        if($user){
            $p = ($page-1)*$pageSize;
            $count = $this->table('sales_call_log')->where('sales_user_id='.$id.' and firms_id='.$companyId)->count();
            $data  = $this->table('sales_call_log')->where('sales_user_id='.$id.' and firms_id='.$companyId)->order('create_time desc')->limit($p,$pageSize)->get();
            if($data){
                $return = array('status'=>200,'list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
            }else{
                $return = array('status'=>200,'list'=>array(),'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * @param $token    未解密的业务员ID
     * 清除访问记录
     */
    public function delYeWuBoDaJiLu($token){
        $id  = authcode($token,'DECODE');
        $id  = intval($id);
        $user= $this->getSalesmanInfo($id);
        if($user){
            $rst = $this->table('sales_call_log')->where('sales_user_id='.$id)->update(array('is_show'=>2));
            if($rst>0){
                $return = array('status'=>200,'msg'=>'已成功清空');
            }else{
                $return = array('status'=>102,'msg'=>'清空失败');
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * @param $token        未解密的业务员ID
     * @param $page
     * @param $pageSize
     */
    public function getThisMonthQiXiu($token,$page,$pageSize,$lat,$lng){
        $id  = authcode($token,'DECODE');
        $id  = intval($id);
        $user= $this->getSalesmanInfo($id);
        if($user){
            $p = ($page-1)*$pageSize;
            $BeginDate= date('Y-m-01 00:00:00', strtotime(date("Y-m-d")));                         //当月第一天
            $endDate  = date('Y-m-d', strtotime("$BeginDate +1 month -1 day")).' 23:59:59';   //当月最后一天
            $count = $this->table('firms_sales_user as a')
                    ->jion('left join firms as b on a.firms_id=b.id')
                    ->where('b.type=2 and a.sales_user_di='.$id.' and a.create_time>="'.$BeginDate.'" and a.create_time<="'.$endDate.'"')
                    ->count();
            $filed = 'ROUND(6378.138*2*ASIN(SQRT(POW(SIN(('.$lat.'*PI()/180-b.latitude*PI()/180)/2),2)+COS('.$lat.'*PI()/180)*COS(latitude*PI()/180)*POW(SIN(('.$lng.'*PI()/180-b.longitude*PI()/180)/2),2)))*1000) AS distance,a.*,b.face_pic,b.id as companyId,b.vid,b.companyname,b.is_check,b.vip_time,b.classification,b.major,b.qq,b.linkPhone';
            $data = $this->table('firms_sales_user as a')
                ->field($filed)
                ->jion('left join firms as b on a.firms_id=b.id')
                ->where('b.type=2 and a.sales_user_di='.$id.' and a.create_time>"'.$BeginDate.'" and a.create_time<"'.$endDate.'"')
                ->limit($p,$pageSize)->get();
            if($data){
                for($i=0; $i<count($data); ++$i){
                    if($data[$i]['linkPhone']){
                        $linkPhone = explode(',',$data[$i]['linkPhone']);
                        $data[$i]['linkPhone'] = $linkPhone[0];
                    }
                    if($data[$i]['qq']){
                        $qq = explode(',',$data[$i]['qq']);
                        $data[$i]['qq'] = $qq[0];
                    }
                    if($data[$i]['vip_time']>date("Y-m-d H:i:s")){
                        $data[$i]['vip'] = 1;
                    }else{
                        $data[$i]['vip'] = 0;
                    }
                    $data[$i]['distance'] = $this->latlng($data[$i]['distance']);
                }
                $return = array('status'=>200,'list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
            }else{
                $return = array('status'=>200,'list'=>array(),'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * @param $token        未解密的业务员ID
     * @param $companyId
     * @param $visit_type  机型3为安卓，4为苹果
     * 存入业务员拨打记录
     */
    public function addYeWuBoDaJiLu($token,$companyId,$visit_type){
        $id  = authcode($token,'DECODE');
        $id  = intval($id);
        $user= $this->getSalesmanInfo($id);
        if($user){
            $arr = array('sales_user_id'=>$id,'firms_id'=>$companyId,'is_show'=>1,'create_time'=>date("Y-m-d H:i:s"),'visit_type'=>$visit_type);
            $rst = $this->table('sales_call_log')->insert($arr);
            if($rst>0){
                $return = array('status'=>200,'msg'=>'保存成功');
            }else{
                $return = array('status'=>104,'msg'=>'数据保存失败');
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * 工资记录列表
     * @param $token  未解密的业务员ID
     * @return array
     */
    public function getWage($token,$page,$pageSize){
        $id  = authcode($token,'DECODE');
        $id  = intval($id);
        $user= $this->getSalesmanInfo($id);
        if($user){
            $p = ($page-1)*$pageSize;
            $count= $this->table('sales_wage_log')->where('sales_user_id='.$id.' and is_show=1')->count();
            $filed= '`id`,`year_month`,total';
            $data = $this->table('sales_wage_log')->where('sales_user_id='.$id.' and is_show=1')->field($filed)->order('`year_month` desc')->limit($p,$pageSize)->get();
            if($data){
                $return = array('status'=>200,'list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
            }else{
                $return = array('status'=>200,'list'=>array(),'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * @param $token    未解密的业务员ID
     * @param $id       工资记录ID
     * @return mixed
     */
    public function getWageInfo($token,$id){
        $sales_user_id  = authcode($token,'DECODE');
        $sales_user_id  = intval($sales_user_id);
        $user= $this->getSalesmanInfo($sales_user_id);
        if($user){
            $field = 'a.id,a.sales_user_id,a.new_firm_num,a.new_firm_prop,a.new_firm_comm,a.firm_num,a.firm_prop,a.firm_comm,';
            $field.= 'a.firm_recharge_money,a.firm_recharge_prop,a.firm_recharge_comm,a.repair_use_money,a.repair_call_money,';
            $field.= 'a.repair_lv1_used,a.repair_lv2_used,a.repair_lv3_used,a.repair_lv1_prop,a.repair_lv2_prop,a.repair_lv3_prop,';
            $field.= 'a.repair_lv1_called,a.repair_lv2_called,a.repair_lv3_called,a.call_lv1_prop,a.call_lv2_prop,a.call_lv3_prop,';
            $field.= 'a.total,DATE_FORMAT(a.year_month, \'%Y-%m\') AS time,a.base_wage,a.subsidies' ;
            if($id){
                $info = $this->table('sales_wage_log a')->field($field)->where(array('a.id'=>$id.' and a.sales_user_id='.$sales_user_id.' and is_show=1'))->getOne() ;//工资基本记录
            }else{
                $monthStart = date('Y-m-01', strtotime('-1 month'));
                $monthEnd   = date('Y-m-t', strtotime("$monthStart +1 month -1 day"));

                $info = $this->table('sales_wage_log a')->field($field)->where('`year_month` between "'.$monthStart.'" and "'.$monthEnd.'" and a.sales_user_id='.$sales_user_id.' and is_show=1')->getOne() ;//工资基本记录
//                dump($info);
            }
            //查询本月新增关联汽修厂数量
            $BeginDate= date('Y-m-01 00:00:00', strtotime(date("Y-m-d")));                         //当月第一天
            $endDate  = date('Y-m-d', strtotime("$BeginDate +1 month -1 day")).' 23:59:59';   //当月最后一天
            $xinZengCount = $this->table('firms_sales_user as a')
                ->jion('left join firms as b on a.firms_id=b.id')
                ->where('b.type=2 and a.sales_user_di='.$sales_user_id.' and a.create_time>="'.$BeginDate.'" and a.create_time<="'.$endDate.'"')
                ->count();

            if($info){
                $return = array('status'=>200,'msg'=>'操作成功','list'=>$info,'xinZenCount'=>$xinZengCount);
            }else{
                $arr = [];
                $arr['new_firm_prop']=$arr['repair_lv1_prop']=$arr['repair_lv2_prop']=$arr['repair_lv3_prop']=$arr['firm_prop']=$arr['firm_recharge_prop']=0.00;
                $arr['new_firm_num']=$arr['repair_use_money']=$arr['repair_lv1_used']=$arr['repair_lv2_used']=$arr['repair_lv3_used']=$arr['firm_num']=$arr['firm_recharge_money']=$arr['base_wage']=$arr['subsidies']=$arr['total']=$arr['new_firm_comm']=$arr['firm_comm']=$arr['firm_recharge_comm']=0;
                $arr['id'] = $id;
                if($id){
                    $info = $this->table('sales_wage_log')->field('year_month')->where('id='.$id)->getOne() ;//工资基本记录
                    if($info){
                        $arr['time'] = date('Y-m', strtotime($info['year_month']));
                    }
                }else{
                    $arr['time'] = date('Y-m', strtotime('-1 month'));
                }
                $return = array('status'=>200,'msg'=>'操作成功','list'=>$arr,'xinZenCount'=>$xinZengCount);
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return ;
    }

    /**
     * @param $token    未解密的业务员ID
     * @param $wageId   业务员工资表ID
     * 查询新增关联汽修厂(上个月关联汽修厂提成)
     */
    public function beforeMonthList($token,$page,$pageSize,$wageId){
        $id  = authcode($token,'DECODE');
        $id  = intval($id);
        $user= $this->getSalesmanInfo($id);
        if($user){
            $p = ($page-1)*$pageSize;
            $BeginDate= date('Y-m-01 00:00:00', strtotime('-1 month'));                     //前月第一天
            $endDate  = date('Y-m-t 23:59:59', strtotime("$BeginDate +1 month -1 day"));   //前月最后一天
            if($wageId){
                $rst = $this->getBeforeMonth($wageId);
                if($rst['status']==200){
                    $where = 'b.type=2 and a.sales_user_di='.$id.' and a.create_time>"'.$rst['data']['startMonth'].'" and a.create_time<"'.$rst['data']['endMonth'].'"';
                }
            }else{
                $where = 'b.type=2 and a.sales_user_di='.$id.' and a.create_time>"'.$BeginDate.'" and a.create_time<"'.$endDate.'"';
            }
            $count = $this->table('firms_sales_user as a')
                ->jion('left join firms as b on a.firms_id=b.id')
                ->where($where)
                ->count();
            $data = $this->table('firms_sales_user as a')
                ->field('b.companyname,a.create_time')
                ->jion('left join firms as b on a.firms_id=b.id')
                ->where($where)
                ->limit($p,$pageSize)->get();
            if($data){
                $return = array('status'=>200,'list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
            }else{
                $return = array('status'=>200,'list'=>array(),'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * @param $token        未解密的业务员ID
     * @param $page
     * @param $pageSize
     * 查询汽修厂拨打提成上个月的数据(页面显示为汽修厂关联提成)
     */
    public function boDaTiCheng($token,$page,$pageSize,$wageId){
        $id  = authcode($token,'DECODE');
        $id  = intval($id);
        $user= $this->getSalesmanInfo($id);
        if($user) {
            $p = ($page - 1) * $pageSize;
            $BeginDate = date('Y-m-01 00:00:00', time());                     //前月第一天
            $endDate = date('Y-m-t 23:59:59', strtotime("$BeginDate +1 month -1 day"));   //前月最后一天
            if ($wageId) {
                $rst = $this->getBeforeMonth($wageId);
                $startTime = $rst['data']['startMonth'];
                $endTime   = $rst['data']['endMonth'];
                if($rst['status']==200){
                    $where = 'value>0 and firm_type=2 and uid='.$id.' and date>="'.$startTime.'" and date<="'.$endTime.'" and type=2';
                }
            }else{
                $where = 'value>0 and firm_type=2 and date>="'.$BeginDate.'" and date<="'.$endDate.'" and uid='.$id.' and type=2';
            }
            $countSql = 'SELECT count(*) as num from sales_wage_info WHERE '.$where.' GROUP BY fid';
            $countAll = $this->count($countSql);
            $countAll = $countAll?$countAll:0;
            $sql = 'SELECT fid,sum(value) as num from sales_wage_info WHERE '.$where.' GROUP BY fid LIMIT '.$p.','.$pageSize;
            $data= $this->get($sql);
            if($data){
                $ids = [];
                for($i=0; $i<count($data); ++$i){
                    array_push($ids,$data[$i]['fid']);
                }
                $ids = join(',',$ids);
                $companyName = $this->table('firms')->field('id,companyname')->where('id in ('.$ids.')')->get();
                if($companyName){
                    for($i=0; $i<count($data); ++$i){
                        for($j=0; $j<count($companyName); ++$j){
                            if($data[$i]['fid'] == $companyName[$j]['id']){
                                $data[$i]['companyName'] = $companyName[$j]['companyname'];
                                continue;
                            }
                        }
                    }
                    $return = array('status'=>200,'list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$countAll);
                }else{
                    $return = array('status'=>103,'msg'=>'数据异常');
                }
            }else{
                $return = array('status'=>200,'list'=>array(),'page'=>$page,'pageSize'=>$pageSize,'count'=>$countAll);
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * @param $token        未解密的业务员ID
     * @param $page
     * @param $pageSize
     * 查询汽修厂拨打提成上个月的数据(页面显示为汽修厂关联提成)
     */
    public function companyPayTiCheng($token,$page,$pageSize,$wageId)
    {
        $id = authcode($token, 'DECODE');
        $id = intval($id);
        $user = $this->getSalesmanInfo($id);
        if ($user) {
            $p = ($page - 1) * $pageSize;
            $BeginDate = date('Y-m-01', time());                     //前月第一天
            $endDate = date('Y-m-t', strtotime("$BeginDate +1 month -1 day"));   //前月最后一天
            if ($wageId) {
                $rst = $this->getBeforeMonth($wageId);
                $startTime = $rst['data']['startMonth'];
                $endTime   = $rst['data']['endMonth'];
                if($rst['status']==200){
                    $where = 'uid='.$id.' and date>="'.$startTime.'" and date<="'.$endTime.'" and type=1';
                }
            }else{
                $where = 'date>="'.$BeginDate.'" and date<="'.$endDate.'" and uid='.$id.' and type=1';
            }
            $countSql = 'SELECT count(*) as num from sales_wage_info WHERE '.$where.' GROUP BY fid';
            $countAll = $this->count($countSql);
            $countAll = $countAll?$countAll:0;
            $sql = 'SELECT fid,sum(value) as price from sales_wage_info WHERE '.$where.' GROUP BY fid LIMIT '.$p.','.$pageSize;
            $data= $this->get($sql);
            if($data) {
                $ids = [];
                for ($i = 0; $i < count($data); ++$i) {
                    array_push($ids, $data[$i]['fid']);
                }
                $ids = join(',', $ids);
                $companyName = $this->table('firms as a')
                        ->field('a.id,a.companyname,b.create_time')
                        ->jion('left join firms_sales_user as b on a.id=b.firms_id and b.sales_user_di='.$id)
                        ->where('a.id in (' . $ids . ')')->get();
                if($companyName){
                    for($i=0; $i<count($data); ++$i){
                        for($j=0; $j<count($companyName); ++$j){
                            if($data[$i]['fid'] == $companyName[$j]['id']){
                                $data[$i]['companyName'] = $companyName[$j]['companyname'];
                                $data[$i]['guanLianTime']= $companyName[$j]['create_time'];
                                continue;
                            }
                        }
                    }
                    $return = array('status'=>200,'list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$countAll);
                }else{
                    $return = array('status'=>103,'msg'=>'数据异常');
                }
            }else{
                $return = array('status'=>200,'list'=>array(),'page'=>$page,'pageSize'=>$pageSize,'count'=>$countAll);
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * @param $monthStart   月初 (2017-05-01)
     * @param $monthEnd     月末 (2017-05-31)
     * @param $id           解密后的业务员id
     */
    public function getMonthXiShu($monthStart,$monthEnd,$id,$wageId=''){
        if($wageId){
            $where = 'sales_user_id='.$id.' and id='.$wageId;
        }else{
            $where = 'sales_user_id='.$id.' and `year_month` between "'.$monthStart.'" and "'.$monthEnd.'"';
        }
        $xiShu = $this->table('sales_wage_log')->field('firm_recharge_prop')->where($where)->getOne();
        return $xiShu;
    }

    /**
     * @param $wageId   业务员工资表ID
     *
     */
    public function getBeforeMonth($wageId){
        $month = $this->table('sales_wage_log')->field('`year_month`')->where('id='.$wageId)->getOne();
        if($month){
            $month = $month['year_month'];  //2017-05-03
            $monthStart = date('Y-m-01',strtotime($month));
            $monthDtart = date('Y-m-d',strtotime('+1 month -1 day',strtotime($monthStart)));
            $data['startMonth'] = $monthStart;
            $data['endMonth']   = $monthDtart;
            $return = array('status'=>200,'data'=>$data);
        }else{
            $return['status'] = 0;
            $return['msg'] = '月份参数错误';
        }
        return $return;
    }


    /**
     * 返回所有修理厂坐标
     * 访问次数(包含QQ和电话)仅统计厂商的访问记录，不包含业务员访问次数
     * @param $city             市
     * @param $district         区
     * @param $classType        厂商类型(1:经销商,2:汽修厂)
     * @param $jiShu            级数(1:一级,2:二级,3:三级)
     * @param $shaiXuan         筛选(1:未认证厂商,2:已关联厂商,3:未关联厂商)
     */
    public function getAllZuoBiao($token,$city,$district,$classType,$jiShu,$shaiXuan,$cityCode,$keywords){
        $id  = authcode($token,'DECODE');
        $id  = intval($id);
        $user= $this->getSalesmanInfo($id);
        if($user){
            $area = $user['area'];      //业务员管辖省份
            if($area == $cityCode){
                //            $where = 'longitude is not null and latitude is not null';
                $where = 'province="'.$cityCode.'"';
                if($city){
                    $where .= ' and city="'.$city.'"';
                }
                if($district){
                    $where .= ' and district="'.$district.'"';
                }
                if($classType){
                    $where .= ' and type='.$classType;
                }
                if($shaiXuan && $shaiXuan==1){
                    $where .= ' and is_check=2';
                }
                if($keywords){
                    $where .= ' and (companyname like "%'.$keywords.'%" or phone like "%'.$keywords.'%" or linkPhone like "%'.$keywords.'%")';
                }
                $field = 'id,EnterpriseID,longitude,latitude,phone,linkPhone,companyname,EnterpriseID,type,0 as isGuanLian,1 as level,scale,is_check';
                $data  = $this->table('firms')->field($field)->where($where)->get();
                $newDay= date('Y-m-d',time());
                $guanLian = $this->table('firms_sales_user')->where('end_time>"'.$newDay.'"')->get();
                if($data){
                    $newTime   = date('Y-m-d H:i:s',time());
                    $startTime = date('Y-m-d H:i:s',strtotime('-30 day'));
                    $levelXiShu= $this->table('base_ini')->field('value')->where('id=7')->getOne();     //获取拨打级数配置
                    $levelXiShu= json_decode($levelXiShu['value']);
                    for($i=0; $i<count($data); ++$i){
                        $nums = $this->table('firms_call_log')->where('to_firms_id='.$data[$i]['id'].' and create_time between "'.$startTime.'" and "'.$newTime.'"')->count();
                        $data[$i]['callCount'] = $nums;
                        if($nums>=$levelXiShu->lv2->min && $nums <=$levelXiShu->lv2->max){
                            $data[$i]['level'] = 2;
                        }elseif($nums>=$levelXiShu->lv3->min){
                            $data[$i]['level'] = 3;
                        }
                    }
                    if($guanLian){
                        for($i=0; $i<count($data); ++$i){
                            for($j=0; $j<count($guanLian); ++$j){
                                if($data[$i]['id']==$guanLian[$j]['firms_id']){
                                    $data[$i]['isGuanLian'] = 1;
                                    continue;
                                }
                            }
                        }
                    }
                    //级数筛选
                    if($jiShu){
                        $list = $data;
                        $data = [];
                        $k = 0;
                        for($i=0; $i<count($list); ++$i){
                            if($list[$i]['level'] == $jiShu){
                                $data[$k] = $list[$i];
                                $k += 1;
                            }
                        }
                    }
                    //筛选厂商
                    if($shaiXuan){
                        if($shaiXuan==2 || $shaiXuan==3){
                            if($shaiXuan==2){
                                $tiaoJian = 1;
                            }else{
                                $tiaoJian = 0;
                            }
                            $list = $data;
                            $data = [];
                            $k = 0;
                            for($i=0; $i<count($list); ++$i){
                                if($list[$i]['isGuanLian'] == $tiaoJian){
                                    $data[$k] = $list[$i];
                                    $k += 1;
                                }
                            }
                        }
                    }
                    $return = array('status'=>200,'list'=>$data,'msg'=>'获取数据成功');
                }else{
                    $return = array('status'=>200,'list'=>array(),'msg'=>'获取数据成功');
                }
            }else{
                $return = array('status'=>104,'msg'=>'关键信息不一致，请重新登录');
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * 返回所有修理厂坐标
     * 访问次数(包含QQ和电话)仅统计厂商的访问记录，不包含业务员访问次数
     * @param $city             市
     * @param $district         区
     * @param $classType        厂商类型(1:经销商,2:汽修厂)
     * @param $jiShu            级数(1:一级,2:二级,3:三级)
     * @param $shaiXuan         筛选(1:未认证厂商,2:已关联厂商,3:未关联厂商)
     * SELECT
     *,
    SELECT *, CASE WHEN t.num > 10 THEN 3 WHEN t.num > 3 THEN 2 WHEN t.num <= 3 THEN 1 END AS levels FROM ( SELECT a.id, COUNT(b.id) AS num, b.create_time FROM firms AS a LEFT JOIN firms_call_log AS b ON a.id = b.to_firms_id WHERE b.create_time > "2017-05-27 05:11:23" OR b.create_time IS NULL GROUP BY a.id ) AS t LIMIT 0, 100
     *
     * SELECT *, CASE WHEN t.num > 10 THEN 3 WHEN t.num > 3 THEN 2 WHEN t.num <= 3 THEN 1 END AS levels FROM (
    SELECT a.id, COUNT(b.id) AS num, b.create_time FROM firms AS a LEFT JOIN firms_call_log AS b ON a.id = b.to_firms_id WHERE b.create_time > "2017-05-27 05:11:23" OR b.create_time IS NULL GROUP BY a.id )
    LEFT JOIN firms_sales_user g ON t.id=g.firms_id WHERE g.end_time<"2017-6-27"
    AS t LIMIT 0, 100
     */
    public function getAllListZuoBiao($token,$city,$district,$classType,$jiShu,$shaiXuan,$page,$pageSize,$lat=30.674024,$lng=104.072315,$cityCode,$keywords){
        $id  = authcode($token,'DECODE');
        $id  = intval($id);
        $user= $this->getSalesmanInfo($id);
        if($user){
            $area = $user['area'];      //业务员管辖省份
            if($area == $cityCode){
                $startTime = date('Y-m-d H:i:s',strtotime('-30 day'));
                $newTime   = date('Y-m-d',time());
                $p = ($page-1)*$pageSize;
                $where = 'y.firmId>0';
                if($city){
                    $where .= ' and y.city="'.$city.'"';
                    if($district){
                        $where .= ' and y.district="'.$district.'"';
                    }
                }
                if($classType){
                    $where .= ' and y.type='.$classType;
                }
                if($jiShu){
                    $where .= ' and y.levels='.$jiShu;
                }
                if($shaiXuan){
                    if($shaiXuan==1){       //未认证
                        $where .= ' and y.is_check>1';
                    }
                    if($shaiXuan==2){       //已关联
                        $where .= ' and y.isGuanLian>0';
                    }
                    if($shaiXuan==3){       //未关联
                        $where .= ' and y.isGuanLian=0';
                    }
                }
                if($keywords){
                    $where .= ' and (y.companyname like "%'.$keywords.'%" or y.phone like "%'.$keywords.'%" or y.linkPhone like "%'.$keywords.'%")';
                }
                $levelXiShu= $this->table('base_ini')->field('value')->where('id=7')->getOne();     //获取拨打级数配置
                $levelXiShu= json_decode($levelXiShu['value']);
                //获取数据总条数
                $sqlC  = 'SELECT * from (';
                $sqlC .= 'SELECT t.id as firmId,t.EnterpriseID,t.companyname,t.major,t.classification,t.linkPhone,t.qq,t.longitude,t.latitude,count(g.id) as isGuanLian ,t.is_check,t.type,t.city,t.district,t.phone,t.num as callCount, CASE WHEN t.num >= '.$levelXiShu->lv3->min.' THEN 3 WHEN t.num >= '.$levelXiShu->lv2->min.' THEN 2 WHEN t.num <= '.$levelXiShu->lv1->max.' THEN 1 END AS levels FROM ';
                $filedC = 'a.companyname,a.EnterpriseID,a.major,a.is_check,a.classification,a.linkPhone,a.phone,a.qq,a.longitude,a.latitude,a.type,a.id,a.city,a.district,b.create_time,count(b.id) AS num';
                $sqlC .= '(SELECT '.$filedC.' FROM firms AS a LEFT JOIN firms_call_log AS b ON a.id = b.to_firms_id AND b.create_time>"'.$startTime.'" where a.province="'.$cityCode.'" GROUP BY a.id)';
                $sqlC .= ' AS t LEFT JOIN firms_sales_user g ON t.id=g.firms_id and g.end_time>"'.$newTime.'" GROUP BY t.id';
                $sqlC .= ') as y where '.$where;
                $count = $this->count($sqlC);
                //获取数据
                $sql  = 'SELECT * from (';
                $sql .= 'SELECT t.id as firmId,t.companyname,t.face_pic,t.major,t.distance,t.classification,t.linkPhone,t.qq,t.longitude,t.latitude,count(g.id) as isGuanLian ,t.is_check,t.type,t.EnterpriseID,t.city,t.phone,t.district,t.vip_time,t.num as callCount, CASE WHEN t.num >= 10 THEN 3 WHEN t.num >= 3 THEN 2 WHEN t.num <= 3 THEN 1 END AS levels FROM ';
                $filed = 'ROUND(6378.138*2*ASIN(SQRT(POW(SIN(('.$lat.'*PI()/180-a.latitude*PI()/180)/2),2)+COS('.$lat.'*PI()/180)*COS(latitude*PI()/180)*POW(SIN(('.$lng.'*PI()/180-a.longitude*PI()/180)/2),2)))*1000) AS distance,a.companyname,a.major,a.is_check,a.classification,a.linkPhone,a.qq,a.longitude,a.face_pic,a.latitude,a.type,a.id,a.city,a.phone,a.district,b.create_time,a.vip_time,count(b.id) AS num,a.EnterpriseID';
                $sql .= '(SELECT '.$filed.' FROM firms AS a LEFT JOIN firms_call_log AS b ON a.id = b.to_firms_id AND b.create_time>"'.$startTime.'" where a.province="'.$cityCode.'" GROUP BY a.id)';
                $sql .= ' AS t LEFT JOIN firms_sales_user g ON t.id=g.firms_id and g.end_time>"'.$newTime.'" GROUP BY t.id';
                $sql .= ') as y where '.$where.' LIMIT '.$p.','.$pageSize;
                $data = $this->get($sql);
                if($data){
                    $nowTime = date('Y-m-d H:i:s',time());
                    for($i=0; $i<count($data); ++$i){
                        if($data[$i]['linkPhone']){
                            $data[$i]['linkPhone'] = explode(',',$data[$i]['linkPhone']);
                        }
                        if($data[$i]['qq']){
                            $data[$i]['qq'] = explode(',',$data[$i]['qq']);
                        }
                        $firmCallNum = $this->table('firms_call_log')->where('to_firms_id='.$data[$i]['firmId'])->count();
//                        $yeWuCallNum = $this->table('sales_call_log')->where('firms_id='.$data[$i]['firmId'])->count();
//                        $allCallNum  = $firmCallNum+$yeWuCallNum;
                        $allCallNum  = $firmCallNum;
                        $firmQqNum   = $this->table('firms_visit_log')->where('to_firms_id='.$data[$i]['firmId'])->count();
                        if($allCallNum>0){
                            $allCallNum = $this->countInt($allCallNum);
                        }
                        if($firmQqNum>0){
                            $firmQqNum = $this->countInt($firmQqNum);
                        }
                        $data[$i]['telNum'] = $allCallNum;
                        $data[$i]['qqNum']  = $firmQqNum;
                        if($data[$i]['distance']){
                            $data[$i]['distance'] = $this->latlng($data[$i]['distance']);
                        }
                        $data[$i]['vip'] = '';
                        if($data[$i]['vip_time']){
                            if($data[$i]['vip_time']>$nowTime){
                                $data[$i]['vip'] = 1;
                            }
                        }
                    }
                    $return = array('status'=>200,'list'=>$data,'msg'=>'获取数据成功','page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
                }else{
                    $return = array('status'=>200,'list'=>array(),'msg'=>'获取数据成功','page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
                }
            }else{
                $return = array('status'=>104,'msg'=>'关键信息不一致，请重新登录');
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * @param $token
     * @param $page
     * @param $pageSize
     * @param $wageId       业务员工资表id
     * @param $jiShu        汽修厂级数
     */
    public function qiXiuJiShuPinLv($token,$page,$pageSize,$wageId,$jiShu){
        $id = authcode($token, 'DECODE');
        $id = intval($id);
        $user = $this->getSalesmanInfo($id);
        if ($user) {
            $p = ($page - 1) * $pageSize;
            $BeginDate = date('Y-m-01 00:00:00', time());                     //前月第一天
            $endDate = date('Y-m-t 23:59:59', strtotime("$BeginDate +1 month -1 day"));   //前月最后一天
            if ($wageId) {
                $rst = $this->getBeforeMonth($wageId);
                $startTime = $rst['data']['startMonth'].' 00:00:00';
                $endTime   = $rst['data']['endMonth'].' 23:59:59';
                if($rst['status']==200){
                    $where = 'firm_type=2 and uid='.$id.' and create_time>"'.$startTime.'" and create_time<"'.$endTime.'" and type=3 and lv='.$jiShu;
                }
            }else{
                $where = 'firm_type=2 and create_time>"'.$BeginDate.'" and create_time<"'.$endDate.'" and uid='.$id.' and type=3 and lv='.$jiShu;
            }
            $countSql = 'SELECT count(*) as num from sales_wage_info WHERE '.$where.' GROUP BY fid';
            $countAll = $this->count($countSql);
            $countAll = $countAll?$countAll:0;
            $sql = 'SELECT fid,count(id) as num from sales_wage_info WHERE '.$where.' GROUP BY fid LIMIT '.$p.','.$pageSize;
            $data= $this->get($sql);
            if($data) {
                $ids = [];
                for ($i = 0; $i < count($data); ++$i) {
                    array_push($ids, $data[$i]['fid']);
                }
                $ids = join(',', $ids);
                $companyName = $this->table('firms')->field('id,companyname')->where('id in ('.$ids.')')->get();
                if($companyName){
                    for($i=0; $i<count($data); ++$i){
                        for($j=0; $j<count($companyName); ++$j){
                            if($data[$i]['fid'] == $companyName[$j]['id']){
                                $data[$i]['companyName'] = $companyName[$j]['companyname'];
                                continue;
                            }
                        }
                    }
                    $return = array('status'=>200,'list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$countAll);
                }else{
                    $return = array('status'=>103,'msg'=>'数据异常');
                }
            }else{
                $return = array('status'=>200,'list'=>array(),'page'=>$page,'pageSize'=>$pageSize,'count'=>$countAll);
            }
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }

    /**
     * @param $token
     * @param $img  头像图片
     */
    public function saveHeader($token,$img){
        $id = authcode($token, 'DECODE');
        $id = intval($id);
        $user = $this->getSalesmanInfo($id);
        if ($user) {
            $time = date('Y-m-d h:i:s', time());
            $this->table('sales_user')->where('id='.$user['id'])->update(array('update_time'=>$time,'facepic'=>$img));
            $return = array('status'=>200,'msg'=>'操作成功');
        }else{
            $return = array('status'=>103,'msg'=>'登录信息不正确，请重新登录');
        }
        return $return;
    }
}