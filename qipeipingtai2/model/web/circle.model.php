<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/23
 * Time: 23:08
 */
class WebCircleModel extends Model
{
    public function getJustOneCircleById($id){
        $res = $this->table('circle')->where(array('id'=>$id,'is_delete'=>0))->getOne();
        return $res;
    }
    //生成圈子ID           $t=time()
    public function makeID($t){
        $zTime = 1495101488;
        $_time = $t;
        $_time = $_time - mt_rand(1,10000000) - mt_rand(1,10000000);
        $_time = substr($_time, 2);
        $rst = $this->table('circle')->where(array('vid'=>$_time))->getOne();
        if($rst){
            $nTime = $zTime - mt_rand(1,10000000) - mt_rand(1,10000000)- mt_rand(1,100000);
            $this->makeID($nTime);
        }else{
            return $_time;
        }
    }
    //厂商发布圈子
    public function firmsToCircle($firmId,$content,$img,$area){
        $data = array(
            'vid'=>$this->makeID(time()),
            'circle_type'=>1,'about_id'=>0,
            'content'=>$content,'imgs'=>$img,
            'parent_id'=>0,'level'=>1,
            'type'=>1,'fu_id'=>$firmId,
            'comments'=>0,'create_time'=>date('Y-m-d H:i:s',time()),
            'collection'=>0,'area'=>$area,
        );
        $res = $this->table('circle')->insert($data);
        return $res;
    }
    //厂商分享邀请码到圈子
    public function inviteToCircle($firmId,$firm_ID,$content){
        $data = array(
            'vid'=>$this->makeID(time()),
            'circle_type'=>2,'about_id'=>$firmId,
            'content'=>$content,'imgs'=>'',
            'parent_id'=>0,'level'=>1,
            'type'=>1,'fu_id'=>$firmId,
            'comments'=>0,'create_time'=>date('Y-m-d H:i:s',time()),
            'collection'=>0,'area'=>'',
        );
        $res = $this->table('circle')->insert($data);
        return $res;
    }
    //获取圈子
    public function getCircleData($keywords,$firmId,$page,$pageSize,$user,$userType=1){
        $start = ($page-1)*$pageSize;
        $where = 'a.`level`=1 AND a.is_delete=0';
        if($keywords){
            $where .= ' and a.content like "%'.$keywords.'%"';
        }

        if($firmId){
            $where .= ' and a.type=1 and a.fu_id='.$firmId;
        }

//SELECT a.*,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname  FROM circle as a LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id WHERE a.`level`=1 AND a.is_delete=0;
        $join = 'LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id';
        $field= 'a.*,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname,b.EnterpriseID,c.uid,(case a.type when 1 THEN b.head_pic ELSE c.facepic END) as head_pic,b.face_pic,b.companyname';
        $count = $this->table('circle as a')->jion($join)->where($where)->count();
        $data  = $this->table('circle as a')->field($field)->jion($join)->where($where)->order('a.create_time desc')->limit($start,$pageSize)->get();
        //查询我收藏的
        $myCollectArr = array();
        if($user){
            $rst = $this->table('collect_circle')->field('GROUP_CONCAT(circle_id SEPARATOR ",") as circle_id')->where(array('type'=>$userType,'fu_id'=>$user['id']))->getOne();
            if($rst['circle_id']){
                $myCollectArr = explode(',',$rst['circle_id']);
            }
        }
        if($data){
            foreach ($data as $k=>$v){
                $data[$k]['fxFirm'] = array();
                if($v['circle_type']==2){
                    $ff = $this->table('firms')->field('EnterpriseID,face_pic,companyname')->where(array('id'=>$v['about_id']))->getOne();
                    $data[$k]['fxFirm'] = $ff;
                }

                $pingLun = $this->table('circle as a')->field($field)->jion($join)
                    ->where(array('`level`'=>2,'parent_id'=>$v['id']))->order('a.create_time desc')->get();
                $data[$k]['pl'] = array();
                foreach ($pingLun as $ki=>$kv){
                    $reply = $this->table('circle as a')->field($field)->jion($join)
                        ->where(array('`level`'=>3,'parent_id'=>$kv['id'],'is_delete'=>0))->get();
                    foreach ($reply as $ri=>$rv){
                        $reply[$ri]['reply'] = $pingLun[$ki];
                        $data[$k]['pl'][]    = $reply[$ri];
                    }
                    if($kv['is_delete']==0){
                        $pingLun[$ki]['reply'] = array();
                        $data[$k]['pl'][] = $pingLun[$ki];
                    }
                }

                $data[$k]['imgs']=$v['imgs']?explode(',',$v['imgs']):array();
                //判断圈子是否收藏
                $data[$k]['is_collect'] = false;
                if(in_array($v['id'],$myCollectArr)){
                    $data[$k]['is_collect'] = true;
                }
            }
        }
        return array('list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
    }
    //获取收藏的圈子
    public function getCollectCircle($keywords,$firmId,$page,$pageSize){
        $start = ($page-1)*$pageSize;
        $where = 'a.`level`=1 AND a.is_delete=0';
        if($keywords){
            $where .= ' and a.content like "%'.$keywords.'%"';
        }

        //查询我收藏的
        $myCollectArr = array();
        $myCollectStr = ' and 1=2';
        if($firmId){
            $rst = $this->table('collect_circle')->field('GROUP_CONCAT(circle_id SEPARATOR ",") as circle_id')->where(array('type'=>1,'fu_id'=>$firmId))->getOne();
            if($rst['circle_id']){
                $myCollectArr = explode(',',$rst['circle_id']);
                $myCollectStr = ' and a.id in ( '.$rst['circle_id'].' ) ';
            }
        }
        $where .= $myCollectStr;
//SELECT a.*,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname  FROM circle as a LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id WHERE a.`level`=1 AND a.is_delete=0;
        $join = 'LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id';
        $field= 'a.*,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname,b.EnterpriseID,c.uid,(case a.type when 1 THEN b.head_pic ELSE c.facepic END) as head_pic,b.face_pic,b.companyname';
        $count = $this->table('circle as a')->jion($join)->where($where)->count();
        $data  = $this->table('circle as a')->field($field)->jion($join)->where($where)->order('a.create_time desc')->limit($start,$pageSize)->get();
        if($data){
            foreach ($data as $k=>$v){
                $pingLun = $this->table('circle as a')->field($field)->jion($join)
                    ->where(array('`level`'=>2,'parent_id'=>$v['id']))->order('a.create_time desc')->get();
                $data[$k]['pl'] = array();
                foreach ($pingLun as $ki=>$kv){
                    $reply = $this->table('circle as a')->field($field)->jion($join)
                        ->where(array('`level`'=>3,'parent_id'=>$kv['id'],'is_delete'=>0))->get();
                    foreach ($reply as $ri=>$rv){
                        $reply[$ri]['reply'] = $pingLun[$ki];
                        $data[$k]['pl'][]    = $reply[$ri];
                    }
                    if($kv['is_delete']==0){
                        $pingLun[$ki]['reply'] = array();
                        $data[$k]['pl'][] = $pingLun[$ki];
                    }
                }

                $data[$k]['imgs']=$v['imgs']?explode(',',$v['imgs']):array();
                //判断圈子是否收藏
                $data[$k]['is_collect'] = false;
                if(in_array($v['id'],$myCollectArr)){
                    $data[$k]['is_collect'] = true;
                }
            }
        }
        return array('list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
    }
    //评论圈子
    public function commentCircle($type,$fu_Id,$pid,$content){
        $join = 'LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id';
        $field= 'a.*,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname';
        $parent = $this->table('circle as a')->field($field)->jion($join)->where(array('a.id'=>$pid))->getOne();
        if($parent){
            $data = array(
                'vid'=>$parent['vid'],
                'circle_type'=>1,'about_id'=>0,
                'content'=>$content,'imgs'=>'',
                'parent_id'=>$pid,'level'=>2,
                'type'=>$type,'fu_id'=>$fu_Id,
                'comments'=>0,'create_time'=>date('Y-m-d H:i:s',time()),
                'collection'=>0,'area'=>'',
            );
            $res = $this->table('circle')->insert($data);
            if($res){

                $this->query('UPDATE circle SET comments=comments+1 WHERE id='.$pid);
                $data['id']           = $res;
                $data['uname']        = '';
                $data['head_pic']     = '';
                $data['EnterpriseID'] = '';
                $data['uid']          = '';
                $data['pName']        = '';
                $data['cirType']      = 0;
                $data['cirFuID']      = 0;
                if($parent){
                    $data['pName']    = $parent['uname'];
                    $data['cirType']  = $parent['type'];
                    $data['cirFuID']  = $parent['fu_id'];
                }

                if($type==1){
                    $rst = $this->table('firms')->where(array('id'=>$fu_Id))->getOne();
                    if($rst){
                        $data['uname']        = $rst['uname'];
                        $data['head_pic']     = $rst['head_pic'];
                        $data['EnterpriseID'] = $rst['EnterpriseID'];
                    }
                }else{
                    $rst = $this->table('sales_user')->where(array('id'=>$fu_Id))->getOne();
                    if($rst){
                        $data['uname']    = $rst['uname'];
                        $data['head_pic'] = $rst['facepic'];
                        $data['uid']      = $rst['uid'];
                    }
                }
                $msgMo = model('web.msg','mysql');
                $msgMo->toSaveMsg(4,$res,$data['uname'].'评论了您的动态',$parent['type']==1?9:10,$parent['fu_id']);
                return array('status'=>1,'msg'=>'评论成功','list'=>$data);
            }else{
                return array('status'=>2,'msg'=>'评论失败，请重试');
            }
        }else{
            return array('status'=>2,'msg'=>'评论失败，请重试');
        }


    }

    //回复评论
    public function replyComment($type,$fu_Id,$pid,$content){
        $join = 'LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id';
        $field= 'a.*,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname,b.EnterpriseID,c.uid,(case a.type when 1 THEN b.head_pic ELSE c.facepic END) as head_pic';
        $parent = $this->table('circle as a')->field($field)->jion($join)->where(array('a.id'=>$pid))->getOne();

        if($parent){
            $this->query('UPDATE circle SET comments=comments+1 WHERE id='.$parent['parent_id']);
            $p2 = $this->table('circle as a')->where(array('a.id'=>$parent['parent_id']))->getOne();
            if($p2){
                $data = array(
                    'vid'=>$p2['vid'],
                    'circle_type'=>1,'about_id'=>0,
                    'content'=>$content,'imgs'=>'',
                    'parent_id'=>$pid,'level'=>3,
                    'type'=>$type,'fu_id'=>$fu_Id,
                    'comments'=>0,'create_time'=>date('Y-m-d H:i:s',time()),
                    'collection'=>0,'area'=>'',
                );
                $res = $this->table('circle')->insert($data);
                if($res){
                    $data['cirType'] = 0;
                    $data['cirFuID'] = 0;
                    $data['cirType'] = $p2['type'];
                    $data['cirFuID'] = $p2['fu_id'];
                    $data['id']           = $res;
                    $data['uname']        = '';
                    $data['head_pic']     = '';
                    $data['EnterpriseID'] = '';
                    $data['uid']          = '';
                    if($type==1){
                        $rst = $this->table('firms')->where(array('id'=>$fu_Id))->getOne();
                        if($rst){
                            $data['uname']        = $rst['uname'];
                            $data['head_pic']     = $rst['head_pic'];
                            $data['EnterpriseID'] = $rst['EnterpriseID'];
                        }
                    }else{
                        $rst = $this->table('sales_user')->where(array('id'=>$fu_Id))->getOne();
                        if($rst){
                            $data['uname']    = $rst['uname'];
                            $data['head_pic'] = $rst['facepic'];
                            $data['uid']      = $rst['uid'];
                        }
                    }
                    $data['reply'] = $parent;
                    $msgMo = model('web.msg','mysql');
                    $msgMo->toSaveMsg(5,$res,$data['uname'].'回复了您的评论',$parent['type']==1?9:10,$parent['fu_id']);
                    return array('status'=>1,'msg'=>'评论成功','list'=>$data);
                }else{
                    return array('status'=>2,'msg'=>'评论失败，请重试');
                }
            }
        }
        return array('status'=>2,'msg'=>'评论失败，请重试');
    }










}