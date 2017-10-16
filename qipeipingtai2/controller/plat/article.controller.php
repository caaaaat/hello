<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10
 * Time: 1:07
 */
class PlatArticleController  extends Controller
{

    private $user = array();
    public function __construct()
    {
        //检查是否登录
        $mo         = model('suAdmin','mysql');
        $this->user = $mo->loginIs();
    }

    /**
     * 文章列表页
     */
    public function lists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.article';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $this->template('plat.article.list');
        }else{
            dump('没有相关权限');

        }
    }

    /**
     * 获取列表统一入口
     */
    public function getLists(){
        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.article';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $data       = $this->getRequest('data' ,'');
            $articleMo  = model('plat.article.article','mysql');
            $res        = $articleMo->getLists($data)  ;
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
        $mod    = 'plat.article';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data      = $this->getRequest('data'    ,'');
            $articleMo = model('plat.article.article','mysql');
            $result    = $articleMo->saveVID($data);
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
     * 详情
     */
    public function getContent(){
        $id         = $this->getRequest('id'    ,'');
        $type       = $this->getRequest('type'    ,'');
        $articleMo  = model('plat.article.article','mysql');
        $res = $articleMo->getContent($id,$type);
        $this->assign('content',$res) ;
        if($type == 1){
            $this->template('plat.article.content') ;
        }else{
            $this->template('plat.article.question') ;
        }

    }

    /**
     * 删除咨询/删除问题
     */
    public function delContent(){
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.article';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data      = $this->getRequest('data'    ,'');
            $articleMo = model('plat.article.article','mysql');
            $result    = $articleMo->delContent($data);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = '删除成功' ;
            }else{
                $return['massage']     = '删除失败'  ;
            }
        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    public function editContent(){
        $id   = $this->getRequest('id'  ,'');
        $type = $this->getRequest('type','');
        if($id > 0){
            $articleMo = model('plat.article.article','mysql');
            $res = $articleMo->getContent($id,$type);

        }else{
            $res = array('id'=>'','title'=>'','face_img'=>'','content'=>'','value'=>'');
        }
        $this->assign('type',$type) ;
        $this->assign('content',$res) ;

        $this->template('plat.article.editContent') ;
    }
    /**
     * 保存活动
     */
    public function saveContent(){

        //检查用户权限
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.article';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $activityMo = model('plat.article.article','mysql');
            $result   = $activityMo->saveContent($data);
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

}