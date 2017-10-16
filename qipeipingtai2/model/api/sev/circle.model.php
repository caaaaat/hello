<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/19
 * Time: 17:57
 */
class ApiSevCircleModel extends Model
{
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
//获取圈子
    public function getCircleData($keywords,$firmId,$page,$pageSize,$user,$userType=1){
        $start = ($page-1)*$pageSize;
        $where = 'a.`level`=1 AND a.is_delete=0';
        if($keywords){
            $where .= ' and a.content like "%'.$keywords.'%"';
        }

        if($firmId){
            if($userType==2){
                $where .= ' and a.type=2 and a.fu_id='.$firmId;
            }else{
                $where .= ' and a.type=1 and a.fu_id='.$firmId;
            }
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
                    $ff = $this->table('firms')->field('EnterpriseID,face_pic,companyname,type')->where(array('id'=>$v['about_id']))->getOne();
                    $data[$k]['fxFirm'] = $ff;
                }
                $data[$k]['imgs']=$v['imgs']?explode(',',$v['imgs']):array();
                //判断圈子是否收藏
                $data[$k]['is_collect'] = false;
                if(in_array($v['id'],$myCollectArr)){
                    $data[$k]['is_collect'] = true;
                }
                $data[$k]['create_time'] = date('m-d H:i' ,strtotime($v['create_time']));
                $data[$k]['can_delete'] = false;
                if($user){
                    if($userType==$v['type'] && $user['id']==$v['fu_id']){
                        $data[$k]['can_delete'] = true;
                    }
                }
            }
        }
        return array('list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
    }

    //获取收藏的圈子
    public function getCollectCircle($keywords,$userId,$page,$pageSize,$userType=1){
        $start = ($page-1)*$pageSize;
        $where = 'a.`level`=1 AND a.is_delete=0';
        if($keywords){
            $where .= ' and a.content like "%'.$keywords.'%"';
        }

        //查询我收藏的
        $myCollectArr = array();
        $myCollectStr = ' and 1=2';
        if($userId){
            $rst = $this->table('collect_circle')->field('GROUP_CONCAT(circle_id SEPARATOR ",") as circle_id')->where(array('type'=>$userType,'fu_id'=>$userId))->getOne();
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
                $data[$k]['imgs']=$v['imgs']?explode(',',$v['imgs']):array();
                //判断圈子是否收藏
                $data[$k]['is_collect'] = false;
                if(in_array($v['id'],$myCollectArr)){
                    $data[$k]['is_collect'] = true;
                }
                $data[$k]['create_time'] = date('m-d H:i' ,strtotime($v['create_time']));
                $data[$k]['can_delete'] = false;
                if($userId){
                    if($userType==$v['type'] && $userId==$v['fu_id']){
                        $data[$k]['can_delete'] = true;
                    }
                }

            }
        }
        return array('list'=>$data,'page'=>$page,'pageSize'=>$pageSize,'count'=>$count);
    }

    /**
     * @param $id
     * @param $userId
     * @param int $userType
     * @return array
     */
    public function getCircleLevelOne($id,$userId,$userType=1){
        $join = 'LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id';
        $field= 'a.*,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname,b.EnterpriseID,c.uid,(case a.type when 1 THEN b.head_pic ELSE c.facepic END) as head_pic,b.face_pic,b.companyname';
        $circle = $this->table('circle as a')->field($field)->where(array('a.id'=>$id,'a.level'=>1))->jion($join)->getOne();
        $return = array('status'=>200,'is'=>false,'msg'=>'该动态不存在或已被删除','data'=>'');
        if($circle){
            if($circle['is_delete']==0){
                $circle['is_collect'] = false;
                $circle['can_delete'] = false;
                if($userId){
                    if($userId == $circle['fu_id'] && $userType == $circle['type']){
                        $circle['can_delete'] = true;
                    }
                    $rst = $this->table('collect_circle')->where(array('type'=>$userType,'fu_id'=>$userId,'circle_id'=>$circle['id']))->getOne();
                    if($rst){
                        $circle['is_collect'] = true;
                    }
                }
                $pingLun = $this->table('circle as a')->field($field)->jion($join)
                    ->where(array('`level`'=>2,'parent_id'=>$circle['id']))->order('a.create_time desc')->get();
                $circle['pl'] = array();
                foreach ($pingLun as $ki=>$kv){
                    $pingLun[$ki]['create_time'] = date('m-d H:i' ,strtotime($kv['create_time']));
                    $reply = $this->table('circle as a')->field($field)->jion($join)
                        ->where(array('`level`'=>3,'parent_id'=>$kv['id'],'is_delete'=>0))->get();
                    foreach ($reply as $ri=>$rv){
                        $reply[$ri]['create_time'] = date('m-d H:i' ,strtotime($rv['create_time']));
                        $reply[$ri]['reply'] = $pingLun[$ki];
                        $circle['pl'][]    = $reply[$ri];
                    }
                    if($kv['is_delete']==0){
                        $pingLun[$ki]['reply'] = array();
                        $circle['pl'][] = $pingLun[$ki];
                    }
                }
                $circle['create_time'] = date('m-d H:i' ,strtotime($circle['create_time']));
                $circle['imgs']=$circle['imgs']?explode(',',$circle['imgs']):array();
                $return = array('status'=>200,'is'=>true,'data'=>$circle);

            }

        }
        return $return;
    }
    //厂商发布圈子
    public function firmsToCircle($userType,$userId,$content,$img,$area,$circleType,$EnterpriseID,$cardId){

        if($circleType==1){//普通
            $data = array(
                'vid'=>$this->makeID(time()),
                'circle_type'=>1,'about_id'=>0,
                'content'=>$content,'imgs'=>$img,
                'parent_id'=>0,'level'=>1,
                'type'=>$userType,'fu_id'=>$userId,
                'comments'=>0,'create_time'=>date('Y-m-d H:i:s',time()),
                'collection'=>0,'area'=>$area,
            );
        }elseif($circleType==2||$circleType==3) {//邀请码 店铺
            $firm = $this->table("firms")->where("EnterpriseID=$EnterpriseID")->getOne();

            $data = array(
                'vid' => $this->makeID(time()),
                'circle_type' => 2, 'about_id' => $firm['id'],
                'content' => $content, 'imgs' => '',
                'parent_id' => 0, 'level' => 1,
                'type' => $userType, 'fu_id' => $userId,
                'comments' => 0, 'create_time' => date('Y-m-d H:i:s', time()),
                'collection' => 0, 'area' => $area,
            );

        }else{//名片

            $data = array(
                'vid' => $this->makeID(time()),
                'circle_type' => 3, 'about_id' => $cardId,
                'content' => $content, 'imgs' => $img,
                'parent_id' => 0, 'level' => 1,
                'type' => $userType, 'fu_id' => $userId,
                'comments' => 0, 'create_time' => date('Y-m-d H:i:s', time()),
                'collection' => 0, 'area' => $area,
            );


        }


        $res = $this->table('circle')->insert($data);
        return $res;
    }
    //回复
    function replyToCircle($userType,$userId,$userName,$circleId,$punId,$content){
        $join = 'LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id';
        $field= 'a.*,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname';
        $circle = $this->table('circle as a')->field($field)->jion($join)->where(array('a.id'=>$punId,'a.level'=>2))->getOne();
        if($circle){
            $data = array(
                'vid'=>$circle['vid'],
                'circle_type'=>1,'about_id'=>0,
                'content'=>$content,'imgs'=>'',
                'parent_id'=>$circle['id'],'level'=>3,
                'type'=>$userType,'fu_id'=>$userId,
                'comments'=>0,'create_time'=>date('Y-m-d H:i:s',time()),
                'collection'=>0,'area'=>'',
            );
            $res = $this->table('circle')->insert($data);
            if($res){
                $this->query('UPDATE circle SET comments=comments+1 WHERE id='.$circle['parent_id']);
                $msgMo = model('web.msg','mysql');
                $msgMo->toSaveMsg(5,$res,$userName.'回复了您的评论',$circle['type']==1?9:10,$circle['fu_id']);
                return array('status'=>200,'msg'=>'评论成功','list'=>$data);
            }
        }
        return array('status'=>205,'msg'=>'评论失败，请稍后再试');
    }
    //评论
    function commentToCircle($userType,$userId,$userName,$circleId,$content){
        $join = 'LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id';
        $field= 'a.*,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname';
        $circle = $this->table('circle as a')->field($field)->jion($join)->where(array('a.id'=>$circleId,'a.level'=>1))->getOne();
        if($circle){
            $data = array(
                'vid'=>$circle['vid'],
                'circle_type'=>1,'about_id'=>0,
                'content'=>$content,'imgs'=>'',
                'parent_id'=>$circleId,'level'=>2,
                'type'=>$userType,'fu_id'=>$userId,
                'comments'=>0,'create_time'=>date('Y-m-d H:i:s',time()),
                'collection'=>0,'area'=>'',
            );
            $res = $this->table('circle')->insert($data);
            if($res){
                $this->query('UPDATE circle SET comments=comments+1 WHERE id='.$circleId);
                $msgMo = model('web.msg','mysql');
                $msgMo->toSaveMsg(4,$res,$userName.'评论了您的动态',$circle['type']==1?9:10,$circle['fu_id']);
                return array('status'=>200,'msg'=>'评论成功');
            }
        }
        return array('status'=>205,'msg'=>'评论失败，请稍后再试');
    }

}