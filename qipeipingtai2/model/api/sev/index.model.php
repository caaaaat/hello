<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 17:56
 */
class ApiSevIndexModel extends Model{


    /**
     * 获取产品分类
     * @return mixed
     */
    public function getProductGroup(){

        //获取一级分类
        $cat_1 = $this->table('product_category')->where('level=1')->order('vid asc')->get();
        //获取二级分类

        $cat_2 = array();

        foreach ($cat_1 as $k=>$item){

            $cat = $this->table('product_category')->where(array('level'=>2,'pid'=>$item['id']))->order('vid asc')->get();

            $cat_2[$k]['pid']  = $item['id'];
            $cat_2[$k]['data'] = $cat;

        }

        $data['cat_1']  = $cat_1;
        $data['cat_2']  = $cat_2;
        $data['status'] = 200;

        return $data;

    }

    /**
     * 搜索产品
     * @param $type
     * @param $groupId
     * @param $keyword
     * @param $p
     * @param $pageSize
     * @param $currentCity
     * @return array
     */
    public function getProducts($type,$groupId,$keyword,$p,$pageSize,$currentCity='成都'){

        $page = (intval($p)-1)*$pageSize;

        $find = "a.is_delete=0 and a.pro_status=1 and a.pro_type = '库存清仓'";

        if($type==1){

            $find = "a.is_delete=0 and a.pro_status=1 and a.pro_type = '新品促销'";

        }

        if($groupId){

            $find .= " and a.pro_cate_2=$groupId";

        }

        if($keyword){

            $find .= " and (a.proName like '%" . $keyword . "%' or a.pro_brand like '%" . $keyword . "%' or a.car_group like '%" . $keyword . "%') ";

        }

        if($currentCity){
            $find .= ' and f.city like "%'.$currentCity.'%" ';
        }

        $filed = "a.*,b.name as cate_1_name,c.name as cate_2_name";

        $join = "inner join firms as f on a.firms_id=f.id left join product_category b on a.pro_cate_1=b.id left join product_category c on a.pro_cate_2=c.id";

        $list  = $this->table('product_list a')->field($filed)->where($find)->jion($join)->limit($page,$pageSize)->order('a.pro_refresh desc')->get();

        $count = $this->table('product_list a')->where($find)->jion($join)->count();

        //预处理数据
        foreach ($list as $k=>$item){

            unset($item['id']);

            $list[$k]          = $item;
            $list[$k]['price'] = $item['pro_price']?'￥'.$item['pro_price']:'欢迎来电询价';
        }

        return $data = array('list'=>$list,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);

    }


    /**
     * 搜索产品
     * @param $EnterpriseID
     * @param $type
     * @param $pro_cate_1
     * @param $pro_cate_2
     * @param $p
     * @param $pageSize
     * @return array
     */
    public function getFirmProducts($EnterpriseID,$type,$pro_cate_1,$pro_cate_2,$p,$pageSize){

        $page = (intval($p)-1)*$pageSize;

        $find = "a.is_delete=0 and a.pro_status=1 and a.firms_id=d.id";

        if($type==1){

            $find .= " and a.pro_type = '新品促销'";

        }

        if($type==2){
            $find .= " and a.pro_type = '库存清仓'";
        }

        if($pro_cate_1){

            $find .= " and a.pro_cate_1=$pro_cate_1";

        }

        if($pro_cate_2){

            $find .= " and a.pro_cate_2=$pro_cate_2";

        }

        $filed = "a.*,b.name as cate_1_name,c.name as cate_2_name";

        $join = "left join product_category b on a.pro_cate_1=b.id left join product_category c on a.pro_cate_2=c.id left join firms d on d.EnterpriseID=$EnterpriseID";

        $list  = $this->table('product_list a')->field($filed)->where($find)->jion($join)->limit($page,$pageSize)->order('a.pro_refresh desc')->get();
        $count = $this->table('product_list a')->where($find)->jion($join)->count();

        //预处理数据
        foreach ($list as $k=>$item){

            unset($item['id']);

            $list[$k]          = $item;
            $list[$k]['price'] = $item['pro_price']?'￥'.$item['pro_price']:'欢迎来电询价';
        }

        return $data = array('list'=>$list,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);

    }

