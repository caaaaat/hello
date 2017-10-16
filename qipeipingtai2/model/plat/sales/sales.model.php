<?php

/**
 * 业务员模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10
 * Time: 11:16
 */
class PlatSalesSalesModel extends Model
{
    /**
     * 获取业务员列表
     * @param $page
     * @param $pageSize
     * @param $status
     * @param $province
     * @param $keywords
     * @return array
     */
    public function getSales($page,$pageSize,$status,$province,$keywords,$order){

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        //初始化总条数
        $count = 0;
        //起始条数
        $pages = ($page-1)* $pageSize;

        $find = 'a.id>0';

        if($suppProv){
            $find .= ' and a.area="'.$suppProv.'"';
        }
        if($status){//状态
            $find   .= ' and a.status ='.$status;
        }

        if($province){//区域

            $find  .= " and a.area ='$province'";
        }

        if($keywords){//关键字
            $findKey = '"%'.$keywords.'%"';
            $find   .= " and (a.`uId` like $findKey or a.`uname` like $findKey or a.`realname` like $findKey )";
        }
        if($order){
            $order   = array('a.last_time'=>$order) ;
        }else{
            //$order   = 'a.id asc' ;
            $order   = array('a.id'=>'asc') ; ;
        }
        $field = 'a.id,a.uId,a.uname,a.realname,a.phone,a.area,a.last_time,a.status,count(b.id) as fir_num';

        $count = $this->table('sales_user a')->where($find)->count();
        $lists = $this->table('sales_user a')->field($field)->where($find)
            ->jion('left join firms_sales_user b on a.id=b.sales_user_di')
            ->order($order)
            ->group('a.id')
            ->limit($pages,$pageSize)
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            //搜索条件
            $search  = array('status'=>$status,'keywords'=>$keywords);
            $data    = array('list'=>$lists,'search'=>$search,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的业务员');
        }

        return $data;


    }

