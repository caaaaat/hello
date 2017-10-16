<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/21
 * Time: 下午 03:49
 */
class ToolsSalesModel extends Model
{

    public function sales2firm(){
        //将 firms_sales_user 未过期的业务员写入 firms 表
        $this->table('firms')->update(array('salesman_ids'=>'')) ; //先全部置空
        $sales = $this->getSales() ;
        if($sales){
            foreach ($sales as $sale){
                $this->table('firms')
                    ->where('id='.$sale['firms_id'])
                    ->update(array('salesman_ids'=>$sale['sales'])) ;//当前 规则为 一家厂商绑定一个业务员
            }
        }
    }

    private function getSales(){
        $time  = date('Y-m-d',time()) ;
        $field = 'firms_id,GROUP_CONCAT(sales_user_di SEPARATOR ",") as sales' ;
        $where = 'DATE_FORMAT(end_time ,"%Y-%m-%d") > "'.$time.'"' ;
        $sales = $this->table('firms_sales_user')->field($field)->where($where)->group('firms_id')->get() ;
        return $sales ;
    }

}