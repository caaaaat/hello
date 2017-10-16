<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 14:33
 */
class PlatProductProModel extends Model
{
    /**
     * 产品列表
     * @param $data
     * @return array
     */
    public function getProduct($data){
        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $pages = ($data['page']-1)* $data['pageSize'];

        $where = 'a.is_delete=0' ;

        if($suppProv){
            $where .= ' and b.province ="'.$suppProv.'"';
        }

        if($data['status']){
            $where .= ' and a.pro_status='.$data['status'] ;
        }
        if($data['pro_type']){
            $where .= ' and a.pro_type="'.$data['pro_type'].'"' ;
        }
        if($data['cate_lv1']){
            $where .= ' and a.pro_cate_1='.$data['cate_lv1'] ;
        }
        if($data['cate_lv2']){
            $where .= ' and a.pro_cate_2='.$data['cate_lv2'] ;
        }
        if($data['com_type']){
            $where .= ' and b.type='.$data['com_type'] ;
        }
        if($data['keywords']){
            $where .= ' and (a.proId like "%'.$data['keywords'] .'%" or a.proName like "%'.$data['keywords'] .'%" or b.companyname like "%'.$data['keywords'] .'%" )';
        }
        $join   = ' left join firms b on a.firms_id=b.id' ;//厂商
        //$join  .= ' left join sales_user e on b.salesman_ids=e.id' ;//业务员

        $count  = $this->table('product_list a')->jion($join)->where($where)->count();

        $join  .= ' left join product_category c on a.pro_cate_1=c.id' ;
        $join  .= ' left join product_category d on a.pro_cate_2=d.id' ;



        $field  = 'a.id,a.proId,a.proName,a.pro_type,a.pro_price,a.pro_refresh,a.pro_status';
        $field .= ',a.refresh_time,a.shelve_time';
        $field .= ',b.companyname,b.EnterpriseID,b.type,b.province,b.city,b.district';
        $field .= ',c.name as cate_name1';
        $field .= ',d.name as cate_name2';
        $lists  = $this->table('product_list a')
            ->field($field)
            ->jion($join)
            ->where($where)
            ->limit($pages,$data['pageSize'])
            ->order('a.create_time DESC,a.pro_status ASC')
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



            $data    = array('list'=>$lists,'count'=>$count,'page'=>$data['page'],'pageSize'=>$data['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的产品');
        }

        return $data ;
    }

