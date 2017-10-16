<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 16:21
 */
class PcCardController extends Controller
{
    private $user = array();
    public function __construct()
    {
        $loginMo    = model('web.login','mysql');
        $this->user = $user = $loginMo->loginIs(false);
    }

    /**
     * 创建名片
     */
    public function ceartCard(){
        $base64 = $this->getRequest('base64','');
        $data   = $this->getRequest('data','');
        $user   = $this->user;
        $result = model('web.card')->base64Save($base64);
        if($result['status'] == 1){
            $data['path'] = $result['path'];        //保存的图片路径
            $rst = model('web.card')->ceartCard($data,$user['id']);
            if($rst > 0){
                $return['status'] = 1;
            }else{
                $return['status'] = 0;
                $return['msg']    = '名片保存失败';
            }
        }else{
            $return = $result;
        }
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

}