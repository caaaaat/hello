<?php
/**
 * 我的求购
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/7
 * Time: 11:36
 */

class ApiSevPurchaseModel extends Model{

    /**
     * 获取我的求购
     * @param $userId
     * @param $purchaseType
     * @param $p
     * @param $pageSize
     * @return array
     */
    public function getPurChaseList($userId,$purchaseType,$p,$pageSize){
        //下架超出时效求购
        $this->updatePuyStatus($userId);

        $page = (intval($p)-1)*$pageSize;

        $purchaseType = $purchaseType==1?$purchaseType:2;

        $find = "a.is_delete=0 and a.status=$purchaseType and a.firms_id=$userId";

        $join = "left join firms b on a.firms_id=b.id";

        $filed = "a.*,b.companyname,b.face_pic";

        $list  = $this->table('want_buy a')->field($filed)->where($find)->jion($join)->limit($page,$pageSize)->order('a.create_time desc,a.limitation desc')->get();
        $count = $this->table('want_buy a')->where($find)->count();

        //预处理数据
        foreach ($list as $k=>$item){

            //获取求购图片
            $wantId  = $item['id'];
            $wantPic = $this->table('want_buy_pic')->where(array('want_buy_id'=>$wantId))->get();

            //获取四级车系
            $foruId = $item['car_group_id'];

            $indexMo = model('api.sev.index','mysql');
            $list[$k]['groups'] = $indexMo->getOneTwoThreeFourByFour($foruId);

            //获取配件数
            $wantList = $this->table('want_buy_list')->field('SUM(amount) as amount')->where(array('want_buy_id'=>$wantId))->group('want_buy_id')->getOne();


            $list[$k]['wantPic'] = $wantPic;
            $list[$k]['wantNum'] = $wantList['amount'];
        }

        $res['purchaseType'] = $purchaseType;
        $res['list'] = $list;

        return $data = array('list'=>$res,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);

    }

    /**
     * 获取求购详情
     * @param $userId
     * @param $wantId
     * @return array
     */
    public function getWantDetail($userId,$wantId){


        $find = "a.is_delete=0 and a.id=$wantId and a.firms_id=$userId";

        $join = "left join firms b on a.firms_id=b.id";

        $filed = "a.*,b.companyname,b.face_pic,b.address,b.province,b.city,b.district,b.is_vip,b.is_check,b.linkPhone,b.qq,b.type as firmType,b.wechat_pic";

        $res  = $this->table('want_buy a')->field($filed)->where($find)->jion($join)->getOne();

        if($res){

            //地址
            $city = ($res['province']==$res['city'])?'':$res['city'];
            $district = ($res['district']==$res['city'])?'':$res['district'];
            $res['address']     = $res['province'].$city.$district.$res['address'];
            //获取求购图片
            $wantPic = $this->table('want_buy_pic')->where(array('want_buy_id'=>$wantId))->get();

            $res['wantPic'] = $wantPic;
            //获取四级车系
            $foruId = $res['car_group_id'];
            $res['groups'] = $this->getOneTwoThreeFourByFour($foruId);
            $type =  isset($res['groups']['carType'])?$res['groups']['carType']:1;
            //获取商家分类
            $carStr = array(1=>'轿车商家',2=>'货车商家',3=>'用品商家');
            $res['carType'] = $carStr[$type];
            //获取配件数
            $filed1 = "a.*,b.name as cate_1_name,c.name as cate_2_name";
            $join1 = "left join product_category b on a.pro_cate1=b.id left join product_category c on a.pro_cate2=c.id";

            $res['wantList'] = $this->table('want_buy_list a')->field($filed1)->jion($join1)->where(array('a.want_buy_id'=>$wantId))->get();

            $return = array('data'=>$res,'status'=>'200','msg'=>'获取成功');

        }else{

            $return = array('data'=>$res,'status'=>'201','msg'=>'求购不存在或已删除');

        }

        return $return;


    }


    /**
     * 产品下架
     * @param $userId
     * @param $purchaseId
     * @return array
     */
    public function offSale($userId,$purchaseId){

        //查询该产品
        $want = $this->table('want_buy')->where("firms_id=$userId and id=$purchaseId and status=1")->getOne();
        if($want){
            //查询到该产品
            $res = $this->table('want_buy')->where("id=$purchaseId")->update(array('status'=>2));
            $return = array('status'=>'200','msg'=>'下架成功');
        }else{//未查询到产品
            $return = array('status'=>'200','msg'=>'该求购不存在，或已下架');
        }
        return $return;
    }


