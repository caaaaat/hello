<?php
/**
 *
 * 文章模型
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1
 * Time: 0:15
 */

class ApiSevArticleModel extends Model{

    /**
     * 获取促销活动列表
     * @param $p
     * @param $pageSize
     * @return mixed
     */
    public function getActivity($p,$pageSize)
    {

        $page = (intval($p)-1)*$pageSize;

        $list  = $this->table('article_activity')->field('art_ID,title,face_img,substring(create_time,1,10) as create_time')->where('')->limit($page,$pageSize)->order('vid asc,create_time desc')->get();
        $count = $this->table('article_activity')->where('')->count();

        return $data = array('list'=>$list,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);
    }


    /**
     * 获取新闻资讯列表
     * @param $p
     * @param $pageSize
     * @return mixed
     */
    public function getNews($p,$pageSize)
    {

        $page = (intval($p)-1)*$pageSize;

        $list  = $this->table('article_news')->field('art_ID,title,face_img,substring(create_time,1,10) as create_time')->where('')->limit($page,$pageSize)->order('vid asc,create_time desc')->get();
        $count = $this->table('article_news')->where('')->count();

        return $data = array('list'=>$list,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);
    }

    /**
     * 获取新手上路列表
     * @param $p
     * @param $pageSize
     * @param $keyword
     * @return mixed
     */
    public function getNewbie($p,$pageSize,$keyword)
    {

        $page = (intval($p)-1)*$pageSize;
        $find = '';

        if($keyword){
            $find = "title like '%$keyword%'";
        }

        $list  = $this->table('article_newbie')->field('art_ID,title,substring(create_time,1,10) as create_time')->where($find)->limit($page,$pageSize)->order('vid asc,create_time desc')->get();
        $count = $this->table('article_newbie')->where($find)->count();

        return $data = array('list'=>$list,'count'=>$count,'status'=>'200','page'=>$p,'pageSize'=>$pageSize);
    }


    /**
     * 获取文章详情
     * @param $art_ID
     * @param $type
     * @return array
     */
    public function getArticleDetail($art_ID,$type){

        $table = ($type=='news')?'article_news':(($type=='activity')?'article_activity':'article_newbie');

        $filed = "art_ID,title,substring(create_time,1,10) as create_time,content";
        //获取字段
        if($table=="article_news"||$table=="article_activity"){

            $filed = $filed.',face_img';
        }

        $data  = $this->table($table)->field($filed)->where(array('art_ID'=>$art_ID))->getOne();

        if($data){
            $return = array('data'=>$data,'status'=>'200','msg'=>'获取成功');
        }else{
            $return = array('data'=>$data,'status'=>'201','msg'=>'文章不见了');
        }

        return $return;
    }

    /**
     * 清空消息列表
     * @param $userType
     * @param $id
     * @param $msgType
     */
    public function clearMsg( $userType,$id,$msgType){

        $res = $this->table('msg a')->jion("left join msg_read b on b.msg_id=a.id and b.type=$userType and b.fu_id=$id")->where("a.msgType=$msgType")->get();
        if($res){
            foreach($res as $item){

                if(!$item['fu_id']){

                    $this->table('msg_read')->insert(array('msg_id'=>$item['id'],'type'=>$userType,'fu_id'=>$id,'create_time'=>date("Y-m-d H:i:s")));
                }
            }

        }

    }

    /**
     * 清空通知公告
     * @param $userType
     * @param $id
     */
    public function clearNotice( $userType,$id){

        if($userType==1){
            $firm  = $this->table('firms')->where(array('id'=>$id))->getOne();
            $where = '( a.toType=0 AND a.toId=0 ) or ( a.toType='.$firm['classification'].' AND a.toId=0 ) or ( a.toType=9 AND a.toId='.$id.' )';
        }else{
            $where = '( a.toType=0 AND a.toId=0 ) or ( a.toType=10 AND a.toId='.$id.' )';
        }

        $res = $this->table('msg as a')->jion("LEFT JOIN msg_read as c on c.msg_id=a.id and c.fu_id=$id and c.type=$userType")->where($where)->get();

        if($res){
            foreach($res as $item){

                if(!$item['fu_id']){

                    $this->table('msg_read')->insert(array('msg_id'=>$item['id'],'type'=>$userType,'fu_id'=>$id,'create_time'=>date("Y-m-d H:i:s")));
                }
            }

        }

    }

}