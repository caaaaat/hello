<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/16
 * Time: 21:55
 */
class PcProductController extends Controller
{
    private $user = array();

    public function __construct()
    {
        $loginMo    = model('web.login','mysql');
        $this->user = $user = $loginMo->loginIs(false);
    }
    /*根据二级产品id查询三级四级分类*/
    public function getThreeAndFourByTwo(){
        $type = $this->getRequest('type','');
        $id   = $this->getRequest('id','');
        $cateMo = model('web.category');
        if($type && $id){
            $carData = $cateMo->getThreeAndFourByTwo($type,$id);//轿车 二级分类
            $return['list']   = $carData;
            $return['status'] = 1;
        }else{
            $return['status'] = 0;
            $return['msg']    = '缺少参数';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 添加产品
     */
    public function addData(){
        $data = $this->getRequest('data','');
        $data['firms_id']   = $this->user['id'];
        $data['update_time'] = date("Y-m-d H:i:s");
        $proId = $this->getRequest('proId','');
        if(!$proId){
            $data['create_time'] = date("Y-m-d H:i:s");
            $unicode = model('web.product')->getUniq(time());       //获取产品唯一ID
            $data['proId'] = $unicode;
        }
        $rst = model('web.product')->saveProduct($data,$proId);
        if($rst > 0){
            $return['status'] = 1;
        }else{
            $return['status'] = 0;
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取对应一级分类的二级分类数据
     */
    public function getTwoType(){
        $oneId = $this->getRequest('id','');
        $return['status'] = 0;
        if($oneId){
            $two = model('web.product')->getTwoType($oneId);        //获取对应一级类型的所有二级类型
            if($two){
                $return['status'] = 1;
                $return['list']   = $two;
            }
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 返回店铺产品列表数据
     */
    public function shopList(){
        $user = $this->user;
        $pro_status = $this->getRequest('pro_status','');
        $seekData       = $this->getRequest('data','');
        $return['status'] = 0;
        if($pro_status && $user['id']){
            model('api.sev.salesmanSouCang','mysql')->resetRefresh($user['id']);  //统计支付表刷新点，修改产品当日刷新点
            $list = model('web.product')->getProductList($user['id'],$pro_status,$seekData);
            if(count($list) > 0){
                $return['status'] = 1;
                $return['list']   = $list;
            }
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 修改商品上架状态
     */
    public function proStatus(){
        $user = $this->user;
        $proId  = $this->getRequest('proId','');
        $status = $this->getRequest('status','');
        $return['status'] = 0;
        if($proId && $status && $user['id']){
            $rst = model('web.product')->proStatus($proId,$status,$user['id']);
            if($rst > 0){
                $return['status'] = 1;
            }
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 删除一条商品数据
     */
    public function delProduct(){
        $user = $this->user;
        $proId  = $this->getRequest('proId','');
        $return['status'] = 0;
        if($proId && $user['id']){
            $rst = model('web.product')->delProduct($proId,$user['id']);
            if($rst > 0){
                $return['status'] = 1;
            }
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 刷新一条产品
     */
    public function refreshProduct(){
        $user = $this->user;
        $proId = $this->getRequest('proId','');
        $return['status'] = 0;
        $return['msg']    = '操作失败';
        if($user['refresh_point'] && $user['refresh_point']>0){
            if($proId && $user['id']){
                $rst = model('web.product')->refreshProduct($proId,$user['id']);
                if($rst > 0){
                    $return['status'] = 1;
                }
            }
        }else{
            $return['msg']    = '刷新点不足，操作失败';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 提交申请
     */
    public function approveing(){
        $user = $this->user;
        $data = $this->getRequest('data','');
        $data['firms_id']    = $user['id'];
        $data['create_time'] = date("Y-m-d H:i:s");
        $data['update_time'] = date("Y-m-d H:i:s");
        $data['audit_time']  = '';
        $data['status']      = 1;
        $rst = model('web.product')->approveing($data);
        if($rst < 1){
            $return['status'] = 0;
        }else{
            $return['status'] = 1;
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    /**
     *首页获取产品
     * 发布求购，添加求购数据
     */
    public function insertShop(){
        $data = $this->getRequest('data','');
        $user = $this->user;
        $rst  = model('web.product')->insertShop($data,$user['id']);
        exit(json_encode($rst,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 发布求购，修改求购数据
     */
    public function insertShopEidt(){
        $data = $this->getRequest('data','');
        $user = $this->user;
        $rst  = model('web.product')->insertShopEidt($data,$user['id']);
        exit(json_encode($rst,JSON_UNESCAPED_UNICODE));
    }

    /**
     *
     */
    public function getProductList(){
        $firmID     = $this->getRequest('ID','');
        $pro_type   = $this->getRequest('type','');
        $pro_cate_1 = $this->getRequest('cate_1',array());
        $pro_cate_2 = $this->getRequest('cate_2',array());
        $keyword    = $this->getRequest('keywords','');
        $page       = $this->getRequest('page',1);
        $productMo  = model('web.product','mysql');
        if($firmID){
            $firms  = $productMo->table('firms')->field('id')->where(array('EnterpriseID'=>$firmID))->getOne();
        }else{
            $firms['id']= 0;
        }
        //当前城市
        $currentCity = cookie('currentCity')?cookie('currentCity'):'成都市';
        $pageSize = 12;
        $return = $productMo->getProduct($firms['id']?$firms['id']:0,$pro_type,$pro_cate_1,$pro_cate_2,$keyword,$page,$pageSize,$currentCity);

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 返回购物中数据
     */
    public function getShopingList(){
        $user = $this->user;
        $page = $this->getRequest('page',1);
        $pageSize = $this->getRequest('pageSize',10);
        $data = model('web.product')->shoping($user['id'],'',$page,$pageSize);
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 返回求购历史数据
     */
    public function getShopHistory(){
        $user = $this->user;
        $page = $this->getRequest('page',1);
        $pageSize = $this->getRequest('pageSize',10);
        $data = model('web.product')->shoping($user['id'],1,$page,$pageSize);
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    public function getCollectProduct(){
        $pro_type   = $this->getRequest('type','');
        $pro_cate_1 = $this->getRequest('cate_1','');
        $pro_cate_2 = $this->getRequest('cate_2','');
        $keyword    = $this->getRequest('keywords','');
        $page       = $this->getRequest('page',1);

        $collectMo  = model('web.collect','mysql');

        $return = $collectMo->collectProductList(1,$this->user['id'],$pro_type,$pro_cate_1,$pro_cate_2,$keyword,$page,12);
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 求购下架
     */
    public function shopXiaJia(){
        $user = $this->user;
        $id   = $this->getRequest('id','');
        if($id){
            $rst = model('web.product')->shopXiaJia($id,$user['id']);
            if($rst > 0){
                $return['status'] = 1;
            }else{
                $return['status'] = 0;
                $return['msg']    = '数据异常，操作失败';
            }
        }else{
            $return['status'] = 0;
            $return['msg']    = '缺少参数，操作失败';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 删除求购
     */
    public function delShop(){
        $user = $this->user;
        $id   = $this->getRequest('id','');
        if($id){
            $rst = model('web.product')->delShop($id,$user['id']);
            if($rst > 0){
                $return['status'] = 1;
            }else{
                $return['status'] = 0;
                $return['msg']    = '删除失败';
            }
        }else{
            $return['status'] = 0;
            $return['msg']    = '删除失败,缺少参数';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 导出采购清单的excel
     */
    public function downExcel(){
        $id   = $this->getRequest('id','');
        if($id){
            model('web.product')->downExcel($id);
        }
    }

    /**
     * 求购
     */
    public function getNeedGou(){
        $type   = $this->getRequest('type',0);
        $cate_1 = $this->getRequest('cate_1',0);
        $cate_2 = $this->getRequest('cate_2',0);
        $page   = $this->getRequest('page',1);

        $cateMo = model('web.category','mysql');
        $car_group_4=array();
        if($type){
            if($cate_1){
                if($cate_2){
                    $car_group_4 = $cateMo->getFourByMore($type,$cate_2,2);
                }else{
                    $car_group_4 = $cateMo->getFourByMore($type,$cate_1,1);
                }
            }else{
                $car_group_4 = $cateMo->getFourByMore($type,0,4);
            }
        }
        //当前城市
        $currentCity = cookie('currentCity')?cookie('currentCity'):'成都市';
        $nBuyMo = model('web.tobuy','mysql');
        $data   = $nBuyMo->getDataList($car_group_4,$currentCity,$page,6);
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 修改主表(商家表)经营范围四级id
     */
    public function save_four_ids(){
        $ids = $this->getRequest('ids','');
        if($this->user){
            $result = model('web.product')->save_four_ids($ids,$this->user['id']);
            if($result>0){
                $return['status'] = 1;
            }else{
                $return['status'] = 0;
                $return['msg'] = '修改失败';
            }
        }else{
            $return['status'] = 0;
            $return['msg'] = '你还没有登录，修改失败';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * vin查询
     */
    public function getVinList(){
        if($this->user){
            $num = $this->getRequest('data','');
            $type= $this->getRequest('type',1);
            header("content-type:text/html;charset=utf-8");
            $url = "http://service.vin114.net/req?wsdl";
            $method = "LevelData";
            if($type==1){
                $data = "<root><appkey>fc93d445cc35decd</appkey><appsecret>206bec36ebcf48f39e1dda63532f500c</appsecret><method>level.vehicle.vin.get</method><requestformat>json</requestformat><vin>".$num."</vin></root>";
            }else{
                $data = "<root><appkey>fc93d445cc35decd</appkey><appsecret>206bec36ebcf48f39e1dda63532f500c</appsecret><method>level.vehicle.vin.get</method><requestformat>json</requestformat><imgbase64>".$num."</imgbase64></root>";
            }
            $client = new SoapClient($url);
            $addResult = $client->__soapCall($method,array(array('xmlInput'=>$data)));

            $LevelDataResult = json_decode($addResult->LevelDataResult);
            $info = $LevelDataResult->Info;
            $success = $info->Success;
            if($success==false){
                $return = array('status'=>201,'msg'=>$info->Desc);
            }else{
                $return = array('status'=>200,'list'=>$LevelDataResult->Result);
            }
        }else{
            $return['status'] = 101;
            $return['msg'] = '你还没有登录请登录后重试';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /*导出vin数据*/
    public function vinExecel(){
        $data = $this->getRequest('data','');
        if($data){
            $data = json_decode($data,true);
            if($data){
                model('web.product')->vinDownExcel($data);
            }
        }
    }
}