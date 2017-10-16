<?php

/**
 * 经销商模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10
 * Time: 11:34
 */
class PlatFirmsFirmsModel extends Model
{
    //==========经销商列表页操作=====================
    /**
     * 获取厂商列表
     * @param $data
     * @return array
     */
    public function getFirms($data){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $pages = ($data['page']-1)* $data['pageSize'];

        $find = 'a.id!=0' ;
        if($suppProv){
            $find  .= ' and a.province ="'.$suppProv.'"';
        }
        if($data['status']){//状态
            $find  .= ' and a.status ='.$data['status'];
        }
        if($data['type']){//类型
            $find  .= ' and a.`type` ='.$data['type'];
        }
        if($data['cfn']){//类型
            $find  .= ' and a.`classification` ='.$data['cfn'];
        }
        if($data['province'] && $data['province'] != '全部'){//省
            $province = str_replace(' ','',$data['province']);
            $find  .= ' and a.province like "%'.$province.'%"';
        }
        if($data['city'] && $data['city'] != '全部'){//市
            $city   = str_replace(' ','',$data['city']) ;
            $find  .= ' and a.city like "%'.$city.'%"';
        }
        if($data['county'] && $data['county'] != '全部'){//区
            $county = str_replace(' ','',$data['county']) ;
            $find  .= ' and a.district like "%'.$county.'%"';
        }
        if($data['is_check']){//认证状态
            $find  .= ' and a.is_check ='.$data['is_check'];
        }
        if($data['sale']){//是否关联业务员

            if($data['sale'] == 1){
                //$find  .= ' and (salesman_ids is not null or salesman_ids<>"" )';
                $find .= ' AND b.id is not null' ;
            }else{
                //$find  .= ' and (salesman_ids is null or salesman_ids="" )';
                $find .= ' AND b.id is null' ;
            }
        }
        if($data['is_vip']){//VIP
            $find  .= ' and a.is_vip ='.$data['is_vip'];
        }

        if($data['keywords']){//关键字
            $findKey = '"%'.$data['keywords'].'%"';
            $find .= " and (a.EnterpriseID like $findKey or a.companyname like $findKey or a.phone like $findKey)";
        }
        $date   = date('Y-m-d H:i:s',time());
        $join  = ' LEFT JOIN firms_sales_user b on a.id=b.firms_id AND DATE_FORMAT(b.end_time,"%Y-%m-%d")>"'.$date.'"' ;
        $field = 'a.id,a.EnterpriseID,a.uname,a.phone,a.companyname,a.type,a.classification,a.province,a.city,a.district';
        $field.= ',a.is_check,a.salesman_ids,a.is_vip,a.refresh_point,a.last_time,a.status,b.id as bId' ;

        $count = $this->table('firms a')->jion($join)->where($find)->count();
        $lists = $this->table('firms a')
            ->jion($join)
            ->field($field)
            ->where($find)
            ->order(array('a.create_time'=>'desc','a.id'=>'asc'))
            ->limit($pages,$data['pageSize'])->get();
        //writeLog($this->lastSql());
        if($lists){
            //搜索条件
            $search  = array();
            $data    = array('list'=>$lists,'search'=>$search,'count'=>$count,'page'=>$data['page'],'pageSize'=>$data['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有符合条件的经销商');
        }
        return $data;
    }

    /**
     * 添加、编辑厂商
     * @param $d
     * @return mixed
     */
    public function saveFirm($d){
        $data = array() ;
        $time = date('Y-m-d H:i:s',time());
        $data['classification'] = $d['cfn'];
        $data['business'] = $d['business'];
        $data['companyname'] = $d['comName'];
        $data['address'] = $d['address'];
        $data['coordinate'] = $d['position'];
        $data['longitude'] = $d['lng'];
        $data['latitude'] = $d['lat'];
        $data['face_pic'] = $d['face_pic'];
        $data['major'] = $d['major'];
        $data['linkMan'] = $d['linkMan'];
        $data['linkPhone'] = $d['linkPhone'];
        $data['linkTel'] = $d['linkTel'];
        $data['qq'] = $d['qq'];
        $data['update_time'] = $time;


        if($d['id']){
            $data['is_showfactry'] = $d['factry'];
            $data['wechat_pic'] = $d['wx_pic'];
            $data['info'] = $d['info'];
            $res = $this->table('firms')->where(array('id'=>$d['id']))->update($data);

            if($res){
                $this->table('firms_banner')->where(array('firms_id'=>$d['id']))->del();
                if($d['com_banner']){
                    foreach ($d['com_banner'] as $v){
                        $this->table('firms_banner')->insert(array('firms_id'=>$d['id'],'banner_url'=>$v));
                    }
                }

            }
            $action = '编辑厂商信息';
        }else{

            $data['phone']          = $d['phone'];
            $enterpriseID           = $this->getEnterpriseID();
            $invite_code            = $this->makeYQ();
            $data['EnterpriseID']   = $enterpriseID;
            $data['password']       = md5(sha1('7777777').'sw');
            $data['type']           = $d['type'];
            $data['uname']          = $d['comName'];
            $data['province']       = $d['province'];
            $data['city']           = $d['city'];
            $data['district']       = $d['county'];
            $data['invite_code']    = $invite_code;
            $data['create_time']    = $time;

            //默认值
            $data['status']         = 1;
            $data['is_vip']         = 2;
            $data['is_check']       = 1;
            $data['refresh_point']  = 0;
            $data['is_showfactry']  = 2;
            $data['is_sales']       = 0;


            if($d['type'] == 2){
                $data['scale'] = $d['scale'];
            }

            $res = $this->table('firms')->insert($data);
            $action = '创建厂商' ;
        }

        if($res){
            //记录日志
            $suUser = G('user') ;

            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res;

    }
    /**
     * 启用/停用厂商帐号
     * @param $comId
     * @param $status
     * @return int
     */
    public function changeStatus($comId,$status){
        $data = array();
        $data['status'] = $status;
        $result = $this->table('firms')->where(array('id'=>$comId))->update($data);
        if($result){
            //记录日志
            $suUser = G('user') ;
            $action = $status == 1 ? '启用厂商帐号' : '停用厂商帐号';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $result;
    }

    /**
     * 启用/停用厂商帐号
     * @param $comId
     * @return int
     */
    public function resetPassword($d){
        $comId  = $d['id'] ;
        $data   = array();
        $data['password'] = md5(sha1('7777777').'sw');;
        $result = $this->table('firms')->where(array('id'=>$comId))->update($data);
        if($result){
            //记录日志
            $suUser = G('user') ;
            $action = '重置厂商密码';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $result;
    }


    /**
     * 生成 企业ID
     * @return bool|int|string
     */
    public function getEnterpriseID(){
        $zTime = 1495101488;
        $_time = time();
        $_time = substr($_time, 2);
        $rst = $this->table('firms')->where(array('EnterpriseID'=>$_time))->getOne();
        if($rst){
            //$nTime = $zTime - mt_rand(1,10000000) - mt_rand(1,10000000);
            $this->getEnterpriseID();
        }else{
            return $_time;
        }
    }

    //生成唯一的邀请码
    public function makeYQ(){
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)] .strtoupper(dechex(date('m'))) .date('d').substr(time(),-5) .substr(microtime(),2,5) .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        $rst = $this->table('firms')->where(array('invite_code'=>$d))->getOne();
        if($rst){
            $this->makeYQ();
        }else{
            return $d;
        }
    }

    /**
     * 获取车系分类
     * @param $type
     * @return mixed
     */
    public function getCarGroup($type){
        $where = 'type='.$type .' and (level=1 or level=2)' ;
        $CarGroup = $this->table('car_group')
            ->field('id,pid,level,name,img')
            ->where($where)
            ->order(array('id'=>'asc','type'=>'asc'))->get();
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

    //==========经销商详情页操作=====================
    //-------------基本信息-------------------------
    /**
     * 获取一家厂商详情
     * @param $id
     * @return mixed
     */
    public function getOneFirm($id){
        $join    = 'left join firms_banner b on a.id=b.firms_id' ;

        $field   = 'a.id,a.companyname,a.type,a.classification,a.business,a.is_showfactry,a.face_pic,a.major';
        $field  .= ',a.linkMan,a.linkPhone,a.linkTel,a.qq,a.wechat_pic,a.coordinate,a.longitude,a.latitude';
        $field  .= ',a.address,a.info,a.create_time,a.last_time,a.is_check,a.refresh_point,a.is_vip,a.vip_time';
        $field  .= ',a.city,a.scale';
        $field  .= ',GROUP_CONCAT(b.banner_url SEPARATOR ",") as banner';
        $firm    = $this->table('firms a')
            ->field($field)
            ->jion($join)
            ->where(array('a.id'=>$id))
            ->group('a.id')
            ->getOne();

        if($firm){
            $firm['management'] = '' ;
            if($firm['type'] == 1){
                $businessId = $firm['business'] ;
                if($businessId){
                    $businessId = trim($businessId,',') ;
                    $business = $this->table('car_group a')
                        ->field("GROUP_CONCAT(CONCAT(b.`name`,'/',a.`name`) SEPARATOR ' ') as `name`")
                        ->jion('LEFT JOIN car_group as b on a.pid = b.id')
                        ->where('a.id in ('.$businessId.') and a.`level` = 2')->getOne();
                    $firm['management'] = $business['name'] ;
                }
            }

        }
        //writeLog($this->lastSql());
        return $firm ;
    }

    //-------------产品信息-------------------------
    /**
     * 产品列表
     * @param $data
     * @return array
     */
    public function getFirmPros($data){
        $pages = ($data['page']-1)* $data['pageSize'];

        $where = 'firms_id='.$data['firmId'] ;
        if($data['proStatus']){
            $where .= ' and a.pro_status='.$data['proStatus'] ;
        }
        if($data['proType']){
            $where .= ' and a.pro_type="'.$data['proType'].'"' ;
        }
        if($data['proCateLv1']){
            $where .= ' and a.pro_cate_1='.$data['proCateLv1'] ;
        }
        if($data['proCateLv2']){
            $where .= ' and a.pro_cate_2='.$data['proCateLv2'] ;
        }
        if($data['keywords']){
            $where .= ' and (a.proId like "%'.$data['keywords'] .'%" or a.proName like "%'.$data['keywords'] .'%" )';
        }

        $count  = $this->table('product_list a')->where($where)->count();
        $join   = ' left join product_category b on a.pro_cate_1=b.id' ;
        $join  .= ' left join product_category c on a.pro_cate_2=c.id' ;

        $field  = 'a.id,a.proId,a.proName,a.pro_type,a.pro_refresh,a.pro_status';
        $field .= ',b.name as cate_name1';
        $field .= ',c.name as cate_name2';
        $lists  = $this->table('product_list a')
            ->field($field)
            ->jion($join)
            ->where($where)
            ->limit($pages,$data['pageSize'])
            ->get();

        if($lists){
            //搜索条件
            //$search  = array('status'=>$status,'keywords'=>$keywords);
            $data    = array('list'=>$lists,'search'=>'','count'=>$count,'page'=>$data['page'],'pageSize'=>$data['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的产品');
        }

        return $data ;
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

        $field  = 'a.pro_pic,a.proName,a.pro_no,a.pro_brand,a.pro_cate_1,a.pro_cate_2' ;
        $field .= ',a.car_group,a.pro_weight,a.pro_area,a.pro_spec,a.pro_text' ;
        $field .= ',b.name as cate_name1';
        $field .= ',c.name as cate_name2';
        $field .= ',d.EnterpriseID ,d.companyname ,d.type as firmType';

       return $this->table('product_list a')
           ->field($field)
           ->jion($join)
           ->where(array('a.id'=>$id))
           ->getOne();
    }

    /**
     * 产品分类列表
     * @return mixed
     */
    public function getProCate(){
        return $this->table('product_category')->field('id,name,level,pid')->where('level=1 or level=2')->order('id asc')->get();
    }

    //-------------认证信息-------------------------
    /**
     * 认证信息列表
     * @param $ComId
     * @return mixed
     */
    public function getCheckData($ComId){
        return $this->table('firms_check')
            ->field('id,firmsName,firmsMan,province,city,district,firmsTel,address,licence_pic,taxes_pic,field_pic,brand_pic,agents_pic,create_time,status')
            ->where('firms_id='.$ComId)
            ->order('create_time desc')
            ->get();
    }

    /**
     * 获取一条认证信息
     * @param $id
     * @return mixed
     */
    public function getOneCheck($id){
        return $this->table('firms_check')
            ->field('id,firmsName,firmsMan,province,city,district,firmsTel,address,licence_pic,taxes_pic,field_pic,brand_pic,agents_pic,status,reason')
            ->where('id='.$id)
            ->getOne();
    }

    /**
     * 保存认证信息
     * @param $d
     * @return bool
     */
    public function saveOneCheck($d){
        if($d['id']){
            $data = array(
                'firmsName'=>$d['firmsName'],
                'firmsMan'=>$d['firmsMan'],
                'province'=>$d['province'],
                'city'=>$d['city'],
                'district'=>$d['county'],
                'firmsTel'=>$d['firmsTel'],
                'address'=>$d['address'],
                'licence_pic'=>$d['licence_pic'],
                'taxes_pic'=>$d['taxes_pic'],
                'field_pic'=>$d['field_pic'],
                'brand_pic'=>$d['brand_pic'],
                'agents_pic'=>$d['agents_pic'],
            );
            $res  = $this->table('firms_check')->where(array('id'=>$d['id']))->update($data) ;
            if($res){
                //记录日志
                $suUser = G('user') ;
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],'编辑认证资料') ;
            }
        }else{
            $res = false ;
        }

        return $res ;
    }


    //-------------VIP记录-------------------------

    public function getVipLog($d){
        $pages = ($d['page']-1)* $d['pageSize'];
        $find  = array('firms_id'=>$d['firmId'],'type'=>1) ;
        $field = 'money,refresh_point,payway,status,create_time';
        $count = $this->table('pay_history')->where($find)->count();
        $lists = $this->table('pay_history')->field($field)->where($find)->order(array('create_time'=>'desc','id'=>'asc'))->limit($pages,$d['pageSize'])->get();
        //writeLog($this->lastSql());
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有充值记录');
        }
        return $data;
    }

    //------------刷新点记录-------------------------
    public function getRefreshLog($d){
        $pages = ($d['page']-1)* $d['pageSize'];
        $find  = 'firms_id='.$d['firmId'] .' and type in (2,3,4) ';//array('firms_id'=>$d['firmId'],'type'=>1) ;
        $field = 'type,money,refresh_point,info,payway,status,create_time';
        $count = $this->table('pay_history')->where($find)->count();
        $lists = $this->table('pay_history')->field($field)->where($find)->order(array('create_time'=>'desc','id'=>'asc'))->limit($pages,$d['pageSize'])->get();
        //writeLog($this->lastSql());
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有充值记录');
        }
        return $data;
    }

