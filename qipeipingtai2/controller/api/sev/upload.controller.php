<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/30
 * Time: 22:44
 */

class ApiSevUploadController extends Controller{

    /**
     * H5+上传图片
     */
    public function uploadImg(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            foreach ( $_FILES as $name=>$file ) {
                $upMo = model('api.sev.upload');
                $return = $upMo->uploadImg($file);
            }

        }else{
            $return['status'] = 101;
            $return['msg']    = '不支持该上传方式';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
    * 上传图片 无登录
    */
    public function uploadImgNoLogin()
    {

        $type   = $this->getRequest('type','');
        $mo     = model('tools.upload');
        $rst    = $mo->upload('file');

        $result = array('statusCode'=>300,'message'=>'');
        if($rst['status']){
            $result['statusCode'] = 200;
            $result['message']    = '上传成功';
            $result['filename']    = $rst['url'];
        }else{
            $result['message']    = $rst['info'];
        }
        exit(json_encode($result));
    }



}