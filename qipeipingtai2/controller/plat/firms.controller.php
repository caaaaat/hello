<?php
/**
 * 经销商管理控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/5
 * Time: 9:22
 */
class PlatFirmsController extends Controller {

    //==========经销商列表页操作=====================
    public function lists(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        //writeLog($isAuth);
        if($isAuth){
            $this->template('plat.firms.list');
        }else{
            dump('没有相关权限');

        }
    }

    /**
     * 获取经销商
     */
    public function getFirms(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data    = $this->getRequest('data','1');
            $comMo   = model('plat.firms.firms','mysql');
            $return  = $comMo->getFirms($data);
            if($return['massageCode'] === 'success'){
                $list = $return['list'];
                if($list){
                    foreach ($list as $k => $item){
                        if(!$item['province']){
                            $area = '';
                        }elseif($item['province']=='全部'){
                            $area = '全部';
                        }elseif($item['city'] == '' || $item['city'] == '全部'){
                            $area = $item['province'];
                        }elseif($item['district'] == '' || $item['district'] == '全部'){
                            $area = $item['province'].'/'.$item['city'];
                        }else{
                            $area = $item['province'].'/'.$item['city'].'/'.$item['district'];
                        }
                        $list[$k]['area'] = $area ;
                    }
                    $return['list'] = $list ;
                }
            }
        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 启用/停用经销商
     */
    public function changeStatus(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){

            $status  = $this->getRequest('status','');
            $comId   = $this->getRequest('comId','');
            $comMo   = model('plat.firms.firms','mysql');
            $result  = $comMo->changeStatus($comId,$status);

            if($result){//判断是否保存成功
                $return['massageCode'] = 'success';
                $return['massage']     = $status == 1 ? '启用成功' : '停用成功' ;
            }else{
                $return['massage']     = $status == 1 ? '启用失败' : '停用失败' ;
            }

        }else{
            $return['massage']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 导出厂商列表
     */
    public function exportToExcel(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){

            $data['status']   = $this->getRequest('status'  ,'');
            $data['type']     = $this->getRequest('type'    ,'');
            $data['cfn']      = $this->getRequest('cfn'     ,'');
            $data['province'] = $this->getRequest('province','');
            $data['city']     = $this->getRequest('city'    ,'');
            $data['county']   = $this->getRequest('county'  ,'');
            $data['is_check'] = $this->getRequest('is_check','');
            $data['sale']     = $this->getRequest('sale'    ,'');
            $data['is_vip']   = $this->getRequest('is_vip'  ,'');
            $data['keywords'] = $this->getRequest('keywords','');
            $data['page']     = $this->getRequest('page'    ,'');
            $data['pageSize'] = $this->getRequest('pageSize','');
            $vipMo    = model('plat.firms.firms','mysql');
            $company  = $vipMo->getFirms($data);

            $fileName = '厂商列表_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('企业ID', '昵称', '手机号', '企业名称', '企业类型', '企业分类', '所属区域', '认证状态',  '关联业务员',  'VIP',  '刷新点', '最后一次登录', '状态');
            $typeArr  = array(0=>'',1=>'经销商',2=>'汽修厂' );
            $classArr = array(1=>'轿车商家',2=>'货车商家',3=>'用品商家',4=>'修理厂',5=>'快修保养',6=>'美容店') ;
            $isCheck  = array(1=>'已认证',2=>'未认证');
            $isVip    = array(1=>'是',2=>'否');
            $statusArr= array(1=>'正常',2=>'禁用');
            $csvArr   = array();//数据
            //dump($company['list']);
            if($company['massageCode']==='success'){
                foreach ($company['list'] as $key => $item) {
                    //$area = '';
                    if(!$item['province']){
                        $area = '';
                    }elseif($item['province']=='全部'){
                        $area = '全部';
                    }elseif($item['city'] == '' || $item['city'] == '全部'){
                        $area = $item['province'];
                    }elseif($item['district'] == '' || $item['district'] == '全部'){
                        $area = $item['province'].'/'.$item['city'];
                    }else{
                        $area = $item['province'].'/'.$item['city'].'/'.$item['district'];
                    }
                    $csvArr[] = array(
                        $item['EnterpriseID']."\n",
                        $item['companyname']."\n",
                        $item['phone']."\n",
                        $item['companyname'],
                        $item['type'] ? $typeArr[$item['type']] : '',
                        $item['classification'] ? $classArr[$item['classification']] : '',
                        $area."\n",
                        $isCheck[$item['is_check']],

                        $item['salesman_ids'] ? '是' : '否',
                        $isVip[$item['is_vip']],
                        $item['refresh_point'] == null ? 0 : $item['refresh_point'],
                        $item['last_time']."\n",
                        $statusArr[$item['status']],
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

    /**
     * 创建经销商
     */
    public function createFirm(){
        $mo       = model('suAdmin','mysql');
        $supper   = $mo->loginIs();
        //$supper   = G('user') ;
        //$suppProv = @$supper['province'] ;
        $this->assign('me',$supper) ;

        $this->template('plat.firms.addFirm');
    }

    public function ChoiceBusiness(){
        $firmMo  = model('plat.firms.firms','mysql');
        $type    = $this->getRequest('type','');
        $carType = $firmMo->getCarGroup($type)  ;
        //dump($carType);
        $this->assign('_type'  ,$type   ) ;
        $this->assign('CarGroupItem',$carType) ;
        $this->template('plat.firms.choiceBusiness');
    }

    /**
     * 保存 经销商
     */
    public function saveFirm(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $firmMo  = model('plat.firms.firms','mysql');

            if(!$data['id']){
                $firm = $firmMo->table('firms')->field('id')->where(array('phone'=>$data['phone']))->getOne() ;
                if($firm){
                    $return['massage']         = '该帐号已存在' ;
                }else{
                    $result   = $firmMo->saveFirm($data);
                    if($result){//判断是否保存成功
                        $return['massageCode'] = 'success' ;
                        $return['massage']     = '创建成功' ;
                    }else{
                        $return['massage']     = '创建失败' ;
                    }
                }
            }else{
                $result   = $firmMo->saveFirm($data);
                if($result){//判断是否保存成功
                    $return['massageCode'] = 'success' ;
                    $return['massage']     = '编辑成功'  ;
                }else{
                    $return['massage']     = '编辑失败'  ;
                }
            }
        }else{
            $return['massage']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));


    }

    public function checkPhone(){
        $data   = $this->getRequest('data'    ,'');
        $firmMo = model('plat.firms.firms','mysql');
        $firm   = $firmMo->table('firms')->field('id')->where(array('phone'=>$data['phone']))->getOne() ;
        $return = array('massageCode'=>0);
        if($firm){
            $return['massage']     = '该帐号已存在' ;
        }else{
            $return['massageCode'] = 'success' ;
            $return['massage']     = '可以新增'  ;
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    public function resetPassword(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data     = $this->getRequest('data' ,'');
            //dump($data);die;
            $firmMo   = model('plat.firms.firms','mysql');
            $res      = $firmMo->resetPassword($data)  ;
            if($res){
                $return['massageCode']  = 'success' ;
                $return['massage']      = '重置成功' ;
            }else{
                $return['massage']      = '重置成功' ;
            }
        }else{
            $return['massage'] = '没有相关权限' ;

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //==========经销商详情页操作=====================


    //-------------基本信息-------------------------
    /**
     * 厂商详情
     */
    public function getOneFirm(){
        $id     = $this->getRequest('id' ,'');
        $firmMo  = model('plat.firms.firms','mysql');
        $firm  = $firmMo->getOneFirm($id)  ;
        $cate  = $firmMo->getProCate()  ;
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
        if(isset($cateItem['lv2'])) {
            $this->assign('cateEnLv2', json_encode($cateItem['lv2'], JSON_UNESCAPED_UNICODE));
        }
        $this->assign('cate'  ,$cateItem) ;
        $this->assign('firm'  ,$firm    ) ;
        $this->template('plat.firms.oneFirm');
    }

    /**
     * 编辑厂商
     */
    public function editFirm(){
        $id     = $this->getRequest('id' ,'');
        $firmMo  = model('plat.firms.firms','mysql');
        $firm  = $firmMo->getOneFirm($id)  ;
        $this->assign('firm'  ,$firm    ) ;
        $this->template('plat.firms.editFirm');

    }
    //-------------产品信息-------------------------
    /**
     * 产品列表
     */
    public function getFirmPros(){
        $data = $this->getRequest('data' ,'');
        $firmMo  = model('plat.firms.firms','mysql');
        $firmPros= $firmMo->getFirmPros($data)  ;
        exit(json_encode($firmPros,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 产品详情
     */
    public function getOnePro(){
        $id     = $this->getRequest('id' ,'');
        $firmMo = model('plat.firms.firms','mysql');
        $pro    = $firmMo->getOnePro($id)  ;
        $this->assign('pro',$pro) ;
        //dump($pro);
        $this->template('plat.firms.onePro');
    }
    public function exportProToExcel(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data = array() ;
            $data['firmId']     = $this->getRequest('firmId'     ,'');
            $data['proStatus']  = $this->getRequest('proStatus'  ,'');
            $data['proType']    = $this->getRequest('proType'    ,'');
            $data['proCateLv1'] = $this->getRequest('proCateLv1' ,'');
            $data['proCateLv2'] = $this->getRequest('proCateLv2' ,'');
            $data['keywords']   = $this->getRequest('keywords'   ,'');
            $data['page']       = $this->getRequest('page'       ,'');
            $data['pageSize']   = $this->getRequest('pageSize'   ,'');
            $name     = $this->getRequest('name' ,'厂商');
            $vipMo    = model('plat.firms.firms','mysql');
            $res      = $vipMo->getFirmPros($data);

            $fileName = $name.'_产品记录_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('产品ID', '产品名称', '类型', '类别', '分类', '今日刷新数', '状态');
            $statusArr= array(1=>'上架中',2=>'未上架' );
            $csvArr   = array();//数据
            if($res['massageCode']==='success'){
                foreach ($res['list'] as $key => $item) {
                    $csvArr[] = array(
                        $item['proId']."\n",
                        $item['proName']."\n",
                        $item['pro_type'],
                        $item['cate_name1'],
                        $item['cate_name2'],
                        $item['pro_refresh'],
                        $statusArr[$item['pro_status']],
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


    //-------------认证信息-------------------------
    /**
     * 认证信息
     */
    public function getCheckData(){
        $firmId   = $this->getRequest('firmId' ,'');
        $firmMo   = model('plat.firms.firms','mysql');
        $CheckData= $firmMo->getCheckData($firmId)  ;
        //dump($CheckData) ;
        exit(json_encode($CheckData,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 编辑和查看认证详情
     */
    public function getOneCheck(){
        $id       = $this->getRequest('id' ,'');
        $type     = $this->getRequest('type' ,'1');
        $firmMo   = model('plat.firms.firms','mysql');
        $OneCheck = $firmMo->getOneCheck($id)  ;
        $this->assign('OneCheck'  ,$OneCheck ) ;
        //dump($OneCheck);
        if($type == 1){
            $this->template('plat.firms.oneCheck');
        }else{
            $this->template('plat.firms.editCheck');
        }

    }

    /**
     * 保存认证信息
     */
    public function saveOneCheck(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data     = $this->getRequest('data' ,'');
            //dump($data);die;
            $firmMo   = model('plat.firms.firms','mysql');
            $res      = $firmMo->saveOneCheck($data)  ;
            if($res){
                $return['massageCode']  = 'success' ;
                $return['massage']      = '编辑成功' ;
            }else{
                $return['massage']      = '编辑失败' ;
            }
        }else{
           $return['massage'] = '没有相关权限' ;

        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    //-------------VIP记录-------------------------

    public function getVipLog(){
        $data     = $this->getRequest('data' ,'');
        $firmMo   = model('plat.firms.firms','mysql');
        $res      = $firmMo->getVipLog($data)  ;
        exit(json_encode($res   ,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 导出记录
     */
    public function exportVipLogToExcel(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data = array() ;
            $data['firmId']   = $this->getRequest('firmId'   ,'');
            $data['page']     = $this->getRequest('page'     ,'1');
            $data['pageSize'] = $this->getRequest('pageSize' ,'10');
            $name     = $this->getRequest('name' ,'厂商');
            $vipMo    = model('plat.firms.firms','mysql');
            $res      = $vipMo->getVipLog($data);

            $fileName = $name.'_VIP充值记录_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('企业名称', '金额（元）', '月数', '支付方式', '结果', '时间');
            $resArr   = array(1=>'充值成功',2=>'充值失败' );
            $payArr   = array(1=>'微信支付',2=>'支付宝支付',3=>'人工收费',4=>'刷新产品',5=>'刷新店铺',6=>'邀请获得',7=>'官方赠送') ;
            $csvArr   = array();//数据
            if($res['massageCode']==='success'){
                foreach ($res['list'] as $key => $item) {
                    $csvArr[] = array(
                        $name,
                        $item['money'],
                        $item['refresh_point'],
                        $payArr[$item['payway']],
                        $resArr[$item['status']],
                        $item['create_time']."\n",
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

    //------------刷新点记录-------------------------

    public function getRefreshLog(){
        $data     = $this->getRequest('data' ,'');
        $firmMo   = model('plat.firms.firms','mysql');
        $res      = $firmMo->getRefreshLog($data)  ;
        exit(json_encode($res   ,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 导出记录
     */
    public function exportRefreshToExcel(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data = array() ;
            $data['firmId']   = $this->getRequest('firmId'   ,'');
            $data['page']     = $this->getRequest('page'     ,'1');
            $data['pageSize'] = $this->getRequest('pageSize' ,'10');
            $name     = $this->getRequest('name' ,'厂商');
            $vipMo    = model('plat.firms.firms','mysql');
            $res      = $vipMo->getRefreshLog($data);

            $fileName = $name.'_刷新点记录_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('企业名称', '点数', '内容', '详细', '金额（元）', '支付方式', '结果', '时间');
            $resArr   = array(1=>'成功',2=>'失败' );
            $typeArr  = array(2=>'充值刷新点',3=>'消费刷新点',4=>'获得新点',5=>'其他' );
            $payArr   = array(1=>'微信支付',2=>'支付宝支付',3=>'人工收费',4=>'刷新产品',5=>'刷新店铺',6=>'邀请获得',7=>'官方赠送') ;
            $csvArr   = array();//数据
            if($res['massageCode']==='success'){
                foreach ($res['list'] as $key => $item) {
                    $csvArr[] = array(
                        $name,
                        $item['refresh_point'],
                        $typeArr[$item['type']],
                        $item['info'],
                        $item['money'],
                        $payArr[$item['payway']],
                        $resArr[$item['status']],
                        $item['create_time']."\n",
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

    
    //-------------来访记录-------------------------

    public function getVisitComeLog(){
        $data     = $this->getRequest('data' ,'');
        $firmMo   = model('plat.firms.firms','mysql');
        $res      = $firmMo->getVisitComeLog($data)  ;
        exit(json_encode($res   ,JSON_UNESCAPED_UNICODE));
    }
    public function exportVisitComeToExcel(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data = array() ;
            $data['firmId']   = $this->getRequest('firmId'   ,'');
            $data['keywords'] = $this->getRequest('keywords' ,'');
            $data['page']     = $this->getRequest('page'     ,'1');
            $data['pageSize'] = $this->getRequest('pageSize' ,'10');
            $name     = $this->getRequest('name' ,'厂商');
            $vipMo    = model('plat.firms.firms','mysql');
            $res      = $vipMo->getVisitComeLog($data);

            $fileName = $name.'_来访记录_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('企业ID','昵称', '手机号', '企业名称', '企业类型', '企业分类', 'VIP', '来访方式', '来源', '时间');
            $typeArr  = array(0=>'',1=>'经销商',2=>'修理厂' );
            $classArr = array(0=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>''),1=>array(1=>'轿车商家',2=>'货车商家',3=>'用品商家'),2=>array(4=>'修理厂',5=>'快修保养',6=>'美容店')) ;
            $visit    = array(1=>'访问',2=>'拨打',3=>'',4=>'') ;
            $visitArr = array(1=>'PC Web',2=>'移动 Web',3=>'',4=>'') ;
            $isVip    = array(1=>'是',2=>'否');
            $csvArr   = array();//数据
            if($res['massageCode']==='success'){
                foreach ($res['list'] as $key => $item) {
                    $csvArr[] = array(
                        $item['EnterpriseID']."\n",
                        $item['uname']."\n",
                        $item['phone']."\n",
                        $item['companyname']."\n",
                        $typeArr[$item['type']],
                        $classArr[$item['type']][$item['classification']],
                        $isVip[$item['is_vip']],
                        $visit[$item['visit']],
                        $visitArr[$item['visit_type']],
                        $item['create_time']."\n",
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
    //-------------访问记录-------------------------
    public function getVisitGoLog(){
        $data     = $this->getRequest('data' ,'');
        $firmMo   = model('plat.firms.firms','mysql');
        $res      = $firmMo->getVisitGoLog($data)  ;
        exit(json_encode($res   ,JSON_UNESCAPED_UNICODE));
    }
    public function exportVisitGoToExcel(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data = array() ;
            $data['firmId']   = $this->getRequest('firmId'   ,'');
            $data['keywords'] = $this->getRequest('keywords' ,'');
            $data['page']     = $this->getRequest('page'     ,'1');
            $data['pageSize'] = $this->getRequest('pageSize' ,'10');
            $name     = $this->getRequest('name' ,'厂商');
            $vipMo    = model('plat.firms.firms','mysql');
            $res      = $vipMo->getVisitGoLog($data);

            $fileName = $name.'_访问记录_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('企业ID','昵称', '手机号', '企业名称', '企业类型', '企业分类', 'VIP', '来访方式', '来源', '时间');
            $typeArr  = array(0=>'',1=>'经销商',2=>'修理厂' );
            $classArr = array(0=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>''),1=>array(1=>'轿车商家',2=>'货车商家',3=>'用品商家'),2=>array(4=>'修理厂',5=>'快修保养',6=>'美容店')) ;
            $visit    = array(1=>'访问',2=>'拨打',3=>'',4=>'') ;
            $visitArr = array(1=>'PC Web',2=>'移动 Web',3=>'',4=>'') ;
            $isVip    = array(1=>'是',2=>'否');
            $csvArr   = array();//数据
            if($res['massageCode']==='success'){
                foreach ($res['list'] as $key => $item) {
                    $csvArr[] = array(
                        $item['EnterpriseID']."\n",
                        $item['uname']."\n",
                        $item['phone']."\n",
                        $item['companyname']."\n",
                        $typeArr[$item['type']],
                        $classArr[$item['type']][$item['classification']],
                        $isVip[$item['is_vip']],
                        $visit[$item['visit']],
                        $visitArr[$item['visit_type']],
                        $item['create_time']."\n",
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
    //-------------求购记录-------------------------
    public function getWantBuyLog(){
        $data     = $this->getRequest('data' ,'');
        $firmMo   = model('plat.firms.firms','mysql');
        $res      = $firmMo->getWantBuyLog($data)  ;
        exit(json_encode($res   ,JSON_UNESCAPED_UNICODE));
    }

    public function getOneWantBuy(){
        $id     = $this->getRequest('id' ,'');
        $firmMo = model('plat.firms.firms','mysql');
        $oneBuy = $firmMo->getOneWantBuy($id)  ;
        $this->assign('pro',$oneBuy) ;

        if($oneBuy){
            $childList = $firmMo->getBuyChildList($oneBuy['id'])  ;
            $picList   = $firmMo->getWantBuyPic($oneBuy['id'])  ;
            $car_group = $firmMo->getOneCarGroup($oneBuy['car_group_id'],'')  ;
//            $car_group = $firmMo->getOneCarGroup(13,'')  ;
            $car_group = rtrim($car_group,'/');
            $oneBuy['car_group'] = $car_group ;
            //dump($car_group);
            $this->assign('childList',$childList) ;
            $this->assign('picList',$picList) ;
        }
        $this->assign('oneBuy',$oneBuy) ;
        $this->template('plat.firms.oneWantBuy');
    }


    //-------------圈子记录-------------------------
    public function getCircleLog(){
        $data     = $this->getRequest('data' ,'');
        $firmMo   = model('plat.firms.firms','mysql');
        $res      = $firmMo->getCircleLog($data)  ;
        exit(json_encode($res,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 圈子详情
     */
    public function getOneCircle(){
        $cid     = $this->getRequest('cid', '');
        $salesMo = model('plat.sales.sales','mysql');
        $circle  = $salesMo->getOneCircle($cid);
        $this->assign('circle',$circle) ;
        $this->template('plat.firms.oneCirle');
    }

    /**
     * 评论页
     */
    public function comment(){
        $cid     = $this->getRequest('cid', '');
        $this->assign('cid',$cid) ;
        $this->template('plat.firms.comments');
    }
    /**
     * 获取圈子评论
     */
    public function getComments(){
        $cid     = $this->getRequest('cid'      , '');
        $type    = $this->getRequest('type'     , '');
        $page    = $this->getRequest('page'     ,'1');
        $pageSize= $this->getRequest('pageSize' ,'10');
        $keywords= $this->getRequest('keywords' , '');
        $salesMo = model('plat.sales.sales','mysql');
        $comments= $salesMo->getComments($cid,$type,$keywords,$page,$pageSize);

        exit(json_encode($comments,JSON_UNESCAPED_UNICODE));

    }
    public function delComment(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.firms';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth) {
            $id      = $this->getRequest('id' , '');
            $pid     = $this->getRequest('pid' , '');
            $salesMo = model('plat.sales.sales','mysql');//调用业务员管理中的方法
            $res     = $salesMo->delComment($id,$pid);
            if($res){
                $return ['massageCode'] = 'success';
                $return ['massage']     = '删除成功';
            }else{
                $return ['massage']     = '删除失败';
            }
        }else{
            $return ['massage'] = '没用相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }



    //-------------邀请信息-------------------------
    public function getInviteLog(){
        $data     = $this->getRequest('data' ,'');
        $firmMo   = model('plat.firms.firms','mysql');
        $res      = $firmMo->getInviteLog($data)  ;
        exit(json_encode($res,JSON_UNESCAPED_UNICODE));
    }
    //------------关联业务员-------------------------

    public function getSaleLog(){
        $data     = $this->getRequest('data' ,'');
        $firmMo   = model('plat.firms.firms','mysql');
        $res      = $firmMo->getSaleLog($data)  ;
        exit(json_encode($res,JSON_UNESCAPED_UNICODE));
    }

}