<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 13:19
 */
class PlatBannerBannerModel extends Model
{
    /**
     * banner列表
     * @param $d
     * @return array
     */
    public function getBanner($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $find   = 'a.type='.$d['type'] ;

        $join   = ' left join article_activity b on a.art_id=b.art_ID' ;
        $join  .= ' left join article_newbie c on a.art_id=c.art_ID' ;
        $join  .= ' left join article_news d on a.art_id=d.art_ID' ;

        /* 1 促销活动 article_activity 2 新闻资讯 article_news 3 新手上路 article_newbie */
        $field  = 'a.id,a.vid,a.img,a.title,a.url_type,a.url,';
        $field .= 'case a.url_type when 1 then b.art_ID when 2 then d.art_ID when 3 then c.art_ID end as art_ID,';
        $field .= 'case a.url_type when 1 then b.title when 2 then d.title when 3 then c.title end as art_title';

        $count  = $this->table('banner a')->where($find)->count();
        $lists  = $this->table('banner a')
            ->field($field)->where($find)
            ->jion($join)
            ->order(array('a.vid'=>'asc','a.id'=>'asc'))
            ->group('a.id')
            ->limit($pages,$d['pageSize'])
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有banner记录');
        }
        return $data;
    }

    /**
     * 保存banner
     * @param $d
     * @return bool
     */
    public function saveBanner($d){
        $id  = isset($d['id'])  ? $d['id']  : '' ;
        $art_id = $d['art_id'] ;
        if($d['url_type'] == 5 || $d['url_type'] == 4){
            $art_id = '' ;
        }
        if($d['url_type'] == 1){
            $url =  '/def/activities?ID='.$d['art_id'] ;
        }elseif ( $d['url_type'] == 2 ){
            $url =  '/def/news?ID='.$d['art_id'] ;
        }elseif ( $d['url_type'] == 3){
            $url =  '/def/newbie?ID='.$d['art_id'] ;
        }elseif ( $d['url_type'] == 4){
            $url =  $d['url'] ;
        }else{
            $url =  '' ;
        }
        $arr = array(
            'img'=>$d['img'],
            'title'=>$d['title'],
            'url_type'=>$d['url_type'],
            'url'=>$url,
            'art_id'=>$art_id,
            'update_time'=>date('Y-m-d H:i:s',time())
        ) ;

        //writeLog($arr);die;
        if($id){
            $res = $this->table('banner')->where(array('id'=>$id))->update($arr) ;
            $action = $d['type'] == 1 ? '编辑顶部banner' : '编辑腰部banner';
        }else{
            $vid  = $this->table('banner')->field('vid')->where('type='.$d['type'])->order('vid desc')->getOne() ;
            $arr['vid'] = $vid['vid']+1 ;
            $arr['type'] = $d['type'] ;
            $arr['create_time'] = date('Y-m-d H:i:s',time()) ;
            $res = $this->table('banner')->insert($arr) ;
            $action = $d['type'] == 1 ? '添加顶部banner' : '添加腰部banner';
        }

        if($res){
            //记录日志
            $suUser = G('user') ;
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }

        return $res ;
    }
    /**
     * 调整banner顺序
     * @param $d
     * @return bool
     */
    public function saveVID($d){
        $id  = isset($d['id'])  ? $d['id']  : '' ;
        $vid = isset($d['vid']) ? $d['vid'] : '' ;
        if($id && $vid){
            $res = $this->table('banner')->where(array('id'=>$id))->update(array('vid'=>$vid,'update_time'=>date('Y-m-d H:i:s',time()))) ;
            if($res){
                //记录日志
                $suUser = G('user') ;
                $action = $d['type'] == 1 ? '调整顶部banner顺序' : '调整腰部banner顺序';
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $res = false ;
        }
        return $res ;
    }

    /**
     * 删除banner
     * @param $d
     * @return bool|mixed
     */
    public function delBanner($d){
        $id  = isset($d['id'])  ? $d['id']  : '' ;
        if($id){
            $res = $this->table('banner')->where(array('id'=>$id))->del() ;
            if($res){
                //记录日志
                $suUser = G('user') ;
                $action = $d['type'] == 1 ? '删除顶部banner' : '删除腰部banner';
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $res = false ;
        }
        return $res ;
    }

    /**
     * 获取一条banner
     * @param $id
     * @return mixed
     */
    public function getOneBanner($id){
        $join   = ' left join article_activity b on a.art_id=b.art_ID' ;
        $join  .= ' left join article_newbie c on a.art_id=c.art_ID' ;
        $join  .= ' left join article_news d on a.art_id=d.art_ID' ;

        $field  = 'a.id,a.img,a.title,a.url_type,a.url,';
        $field .= 'case a.url_type when 1 then b.art_ID when 2 then d.art_ID when 3 then c.art_ID end  as art_ID,';
        $field .= 'case a.url_type when 1 then b.title when 2 then d.title when 3 then c.title end  as art_title';

        return $this->table('banner a')
            ->where(array('a.id'=>$id))
            ->field($field)
            ->jion($join)
            ->getOne() ;
    }

    /**
     * 根据url_type 查询不同表的咨询ID 标题
     * @param $art_type
     * @param $keywords
     * @return array
     */
    public function getArticle($art_type,$keywords){

        if($art_type == 1){
            $_table = 'article_activity' ;
        }elseif ($art_type == 2 ){
            $_table = 'article_news' ;
        }else{
            $_table = 'article_newbie' ;
        }
        $where = '' ;
        if($keywords){
            $where .= 'art_ID like "%'.$keywords.'%" or title like "%'.$keywords.'%"' ;
        }
        $lists = $this->table($_table)
            ->where($where)
            ->field('art_ID,title')
            ->order('create_time desc')
            ->get() ;
        if($lists){
            $data    = array('list'=>$lists,'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有任何记录');
        }
        return $data;
    }

}