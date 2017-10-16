<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 13:51
 */
class PlatProductController extends Controller
{
    private $user = array();
    public function __construct()
    {
        //检查是否登录
        $mo         = model('suAdmin','mysql');
        $this->user = $mo->loginIs();
    }

    /**
     * 产品主页
     */
    public function lists(){
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.product';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        //writeLog($isAuth);
        if($isAuth){
            $proMo = model('plat.product.pro','mysql');
            $cate  = $proMo->getProCate()  ;
            $cateItem = array() ;
            if($cate){
                foreach ($cate as $v){
                    if($v['level'] == 1){
                        $cateItem['lv1'][] = $v ;
                    }else{
                        $cateItem['lv2'][] = $v ;
                    }
                }
            }
            if(isset($cateItem['lv2'])){
                $this->assign('cateEnLv2'  ,json_encode($cateItem['lv2'],JSON_UNESCAPED_UNICODE)) ;
            }

            $this->assign('cate'  ,$cateItem) ;
            $this->template('plat.product.list');
        }else{
            dump('没有相关权限');
        }
    }

    public function getProduct(){
        $data  = $this->getRequest('data' ,'');
        $proMo = model('plat.product.pro','mysql');
        $pros  = $proMo->getProduct($data)  ;
        exit(json_encode($pros,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 上下架
     */
    public function changeStatus(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.product';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $status  = $this->getRequest('status','');
            $proId   = $this->getRequest('proId','');
            $proMo   = model('plat.product.pro','mysql');
            $result  = $proMo->changeStatus($proId,$status);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success';
                $return['massage']     = $status == 1 ? '上架成功' : '下架成功' ;
            }else{
                $return['massage']     = $status == 1 ? '上架失败' : '下架失败' ;
            }

        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 删除产品
     */
    public function delProduct(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.product';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $proId   = $this->getRequest('id','');
            $proMo   = model('plat.product.pro','mysql');
            $result  = $proMo->delProduct($proId);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success';
                $return['massage']     = '删除成功';
            }else{
                $return['massage']     = '删除失败';
            }

        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 产品详情
     */
    public function getOnePro(){
        $id     = $this->getRequest('id' ,'');
        $proMo  = model('plat.product.pro','mysql');
        $pro    = $proMo->getOnePro($id)  ;

        $pro['group_span'] = '' ;
        if($pro['car_group_id']){
            $group   = explode(',',$pro['car_group_id']);
            $firMo  = model('plat.firms.firms','mysql');
            foreach ($group as $v){
                $group_name = $firMo->getOneCarGroup($v,'');
                if($group_name){
                    $group_name = rtrim($group_name,'/') ;
                    $item       = '<span title="'.$group_name.'" data-value="'.$v.'" class="badge badge-info car_group" style="margin-right: 5px;">';
                    $item      .= $group_name ;
                    $item      .= '<span onclick="delGroup(this)" title="删除此车系" style="display: inline-block;width: 13px;height: 13px;border-radius: 6px; background-color: red;position: relative;left: 5px;cursor: pointer;">x</span></span>' ;
                    $pro['group_span'] .= $item ;
                }
            }
        }
        $cate   = $proMo->getProCate()  ;
        $cateItem = array() ;
        if($cate){
            foreach ($cate as $v){
                if($v['level'] == 1){
                    $cateItem['lv1'][] = $v ;
                }else{
                    $cateItem['lv2'][] = $v ;
                }
            }
        }
        $this->assign('cateEnLv2'  ,json_encode($cateItem['lv2'],JSON_UNESCAPED_UNICODE)) ;
        $this->assign('cate'  ,$cateItem) ;

        $this->assign('pro',$pro) ;
        $this->template('plat.product.onePro');
    }

    /**
     * 选择适用车系
     */
    public function choiceCarGroup(){
        $s = $this->getRequest('s' ,'');
        $this->assign('s',$s) ;
        //dump($s);
        $this->template('plat.product.choiceCarGroup');
    }

    /**
     * 获取一二级车系分类
     */
    public function getCarClass(){
        $data   = $this->getRequest('data' ,'');
        $proMo  = model('plat.product.pro','mysql');
        $res    = $proMo->getCarClass($data)  ;
        exit(json_encode($res,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 获取三四级车系分类
     */
    public function getCarGroup(){
        $data   = $this->getRequest('data' ,'');
        $proMo  = model('plat.product.pro','mysql');
        $res    = $proMo->getCarGroup($data)  ;
        exit(json_encode($res,JSON_UNESCAPED_UNICODE));
    }


    public function saveProduct(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.product';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data    = $this->getRequest('data','');
            $proMo   = model('plat.product.pro','mysql');
            $result  = $proMo->saveProduct($data);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success';
                $return['massage']     = '编辑成功';
            }else{
                $return['massage']     = '编辑失败';
            }

        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 导出产品
     */
    public function exportProToExcel(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.product';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){

            $data['status']   = $this->getRequest('status'  ,'');
            $data['pro_type'] = $this->getRequest('pro_type','');
            $data['cate_lv1'] = $this->getRequest('cate_lv1','');
            $data['cate_lv2'] = $this->getRequest('cate_lv2','');
            $data['com_type'] = $this->getRequest('com_type','');
            $data['keywords'] = $this->getRequest('keywords','');
            $data['page']     = $this->getRequest('page'    ,'');
            $data['pageSize'] = $this->getRequest('pageSize','');
            $vipMo    = model('plat.product.pro','mysql');
            $company  = $vipMo->getProduct($data);

            $fileName = '产品列表_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('产品ID', '产品名称', '类型', '类别', '分类', '价格', '今日刷新数', '所属企业',  '企业ID',  '企业类型',  '状态');
            $csvArr   = array();//数据
            //dump($company['list']);
            if($company['massageCode']==='success'){
                foreach ($company['list'] as $key => $item) {
                    $csvArr[] = array(
                        $item['proId'],
                        $item['proName'],
                        $item['pro_type'],
                        $item['cate_name1'],
                        $item['cate_name2'],
                        $item['pro_price'],
                        $item['pro_refresh'],
                        $item['companyname'],
                        $item['EnterpriseID'],
                        $item['type'] == 1 ? '经销商' : '修理厂',
                        $item['pro_status'] == 1 ? '上架中' : '未上架',
                    );
                }
            }

            //dump($csvArr);die;
            $csvMo = model('tools.getCsv', 'mysql');
            echo $csvMo->array2csv($csvArr, $head, $fileName);
            unset($csvArr);
            die();
        }else{
            dump('没有相关权限');
        }
    }
}