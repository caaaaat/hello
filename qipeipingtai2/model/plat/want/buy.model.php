<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/13
 * Time: 16:58
 */
class PlatWantBuyModel extends Model
{

    public function getLists($d){
        $pages  = ($d['page']-1)* $d['pageSize'];

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $find   = 'is_delete=0' ;

        if($suppProv){
            $find  .= ' and b.province ="'.$suppProv.'"';
        }

        if($d['status']){
            $find  .= ' and a.status='.$d['status'] ;
        }

        if($d['keywords']){
            $find  .= ' and ( a.bID like "%'.$d['keywords'].'%" or b.EnterpriseID like "%'.$d['keywords'].'%" or b.uname like "%'.$d['keywords'].'%" or b.phone like "%'.$d['keywords'].'%" or b.companyname like "%'.$d['keywords'].'%" )' ;
        }
        $join   = ' left join firms b on a.firms_id=b.id' ;
        //$join  .= ' left join sales_user e on b.salesman_ids=e.id' ;//业务员

        $field  = 'a.id,a.bID,a.status,a.create_time';
        $field .= ',b.EnterpriseID,b.uname,b.phone,b.companyname,b.province,b.city,b.district' ;

        $count  = $this->table('want_buy a')->jion($join)->where($find)->count();
        $lists  = $this->table('want_buy a')
            ->jion($join)
            ->field($field)->where($find)
            ->order(array('a.create_time'=>'desc','a.id'=>'asc'))
            ->limit($pages,$d['pageSize'])
            ->get();
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
            $data    = array('massageCode'=>0,'massage'=>'没有求购记录');
        }
        return $data;
    }


    public function delWant($d){
        $id  = isset($d['id'])  ? $d['id']  : '' ;
        if( $id ){
            $res = $this->table('want_buy')->where(array('id'=>$id))->update(array('is_delete'=>1,'update_time'=>date('Y-m-d H:i:s',time()))) ;
            if($res){
                //删除对应清单 ...

                //记录日志
                $suUser = G('user') ;
                $action = '删除求购' ;
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $res = false ;
        }
        return $res ;
    }

    public function checkStatus($d){
        $id  = isset($d['id'])  ? $d['id']  : '' ;
        if( $id ){
            $res = $this->table('want_buy')->where(array('id'=>$id))->update(array('status'=>$d['val'],'update_time'=>date('Y-m-d H:i:s',time()))) ;
            if($res){
                //记录日志
                $suUser = G('user') ;
                $action = '下架求购' ;
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $res = false ;
        }
        return $res ;
    }

}