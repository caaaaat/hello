<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/3
 * Time: 18:27
 */
class ApiSevSalesmanSouCangController extends Controller
{
    /**
     * 业务员点击进入厂商过渡页面
     */
    public function storeVeiwData(){
        $token = $this->getRequest('token','');
        if($token){
            $id = $this->getRequest('EnterpriseID','');     //店铺id(唯一标识)
            if($id){
                $return = model('api.sev.salesmanSouCang','mysql')->storeVeiwData($token,$id);
            }else{
                $return = array('status'=>102,'msg'=>'缺失参数,请重试');
            }
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取店铺基础信息
     */
    public function getStoreInfo(){
        //获取提交的数据
        $tokenId = $this->getRequest('tokenId','');
        if($tokenId){
            $tokenId = authcode($tokenId, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql')->getStoreInfo($tokenId);
        }else{
            $return = array('status'=>101,'msg'=>'数据缺失,请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取店铺产品
     */
    public function getProducts(){
        //获取提交的数据
        $token    = $this->getRequest('token','');
        $pro_type = $this->getRequest('pro_type','');
        $pro_cate_1 = $this->getRequest('pro_cate_1','');
        $pro_cate_2 = $this->getRequest('pro_cate_2','');
        $pro_status = $this->getRequest('pro_status','');
        $keyword  = $this->getRequest('keyword','');
        $page     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');

        if($token){
            $userMo = model('api.sev.salesmanSouCang','mysql');
            $tokenId = authcode($token, 'DECODE');
            model('api.sev.salesmanSouCang','mysql')->resetRefresh($tokenId);  //统计支付表刷新点，修改产品当日刷新点
            $return = $userMo ->getProducts($tokenId,$pro_type,$pro_cate_1,$pro_cate_2,$pro_status,$keyword,$page,$pageSize);
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户访问记录数据
     */
    public function getVisitToFirmsLog(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');
        if($token){
            $tokenId = authcode($token, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql')->getVisitToFirmsLog($tokenId,$p,$pageSize);
        }else{
            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户拨打记录数据
     */
    public function getCallToFirmsLog(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');
        if($token){
            $tokenId = authcode($token, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql') ->getCallToFirmsLog($tokenId,$p,$pageSize);
        }else{
            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 获取用户认证状态
     */
    public function getCompanyAuth(){
        //获取提交的数据
        $token     = $this->getRequest('token','');
        if($token){
            $tokenId = authcode($token, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql')->getCompanyAuth($tokenId);
        }else{
            $return = array('status'=>101,'msg'=>'你还未登录，登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 保存用户认证信息
     */
    public function saveAuth(){
        //获取提交的数据
        $token     = $this->getRequest('token','');
        $firmsName = $this->getRequest('firmsName','');
        $firmsMan  = $this->getRequest('firmsMan','');
        $firmsTel  = $this->getRequest('firmsTel','');
        $province  = $this->getRequest('province','');
        $city      = $this->getRequest('city','');
        $district  = $this->getRequest('district','');
        $address   = $this->getRequest('address','');
        $licence_pic   = $this->getRequest('licence_pic','');
        $taxes_pic     = $this->getRequest('taxes_pic','');
        $field_pic     = $this->getRequest('field_pic','');
        $brand_pic     = $this->getRequest('brand_pic','');
        $agents_pic    = $this->getRequest('agents_pic','');
        if($token){
            if($firmsName&&$firmsMan&&$firmsTel&&$province&&$address){
                if($licence_pic||$taxes_pic||$field_pic||$brand_pic||$agents_pic){
                    //用户数据请求成功
                    $userId = authcode($token, 'DECODE');
                    $salesmanId = $this->getRequest('userId','');
                    $salesmanId = authcode($salesmanId, 'DECODE');
                    $return   = model('api.sev.salesmanSouCang','mysql')->isGuanLian($salesmanId,$userId);
                    if($return['status'] == 200){
                        $data = array();
                        $data['firms_id'] = $userId;
                        $data['firmsName'] = $firmsName;
                        $data['firmsMan'] = $firmsMan;
                        $data['firmsTel'] = $firmsTel;
                        $data['province'] = $province;
                        $data['city']     = $city;
                        $data['district'] = $district;
                        $data['address']  = $address;
                        $data['licence_pic'] = $licence_pic;
                        $data['taxes_pic']   = $taxes_pic;
                        $data['field_pic']   = $field_pic;
                        $data['brand_pic']   = $brand_pic;
                        $data['agents_pic']  = $agents_pic;
                        $nowTime = date("Y-m-d H:i:s");
                        $data['create_time']  = $nowTime;
                        $data['update_time']  = $nowTime;
                        $data['status']       = 1;
                        $return = model('api.sev.salesmanSouCang','mysql')->saveAuth($data);
                    }
                }else{
                    $return = array('status'=>103,'msg'=>'请至少上传一张资质图片');
                }
            }else{
                $return = array('status'=>102,'msg'=>'提交数据有误，请检查后重试');
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取店铺车系 - 二级分类信息
     */
    public function getRange(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        if($token){
            $userId = authcode($token, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql')->getRange($userId);
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取店铺车系 - 四级分类信息 - 个人
     */
    public function getStoreSeries(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $cid   = $this->getRequest('cid','');
        if($token){
            if($cid){
                $userId = authcode($token, 'DECODE');
                $return = model('api.sev.salesmanSouCang','mysql')->getStoreSeries($userId,$cid);
            }else{
                $return = array('status'=>102,'msg'=>'提交数据有误，请检查后重试');
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 保存四级车系
     */
    public function saveRange(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $ranges= $this->getRequest('ranges','');
        if($token){
            $userId = authcode($token, 'DECODE');
            $salesman   = $this->getRequest('userId','');
            $salesmanId = authcode($salesman, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql')->isGuanLian($salesmanId,$userId);
            if($return['status'] == 200){
                $return = model('api.sev.salesmanSouCang','mysql')->saveRange($userId,$ranges);
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 企业名片
     */
    public function getCardInfo(){
        //获取提交的数据
        $token      = $this->getRequest('token','');
        if($token){
            $userId = authcode($token, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql')->getCardInfo($userId);
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登陆后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 企业名片模板信息
     */
    public function getCardTplInfo(){
        //获取提交的数据
        $token      = $this->getRequest('token','');
        if($token){
            $userId = authcode($token, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql')->getCardTplInfo($userId);
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登陆后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 创建名片
     */
    public function ceartCard(){
        $token  = $this->getRequest('token','');
        if($token){
            $base64 = $this->getRequest('base64','');
            $data   = $this->getRequest('data','');
            $userId = authcode($token, 'DECODE');
            $salesman = $this->getRequest('userId','');
            $salesmanId = authcode($salesman, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql')->isGuanLian($salesmanId,$userId);
            if($return['status'] == 200){
                $result = model('api.sev.salesmanSouCang','mysql')->base64Save($base64);
                if($result['status'] == 200){
                    $data['path'] = $result['path'];        //保存的图片路径
                    $return = model('api.sev.salesmanSouCang','mysql')->ceartCard($data,$userId);
                }else{
                    $return = $result;
                }
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登陆后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户被访问记录数据
     */
    public function getFirmsToVisitLog(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');
        if($token){
            $userId = authcode($token, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql')->getFirmsToVisitLog($userId,$p,$pageSize);
        }else{
            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户被访问记录数据
     */
    public function getFirmsToVisitLog2(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');
        if($token){
            $id = model('api.sev.salesmanSouCang','mysql')->uIdGetFirmId($token);
            if($id){
                $userId = $id['id'];
                $return = model('api.sev.salesmanSouCang','mysql')->getFirmsToVisitLog($userId,$p,$pageSize);
            }else{
                $return = array('status'=>102,'msg'=>'获取厂商信息失败');
            }
        }else{
            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户被拨打记录数据
     */
    public function getFirmsToCallLog(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');
        if($token){
            $userId = authcode($token, 'DECODE');
            $return = model('api.sev.salesmanSouCang','mysql')->getFirmsToCallLog($userId,$p,$pageSize);
        }else{
            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户被拨打记录数据
     */
    public function getFirmsToCallLog2(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');
        if($token){
            $id = model('api.sev.salesmanSouCang','mysql')->uIdGetFirmId($token);
            if($id){
                $userId = $id['id'];
                $return = model('api.sev.salesmanSouCang','mysql')->getFirmsToCallLog($userId,$p,$pageSize);
            }else{
                $return = array('status'=>102,'msg'=>'获取厂商信息失败');
            }
        }else{
            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户数据
     */
    public function getUserInfo(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        if($token){
            $return = model('api.sev.salesmanSouCang','mysql')->uIdGetFirmId($token);
            $return = array('data'=>$return,'status'=>200);
        }else{
            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户刷新点数据
     */
    public function getRefreshHistory(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');
        if($token){
            $id = model('api.sev.salesmanSouCang','mysql')->uIdGetFirmId($token);
            if($id){
                $userId = $id['id'];
                $return = model('api.sev.salesmanSouCang','mysql')->getRefreshHistory($userId,$p,$pageSize);
            }else{
                $return = array('status'=>102,'msg'=>'获取厂商信息失败');
            }
        }else{
            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户vip充值数据
     */
    public function getVipHistory(){
        //获取提交的数据
        $token = $this->getRequest('token','');
        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');
        if($token){
            $id = model('api.sev.salesmanSouCang','mysql')->uIdGetFirmId($token);
            if($id){
                $userId = $id['id'];
                $return = model('api.sev.salesmanSouCang','mysql')->getVipHistory($userId,$p,$pageSize);
            }else{
                $return = array('status'=>102,'msg'=>'获取厂商信息失败');
            }
        }else{
            $return = array('status'=>101,'msg'=>'请求数据失败，请重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取用户认证状态
     */
    public function getCompanyAuth2(){
        //获取提交的数据
        $token     = $this->getRequest('token','');
        if($token){
            $id = model('api.sev.salesmanSouCang','mysql')->uIdGetFirmId($token);
            if($id){
                $userId = $id['id'];
                $return = model('api.sev.salesmanSouCang','mysql')->getCompanyAuth($userId);
            }else{
                $return = array('status'=>102,'msg'=>'获取厂商信息失败');
            }
        }else{
            $return = array('status'=>101,'msg'=>'你还未登录，登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 保存用户认证信息
     */
    public function saveAuth2(){
        //获取提交的数据
        $token     = $this->getRequest('token','');
        $firmsName = $this->getRequest('firmsName','');
        $firmsMan  = $this->getRequest('firmsMan','');
        $firmsTel  = $this->getRequest('firmsTel','');
        $province  = $this->getRequest('province','');
        $city      = $this->getRequest('city','');
        $district  = $this->getRequest('district','');
        $address   = $this->getRequest('address','');
        $licence_pic   = $this->getRequest('licence_pic','');
        $taxes_pic     = $this->getRequest('taxes_pic','');
        $field_pic     = $this->getRequest('field_pic','');
        $brand_pic     = $this->getRequest('brand_pic','');
        $agents_pic    = $this->getRequest('agents_pic','');
        if($token){
            $id = model('api.sev.salesmanSouCang','mysql')->uIdGetFirmId($token);
            if($id){
                $userId = $id['id'];
                if($firmsName&&$firmsMan&&$firmsTel&&$province&&$address){
                    if($licence_pic||$taxes_pic||$field_pic||$brand_pic||$agents_pic){
                        //用户数据请求成功
                        $userId = authcode($token, 'DECODE');
                        $data = array();
                        $data['firms_id'] = $userId;
                        $data['firmsName'] = $firmsName;
                        $data['firmsMan'] = $firmsMan;
                        $data['firmsTel'] = $firmsTel;
                        $data['province'] = $province;
                        $data['city']     = $city;
                        $data['district'] = $district;
                        $data['address']  = $address;
                        $data['licence_pic'] = $licence_pic;
                        $data['taxes_pic']   = $taxes_pic;
                        $data['field_pic']   = $field_pic;
                        $data['brand_pic']   = $brand_pic;
                        $data['agents_pic']  = $agents_pic;
                        $nowTime = date("Y-m-d H:i:s");
                        $data['create_time']  = $nowTime;
                        $data['update_time']  = $nowTime;
                        $data['status']       = 1;
                        $return = model('api.sev.salesmanSouCang','mysql')->saveAuth($data);
                    }else{
                        $return = array('status'=>103,'msg'=>'请至少上传一张资质图片');
                    }
                }else{
                    $return = array('status'=>102,'msg'=>'提交数据有误，请检查后重试');
                }
            }else{
                $return = array('status'=>102,'msg'=>'获取厂商信息失败');
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 编辑店铺信息
     */
    public function saveStore(){
        //获取提交的数据
        $token     = $this->getRequest('token','');
        $faceImg    = $this->getRequest('faceImg','');
        $wechatPic  = $this->getRequest('wechatPic','');
        $bannerPic  = $this->getRequest('bannerPic','');
        $coordinate = $this->getRequest('coordinate','');
        $longitude  = $this->getRequest('longitude','');
        $latitude   = $this->getRequest('latitude','');
        $address    = $this->getRequest('address','');
        $major   = $this->getRequest('major','');
        $linkMan = $this->getRequest('linkMan','');
        $info    = $this->getRequest('info','');
        $phones  = $this->getRequest('phones','');
        $qqs     = $this->getRequest('qqs','');
        $tels    = $this->getRequest('tels','');
        $salesmanId  = $this->getRequest('userId','');
        if($token){
            if($faceImg&&$longitude&&$major&&$linkMan&&$phones&&$qqs&&$tels){
                $userId = authcode($token, 'DECODE');
                $salesmanId = authcode($salesmanId, 'DECODE');
                $return = model('api.sev.salesmanSouCang','mysql')->isGuanLian($salesmanId,$userId);
                if($return['status'] == 200){
                    $data = array();
                    $data['info']     = $info;
                    $data['address']  = $address;
                    $data['coordinate'] = $coordinate;
                    $data['longitude']  = $longitude;
                    $data['latitude']   = $latitude;
                    $data['face_pic']   = $faceImg;
                    $data['wechat_pic'] = $wechatPic;
                    $data['major']   = $major;
                    $data['linkMan'] = $linkMan;
                    $data['linkPhone']  = $phones;
                    $data['linkTel']  = $tels;
                    $data['qq']  = $qqs;
                    $nowTime = date("Y-m-d H:i:s");
                    $data['update_time']  = $nowTime;
                    $return = model('api.sev.salesmanSouCang','mysql')->saveStore($data,$userId,$bannerPic);
                }
            }else{
                $return = array('status'=>102,'msg'=>'提交数据有误，请检查后重试');
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 查询当前厂商的拨打记录
     */
    public function getBoDaJiLuNow(){
        $token = $this->getRequest('token','');
//        $token = '1f7f03z4sTLFLUMR9CN4fxOR9MP011+/vR0v6ccN';
        if($token){
            $id = model('api.sev.salesmanSouCang','mysql')->uIdGetFirmId($token);
            if($id){
                $userId = $id['id'];
                $page = $this->getRequest('page',1);
                $pageSize = $this->getRequest('pageSize',10);
                $lat  = $this->getRequest('lat','');
                $lng  = $this->getRequest('lng','');
                $type = $this->getRequest('type',1);        //1为拨打记录,2为访问记录
                $dingWeiToken = '';
                if(!$lat || !$lng){
                    $dingWeiToken = 1;
                }
                $return = model('api.sev.salesmanSouCang')->getBoDaJiLuNow($userId,$page,$pageSize,$lat,$lng,$dingWeiToken,$type);
            }else{
                $return = array('status'=>102,'msg'=>'获取厂商信息失败');
            }
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 根据厂商唯一ID查询记录
     */
    public function getMingXi(){
        $token = $this->getRequest('token','');
        $type  = $this->getRequest('type','');
        $companyId = $this->getRequest('companyId','');
        if($token && $type && $companyId){
            $page = $this->getRequest('page',1);
            $pageSize = $this->getRequest('pageSize',10);
            $id = model('api.sev.salesmanSouCang','mysql')->uIdGetFirmId($token);
            if($id){
                $userId = $id['id'];
                $return = model('api.sev.salesmanSouCang')->getMingXi($page,$pageSize,$userId,$type,$companyId);
            }else{
                $return = array('status'=>102,'msg'=>'获取厂商信息失败');
            }
        }else{
            $return = array('status'=>101,'msg'=>'参数丢失请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
}