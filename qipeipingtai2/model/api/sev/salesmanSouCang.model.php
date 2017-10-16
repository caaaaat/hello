<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/3
 * Time: 18:29
 */
class ApiSevSalesmanSouCangModel extends Model
{
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
     * @param $token
     * @param $id       店铺唯一标识id
     */
    public function storeVeiwData($token,$EnterpriseID){
        $filed   = 'companyname,type,uname,refresh_point,is_check,vip_time,EnterpriseID,id,head_pic';
        $company = $this->table('firms')->field($filed)->where('EnterpriseID='.$EnterpriseID)->getOne();
        if($company){
            $company['vip'] = 0;
            if($company['vip_time']){
                if($company['vip_time']>date('Y-m-d h:i:s', time())){
                    $company['vip'] = 1;
                }
                $company['vip_time'] = date('Y-m-d', strtotime($company['vip_time']));
            }
            $company['companyType'] = '';
            if($company['type']==1){
                $company['companyType'] = '经销商';
            }elseif($company['type']==2){
                $company['companyType'] = '汽修厂';
            }
            $company['tokenId'] = authcode($company['id'],'ENCODE');
            $boDaAll = $this->table('firms_call_log')->where('to_firms_id='.$company['id'])->count();
            $fangWen = $this->table('firms_visit_log')->where('to_firms_id='.$company['id'])->count();

            if($boDaAll && $boDaAll>0){
                $boDaAll = $this->countInt($boDaAll);
            }
            if($fangWen && $fangWen>0){
                $fangWen = $this->countInt($fangWen);
            }
            $company['boDa']    = $boDaAll;
            $company['fangWen'] = $fangWen;
            $return = array('status'=>200,'msg'=>'操作成功','data'=>$company);
        }else{
            $return = array('status'=>104,'msg'=>'该厂商不存在');
        }
        return $return;
    }

    /**
     * 获取店铺基础信息
     * @param $userId
     * @return array|mixed
     */
    public function getStoreInfo($userId)
    {
        $field = 'companyname,face_pic,type,classification,address,business,coordinate,longitude,latitude,major,linkMan,linkPhone,linkTel,qq,wechat_pic,info';
        $res = $this->table('firms')->field($field)->where(array('id'=>$userId))->getOne();

        //获取车系数据
        $res['businessStr'] = '';
        if($res['business']){
            $business = trim($res['business'],',');
            $cate07 = $this->table('car_group as a')
                ->field("GROUP_CONCAT(CONCAT(b.`name`,'/',a.`name`) SEPARATOR ' ') as `name`")
                ->jion('LEFT JOIN car_group as b on a.pid = b.id')
                ->where('a.id in ('.$business.') and a.`level` = 2')->get();
            if($cate07){
                $res['businessStr'] = $cate07[0]['name'];
            }
        }
        $res['linkPhoneArr'] = '';
        if($res['linkPhone']){
            $res['linkPhoneArr']  = explode(',',$res['linkPhone']);
        }
        $res['linkTelArr'] = '';
        if($res['linkTel']){
            $res['linkTelArr']    = explode(',',$res['linkTel']);
        }
        $res['qqArr'] = '';
        if($res['qq']){
            $res['qqArr']         = explode(',',$res['qq']);
        }
        if($res['linkPhone']){
            $res['linkPhone']  = str_replace(",","<br>",$res['linkPhone']);
        }
        if($res['linkTel']){
            $res['linkTel']    = str_replace(",","<br>",$res['linkTel']);
        }
        if($res['qq']){
            $res['qq']         = str_replace(",","<br>",$res['qq']);
        }
        $typeArr = array(1=>'经销商',2=>'修理厂');
        $classArr = array(1=>'轿车商家' ,2=>'货车商家', 3=>'用品商家', 4=>'修理厂', 5=>'快修保养', 6=>'美容店');

        $res['typeStr']  = $typeArr[$res['type']];
        $res['classStr'] = $classArr[$res['classification']];
        //获取访问数据
        $res['visit_num'] = $this->table('firms_visit_log')->where(array('to_firms_id'=>$userId))->count();
        $res['visit_num'] = $this->countInt($res['visit_num']);
        //获取来电数据
        $res['call_num']  = $this->table('firms_call_log')->where(array('to_firms_id'=>$userId))->count();
        $res['call_num']  = $this->countInt($res['call_num']);
        //获取该厂商banner
        $res['banners'] = $this->table('firms_banner')->where(array('firms_id'=>$userId))->limit(0,3)->get();
        $res['bannersArr'] = array();
        foreach($res['banners'] as $i=>$bannerItem){
            $res['bannersArr'][$i] = $bannerItem['banner_url'];
        }
        return $res;
    }

