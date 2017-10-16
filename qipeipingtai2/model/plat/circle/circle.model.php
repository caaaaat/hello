<?php

/**
 * 互动圈子
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/13
 * Time: 11:53
 */
class PlatCircleCircleModel extends Model
{
    /**
     * 获取数据统一入口
     * @param $d
     * @return array|void
     */
    public function getLists($d){
        if($d['type'] == 1){
            $res = $this->getCircle($d);
        }elseif ($d['type'] == 2){
            $res = $this->getComment($d);
        }else{
            $res = array('massageCode'=>0,'massage'=>'非法操作');
        }
        return $res ;
    }


    /**
     * 圈子
     * @param $d
     * @return array
     */
    private function getCircle($d){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = 'a.is_delete=0 and level=1' ;

        if($d['status']){
            $where  .= ' and a.type=' .$d['status'];
        }
        if($d['keywords']){
            $where  .= ' and (A.VID like "%' .$d['keywords'].'%" or b.EnterpriseID like "%' .$d['keywords'].'%" or b.uname like "%' .$d['keywords'].'%" or b.companyname like "%' .$d['keywords'].'%" or c.uId like "%' .$d['keywords'].'%" or c.uname like "%' .$d['keywords'].'%")';
        }

        if($suppProv){
            $where .= ' and ( CASE WHEN a.type=1 THEN b.province ="'.$suppProv.'"  WHEN a.type=2 THEN c.area ="'.$suppProv.'" END )';
        }

        //$join  = ' left join firms b on a.fu_id=b.id left join sales_user c on a.fu_id=c.id' ;

        $join   = ' LEFT JOIN firms b on a.fu_id=b.id' ;
        $join  .= ' LEFT JOIN sales_user c on a.fu_id=c.id' ;

        $field  = 'a.id,a.vid,a.type,a.area,a.content,a.comments,a.create_time,';
        $field .= 'case a.type when 1 then b.EnterpriseID when 2 then c.uId end  as exID,';
        $field .= 'case a.type when 1 then b.uname when 2 then c.uname end  as uname,';
        $field .= 'case a.type when 1 then b.companyname when 2 then c.uname end  as exName';

        $count  = $this->table('circle a') ->jion($join)->where($where)->count();
        $lists  = $this->table('circle a')
            ->field($field)->where($where)
            ->jion($join)
            ->order(array('a.create_time'=>'desc','a.id'=>'asc'))
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
     * 评论
     * @param $d
     * @return array
     */
    private function getComment($d){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $pages = ($d['page']-1)* $d['pageSize'];

        $where = 'a.is_delete=0 and a.level>1' ;
        if($d['status']){
            $where  .= ' and a.type=' .$d['status'];
        }
        if($d['keywords']){
            $where  .= ' and (A.VID like "%' .$d['keywords'].'%" or b.EnterpriseID like "%' .$d['keywords'].'%" or b.uname like "%' .$d['keywords'].'%" or c.uId like "%' .$d['keywords'].'%" or c.uname like "%' .$d['keywords'].'%")';
        }

        if($suppProv){
            $where .= ' and ( CASE WHEN a.type=1 THEN b.province ="'.$suppProv.'"  WHEN a.type=2 THEN c.area ="'.$suppProv.'" END )';
        }

        $join  = ' left join firms b on a.fu_id=b.id left join sales_user c on a.fu_id=c.id' ;

        $field  = 'a.id,a.vid,a.type,a.area,a.content,a.comments,a.create_time,a.level,a.parent_id,';
        $field .= 'case a.type when 1 then b.EnterpriseID when 2 then c.uId end  as exID,';
        $field .= 'case a.type when 1 then b.uname when 2 then c.uname end  as uname,';
        $field .= 'case a.type when 1 then b.companyname when 2 then c.uname end  as exName';


        $count = $this->table('circle a')->jion($join)->where($where)->count();
        $lists = $this->table('circle a')
            ->jion($join)
            ->field($field)
            ->where($where)
            ->order(array('vid'=>'asc','level'=>'asc','create_time'=>'desc','id'=>'asc'))
            ->limit($pages,$d['pageSize'])->get();
        //writeLog($this->lastSql());
        if($lists){
            foreach ($lists as $k => $v){
                if($v['level'] == 3){
                    $field  = 'a.content,a.parent_id,';
                    $field .= 'case a.type when 1 then b.uname when 2 then c.uname end  as uname';
                    $user   = $this->table('circle a')->jion($join)->field($field)->where(array('a.id'=>$v['parent_id']))->getOne() ;
                    $hf     = '回复 '.$user['uname'].': '.$user['content'] .' // '.$v['uname'].': '.$v['content'];
                    $lists[$k]['content'] = $hf ;
                }
            }
            //$lists = arraySort($lists,'vid');
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有符合条件的评论');
        }
        //dump($data);
        return $data;
    }

    /**
     * 删除圈子
     * @param $d
     * @return bool
     */
    public function delContent($d){
        $type= isset($d['type'])  ? $d['type']  : '' ;
        $id  = isset($d['id'])  ? $d['id']  : '' ;
        if( $type && $id ){
            $_table = 'circle' ;
            if($type == 1 || $type == 2){
                $res = $this->table($_table)->where(array('id'=>$id))->update(array('is_delete'=>1,'update_time'=>date('Y-m-d H:i:s',time()))) ;

            }else{
                $res = false ;
            }
            if($res){
                if($type == 1){
                    $ids = $this->table($_table)->field('id')->where(array('parent_id'=>$id,'level'=>2))->get();//先查出二级评论的id
                    $this->table($_table)->where(array('parent_id'=>$id,'level'=>2))->update(array('is_delete'=>1,'update_time'=>date('Y-m-d H:i:s',time()))) ;//删除二级评论
                    if($ids){
                        foreach ($ids as $i){
                            $this->table($_table)->where(array('parent_id'=>$i['id'],'level'=>3))->update(array('is_delete'=>1,'update_time'=>date('Y-m-d H:i:s',time()))) ;//删除三级评论
                        }
                    }
                }
                //记录日志
                $suUser = G('user') ;
                $action = $d['type'] == 1 ? '删除圈子' : '删除评论';
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $res = false ;
        }
        return $res ;
    }


}