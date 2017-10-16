<?php
/**
 * 用户信息模块
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/21
 * Time: 17:35
 */

class ApiSevUserModel extends Model{

    protected $_table    = 'firms';
    protected $_payTable = 'pay_history';

    /**
     * 检查是否登录 并且获取用户基本信息
     * @return mixed
     */
    public function loginIs($userToken){
        $userToken = authcode($userToken,'DECODE');
        $user      = $this->getUserInfo($userToken);

        if(empty($user)){

            $return['status'] = 201;//未找到用户数据
            $return['msg']    = '未找到用户数据';
        }else{
            unset($user['password']) ;
            //预处理数据
            $user['vip']   = ((strtotime($user['vip_time'])>time())==1)?1:0;
            $user['vip_time'] = date('Y-m-d', strtotime($user['vip_time']));
            $user['uname'] = $user['uname']?$user['uname']:substr_replace($user['phone'], '****', 3, 4);
            $user['role']  = ($user['type']==1)?'经销商':'汽修厂';


            $return['status'] = 200;//请求用户数据成功
            $return['msg']    = '请求用户数据成功';
            $return['data']   = $user;
        }

        return $return;
    }

    /**
     * 检查是否登录 并且获取业务员基本信息
     * @return mixed
     */
    public function loginYeWuIs($userToken){
        $userToken = authcode($userToken,'DECODE');
        if(empty($userToken)){
            $return['status'] = 201;//未找到用户数据
            $return['msg']    = '未找到符合token数据';
        }else{
            $data = $this->table('sales_user')->where('id='.$userToken)->getOne();
            if(empty($data)){
                $return['status'] = 202;//未找到用户数据
                $return['msg']    = '未找到业务员数据';
            }else{
                $data['type'] = 3;      //业务员
                $data['role'] = '业务员';      //业务员
                $data['head_pic'] = $data['facepic'];
                $return['status'] = 200;//请求用户数据成功
                $return['msg']    = '请求用户数据成功';
                $return['data']   = $data;
            }


        }
        return $return;
    }

    /**
     * 获取用户基本信息
     * @param $userToken
     * @return array|mixed
     */
    public function getUserInfo($userToken)
    {
        $user = $this->table($this->_table)->where(array('id'=>$userToken))->getOne();

        return $user;
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

        $list  = $this->table($this->_payTable)->where(array('firms_id'=>$userId,'type'=>1))->limit($page,$pageSize)->order('create_time desc')->get();
        $count = $this->table($this->_payTable)->where(array('firms_id'=>$userId,'type'=>1))->count();

        //预处理数据
        foreach ($list as $k=>$item){

            $list[$k]['statusStr'] = $item['status']==1?'充值成功':'充值失败';
            $list[$k]['paywayStr'] = $item['payway']==1?'微信支付':($item['payway']==2?'支付宝':'人工充值');
            $list[$k]['money']     = floatval($list[$k]['money']);


        }

        return $data = array('list'=>$list,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);
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

        $list  = $this->table($this->_payTable)->where("firms_id=$userId and type>1")->limit($page,$pageSize)->order('create_time desc,payway asc')->get();
        $count = $this->table($this->_payTable)->where("firms_id=$userId and type>1")->count();

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
     * 判断汽修厂或经销商是否绑定业务员
     * @param $userId
     * @return mixed
     */
    public function isSalesMan($userId){

        $data['status']     = 200;

        $nowTime = date("Y-m-d",time());

        $res = $this->table('firms_sales_user a')->field('b.uId')->jion('left join sales_user b on b.id=a.sales_user_di')->where("a.firms_id = $userId and $nowTime<a.end_time")->getOne();
        $isSalesMan = false;

        if($res){
            $isSalesMan  = true;
        }

        $res['isSalesMan']  = $isSalesMan;
        $data['data']   = $res;
        return $data;
    }


    /**
     * 绑定业务员
     * @param $userId
     * @param $salesUserId
     * @return mixed
     */
    public function bindSales($userId,$salesUserId){
        //查询业务员是否存在
        $salesperson = $this->table('sales_user')->where(array('uId'=>$salesUserId))->getOne();

        if($salesperson){//业务员存在 绑定

            $data['sales_user_di'] = $salesperson['id'];
            $data['firms_id'] =$userId;
            $data['end_time'] = date('Y-m-d',strtotime("+3 month +1 day"));
            $data['create_time'] = date("Y-m-d H:i:s",time());

            $this->table('firms_sales_user')->insert($data);

            $return['status'] = 200;//绑定业务员成功
            $return['msg']    = '绑定业务员成功';

        }else{

            $return['status'] = 201;//请求用户数据成功
            $return['msg']    = '该业务员不存在，请核对后重试';

        }

        return $return;
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

        $firmId = $data['firms_id'];
        //将之前审核通过的设为拒绝
        $this->table('firms_check')->where("firms_id=$firmId and status in(1,2)")->update(array("status"=>3,'reason'=>'重新提交审核'));

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
     * 获取联系信息 qq 电话
     */
    public function getLinkInfo(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>4))->getOne();
        $ret = array('QQ'=>'','Tel'=>'');
        if($res){
            if($res['value']){
                $arr = json_decode($res['value'],true);
                $ret['QQ']  = empty($arr['qq'])?'':$arr['qq'];
                $ret['Tel'] = empty($arr['tel'])?'':str_replace("-","",$arr['tel']);
            }
        }

        $return['status'] = 200;
        $return['msg']    = '获取数据成功';
        $return['data']   = $ret;
        return $return;

    }


