<?php

/**
 * 基础信息 配置控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/15
 * Time: 16:49
 */
class PlatIniController extends Controller
{
    //=========配置主页====================
    public function lists(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.ini';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $iniMo = model('plat.sys.ini','mysql');
            $ini   = $iniMo->getIni();
            $carIni= $iniMo->getCarIni();
            $proIni= $iniMo->getProIni();
            $type1 = array();
            $type2 = array();
            $type3 = array();
            //dump($carIni);
            if($carIni){
                foreach ($carIni as $v){
                    if($v['type'] == 1){
                        $type1[] = $v ;
                    }elseif ($v['type'] == 2){
                        $type2[] = $v ;
                    }else{
                        $type3[] = $v ;
                    }
                }
            }
            $types['type1'] = $type1 ;
            $types['type2'] = $type2 ;
            $types['type3'] = $type3 ;
            //dump($proIni);
            $this->assign('ini',$ini);
            $this->assign('carIni',$types);
            $this->assign('proIni',$proIni);
            //dump($carIni);
            $this->template('plat.sys.ini.list');
        }else{
            dump('没有相关权限');

        }
    }
    //==========基础配置===================
    /**
     * 基础配置
     */
    public function ini(){
        $id = $this->getRequest('id','');
        //获取 配置 值
        $iniMo = model('plat.sys.ini','mysql');
        $ini   = $iniMo->getOneIni($id);
        if($id == 1){
            $ini   = json_decode($ini['value']) ;
            $ini   = object2array($ini);
        }elseif ($id == 2){
            $ini   = json_decode($ini['value']) ;
            $ini   = object2array($ini);
        }elseif ($id == 3){

        }elseif ($id == 4){
            $ini   = json_decode($ini['value']) ;
            $ini   = (array)$ini ;
        }elseif ($id == 5){

        }elseif ($id == 6){
            $ini   = json_decode($ini['value']) ;
            $ini   = (array)$ini ;
        }elseif ($id == 7){
            $ini   = json_decode($ini['value']) ;
            $ini   = object2array($ini) ;
        }elseif ($id == 8){
            $ini   = json_decode($ini['value']) ;
            $ini   = object2array($ini) ;
        }else{

        }
        //dump($ini);
        $this->assign('id',$id);
        $this->assign('ini',$ini);
        $this->template('plat.sys.ini.ini');
    }

    /**
     * 获取jstree 所需城市数据
     */
    public function getArea(){
        $area  = include_once(APPROOT.'/data/jstree_city.php');
        $iniMo = model('plat.sys.ini','mysql');
        $ini   = $iniMo->getOneIni(3);
        if($ini){
            $value = $ini['value'] ;
            if($value){
                $value = json_decode($value);
                foreach ($area as $k=>$v){
                    foreach ($v['children'] as $ck => $cv){
                        if(in_array($cv['text'],$value)){
                            $v['children'][$ck]['state'] = array("selected"=>"true") ;
                        }
                        $area[$k] = $v ;
                    }
                }
            }
        }
        exit(json_encode($area,JSON_UNESCAPED_UNICODE));
    }




    /**
     * 保存基础配置
     */
    public function saveIni(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();
        //writeLog(strftime("%Y-%m-%d %H:%M:%S"));
        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.ini';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $iniMo = model('plat.sys.ini','mysql');
            $id    = $this->getRequest('id'  ,'');
            $name  = $this->getRequest('name','');
            $data  = $this->getRequest('data','');
            if($id != 5)
                $val   = json_encode($data,JSON_UNESCAPED_UNICODE) ;
            else
                $val   = ($data) ;
            // writeLog($val);die;
            $res   = $iniMo->saveIni($id,$name,$val);
            if($res){
                $return['massageCode'] = 'success' ;
                $return['massage']     = '配置成功' ;
            }else{
                $return['massage']     = '配置失败' ;
            }
        }else{
            $return['massage']         = '没有相关权限' ;
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }


    //==========车系配置===================
    public function getCarIni(){
        $id    = $this->getRequest('id'  ,'');
        $iniMo = model('plat.sys.ini','mysql');
        $carIni= $iniMo->getCarIni($id) ;
        exit(json_encode($carIni,JSON_UNESCAPED_UNICODE)) ;
    }

    /**
     * 保存 新增、编辑分类
     */
    public function saveCarIni(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.ini';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $iniMo = model('plat.sys.ini','mysql');
            $id    = $this->getRequest('id'  ,'');
            $type  = $this->getRequest('type','');
            $level = $this->getRequest('lv','');
            $pid   = $this->getRequest('pid','');
            $name  = $this->getRequest('name','');
            $vid   = $this->getRequest('vid','');
            $img   = $this->getRequest('img','');
            //writeLog($_REQUEST);

            $res   = $iniMo->saveCarIni($id,$type,$level,$pid,$name,$vid,$img);
//            $res = 3;
            if($res){
                $return['massageId']   = $res ;
                $return['massageCode'] = 'success' ;
                $return['massage']     = $id ? '修改成功' : '增加成功' ;
            }else{
                $return['massage']     = $id ? '修改失败' : '增加失败' ;
            }
        }else{
            $return['massage']         = '没有相关权限' ;
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 删除分类
     */
    public function delCarClass(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();
        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.ini';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $iniMo = model('plat.sys.ini','mysql');
            $id    = $this->getRequest('id'  ,'');
            $res   = $iniMo->delCarClass($id);
//            $res = 9;
            if($res){
                $return['massageCode'] = 'success' ;
                $return['massage']     = '删除成功' ;
            }else{
                $return['massage']     = '删除失败' ;
            }
        }else{
            $return['massage']         = '没有相关权限' ;
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 编辑二级分类需调用其他页面
     */
    public function editCarClass(){
        $id     = $this->getRequest('id'  ,'');
        $type   = $this->getRequest('type'  ,'');
        $lv     = $this->getRequest('lv'  ,'');
        $pid    = $this->getRequest('pid'  ,'');
        $vid    = $this->getRequest('vid'  ,'');
        $tid    = $this->getRequest('tid'  ,'');
        $classId= $this->getRequest('classId'  ,'');
        $trId   = $this->getRequest('trId'  ,'');

        if($id){
            $ini = model('plat.sys.ini','mysql')->table('car_group')->field('id,name,img') ->where(array('id'=>$id))->getOne();
        }else{
            $ini = array('id'=>'','name'=>'','img'=>'');
        }

        $this->assign('type',$type  ) ;
        $this->assign('lv'  ,$lv    ) ;
        $this->assign('pid' ,$pid   ) ;
        $this->assign('vid' ,$vid   ) ;
        $this->assign('tid' ,$tid   ) ;
        $this->assign('classId' ,$classId) ;
        $this->assign('trId',$trId  ) ;
        $this->assign('ini' ,$ini   ) ;
        $this->template('plat.sys.ini.editCarClass');
    }

    //==========产品配置===================
    public function getProIni(){
        $id    = $this->getRequest('pid'  ,'');
        $iniMo = model('plat.sys.ini','mysql');
        $carIni= $iniMo->getProIni($id) ;
        exit(json_encode($carIni,JSON_UNESCAPED_UNICODE)) ;
    }
    /**
     * 产品新增、编辑分类
     */
    public function saveProIni(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.ini';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $iniMo = model('plat.sys.ini','mysql');
            $id    = $this->getRequest('id'  ,'');
            $level = $this->getRequest('lv','');
            $pid   = $this->getRequest('pid','');
            $name  = $this->getRequest('name','');
            $vid   = $this->getRequest('vid','');
            $img   = $this->getRequest('img','');
            //writeLog($_REQUEST);die;

            $res   = $iniMo->saveProIni($id,$level,$pid,$name,$vid,$img);
//            $res = 3;
            if($res){
                $return['massageId']   = $res ;
                $return['massageCode'] = 'success' ;
                $return['massage']     = $id ? '修改成功' : '增加成功' ;
            }else{
                $return['massage']     = $id ? '修改失败' : '增加失败' ;
            }
        }else{
            $return['massage']         = '没有相关权限' ;
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 编辑二级分类需调用其他页面
     */
    public function editProClass(){
        $id     = $this->getRequest('id'    ,'');
        $pid    = $this->getRequest('pid'   ,'');
        $vid    = $this->getRequest('vid'   ,'');
        $tbId   = $this->getRequest('tbId'  ,'');
        $trId   = $this->getRequest('trId'  ,'');

        if($id){
            $ini = model('plat.sys.ini','mysql')->table('product_category')->field('id,name,img') ->where(array('id'=>$id))->getOne();
        }else{
            $ini = array('id'=>'','name'=>'','img'=>'');
        }

        $this->assign('id'  ,$id    ) ;
        $this->assign('lv'  ,2      ) ;
        $this->assign('pid' ,$pid   ) ;
        $this->assign('vid' ,$vid   ) ;
        $this->assign('tbId',$tbId  ) ;
        $this->assign('trId',$trId  ) ;
        $this->assign('ini' ,$ini   ) ;
        $this->template('plat.sys.ini.editProClass');
    }

    /**
     * 删除产品分类
     */
    public function delProClass(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();
        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.ini';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $iniMo = model('plat.sys.ini','mysql');
            $id    = $this->getRequest('id'  ,'');
            $res   = $iniMo->delProClass($id);
//            $res = 9;
            if($res){
                $return['massageCode'] = 'success' ;
                $return['massage']     = '删除成功' ;
            }else{
                $return['massage']     = '删除失败' ;
            }
        }else{
            $return['massage']         = '没有相关权限' ;
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }

    public function setVid(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();
        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.ini';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        $return = array('massageCode'=>0);
        if($isAuth){
            $iniMo = model('plat.sys.ini','mysql');
            $id    = $this->getRequest('id'  ,'');
            $vid   = $this->getRequest('vid' ,'');
            $type  = $this->getRequest('type' ,'');
            $res   = $iniMo->setVid($id,$vid,$type);
//            $res = 9;
            if($res){
                $return['massageCode'] = 'success' ;
                $return['massage']     = '调整顺序成功' ;
            }else{
                $return['massage']     = '调整顺序失败' ;
            }
        }else{
            $return['massage']         = '没有相关权限' ;
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }





}