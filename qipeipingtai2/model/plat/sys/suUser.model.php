<?php

/**
 * 管理员模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10
 * Time: 11:34
 */
class PlatSysSuUserModel extends Model
{
    /**
     * 获取列表
     * @param $page
     * @param $pageSize
     * @param $status
     * @param $province
     * @param $keywords
     * @return array
     */
    public function getSuUser($page,$pageSize,$status,$province,$keywords){
        $supper   = G('user') ;
        $suppProv = @$supper['province'] ;

        //初始化总条数
        $count = 0;
        //起始条数
        $pages = ($page-1)* $pageSize;

        $find = 'id<>0';

        if($suppProv){
            $find .= ' and province ="'.$suppProv.'"';
        }

        if($status){//状态
            $find  .= ' and status  ='.$status;
        }
        if($province){//状态
            $find  .= ' and province="'.$province.'"';
        }
        if($keywords){//关键字
            $findKey = '"%'.$keywords.'%"';
            $find   .= " and (`code` like $findKey or `name` like $findKey )";
        }

        $field = 'id,code,name,status,province';

        $count       = $this->table('su_user')->where($find)->count();
        $lists = $this->table('su_user')->field($field)->where($find)->order(array('id'=>'asc'))->limit($pages,$pageSize)->get();

        if($lists){
            foreach ($lists as $k => $v){
                if(!$v['province']){$lists[$k]['province'] = '全部' ;}
            }

            $data    = array('list'=>$lists,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'massageCode'=>'success');
        }else{
            $data    = array('massageCode'=>0,'massage'=>'暂时没有符合条件的管理员');
        }

        return $data;
    }

    /**
     * 启用/停用管理员
     * @param $userId
     * @param $status
     * @return int
     */
    public function changeStatus($userId,$status){
        $data = array();
        $data['status'] = $status;
        $result = $this->table('su_user')->where(array('id'=>$userId))->update($data);
        if($result){
            //记录日志
            $suUser = G('user') ;
            $action = $status == 1 ? '启用管理员' : '停用管理员';
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
        $data['pwd'] = md5(sha1('123456').'sw');
        $result = $this->table('su_user')->where(array('id'=>$userId))->update($data);
        if($result){
            //记录日志
            $suUser = G('user') ;
            $action = '重置管理员密码';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $result;
    }


    public function getSupperInfo($userId){

        return $this->table('su_user')->field('id,name,province')->where(array('id'=>$userId))->getOne();

    }

    /**
     * 添加子管理员
     * @param $d
     * @return int
     */
    public function saveSu($d){
        $id     = isset($d['id'])     ? $d['id']     : '' ;
        $suCode = isset($d['suCode']) ? $d['suCode'] : '' ;
        $suName = isset($d['suName']) ? $d['suName'] : '' ;
        $prov   = isset($d['prov'])   ? $d['prov']   : '' ;

        if($id){
            if($suName){
                $data = array(
                    'name'       =>$suName ,
                    'province'   =>$prov   ,
                    'update_time'=>date('Y-m-d H:i:s') ,
                );
                $return = $this->table('su_user')->where('id='.$id)->update($data);
                if($return){
                    //记录日志
                    $suUser = G('user') ;
                    $action = '编辑管理员';
                    model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
                }
            }else{
                $return = false ;
            }
        }else{
            if($suCode && $suName){
                $data = array(
                    'code'       =>$suCode ,
                    'name'       =>$suName ,
                    'province'   =>$prov   ,
                    'pwd'        =>md5(sha1('123456').'sw') ,
                    'create_time'=>date('Y-m-d H:i:s') ,
                    'update_time'=>date('Y-m-d H:i:s') ,
                );
                $return = $this->table('su_user')->insert($data);
                if($return){
                    //获取所有权限id
                    $authId = $this->table('core_auth')->field('id')->where('isMenu=1 and id>1')->get();
                    $sql    = 'insert into core_user_auth (`userId`,`authId`) VALUES ';
                    $val    = '' ;
                    $ext    = '' ;
                    foreach ($authId as $v){
                        $val .= $ext.'('.$return.','.$v['id'].')';
                        $ext  = ',' ;
                    }
                    $sql     .= $val ;
                    $this->query($sql); //添加权限
                    if($return){
                        //记录日志
                        $suUser = G('user') ;
                        $action = '添加管理员';
                        model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
                    }

                }

            }else{
                $return = false ;
            }
        }


        return $return;
    }

    /**
     * 编辑权限
     * @param $id
     * @param $auth
     * @return mixed
     */
    public function saveAuth($id,$auth,$prov){
        $return = false ;
        $this->table('core_user_auth')->where(array('userId'=>$id))->del();
        $sql    = 'insert into core_user_auth (`userId`,`authId`) VALUES ';
        $val    = '' ;
        $ext    = '' ;
        foreach ($auth as $v){
            $val .= $ext.'('.$id.','.$v.')';
            $ext  = ',' ;
        }
        $sql     .= $val ;
        //dump($sql);
        $result = $this->query($sql); //添加权限

        //$this->table('su_user')->where('id='.$id)->update(array('province'=>$prov));

        if($result){
            //记录日志
            $suUser = G('user') ;
            $action = '编辑管理员权限';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;

            $return = true ;
        }
        return $return;
    }

}