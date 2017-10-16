<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 17:13
 */
class WebMsgModel extends Model
{
    /**
     *  $msgType 消息类型：
     *     1普通公告,
     *     2商家入驻,aboutI字段为厂商ID
     *     3求购消息,aboutI字段为求购id
     *     4圈子被评论,aboutI字段为圈子id
     *     5圈子被回复,aboutI字段为圈子id
     *     6店铺被访问,aboutI字段为厂商ID
     *     7活动促销,aboutI字段为活动促销ID
     *     8新闻资讯,aboutI字段为新闻资讯ID
     *     9新手上路,aboutI字段为新手上路ID
     *  $aboutId
     *  $content 消息内容
     *  $toType  接收消息对象类型 0全部 1轿车商家 2货车商家 3用品商家 4修理厂 5快修保养  6美容店  7 全部经销商 8 全部汽修厂 9 全部厂商 10业务员
     *  $toId    接收消息对象id   $toType=(0 1 2 3 4 5 6 7 8) $toId=0
     *                          $toType=9  $toId=厂商id或0
     *                          $toType=9  $toId=业务员id或0
     */
    public function toSaveMsg($msgType,$aboutId,$content,$toType,$toId,$area=0){
        $data = array(
            'msgType'=>$msgType,
            'aboutId'=>$aboutId,
            'msgText'=>$content,
            'toType' =>$toType,
            'toId'   =>$toId,
            'createTime'=>date('Y-m-d H:i:s'),
            'city'   =>$area,
        );
        $res = $this->table('msg')->insert($data);
        return $res;
    }

    /**
     * 获取未读消息条数
     *  $toType         类型   1厂商  2业务员
     *  $toId    厂商业务员id   厂商id或业务员id
     */
    public function getUnReadMsgNum($type,$toId){
//SELECT count(*) From msg WHERE (toType=0 AND toId=0) or (toType=1 AND toId=9);
//SELECT count(*) FROM msg as a LEFT JOIN msg_read as c on c.msg_id=a.id WHERE c.fu_id = 9 AND c.type = 1
        if($type==1){
            $firm  = $this->table('firms')->where(array('id'=>$toId))->getOne();
            $where = '( ( toType=0 AND toId=0 ) or ( toType='.$firm['classification'].' AND toId=0 ) or ( toType=9 AND toId='.$toId.' ) ) and msgType < 7';
        }else{
            $where = '( toType=0 AND toId=0 ) or ( toType=10 AND toId='.$toId.' )';
        }

        $res = $this->table('msg')->where($where)->count();
        $rst = $this->table('msg as a')->jion('LEFT JOIN msg_read as c on c.msg_id=a.id')->where('c.fu_id='.$toId.' and c.type='.$type.' and msgType < 7')->count();
        return $res-$rst;
    }