    /**
     *  获取产品详情
     * @param $proId
     * @param $id
     * @return mixed
     */
    public function getProductDetail($proId,$id,$userType){

        $filed = "a.*,b.name as cate_1_name,c.name as cate_2_name,d.EnterpriseID,d.companyname,d.address,d.province,d.city,d.district,d.is_vip,d.is_check,d.linkPhone,d.qq,d.type as firmType,d.wechat_pic";

        $join = "left join product_category b on a.pro_cate_1=b.id left join product_category c on a.pro_cate_2=c.id left join firms d on a.firms_id=d.id";


        $data  = $this->table('product_list a')->field($filed)->where(array('a.proId'=>$proId,'a.pro_status'=>1))->jion($join)->getOne();

        if($data){

            $return['status']    = 200;
            $return['is_delete'] = 1;
            $return['data']      = array();

            if($data['is_delete']==0){
                $return['is_delete'] = 0;

                //价格
                $data['price']       = $data['pro_price']?'￥'.$data['pro_price']:'欢迎来电询价';
                //地址
                $city = ($data['province']==$data['city'])?'':$data['city'];
                $district = ($data['district']==$data['city'])?'':$data['district'];
                //

                if($data['linkPhone']){
                    $linkPhone         = explode(',',$data['linkPhone']);
                    $data['linkPhone'] = $linkPhone[0];
                }

                if($data['qq']){
                    $qq         = explode(',',$data['qq']);
                    $data['qq'] = $qq[0];
                }

                $data['address']     = $data['province'].$city.$district.$data['address'];

                //获取收藏记录
                $collectMo = model('web.collect','mysql');

                //判断是否收集了该产品
                $isCollectProduct = $collectMo->isCollectProduct($userType,$id,$data['id']);
                $data['isCollectProduct'] = $isCollectProduct;

                $return['data']      = $data;
            }

        }else{
            $return['status'] = 201;
            $return['msg'] = '提交数据有误';
        }

        return $return;

    }





    /**
     * 获取厂商通过企业ID 判断是否收藏该汽修厂
     * @param $EnterpriseID
     * @param $id
     * @param $userType
     * @return mixed
     */
    public function getFirmInfoByEnID($EnterpriseID,$id,$userType){
        $res = $this->table('firms')->where(array('EnterpriseID'=>$EnterpriseID))->getOne();
        if($res){
            //获取该厂商banner
            $res['banners'] = $this->table('firms_banner')->where(array('firms_id'=>$res['id']))->get();
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

            //获取是否收藏
            $res['isCollect'] = 0;

            if($id){
                $isCollect = $this->isCollectFirms($userType,$id,$res['id']);
                if($isCollect){
                    $res['isCollect'] = 1;
                }
            }

            //是否是vip
            $res['is_vip']   = ((strtotime($res['vip_time'])>time())==1)?1:0;

            if($res['type']==1){
                //获取产品分类
                //二级分类
                $pro_cate_2 = $this->table('product_list a')
                    ->field('a.pro_cate_2,b.pid,b.name,a.pro_type,case a.pro_type when "新品促销" then "xp" else "qc" end as typeId')
                    ->jion('left join product_category b on a.pro_cate_2=b.id')
                    ->where("a.is_delete=0 and a.pro_status=1 and a.firms_id=".$res['id'])
                    ->group('a.pro_cate_2')
                    ->get();
                $res['pro_cate_2'] = $pro_cate_2;
                //一级分类
                $pro_cate_1 = $this->table('product_list a')
                    ->field('a.pro_cate_1,b.pid,b.name,a.pro_type,case a.pro_type when "新品促销" then "xp" else "qc" end as typeId')
                    ->jion('left join product_category b on a.pro_cate_1=b.id')
                    ->where("a.is_delete=0 and a.pro_status=1 and a.firms_id=".$res['id'])
                    ->group('a.pro_cate_1')->get();
                $res['pro_cate_1'] = $pro_cate_1;
                //清仓 促销
                $pro_type = $this->table('product_list a')
                    ->field('a.pro_type,case a.pro_type when "新品促销" then "xp" else "qc" end as typeId')
                    ->where("a.is_delete=0 and a.pro_status=1 and a.firms_id=".$res['id'])
                    ->group('a.pro_type')->get();
                $res['pro_type'] = $pro_type;

            }

            //获取访问数据
            $count1 = $this->table('firms_visit_log')->field('count(DISTINCT firms_id) as num') ->where('to_firms_id='.$res['id'].' and firms_id<>0')->getOne();
            $res['visit_num'] = $count1['num'];
            //获取来电数据
            $count2  = $this->table('firms_call_log')->field('count(DISTINCT firms_id) as num') ->where('to_firms_id='.$res['id'].' and firms_id<>0')->getOne();
            $res['call_num']  = $count2['num'];

            //获取认证信息
            $res['renzheng_info'] = $this->table('firms_check')->where(array('firms_id'=>$res['id'],'status'=>2))->order('create_time desc')->getOne();
        }
        return $res;
    }

