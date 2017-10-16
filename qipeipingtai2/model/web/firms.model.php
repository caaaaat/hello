<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/16
 * Time: 11:29
 */
class WebFirmsModel extends Model
{
    public function getFirms($type,$classification,$business,$categorise,$keyword,$page,$pageSize=4,$currentCity='成都市'){
        $start = ($page-1)*$pageSize;
        $where = 'status=1 and is_check=1';
        if($type){
            $where .= " and type = {$type}";
        }
        if($classification){
            $where .= " and classification = {$classification}";
        }
        if($business){
            $where .= " and business like '%,{$business},%' ";
        }
        if($categorise){
            $sql = '';
            $or  = '';
            foreach ($categorise as $v){
                $sql .= $or.' business like "%,'.$v.',%" ';
                $or   = ' or ';
            }
            if($sql){
                $where .= ' and ( '.$sql.' )';
            }
        }
        if($keyword){
            $where .= ' and ( companyname like "%'.$keyword.'%" or major like "%'.$keyword.'%" or address like "%'.$keyword.'%" )';
        }
        if($currentCity){
            $where .= ' and city like "%'.$currentCity.'%" ';
        }

        $count = $this->table('firms')->where($where)->count();
        $data  = $this->table('firms')
            ->field('id,EnterpriseID,face_pic,companyname,major,province,city,district,address,longitude,latitude,wechat_pic,is_vip,vip_time,is_check,QR_pic')
            ->where($where)
            ->order('vip_time desc,rand()')->limit($start,$pageSize)->get();
        foreach($data as $k=>$v){
            if(strtotime($v['vip_time'])>time()){
                $data[$k]['is_vip'] = 1;
            }else{
                $data[$k]['is_vip'] = 0;
            }
            $data[$k]['face_pic'] = $v['face_pic']?$v['face_pic']:'/images/pub/face_pic.png';
            //$data[$k]['QR_pic'] = $v['QR_pic']?$v['QR_pic']:'/images/pub/QR_pic.png';
        }

        return array('list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);

    }

    //获取推荐经销商
    public function getTheDealers($currentCity){
        $where = 'type=1 and is_sales=1';
        if($currentCity){
            $where .= ' and city like "%'.$currentCity.'%" ';
        }
        $res = $this->table('firms')
            ->field('id,EnterpriseID,companyname,face_pic,major')
            ->where($where)
            ->order('is_vip asc,refresh_time desc,refresh_point desc')->get();
        return $res;
    }
    //获取厂商详情id
    public function getFirmInfo($id){
        $res = $this->table('firms')->where(array('id'=>$id))->getOne();
        if($res){
            $res['face_pic'] = $res['face_pic']?$res['face_pic']:'/images/pub/face_pic.png';
            $res['wechat_pic'] = $res['wechat_pic']?$res['wechat_pic']:'/images/pub/QR_pic.png';
        }
        return $res;
    }
    //获取厂商通过企业ID
    public function getFirmInfoByEnID($EnterpriseID){
        $res = $this->table('firms')->where(array('EnterpriseID'=>$EnterpriseID))->getOne();
        if($res){
            $res['face_pic'] = $res['face_pic']?$res['face_pic']:'/images/pub/face_pic.png';
            $res['wechat_pic'] = $res['wechat_pic']?$res['wechat_pic']:'/images/pub/QR_pic.png';
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
            //获取访问数据
            $res['visit_num'] = $this->table('firms_visit_log')->where("to_firms_id={$res['id']} and firms_id <>0")->count();
            //获取来电数据
            $res['call_num']  = $this->table('firms_call_log')->where("to_firms_id={$res['id']} and firms_id <>0")->count();
            //获取认证信息
            $res['renzheng_info'] = $this->table('firms_check')->where(array('firms_id'=>$res['id'],'status'=>2))->order('create_time desc')->getOne();
            //名片
            $res['card'] = $this->table('firms_card')->where(array('firms_id'=>$res['id']))->order('create_time desc')->getOne();
        }
        return $res;
    }
    //修改厂商数据
    public function changeFirm($id,$data){
        $res = $this->table('firms')->where(array('id'=>$id))->update($data);
        return $res;
    }
    //获取vip充值记录
    public function getVipHistory($firmId,$page,$pageSize){
        $start = ($page-1)*$pageSize;
        $count = $this->table('pay_history')->where(array('type'=>1,'firms_id'=>$firmId))->count();
        $res   = $this->table('pay_history')->where(array('type'=>1,'firms_id'=>$firmId))->order('create_time desc')->limit($start,$pageSize)->get();
        return array('list'=>$res,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }
    //获取刷新点记录
    public function getRefreshHistory($firmId,$page,$pageSize){
        $start = ($page-1)*$pageSize;
        $count = $this->table('pay_history')->where('type<>1 and firms_id='.$firmId)->count();
        $res   = $this->table('pay_history')->where('type<>1 and firms_id='.$firmId)->order('create_time desc')->limit($start,$pageSize)->get();
        $paywayArr = array('','微信支付','支付宝支付','人工收费','刷新产品','刷新店铺','邀请获得','官方赠送');
        $return    = array();
        if($res){
            foreach ($res as $k=>$v){
                $arr = array();
                if ($v['type']==2){
                    $arr['str1'] = '购买'.($v['status']==1?'成功':'失败');
                }elseif($v['type']==3){
                    $arr['str1'] = '刷新点消费';
                }elseif($v['type']==4){
                    $arr['str1'] = '获取刷新点';
                }else{
                    $arr['str1'] = '';
                }
                $arr['create_time'] = $v['create_time'];
                $arr['info']  = $v['info'];
                $arr['money'] = $v['money'];
                $arr['refresh_point'] = $v['refresh_point'];
                $arr['payStr'] = $paywayArr[$v['payway']];
                $return[] = $arr;
            }
        }
        return array('list'=>$return,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }
    //查询我邀请厂商信息
    public function getInInviteMe($myFirmId){
        $res = $this->table('invite_log as a')
            ->field('b.id,b.uname,b.EnterpriseID,b.companyname')
            ->where(array('a.firms_id'=>$myFirmId,'a.type'=>1))
            ->jion('left join firms as b on b.id=a.fu_id')->getOne();
        return $res;
    }
    //绑定邀请人
    public function bindInviteId($myFirmId,$inviteCode){
        //查询是否已绑定
        $res = $this->getInInviteMe($myFirmId);
        if($res){
            $return = array('status'=>0,'msg'=>'已经绑定了邀请人，请刷新页面');
        }else{
            $firmId = $this->table('firms')->where(array('invite_code'=>$inviteCode))->getOne();
            if($firmId){
                if($myFirmId == $firmId['id']){
                    $return = array('status'=>0,'msg'=>'不能绑定自己的邀请码');
                }else{
                    //获取分享送刷新点配置
                    $iniMo = model('web.ini','mysql');
                    $point = $iniMo->getInviteInfo();
                    $invitation = isset($point['invitation'])?$point['invitation']:'0';
                    $invited = isset($point['invited'])?$point['invited']:'0';

                    $rst = $this->table('invite_log')->insert(array('type'=>1,'fu_id'=>$firmId['id'],'firms_id'=>$myFirmId,'create_time'=>date('Y-m-d H:i:s',time())));
                    if($rst){
                        if($invitation){
                            $sql1 = "update firms set refresh_point=refresh_point+{$invitation} where id={$firmId['id']}";
                            $res1 = $this->query($sql1);
                            if($res1){
                                $this->table('pay_history')->insert(array('type'=>4,'status'=>1,'info'=>"邀请厂商获得{$invitation}刷新点",'payway'=>6,'refresh_point'=>$invitation,'firms_id'=>$firmId['id'],'create_time'=>date('Y-m-d H:i:s',time())));
                            }
                        }
                        if($invited){
                            $sql2 = "update firms set refresh_point=refresh_point+{$invited} where id={$myFirmId}";
                            $res2 = $this->query($sql2);
                            if($res2){
                                $this->table('pay_history')->insert(array('type'=>4,'status'=>1,'info'=>"填写厂商邀请码获得{$invited}",'payway'=>6,'refresh_point'=>$invited,'firms_id'=>$myFirmId,'create_time'=>date('Y-m-d H:i:s',time())));
                            }
                        }
                        $return = array('status'=>1,'msg'=>'绑定邀请人成功');
                    }else{
                        $return = array('status'=>0,'msg'=>'绑定邀请人失败');
                    }
                }
            }else{
                $return = array('status'=>0,'msg'=>'该邀请码的厂商不存在，请重新输入邀请码');
            }
        }

        return $return;
    }

    /**
     * @param $data         数据
     * @param $companyId    商家id
     */
    public function editFirm($data,$companyId){
        $data1 = $this->table('firms')->where('id='.$companyId)->getOne();
        $retrun = 0;
        if($data1){
            $firms['face_pic']  = $data['face_pic']; //封面
            $firms['major']     = $data['major']; //主营
            $firms['linkMan']   = $data['linkMan']; //联系人
            $firms['linkPhone'] = $data['linkPhone']; //联系人手机号码
            $firms['linkTel']   = $data['linkTel']; //座机号码
            $firms['qq']         = $data['qq']; //qq号码
            $firms['wechat_pic']= $data['wechat_pic']; //微信二维码
            $firms['longitude'] = $data['longitude']; //经度
            $firms['latitude']  = $data['latitude']; //纬度
            $firms['address']   = $data['address']; //详细地址
            $firms['info']      = $data['info'];    //详细地址
            $firms['QR_pic']    = $this->getQRStore($data1['EnterpriseID'],$data1['companyname'],$data1['type']); //店铺二维码
            $firms['update_time']= date("Y-m-d H:i:s");
            $rst = $this->table('firms')->where('id='.$companyId)->update($firms);  //修改店铺信息
            if($rst > 0){
                $retrun = 1;
                $del    = $this->table('firms_banner')->where('firms_id='.$companyId)->del();
                if($data['firms_banner']){
                    $banner = explode(',',$data['firms_banner']);
                    for($i=0; $i<count($banner); ++$i){
                        $firms_banner = [];
                        $firms_banner['firms_id'] = $companyId;
                        $firms_banner['banner_url'] = $banner[$i];
                        $result = $this->table('firms_banner')->insert($firms_banner);
                        if($result <= 0){
                            $retrun = 0;
                        }
                    }
                }
            }
        }

        return $retrun;
    }

    /**
     * @param $companyId    商家id
     * 返回商家详细信息，及商家认证信息
     */
    public function firmInfo($companyId){
        $field = 'a.*,b.licence_pic,b.taxes_pic,b.field_pic,b.brand_pic,b.agents_pic,b.audit_time,b.status as aubitStatus';
        $data = $this->table('firms as a')
            ->where('a.id='.$companyId)
            ->field($field)
            ->jion('left join firms_check as b on a.id=b.firms_id')
            ->getOne();

        return $data;
    }

    /**
     * @param $page
     * @param $pageSize
     * @param $wordsKey     商家名称关键字查询
     * @param $province     省
     * @param $city         市
     * @param $district     区
     * @param $classification 类型筛选(4.修理厂  5.快修保养 6.美容店)
     * @return mixed
     */
    public function showCarMend($page,$pageSize,$wordsKey,$province,$city,$district,$classification){
        $start = ($page-1)*$pageSize;
        $where = 'type=2 and status=1';
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
        $field = 'id,companyname,EnterpriseID,city,district,classification,vip_time,is_check,face_pic,type,longitude,latitude';
        $data = $this->table('firms')->field($field)->where($where)->limit($start,$pageSize)->get();
        if($data){
            for($i=0; $i<count($data); ++$i){
                $data[$i]['vip'] = 0;
                if($data[$i]['vip_time']>date("Y-m-d H:i:s")){
                    $data[$i]['vip'] = 1;
                }
            }
        }
//        $mapField = 'longitude,latitude,companyname,EnterpriseID';
//        $mapData = $this->table('firms')->field($mapField)->where($where)->get();
        $return['list']     = $data;
//        $return['mapList']  = $mapData;
        $return['page']     = $page;
        $return['pageSize'] = $pageSize;
        $return['count']    = $count;
        return $return;
    }

    /**
     * @param $page
     * @param $pageSize
     * @param $wordsKey     商家名称关键字查询
     * @param $province     省
     * @param $city         市
     * @param $district     区
     * @param $classification 类型筛选(4.修理厂  5.快修保养 6.美容店)
     * @return mixed
     */
    public function showCarMend2($page,$pageSize,$wordsKey,$province,$city,$district,$classification,$nowCity){
        $start = ($page-1)*$pageSize;
        $where = 'type=2 and status=1';
        if($wordsKey){
            $where .= ' and companyname like"%'.$wordsKey.'%"';
        }
        if($province){
            $where .= ' and province="'.$province.'"';
        }
        if($city){
            $where .= ' and city="'.$city.'"';
        }
        if(!$city && $nowCity){
            $where .= ' and city="'.$nowCity.'"';
        }
        if($district){
            $where .= ' and district="'.$district.'"';
        }
        if($classification){
            $where .= ' and classification="'.$classification.'"';
        }
        $count = $this->table('firms')->where($where)->count();
        $field = 'id,companyname,EnterpriseID,city,district,classification,vip_time,is_check,face_pic,type,longitude,latitude';
        $data = $this->table('firms')->field($field)->where($where)->limit($start,$pageSize)->get();
        if($data){
            for($i=0; $i<count($data); ++$i){
                $data[$i]['vip'] = 0;
                if($data[$i]['vip_time']>date("Y-m-d H:i:s")){
                    $data[$i]['vip'] = 1;
                }
            }
        }
        $return['list']     = $data;
        $return['page']     = $page;
        $return['pageSize'] = $pageSize;
        $return['count']    = $count;
        return $return;
    }

    /**
     * 返回所有修理厂坐标
     */
    public function getAllZuoBiao(){
        $field = 'longitude,latitude,companyname,EnterpriseID';
        $data = $this->table('firms')->field($field)->where('type=2 and longitude is not null and latitude is not null')->get();
        return $data;
    }

    /**
     * 生成店铺二维码地址
     * @param $firmsID
     * @return string
     */
    public function getQRStore($firmsID,$name,$type){
        $domainName = G('config')['domainName'];
        $outPath    = './data/firmsQR/'.$firmsID.'.png';
        include_once './lib/phpqrcode/phpqrcode.php';
        if($type==1){
            $url = "{$domainName}/weixin/view/index/shangjia/jingxiao.html?data={\"EnterpriseID\":\"{$firmsID}\",\"qxName\":\"{$name}\",\"type\":\"QR\"}";
        }else{
            $url = "{$domainName}/weixin/view/person/repair/detail.html?data={\"EnterpriseID\":\"{$firmsID}\",\"qxName\":\"{$name}\",\"type\":\"QR\"}";
        }
        QRcode::png($url,$outPath,'',3,1);
        return ltrim($outPath,'.');
    }

    /**
     * 查询当前厂商绑定的业务员
     * @param $firmId
     * @return mixed
     */
    public function getBindUser($firmId){
        $nowTime = date("Y-m-d",time());
        $res = $this->table('firms_sales_user a')->field('b.uId,b.facepic')
            ->jion('left join sales_user b on b.id=a.sales_user_di')
            ->where("a.firms_id = $firmId and $nowTime<a.end_time")->getOne();
        return $res;
    }

    public function bindSaleUser($uId,$firmId){
        $rst = $this->getBindUser($firmId);
        if($rst){
            $return = array('status'=>2,'msg'=>'已绑定，请刷新页面');
        }else{
            $res = $this->table('sales_user')->where(array('uId'=>$uId))->getOne();
            if($res){
                $data['sales_user_di'] = $res['id'];
                $data['firms_id'] =$firmId;
                $data['end_time'] = date('Y-m-d',strtotime("+3 month +1 day"));
                $data['create_time'] = date("Y-m-d H:i:s",time());

                $rrr = $this->table('firms_sales_user')->insert($data);
                if($rrr){
                    $return = array('status'=>1,'msg'=>'绑定成功');
                }else{
                    $return = array('status'=>2,'msg'=>'绑定失败，请稍后重试');
                }
            }else{
                $return = array('status'=>2,'msg'=>'该ID业务员不存在，请确认后重新输入');
            }
        }
        return $return;
    }

}