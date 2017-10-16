<?php

/**
 * 人工收费
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/13
 * Time: 11:53
 */
class PlatChargeChargeModel extends Model
{
    /**
     * 获取数据统一入口
     * @param $d
     * @return array|void
     */
    public function getLists($d){
        if($d['style'] == 2){
            $res = $this->getPayHistory($d);
        }else{
            $res = array('massageCode'=>0,'massage'=>'非法操作');
        }
        return $res ;
    }


    /**
     * 收费记录
     * @param $d
     * @return array
     */
    private function getPayHistory($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = 'a.status=1' ;

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        if($suppProv){
            $where  .= ' and b.province ="'.$suppProv.'"';
        }

        if($d['type']){
            $where  .= ' and a.type=' .$d['type'];
        }else{
            $where  .= ' and (a.type=1 or a.type=2)';
        }
        if($d['firm']){
            $where  .= ' and b.type=' .$d['firm'];
        }
        if($d['keywords']){
            $where  .= ' and (b.EnterpriseID like "%' .$d['keywords'].'%" or b.companyname like "%' .$d['keywords'].'%" or c.name like "%' .$d['keywords'].'%" or c.code like "%' .$d['keywords'].'%" )';
        }
        $join   = ' left join firms b on a.firms_id=b.id' ;
        $join  .= ' left join su_user c on a.admin_id=c.id' ;
        //$join  .= ' left join sales_user e on b.salesman_ids=e.id' ;//业务员


        $field  = 'a.type,a.info,a.refresh_point,a.money,a.create_time,a.vip_month,a.info,';
        $field .= 'b.EnterpriseID,b.companyname,b.type as firm_type,';
        $field .= 'c.code,c.name';


        $count  = $this->table('pay_history a') ->jion($join)->where($where)->count();
        $lists  = $this->table('pay_history a')
            ->field($field)->where($where)
            ->jion($join)
            ->order(array('a.create_time'=>'desc','a.id'=>'asc'))
            ->limit($pages,$d['pageSize'])
            ->get();
        if($lists){
            /*foreach ($lists as $k => $v){
                if($v['type'] == 1){
                    $content = '充值VIP '.$v['vip_month'].' 个月' ;
                }else{
                    $content = '充值刷新点 '.$v['refresh_point'].' 点' ;
                }
                $lists[$k]['content'] = $content ;
            }*/


            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有记录');
        }
        return $data;
    }

    /**
     * 获取充值厂商
     * @param $d
     * @return array
     */
    public function getChoiceFirm($d){
        //$find = 'is_sales=0 and is_check=1' ;

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;


        //$find = 'a.is_check=1' ;


        if($d['type'] == 1){
            $find  = ' a.type=1' ;
        }else{
            $find  = ' ( a.type=1 or a.type=2 )' ;
        }
        if($suppProv){
            $find .= ' and a.province ="'.$suppProv.'"';
        }

        if($d['keywords']){
            $findKey = '"%'.$d['keywords'].'%"';
            $find .= " and (a.`EnterpriseID` like $findKey or a.`uname` like $findKey or a.`companyname` like $findKey or a.`phone` like $findKey)";
        }

        //$join  = ' left join sales_user b on a.salesman_ids=b.id' ;

        $field = 'a.id,a.vid,a.EnterpriseID,a.uname,a.phone,a.companyname,a.type,a.classification,a.province,a.city,a.district';
        $lists = $this->table('firms a')->field($field)->where($find)
            ->order(array('a.vid'=>'asc','a.create_time'=>'desc','a.id'=>'asc'))->get();

        if($lists){
            if($lists){
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
                    $lists[$k]['area'] = $area ;
                }
                $return['list'] = $lists ;
            }
            $data    = array('list'=>$lists,'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的经销商');
        }
        return $data;

    }


    /**
     * 充值
     * @param $d
     * @return bool
     */
    public function chargeMoney($d){
        $firmId = isset($d['firm'])  ? $d['firm']  : '' ;
        $num    = isset($d['num'])   ? $d['num']   : '' ;
        $money  = isset($d['money']) ? $d['money'] : '' ;
        $suUser = G('user') ;
        $info   = $d['type'] == 1 ? '充值VIP至'.$num : '充值刷新点'.$num .'点' ;
        $action = $d['type'] == 1 ? '充值VIP' : '充值刷新点' ;
        if($firmId && $num ){
            $charge = array(
                'type'=>$d['type'],
                'status'=>1,
                'info'=>$info,
                'payway'=>3,
                'refresh_point'=>$num,
                'vip_month'=>$num,
                'firms_id'=>$firmId,
                'money'=>$money,
                'admin_id'=>$suUser['id'],
                'create_time'=>date('Y-m-d H:i:s')
            );

            $res = $this->table('pay_history')->insert($charge);

        }else{
            $res = false ;
        }
        if($res){
            $firm = $this->table('firms')->field('vip_time,refresh_point')->where(array('id'=>$firmId))->getOne();
            $time = time() ;
            $vip_time = strtotime($firm['vip_time']);

            if($d['type'] == 1){//VIP

                $new_date = $num ;
                /*if($vip_time < $time){ //VIP已经过期
                    $new_date  = date('Y-m-d H:i:s',strtotime("+ ".$num ."month"));
                }else{
                    $new_date  = date('Y-m-d H:i:s',strtotime("+ ".$num ."month",strtotime($firm['vip_time'])));
                }*/
                $this->table('firms')->where(array('id'=>$firmId))
                    ->update(array('vip_time'=>$new_date,'is_vip'=>1,'update_time'=>date('Y-m-d H:i:s',$time)));



            }else{//刷新点
                $new_point = $firm['refresh_point'] > 0 ? ($firm['refresh_point'] + $num) : $num ;
                $this->table('firms')->where(array('id'=>$firmId))
                    ->update(array('refresh_point'=>$new_point,'update_time'=>date('Y-m-d H:i:s',$time)));
            }
            //记录日志
            //$action = $info;
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }
}