    /**
     * 判断是否收藏该产品
     * @param $type      int 1厂商 2业务员
     * @param $myFirmId  int 厂商id  业务员id
     * @param $firms_id
     * @return bool
     */
    public function isCollectFirms($type,$myFirmId,$firms_id){
        $res = $this->table('collect_firms')->where(array('type'=>$type,'fu_id'=>$myFirmId,'firms_id'=>$firms_id))->getOne();

        if($res){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 搜索求购
     * @param $p
     * @param $pageSize
     * @return array
     */
    public function getMountings($p,$pageSize,$currentCity='成都'){

        $page = (intval($p)-1)*$pageSize;

        $find = "a.is_delete=0 and a.status=1";

        if($currentCity){
            $find .= ' and b.city like "%'.$currentCity.'%" ';
        }

        $join = "left join firms b on a.firms_id=b.id";

        $filed = "a.*,b.companyname,b.face_pic";

        $list  = $this->table('want_buy a')->field($filed)->where($find)->jion($join)->limit($page,$pageSize)->order('a.create_time desc,a.limitation desc')->get();
        $count = $this->table('want_buy a')->where($find)->jion($join)->count();

        //预处理数据
        foreach ($list as $k=>$item){

            //获取求购图片
            $wantId  = $item['id'];
            $wantPic = $this->table('want_buy_pic')->where(array('want_buy_id'=>$wantId))->get();

            //获取四级车系
            $foruId = $item['car_group_id'];
            $list[$k]['groups'] = $this->getOneTwoThreeFourByFour($foruId);

            //获取配件数
            $wantList = $this->table('want_buy_list')->field('SUM(amount) as amount')->where(array('want_buy_id'=>$wantId))->group('want_buy_id')->getOne();


            $list[$k]['wantPic'] = $wantPic;
            $list[$k]['wantNum'] = $wantList['amount'];
        }

        return $data = array('list'=>$list,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);

    }


    /**
     * 获取求购详情
     * @param $wantId
     * @return array
     */
    public function getWantDetail($wantId){

        $find = "a.is_delete=0 and a.status=1 and a.id=$wantId";

        $join = "left join firms b on a.firms_id=b.id";

        $filed = "a.*,b.companyname,b.EnterpriseID,b.face_pic,b.address,b.province,b.city,b.district,b.is_vip,b.is_check,b.linkPhone,b.qq,b.type as firmType,b.wechat_pic";

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
     * 获取未读消息条数
     *  $toType         类型   1厂商  2业务员
     *  $toId    厂商业务员id   厂商id或业务员id
     */
    public function getUnReadMsgNum($type,$toId){
        //SELECT count(*) From msg WHERE (toType=0 AND toId=0) or (toType=1 AND toId=9);
        //SELECT count(*) FROM msg as a LEFT JOIN msg_read as c on c.msg_id=a.id WHERE c.fu_id = 9 AND c.type = 1
        if($type==1){
            $firm  = $this->table('firms')->where(array('id'=>$toId))->getOne();
            $where = '( toType=0 AND toId=0 ) or ( toType='.$firm['classification'].' AND toId=0 ) or ( toType=9 AND toId='.$toId.' )';
        }else{
            $where = '( toType=0 AND toId=0 ) or ( toType=10 AND toId='.$toId.' )';
        }

        $res = $this->table('msg')->where($where)->count();
        $rst = $this->table('msg as a')->jion('LEFT JOIN msg_read as c on c.msg_id=a.id')->where(array('c.fu_id'=>$toId,'c.type'=>$type))->count();

        //新闻 促销
        $allNews   = $this->table('article_news')->count();
        $allActive = $this->table('article_activity')->count();

        $news   = $this->table('msg as a')->jion('LEFT JOIN msg_read as c on c.msg_id=a.id')->where(array('c.fu_id'=>$toId,'c.type'=>$type,'a.msgType'=>8))->count();
        $active = $this->table('msg as a')->jion('LEFT JOIN msg_read as c on c.msg_id=a.id')->where(array('c.fu_id'=>$toId,'c.type'=>$type,'a.msgType'=>7))->count();

        $data['naNum'] = $res-$rst;
        $data['newNum'] = $allNews-$news;
        $data['activeNum'] = $allActive-$active;

        return $return = array('data'=>$data,'status'=>'200','msg'=>'获取成功');

    }

    /**
     * 获取刷新点配置
     * @return array
     */
    public function getRefreshIni(){

        $res = $this->table('base_ini')->where(array('`name`'=>'刷新点配置'))->getOne();

        if($res&&!empty($res)){

            $return = array('status'=>'200','msg'=>'获取刷新点配置成功','data'=>$res);
        }else{
            $return = array('status'=>'201','msg'=>'获取刷新点配置失败');
        }

        return $return;
    }

    /**
     * 获取vip配置
     * @return array
     */
    public function getVipIni(){

        $res = $this->table('base_ini')->where(array('`name`'=>'经销商VIP配置'))->getOne();

        if($res&&!empty($res)){

            $return = array('status'=>'200','msg'=>'获取经销商VIP配置成功','data'=>$res);
        }else{
            $return = array('status'=>'201','msg'=>'加载页面失败');
        }

        return $return;
    }

    /**
     * 获取配置城市数据
     * @param $key
     * @return array
     */
    public function getCityIni($key){
        $res = $this->table('base_ini')->where(array('`name`'=>'服务城市配置'))->getOne();

        $citys = array();

        if($res&&!empty($res)){
            $VAL = json_decode($res['value']);
            foreach ($VAL as $k=>$item){
                if($key){
                    if(strstr($item,$key)){
                        $citys[]['zh'] = $item;
                    }
                } else{
                    $citys[]['zh'] = $item;
                }
            }

            $return = array('status'=>'200','msg'=>'获取服务城市配置成功','data'=>$citys);
        }else{
            $return = array('status'=>'201','msg'=>'获取数据失败');
        }

        return $return;
    }

    /**
     * 获取vip配置
     * @return array
     */
    public function getServerTelQq(){

        $res = $this->table('base_ini')->where(array('`name`'=>'客服电话QQ配置'))->getOne();

        if($res&&!empty($res)){

            $return = array('status'=>'200','msg'=>'获取客服电话QQ配置成功','data'=>$res);
        }else{
            $return = array('status'=>'201','msg'=>'客服电话QQ配置失败');
        }

        return $return;
    }


    /**
     *  次数转换成文字
     * @param $count
     * @return int|string
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

}