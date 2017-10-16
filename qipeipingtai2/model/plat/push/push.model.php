<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/15
 * Time: 17:28
 */
class PlatPushPushModel extends Model
{
    public function getLists($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = '' ;

        if($d['keywords']){
            $where  .= ' a.msg like "%' .$d['keywords'].'%" ';
        }
        $join   = ' inner join su_user b on a.admin_id=b.id' ;

        $field  = 'a.id,a.msg,a.start_time,a.create_time,';
        $field .= 'b.name';

        $count  = $this->table('notice a') ->jion($join)->where($where)->count();
        $lists  = $this->table('notice a')
            ->field($field)
            ->where($where)
            ->jion($join)
            ->order(array('a.create_time'=>'desc','a.start_time'=>'desc','a.id'=>'asc'))
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

    public function getContent($id){
        return $this ->table('notice a') ->field('msg')->where('id='.$id)->getOne();
    }

    public function savePush($d){
        $suUser = G('user') ;
        $push   = array(
            'msg'=>$d['msg'],
            'start_time'=>$d['time'],
            'create_time'=>date('Y-m-d H:i:s',time()),
            'admin_id'=>$suUser['id']
        );
        $res = $this->table('notice')->insert($push);
        if($res){
            //记录日志
            $action = '添加推送';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }

    public function addToMsg(){
        $notice = $this->table('notice')->field('id,msg,start_time')->where('state=0 and DATE_FORMAT(start_time,"%Y-%m-%d %H:%i:%s%")')->get() ;
        if($notice){
            foreach ($notice as $n){
                $arr = array(
                    'msgType'=>1,  'aboutId'=>$n['id'],'toType'=>0,
                    'msgText'=>$n['msg'],'createTime'=>$n['start_time']
                );
                $res = $this->table('msg')->insert($arr);
                if($res){
                    $this->table('notice')->where('id='.$n['id'])->update(array('state'=>1));
                }
            }
        }
    }

}