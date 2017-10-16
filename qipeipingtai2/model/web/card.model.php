<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 16:26
 */
class WebCardModel extends Model
{
    /**
     * @param $data         数据
     * @param $companyId    企业ID
     * 添加企业名片数据
     */
    public function ceartCard($data,$companyId){
        $firms_type = $this->table('firms')->field('type')->where('id='.$companyId)->getOne();
        $result = 0;
        if($firms_type){
            $data['firms_type']  = $firms_type['type'];
            $data['firms_id']    = $companyId;
            $card = $this->table('firms_card')->where('firms_id='.$companyId)->getOne();
            if($card){
                $rst = $this->table('firms_card')->where('firms_id='.$companyId)->update($data);
                if($rst > 0){
                    $result = 1;
                }
            }else{
                $data['create_time'] = date("Y-m-d H:i:s");
                $rst = $this->table('firms_card')->insert($data);
                if($rst > 0){
                    $result = 1;
                }
            }

        }
        return $result;
    }

    /**
     * @param $companyId    企业id
     */
    public function getCardInfo($companyId){
        $data = $this->table('firms_card')->where('firms_id='.$companyId)->group('create_time desc')->getOne();
        return $data;
    }

    /**
     * @param $base64   base64字符串
     */
    public function base64Save($base64){
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
            $type = $result[2];
            $size = strlen(file_get_contents($base64));     //获取base64图片大小
            if($size > 5242880){           //图片大于5M
                $return['status'] = 0;
                $return['msg']    = '名片大小超过5M，上传失败';
            }else{
                $new_file = APPROOT."/data/card/".date('Ymd',time())."/";
                if(!file_exists($new_file))
                {
//检查是否有该文件夹，如果没有就创建，并给予最高权限
                    mkdir($new_file, 0700);
                }
                $new_file = $new_file.time().".{$type}";
                $path     = "/data/card/".date('Ymd',time())."/".time().'.'.$result[2];
                if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64)))){
                    $return['status'] = 1;
                    $return['path']   = $path;
                }else{
                    $return['status'] = 0;
                    $return['msg']    = '名片保存失败';
                }
            }
        }else{
            $return['status'] = 0;
            $return['msg']    = 'base64格式错误';
        }
        return $return;
    }

    /**
     * @param $companyId    商家id
     */
    public function getFirms($companyId){
        $rst = $this->table('firms_card')->where('firms_id='.$companyId)->getOne();
        return $rst;
    }

    /**
     * @param $erId  选择的二级分类id(',1,5,'),最多三个
     * 返回二级分类的图标
     */
    public function getErJiTuPian($erId){
        $erId = substr($erId,1,-1);
        $result = $this->table('car_group')->where('id in ('.$erId.')')->get();
        return $result;
    }
}