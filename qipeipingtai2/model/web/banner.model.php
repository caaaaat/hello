<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/14
 * Time: 21:20
 */
class WebBannerModel extends Model
{
    protected $tableName = 'banner';
    //获取顶部banner
    public function getTopBanner(){
        $res = $this->table($this->tableName)->where(array('type'=>1))->order('vid asc,create_time desc')->get();
        return $res;
    }
    //腰部顶部banner
    public function getYaoBanner(){
        $res = $this->table($this->tableName)->where(array('type'=>2))->order('vid asc')->get();
        return $res;
    }
    //获取某厂商的banner
    public function getFirmBanner($firmId){

    }

}