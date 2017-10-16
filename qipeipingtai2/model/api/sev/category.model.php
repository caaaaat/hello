<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/29
 * Time: 23:57
 */
class ApiSevCategoryModel extends Model
{
    /**
     * 根据层级获取车系分类
     * @param $type    int   1.轿车商家 2.货车商家 3.物流货运
     * @param $level   int   层系
     * @return mixed
     */
    public function getCarCateByLevel($type,$level){
        $res = $this->table('car_group a')->field('a.*,b.name as pName')->where(array('a.type'=>$type,'a.level'=>$level))->jion('left join car_group b on b.id=a.pid')->order('vid asc')->get();
        return $res;
    }

    /**
     * 根据父级id只获取子级id
     * @param $type
     * @param $parentId
     * @return mixed
     */
    public function getCarCateChild($type,$parentId){
        $res = $this->table('car_group')->where(array('type'=>$type,'pid'=>$parentId))->order('vid asc')->get();
        return $res;
    }

    //获取产品分类
    public function getProCate(){

    }

    /**
     * @param $type 1：轿车商家  2：货车商家  3：物流
     * @param $id   2级分类ID
     */
    public function getThreeAndFourByTwo($type,$id){
        $threData = $this->table('car_group')->where('type='.$type.' and pid='.$id)->get();
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

}