    /**
     * 统计支付表刷新点，修改产品当日刷新点
     */
    public function resetRefresh($companyId){
        if($companyId){
            $nowTime = date("Y-m-d 00:00:00");
            $data    = $this->table('pay_history')->field('refrsh_prduct_id,count(refrsh_prduct_id) as num')->where('type=3 and create_time>="'.$nowTime.'" and refrsh_prduct_id is not null and firms_id='.$companyId)->group('refrsh_prduct_id')->get();
            if($data){
                $ids = array();
                foreach($data as $v){
                    array_push($ids,$v['refrsh_prduct_id']);
                    $this->table('product_list')->where('id='.$v['refrsh_prduct_id'])->update(array('pro_refresh'=>$v['num']));
                }
                $ids = join(',',$ids);
                $this->table('product_list')->where('id not in ('.$ids.') and firms_id='.$companyId)->update(array('pro_refresh'=>0));
            }
        }
    }

    /**
     * 获取产品
     * @param $firms_id
     * @param $pro_type
     * @param array $pro_cate_1
     * @param array $pro_cate_2
     * @param $pro_status
     * @param string $keyword
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function getProducts($firms_id,$pro_type,$pro_cate_1=array(),$pro_cate_2=array(),$pro_status,$keyword='',$page=1,$pageSize=10){
        $this->resetRefresh($firms_id);
        $start = ($page-1)*$pageSize;
        $where = 'a.is_delete=0';
        if($firms_id){
            $where .= ' and a.firms_id='.$firms_id;
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
        if($pro_status){
            $where .= ' and a.pro_status="'.$pro_status.'"';
        }
        if($keyword){
            $where .= ' and ( a.proName like "%'.$keyword.'%" or a.car_group like "%'.$keyword.'%" or a.pro_brand like "%'.$keyword.'%" )';
        }

        $count = $this->table('product_list as a')->where($where)->count();
        $data  = $this->table('product_list as a')
            ->field('a.proId,a.proName,a.pro_type,b.name as cate_1_name,c.name as cate_2_name,a.pro_price,a.car_group,a.pro_pic,a.pro_refresh')
            ->jion('left join product_category as b on a.pro_cate_1=b.id left join product_category as c on a.pro_cate_2=c.id')
            ->where($where)->order('pro_refresh desc')->limit($start,$pageSize)->get();
        return $data = array('list'=>$data,'count'=>$count,'status'=>'200','page'=>$page,'pageSize'=>$pageSize);
    }

    /**
     * 访问记录
     * @param $myFirmId
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function getVisitToFirmsLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_visit_log as a')->field('count(DISTINCT to_firms_id) as num')->where(array('a.firms_id'=>$myFirmId,'is_show'=>1))->getOne();
        $res = $this->table('firms_visit_log as a')
            ->field('count(a.to_firms_id) as num,a.to_firms_id,a.id as nid,b.classification,b.id,b.uname,b.type,b.EnterpriseID,b.companyname,b.major,b.head_pic,b.face_pic,b.wechat_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,max(a.create_time) as invitime,a.visit_type')
            ->where(array('a.firms_id'=>$myFirmId,'a.is_show'=>1))
            ->jion('left join firms as b on a.to_firms_id=b.id')
            ->group('a.to_firms_id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        //预处理数据
        foreach($res as $key=>$item){
            $res[$key]['isVip']    = 0;
            $res[$key]['invitime'] = date("m-d H:i",strtotime($item['invitime']));
            //判断vip是否过期
            if(strtotime($item['vip_time'])>time()){
                $res[$key]['isVip']    = 1;
            }
        }
        return array('list'=>$res,'count'=>$count['num'],'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }

    /**
     * 拨打记录
     * @param $myFirmId
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function getCallToFirmsLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_call_log as a')->field('count(DISTINCT to_firms_id) as num')->where(array('a.firms_id'=>$myFirmId,'is_show'=>1))->getOne();
        $res = $this->table('firms_call_log as a')
            ->field('count(a.to_firms_id) as num,a.to_firms_id,a.id as nid,b.classification,b.id,b.uname,b.type,b.EnterpriseID,b.companyname,b.major,b.head_pic,b.face_pic,b.wechat_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,max(a.create_time) as invitime,a.call_type')
            ->where(array('a.firms_id'=>$myFirmId,'a.is_show'=>1))
            ->jion('left join firms as b on a.to_firms_id=b.id')
            ->group('a.to_firms_id')
            ->order('a.create_time desc')
            ->limit($start,$pageSize)->get();
        //预处理数据
        foreach($res as $key=>$item){
            $res[$key]['isVip']    = 0;
            $res[$key]['invitime'] = date("m-d H:i",strtotime($item['invitime']));
            //判断vip是否过期
            if(strtotime($item['vip_time'])>time()){
                $res[$key]['isVip']    = 1;
            }
        }
        return array('list'=>$res,'count'=>$count['num'],'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }

    /**
     * 获取用户认证状态
     * @param $userId
     * @return mixed
     */
    public function getCompanyAuth($userId){
        //获取认证信息
        $res = $this->table('firms_check')->where(array('firms_id'=>$userId))->order('create_time desc')->getOne();
        if($res){//有相关数据
            $return['status'] = 200;//请求用户数据成功
            $return['msg']    = '厂商提交认证';
            $return['data']   = $res;
        }else{//无相关数据
            $return['status'] = 201;//请求用户数据成功
            $return['msg']    = '厂商未提交认证';
        }
        return $return;
    }