    /**
     * 上下架
     * @param $proId
     * @param $status
     * @return int
     */
    public function changeStatus($proId,$status){
        $data = array();
        $data['pro_status']  = $status;
        $data['update_time'] = date('Y-m-d H:i:s',time());

        if($status == 1){
            $data['shelve_time'] = date('Y-m-d H:i:s',time());
        }
        $result = $this->table('product_list')->where(array('id'=>$proId))->update($data);
        if($result){
            //记录日志
            $suUser = G('user') ;
            $action = $status == 1 ? '上架产品' : '下架产品';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $result;
    }

    public function delProduct($id){
        $result = $this->table('product_list')->where(array('id'=>$id))->update(array('is_delete'=>1,'update_time'=>date('Y-m-d H:i:s',time())));
        if($result){
            //记录日志
            $suUser = G('user') ;
            $action = '删除产品' ;
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $result;
    }

    /**
     * 产品详情
     * @param $id
     * @return mixed
     */
    public function getOnePro($id){

        $join   = ' left join product_category b on a.pro_cate_1=b.id' ;
        $join  .= ' left join product_category c on a.pro_cate_2=c.id' ;
        $join  .= ' left join firms d on a.firms_id=d.id' ;

        $field  = 'a.id,a.pro_pic,a.proName,a.pro_no,a.pro_brand,a.pro_cate_1,a.pro_cate_2' ;
        $field .= ',a.car_group_id,a.car_group,a.pro_weight,a.pro_area,a.pro_spec,a.pro_text,a.pro_price' ;
        $field .= ',b.name as cate_name1';
        $field .= ',c.name as cate_name2';
        $field .= ',d.EnterpriseID ,d.companyname ,d.type as firmType';

        return $this->table('product_list a')
            ->field($field)
            ->jion($join)
            ->where(array('a.id'=>$id))
            ->getOne();
    }

    public function saveProduct($d){

        $id = isset($d['pro_id']) ? $d['pro_id'] : '' ;
        if($id){
            $proArr = array(
                'pro_pic'       =>$d['img_src'],
                'proName'       =>$d['pro_name'],
                'pro_no'        =>$d['pro_no'],
                'pro_brand'     =>$d['pro_brand'],
                'pro_cate_1'    =>$d['pro_cate_1'],
                'pro_cate_2'    =>$d['pro_cate_2'],
                'pro_price'     =>$d['pro_price'],
                'car_group_id'  =>$d['car_group_id'],
                'car_group'     =>$d['car_group'],
                'pro_text'      =>$d['pro_text'],
                'update_time'   =>date('Y-m-d H:i:s',time())
            );
            $result = $this->table('product_list')->where(array('id'=>$id))->update($proArr);
            if($result){
                //记录日志
                $suUser = G('user') ;
                $action = '编辑产品' ;
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $result = false ;
        }


        return $result;

    }

    /**
     * 产品分类列表
     * @return mixed
     */
    public function getProCate(){
        return $this->table('product_category')->field('id,name,level,pid')->where('level=1 or level=2')->order('vid asc')->get();
    }

    /**
     * 获取一二级车系分类
     * @param $d
     * @return mixed
     */
    public function getCarClass($d){
        $group_id = isset($d['group']) ? $d['group'] : '' ;

        $where = 'type='.$d['type'] .' and (level=1 or level=2)' ;
        if($group_id){
            $where .= ' and id not in ('.$group_id.')' ;
        }
        $CarGroup = $this->table('car_group')
            ->field('id,pid,level,name,img')
            ->where($where)
            ->order(array('vid'=>'asc','type'=>'asc'))->get();
        //writeLog($this->lastSql());
        $CarGroupLv1 = array() ;
        $CarGroupLv2 = array() ;
        $CarGroupItem = array() ;
        //dump($CarGroup);
        if($CarGroup){
            foreach ($CarGroup as $k=>$v){
                if($v['level'] ==1){
                    $CarGroupLv1[$k]['id']   = $v['id'] ;
                    $CarGroupLv1[$k]['name'] = $v['name'] ;
                }else{
                    $CarGroupLv2[$v['pid']][] = $v ;
                }
            }
            foreach ($CarGroupLv1 as $pv){
                foreach ($CarGroupLv2 as $ck=>$cv){
                    if($pv['id'] == $ck){
                        $pv['child'] = $cv ;
                        $item =  $pv ;
                        $CarGroupItem[] = $item ;
                    }
                }
            }

        }
        return $CarGroupItem ;
    }

    public function getCarGroup($d){
        $p_id     = isset($d['p_id'])  ? $d['p_id']  : '' ;
        $group_id = isset($d['group']) ? $d['group'] : '' ;

        $where    = 'pid='.$p_id ;
        if($group_id){
            $where .= ' and id not in ('.$group_id.')' ;
        }
        $CarGroup = $this->table('car_group')
            ->field('id,pid,level,name')
            ->where($where)
            ->order(array('vid'=>'asc','type'=>'asc'))->get();
       if($CarGroup){
           foreach ($CarGroup as $k => $v){
               $where    = 'pid='.$v['id'] ;
               if($group_id){
                   $where .= ' and id not in ('.$group_id.')' ;
               }
               $child = $this->table('car_group')->field('id,pid,level,name')->where($where)->order(array('vid'=>'asc','type'=>'asc'))->get();
               $CarGroup[$k]['child'] = $child;
           }
       }
       return $CarGroup ;
    }


}