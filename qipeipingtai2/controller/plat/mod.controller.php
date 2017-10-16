<?php
/**
 * 模块管理控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/5
 * Time: 9:22
 */
class PlatModController extends Controller {

    public function lists(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.mod';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){
            $modsMo = model('plat.sys.mods','mysql');
            $modsName = $modsMo->getModsName();

            $this->assign('modsName',$modsName);
            $this->template('plat.sys.mod.list');

        }else{
            dump('没有相关权限');

        }
    }


    public function icon(){

      $this->template('plat.tools.fontawesome');

    }

    /**
     * 获取模块列表
     */
    public function getModsList(){

        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        //检查用户权限
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.mod';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){

            $page    = $this->getRequest('page','1');
            $pageSize= $this->getRequest('pageSize','5');
            $status  = $this->getRequest('status','');
            $modName = $this->getRequest('modName','');
            $keywords= $this->getRequest('keywords','');

            $modsMo = model('plat.sys.mods','mysql');
            $return = $modsMo->getModsList($page,$pageSize,$status,$modName,$keywords);

        }else{
            $return['status'] = 201;
            $return['msg']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }

    /**
     * 获取模块
     */
    public function getOneMods(){

        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.mod';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){

            $funId = $this->getRequest('funId','');
            if($funId){
                $modsMo = model('plat.sys.mods','mysql');
                $return = $modsMo->getOneMods($funId);
            }else{
                $return['status'] = 202;
                $return['msg']    = '参数缺失';
            }
        }else{
            $return['status'] = 201;
            $return['msg']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }



    /**
     * 获取模块列表
     */
    public function changeStatus(){

        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.mod';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){

            //搜索条件获取
            $funId = $this->getRequest('funId','');
            $isMenu  = $this->getRequest('isMenu','');
            //查找质量跟踪列表
            //var_dump($serverStatus);
            $modsMo   = model('plat.sys.mods','mysql');
            $result = $modsMo->changeStatus($funId,$isMenu);
            if($result){//判断是否保存成功
                $return['status'] = '1';
                $return['msg']    = '保存成功';
            }else{
                $return['status'] = '0';
                $return['msg']    = '保存失败';
            }

        }else{
            $return['status'] = 201;
            $return['msg']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 保存模块信息
     */
    public function saveModInfo(){

        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();

        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.mod';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);
        if($isAuth){

            $funId = $this->getRequest('funId','');
            $field  = $this->getRequest('field','');
            $key  = $this->getRequest('key','');

            if($funId&&$field&&$key){
                //var_dump($serverStatus);
                $modsMo   = model('plat.sys.mods','mysql');
                $result = $modsMo->saveModInfo($funId,$field,$key);

                if($result){//判断是否保存成功
                    $return['status'] = '1';
                    $return['msg']    = '保存成功';
                }else{
                    $return['status'] = '0';
                    $return['msg']    = '保存失败';
                }

            }else{
                $return['status'] = '202';
                $return['msg']    = '提交信息有误';
            }

        }else{
            $return['status'] = 201;
            $return['msg']    = '没有相关权限';
        }

        exit(json_encode($return,JSON_UNESCAPED_UNICODE));

    }


    /**
     * 部门增加页面
     */
    public function addMod(){
        //检查是否登录
        $mo     = model('suAdmin','mysql');
        $user   = $mo->loginIs();
        $userId = $user["id"];
        $authMo = model("suAdmin","mysql");//链接权限模块
        $mod    = 'plat.mod';
        $fun    = 'lists';
        $isAuth = $authMo->checkUserAuth($userId,$mod,$fun);

        if($isAuth){
            $this->template('plat.sys.mod.addMod');
        }else{
            dump('没有相关权限');
        }
    }



}