    /**
     * 获取消息
     *  $toType         类型   1厂商  2业务员
     *  $toId    厂商业务员id   厂商id或业务员id
     */
    public function getMsg($type,$toId,$page,$pageSize=10,$msgType=0,$area=0){
        $start = ($page-1)*$pageSize;
        if($type==1){
            $firm  = $this->table('firms')->where(array('id'=>$toId))->getOne();
            $where = '( ( toType=0 AND toId=0 ) or ( toType='.$firm['classification'].' AND toId=0 ) or ( toType=9 AND toId='.$toId.' ) ) and msgType < 7 ';
        }else{
            $where = '( toType=0 AND toId=0 ) or ( toType=10 AND toId='.$toId.' )';
        }

        if($area){
            $where .= ' and ( city = 0 or city like "%'.$area.'%" )';
        }

        if($msgType){
            $where .= ' and msgType in ('.$msgType.') ';
        }

        $count = $this->table('msg')->where($where)->count();
        $data  = $this->table('msg')->where($where)->order('createTime desc')->limit($start,$pageSize)->get();
        foreach($data as $k=>$v){
            $rst = $this->table('msg_read')->where(array('fu_id'=>$toId,'type'=>$type,'msg_id'=>$v['id']))->getOne();
            if($rst){
                $data[$k]['is_read'] = 1;
            }else{
                $data[$k]['is_read'] = 0;
                //将消息设置为已读
                $this->table('msg_read')->insert(array('fu_id'=>$toId,'type'=>$type,'msg_id'=>$v['id'],'create_time'=>date("Y-m-d H:i:s")));
            }
            $data[$k]['info'] = array();
            switch ($v['msgType']){
                case 1:break;
                case 2:break;
                case 3:
                    $info = $this->table('want_buy as a')->field('a.limitation,a.create_time,CONCAT(d.name,"",f.name) as car12Str,CONCAT(b.name,"",c.name) as car34Str')
                        ->jion('left join car_group as b on a.car_group_id=b.id left join car_group as c on b.pid=c.id left join car_group as d on c.pid=d.id left join car_group as f on d.pid=f.id')
                        ->where(array('a.id'=>$v['aboutId']))->getOne();
                    $info['paiS'] = $this->table('want_buy_list')->where(array('want_buy_id'=>$v['aboutId']))->count();
                    $data[$k]['info']=$info;
                    break;
                case 4:
                    $info = $this->table('circle as a')
                        ->field('a.content,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname,b.EnterpriseID,c.uid')
                        ->jion('LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id')
                        ->where(array('a.id'=>$v['aboutId']))->getOne();
                    $data[$k]['info'] = $info;
                    break;
                case 5:
                    $info = $this->table('circle as a')
                        ->field('a.content,(case a.type when 1 THEN b.uname ELSE c.uname END) as uname,b.EnterpriseID,c.uid')
                        ->jion('LEFT JOIN firms as b on b.id=a.fu_id LEFT JOIN sales_user as c on c.id=a.fu_id')
                        ->where(array('a.id'=>$v['aboutId']))->getOne();
                    $data[$k]['info'] = $info;
                    break;
                case 6:
                    $info = $this->table('firms')->field('type,companyname,major')->where(array('EnterpriseID'=>$v['aboutId']))->getOne();
                    $data[$k]['info']=$info;
                    break;
                default:
            }

        }
        return array('list'=>$data,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize);
    }

    /**
     * 获取通知公告
     *  $toType         类型   1厂商  2业务员
     *  $toId    厂商业务员id   厂商id或业务员id
     */
    public function getNotice($isQx,$page,$pageSize,$area=0){
        $start = ($page-1)*$pageSize;

        if($isQx){
            $where = 'msgType in (2,3)';
        }else{//不能查看汽修厂入驻
            $where = 'msgType in (2,3) and msgText not like "%汽修厂%"';
        }

        if($area){
            $where .= ' and ( city = 0 or city like "%'.$area.'%" )';
        }

        $count = $this->table('msg')->where($where)->count();
        $data  = $this->table('msg')->where($where)->order('createTime desc')->limit($start,$pageSize)->get();
        foreach($data as $k=>$v){

            $data[$k]['info'] = array();
            switch ($v['msgType']){
                case 1:break;
                case 2:break;
                case 3:
                    $info = $this->table('want_buy as a')->field('a.limitation,a.create_time,CONCAT(d.name,"",f.name) as car12Str,CONCAT(b.name,"",c.name) as car34Str')
                        ->jion('left join car_group as b on a.car_group_id=b.id left join car_group as c on b.pid=c.id left join car_group as d on c.pid=d.id left join car_group as f on d.pid=f.id')
                        ->where(array('a.id'=>$v['aboutId']))->getOne();
                    $info['paiS'] = $this->table('want_buy_list')->where(array('want_buy_id'=>$v['aboutId']))->count();
                    $data[$k]['info']=$info;
                    break;
                default:
            }
        }
        return array('list'=>$data,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize,'status'=>200);
    }


    /**
     * @param $msgId
     * @param $type
     * @param $toId
     * @return bool
     */
    public function readMsg($msgId,$type,$toId){
        $res = $this->table('msg_read')->where(array('msg_id'=>$msgId,'type'=>$type,'fu_id'=>$toId))->getOne();
        if($res){
            return true;
        }else{
            $rst = $this->table('msg_read')->insert(array('msg_id'=>$msgId,'type'=>$type,'fu_id'=>$toId,'create_time'=>date('Y-m-d H:i:s')));
            if($rst){
                return true;
            }else{
                return true;
            }
        }
    }


}