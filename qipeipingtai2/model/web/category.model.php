<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/17
 * Time: 22:56
 */
class WebCategoryModel extends Model
{
    /**
     * 根据层级获取车系分类
     * @param $type    int   1.轿车商家 2.货车商家 3.用品商家
     * @param $level   int   层系
     * @return mixed
     */
    public function getCarCateByLevel($type,$level){
        $res = $this->table('car_group')->where(array('type'=>$type,'level'=>$level))->order('vid asc')->get();
        return $res;
    }

    /**
     * 根据父级id只获取子级id
     * @param $type
     * @param $parentId
     * @return mixed
     */
    public function getCarCateChild($type,$parentId){

        $find = array('type'=>$type);
        if($parentId){
            $find['pid'] = $parentId;
        }
        $res = $this->table('car_group')->where($find)->order('vid asc')->get();
        return $res;
    }

    //获取产品分类
    public function getProCateByLevel($level){
        $res = $this->table('product_category')->where(array('level'=>$level))->order('vid asc')->get();
        return $res;
    }
    //获取产品分类 根据父级id只获取子级id
    public function getProCateChild($parentId){
        $res = $this->table('product_category')->where(array('pid'=>$parentId))->order('vid asc')->get();
        return $res;
    }


    /**
     * @param $type 1：轿车商家  2：货车商家  3：物流
     * @param $id   2级分类ID
     */
    public function getThreeAndFourByTwo($type,$id){
        $threData = $this->table('car_group')->where('type='.$type.' and pid='.$id)->order('vid asc')->get();
        if($threData){
            $ids = [];
            foreach($threData as $v){
                array_push($ids,$v['id']);
            }
            $ids = join(',',$ids);
            $fourData = $this->table('car_group')->where('pid in ('.$ids.')')->get();
            if($fourData){
                for($i=0; $i<count($threData); ++$i){
                    $threData[$i]['child'] = [];
                    $k = 0;
                    for($j=0; $j<count($fourData); ++$j){
                        if($threData[$i]['id'] == $fourData[$j]['pid']){
                            $threData[$i]['child'][$k] = $fourData[$j];
                            $k += 1;
                        }
                    }
                }
            }
        }else{
            $threData = '';
        }
        return $threData;
    }

    public function getFourByMore($type=0,$pid=0,$level=4){
        $res['fourId'] = array();
        switch ($level){
            case 1:
                if($type){
                    $two = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as twoId')->where(array('`level`'=>2,'type'=>$type,'pid'=>$pid))->getOne();
                }else{
                    $two = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as twoId')->where(array('`level`'=>2,'pid'=>$pid))->getOne();
                }
                if($two['twoId']){
                    $three = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as threeId')->where('pid in ( '.$two['twoId'].' ) ')->getOne();
                    if($three['threeId']){
                        $res = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as fourId')->where('pid in ( '.$three['threeId'].' ) ')->getOne();
                    }
                }
                break;
            case 2:
                if($type){
                    $three = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as threeId')->where(array('`level`'=>3,'type'=>$type,'pid'=>$pid))->getOne();
                }else{
                    $three = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as threeId')->where(array('`level`'=>3,'pid'=>$pid))->getOne();
                }
                if($three['threeId']){
                    $res = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as fourId')->where('pid in ( '.$three['threeId'].' ) ')->getOne();
                }
                break;
            case 3:
                if($type){
                    $res = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as fourId')->where(array('`level`'=>4,'type'=>$type,'pid'=>$pid))->getOne();
                }else{
                    $res = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as fourId')->where(array('`level`'=>4,'pid'=>$pid))->getOne();
                }
                break;
            default:
                if($type){
                    $res = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as fourId')->where(array('`level`'=>4,'type'=>$type))->getOne();
                }else{
                    $res = $this->table('car_group')->field('GROUP_CONCAT(id SEPARATOR ",") as fourId')->where(array('`level`'=>4))->getOne();
                }

        }
        if($res['fourId']){
            $array=explode(',',$res['fourId']);
            return $array;
        }else{
            return array('0');
        }
    }

}