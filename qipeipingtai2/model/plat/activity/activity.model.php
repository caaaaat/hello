<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/12
 * Time: 11:09
 */
class PlatActivityActivityModel extends Model
{
    /**
     * 获取数据统一入口
     * @param $d
     * @return array|void
     */
    public function getLists($d){
        if($d['type'] == 1){
            $res = $this->getActivity($d);
        }elseif ($d['type'] == 2){
            $res = $this->getRecommendFirm($d);
        }elseif ($d['type'] == 3){
            $res = $this->getFriendLink($d);
        }else{
            $res = array('massageCode'=>0,'massage'=>'非法操作');
        }
        return $res ;
    }

    /**
     * 促销活动
     * @param $d
     * @return array
     */
    private function getActivity($d){
        $pages  = ($d['page']-1)* $d['pageSize'];
        $where  = 'a.is_del=0' ;

        if($d['keywords']){
            $where  .= ' and (a.art_ID like "%' .$d['keywords'].'%" or a.title like "%' .$d['keywords'].'%")';
        }
        $join   = ' inner join su_user b on a.admin_id=b.id' ;

        $field  = 'a.id,a.vid,a.art_ID,a.face_img,a.title,a.create_time,';
        $field .= 'b.code';


        $count  = $this->table('article_activity a') ->jion($join)->where($where)->count();
        $lists  = $this->table('article_activity a')
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

    public function getContent($id){
        return $this->table('article_activity')
            ->field('id,title,face_img,content')
            ->where(array('id'=>$id,'is_del'=>0))->getOne() ;
    }
    /**
     * 保存促销活动
     * @param $d
     * @return mixed
     */
    public function saveActivity($d){
        $suUser = G('user') ;
        $arr = array(
            'face_img'=>$d['img'],
            'title'=>$d['title'],
            'content'=>$d['content'],
            'update_time'=>date('Y-m-d H:i:s')
        ) ;

        if($d['id']){
            $res = $this ->table('article_activity')->where(array('id'=>$d['id']))->update($arr);
            $action = '编辑促销活动' ;
        }else{
            $count  = $this->table('article_activity')->count();
            $art_ID = $this->getActivityID();

            $arr['admin_id'] = $suUser['id'] ;
            $arr['vid']      = $count + 1 ;
            $arr['art_ID']  = $art_ID ;
            $arr['create_time'] = date('Y-m-d H:i:s') ;

            $res = $this ->table('article_activity')->insert($arr);
            $action = '新增促销活动' ;

            if($res){
                //增加内容至msg表
                $data = array(
                    'msgType'=>7,
                    'aboutId'=>$art_ID,
                    'msgText'=>$d['title'],
                    'toType' =>0,
                    'toId'   =>0,
                    'createTime'=>date('Y-m-d H:i:s')
                );
                $this->table('msg')->insert($data);
            }
        }
        if($res){
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }

        return $res ;
    }
    /**
     * 推荐经销商
     * @param $d
     * @return array
     */
    private function getRecommendFirm($d){
        $pages = ($d['page']-1)* $d['pageSize'];

        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $find = 'a.is_sales=1' ;
        if($suppProv){
            $find .= ' and a.province ="'.$suppProv.'"';
        }

        //$join  = ' left join sales_user b on a.salesman_ids=b.id' ;//业务员

        $field = 'a.id,a.vid,a.EnterpriseID,a.uname,a.phone,a.companyname,a.type,a.classification,a.province,a.city,a.district';
        $count = $this->table('firms a')->where($find)->count();
        $lists = $this->table('firms a')->field($field)->where($find)
            ->order(array('a.vid'=>'asc','a.create_time'=>'desc','a.id'=>'asc'))->limit($pages,$d['pageSize'])->get();
        //writeLog($this->lastSql());
        if($lists){

            //$list = $lists['list'];
            if($lists){
                foreach ($lists as $k => $item){
                    if(!$item['province']){
                        $area = '';
                    }elseif($item['province']=='全部'){
                        $area = '全部';
                    }elseif($item['city'] == '' || $item['city'] == '全部'){
                        $area = $item['province'];
                    }elseif($item['district'] == '' || $item['district'] == '全部'){
                        $area = $item['province'].'/'.$item['city'];
                    }else{
                        $area = $item['province'].'/'.$item['city'].'/'.$item['district'];
                    }
                    $lists[$k]['area'] = $area ;
                }
                $return['list'] = $lists ;
            }
            $data    = array('list'=>$lists,'count'=>$count,'page'=>$d['page'],'pageSize'=>$d['pageSize'],'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的经销商');
        }
        return $data;
    }

    /**
     * 获取未被推荐的经销商
     * @param $d
     * @return array
     */
    public function getChoiceFirm($d){
        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        $find = 'is_sales=0 and type=1' ;
        if($d['keywords']){
            $findKey = '"%'.$d['keywords'].'%"';
            $find .= " and (`EnterpriseID` like $findKey or `companyname` like $findKey or `phone` like $findKey)";
        }

        if($suppProv){
            $find .= ' and a.province ="'.$suppProv.'"';
        }


        //$join  = ' left join sales_user b on a.salesman_ids=b.id' ; //业务员
        $field = 'a.id,a.vid,a.EnterpriseID,a.uname,a.phone,a.companyname,a.type,a.classification,a.province,a.city,a.district';
        $lists = $this->table('firms a')->field($field)->where($find)
            ->order(array('a.vid'=>'asc','a.create_time'=>'desc','a.id'=>'asc'))->get();
        if($lists){
            if($lists){
                foreach ($lists as $k => $item){
                    if(!$item['province']){
                        $area = '';
                    }elseif($item['province']=='全部'){
                        $area = '全部';
                    }elseif($item['city'] == '' || $item['city'] == '全部'){
                        $area = $item['province'];
                    }elseif($item['district'] == '' || $item['district'] == '全部'){
                        $area = $item['province'].'/'.$item['city'];
                    }else{
                        $area = $item['province'].'/'.$item['city'].'/'.$item['district'];
                    }
                    $lists[$k]['area'] = $area ;
                }
                $return['list'] = $lists ;
            }
            $data    = array('list'=>$lists,'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的经销商');
        }
        return $data;

    }

    /**
     * 保存推荐经销商
     * @param $d
     * @return bool
     */
    public function saveRecommendFirm($d){
        if($d){
            $ids = implode(',',$d);
            $res = $this->table('firms')->where('id in ('.$ids.')')->update(array('is_sales'=>1));
        }else{
            $res = false ;
        }

        if($res){
            $suUser = G('user') ;
            $action = '新增PC推荐经销商' ;
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }
    /**
     * 友情链接
     * @param $d
     * @return array
     */
    private function getFriendLink($d){
        $where  = 'a.is_del=0' ;
        $pages  = ($d['page']-1)* $d['pageSize'];
        $field  = 'a.id,a.vid,a.vname,a.vurl,a.create_time,';
        $field .= 'b.code';

        $join   = ' inner join su_user b on a.admin_id=b.id' ;
        $count  = $this->table('friendly_link a') ->jion($join)->where($where)->count();
        $lists  = $this->table('friendly_link a')
            ->field($field)
            ->jion($join)
            ->where($where)
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
     * 保存友情链接
     * @param $d
     * @return mixed
     */
    public function saveLink($d){
        $suUser = G('user') ;
        $arr = array(
            'vname'=>$d['name'],
            'vurl'=>$d['url'],
            'update_time'=>date('Y-m-d H:i:s')
        ) ;

        if($d['id']){
            $res = $this ->table('friendly_link')->where(array('id'=>$d['id']))->update($arr);
            $action = '编辑友情链接' ;
        }else{
            $count  = $this->table('friendly_link')->count();
            $arr['admin_id'] = $suUser['id'] ;
            $arr['vid']      = $count + 1 ;

            $arr['create_time'] = date('Y-m-d H:i:s') ;
            $res = $this ->table('friendly_link')->insert($arr);
            $action = '新增友情链接' ;
        }
        if($res){

            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
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
        if($type && $id && $vid){
            $_table = $type == 1 ? 'article_activity' : ($type == 2 ? 'firms' : 'friendly_link');
            $res = $this->table($_table)->where(array('id'=>$id))->update(array('vid'=>$vid,'update_time'=>date('Y-m-d H:i:s',time()))) ;
            if($res){
                //记录日志
                $suUser = G('user') ;
                $action = $d['type'] == 1 ? '调整促销活动顺序' : ( $d['type'] == 2 ? '调整推荐经销商顺序' : '调整友情链接顺序' );
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $res = false ;
        }
        return $res ;
    }

    /**
     * 删除促销活动/取消推荐经销商/删除友情链接
     * @param $d
     * @return bool
     */
    public function delContent($d){
        $type= isset($d['type'])  ? $d['type']  : '' ;
        $id  = isset($d['id'])  ? $d['id']  : '' ;
        if( $type && $id ){
            $_table = $type == 1 ? 'article_activity' : ($type == 2 ? 'firms' : 'friendly_link');
            if($type == 1 || $type == 3){
                $res = $this->table($_table)->where(array('id'=>$id))->update(array('is_del'=>1,'update_time'=>date('Y-m-d H:i:s',time()))) ;
            }elseif ($type == 2){
                $res = $this->table($_table)->where(array('id'=>$id))->update(array('is_sales'=>0,'update_time'=>date('Y-m-d H:i:s',time()))) ;
            }else{
                $res = false ;
            }
            if($res){
                //记录日志
                $suUser = G('user') ;
                $action = $d['type'] == 1 ? '删除促销活动' : ( $d['type'] == 2 ? '取消推荐经销商' : '删除友情链接' );
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $res = false ;
        }
        return $res ;
    }


    private function getActivityID(){
        $time = time();
        $fix  = substr($time,2,8) ;
        $rand = rand(100,999);
        $code = $fix . $rand ;
        $res = $this ->table('article_activity')->where(array('art_ID'=>$code))->getOne();
        if($res){
            $this->getActivityID();
        }else{
            return $code ;
        }
    }

}