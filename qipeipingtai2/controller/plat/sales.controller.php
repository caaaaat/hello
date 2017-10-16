<?php

/**
 * 业务员管理控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10
 * Time: 11:16
 */
class PlatSalesController extends Controller
{
    public function lists(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.Sales';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $this->template('plat.sales.list');
        }else{
            dump('没有相关权限');
        }
    }

    /**
     * 获取业务员列表
     */
    public function getSales(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.Sales';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $page    = $this->getRequest('page'     ,'1');
            $pageSize= $this->getRequest('pageSize' ,'10');
            $status  = $this->getRequest('status'   ,'');
            $province= $this->getRequest('province' ,'');
            $keywords= $this->getRequest('keywords' ,'');
            $order   = $this->getRequest('order'    ,'');
            $salesMo = model('plat.sales.sales','mysql');
            $return  = $salesMo->getSales($page,$pageSize,$status,$province,$keywords,$order);
        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    public function exportToExcel(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.Sales';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $status  = $this->getRequest('status'   ,'');
            $province= $this->getRequest('province' ,'');
            $keywords= $this->getRequest('keywords' ,'');
            $order   = $this->getRequest('order'    ,'');
            $page    = $this->getRequest('page'     ,'1');
            $pageSize= $this->getRequest('pageSize' ,'10');
            $salesMo = model('plat.sales.sales','mysql');
            $return  = $salesMo->getSales($page,$pageSize,$status,$province,$keywords,$order);

            $fileName = '业务员列表_'.date('ymdHis') . ".csv";//自定义名称
            $head     = array('业务员ID','昵称', '姓名', '联系电话', '管辖区域', '关联厂商数', '最近登录', '状态');

            $statusArr= array(1=>'正常',2=>'停用');
            $csvArr   = array();//数据
            if($return['massageCode']==='success'){
                foreach ($return['list'] as $key => $item) {
                    $csvArr[] = array(
                        $item['uId']."\n",
                        $item['uname'],
                        $item['realname'],
                        $item['phone']."\n",
                        $item['area'],
                        $item['fir_num'],
                        $item['last_time'],
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
     * 启用/停用业务员
     */
    public function changeStatus(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.Sales';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){

            $status = $this->getRequest('status','');
            $uId    = $this->getRequest('userId','');
            $userMo = model('plat.sales.sales','mysql');
            $result = $userMo->changeStatus($uId,$status);

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
     * 重置密码
     */
    public function resetPassword(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.Sales';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){

            $uId    = $this->getRequest('userId','');
            $userMo = model('plat.sales.sales','mysql');
            $result = $userMo->resetPassword($uId);

            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = '重置成功' ;
            }else{
                $return['massage']     = '重置失败' ;
            }

        }else{
            $return['massage']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    public function addSale(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $supper = $mo->loginIs();
        $this->assign('me',$supper) ;

        $this->template('plat.sales.addSale');
    }

    /**
     * 添加子业务员 / 编辑业务员
     */
    public function saveSale(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.Sales';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $uname    = $this->getRequest('uname'    ,'');
            $province = $this->getRequest('province' ,'');
            $tel      = $this->getRequest('tel'      ,'');
            $realname = $this->getRequest('realname' ,'');
            $id       = $this->getRequest('id'       ,'');
            $face     = $this->getRequest('face'     ,'');
            $salesMo  = model('plat.sales.sales','mysql');
            $result   = $salesMo->saveSale($uname,$province,$tel,$realname,$id,$face);

            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = $id ? '编辑成功' : '添加成功' ;
            }else{
                $return['massage']     = $id ? '编辑失败' : '添加失败' ;
            }

        }else{
            $return['massage']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 业务员详情
     */
    public function getOneSale(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.Sales';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth) {

            $id   = $this->getRequest('id', '');
            $salesMo   = model('plat.sales.sales','mysql');
            //1.获取基础信息
            $sale = $salesMo->getOneSale($id);

            $this->assign('sale',$sale) ;
            //2.获取圈子记录

            //3.获取管理厂商


            $this->template('plat/sales/oneSale') ;
        }else{
            dump('没用相关权限');
        }
    }

    /**
     * 编辑业务员
     */
    public function editSale(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.Sales';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth) {

            $id      = $this->getRequest('id', '');
            $salesMo = model('plat.sales.sales','mysql');
            //1.获取基础信息
            $sale    = $salesMo->getOneSale($id);
            $area    = $sale['area'] ;
            $area    = explode('/',$area);
            $province= isset($area[0]) ? $area[0] : '全部' ;
            $city    = isset($area[1]) ? $area[1] : '全部' ;
            $county  = isset($area[2]) ? $area[2] : '全部' ;
            $this->assign('province',$province) ;
            $this->assign('city',$city) ;
            $this->assign('county',$county) ;
            $this->assign('sale',$sale) ;
            //writeLog($sale);

            $this->assign('me',$user) ;
            $this->template('plat/sales/editSale') ;
        }else{
            dump('没用相关权限');
        }
    }

    /**
     * 业务员关联圈子
     */
    public function getSaleCircle(){
        $id      = $this->getRequest('id', '');
        $page    = $this->getRequest('page'     ,'1');
        $pageSize= $this->getRequest('pageSize' ,'10');
        $keywords= $this->getRequest('keywords' ,'');
        $salesMo = model('plat.sales.sales','mysql');
        $return  = $salesMo->getSaleCircle($id,$page,$pageSize,$keywords);
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 圈子详情
     */
    public function getOneCircle(){

        $cid     = $this->getRequest('cid', '');
        $salesMo = model('plat.sales.sales','mysql');
        $circle  = $salesMo->getOneCircle($cid);
        $this->assign('circle',$circle) ;
//        dump($circle);
        $this->template('plat.sales.oneCirle');
    }

    /**
     * 评论页
     */
    public function comment(){
        $cid     = $this->getRequest('cid', '');
        $this->assign('cid',$cid) ;
        $this->template('plat.sales.comments');
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
        $mod    = 'plat.Sales';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth) {
            $id      = $this->getRequest('id' , '');
            $pid     = $this->getRequest('pid' , '');
            $salesMo = model('plat.sales.sales','mysql');
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

    //============== 关联厂商 =================

    public function getFirms(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.Sales';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data    = $this->getRequest('data'     ,'1');
            $salesMo = model('plat.sales.sales','mysql');
            $return  = $salesMo->getFirms($data);

        }else{
            $return['massageCode'] = 0;
            $return['massage']     = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
}