    //-------------来访记录-------------------------
    public function getVisitComeLog($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $find   = 'a.firms_id>0 and a.to_firms_id='.$d['firmId'] ;
        if($d['keywords']){
            $find  .= ' and (b.EnterpriseID like "%'.$d['keywords'].'%" or b.companyname like "%'.$d['keywords'].'%" or b.phone like "%'.$d['keywords'].'%")' ;
        }

        $countSql   = 'SELECT COUNT(1) as count FROM(' ;
        $countSql  .= ' SELECT firms_id,to_firms_id FROM firms_visit_log' ;
        $countSql  .= ' UNION ALL SELECT firms_id,to_firms_id FROM firms_call_log) a ' ;
        $countSql  .= ' LEFT JOIN firms b ON a.firms_id=b.id where '.$find ;

        $count      = $this->getOne($countSql);
        //writeLog($this->lastSql());
        $count      = $count['count'] ;

        $field      = 'a.create_time,a.visit_type,a.visit,';
        $field     .= 'b.EnterpriseID,b.uname,b.companyname,b.phone,b.type,b.classification,b.is_vip';
        $dataSql    = 'SELECT '.$field .' FROM(';
        $dataSql   .= ' SELECT firms_id,create_time,visit_type,to_firms_id,1 as visit FROM firms_visit_log' ;
        $dataSql   .= ' UNION ALL SELECT firms_id,create_time,visit_type,to_firms_id,2 as visit FROM firms_call_log) a' ;
        $dataSql   .= ' LEFT JOIN firms b ON a.firms_id=b.id where '.$find.' LIMIT '.$pages.','.$d['pageSize'] ;
        $lists      = $this->get($dataSql);

        //writeLog($this->lastSql());
        /*$field  = 'a.create_time,a.visit_type,';
        $field .= 'b.EnterpriseID,b.uname,b.companyname,b.phone,b.type,b.classification,b.is_vip';

        $join   = ' left join firms b on a.firms_id=b.id';


        $count  = $this->table('firms_visit_log a')->jion($join)->where($find)->count();
        $lists  = $this->table('firms_visit_log a')
            ->field($field)->where($find)
            ->jion($join)
            ->order(array('a.create_time'=>'desc','a.id'=>'asc'))
            ->limit($pages,$d['pageSize'])
            ->get();*/
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有来访记录');
        }
        return $data;

    }
    //-------------访问记录-------------------------
    public function getVisitGoLog($d){

        $pages  = ($d['page']-1)* $d['pageSize'];
        $find   = 'a.firms_id='.$d['firmId'] ;
        if($d['keywords']){
            $find  .= ' and (b.EnterpriseID like "%'.$d['keywords'].'%" or b.companyname like "%'.$d['keywords'].'%" or b.phone like "%'.$d['keywords'].'%")' ;
        }

        $countSql   = 'SELECT COUNT(1) as count FROM(' ;
        $countSql  .= ' SELECT firms_id,to_firms_id FROM firms_visit_log' ;
        $countSql  .= ' UNION ALL SELECT firms_id,to_firms_id FROM firms_call_log) a ' ;
        $countSql  .= ' LEFT JOIN firms b ON a.to_firms_id=b.id where '.$find ;

        $count      = $this->getOne($countSql);
        $count      = $count['count'] ;

        $field      = 'a.create_time,a.visit_type,a.visit,';
        $field     .= 'b.EnterpriseID,b.uname,b.companyname,b.phone,b.type,b.classification,b.is_vip';
        $dataSql    = 'SELECT '.$field .' FROM(';
        $dataSql   .= ' SELECT firms_id, create_time,visit_type,to_firms_id,1 as visit FROM firms_visit_log' ;
        $dataSql   .= ' UNION ALL SELECT firms_id, create_time,visit_type,to_firms_id,2 as visit FROM firms_call_log) a' ;
        $dataSql   .= ' LEFT JOIN firms b ON a.to_firms_id=b.id where '.$find.' LIMIT '.$pages.','.$d['pageSize'] ;
        $lists      = $this->get($dataSql);
        //writeLog($this->lastSql());
        /*$field  = 'a.create_time,a.visit_type,';
        $field .= 'b.EnterpriseID,b.uname,b.companyname,b.phone,b.type,b.classification,b.is_vip';

        $join   = ' left join firms b on a.to_firms_id=b.id';
        $count  = $this->table('firms_visit_log a')->jion($join)->where($find)->count();
        $lists  = $this->table('firms_visit_log a')
            ->field($field)->where($find)
            ->jion($join)
            ->order(array('a.create_time'=>'desc','a.id'=>'asc'))
            ->limit($pages,$d['pageSize'])
            ->get();*/
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有来访记录');
        }
        return $data;

    }
    //-------------求购记录-------------------------
    public function getWantBuyLog($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $find   = 'a.is_delete=0 and a.firms_id='.$d['firmId'] ;

        if($d['status']){
            $find  .= ' and a.status='.$d['status'] ;
        }

        if($d['keywords']){
            $find  .= ' and ( a.id like "%'.$d['keywords'].'%" )' ;
        }

        $field  = 'a.id,a.status,a.create_time';

        $count  = $this->table('want_buy a')->where($find)->count();
        $lists  = $this->table('want_buy a')
            ->field($field)->where($find)
            ->order(array('a.create_time'=>'desc','a.id'=>'asc'))
            ->limit($pages,$d['pageSize'])
            ->get();
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有求购记录');
        }
        return $data;
    }

    /**
     * 采购详情
     * @param $id
     * @return mixed
     *
     */
    public function getOneWantBuy($id){

        $join = ' left join firms b on a.firms_id=b.id' ;

        $field  = 'a.id,a.frame_number,a.limitation,a.vin_pic,a.memo,a.create_time,a.car_group_id' ;
        $field .= ',b.companyname' ;
        return $this->table('want_buy a')
            ->field($field)
            ->jion($join)
            ->where('a.is_delete=0 and a.id='.$id)
            ->getOne();
    }

    /**
     * 返回车系分组名称
     * @param $id
     * @param string $name
     * @return bool|string
     */
    public function getOneCarGroup($id,$name=''){
        $res   = $this->table('car_group')->field('pid,name')->where('id='.$id)->getOne();
        //dump($res);
        if($res){
            $name = $res['name'] . '/' . $name;
            if($res['pid'] > 0){
                $res   = $this->table('car_group')->field('pid,name')->where('id='.$res['pid'])->getOne();
                if($res){
                    $name = $res['name'] . '/' . $name;

                    if($res['pid'] > 0){
                        $res   = $this->table('car_group')->field('pid,name')->where('id='.$res['pid'])->getOne();
                        if($res){
                            $name = $res['name'] . '/' . $name;
                            if($res['pid'] > 0){
                                $res   = $this->table('car_group')->field('pid,name')->where('id='.$res['pid'])->getOne();
                                $name = $res['name'] . '/' . $name;
                                return $name ;
                            }else{
                                return $name ;
                            }
                        }else{
                            return $name ;
                        }
                    }else{
                        return $name ;
                    }
                }else{
                    return $name ;
                }
            }else{
                return $name ;
            }
        }else{
            return $name ;
        }
    }

    /**
     * 采购详情管理图片
     * @param $id : want_buy表ID
     * @return mixed
     */
    public function getWantBuyPic($id){
        return $this->table('want_buy_pic')
            ->field('pic_url')
            ->where('want_buy_id='.$id)
            ->get();
    }
    /**
     * 采购详情的 采购清单
     * @param $id : want_buy表ID
     * @return mixed
     */
    public function getBuyChildList($id){
        $find   = 'a.want_buy_id='.$id ;

        $join   = ' left join product_category b on a.pro_cate1=b.id' ;
        $join  .= ' left join product_category c on a.pro_cate2=c.id' ;

        $field  = 'a.id,a.amount,a.list_memo';
        $field .= ',b.name as cate_name1';
        $field .= ',c.name as cate_name2';

        $lists  = $this->table('want_buy_list a')
            ->field($field)->where($find)
            ->jion($join)
            ->order(array('a.id'=>'asc'))
            ->get();
        return $lists ;
    }
    //-------------圈子记录-------------------------
    /**
     * 获取关联圈子
     * @param $d
     * @return array
     */
    public function getCircleLog($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $where = 'level=1 and type=1 and fu_id='.$d['firmId'] ;
        if($d['keywords']){
            $where    .= ' and vid like "%'.$d['keywords'].'%"' ;
        }
        $count = $this->table('circle')->where($where)->count();
        $lists = $this->table('circle')
            ->field('id,vid,content,comments,create_time,area')
            ->where($where)
            ->limit($pages,$d['pageSize'])
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            //搜索条件
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的圈子记录');
        }
        return $data ;
    }

    //-------------邀请信息-------------------------
    public function getInviteLog($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $find   = 'a.fu_id='.$d['firmId'] ;

        if($d['keywords']){
            $find  .= ' and (b.EnterpriseID like "%'.$d['keywords'].'%" or b.companyname like "%'.$d['keywords'].'%" or b.phone like "%'.$d['keywords'].'%")' ;
        }

        $field  = 'a.create_time,';
        $field .= 'b.EnterpriseID,b.companyname,b.phone';
        $join   = ' left join firms b on a.firms_id=b.id' ;
        $count  = $this->table('invite_log a')->jion($join)->where($find)->count();

        $lists  = $this->table('invite_log a')
            ->field($field)->where($find)
            ->jion($join)
            ->order(array('a.create_time'=>'desc','a.id'=>'asc'))
            ->limit($pages,$d['pageSize'])
            ->get();
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有邀请记录');
        }
        return $data;
    }
    //------------关联业务员-------------------------

    public function getSaleLog($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $date   = date('Y-m-d H:i:s',time());
        $find   = 'a.firms_id='.$d['firmId'] . ' and DATE_FORMAT(a.end_time,"%Y-%m-%d") >"'. $date .'"';

        if($d['keywords']){
            $find  .= ' and (b.uId like "%'.$d['keywords'].'%" or b.uname like "%'.$d['keywords'].'%" or b.phone like "%'.$d['keywords'].'%")' ;
        }

        $field  = 'a.create_time,';
        $field .= 'b.uId,b.uname,b.phone';
        $join   = ' left join sales_user b on a.sales_user_di=b.id' ;
        $count  = $this->table('firms_sales_user a')->jion($join)->where($find)->count();

        $lists  = $this->table('firms_sales_user a')
            ->field($field)->where($find)
            ->jion($join)
            ->order(array('a.create_time'=>'desc','a.id'=>'asc'))
            ->limit($pages,$d['pageSize'])
            ->get();
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有关联记录');
        }
        return $data;
    }


    /**
     * 获取一个月内VIP到期列表
     * @param $page
     * @param $pageSize
     * @param $status
     * @param $keywords
     * @return array
     */
    public function getVipList($page,$pageSize,$status,$keywords){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        //起始条数
        $pages = ($page-1)* $pageSize;

        $time1 = date('Y-m-d 23:59:59',strtotime(' -1 day')) ;
        $time2 = date('Y-m-d 23:59:59',strtotime(' +1 Months')) ;
        $find  = 'a.type=1 and a.is_vip=1 and a.vip_time>"'.$time1.'" and a.vip_time<"'.$time2.'"';

        if($status){//状态
            $find  .= ' and a.status ='.$status;
        }
        if($keywords){//关键字
            $findKey = '"%'.$keywords.'%"';
            $find .= " and (a.`companyname` like $findKey )";
        }
        if($suppProv){
            $find .= ' and b.area ="'.$suppProv.'"';
        }

        $field = 'a.id,a.EnterpriseID,a.phone,a.companyname,a.type,a.classification,a.province,a.city,a.district,a.vip_time,a.last_time';

        $join  = ' left join sales_user b on a.salesman_ids=b.id' ;//业务员

        $count = $this->table('firms a')->jion($join)->where($find)->count();
        $lists = $this->table('firms a')->jion($join)->field($field)->where($find)->order(array('a.id'=>'asc'))->limit($pages,$pageSize)->get();
        //writeLog($this->lastSql());
        if($lists){
            //搜索条件
            $search  = array('status'=>$status,'keywords'=>$keywords);
            $data    = array('list'=>$lists,'search'=>$search,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>'fail','massage'=>'暂时没有符合条件的经销商');
        }

        return $data;
    }


}