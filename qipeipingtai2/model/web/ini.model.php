<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/16
 * Time: 21:03
 */
class WebIniModel extends Model
{
    //获取友情链接
    public function getLinks(){
        $res = $this->table('friendly_link')->field('vname,vurl')->order('vid asc ,create_time desc')->get();

        if($res){
            foreach ($res as $k => $v){
                if(!preg_match('/(http:\/\/)|(https:\/\/)/i', $v['vurl'])){
                    $res[$k]['vurl'] = 'http://' . $v['vurl'] ;
                }
            }
        }
        return $res;
    }
    //获取公司简介
    public function getInfo(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>9))->getOne();
        return $res;
    }
    //qq 电话
    public function getQQ(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>4))->getOne();
        $ret = array('QQ'=>'','Tel'=>'');
        if($res){
            if($res['value']){
                $arr = json_decode($res['value'],true);
                $ret['QQ']  = empty($arr['qq'])?'':$arr['qq'];
                $ret['Tel'] = empty($arr['tel'])?'':$arr['tel'];
            }
        }
        return $ret;
    }
    //分享送刷新点配置
    public function getInviteInfo(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>6))->getOne();
        if($res){
            $arr = json_decode($res['value'],true);
        }else{
            $arr = array('invitation'=>0,'invited'=>0);
        }
        return $arr;
    }
    //读取城市配置
    public function cityIni(){
        $res = $this->table('base_ini')->field('value')->where(array('id'=>3))->getOne();
        if($res){
            $res = json_decode($res['value'],true);

        }
        return $res;
    }

}