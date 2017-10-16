<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/16
 * Time: 15:17
 */
class PcArticleController extends Controller
{
    //新闻
    public function getNewsList(){
        $page  = $this->getRequest('page',1);
        $start    = ($page-1)*6;
        $articleMo = model('web.article','mysql');
        $news = $articleMo->getNews(6,$start);
        exit(json_encode(array('list'=>$news,'page'=>$page),JSON_UNESCAPED_UNICODE));
    }
    //活动
    public function getActivityList(){
        $page  = $this->getRequest('page',1);
        $start    = ($page-1)*6;
        $articleMo = model('web.article','mysql');
        $news = $articleMo->getActivity(6,$start);
        exit(json_encode(array('list'=>$news,'page'=>$page),JSON_UNESCAPED_UNICODE));
    }
}