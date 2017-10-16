<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10
 * Time: 0:32
 */
class PlatAuthenticationAuthenticationModel extends Model
{

    /**
     * 认证列表
     * @param $d
     * @return array
     */
    public function getAuthentication($d){
        $pages  = ($d['page']-1)* $d['pageSize'];

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        if($d['type']  == 1){
            $find   = 'a.status=1' ;
        }else{
            $find   = 'a.status in (2,3)' ;
        }

        if($suppProv){
            $find  .= ' and b.province ="'.$suppProv.'"';
        }

        if($d['result']){
            $find  .= ' and a.status=' .$d['result'];
        }
        if($d['keyword']){
            $find  .= ' and (b.EnterpriseID like "%' .$d['keyword'].'%" or b.companyname like "%' .$d['keyword'].'%")';
        }
        $join   = ' left join firms b on a.firms_id=b.id' ;
        //$join  .= ' left join sales_user e on b.salesman_ids=e.id' ;//业务员

        $field  = 'a.id,a.create_time,a.status,';
        $field .= 'b.EnterpriseID,b.companyname,b.type,b.classification,b.province,b.city,b.district';


        $count  = $this->table('firms_check a') ->jion($join)->where($find)->count();
        $lists  = $this->table('firms_check a')
            ->field($field)->where($find)
            ->jion($join)
            ->order(array('a.create_time'=>'desc','a.id'=>'asc'))
            ->limit($pages,$d['pageSize'])
            ->get();
        //writeLog($this->lastSql());
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

            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有记录');
        }
        return $data;
    }


    /**
     * 认证详情
     * @param $id
     * @return array|mixed
     */
    public function getOneApply($id){
        if($id){
            $field = 'id,firmsName,firmsMan,province,city,district,firmsTel,address,licence_pic,taxes_pic,field_pic,brand_pic,agents_pic,status,reason' ;
            $res = $this->table('firms_check')->where('id='.$id)->field($field)->getOne();
        }else{

            $res = array() ;
        }

        return $res ;
    }

    /**
     * 通过/拒绝认证
     * @param $d
     * @return bool
     */
    public function checkStat($d){
        $id = isset($d['id']) ? $d['id'] : '' ;
        if ($id){
            $res = $this->table('firms_check')->where('id='.$d['id'])
                ->update(array('status'=>$d['status'],'reason'=>$d['reason'],'update_time'=>date('Y-m d H:i:s')));
            if($res){
                if($d['status'] == 2){
                    $firmId = $this->table('firms_check')->field('firms_id')->where('id='.$d['id'])->getOne() ;
                    $this->table('firms')->where('id='.$firmId['firms_id'])
                        ->update(array('is_check'=>1,'update_time'=>date('Y-m d H:i:s')));
                }

                //记录日志
                $suUser = G('user') ;
                $action = $d['status'] == 2 ? '同意认证申请' : '拒绝认证申请';
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $res = false ;
        }
        return $res ;
    }
}