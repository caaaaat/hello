<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10
 * Time: 1:05
 */
class PlatActivityController extends Controller
{

    private $user = array();
    public function __construct()
    {
        //检查是否登录
        $mo         = model('suAdmin','mysql');
        $this->user = $mo->loginIs();
    }


    public function lists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.activity';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $this->template('plat.activity.list');
        }else{
            dump('没有相关权限');
        }
    }

    public function getLists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.activity';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data       = $this->getRequest('data' ,'');
            $activityMo = model('plat.activity.activity','mysql');
            $res        = $activityMo->getLists($data)  ;
            $return     = $res ;
        }else{
            $return['massage'] = '没有相关权限' ;

        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 调整顺序
     */
    public function saveVID(){
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.activity';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $activityMo = model('plat.activity.activity','mysql');
            $result   = $activityMo->saveVID($data);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                //$return['massage']     = '排序成功'  ;
            }else{
                //$return['massage']     = '排序失败'  ;
            }
        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 删除促销活动/友情链接/取消推荐经销商
     */
    public function delContent(){
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.activity';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $activityMo = model('plat.activity.activity','mysql');
            $result   = $activityMo->delContent($data);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = $data['type'] == 1 ? '删除成功' : ($data['type'] == 2 ? '删除成功' : '取消成功') ;
            }else{
                $return['massage']     = '操作失败'  ;
            }
        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    public function getContent(){
        $id = $this->getRequest('id'    ,'');
        $activityMo = model('plat.activity.activity','mysql');
        $res = $activityMo->getContent($id);
        $this->assign('activity',$res) ;
        $this->template('plat.activity.content') ;
    }

    /**
     * 编辑/增加活动
     */
    public function editContent(){
        $id = $this->getRequest('id'    ,'');
        $activityMo = model('plat.activity.activity','mysql');

        if($id){
            $res = $activityMo->getContent($id);
        }else{
            $res = array('id'=>'','title'=>'','face_img'=>'','content'=>'') ;
        }
        $this->assign('activity',$res) ;
        $this->template('plat.activity.editContent') ;
    }
    /**
     * 保存活动
     */
    public function saveActivity(){

        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.activity';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $activityMo = model('plat.activity.activity','mysql');
            $result   = $activityMo->saveActivity($data);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = $data['id'] > 0 ? '编辑成功' : '新增成功'  ;
            }else{
                $return['massage']     = $data['id'] > 0 ? '编辑失败' : '新增失败'  ;
            }

        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 选择经销商
     */
    public function choiceFirm(){
        $this->template('plat.activity.choiceFirm') ;
    }

    public function getChoiceFirm(){
        $data     = $this->getRequest('data'    ,'');
        $activityMo = model('plat.activity.activity','mysql');
        $choiceFirm = $activityMo->getChoiceFirm($data) ;
        exit(json_encode($choiceFirm,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 保存推荐经销商
     */
    public function saveRecommendFirm(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.activity';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $activityMo = model('plat.activity.activity','mysql');
            $result   = $activityMo->saveRecommendFirm($data);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = '新增成功'  ;
            }else{
                $return['massage']     = '新增失败'  ;
            }
        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 编辑/增加友情链接
     */
    public function friendlyLink(){
        $id = $this->getRequest('id'    ,'');

        if($id){
            $res = model('plat.activity.activity','mysql')->field('id,vname,vurl')->table('friendly_link')->where(array('id'=>$id,'is_del'=>0))->getOne() ;

        }else{
            $res = array('id'=>'','vname'=>'','vurl'=>'') ;
        }
        $this->assign('link',$res) ;
        $this->template('plat.activity.addLink') ;
    }
    /**
     * 编辑/增加友情链接
     */
    public function saveLink(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.activity';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $activityMo = model('plat.activity.activity','mysql');

            //判断友情链接是否存在
            if($data['id'] > 0){
                $res = model('plat.activity.activity','mysql')->field('id')->table('friendly_link')
                    ->where('id<>"'.$data['id'].' and is_del=0" and vname="'.$data['name'].'" and vurl="'.$data['url'].'"')
                    ->getOne() ;
            }else{
                $res = model('plat.activity.activity','mysql')->field('id')->table('friendly_link')
                    ->where(array('vname'=>$data['name'],'vurl'=>$data['url'],'is_del'=>0))->getOne() ;
            }

            if($res){
                $return['massage']    = '该友情链接已存在';
            }else{
                $result   = $activityMo->saveLink($data);
                if($result){//判断是否保存成功
                    $return['massageCode'] = 'success' ;
                    $return['massage']     = $data['id'] > 0 ? '编辑成功' : '新增成功'  ;
                }else{
                    $return['massage']     = $data['id'] > 0 ? '编辑失败' : '新增失败'  ;
                }
            }

        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


}