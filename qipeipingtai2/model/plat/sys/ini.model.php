<?php

/**
 * 系统配置模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/15
 * Time: 16:53
 */
class PlatSysIniModel extends Model
{

    //==========基础配置===================
    /**
     * 获取基础配置列表
     * @return mixed
     */
    public function getIni(){
        return $this->table('base_ini')->field('id,name')->where('id<9')->order('id asc')->get();
    }
    /**
     * 获取一项配置
     * @param $id
     * @return mixed
     */
    public function getOneIni($id){
        return $this->table('base_ini')
            ->field('id,value')
            ->where(array('id'=>$id))
            ->getOne();
    }
    /**
     * 保存基本配置
     * @param $id
     * @param $name 操作内容  如配置经销商VIP等级
     * @param $val
     * @return mixed
     */
    public function saveIni($id,$name,$val){
        $time = date('Y-m-d H:i:s',time()) ;
        $res = $this->table('base_ini') ->where(array('id'=>$id))->update(array('value'=>$val,'update_time'=>$time));
        if($res){
            //记录日志...
            $suUser = G('user') ;
            $action = $name;
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }


    //==========车系配置===================
    /**
     * 获取车系配置
     * @param string $pid
     * @return mixed
     */
    public function getCarIni($pid=''){
        if(!$pid){
            return $this->table('car_group')->field('id,vid,name,type')
                ->where('level=1')
                ->order('vid asc')->get();
        }else{
            return $this->table('car_group')->field('id,vid,name,img,level')
                ->where('pid='.$pid)
                ->order('vid asc')->get();
        }
    }
    /**
     * 保存 分类
     * @param $id
     * @param $type
     * @param $level
     * @param $pid
     * @param $name
     * @param $vid
     * @param $img
     * @return mixed
     */
    public function saveCarIni($id,$type,$level,$pid,$name,$vid,$img){
        if($id){
            $res = $this->table('car_group') ->where(array('id'=>$id))->update(array('name'=>$name,'img'=>$img));
            $action = '修改车系分类名称';
        }else{
            $data = array(
                'type'=>$type, 'level'=>$level, 'img'=>$img,
                'pid'=>$pid, 'name'=>$name, 'vid'=>$vid,
            );
            $res = $this->table('car_group') ->insert($data);
            $action = '增加车系分类';
        }
        if($res){
            //记录日志...
            $suUser = G('user') ;
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }

    /**
     * 删除一系列配置
     * @param $id
     * @return mixed
     */
    public function delCarClass($id){
        $res = $this->table('car_group') ->where(array('id'=>$id))->del();
        if($res){
            $children =  $this->table('car_group')->field('id') ->where(array('pid'=>$id))->get();
            if($children){
                foreach ($children as $v){
                    $this->delClass($v['id']) ;
                }
            }

            //记录日志...
            $suUser = G('user') ;
            $action = '删除车系分类';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }
    //==========产品配置===================
    public function getProIni($pid=''){
        if(!$pid){
            return $this->table('product_category')->field('id,vid,name')
                ->where('level=1')
                ->order('vid asc')->get();
        }else{
            return $this->table('product_category')->field('id,vid,name,img,level')
                ->where('pid='.$pid)
                ->order('vid asc')->get();
        }
    }

    /**
     * 保存 分类
     * @param $id
     * @param $type
     * @param $level
     * @param $pid
     * @param $name
     * @param $vid
     * @param $img
     * @return mixed
     */
    public function saveProIni($id,$level,$pid,$name,$vid,$img){
        if($id){
            $res = $this->table('product_category') ->where(array('id'=>$id))->update(array('name'=>$name,'img'=>$img));
            $action = '修改产品分类名称';
        }else{
            $data = array(
                 'level'=>$level, 'img'=>$img,
                'pid'=>$pid, 'name'=>$name, 'vid'=>$vid,
            );
            $res = $this->table('product_category') ->insert($data);
            $action = '增加产品分类';
        }
        if($res){
            //记录日志...
            $suUser = G('user') ;
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }

    /**
     * 删除一系列配置
     * @param $id
     * @return mixed
     */
    public function delProClass($id){
        $res = $this->table('product_category') ->where(array('id'=>$id))->del();
        if($res){
            $children =  $this->table('product_category')->field('id') ->where(array('pid'=>$id))->get();
            if($children){
                foreach ($children as $v){
                    $this->delProClass($v['id']) ;
                }
            }

            //记录日志...
            $suUser = G('user') ;
            $action = '删除产品分类';
            model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
        }
        return $res ;
    }


    public function setVid($id,$vid,$type){

        if($type == 'car'){
            $_table = 'car_group' ;
        }elseif ($type == 'pro'){
            $_table = 'product_category' ;
        }else{
            $_table = '' ;
            $id     = false ;//如果不是 上述 两个类型则将id置空，直接返回false ；
        }

        if($id){

            $res = $this->table($_table) ->where(array('id'=>$id))->update(array('vid'=>$vid));

            if($res){

                //记录日志...
                $suUser = G('user') ;
                $action = $type == 'car' ? '调整汽车分类排序' : '调整产品分类排序';
                model('actionLog')->actionLog($suUser['id'],$suUser['name'],$suUser['code'],$action) ;
            }
        }else{
            $res = false ;
        }

        return $res ;
    }
}