    /**
     * 产品删除
     * @param $userId
     * @param $purchaseId
     * @return array
     */
    public function delSale($userId,$purchaseId){

        //查询该产品
        $want = $this->table('want_buy')->where("firms_id=$userId and id=$purchaseId and is_delete=0")->getOne();
        if($want){
            //查询到该产品
            $res = $this->table('want_buy')->where("id=$purchaseId")->update(array('status'=>2,'is_delete'=>1));
            $return = array('status'=>'200','msg'=>'删除成功');
        }else{//未查询到产品
            $return = array('status'=>'200','msg'=>'该求购不存在，或已删除');
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
                if($d[$i]['create_time'] < $shopDate){
                    $this->table('want_buy')->where('id='.$d[$i]['id'])->update(array('status'=>2));
                }
            }
        }
    }


    /**
     * 添加求购数据
     * @param $car_group_id
     * @param $frame_number
     * @param $limitation
     * @param $vin_pic
     * @param $otherp
     * @param $buyArr
     * @param $memo
     * @param $userId
     * @return array
     */
    public function insertShop($car_group_id,$frame_number,$limitation,$vin_pic,$otherp,$buyArr,$memo,$userId){
        //添加到want_buy表数据
        $buy = [];
        $buy['bID']           = $this->getUniqBuy(time());
        $buy['firms_id']     = $userId;
        $buy['car_group_id'] = $car_group_id;
        $buy['frame_number'] = $frame_number;
        $buy['limitation']   = $limitation;
        $buy['vin_pic']      = $vin_pic;
        $buy['memo']         = $memo;
        $buy['create_time']  = date("Y-m-d H:i:s");
        $buy['status']       = 1;
        $buy['is_delete']    = 0;
        $add = $this->table('want_buy')->insert($buy);
        if($add>0 && $add){
            //添加到want_buy_pic表数据
            $want_buy_pic = $otherp;
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
            //添加到采购清单表
            $want_buy_list = $buyArr;
            if($want_buy_list && count($want_buy_list)>0){
                for($i=0; $i<count($want_buy_list); ++$i){
                    $buy_list = [];
                    $buy_list['want_buy_id'] = $add;
                    $buy_list['pro_cate1'] = $want_buy_list[$i]['pro_cate1'];
                    $buy_list['pro_cate2'] = $want_buy_list[$i]['pro_cate2'];
                    $buy_list['amount']    = $want_buy_list[$i]['amount'];
                    $buy_list['list_memo'] = $want_buy_list[$i]['list_memo'];
                    $addBuyList = $this->table('want_buy_list')->insert($buy_list);
                    if($addBuyList < 1){
                        return array('status'=>0,'msg'=>'操作失败');
                    }
                }
            }

            if($car_group_id){
                //获取厂商地址信息
                $firmInfo =  $this->table('firms')->where('id='.$userId)->getOne();
                $city = $firmInfo['city']?$firmInfo['city']:'0';

                $groupType = $this->table('car_group')->where('id='.$car_group_id)->getOne()['type'];
                if($groupType){
                    model('web.msg')->toSaveMsg(3,$add,'你有一条求购信息',$groupType,0,$city);
                }
            }
        }
        return array('status'=>200,'msg'=>'发布求购成功');
    }



    /**
     * 编辑求购数据
     * @param $car_group_id
     * @param $frame_number
     * @param $limitation
     * @param $vin_pic
     * @param $otherp
     * @param $buyArr
     * @param $memo
     * @param $userId
     * @param $buyId
     * @return array
     */
    public function editPurchase($car_group_id,$frame_number,$limitation,$vin_pic,$otherp,$buyArr,$memo,$userId,$buyId){
        //添加到want_buy表数据
        $buy = [];
        $buy['car_group_id'] = $car_group_id;
        $buy['frame_number'] = $frame_number;
        $buy['limitation']   = $limitation;
        $buy['vin_pic']      = $vin_pic;
        $buy['memo']         = $memo;
        $buy['create_time']  = date("Y-m-d H:i:s");
        $buy['update_time']  = date("Y-m-d H:i:s");
        $buy['status']       = 1;
        $buy['is_delete']    = 0;
        $add = $this->table('want_buy')->where("id=$buyId")->update($buy);
        if($add){

            //删除want_buy_pic表数据
            $this->table('want_buy_pic')->where("want_buy_id=$buyId")->del();

            //添加到want_buy_pic表数据
            $want_buy_pic = $otherp;
            if($want_buy_pic && count($want_buy_pic)>0){
                for($i=0; $i<count($want_buy_pic); ++$i){
                    $pic = [];
                    $pic['want_buy_id'] = $buyId;
                    $pic['pic_url']     = $want_buy_pic[$i];
                    $addPic = $this->table('want_buy_pic')->insert($pic);
                    if($addPic>0 && $addPic){}else{
                        return array('status'=>0,'msg'=>'操作失败');
                    }
                }
            }

            //删除采购清单表
            $this->table('want_buy_list')->where("want_buy_id=$buyId")->del();

            //添加到采购清单表
            $want_buy_list = $buyArr;
            if($want_buy_list && count($want_buy_list)>0){
                for($i=0; $i<count($want_buy_list); ++$i){
                    $buy_list = [];
                    $buy_list['want_buy_id'] = $buyId;
                    $buy_list['pro_cate1'] = $want_buy_list[$i]['pro_cate1'];
                    $buy_list['pro_cate2'] = $want_buy_list[$i]['pro_cate2'];
                    $buy_list['amount']    = $want_buy_list[$i]['amount'];
                    $buy_list['list_memo'] = $want_buy_list[$i]['list_memo'];
                    $addBuyList = $this->table('want_buy_list')->insert($buy_list);
                    if($addBuyList < 1){
                        return array('status'=>0,'msg'=>'操作失败');
                    }
                }
            }
        }
        return array('status'=>200,'msg'=>'发布求购成功');
    }


    /**
     * 生成求购表唯一产品ID
     * @param $time
     * @return string
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
     * 返回1-4级id及名称
     * @param $foruId
     * @return mixed
     */
    public function getOneTwoThreeFourByFour($foruId){
        $filed = 'a.id as fourId,a.type,a.pid as fourPid,a.name as fourName,a.type as carType,b.id as threeId,b.pid as threePid,b.name as threeName,c.id as twoId,c.pid as twoPid,c.name as twoName';
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
     * 通过二级获取三级四级数据
     * @param $cid
     * @return mixed|string
     */
    public function getThreeAndFourByTwo($cid){
        $data = $this->table('car_group')->where('pid='.$cid)->get();

        if($data){
            $ids = [];
            foreach($data as $v){
                array_push($ids,$v['id']);
            }
            $ids = join(',',$ids);
            $fourData = $this->table('car_group')->where('pid in ('.$ids.')')->get();
            if($fourData){
                for($i=0; $i<count($data); ++$i){
                    $data[$i]['child'] = [];
                    $k = 0;
                    for($j=0; $j<count($fourData); ++$j){
                        if($data[$i]['id'] == $fourData[$j]['pid']){
                            $data[$i]['child'][$k] = $fourData[$j];
                            $k += 1;
                        }
                    }
                }
            }
        }

        $return['status'] = 200;
        $return['msg']    = '请求成功';
        $return['data']   = $data;

        return $return;
    }


    /**
     * 获取产品分类一级
     * @return mixed
     */
    public function getProductGroupOne(){
        //获取一级分类
        $data = $this->table('product_category')->where('level=1')->order('vid asc')->get();
        $data['data']  = $data;
        $data['status'] = 200;
        return $data;
    }

    /**
     * 获取产品分类二级
     * @param $pid
     * @return mixed
     */
    public function getProductGroupTwo($pid){
        //获取一级分类
        $data = $this->table('product_category')->where(array('level'=>2,'pid'=>$pid))->order('vid asc')->get();
        $data['data']  = $data;
        $data['status'] = 200;
        return $data;
    }

    /**
     * 根据品牌获取车系
     * @param $brand 品牌
     * @param $salesVersion 车系
     * @param $typeName 商家类别
     */
    public function getCarGroupByVin($brand,$salesVersion,$typeName){

        $type = $typeName=='轿车商家'?1:($typeName=='货车商家'?2:3);//转换商家类型

        //查询是否有类似车系
        $salesVersionBy = $this->table('car_group')->where("`type`=$type and `name` like '%$salesVersion%' and level=4")->get();

        $likeF = '';//四级车系
        if(!empty($salesVersionBy)){//如果有该车系  判断是否有该车系在该品牌下面
            foreach($salesVersionBy as $k=>$item){//获取三级id

                if($item['name']==$salesVersion){//车系名称是否有相同的
                    $likeF = $item['id'];
                }

            }

            //没有相同的 获取第一个
            if($likeF==''){
                $likeF = $salesVersionBy[0]['id'];
            }

        }else{//通过车系未获取到 直接获取品牌

            $brandBy = $this->table('car_group a')->field("c.id as fId")->where("a.`type`=$type and a.`name` like '%$brand%' and a.level=2")->jion("inner join car_group b on a.id=b.pid inner join car_group c on b.id=c.pid")->getOne();
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


}