    /**
     * 启用/停用业务员
     * @param $userId
     * @param $status
     * @return int
     */
    public function changeStatus($userId,$status){
        $data = array();
        $data['status'] = $status;
        $result = $this->table('sales_user')->where(array('id'=>$userId))->update($data);
        if($result){
            //记录日志
            $suUser = G('user') ;
            $action = $status == 1 ? '启用业务员' : '停用业务员';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $result;
    }

    /**
     * 重置密码
     * @param $userId
     * @return int
     */
    public function resetPassword($userId){
        $data = array();
        $data['password'] = md5(sha1('7777777').'sw');
        $result = $this->table('sales_user')->where(array('id'=>$userId))->update($data);
        if($result){
            //记录日志
            $suUser = G('user') ;
            $action = '重置业务员密码';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $result;
    }

    /**
     * 添加业务员 / 编辑业务员
     * @param $uname
     * @param $province
     * @param $tel
     * @param $realname
     * @param $id
     * @param $face
     * @return bool
     */
    public function saveSale($uname,$province,$tel,$realname,$id='',$face=''){
        $return = false ;
        $province = $province ? $province : '全部' ;
        if($id){
            $data = array(
                'uname'      => $uname ,
                'area'       => $province ,
                'realname'   => $realname ,
                'phone'      => $tel ,
                'facepic'    => $face ,
                'update_time'=> date('Y-m-d H:i:s') ,
            );
            $result = $this->table('sales_user')->where(array('id'=>$id))->update($data);
            //writeLog($this->lastSql());
            if($result){

                if($result){
                    //记录日志
                    $suUser = G('user') ;
                    $action = '编辑业务员';
                    model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
                }
                $return = true ;
            }
        }else{
            $uId = $this->getUId() ;

            $data = array(
                'uId'        =>$uId ,
                'uname'      =>$uname ,
                'area'       =>$province ,
                'phone'      =>$tel ,
                'realname'   =>$realname ,
                'password'   =>md5(sha1('7777777').'sw') ,
                'create_time'=>date('Y-m-d H:i:s') ,
                'update_time'=>date('Y-m-d H:i:s') ,
                'status'     => 1 ,
                'facepic'    => "/images/header/user-def.jpg" ,
            );

            $result = $this->table('sales_user')->insert($data);
            if($result){

                if($result){
                    //记录日志
                    $suUser = G('user') ;
                    $action = '添加业务员';
                    model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
                }
                $return = true ;
            }
        }

        return $return;
    }

    /**
     * 新生成一个业务员ID
     * @return string
     */
    private function getUId(){
        $num  = '0123456789' ;
        $uId  = '' ;
        for ($i=0; $i<8;$i++){
            $uId .= substr($num,rand(0,9),1) ;
        }
        $user = $this->table('sales_user')->where(array('uId'=>$uId))->getOne() ;

        if($user){
            $this->getUId();
        }else{
            return $uId ;
        }
    }

    /**
     * 获取业务员基本信息
     * @param $id
     * @return mixed
     */
    public function getOneSale($id){
        $sale =  $this->table('sales_user')->field('id,uId,uname,realname,phone,area,facepic,create_time,last_time')->where(array('id'=>$id))->getOne();
        //writeLog($this->lastSql());
        return $sale ;
    }

    /**
     * 获取业务员关联圈子
     * @param $id
     * @param $page
     * @param $pageSize
     * @param $keywords
     * @return array
     */
    public function getSaleCircle($id,$page,$pageSize,$keywords){
        $pages = ($page-1)* $pageSize;

        $where = 'level=1 and type=2 and fu_id='.$id ;
        if($keywords){
            $where    .= ' and vid like "%'.$keywords.'%"' ;
        }
        $count = $this->table('circle')->where($where)->count();
        $lists = $this->table('circle')

            ->field('id,vid,content,comments,create_time,area')

            ->where($where)
            ->limit($pages,$pageSize)
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            //搜索条件
            $search  = array('keywords'=>$keywords);
            $data    = array('list'=>$lists,'search'=>$search,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的圈子记录');
        }
        return $data ;
    }

    /**
     * 获取一条圈子详情
     * @param $cid
     * @return mixed
     */
    public function getOneCircle($cid){
        $join   = ' left join firms b on a.about_id=b.EnterpriseID left join firms_card c on a.about_id=c.id' ;

        $field  = 'a.type,a.circle_type,a.content,a.imgs,' ;
        $field .= 'case a.circle_type when 2 then b.id when 3 then c.id end as exID,';
        $field .= 'case a.circle_type when 2 then b.companyname when 3 then c.firms_name end as uname,';
        $field .= 'case a.circle_type when 2 then b.face_pic when 3 then c.path end as pic';
        $res = $this->table('circle a')
            ->jion($join)
            ->field($field)
            ->where(array('a.id'=>$cid))
            ->getOne();
        return $res ;
    }

    /**
     * 获取圈子评论
     * @param $vid
     * @param $type
     * @param $keywords
     * @return mixed
     */
    public function getComments($vid,$type,$keywords,$page,$pageSize){

        $pages = ($page-1)* $pageSize;
        $where  = ' a.vid='.$vid .' and a.is_delete=0 and `level` >1';
        if($type){
            $where   .= ' and a.type='.$type ;
        }
        if($keywords){
            $where   .= ' and (b.EnterpriseID like "%'.$keywords.'%" or b.uname like "%'.$keywords.'%" or b.companyname like "%'.$keywords.'%" or c.uId like "%'.$keywords.'%" or c.uname like "%'.$keywords.'%" )' ;
        }

        $join  = ' left join firms b on a.fu_id=b.id left join sales_user c on a.fu_id=c.id' ;

        $field = 'a.id,a.parent_id,a.fu_id,a.type,a.content,a.create_time,a.parent_id,a.level,a.vid,' ;
        $field.= 'case a.type when 1 then b.uname when 2 then c.uname end  as uname,';
        $field.= 'case a.type when 1 then b.EnterpriseID when 2 then c.uId end  as exID,';
        $field.= 'case a.type when 1 then b.companyname when 2 then c.uname end  as exName';

        $count = $this->table('circle a')->jion($join)->where($where)->count();
        $lists = $this->table('circle a')
            ->field($field)
            ->jion($join)
            ->where($where)
            ->order(array('vid'=>'asc','level'=>'asc','create_time'=>'desc','id'=>'asc'))
            ->limit($pages,$pageSize)
            ->get();
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

            $data    = array('list'=>$lists,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的评论');
        }
        return $data ;
    }

    public function delComment($id,$pid){
        $result = $this->table('circle')->where('id='.$id)->update(array('is_delete'=>1));
        if($result){
            $this->query('update circle set comments=comments-1 WHERE id='.$pid);//评论数减 1
            $this->table('circle')->where('comments<0')->update(array('comments'=>0));//评论数低于0的修改为0
            //记录日志
            $suUser = G('user') ;
            $action = '删除圈子评论';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $result ;
    }

    public function getFirms($d){
        $pages = ($d['page']-1)* $d['pageSize'];

        $where = 'a.sales_user_di='.$d['uid'] ;

        if($d['status']){
            $where    .= ' and b.status='.$d['status'] ;
        }

        if($d['type']){
            $where    .= ' and b.type='.$d['type'] ;
        }
        if($d['c_lass']){
            $where    .= ' and b.classification='.$d['c_lass'] ;
        }

        if($d['province'] && $d['province'] != '全部'){//省
            $province = str_replace(' ','',$d['province']);
            $where   .= ' and b.province like "'.$province.'%"';
        }

        if($d['city'] && $d['city'] != '全部'){//市
            $city   = str_replace(' ','',$d['city']) ;
            $where .= ' and b.city like "'.$city.'%"';
        }
        if($d['county'] && $d['county'] != '全部'){//区
            $county = str_replace(' ','',$d['county']) ;
            $where .= ' and b.district like "'.$county.'%"';
        }
        if($d['keywords']){//区
            $keywords = str_replace(' ','',$d['keywords']) ;
            $where .= ' and (b.EnterpriseID like "%'.$keywords.'%" or b.companyname like "%'.$keywords.'%" or b.phone like "%'.$keywords.'%" )';
        }

        $join  = ' inner join firms b on a.firms_id=b.id' ;
        $count = $this->table('firms_sales_user a')->jion($join)->where($where)->count();
        $lists = $this->table('firms_sales_user a')

            ->field('a.create_time,b.EnterpriseID,b.uname,b.phone,b.companyname,b.type,b.classification')
            ->jion($join)
            ->where($where)
            ->limit($pages,$d['pageSize'])
            ->get();
        //writeLog($this->lastSql());
        if($lists){
            //搜索条件

            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'没有任何记录');
        }
        return $data ;
    }
}