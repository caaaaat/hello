<?php

/**
 * 文章管理
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/13
 * Time: 11:53
 */
class PlatArticleArticleModel extends Model
{
    /**
     * 获取数据统一入口
     * @param $d
     * @return array|void
     */
    public function getLists($d){
        if($d['type'] == 1){
            $res = $this->getNews($d);
        }elseif ($d['type'] == 2){
            $res = $this->getNewbie($d);
        }elseif ($d['type'] == 3){
            $res = $this->getFirmIntroduction();
        }else{
            $res = array('massageCode'=>0,'massage'=>'非法操作');
        }
        return $res ;
    }


    /**
     * 新闻资讯
     * @param $d
     * @return array
     */
    private function getNews($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = 'a.is_del=0' ;

        if($d['keywords']){
            $where  .= ' and (a.art_ID like "%' .$d['keywords'].'%" or a.title like "%' .$d['keywords'].'%")';
        }
        $join   = ' inner join su_user b on a.admin_id=b.id' ;

        $field  = 'a.id,a.vid,a.art_ID,a.face_img,a.title,a.create_time,';
        $field .= 'b.code';


        $count  = $this->table('article_news a') ->jion($join)->where($where)->count();
        $lists  = $this->table('article_news a')
            ->field($field)->where($where)
            ->jion($join)
            ->order(array('a.vid'=>'asc','a.create_time'=>'desc','a.id'=>'asc'))
            ->limit($pages,$d['pageSize'])
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有记录');
        }
        return $data;
    }

    /**
     * 新手上路
     * @param $d
     * @return array
     */
    private function getNewbie($d){
        $pages = ($d['page']-1)* $d['pageSize'];

        $where = 'a.is_del=0' ;
        if($d['keywords']){
            $where  .= ' and (a.art_ID like "%' .$d['keywords'].'%" or a.title like "%' .$d['keywords'].'%")';
        }

        $join   = ' inner join su_user b on a.admin_id=b.id' ;

        $field  = 'a.id,a.vid,a.art_ID,a.title,a.create_time,';
        $field .= 'b.code';

        $count = $this->table('article_newbie a')->where($where)->count();
        $lists = $this->table('article_newbie a')
            ->jion($join)
            ->field($field)
            ->where($where)
            ->order(array('vid'=>'asc','create_time'=>'desc','id'=>'asc'))
            ->limit($pages,$d['pageSize'])->get();
        //writeLog($this->lastSql());
        if($lists){

            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的经销商');
        }
        return $data;
    }


    /**
     * 公司简介
     * @return array
     */
    private function getFirmIntroduction(){

        $res = $this->table('base_ini')->field('id,name,value as content')->where(array('id'=>9))->getOne() ;
        if($res){
            $res = str_replace(array("\n","\r","\n\r"),'<br/>',$res);
        }else{

        }
        return $res ;
    }

    /**
     * 调整顺序
     * @param $d
     * @return bool
     */
    public function saveVID($d){
        $type= isset($d['type'])  ? $d['type']  : '' ;
        $id  = isset($d['id'])  ? $d['id']  : '' ;
        $vid = isset($d['vid']) ? $d['vid'] : '' ;
        if($type == 1 || $type == 2){
            if($id && $vid){
                $_table = $type == 1 ? 'article_news' : 'article_newbie';
                $res = $this->table($_table)->where(array('id'=>$id))->update(array('vid'=>$vid,'update_time'=>date('Y-m-d H:i:s',time()))) ;
                if($res){
                    //记录日志
                    $suUser = G('user') ;
                    $action = $d['type'] == 1 ? '调整新闻咨询顺序' : '调整新手上路顺序';
                    model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
                }
            }else{
                $res = false ;
            }
        }else{
            $res = false ;
        }

        return $res ;
    }

    /**
     * 详情
     * @param $id
     * @param $type
     * @return bool|mixed
     */
    public function getContent($id,$type){
        if($type == 1){
            $res = $this->table('article_news')
                ->field('id,title,face_img,content')
                ->where(array('id'=>$id,'is_del'=>0))->getOne() ;
            $res = str_replace(array("\n","\r","\n\r"),'<br/>',$res);
        }elseif ($type == 2){
            $res = $this->table('article_newbie')
                ->field('id,title,content,create_time')
                ->where(array('id'=>$id,'is_del'=>0))->getOne() ;
            $res = str_replace(array("\n","\r","\n\r"),'<br/>',$res);
        }elseif ($type == 3){
            $res = $this->getFirmIntroduction();
        }else{
            $res = false ;
        }
        return $res ;
    }

    /**
     * 删除咨询/删除问题
     * @param $d
     * @return bool
     */
    public function delContent($d){
        $type= isset($d['type'])  ? $d['type']  : '' ;
        $id  = isset($d['id'])  ? $d['id']  : '' ;
        if( $type && $id ){
            $_table = $type == 1 ? 'article_news' : 'article_newbie';
            if($type == 1 || $type == 2){
                $res = $this->table($_table)->where(array('id'=>$id))->update(array('is_del'=>1,'update_time'=>date('Y-m-d H:i:s',time()))) ;
            }else{
                $res = false ;
            }
            if($res){
                //记录日志
                $suUser = G('user') ;
                $action = $d['type'] == 1 ? '删除咨询' : '删除问题';
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $res = false ;
        }
        return $res ;
    }

    /**
     * 保存
     * @param $d
     * @return mixed
     */
    public function saveContent($d){
        $suUser = G('user') ;
        if($d['id']){
            if( $d['type'] == 1 ){
                $arr = array(
                    'face_img'=>$d['img'],
                    'title'=>$d['title'],
                    'content'=>$d['content'],
                    'update_time'=>date('Y-m-d H:i:s')
                ) ;
                $res = $this ->table('article_news')->where(array('id'=>$d['id']))->update($arr);
                $action = '编辑新闻咨询' ;
            }elseif ( $d['type'] == 2 ){
                $arr = array(
                    'title'=>$d['title'],
                    'content'=>$d['content'],
                    'update_time'=>date('Y-m-d H:i:s')
                ) ;
                $res = $this ->table('article_newbie')->where(array('id'=>$d['id']))->update($arr);
                $action = '编辑新手上路' ;
            }elseif ( $d['type'] == 3 ){
                $arr = array(
                    'value'=>$d['content'],
                    'update_time'=>date('Y-m-d H:i:s')
                ) ;
                $res = $this ->table('base_ini')->where(array('id'=>$d['id']))->update($arr);
                $action = '编辑公司简介' ;
            }else{
                $res = false ;
                $action = '' ;
            }

        }else{
            if( $d['type'] == 1 ){
                $count  = $this->table('article_news')->count();
                $art_ID = $this->getID();
                $arr = array(
                    'face_img'=>$d['img'],
                    'title'=>$d['title'],
                    'content'=>$d['content'],
                    'vid'=>$count + 1 ,
                    'art_ID'=>$art_ID,
                    'admin_id'=>$suUser['id'],

                    'create_time'=>date('Y-m-d H:i:s'),
                    'update_time'=>date('Y-m-d H:i:s'),
                ) ;
                $res = $this ->table('article_news')->insert($arr);
                $action = '新增新闻咨询' ;

                if($res){
                    //增加内容至msg表
                    $data = array(
                        'msgType'=>8,
                        'aboutId'=>$art_ID,
                        'msgText'=>$d['title'],
                        'toType' =>0,
                        'toId'   =>0,
                        'createTime'=>date('Y-m-d H:i:s')
                    );
                    $this->table('msg')->insert($data);
                }


            }elseif ( $d['type'] == 2 ){
                $count  = $this->table('article_newbie')->count();
                $art_ID = $this->getID();
                $arr = array(
                    'title'=>$d['title'],
                    'content'=>$d['content'],
                    'vid'=>$count + 1 ,
                    'art_ID'=>$art_ID,
                    'admin_id'=>$suUser['id'],

                    'create_time'=>date('Y-m-d H:i:s'),
                    'update_time'=>date('Y-m-d H:i:s'),
                ) ;
                $res = $this ->table('article_newbie')->insert($arr);
                $action = '新增新手上路' ;
            }else{
                $res = false ;
                $action = '' ;
            }
        }
        if($res){
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }


    private function getID(){
        $time = time();
        $fix  = substr($time,2,8) ;
        $rand = rand(100,999);
        $code = $fix . $rand ;
        $res = $this ->table('article_activity')->where(array('art_ID'=>$code))->getOne();
        if($res){
            $this->getID();
        }else{
            return $code ;
        }
    }

}