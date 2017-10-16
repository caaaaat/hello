<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/14
 * Time: 21:42
 */
class WebProductModel extends Model
{
    //获取产品
    public function getProduct($firms_id,$pro_type,$pro_cate_1=array(),$pro_cate_2=array(),$keyword='',$page=1,$pageSize=12,$currentCity='成都市'){
        $start = ($page-1)*$pageSize;
        $where = 'a.pro_status=1 and a.is_delete=0';
        if($firms_id){
            $where .= ' and a.firms_id='.$firms_id;
        }else{
            if($currentCity){
                $where .= ' and f.city like "%'.$currentCity.'%" ';
            }
        }
        if($pro_type){
            $where .= ' and a.pro_type="'.$pro_type.'"';
        }
        if($pro_cate_1){
            $sql = '';
            $or  = '';
            foreach ($pro_cate_1 as $v){
                $sql .= $or.' a.pro_cate_1 ='.$v;
                $or   = ' or ';
            }
            if($sql){
                $where .= ' and ( '.$sql.' )';
            }
        }
        if($pro_cate_2){
            $sql = '';
            $or  = '';
            foreach ($pro_cate_2 as $v){
                $sql .= $or.' a.pro_cate_2 ='.$v;
                $or   = ' or ';
            }
            if($sql){
                $where .= ' and ( '.$sql.' )';
            }
        }
        if($keyword){
            $where .= ' and ( a.proName like "%'.$keyword.'%" or a.pro_brand like "%'.$keyword.'%" )';
        }

        $count = $this->table('product_list as a')->jion('inner join firms as f on a.firms_id=f.id left join product_category as b on a.pro_cate_1=b.id left join product_category as c on a.pro_cate_2=c.id')->where($where)->count();
        $data  = $this->table('product_list as a')
            ->field('a.proId,a.proName,a.pro_type,b.name as cate_1_name,c.name as cate_2_name,a.pro_price,a.car_group,a.pro_pic')
            ->jion('inner join firms as f on a.firms_id=f.id left join product_category as b on a.pro_cate_1=b.id left join product_category as c on a.pro_cate_2=c.id')
            ->where($where)->order('a.pro_refresh desc,a.create_time desc')->limit($start,$pageSize)->get();
        foreach ($data as $k=>$v){
            if($v['pro_price']==0 || $v['pro_price']=='0.00'){
                $data[$k]['pro_price'] = '欢迎来电咨询';
            }else{
                $data[$k]['pro_price'] = '￥ '.$v['pro_price'];
            }
        }
        return array('list'=>$data,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }

    //首页新品促销6条
    public function getNewPro($currentCity){
        $res = $this->table('product_list as a')
            ->field('a.proId,a.proName,a.car_group,a.pro_price,a.pro_pic,c.name')
            ->jion('left join firms as b on a.firms_id=b.id left join product_category as c on a.pro_cate_1=c.id')
            ->where('a.is_delete=0 and a.pro_status=1 and a.pro_type="新品促销" and b.city like "%'.$currentCity.'%"')
            ->order('a.refresh_time desc,b.create_time desc')
            ->limit(0,6)->get();
        return $res;
    }
    //首页库存清仓6条
    public function getEmpty($currentCity){
        $res = $this->table('product_list as a')
            ->field('a.proId,a.proName,a.car_group,a.pro_price,a.pro_pic,c.name')
            ->jion('left join firms as b on a.firms_id=b.id left join product_category as c on a.pro_cate_1=c.id')
            ->where('a.is_delete=0 and a.pro_status=1 and a.pro_type="库存清仓" and b.city like "%'.$currentCity.'%"')
            ->order('a.refresh_time desc,b.create_time desc')
            ->limit(0,6)->get();
        return $res;
    }

    /**
     * @param $data            数据
     * @param string $proId    产品ID
     */
    public function saveProduct($data,$proId=''){
        $rst = 0;
        if($proId){
            //修改
            $rst = $this->table('product_list')->where('proId='.$proId)->update($data);
        }else{
            //添加
            $data['pro_refresh'] = 0;
            $data['pro_status'] = 1;
            $rst = $this->table('product_list')->insert($data);
        }
        return $rst;
    }

    /**
     * @param $time 当前时间搓
     * 生成产品表唯一产品ID
     */
    public function getUniq($time){
        $t = substr($time , 4 , 6);
        $s = substr(strval(rand(10000,19999)),1,4);
        $code = $s.$t;
        $proIds = $this->table('product_list')->where('proId="'.$code.'"')->getOne();
        if($proIds){
           $this->getUniq($time);
        }else{
            return $code;
        }
    }

    /**
     * @param $id      厂商ID
     * @param $status  上架状态
     *  返回本厂商的产品列表
     */
    public function getProductList($id,$status,$seekData){
        $where = 'a.firms_id='.$id.' and pro_status='.$status.' and is_delete != 1';
        if($seekData['pro_type']){
            $where .= ' and a.pro_type="'.$seekData['pro_type'].'"';
        }
        if($seekData['pro_cate_1']){
            $where .= ' and a.pro_cate_1='.$seekData['pro_cate_1'];
        }
        if($seekData['pro_cate_2']){
            $where .= ' and a.pro_cate_2='.$seekData['pro_cate_2'];
        }
        if($seekData['keyword']){
            $where .= ' and (a.proName like "%'.$seekData['keyword'].'%" or a.proId like "%'.$seekData['keyword'].'%")';
        }
        $list = $this->table('product_list as a')
                ->field('a.*,b.name as oneTypeName,c.name as twoTypeName')
                ->where($where)
                ->order('a.refresh_time desc,a.create_time desc')
                ->jion('left join product_category as b on b.id=a.pro_cate_1 left join product_category as c on c.id=a.pro_cate_2')
                ->get();

        return $list;
    }

    /**
     * 获取所有的产品分类的一级分类
     */
    public function getOneType(){
        $one = $this->table('product_category')->where('level=1')->order('vid asc')->get();
        return $one;
    }

    /**
     * @param $id  一级分类的ID
     * 获取所有对应一级的二级分类
     */
    public function getTwoType($id){
        $two = $this->table('product_category')->where('level=2 and pid='.$id)->order('vid asc')->get();
        return $two;
    }

    /**
     * @param $proId        产品id
     * @param $companyId    厂家id
     */
    public function getOneProduct($proId,$companyId){

        $data = $this->table('product_list as a')
                ->field('a.*,b.name as twoTypeName')
                ->where('a.proId='.$proId.' and a.firms_id='.$companyId)
                ->jion('left join product_category as b on b.id=a.pro_cate_2')
                ->getOne();
        if($data){
            $cheXiName = explode(',',$data['car_group']);
            $cheXiId   = explode(',',$data['car_group_id']);
            if($cheXiName && $cheXiId){
                $k = 0;
                for($i=0; $i<count($cheXiName); ++$i){
                    $typeData[$k]['name'] = $cheXiName[$i];
                    $typeData[$k]['id']   = $cheXiId[$i];
                    $k += 1;
                }
            }
            $data['cheXiData'] = $typeData;
        }
        return $data;
    }

    /**
     * @param $proId    产品ID
     * @param $status   产品上架状态
     */
    public function proStatus($proId,$status,$companyId){
        $pro_status = 1;
        if($status==1){
            $pro_status = 2;
        }
        $rst = $this->table('product_list')->where('proId='.$proId.' and firms_id='.$companyId)->update(array('pro_status'=>$pro_status));
        return $rst;
    }

    /**
     * @param $proId        产品ID
     * @param $companyId    商家ID
     */
    public function delProduct($proId,$companyId){
        $rst = $this->table('product_list')->where('proId='.$proId.' and firms_id='.$companyId)->update(array('is_delete'=>1));
        return $rst;
    }

    /**
     * @param $proId        产品ID
     * @param $companyId    商家Id
     */
    public function refreshProduct($proId,$companyId){
        $refresh_time = date("Y-m-d H:i:s");
        $sql = 'update product_list set pro_refresh=pro_refresh+1,refresh_time="'.$refresh_time.'" where proId='.$proId.' and firms_id='.$companyId;
        $return = 0;
        $rst = $this->query($sql);
        if($rst > 0){
            $productName = $this->table('product_list')->field('proName,id')->where('proId='.$proId.' and firms_id='.$companyId)->getOne();
            $data['type']   = 3;          //刷新点消费
            $data['status'] = 1;          //充值成功
            $data['info']   = $productName['proName']; //详情
            $data['payway'] = 4;          //刷新产品
            $data['refresh_point'] = 1;   //刷新点数
            $data['firms_id'] = $companyId;//厂商ID
            $data['money']    = 0;         //充值金额
            $data['admin_id'] = 0;         //管理员ID（没有为0）
            $data['create_time'] = date("Y-m-d H:i:s");
            $data['refrsh_prduct_id'] = $productName['id'];
            $result = $this->table('pay_history')->insert($data);
            $sqli = 'update firms set refresh_point=refresh_point-1 where id='.$companyId;
            $this->query($sqli);
            if($result>0){
                $return = 1;
            }
        }
        return $rst;
    }

    /**
     * @param $id   产品ID
     * 返回产品详情数据，及对应经销商数据
     */
    public function getProductInfo($id){
        $product = $this->table('product_list as a')
                    ->where('a.proId='.$id)
                    ->field('a.*,b.name as twoTypeName,c.name as oneTypeName')
                    ->jion('left join product_category as b on b.id=a.pro_cate_2 left join product_category as c on c.id=a.pro_cate_1')
                    ->getOne();
        if($product){
            $filed = 'a.companyname,a.vip_time,a.id,a.EnterpriseID,a.uname,a.classification,a.city,a.district,a.linkMan,a.linkPhone,a.qq,a.wechat_pic,b.licence_pic,b.taxes_pic,b.field_pic,b.brand_pic,b.agents_pic,a.head_pic,a.is_vip,a.is_check';
            $companyInfo = $this->table('firms as a')
                ->where('a.id='.$product['firms_id'])
                ->field($filed)
                ->jion('left join firms_check as b on b.id=a.id')
                ->getOne();
            if($companyInfo['qq']){
                $companyInfo['qq'] = explode(',',$companyInfo['qq']);
            }
            $product['company'] = $companyInfo;
        }

        return $product;
    }

    /**
     * @param $firmId   企业id
     * 返回企业(营业执照、纳税认证。。)认证信息
     */
    public function approveInfo($firmId){
        $renZheng = $this->table('firms_check')->where('firms_id='.$firmId)->order('id desc')->getOne();
        return $renZheng;
    }

    /**
     * @param $firmId   商家ID
     * @return mixed
     */
    public function approveSuccessInfo($firmId){
        $renZheng = $this->table('firms_check')->where('firms_id='.$firmId.' and status=2')->order('id desc')->getOne();
        return $renZheng;
    }

    /**
     * @param $firmId   商家ID
     * @return mixed
     */
    public function approveIngInfo($firmId){
        $renZheng = $this->table('firms_check')->where('firms_id='.$firmId.' and status=1')->getOne();
        return $renZheng;
    }

    /**
     * @param $data 发起审核数据
     * 添加到商家认证表
     */
    public function approveing($data){
        $rst = $this->table('firms_check')->insert($data);
        if($rst<1 || !$rst){
            $rst = 0;
        }
        return $rst;
    }

    /**
     * @param $business 二级分类ID ,分割
     */
    public function getCarGroup($business){
        $business = substr($business,1,strlen($business)-2);
        $er = $this->table('car_group')->where('id in ('.$business.')')->get();
        $pid = '';
        for($i=0; $i<count($er); ++$i){
            $pid .= $er[$i]['pid'];
            if($i < count($er)-1){
                $pid .= ',';
            }
        }
        $one = $this->table('car_group')->where('id in ('.$pid.')')->get();
        for($i=0; $i<count($er); ++$i){
            for($j=0; $j<count($one); ++$j){
                if($er[$i]['pid'] == $one[$j]['id']){
                    $er[$i]['pName'] = $one[$j]['name'];
                }
            }
        }
        $three = $this->table('car_group')->where('pid in ('.$business.')')->get();
        if($three){
            $three_ids = '';
            for($i=0; $i<count($three); ++$i){
                $three_ids .= $three[$i]['id'];
                if($i<count($three)-1){
                    $three_ids .= ',';
                }
            }
            $four = $this->table('car_group')->where('pid in ('.$three_ids.')')->get();
            if($four){
                $k = 0;
                for($i=0; $i<count($three); ++$i){
                    for($j=0; $j<count($four); ++$j){
                        if($three[$i]['id'] == $four[$j]['pid']){
                            $three[$i]['four'][$k] = $four[$j];
                            $k += 1;
                        }
                    }
                }
            }
            $l = 0;
            for($i=0; $i<count($er); ++$i){
                for($j=0; $j<count($three); ++$j){
                    if($er[$i]['id'] == $three[$j]['pid']){
                        $er[$i]['three'][$l] = $three[$j];
                        $l += 1;
                    }
                }
            }
        }
//        dump($er);
        return $er;
    }

    /**
     * @param $data         求购数据
     * @param $companyId    商家id
     * 添加求购数据
     */
    public function insertShop($data,$companyId){
        $firm = $this->table('firms')->where(array('id'=>$companyId))-getOne();
        //添加到want_buy表数据
        $buy = [];
        $buy['bID']           = $this->getUniqBuy(time());
        $buy['firms_id']     = $companyId;
        $buy['car_group_id'] = $data['car_group_id'];
        $buy['frame_number'] = $data['frame_number'];
        $buy['limitation']   = $data['limitation'];
        $buy['vin_pic']      = $data['vin_pic'];
        $buy['memo']         = $data['memo'];
        $buy['create_time'] = date("Y-m-d H:i:s");
        $buy['status']       = 1;
        $buy['is_delete']    = 0;
        $add = $this->table('want_buy')->insert($buy);
        if($add>0 && $add){
            //添加到want_buy_pic表数据
            if($data['want_buy_pic']){
                $want_buy_pic = explode(',',$data['want_buy_pic']);
                if($want_buy_pic && count($want_buy_pic)>0){
                    for($i=0; $i<count($want_buy_pic); ++$i){
                        $pic = [];
                        $pic['want_buy_id'] = $add;
                        $pic['pic_url']     = $want_buy_pic[$i];
                        $addPic = $this->table('want_buy_pic')->insert($pic);
                        if($addPic>0 && $addPic){}else{
                            return array('status'=>0,'msg'=>'操作失败');
                        }
                    }
                }
            }

            //添加到采购清单表
            $want_buy_list = explode('#want_list#',$data['want_buy_list']);
            if($want_buy_list && count($want_buy_list)>0){
                for($i=0; $i<count($want_buy_list); ++$i){
                    $buy_list = [];
                    $buy_list['want_buy_id'] = $add;
                    $buy_list['pro_cate1'] = explode('|want_list|',$want_buy_list[$i])[0];
                    $buy_list['pro_cate2'] = explode('|want_list|',$want_buy_list[$i])[1];
                    $buy_list['amount']    = explode('|want_list|',$want_buy_list[$i])[2];
                    $buy_list['list_memo'] = explode('|want_list|',$want_buy_list[$i])[3];
                    $addBuyList = $this->table('want_buy_list')->insert($buy_list);
                    if($addBuyList < 1){
                        return array('status'=>0,'msg'=>'操作失败');
                    }
                }
            }
        }
        if($data['car_group_id']){
           $groupType = $this->table('car_group')->where('id='.$data['car_group_id'])->getOne()['type'];
            if($groupType){
                model('web.msg')->toSaveMsg(3,$add,'你有一条求购信息',$groupType,0,$firm['city']);
            }
        }
        return array('status'=>1);
    }

    /**
     * @param $data         求购数据
     * @param $companyId    商家id
     * 修改求购数据
     */
    public function insertShopEidt($data,$companyId){
        $firm = $this->table('firms')->where(array('id'=>$companyId))-getOne();
        //添加到want_buy表数据
        $buy = [];
        $buy['car_group_id'] = $data['car_group_id'];
        $buy['frame_number'] = $data['frame_number'];
        $buy['limitation']   = $data['limitation'];
        $buy['vin_pic']      = $data['vin_pic'];
        $buy['memo']         = $data['memo'];
        $buy['update_time'] = date("Y-m-d H:i:s");
        $buy['status']       = 1;
        $buy['is_delete']    = 0;

        $add = $this->table('want_buy')->where('id='.$data['id'].' and firms_id='.$companyId)->update($buy);
        if($add>0 && $add){
            $add = $data['id'];
            //删除want_buy_pic表对应数据
            $this->table('want_buy_pic')->where('want_buy_id='.$add)->del();
            //添加到want_buy_pic表数据
            if($data['want_buy_pic']){
                $want_buy_pic = explode(',',$data['want_buy_pic']);
                if($want_buy_pic && count($want_buy_pic)>0){
                    for($i=0; $i<count($want_buy_pic); ++$i){
                        $pic = [];
                        $pic['want_buy_id'] = $add;
                        $pic['pic_url']     = $want_buy_pic[$i];
                        $addPic = $this->table('want_buy_pic')->insert($pic);
                        if($addPic>0 && $addPic){}else{
                            return array('status'=>0,'msg'=>'操作失败');
                        }
                    }
                }
            }

            //添加到采购清单表
            $want_buy_list = explode('#want_list#',$data['want_buy_list']);
            if($want_buy_list && count($want_buy_list)>0){
                //删除表对应数据
                $this->table('want_buy_list')->where('want_buy_id='.$add)->del();
                for($i=0; $i<count($want_buy_list); ++$i){
                    $buy_list = [];
                    $buy_list['want_buy_id'] = $add;
                    $buy_list['pro_cate1'] = explode('|want_list|',$want_buy_list[$i])[0];
                    $buy_list['pro_cate2'] = explode('|want_list|',$want_buy_list[$i])[1];
                    $buy_list['amount']    = explode('|want_list|',$want_buy_list[$i])[2];
                    $buy_list['list_memo'] = explode('|want_list|',$want_buy_list[$i])[3];
                    $addBuyList = $this->table('want_buy_list')->insert($buy_list);
                    if($addBuyList < 1){
                        return array('status'=>0,'msg'=>'操作失败');
                    }
                }
            }
        }
        if($data['car_group_id']){
            $groupType = $this->table('car_group')->where('id='.$data['car_group_id'])->getOne()['type'];
            if($groupType){
                model('web.msg')->toSaveMsg(3,$add,'你有一条求购信息',$groupType,0,$firm['city']);
            }
        }
        return array('status'=>1);
    }

    /**
     * @param $companyId    商家id
     * 查询本商家求购中的信息
     */
    public function shoping($companyId,$history='',$page=1,$pageSize=10){
        $p = ($page-1)*$pageSize;
        $return = '';
        $this->updatePuyStatus($companyId);
        if($history){
            //求购历史
            $buy = $this->table('want_buy as a')
                ->where('a.firms_id='.$companyId.' and a.is_delete !=1')
                ->field('a.*,b.name as fourName,b.id as fourId,b.pid as fourPid')
                ->jion('left join car_group as b on b.id=a.car_group_id')
                ->order('create_time desc')
                ->limit($p,$pageSize)
                ->get();
            $count = $this->table('want_buy')->where('firms_id='.$companyId.' and is_delete !=1')->count();
        }else{
            $buy = $this->table('want_buy as a')
                ->where('a.firms_id='.$companyId.' and a.status=1 and a.is_delete !=1')
                ->field('a.*,b.name as fourName,b.id as fourId,b.pid as fourPid')
                ->jion('left join car_group as b on b.id=a.car_group_id')
                ->order('create_time desc')
                ->limit($p,$pageSize)
                ->get();
            $count = $this->table('want_buy')->where('firms_id='.$companyId.' and status=1 and is_delete !=1')->count();
        }
        if($buy){
            //求购数量
            $fourPid = [];
            for($i=0; $i<count($buy); ++$i){
                $list = '';
                $num  = 0;
                $list = $this->table('want_buy_list')->where('want_buy_id='.$buy[$i]['id'])->get();
                if($list){
                    for($j=0; $j<count($list); ++$j){
                        $num += $list[$j]['amount'];
                    }
                }
                $buy[$i]['num'] = $num;
                array_push($fourPid,$buy[$i]['fourPid']) ;
            }
            $fourPid = join(',',$fourPid);
            $three  = $this->table('car_group')->where('id in ('.$fourPid.') and level=3')->get();
            if($three){
                $threePid = [];
                for($i=0; $i<count($three); ++$i){
                    array_push($threePid,$three[$i]['pid']);
                    for($j=0; $j<count($buy); ++$j){
                        if($three[$i]['id'] == $buy[$j]['fourPid']){
                            $buy[$j]['threeId']  = $three[$i]['id'];
                            $buy[$j]['threePid'] = $three[$i]['pid'];
                            $buy[$j]['threeName']= $three[$i]['name'];
                        }
                    }
                }
                $threePid = join(',',$threePid);
                $two  = $this->table('car_group')->where('id in ('.$threePid.') and level=2')->get();
                if($two){
                    $towPid = [];
                    for($i=0; $i<count($two); ++$i){
                        array_push($towPid,$two[$i]['pid']);
                        for($j=0; $j<count($buy); ++$j){
                            if($two[$i]['id'] == $buy[$j]['threePid']){
                                $buy[$j]['twoId']  = $two[$i]['id'];
                                $buy[$j]['twoPid'] = $two[$i]['pid'];
                                $buy[$j]['twoName'] = $two[$i]['name'];
                            }
                        }
                    }
                }
                $towPid = join(',',$towPid);
                $one = $this->table('car_group')->where('id in ('.$towPid.') and level=1')->get();
                if($one){
                    for($i=0; $i<count($one); ++$i){
                        for($j=0; $j<count($buy); ++$j){
                            if($one[$i]['id'] == $buy[$j]['twoPid']){
                                $buy[$j]['oneId']   = $one[$i]['id'];
                                $buy[$j]['onePid']  = $one[$i]['pid'];
                                $buy[$j]['oneName'] = $one[$i]['name'];
                            }
                        }
                    }
                    $return['list']      = $buy;
                    $return['count']     = $count;
                    $return['page']      = $page;
                    $return['pageSize']  = $pageSize;
                }
            }
        }
        return $return;
    }

    /**
     * @param $companyId    商家id
     * 修改当前商家求购超过时效的状态值
     */
    public function updatePuyStatus($companyId){
        $d = $this->table('want_buy')->where('firms_id='.$companyId.' and status=1')->get();
        if($d){
            $nowDate = date("Y-m-d H:i:s");
            for($i=0; $i<count($d); ++$i){
                $shopDate =  date('Y-m-d H:i:s',strtotime('-'.$d[$i]['limitation'].' day'));
                if($d[$i]['update_time']){
                    if($d[$i]['update_time'] < $shopDate){
                        $this->table('want_buy')->where('id='.$d[$i]['id'])->update(array('status'=>2));
                    }
                }else{
                    if($d[$i]['create_time'] < $shopDate){
                        $this->table('want_buy')->where('id='.$d[$i]['id'])->update(array('status'=>2));
                    }
                }
           }
        }
    }

    /**
     * @param $id   求购主表id
     * 返回对应id的详细信息,如果没有id,返回最新的一条求购详情
     */
    public function shopingInfo($companyId,$id){
        $data = '';
        if($id){
            $data = $this->table('want_buy')->where('bID='.$id.' and firms_id='.$companyId)->getOne();
        }else{
            //返回最新一条求购详情
            $data = $this->table('want_buy')->where('firms_id='.$companyId)->group('create_time desc')->getOne();
        }

        if($data){
            $oneTwoThreeFour = $this->getOneTwoThreeFourByFour($data['car_group_id']);
            $data['cheXi'] = $oneTwoThreeFour;
            $buyList = $this->table('want_buy_list')->where('want_buy_id='.$data['id'])->get();
            if($buyList){
                $pro_cate1 = [];
                $pro_cate2 = [];
                for($i=0; $i<count($buyList); ++$i){
                    array_push($pro_cate1,$buyList[$i]['pro_cate1']);
                    array_push($pro_cate2,$buyList[$i]['pro_cate2']);
                }
                $pro_cate1 = join($pro_cate1,',');
                $pro_cate2 = join($pro_cate2,',');
                $product_category = $this->table('product_category')->where('id in ('.$pro_cate1.') or id in ('.$pro_cate2.')')->get();
                if($product_category){
                    for($i=0; $i<count($product_category); ++$i){
                        for($j=0; $j<count($buyList); ++$j){
                            if($buyList[$j]['pro_cate1'] == $product_category[$i]['id']){
                                $buyList[$j]['pro_cate1Name'] = $product_category[$i]['name'];
                            }
                            if($buyList[$j]['pro_cate2'] == $product_category[$i]['id']){
                                $buyList[$j]['pro_cate2Name'] = $product_category[$i]['name'];
                            }
                        }
                    }
                }
            }
            $buyPic  = $this->table('want_buy_pic')->where('want_buy_id='.$data['id'])->get();
            $data['list'] = $buyList;
            $data['pic']  = $buyPic;
        }
        return $data;
    }

    /**
     * @param $foruId   车系四级id
     * 返回1-4级id及名称
     */
    public function getOneTwoThreeFourByFour($foruId){
        $filed = 'a.id as fourId,a.type,a.pid as fourPid,a.name as fourName,b.id as threeId,b.pid as threePid,b.name as threeName,c.id as twoId,c.pid as twoPid,c.name as twoName';
        $data  = $this->table('car_group as a')
                ->where('a.id='.$foruId)
                ->field($filed)
                ->jion('left join car_group as b on b.id=a.pid left join car_group as c on c.id=b.pid')
                ->getOne();
        if($data){
            $one = $this->table('car_group')->where('id='.$data['twoPid'])->getOne();
            $data['oneId']  = $one['id'];
            $data['oneName']= $one['name'];
        }
        return $data;
    }

    /**
     * @param $id           求购产品id
     * @param $companyId    商家id
     */
    public function shopXiaJia($id,$companyId){
        $rst = $this->table('want_buy')->where('id='.$id.' and firms_id='.$companyId)->update(array('status'=>2));
        return $rst;
    }

    /**
     * @param $id           求购产品id
     * @param $companyId    商家id
     */
    public function delShop($id,$companyId){
        $rst = $this->table('want_buy')->where('id='.$id.' and firms_id='.$companyId)->update(array('status'=>2,'is_delete'=>1));
        return $rst;
    }

    /**
     * 导出采购清单的excel
     */
    public function downExcel($id){
        require './lib/phpexcel1.7.6/PHPExcel/PHPExcel.php';
        $excel = new PHPExcel();
        //Excel表格式,这里简略写了8列
        $letter = array('A','B','C','D','E','F','F','G');
        //表头数组
        $tableheader = array('序号','配件类型','数量','备注');
        //填充表头信息
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }
        //Set column widths 设置列宽度
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(150);
        //所有单元格居中对齐
        $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //表格数组
        $data = $this->table('want_buy_list')->where('want_buy_id='.$id)->get();
        if($data){
            $pro_cate1Id = [];
            $pro_cate2Id = [];
            for($i=0; $i<count($data); ++$i){
                array_push($pro_cate1Id,$data[$i]['pro_cate1']);
                array_push($pro_cate2Id,$data[$i]['pro_cate2']);
            }
            $pro_cate1Id = join(',',$pro_cate1Id);
            $pro_cate2Id = join(',',$pro_cate2Id);
            $pro_cateData = $this->table('product_category')->where('id in ('.$pro_cate1Id.') or id in ('.$pro_cate2Id.')')->get();
            $dataArr = array();
            if($pro_cateData){
                for($i=0; $i<count($data); ++$i){
                    for($j=0; $j<count($pro_cateData); ++$j){
                        if($data[$i]['pro_cate1'] == $pro_cateData[$j]['id']){
                            $data[$i]['pro_cate1Name'] = $pro_cateData[$j]['name'];
                        }
                        if($data[$i]['pro_cate2'] == $pro_cateData[$j]['id']){
                            $data[$i]['pro_cate2Name'] = $pro_cateData[$j]['name'];
                        }
                    }
                    $dataArr[$i]['id']        = $data[$i]['id'];
                    $dataArr[$i]['type']      = $data[$i]['pro_cate1Name'].'/'.$data[$i]['pro_cate2Name'];
                    $dataArr[$i]['amount']    = $data[$i]['amount'];
                    $dataArr[$i]['list_memo'] = $data[$i]['list_memo'];
                }
            }
        }
//        $data = array(
//            array('1','小王','男','20','100'),
//            array('2','小李','男','20','101'),
//            array('3','小张','女','20','102'),
//            array('4','小赵','女','20','103')
//        );
        //填充表格信息
        for ($i = 2;$i <= count($dataArr) + 1;$i++) {
            $j = 0;
            foreach ($dataArr[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }
        //创建Excel输入对象
        $write = new PHPExcel_Writer_Excel5($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        $filename = iconv("utf-8","gb2312",'采购清单.xls');
        header('Content-Disposition:attachment;filename='.$filename);
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }

    /**
     * @param $time 当前时间搓
     * 生成求购表唯一产品ID
     */
    public function getUniqBuy($time){
        $t = substr($time , 4 , 6);
        $s = substr(strval(rand(10000,19999)),1,4);
        $code = $s.$t;
        $proIds = $this->table('want_buy')->where('bID="'.$code.'"')->getOne();
        if($proIds){
            $this->getUniq($time);
        }else{
            return $code;
        }
    }

    /**
     * @param $id   求购主表id
     * 返回对应id的详细信息,如果没有id,返回最新的一条求购详情
     */
    public function buyInfo($id){
        $data = '';
        $data = $this->table('want_buy')->where('bID='.$id)->getOne();
        if($data){
            $oneTwoThreeFour = $this->getOneTwoThreeFourByFour($data['car_group_id']);
            $data['cheXi'] = $oneTwoThreeFour;
            $buyList = $this->table('want_buy_list')->where('want_buy_id='.$data['id'])->get();
            if($buyList){
                $pro_cate1 = [];
                $pro_cate2 = [];
                for($i=0; $i<count($buyList); ++$i){
                    array_push($pro_cate1,$buyList[$i]['pro_cate1']);
                    array_push($pro_cate2,$buyList[$i]['pro_cate2']);
                }
                $pro_cate1 = join($pro_cate1,',');
                $pro_cate2 = join($pro_cate2,',');
                $product_category = $this->table('product_category')->where('id in ('.$pro_cate1.') or id in ('.$pro_cate2.')')->get();
                if($product_category){
                    for($i=0; $i<count($product_category); ++$i){
                        for($j=0; $j<count($buyList); ++$j){
                            if($buyList[$j]['pro_cate1'] == $product_category[$i]['id']){
                                $buyList[$j]['pro_cate1Name'] = $product_category[$i]['name'];
                            }
                            if($buyList[$j]['pro_cate2'] == $product_category[$i]['id']){
                                $buyList[$j]['pro_cate2Name'] = $product_category[$i]['name'];
                            }
                        }
                    }
                }
            }
            $buyPic  = $this->table('want_buy_pic')->where('want_buy_id='.$data['id'])->get();
            $data['list'] = $buyList;
            $data['pic']  = $buyPic;
        }
        return $data;
    }

    /**
     * @param $ids          经营范围的四级id
     * @param $companyId    企业id
     */
    public function save_four_ids($ids,$companyId){
        $rst = $this->table('firms')->where('id='.$companyId)->update(array('four_ids'=>$ids));
        return $rst;
    }

    /**
     * 根据品牌获取车系
     * @param $brand 品牌
     * @param $salesVersion 车系
     * @param $typeName 商家类别
     */
    public function piPeiVin($brand,$salesVersion,$type){
        $salesVersion = explode(',',$salesVersion);
        $whereName    = ' and (';
        for($i=0; $i<count($salesVersion); ++$i){
            $whereName .= '`name` like "%'.$salesVersion[$i].'%"';
            if($i < count($salesVersion)-1){
                $whereName .= ' or ';
            }
        }
        $whereName .= ')';
        //查询是否有类似车系
        $salesVersionBy = $this->table('car_group')->where("`type`=".$type.$whereName." and level=4")->get();
        $likeF = '';//四级车系
        if(!empty($salesVersionBy)){//如果有该车系  判断是否有该车系在该品牌下面
            foreach($salesVersionBy as $k=>$item){//获取三级id
                if(in_array($item['name'],$salesVersion)){//车系名称是否有相同的
                    $likeF = $item['id'];
                }

            }
            //没有相同的 获取第一个
            if($likeF==''){
                $likeF = $salesVersionBy[0]['id'];
            }
        }else{//通过车系未获取到 直接获取品牌
            $brand = explode(',',$brand);
            $whereBrand = ' and (';
            for($i=0; $i<count($brand); ++$i){
                $whereBrand .= 'a.`name` like "%'.$brand[$i].'%"';
                if($i < count($brand)-1){
                    $whereBrand .= ' or ';
                }
            }
            $whereBrand .= ')';
            $brandBy = $this->table('car_group a')->field("c.id as fId")->where("a.`type`=".$type.$whereBrand." and a.level=2")->jion("inner join car_group b on a.id=b.pid inner join car_group c on b.id=c.pid")->getOne();
            if($brandBy){
                $likeF = $brandBy['fId'];
            }
        }
        $res = '';
        if($likeF){//获取到四级
            $res = $this->getOneTwoThreeFourByFour($likeF);
        }
        if($res){//获取到数据
            return array('status'=>200,'msg'=>'获取车系成功','data'=>$res);
        }else{
            return array('status'=>201,'msg'=>'系统暂无对应车系，请手动选择车系');
        }

    }

    /**
     * 导出vin excel
     */
    public function vinDownExcel($data)
    {
        require './lib/phpexcel1.7.6/PHPExcel/PHPExcel.php';
        $excel = new PHPExcel();
        //Excel表格式,这里简略写了8列
        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        //表头数组
        $tableheader = array('厂家', '品牌', '车型', '销售版本','厂商指导价','生产年份','停产年份','排放标准','车型代码','底盘号','国产合资进口类型','发动机型号','进气形式','排量','最大功率','最大马力','驱动形式','变速器类型','变速器描述','档位数','车身型式','车门数','气缸排列形式','气缸数','前制动类型','后制动类型');
        //填充表头信息
        for ($i = 0; $i < count($tableheader); $i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1", "$tableheader[$i]");
        }
        //Set column widths 设置列宽度
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
        //所有单元格居中对齐
        $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //表格数组
        if ($data) {
            for ($i = 0; $i < count($data); ++$i) {
                $dataArr[$i][0] = $data[$i]['Manufacturers'];
                $dataArr[$i][1] = $data[$i]['Brand'];
                $dataArr[$i][2] = $data[$i]['Models'];
                $dataArr[$i][3] = $data[$i]['SalesVersion'];
                $dataArr[$i][4] = $data[$i]['GuidingPrice'];
                $dataArr[$i][5] = $data[$i]['ProducedYear'];
                $dataArr[$i][6] = $data[$i]['IdlingYear'];
                $dataArr[$i][7] = $data[$i]['EmissionStandard'];
                $dataArr[$i][8] = $data[$i]['ModelCode'];
                $dataArr[$i][9] = $data[$i]['ChassisCode'];
                $dataArr[$i][10] = $data[$i]['VehicleAttributes'];
                $dataArr[$i][11] = $data[$i]['EngineModel'];
                $dataArr[$i][12] = $data[$i]['Induction'];
                $dataArr[$i][13] = $data[$i]['Displacement'];
                $dataArr[$i][14] = $data[$i]['PowerKw'];
                $dataArr[$i][15] = $data[$i]['Horsepower'];
                $dataArr[$i][16] = $data[$i]['DriveModel'];
                $dataArr[$i][17] = $data[$i]['TransmissionType'];
                $dataArr[$i][18] = $data[$i]['TransmissionDescription'];
                $dataArr[$i][19] = $data[$i]['GearNumber'];
                $dataArr[$i][20] = $data[$i]['BodyType'];
                $dataArr[$i][21] = $data[$i]['Doors'];
                $dataArr[$i][22] = $data[$i]['CylinderArrangement'];
                $dataArr[$i][23] = $data[$i]['Cylinders'];
                $dataArr[$i][24] = $data[$i]['FrontBrake'];
                $dataArr[$i][25] = $data[$i]['RearBrake'];
            }
//        $data = array(
//            array('1','小王','男','20','100'),
//            array('2','小李','男','20','101'),
//            array('3','小张','女','20','102'),
//            array('4','小赵','女','20','103')
//        );
            //填充表格信息
            for ($i = 2; $i <= count($dataArr) + 1; $i++) {
                $j = 0;
                foreach ($dataArr[$i - 2] as $key => $value) {
                    $excel->getActiveSheet()->setCellValue("$letter[$j]$i", "$value");
                    $j++;
                }
            }
            //创建Excel输入对象
            $write = new PHPExcel_Writer_Excel5($excel);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/vnd.ms-execl");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");
            $filename = iconv("utf-8", "gb2312", 'VIN车架数据.xls');
            header('Content-Disposition:attachment;filename=' . $filename);
            header("Content-Transfer-Encoding:binary");
            $write->save('php://output');
        }
    }
}