    /**
     * 保存认证信息
     * @param $data
     * @return mixed
     */
    public function saveAuth($data){
        $res = $this->table('firms_check')->insert($data);
        if($res){
            $return['status'] = 200;
            $return['msg']    = '申请认证成功';
        }else{
            $return['status'] = 201;
            $return['msg']    = '保存认证失败，请稍候重试';
        }
        return $return;
    }

    /**
     * 获取厂商经营范围 二级
     * @param $userId
     * @return mixed
     */
    public function getRange($userId){
        $business = $this->table('firms')->field('business')->where("id=$userId")->getOne();
        $business = substr($business['business'],1,(strlen($business['business'])-2));
        $res = $this->table('car_group a')->field('a.id,a.name,b.name as pName')->jion('left join car_group b on a.pid=b.id')->where("a.id in($business)")->get();
        $return['status'] = 200;
        $return['msg']    = '获取成功';
        $return['data']   = $res;
        return $return;
    }

    /**
     * 移除数组值
     * @param $arr
     * @param $value
     * @return mixed
     */
    public function delByValue($arr, $value){
        $keys = array_keys($arr, $value);
        if(!empty($keys)){
            foreach ($keys as $key) {
                unset($arr[$key]);
            }
        }
        return $arr;
    }

    /**
     * 获取店铺车系 - 四级分类信息 - 个人
     * @param $userId
     * @param $cid
     * @return mixed
     */
    public function getStoreSeries($userId,$cid){
        //获取个人四级
        $myFour = $this->table('firms')->field('four_ids')->where("id=".$userId)->getOne();
        $myFourArr = explode(',',$myFour['four_ids']);
        //获取全部四级
        $threData = $this->table('car_group')->where('pid='.$cid)->get();
        $unUse = $myFourArr;
        if($threData){
            $ids = [];
            foreach($threData as $v){
                array_push($ids,$v['id']);
            }
            $ids = join(',',$ids);
            $fourData = $this->table('car_group')->where('pid in ('.$ids.')')->get();
            if($fourData){
                for($i=0; $i<count($threData); ++$i){
                    $threData[$i]['child'] = [];
                    $k = 0;
                    for($j=0; $j<count($fourData); ++$j){
                        if($threData[$i]['id'] == $fourData[$j]['pid']){
                            $threData[$i]['child'][$k] = $fourData[$j];
                            $threData[$i]['child'][$k]['checked'] = '';
                            if(in_array($fourData[$j]['id'],$myFourArr)){
                                $threData[$i]['child'][$k]['checked'] = 'checked';
                                $unUse = $this-> delByValue($unUse,  $fourData[$j]['id']);
                            }
                            $k += 1;
                        }
                    }
                }
            }
        }else{
            $threData = array();
        }
        $return['status'] = 200;
        $return['msg']    = '获取成功';
        $return['data']['threData'] = $threData;
        $return['data']['unUse']    = implode(',',$unUse);
        return $return;
    }