    //获取公司简介
    public function getMyInfo(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>9))->getOne();
        $return['status'] = 200;
        $return['msg']    = '获取数据成功';
        $return['data']   = $res;
        return $return;
    }

    //获取服务协议
    public function getFuWuXieYi(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>5))->getOne();
        $return['status'] = 200;
        $return['msg']    = '获取数据成功';
        $return['data']   = $res;
        return $return;
    }

    //获取服务协议
    public function getAppVersion(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>11))->getOne();
        $return['status'] = 200;
        $return['msg']    = '获取数据成功';
        $return['data']   = $res;
        return $return;
    }

    /*——————————————
     *店铺管理
     *——————————————*/

    /**
     * 获取店铺基础信息
     * @param $userId
     * @return array|mixed
     */
    public function getStoreInfo($userId)
    {

        $field = 'companyname,EnterpriseID,face_pic,type,classification,address,business,coordinate,longitude,latitude,major,linkMan,linkPhone,linkTel,qq,wechat_pic,info';

        $res = $this->table($this->_table)->field($field)->where(array('id'=>$userId))->getOne();

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

        $res['linkPhoneArr']  = explode(',',$res['linkPhone']);
        $res['linkTelArr']    = explode(',',$res['linkTel']);
        $res['qqArr']         = explode(',',$res['qq']);

        $res['linkPhone']  = str_replace(",","<br>",$res['linkPhone']);
        $res['linkTel']    = str_replace(",","<br>",$res['linkTel']);
        $res['qq']         = str_replace(",","<br>",$res['qq']);



        $typeArr = array(1=>'经销商',2=>'修理厂');
        $classArr = array(1=>'轿车商家' ,2=>'货车商家', 3=>'用品商家', 4=>'修理厂', 5=>'快修保养', 6=>'美容店');

        $res['typeStr']  = $typeArr[$res['type']];
        $res['classStr'] = $classArr[$res['classification']];

        //获取访问数据'a.to_firms_id='.$myFirmId.' and a.firms_id<>0'

        $count1 = $this->table('firms_visit_log')->field('count(DISTINCT firms_id) as num') ->where('to_firms_id='.$userId.' and firms_id<>0')->getOne();
        $res['visit_num'] = $count1['num'];
        //获取来电数据
        $count2  = $this->table('firms_call_log')->field('count(DISTINCT firms_id) as num') ->where('to_firms_id='.$userId.' and firms_id<>0')->getOne();
        $res['call_num']  = $count2['num'];
        //获取该厂商banner
        $res['banners'] = $this->table('firms_banner')->where(array('firms_id'=>$userId))->limit(0,3)->get();

        $res['bannersArr'] = array();
        foreach($res['banners'] as $i=>$bannerItem){
            $res['bannersArr'][$i] = $bannerItem['banner_url'];
        }

        return $res;
    }

    /**
     *  保存店铺信息
     * @param $data
     * @param $userId
     * @param $bannerPic
     * @return mixed
     */
    public function saveStore($data,$userId,$bannerPic){

        $company =  $this->table('firms')->where('id='.$userId)->getOne();
        $data['QR_pic'] = model('web.firms')->getQRStore($company['EnterpriseID'],$company['companyname'],$company['type']);
        //保存基础信息
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
     * 获取店铺车系 - 四级分类信息 - 个人
     * @param $userId
     * @param $cid
     * @return mixed
     */
    public function getStoreSeries($userId,$cid){
        //获取个人四级
        $myFour = $this->table('firms')->field('four_ids')->where("id=$userId")->getOne();
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
        $this->table('firms')->where(array('id'=>$userId))->update(array('four_ids'=>$ranges));
        $return['status'] = 200;
        $return['msg']    = '保存成功';
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
     * 产品下架
     * @param $userId
     * @param $productId
     * @return array
     */
    public function productOffSale($userId,$productId){
        //查询该产品
        $product = $this->table('product_list')->where("firms_id=$userId and proId=$productId and pro_status=1")->getOne();

        if($product){
            //查询到该产品
            $res = $this->table('product_list')->where("proId=$productId")->update(array('pro_status'=>2,'update_time'=>date("Y-m-d H:i:s")));
            $return = array('status'=>'200','msg'=>'下架成功');
        }else{//未查询到产品
            $return = array('status'=>'200','msg'=>'该产品不存在，或已下架');
        }
        return $return;
    }

    /**
     * 产品上架
     * @param $userId
     * @param $productId
     * @return array
     */
    public function productSale($userId,$productId){
        //查询该产品
        $product = $this->table('product_list')->where("firms_id=$userId and proId=$productId and pro_status=2")->getOne();

        if($product){
            //查询到该产品
            $res = $this->table('product_list')->where("proId=$productId")->update(array('pro_status'=>1,'update_time'=>date("Y-m-d H:i:s")));
            $return = array('status'=>'200','msg'=>'上架成功');
        }else{//未查询到产品
            $return = array('status'=>'200','msg'=>'该产品不存在，或已上架');
        }
        return $return;
    }



    /**
     * 产品删除
     * @param $userId
     * @param $productId
     * @return array
     */
    public function delProduct($userId,$productId){

        //查询该产品
        $want = $this->table('product_list')->where("firms_id=$userId and proId=$productId and is_delete=0")->getOne();
        if($want){
            //查询到该产品
            $res = $this->table('product_list')->where("proId=$productId")->update(array('pro_status'=>2,'is_delete'=>1,'update_time'=>date("Y-m-d H:i:s")));
            $return = array('status'=>'200','msg'=>'删除成功');
        }else{//未查询到产品
            $return = array('status'=>'200','msg'=>'该产品不存在，或已删除');
        }
        return $return;
    }

    /**
     *  获取刷新点
     * @param $userId
     * @return array
     */
    public function getRefresh($userId){

        $refresh = $this->table('firms')->where("id=$userId")->field("refresh_point")->getOne();

        if($refresh){
            $refresh['refresh_point'] = $refresh['refresh_point']?$refresh['refresh_point']:0;
            $return = array('status'=>'200','msg'=>'获取刷新点数据成功','refresh_point'=>$refresh['refresh_point']);
        }else{
            $return = array('status'=>'201','msg'=>'获取刷新点数据失败');
        }
        return $return;
    }


    /**
     * 刷新产品
     * @param $productId
     * @param $userId
     * @return array
     */
    public function refreshProduct($productId,$userId){
        $return = array('status'=>'201','msg'=>'产品刷新失败');

        //扣除点数
        $sqli = 'update firms set refresh_point=refresh_point-1 where id='.$userId;
        $res1 = $this->query($sqli);

        //生成记录
        if($res1){
            $productName = $this->table('product_list')->field('proName')->where('proId='.$productId.' and firms_id='.$userId)->getOne();

            $data['type']   = 3;          //刷新点消费
            $data['status'] = 1;          //充值成功
            $data['info']   = $productName['proName']; //详情
            $data['payway'] = 4;          //刷新产品
            $data['refresh_point'] = 1;   //刷新点数
            $data['firms_id'] = $userId;  //厂商ID
            $data['money']    = 0;         //充值金额
            $data['admin_id'] = 0;         //管理员ID（没有为0）
            $data['create_time'] = date("Y-m-d H:i:s");
            $result = $this->table('pay_history')->insert($data);
            if($result>0){
                //刷新产品
                $sql = 'update product_list set pro_refresh=pro_refresh+1,refresh_time="'.date("Y-m-d H:i:s").'" where proId='.$productId.' and firms_id='.$userId;
                $rst = $this->query($sql);
                if($rst){
                    $return = array('status'=>'200','msg'=>'产品刷新成功');
                }
            }
        }
        return $return;
    }

    /**
     * 获取企业名片信息
     * @param $userId
     * @return mixed
     */
    public function getCardInfo($userId){
        $data = $this->table('firms_card')->where('firms_id='.$userId)->group('create_time desc')->getOne();
        return   $return = array('status'=>'200','msg'=>'获取名片成功','data'=>$data);
    }


    /**
     * 获取企业名片信息
     * @param $EnterpriseID
     * @return mixed
     */
    public function getCardInfoByEnterpriseID($EnterpriseID){

        $firms = $this->table("firms")->where("EnterpriseID=$EnterpriseID")->getOne();

        $userId = $firms['id'];

        $data = $this->table('firms_card')->where('firms_id='.$userId)->group('create_time desc')->getOne();
        return   $return = array('status'=>'200','msg'=>'获取名片成功','data'=>$data);
    }

    /**
     * 获取企业模板信息
     * @param $userId
     * @return mixed
     */
    public function getCardTplInfo($userId){
        $user = $this->table($this->_table)->where(array('id'=>$userId))->getOne();

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
     * 返回所有修理厂坐标
     */
    public function getAllZuoBiao($classification,$wordsKey,$province,$city,$district){

        $find = 'type=2 and longitude is not null and latitude is not null and status=1';
        if($classification){
            $find .= ' and classification='.$classification;
        }

        if($wordsKey){
            $find .= ' and companyname like"%'.$wordsKey.'%"';
        }

        if($province){
            $find .= ' and province="'.$province.'"';
        }
        if($city){
            $find .= ' and city="'.$city.'"';
        }
        if($district){
            $find .= ' and district="'.$district.'"';
        }

        $field = 'longitude,latitude,companyname,EnterpriseID';
        $data = $this->table('firms')->field($field)->where($find)->get();
        return $data;
    }

    /**
     * 搜索汽修厂列表
     * @param $page
     * @param $pageSize
     * @param $wordsKey
     * @param $province
     * @param $city
     * @param $district
     * @param $classification
     * @param $lat
     * @param $lng
     * @return array
     */
    public function showCarMend($page,$pageSize,$wordsKey,$province,$city,$district,$classification,$lat,$lng){
        $start = ($page-1)*$pageSize;
        $where = 'type=2 and status=1 and longitude is not null and latitude is not null';
        if($wordsKey){
            $where .= ' and companyname like"%'.$wordsKey.'%"';
        }
        if($province){
            $where .= ' and province="'.$province.'"';
        }
        if($city){
            $where .= ' and city="'.$city.'"';
        }
        if($district){
            $where .= ' and district="'.$district.'"';
        }
        if($classification){
            $where .= ' and classification="'.$classification.'"';
        }
        $count = $this->table('firms')->where($where)->count();
        $field = 'id,companyname,EnterpriseID,city,district,classification,vip_time,is_check,face_pic,type,longitude,latitude,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(('.$lat.'*PI()/180-latitude*PI()/180)/2),2)+COS('.$lat.'*PI()/180)*COS(latitude*PI()/180)*POW(SIN(('.$lng.'*PI()/180-longitude*PI()/180)/2),2)))*1000) AS distance';
        $data = $this->table('firms')->field($field)->where($where)->order('distance ASC')->limit($start,$pageSize)->get();

        if($data){
            for($i=0; $i<count($data); ++$i){
                $data[$i]['vip'] = 0;
                if($data[$i]['vip_time']>date("Y-m-d H:i:s")){
                    $data[$i]['vip'] = 1;
                }

                $distance  =  $this->latlng($data[$i]['distance']);
                $data[$i]['distance'] = $distance;

            }
        }

        return $data = array('list'=>$data,'count'=>$count,'status'=>'200','page'=>$page,'pageSize'=>$pageSize);
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
     * @param $token    加密的id
     */
    public function editYeWu($token,$data){
        $id  = authcode($token,'DECODE');
        $data['update_time'] = date("Y-m-d H:i:s");
        $rst = $this->table('sales_user')->where('id='.$id)->update($data);
        return $rst;
    }

    /**
     * @param $token  加密后的ID
     * @param $pwd    密码
     */
    public function yeWuYuanYanZheng($token,$pwd){
        $id  = authcode($token,'DECODE');
        $pwd = md5(sha1($pwd).'sw');
        $rst = $this->table('sales_user')->where('id='.$id.' and password="'.$pwd.'"')->getOne();
        return $rst;
    }

    /**
     * @param $token    加密后的ID
     * @param $pwd      密码
     */
    public function editPasswordYeWu($token,$pwd){
        $id  = authcode($token,'DECODE');
        $pwd = md5(sha1($pwd).'sw');
        $rst = $this->table('sales_user')->where('id='.$id)->update(array('password'=>$pwd));
        return $rst;
    }
}