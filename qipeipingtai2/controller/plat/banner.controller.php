<?php

/**
 * banner 管理
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 9:41
 */
class PlatBannerController extends Controller
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
        $mod    = 'plat.banner';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        /*for ($i = 0; $i<500; $i++){
            $y  = date('Y',time());
            $m  = rand(1,12);
            $d  = rand(1,29);
            $time = date('H:i:s',time());
            $date = $y.'-'.$m.'-'.$d.' '.$time ;
            $type = rand(1,2);
            $pay  = rand(1,3);
            $money= rand(1,2000);
            $firId= rand(1,30);
            $point= rand(1,300);
            $admin= rand(1,5);
            $data = array(
                'type'=>$type,
                'status'=>1,
                'payway'=>$pay,
                'refresh_point'=>$point,
                'firms_id'=>$firId,
                'money'=>$money,
                'info'=>$type == 1 ?'充值VIP':'充值刷新点',
                'create_time'=>$date,
                'admin_id'=>$admin
            );
            $authMo->table('pay_history')->insert($data);
        }*/
        if($isAuth){
            $this->template('plat.banner.list');
        }else{
            dump('没有相关权限');

        }
    }
    public function getBanner(){
        $data     = $this->getRequest('data' ,'');
        $bannerMo = model('plat.banner.banner','mysql');
        $banner   = $bannerMo->getBanner($data)  ;
        exit(json_encode($banner,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 调整banner顺序
     */
    public function saveVID(){
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.banner';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $bannerMo = model('plat.banner.banner','mysql');
            $result   = $bannerMo->saveVID($data);
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
     * 删除banner
     */
     public function delBanner(){
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.banner';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $bannerMo = model('plat.banner.banner','mysql');
            $result   = $bannerMo->delBanner($data);
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

    public function banner(){
        $id       = $this->getRequest('id' ,'');
        $type     = $this->getRequest('type' ,'');
        $style    = $this->getRequest('style' ,'');
        if(!$id || $id == 0 || $style == 1){ //新增
            $banner = array('id'=>0,'img'=>'','title'=>'','url_type'=>'5','url'=>'','art_ID'=>'') ;
        }else{//编辑
            $bannerMo = model('plat.banner.banner','mysql');
            $banner   = $bannerMo->getOneBanner($id);
            //dump($banner) ;
        }
        $this->assign('type',$type) ;
        $this->assign('banner',$banner) ;
        $this->template('plat.banner.banner');
    }

    /**
     * 咨询选择
     */
    public function choiceArticle(){
        $art_type = $this->getRequest('type' ,'');
        /*$bannerMo = model('plat.banner.banner','mysql');
        $article  = $bannerMo->getArticle($art_type,'');

        dump($article) ;*/
        $dh          = $art_type == 1 ? '活动ID' : ( $art_type == 2 ? '咨询ID' : '问题ID' ) ;
        $placeholder = $art_type == 1 ? '活动ID/标题' : ( $art_type == 2 ? '咨询ID/标题' : '问题ID/标题' ) ;
        $this->assign('placeholder',$placeholder) ;
        $this->assign('dh',$dh) ;
        $this->assign('art_type',$art_type) ;
        $this->template('plat.banner.choiceArticle');
    }
    /**
     * 咨询搜索
     */
    public function getArticle(){
        $art_type = $this->getRequest('art_type' ,'');
        $keywords = $this->getRequest('keywords' ,'');
        $bannerMo = model('plat.banner.banner','mysql');
        $article  = $bannerMo->getArticle($art_type,$keywords);
        exit(json_encode($article,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 保存banner
     */
    public function saveBanner(){
        $userId = $this->user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.banner';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0) ;
        if($isAuth){
            $data     = $this->getRequest('data'    ,'');
            $bannerMo = model('plat.banner.banner','mysql');
            $result   = $bannerMo->saveBanner($data);
            if($result){//判断是否保存成功
                $return['massageCode'] = 'success' ;
                $return['massage']     = $data['id'] ? '编辑成功' : '增加成功' ;
            }else{
                $return['massage']     = $data['id'] ? '编辑失败' : '增加失败' ;
            }
        }else{
            $return['massage']    = '没有相关权限';
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

}