    /**
     *  保存店铺保存四级车系
     * @param $userId
     * @param $ranges
     * @return mixed
     */
    public function saveRange($userId,$ranges){
        //保存四级车系信息
        $rst = $this->table('firms')->where(array('id'=>$userId))->update(array('four_ids'=>$ranges));
        $return['status'] = 200;
        $return['msg']    = '保存成功';
        return $return;
    }

    /**
     * 获取企业名片信息
     * @param $userId
     * @return mixed
     */
    public function getCardInfo($userId){
        $data = $this->table('firms_card')->where('firms_id='.$userId)->group('create_time desc')->getOne();
        return   $return = array('status'=>'200','msg'=>'产品刷新成功','data'=>$data);
    }

    /**
     * 获取企业模板信息
     * @param $userId
     * @return mixed
     */
    public function getCardTplInfo($userId){
        $user = $this->table('firms')->where(array('id'=>$userId))->getOne();
        if($user){
            $icon = array();
            //预处理数据
            if($user['type']==1&&$user['business']){
                $erId = substr($user['business'],1,-1);
                $icon = $this->table('car_group')->where('id in ('.$erId.')')->field('img')->limit(0,3)->get();
            }
            $user['icon'] = $icon;
            $linkPhoneArr  = explode(',',$user['linkPhone']);
            $linkTelArr    = explode(',',$user['linkTel']);
            $qqArr         = explode(',',$user['qq']);
            //手机号码
            $phone1  = (isset($linkPhoneArr[0])&&!empty($linkPhoneArr[0]))?$linkPhoneArr[0]:'';
            $phone2  = (isset($linkPhoneArr[1])&&!empty($linkPhoneArr[1]))?(','.$linkPhoneArr[1]):'';
            $user['phoneStr'] = $phone1.$phone2;
            //qq
            $qq1  = (isset($qqArr[0])&&!empty($qqArr[0]))?$qqArr[0]:'';
            $qq2  = (isset($qqArr[1])&&!empty($qqArr[1]))?(','.$qqArr[1]):'';
            $user['qqStr'] = $qq1.$qq2;
            //座机
            $tel1  = (isset($linkTelArr[0])&&!empty($linkTelArr[0]))?$linkTelArr[0]:'';
            $tel2  = (isset($linkTelArr[1])&&!empty($linkTelArr[1]))?(','.$linkTelArr[1]):'';
            $user['telStr'] = $tel1.$tel2;
            $return = array('status'=>'200','msg'=>'获取用户数据成功','data'=>$user);
        }else{
            $return = array('status'=>'201','msg'=>'获取用户数据失败');
        }
        return $return;
    }

