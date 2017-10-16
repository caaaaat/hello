<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/30
 * Time: 23:11
 */
class ApiSevUploadModel extends Model{

 public $cTempFile = true;

 public function uploadImg($file){

        header('Content-type: text/html; charset=UTF-8');
        //文件保存目录路径
        $savePath = APPROOT . '/data/upload/';
        if (!file_exists($savePath)) mkdir($savePath);
        $savePath = $savePath . date("Ymd") . '/';
        if (!file_exists($savePath)) mkdir($savePath);

        //文件访问URL目录
        $saveUrl = str_replace(APPROOT, '', $savePath);

        //定义允许上传的文件扩展名
        $extArr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png'),
            'flash' => array('swf'),
            'media' => array('swf'),
            'file' => array('txt', 'zip', 'rar', 'gz', 'bz2'),
        );

        //最大文件大小
        $maxSize = 100000000;

        $savePath = realpath($savePath) . '/';

        //PHP上传失败返回的信息
        if (!empty($file['error'])) {
            switch ($file['error']) {
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            return array('status' => 201, 'msg' => $error);
        }
        //有上传文件时
        if (empty($file) === false) {
            //原文件名
            $fileName = $file['name'];
            //服务器上临时文件名
            $tmpName = $file['tmp_name'];
            //文件大小
            $fileSize = $file['size'];

            $s = getimagesize($tmpName);
            $width = $s[0];
            $height = $s[1];            //检查文件名

            if (!$fileName) {
                $error = ("请选择文件。");
                return array('status' => 201,'msg' => $error);
            }
            //检查目录
            if (@is_dir($savePath) === false) {
                $error = ("上传目录不存在。");
                return array('status' => 201, 'msg' => $error);
            }
            //检查目录写权限
            if (@is_writable($savePath) === false) {
                $error = ("上传目录没有写权限。");
                return array('status' => 201, 'msg' => $error);
            }
            //检查是否已上传
            if (@is_uploaded_file($tmpName) === false) {
                $error = ("上传失败。");
                return array('status' => 201, 'msg' => $error);
            }
            //检查文件大小
            if ($fileSize > $maxSize) {
                $error = ("上传文件大小超过限制。");
                return array('status' => 201, 'msg' => $error);
            }
            //检查文件类型
            $dirName = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
            if (empty($extArr[$dirName])) {
                $error = ("目录名不正确。");
                return array('status' => 201, 'msg' => $error);
            }
            /*dump($extArr);
            dump($_REQUEST);
            exit;*/
            //获得文件扩展名
            $temp_arr = explode(".", $fileName);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            //检查扩展名
            if (in_array($file_ext, $extArr[$dirName]) === false) {
                $error = ("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $extArr[$dirName]) . "格式。");
                return array('status' => 201,'msg' => $error);
            }
            //新文件名
            $newFileName = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
            //移动文件
            $filePath = $savePath . $newFileName;
            if (move_uploaded_file($tmpName, $filePath) === false) {
                $error = ("上传文件失败。");
                return array('status' => 201, 'msg' => $error);
            }
            @chmod($filePath, 0644);
            $fileUrl = $saveUrl . $newFileName;

            //生成缩微图
            if($this->cTempFile&&$fileSize>500*1024)
            {
                if($width>600||$height>600){

                    if($width>600){
                        $newWidth = 600;
                        $newHeight = intval($height*600/$width);
                    }else{
                        $newHeight = 600;
                        $newWidth = intval($width*600/$height);
                    }

                    $this->createTempFile($savePath . "/" . $newFileName,$newWidth,$newHeight);
                }
            }

            return array('status' => 200, 'url' => $fileUrl,'msg'=>'上传成功');

        }else{
            return array('status' => 201, 'msg' => '上传文件失败');
        }

    }

 /**
     * 生成缩微图
     * @param $file
     * @param string $width     280
     * @param string $height    175
     * @param string $type    1 不保留原图
     */
    public function createTempFile($file,$width='200',$height='175',$type='1')
    {
        $fileInfos  = explode('.',$file);
        $len        = count($fileInfos)-1;
        $fileExt    = $fileInfos[$len];
        if($fileExt=='jpg' || $fileExt=='jpeg' || $fileExt=='png' || $fileExt=='gif'  || $fileExt=='JPG' || $fileExt=='JPEG' || $fileExt=='PNG' || $fileExt=='GIF')
        {
            $thumbFile  = $type=='1'?$file:str_replace($fileExt,$width.'.'.$fileExt,$file); //缩略图
            if(file_exists($file))
            {
                $resize = import('img', 'lib', true);
                $resize->resizeImg($file, $width, $height ,'1' ,$thumbFile);
            }
        }
    }

}