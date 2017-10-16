<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/14
 * Time: 21:30
 */
class WebArticleModel extends Model
{
    //获取服务协议
    public function getFuWuXieYi(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>5))->getOne();
        return $res;
    }

    //获取公告
    public function getNoticeList($limit=7,$start=0){
        $res = $this->table('notice')->where('start_time <= "'.date('Y-m-d H:i:s').'"')->limit($start,$limit)->get();
        return $res;
    }
    //获取推送消息
    public function getTuiNotice($limit=7,$start=0){
        $res = $this->table('msg')->where('msgType in (1,2,3)')->order('createTime desc')->limit($start,$limit)->get();
        foreach ($res as $k=>$v){
            switch ($v['msgType']){
                case 1:break;
                case 2:break;
                case 3:
                    $info = $this->table('want_buy as a')->field('h.companyname,a.bID,a.limitation,a.create_time,CONCAT(d.name,"",f.name) as car12Str,CONCAT(b.name,"",c.name) as car34Str')
                        ->jion('inner join firms as h on a.firms_id=h.id left join car_group as b on a.car_group_id=b.id left join car_group as c on b.pid=c.id left join car_group as d on c.pid=d.id left join car_group as f on d.pid=f.id')
                        ->where(array('a.id'=>$v['aboutId']))->getOne();
                    $info['paiS'] = $this->table('want_buy_list')->where(array('want_buy_id'=>$v['aboutId']))->count();
                    $res[$k]['msgText']=$info['companyname'].'发布了求购信息';
                    $res[$k]['aboutId']=$info['bID'];
                    break;
                default:
            }
        }
        return $res;
    }
    //获取促销活动
    public function getActivity($limit=4,$start=0){
        $res = $this->table('article_activity')->field('id,art_ID,title,face_img')->where('')->order('vid asc,create_time desc')->limit($start,$limit)->get();
        return $res;
    }
    //新闻资讯
    public function getNews($limit=3,$start=0){
        $res = $this->table('article_news')->field('id,art_ID,title,face_img')->where('')->order('vid asc,create_time desc')->limit($start,$limit)->get();
        return $res;
    }
    //新手上路
    public function getNewbie($keywords='',$limit=10,$start=0){
        $where = '1=1';
        if($keywords){
            $where .= ' and ( title like "%'.$keywords.'%" or content like "%'.$keywords.'%")';
        }
        $count = $this->table('article_newbie')->where($where)->count();
        $res = $this->table('article_newbie')->field('id,art_ID,title,content')->where($where)->order('vid asc,create_time desc')->limit($start,$limit)->get();
        return array('list'=>$res,'count'=>$count);
    }

    //获取一条公告
    public function getOneNotice($ID){

    }
    //获取一条促销活动
    public function getOneActivity($ID){
        $res = $this->table('article_activity')->field('id,art_ID,title,content,face_img,create_time')->where(array('art_ID'=>$ID))->getOne();
        return $res;
    }
    //获取一条新闻资讯
    public function getOneNews($ID){
        $res = $this->table('article_news')->field('id,art_ID,title,content,face_img,create_time')->where(array('art_ID'=>$ID))->getOne();
        return $res;
    }
    //获取一条新手上路
    public function getOneNewbie($ID){
        $res = $this->table('article_newbie')->field('id,art_ID,title,content,create_time')->where(array('art_ID'=>$ID))->getOne();
        return $res;
    }



}