    /**
     * 保存名片图片
     * @param $base64 string  base64字符串
     */
    public function base64Save($base64){
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
            $type = $result[2];
            $size = strlen(file_get_contents($base64));     //获取base64图片大小
            if($size > 5242880){           //图片大于5M
                $return['status'] = 0;
                $return['msg']    = '名片大小超过5M，上传失败';
            }else{
                $new_file = APPROOT."/data/card/".date('Ymd',time())."/";
                if(!file_exists($new_file))
                {
                    //检查是否有该文件夹，如果没有就创建，并给予最高权限
                    mkdir($new_file, 0700);
                }
                $new_file = $new_file.time().".{$type}";
                $path     = "/data/card/".date('Ymd',time())."/".time().'.'.$result[2];
                if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64)))){
                    $return['status'] = 200;
                    $return['path']   = $path;
                    $return['msg']    = '名片图片保存成功';
                }else{
                    $return['status'] = 202;
                    $return['msg']    = '名片保存失败';
                }
            }
        }else{
            $return['status'] =201;
            $return['msg']    = '提交数据有误';
        }
        return $return;
    }

    /**
     * 保存企业名片数据
     * @param $data
     * @param $companyId
     * @return int
     */
    public function ceartCard($data,$companyId){
        $firms_type = $this->table('firms')->field('type')->where('id='.$companyId)->getOne();
        $return = array('status'=>'301','msg'=>'名片保存失败');
        if($firms_type){
            $data['firms_type']  = $firms_type['type'];
            $data['firms_id']    = $companyId;
            $card = $this->table('firms_card')->where('firms_id='.$companyId)->getOne();
            if($card){
                $rst = $this->table('firms_card')->where('firms_id='.$companyId)->update($data);
                if($rst > 0){
                    $return = array('status'=>'200','msg'=>'名片保存成功');
                }
            }else{
                $data['create_time'] = date("Y-m-d H:i:s");
                $rst = $this->table('firms_card')->insert($data);
                if($rst > 0){
                    $return = array('status'=>'200','msg'=>'名片保存成功');
                }
            }
        }
        return $return;
    }

    //来访记录
    public function getFirmsToVisitLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_visit_log as a')->field('count(DISTINCT a.firms_id) as num') ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')->getOne();
        $res = $this->table('firms_visit_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.head_pic,b.face_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,max(a.create_time) as invitime,a.visit_type')
            ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')
            ->jion('left join firms as b on a.firms_id=b.id')
            ->order('a.create_time desc')
            ->group('a.firms_id')
            ->limit($start,$pageSize)->get();
        //预处理数据
        foreach($res as $key=>$item){
            $res[$key]['isVip']    = 0;
            //判断vip是否过期
            if(strtotime($item['vip_time'])>time()){
                $res[$key]['isVip']    = 1;
            }

            if($item['linkPhone']){
                $linkPhone         = explode(',',$item['linkPhone']);
                $res[$key]['linkPhone'] = $linkPhone[0];
            }
        }
        $count = $count?$count:0;
        return array('list'=>$res,'count'=>$count['num'],'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }

    //来电记录
    public function getFirmsToCallLog($myFirmId,$page=1,$pageSize=10){
        $start = ($page-1)*$pageSize;
        $count = $this->table('firms_call_log as a')->field('count(DISTINCT a.firms_id) as num') ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')->getOne();
        $res   = $this->table('firms_call_log as a')
            ->field('a.id as nid,b.classification,b.id,b.uname,b.EnterpriseID,b.companyname,b.head_pic,b.face_pic,b.is_check,b.vip_time,b.linkMan,b.linkPhone,max(a.create_time) as invitime,a.call_type')
            ->where('a.to_firms_id='.$myFirmId.' and a.firms_id<>0')
            ->jion('left join firms as b on a.firms_id=b.id')
            ->order('a.create_time desc')
            ->group('a.firms_id')
            ->limit($start,$pageSize)->get();
        //预处理数据
        foreach($res as $key=>$item){
            $res[$key]['isVip']    = 0;
            //判断vip是否过期
            if(strtotime($item['vip_time'])>time()){
                $res[$key]['isVip']    = 1;
            }
            if($item['linkPhone']){
                $linkPhone         = explode(',',$item['linkPhone']);
                $res[$key]['linkPhone'] = $linkPhone[0];
            }
        }
        $count = $count?$count:0;
        return array('list'=>$res,'count'=>$count['num'],'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }

    /**
     * @param $uId  厂商唯一ID
     */
    public function uIdGetFirmId($uId){
        $data = $this->table('firms')->where('EnterpriseID='.$uId)->getOne();
        if($data){
            unset($data['password']) ;
            //预处理数据
            $data['vip'] = 0;
            if($data['vip_time']){
                if($data['vip_time'] > date('Y-m-d H:i:s',time())){
                    $data['vip'] = 1;
                }
            }
            $return = $data;
        }else{
            $return = '';
        }
        return $return;
    }

    /**
     * 获取用户刷新点数据
     * @param $userId
     * @param $p
     * @param $pageSize
     * @return mixed
     */
    public function getRefreshHistory($userId,$p,$pageSize)
    {
        $page = (intval($p)-1)*$pageSize;
        $list  = $this->table('pay_history')->where("firms_id=$userId and type>1")->limit($page,$pageSize)->order('create_time desc,payway asc')->get();
        $count = $this->table('pay_history')->where("firms_id=$userId and type>1")->count();
        //预处理数据
        foreach ($list as $k=>$item){
            if($item['type']==2){//充值刷新点
                $list[$k]['title']  = $item['status']==1?'充值成功':'充值失败';
                $list[$k]['tips']   = $item['payway']==1?'微信支付':($item['payway']==2?'支付宝':'人工充值');
                $list[$k]['pointStr']  = '<span style="font-size: 12px;">+</span>'.$item['refresh_point'];
            }elseif ($item['type']==3){//消费刷新点
                $list[$k]['title']  = $item['payway']==4?'刷新产品':'刷新店铺';
                $list[$k]['tips']   = $item['info']?$item['info']:'';
                $list[$k]['pointStr']  = '<span style="font-size: 12px;">-</span>'.abs($item['refresh_point']);
            }else{
                $list[$k]['title']  = '获得刷新点';
                $list[$k]['tips']   = $item['payway']==6?$item['info']:'官方赠送';
                $list[$k]['pointStr']  = '<span style="font-size: 12px;">+</span>'.$item['refresh_point'];
            }
        }
        return $data = array('list'=>$list,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);
    }

    /**
     * 获取用户vip充值数据
     * @param $userId
     * @param $p
     * @param $pageSize
     * @return mixed
     */
    public function getVipHistory($userId,$p,$pageSize)
    {
        $page = (intval($p)-1)*$pageSize;
        $list  = $this->table('pay_history')->where(array('firms_id'=>$userId,'type'=>1))->limit($page,$pageSize)->order('create_time desc')->get();
        $count = $this->table('pay_history')->where(array('firms_id'=>$userId,'type'=>1))->count();
        //预处理数据
        foreach ($list as $k=>$item){
            $list[$k]['statusStr'] = $item['status']==1?'充值成功':'充值失败';
            $list[$k]['paywayStr'] = $item['payway']==1?'微信支付':($item['payway']==2?'支付宝':'人工充值');
            $list[$k]['money']     = intval($list[$k]['money']);
        }
        return $data = array('list'=>$list,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);
    }

    /**
     *  保存店铺信息
     * @param $data
     * @param $userId
     * @param $bannerPic
     * @return mixed
     */
    public function saveStore($data,$userId,$bannerPic){
        //保存基础信息
        $company = $this->table('firms')->where(array('id'=>$userId))->getOne();
        if($company && $company['EnterpriseID']){
            $data['QR_pic'] = model('web.firms')->getQRStore($company['EnterpriseID'],$company['companyname'],$company['type']);
            $this->table('firms')->where(array('id'=>$userId))->update($data);
            //删除旧banner
            $this->table('firms_banner')->where(array('firms_id'=>$userId))->del();
            $bannerData = array('firms_id'=>$userId);
            //插入新banner
            foreach ($bannerPic as $item){
                $bannerData['banner_url'] = $item;
                $this->table('firms_banner')->insert($bannerData);
            }
            $return['status'] = 200;
            $return['msg']    = '保存成功';
        }else{
            $return['status'] = 201;
            $return['msg']    = '厂商数据获取失败';
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
     * @param $token    未解密的业务员id
     * 返回当前业务员拨打记录数据
     * @param $dingWeiToken    如果为一，表示定位当前位置失败
     */
    public function getBoDaJiLuNow($token,$page,$pageSize,$lat=104.06685359181,$lng=30.655965991207,$dingWeiToken,$type){
        $p = ($page-1)*$pageSize;
        if($type==1){
            $table = 'firms_visit_log';
        }else{
            $table = 'firms_call_log';
        }
        $data= $this->table($table)->where('firms_id='.$token.' and is_show=1')->group('to_firms_id')->limit($p,$pageSize)->get();
        $count= $this->getOne("select count(distinct to_firms_id) as nums from ".$table." where firms_id=".$token." and is_show=1");
        $count= $count['nums'];
        if($data){
            $companyIds = '';
            $counts = [];
            for($i=0; $i<count($data); ++$i){
                $counts[$i]['count']     = $this->table($table)->where('firms_id='.$token.' and to_firms_id='.$data[$i]['to_firms_id'].' and is_show=1')->count();
//                $companyId = $this->table('sales_call_log')->field('firms_id')->where('firms_id='.$data[$i]['firms_id'].' and sales_user_id='.$id.' and is_show=1')->getOne();
                $counts[$i]['companyId'] = $data[$i]['to_firms_id'];
                $companyIds .= $data[$i]['to_firms_id'];
                if($i<count($data)-1){
                    $companyIds .= ',';
                }
            }
            $filed = 'ROUND(6378.138*2*ASIN(SQRT(POW(SIN(('.$lat.'*PI()/180-latitude*PI()/180)/2),2)+COS('.$lat.'*PI()/180)*COS(latitude*PI()/180)*POW(SIN(('.$lng.'*PI()/180-longitude*PI()/180)/2),2)))*1000) AS distance,companyname,face_pic,major,is_check,vip_time,linkPhone,qq,classification,longitude,latitude,id,type,face_pic,linkMan';
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
                $last_time = $this->table($table)->where('firms_id='.$token.' and is_show=1 and to_firms_id='.$companyInfo[$i]['id'])->order('create_time desc')->getOne();
                $last_time = date('m-d H:i',strtotime($last_time['create_time']));
                $companyInfo[$i]['last_create_time'] = $last_time;
                if($companyInfo[$i]['qq']){
                    $companyInfo[$i]['qq'] = explode(',',$companyInfo[$i]['qq']);
                }
                if($companyInfo[$i]['linkPhone']){
                    $companyInfo[$i]['linkPhone'] = explode(',',$companyInfo[$i]['linkPhone']);
                }
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
        return $return;
    }

    /**
     * @param $page
     * @param $pageSize
     * @param $userId   当前厂商id
     * @param $type     1为访问记录   2为拨打记录
     * @param $companyId 访问的厂商id
     */
    public function getMingXi($page,$pageSize,$userId,$type,$companyId){
        $p = ($page-1)*$pageSize;
        if($type==1){
            $table = 'firms_visit_log';
        }else{
            $table = 'firms_call_log';
        }
        $data  = $this->table($table)->where('firms_id='.$userId.' and to_firms_id='.$companyId.' and  is_show=1')->limit($p,$pageSize)->order('create_time desc')->get();
        $count = $this->table($table)->where('firms_id='.$userId.' and to_firms_id='.$companyId.' and  is_show=1')->count();
        if($data){
            $return = array('status'=>200,'list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
        }else{
            $return = array('status'=>200,'list'=>array(),'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
        }
        return $return;
    }

    /**
     * @param $userId       业务员Id
     * @param $companyId    商家id
     */
    public function isGuanLian($userId,$companyId){
        $data = $this->table('firms_sales_user')->where('firms_id='.$companyId.' and sales_user_di='.$userId)->getOne();
        if($data){
            $return = array('status'=>200,'data'=>$data);
        }else{
            $return = array('status'=>201,'msg'=>'对不起该厂商和你不是关联关系，操作失败');
        }
        return $return;
    }
}








