<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/23
 * Time: 23:05
 */
class WebTobuyModel extends Model
{
    public function getDataList($car_group_4=array(),$currentCity,$page=1,$pageSize=6){
        $start = ($page-1)*$pageSize;
        $where = 'a.status=1 and is_delete=0';
        if($car_group_4){
            $car_group_4_string=implode(',',$car_group_4);
            $where .= ' and a.car_group_id in ( '.$car_group_4_string.' )';
        }
        if($currentCity){
            $where .= ' and f.city like "%'.$currentCity.'%" ';
        }
        $count = $this->table('want_buy as a')->jion('left join firms as f on f.id=a.firms_id')->where($where)->count();
        $data  = $this->table('want_buy as a')
            ->field('a.*,f.companyname,f.EnterpriseID')
            ->jion('left join firms as f on f.id=a.firms_id')
            ->where($where)
            ->order('a.create_time desc')->limit($start,$pageSize)->get();
        $sql = $this->lastSql();
        if($data){
            foreach ($data as $k=>$v){
                $data[$k]['pic_3'] = $this->table('want_buy_pic')->field('pic_url')->where(array('want_buy_id'=>$v['id']))->limit(0,3)->get();
                $data[$k]['peiJS'] = $this->table('want_buy_list')->where(array('want_buy_id'=>$v['id']))->count();

                $str = $this->getCarGroupStrByFourId($v['car_group_id']);
                $data[$k]['c1and2'] = $str['c1and2'];
                $data[$k]['c3and4'] = $str['c3and4'];
            }
        }
        return array('list'=>$data,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }

    /**
     * @param $id
     */
    public function getCarGroupStrByFourId($id){
        $str = $this->table('car_group as a')->field('CONCAT(d.name,"/",c.name,",",b.name,"/",a.name) as str')
            ->jion('LEFT JOIN car_group as b on a.pid=b.id LEFT JOIN car_group as c on b.pid=c.id LEFT JOIN car_group as d on c.pid=d.id ')
            ->where(array('a.id'=>$id))->getOne();
        $return = array('c1and2'=>'','c3and4'=>'','str'=>'');
        if($str['str']){
            $return['c1and2'] = strstr($str['str'], ',', TRUE);
            $return['c3and4'] = strstr($str['str'], ',');
            $return['str']    = str_replace(',','/',$str['str']);
        }
        return $return;
    }
}