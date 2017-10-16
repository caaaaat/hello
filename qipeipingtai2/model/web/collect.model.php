<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/23
 * Time: 23:10
 */
class WebCollectModel extends Model
{
    /**
     * 收藏店铺
     * @param $type     int 1厂商 2业务员
     * @param $myId     int 厂商id  业务员id
     * @param $storeId
     * @param $do       int 1收藏 2取消收藏
     * @return mixed
     */
    public function collectStore($type,$myId,$storeId,$do){
        if($do==='1' || $do===1){
            $res = $this->table('collect_firms')->insert(array('type'=>$type,'fu_id'=>$myId,'firms_id'=>$storeId,'create_time'=>date('Y-m-d H:i:s')));
        }else{
            $res = $this->table('collect_firms')->where(array('type'=>$type,'fu_id'=>$myId,'firms_id'=>$storeId))->del();
        }
        return $res;
    }
    /**
     * 收藏产品
     * @param $type   int 1厂商 2业务员
     * @param $myId   int 厂商id  业务员id
     * @param $pro_id
     * @return mixed
     */
    public function collectProduct($type,$myId,$pro_id,$do){
        if($do==='1' || $do===1){
            $res = $this->table('collect_product')->insert(array('type'=>$type,'fu_id'=>$myId,'pro_id'=>$pro_id,'create_time'=>date('Y-m-d H:i:s')));
        }else{
            $res = $this->table('collect_product')->where(array('type'=>$type,'fu_id'=>$myId,'pro_id'=>$pro_id))->del();
        }

        return $res;
    }
    //收藏圈子
    public function collectCircle($type,$myId,$circle_id,$do){
        if($do==='1' || $do===1){
            $res = $this->table('collect_circle')->insert(array('type'=>$type,'fu_id'=>$myId,'circle_id'=>$circle_id,'create_time'=>date('Y-m-d H:i:s')));
            if($res){
                $this->query('UPDATE circle SET collection=collection+1 WHERE id='.$circle_id);
            }
        }else{
            $res = $this->table('collect_circle')->where(array('type'=>$type,'fu_id'=>$myId,'circle_id'=>$circle_id))->del();
            if($res){
                $this->query('UPDATE circle SET collection=collection-1 WHERE id='.$circle_id);
            }
        }
        return $res;
    }

    /**
     * 获取我收藏的厂商id
     * @param $type   int 1厂商 2业务员
     * @param $myId   int 厂商id  业务员id
     * @return array
     */
    public function getMyCollectStore($type,$myId){
        $res = $this->table('collect_firms')->field('GROUP_CONCAT(firms_id SEPARATOR ",") as firms')->where(array('fu_id'=>$myId,'type'=>$type))->getOne();
        if($res['firms']){
            return explode(',',$res['firms']);
        }else{
            return array();
        }

    }

    /**
     * 判断是否收藏该产品
     * @param $type      int 1厂商 2业务员
     * @param $myFirmId  int 厂商id  业务员id
     * @param $productId
     * @return bool
     */
    public function isCollectProduct($type,$myFirmId,$productId){
        $res = $this->table('collect_product')->where(array('type'=>$type,'fu_id'=>$myFirmId,'pro_id'=>$productId))->getOne();
        if($res){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 收藏店铺列表
     * @param $type            int 1厂商 2业务员
     * @param $myId            int 厂商id  业务员id
     * @param $classification
     * @param $business
     * @param $categorise
     * @param $keywords
     * @param $page
     * @param $pageSize
     * @return array
     */
    public function collectStoreList($type,$myId,$classification,$business,$categorise,$keywords,$page,$pageSize){
        $start = ($page-1)*$pageSize;
        $where = 'a.type='.$type.' and b.companyname is not null and a.fu_id='.$myId;
        if($classification){
            $where .= ' and b.classification='.$classification;
        }
        if($business){
            $where .= " and b.business like '%,{$business},%' ";
        }
        if($categorise){
            $sql = '';
            $or  = '';
            foreach ($categorise as $v){
                $sql .= $or.' b.business like "%,'.$v.',%" ';
                $or   = ' or ';
            }
            if($sql){
                $where .= ' and ( '.$sql.' )';
            }
        }
        if($keywords){
            $where .= ' and ( b.companyname like "%'.$keywords.'%" or b.major like "%'.$keywords.'%" )';
        }

        $count = $this->table('collect_firms as a')
            ->jion('left join firms as b on a.firms_id=b.id')
            ->where($where)->count();
        $res   = $this->table('collect_firms as a')
            ->field('b.EnterpriseID as sID,a.create_time as collectTime,b.face_pic,b.companyname,b.type,b.classification,b.province,b.city,b.district,b.major,b.wechat_pic,b.is_vip,b.is_check,b.vip_time,QR_pic')
            ->jion('left join firms as b on a.firms_id=b.id')
            ->where($where)->order('a.create_time desc')->limit($start,$pageSize)->get();

        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }

    //收藏产品列表
    public function collectProductList($type,$myId,$pro_type,$pro_cate_1,$pro_cate_2,$keyword='',$page=1,$pageSize=12){
        $start = ($page-1)*$pageSize;
        $where = 'a.type='.$type.' and a.fu_id='.$myId.' and d.pro_status=1 and d.is_delete=0';
        if($pro_type){
            $where .= ' and d.pro_type="'.$pro_type.'"';
        }
        if($pro_cate_1){
            $where.= ' and d.pro_cate_1 ='.$pro_cate_1;
        }
        if($pro_cate_2){
           $where.= ' and d.pro_cate_2 ='.$pro_cate_2;
        }
        if($keyword){
            $where .= ' and ( d.proName like "%'.$keyword.'%" or d.pro_brand like "%'.$keyword.'%" )';
        }

        $count = $this->table('collect_product as a')->jion('left join product_list as d on a.pro_id=d.id')->where($where)->count();
        $data  = $this->table('collect_product as a')
            ->field('d.proId,d.proName,d.pro_type,b.name as cate_1_name,c.name as cate_2_name,d.pro_price,d.car_group,d.pro_pic,d.pro_brand')
            ->jion('left join product_list as d on a.pro_id=d.id left join product_category as b on d.pro_cate_1=b.id left join product_category as c on d.pro_cate_2=c.id')
            ->where($where)->order('a.create_time desc')->limit($start,$pageSize)->get();

        return array('list'=>$data,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }
    //收藏圈子列表
    public function collectCircleList($type,$myId){


    }


}