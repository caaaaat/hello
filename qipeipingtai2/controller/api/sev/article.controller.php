<?php
/**
 *
 * 文章页面  促销 新闻 新手
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 23:42
 */
class ApiSevArticleController extends Controller{


    private $user = array();
    private $userType = 1;

    public function __construct()
    {
        //获取提交的数据
        $token    = $this->getRequest('token','');
        $userType = $this->getRequest('userType','');
        if($userType==2){
            $this->userType = 2;
        }else{
            $this->userType = 1;
        }
        if($token){
            $userMo = model('api.sev.user','mysql');
            if($userType==2){
                $this->user = $userMo->loginYeWuIs($token);
            }else{
                $this->user = $userMo ->loginIs($token);
            }
        }else{
            $this->user = array('status'=>101,'msg'=>'您还未登录，请登录后重试');
        }
    }

    /**
     * 获取文章列表提示  如果登录  提示未读
     */
    public function getArticleTips(){




    }


    //新闻
    public function getNewsList(){

        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');

        $articleMo = model('api.sev.article','mysql');

        $return = $articleMo ->getNews($p,$pageSize);

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    //清空新闻
    public function clearNews(){

        if($this->user['status']==200){
            $msgMo = model('api.sev.article','mysql');
            $msgMo->clearMsg( $this->userType,$this->user['data']['id'],8);
        }

    }

    //清空活动
    public function clearActive(){

        if($this->user['status']==200){
            $msgMo = model('api.sev.article','mysql');
            $msgMo->clearMsg( $this->userType,$this->user['data']['id'],7);
        }

    }

    //清空通知公告
    public function clearNotice(){

        if($this->user['status']==200){
            $msgMo = model('api.sev.article','mysql');
            $msgMo->clearNotice( $this->userType,$this->user['data']['id']);
        }

    }

    //活动
    public function getActivityList(){

        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');

        $articleMo = model('api.sev.article','mysql');

        $return = $articleMo ->getActivity($p,$pageSize);

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    //新手
    public function getNewbieList(){

        $p     = $this->getRequest('page','1');
        $pageSize = $this->getRequest('pageSize','10');
        $keyword  = $this->getRequest('keyword','');

        $articleMo = model('api.sev.article','mysql');

        $return = $articleMo ->getNewbie($p,$pageSize,$keyword);

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    //获取文章详情
    public function getArticleDetail(){

        $art_ID = $this->getRequest('art_ID','');
        $type   = $this->getRequest('type','');

        if($art_ID&&$type){
            $articleMo = model('api.sev.article','mysql');

            $return = $articleMo ->getArticleDetail($art_ID,$type);

        }else{
            $return['status'] = 101;
            $return['msg']    = '您提交的数据有误';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    //通过id获取厂商详情
    public function getFirmInfo(){

        $EnterpriseID = $this->getRequest('EnterpriseID','');

        if($EnterpriseID){

            $firmsMo = model('web.firms','mysql');
            $res = $firmsMo->getFirmInfoByEnID($EnterpriseID);

            if($res){
                $return['status'] = 200;
                $return['msg']    = '获取厂商信息成功';
                $return['data']   = $res;
            }else{
                $return['status'] = 102;
                $return['msg']    = '获取厂商信息失败';
            }

        }else{
            $return['status'] = 101;
            $return['msg']    = '您提交的数据有误';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


}