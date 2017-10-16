<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/20
 * Time: 14:51
 */
class ApiSevSalesmanController extends Controller
{
    /**
     * 查询当前业务的拨打记录
     */
    public function getBoDaJiLu(){
        $token = $this->getRequest('token','');
//        $token = '1f7f03z4sTLFLUMR9CN4fxOR9MP011+/vR0v6ccN';
        if($token){
            $page = $this->getRequest('page',1);
            $pageSize = $this->getRequest('pageSize',10);
            $lat  = $this->getRequest('lat','');
            $lng  = $this->getRequest('lng','');
            $dingWeiToken = '';
            if(!$lat || !$lng){
                $dingWeiToken = 1;
            }
            $return = model('api.sev.salesman')->getBoDaJiLu($token,$page,$pageSize,$lat,$lng,$dingWeiToken);
        }else{
            $return = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 查询业务员对某个厂商的访问明细记录
     */
    public function getOneCompanyBoDaInfo(){
        $token = $this->getRequest('token','');
        $companyId = $this->getRequest('companyId','');
        if($token && $companyId){
            $page   = $this->getRequest('page',1);
            $pageSize = $this->getRequest('pageSize',15);
            $return = model('api.sev.salesman')->getOneCompanyBoDaInfo($token,$companyId,$page,$pageSize);
        }elseif(!$token){
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }elseif(!$companyId){
            $return = array('status'=>102,'msg'=>'数据缺失');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 清空业务员拨打记录
     */
    public function delYeWuBoDaJiLu(){
        $token = $this->getRequest('token','');
        if($token){
            $return = model('api.sev.salesman')->delYeWuBoDaJiLu($token);
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 查询业务员本月关联汽修厂数据
     */
    public function getThisMonthQiXiu(){
        $token = $this->getRequest('token','');
        $lat  = $this->getRequest('lat',30.674024);
        $lng  = $this->getRequest('lng',104.072315);
//        $token = '1f7f03z4sTLFLUMR9CN4fxOR9MP011+/vR0v6ccN';
        if($token){
            $page = $this->getRequest('page',1);
            $pageSize = $this->getRequest('pageSize',10);
            $return = model('api.sev.salesman')->getThisMonthQiXiu($token,$page,$pageSize,$lat,$lng);
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 存入业务员拨打记录
     */
    public function addYeWuBoDaJiLu(){
        $token = $this->getRequest('token','');
        if($token){
            $companyId = $this->getRequest('companyId','');
            $visit_type= $this->getRequest('visit_type',3);     //机型3为安卓，4为苹果
            if($companyId){
                $return = model('api.sev.salesman')->addYeWuBoDaJiLu($token,$companyId,$visit_type);
            }else{
                $return = array('status'=>102,'msg'=>'关键数据缺失，操作失败');
            }
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }



    /**
     *工资列表
     */
    public function getWageList(){
        $token = $this->getRequest('token','');
        if($token){
            $page    = $this->getRequest('page' ,1);
            $pageSize= $this->getRequest('pageSize',15);
            $return  = model('api.sev.salesman','mysql')->getWage($token,$page,$pageSize);
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 工资详情
     */
    public function getWageInfo(){
        $token = $this->getRequest('token','');
//        $token = '1f7f03z4sTLFLUMR9CN4fxOR9MP011+/vR0v6ccN';
        if($token){
            $id = $this->getRequest('id' ,'');
            $return  = model('api.sev.salesman','mysql')->getWageInfo($token,$id);
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     *查询新增关联汽修厂(上个月关联汽修厂提成)
     */
    public function beforeMonthList(){
        $token = $this->getRequest('token','');
//        $token = '1f7f03z4sTLFLUMR9CN4fxOR9MP011+/vR0v6ccN';
        if($token){
            $page     = $this->getRequest('page',1);
            $pageSize = $this->getRequest('pageSize',10);
            $wageId   = $this->getRequest('id','');
            $return = model('api.sev.salesman','mysql')->beforeMonthList($token,$page,$pageSize,$wageId);
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 查询汽修厂拨打提成上个月的数据(页面显示为汽修厂关联提成)
     */
    public function boDaTiCheng(){
        $token = $this->getRequest('token','');
//        $token = '1f7f03z4sTLFLUMR9CN4fxOR9MP011+/vR0v6ccN';
        if($token){
            $page     = $this->getRequest('page',1);
            $pageSize = $this->getRequest('pageSize',10);
            $wageId   = $this->getRequest('id','');
            $return = model('api.sev.salesman','mysql')->boDaTiCheng($token,$page,$pageSize,$wageId);
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 查询上个月厂商充值提成明细
     */
    public function companyPayTiCheng(){
        $token = $this->getRequest('token','');
//        $token = '1f7f03z4sTLFLUMR9CN4fxOR9MP011+/vR0v6ccN';
        if($token){
            $page     = $this->getRequest('page',1);
            $pageSize = $this->getRequest('pageSize',10);
            $wageId   = $this->getRequest('id',1);
            $return = model('api.sev.salesman','mysql')->companyPayTiCheng($token,$page,$pageSize,$wageId);
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     * 返回所有修理厂坐标
     */
    public function getAllZuoBiao(){
        $token = $this->getRequest('token','');
        if($token){
            $city      = $this->getRequest('city','');            //市
            $district  = $this->getRequest('district','');        //区
            $city = '';
            $district = '';
            $classType = $this->getRequest('classType','');       //厂商类型(1:经销商,2:汽修厂)
            $jiShu     = $this->getRequest('jiShu','');           //级数(1:一级,2:二级,3:三级)
            $shaiXuan  = $this->getRequest('shaiXuan','');        //筛选(1:未认证厂商,2:已关联厂商,3:未关联厂商)
            $cityCode  = $this->getRequest('cityCode','');        //管辖省份
            $keywords  = $this->getRequest('keywords','');        //关键字
            if($cityCode){
                $return = model('api.sev.salesman','mysql')->getAllZuoBiao($token,$city,$district,$classType,$jiShu,$shaiXuan,$cityCode,$keywords);
            }else{
                $return = array('status'=>102,'msg'=>'登录过期,获取信息失败,请重新登录');
            }
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 返回所有修理厂坐标(列表)
     */
    public function getAllListZuoBiao(){
        $token = $this->getRequest('token','');
//        $token = '1f7f03z4sTLFLUMR9CN4fxOR9MP011+/vR0v6ccN';
        if($token){
            $city      = $this->getRequest('city','');            //市
            $district  = $this->getRequest('district','');        //区
            $classType = $this->getRequest('classType','');       //厂商类型(1:经销商,2:汽修厂)
            $jiShu     = $this->getRequest('jiShu','');           //级数(1:一级,2:二级,3:三级)
            $shaiXuan  = $this->getRequest('shaiXuan','');        //筛选(1:未认证厂商,2:已关联厂商,3:未关联厂商)
            $keywords  = $this->getRequest('keywords','');        //关键字
            $page      = $this->getRequest('page',1);
            $pageSize  = $this->getRequest('pageSize',100);
            $lat       = $this->getRequest('lat',30.674024);
            $lng       = $this->getRequest('lng',104.072315);
            $cityCode  = $this->getRequest('cityCode','四川省');        //管辖省份
            if($cityCode){
                $return = model('api.sev.salesman','mysql')->getAllListZuoBiao($token,$city,$district,$classType,$jiShu,$shaiXuan,$page,$pageSize,$lat,$lng,$cityCode,$keywords);
            }else{
                $return = array('status'=>102,'msg'=>'登录过期，获取信息失败，请重新登录');
            }
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 汽修厂使用频率提成(分1级、2级、3级)
     */
    public function qiXiuJiShuPinLv(){
        $token = $this->getRequest('token','');
//        $token = '1f7f03z4sTLFLUMR9CN4fxOR9MP011+/vR0v6ccN';
        if($token){
            $page     = $this->getRequest('page',1);
            $pageSize = $this->getRequest('pageSize',10);
            $wageId   = $this->getRequest('id','');
            $jiShu    = $this->getRequest('jiShu','');
            if($jiShu){
                $return = model('api.sev.salesman','mysql')->qiXiuJiShuPinLv($token,$page,$pageSize,$wageId,$jiShu);
            }else{
                $return = array('status'=>102,'msg'=>'获取数据信息失败，请再次登录后重试');
            }
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 保存头像
     */
    public function saveHeader(){
        $token = $this->getRequest('token','');
        $img   = $this->getRequest('headerImg','');
        if($token){
            $return = model('api.sev.salesman','mysql')->saveHeader($token,$img);
        }else{
            $return = array('status'=>101,'msg'=>'登录过期，请重新登录');
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

}