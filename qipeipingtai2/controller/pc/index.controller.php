<?php

/**
 * Created by PhpStorm.
 * User: mengzian
 * Date: 2017/5/14
 * Time: 17:29
 */
class PcIndexController extends Controller
{
    private $user = array();

    public function __construct()
    {
        cookie('last_page',$_SERVER['REQUEST_URI']);
        $loginMo    = model('web.login','mysql');
        $this->user = $user = $loginMo->loginIs(false);
    }
    /**
     * @param $htmlName
     * @param string $title
     * @param bool $loadSeach  是否加载搜索
     * @param bool $loadNav    是否加载导航
     */
    protected function main_template($htmlName,$title='首页',$loadSeach=true,$loadNav=true){
        $this ->assign('userInfo',$this->user);

        $iniMo = model('web.ini','mysql');
        //城市配置表
        $cityIni = $iniMo->cityIni();
        $this->assign('cityIni',$cityIni);
        $currentCity = cookie('currentCity')?cookie('currentCity'):'成都市';
        $this->assign('currentCity',$currentCity);
        //电话和qq
        $qqTel = $iniMo->getQQ();
        $this->assign('qqTel',$qqTel);
        //友情链接
        $fLinks = $iniMo->getLinks();
        $this->assign('fLinks',$fLinks);

        $this->assign('title',$title);

        $this->template('pc/layout/head');
        //加载搜索页
        if($loadSeach){
            $keywords = $this->getRequest('keywords','');
            $u_type   = $this->getRequest('u_type','');
            $this->assign('keywords',$keywords);
            $this->assign('u_type',$u_type);
            $this->template('pc/layout/search');
        }
        //加载首页导航
        if($loadNav){
            $this->assign('fun',G('act'));
            $this->template('pc/layout/homeNav');
        }
        //主页面
        $this->template($htmlName);
        //尾部
        $this->template('pc/layout/footer');

    }

    //pc主页
    public function home(){
        //获取banner
        $bannerMo = model('web.banner','mysql');
        $top_banners = $bannerMo->getTopBanner();//顶部banner
        $yao_banners = $bannerMo->getYaoBanner();//腰部banner

        //获取车系
        $cateMo = model('web.category');
        //轿车商家
        $car_cate['cate_1'] = $cateMo->getCarCateByLevel(1,1);//轿车 一级分类
        $car_cate['cate_2'] = $cateMo->getCarCateByLevel(1,2);//轿车 二级分类
        //货车商家
        $van_cate['cate_1'] = $cateMo->getCarCateByLevel(2,1);//货车 一级分类
        $van_cate['cate_2'] = $cateMo->getCarCateByLevel(2,2);//货车 二级分类


        //获取推荐经销商
        $firmsMo = model('web.firms','mysql');
        //当前城市
        $currentCity = cookie('currentCity')?cookie('currentCity'):'成都市';
        $dealers = $firmsMo->getTheDealers($currentCity);

        //获取产品
        $proMo  = model('web.product','mysql');
        $newPro = $proMo->getNewPro($currentCity);
        $empPro = $proMo->getEmpty($currentCity);

        //获取求购
        $nBuyMo = model('web.tobuy','mysql');
        $buyData= $nBuyMo->getDataList(0,$currentCity,1,3);

        $articleMo  = model('web.article','mysql');
        //获取公告
        $pushMo = model('plat.push.push','mysql');
        $pushMo->addToMsg() ;
        //$notices   = $articleMo->getNoticeList(7);
        $notices   = $articleMo->getTuiNotice(14);
        //获取促销活动
        $activities = $articleMo->getActivity(4);
        //获取新闻资讯
        $car_news       = $articleMo->getNews(3);

        $this->assign('top_banners',$top_banners);
        $this->assign('yao_banners',$yao_banners);

        $this->assign('car_cate',$car_cate);
        $this->assign('van_cate',$van_cate);

        $this->assign('newPro',$newPro);
        $this->assign('empPro',$empPro);

        $this->assign('buyData',$buyData['list']);

        $this->assign('dealers',$dealers);

        $this->assign('notices',$notices);
        $this->assign('activities',$activities);
        $this->assign('car_news',$car_news);

        $this->main_template('pc/index/index');
    }

    //轿车商家
    public function cars(){
        $keywords = $this->getRequest('keyword','');
        $business = $this->getRequest('van_cate',0);
        //获取车系
        $cateMo = model('web.category');
        //轿车商家
        $car_cate['cate_1'] = $cateMo->getCarCateByLevel(1,1);//轿车 一级分类
        $car_cate['cate_2'] = $cateMo->getCarCateByLevel(1,2);//轿车 二级分类
        //获取商家
        //$firmMo = model('web.firms','mysql');
        //$firms  = $firmMo->getFirms(1,1,$business,array(),$keywords,1,4);


        $this->assign('car_cate',$car_cate);
        $this->assign('car_cate_2_chose',$business);
        //$this->assign('firms',$firms['list']);
        $this->assign('firms',array());
        $this->main_template('pc/index/cars','轿车商家');
    }
    //货车商家
    public function vans(){
        $keywords = $this->getRequest('keyword','');
        $business = $this->getRequest('van_cate',0);
        //获取车系
        $cateMo = model('web.category');
        //轿车商家
        $car_cate['cate_1'] = $cateMo->getCarCateByLevel(2,1);//轿车 一级分类
        $car_cate['cate_2'] = $cateMo->getCarCateByLevel(2,2);//轿车 二级分类
        //获取商家
        //$firmMo = model('web.firms','mysql');
        //$firms  = $firmMo->getFirms(1,2,$business,array(),$keywords,1,4);


        $this->assign('car_cate',$car_cate);
        $this->assign('car_cate_2_chose',$business);
        //$this->assign('firms',$firms['list']);
        $this->assign('firms',array());
        $this->main_template('pc/index/vans','货车商家');
    }
    //新品促销
    public function newMarket(){
        $cateMo = model('web.category','mysql');
        $cate_1 = $cateMo->getProCateByLevel(1);
        $cate_2 = $cateMo->getProCateByLevel(2);

        $this->assign('proCate',array('cate_1'=>$cate_1,'cate_2'=>$cate_2));

        $this->main_template('pc/index/newMarket','新品促销');
    }
    //库存清仓
    public function clearance(){
        $cateMo = model('web.category','mysql');
        $cate_1 = $cateMo->getProCateByLevel(1);
        $cate_2 = $cateMo->getProCateByLevel(2);

        $this->assign('proCate',array('cate_1'=>$cate_1,'cate_2'=>$cate_2));

        $this->main_template('pc/index/clearance','库存清仓');
    }
    //配件求购
    public function mountings(){
        $cateMo = model('web.category','mysql');

        $car_cate['cate_1'] = $cateMo->getCarCateByLevel(1,1);//轿车 一级分类
        $car_cate['cate_2'] = $cateMo->getCarCateByLevel(1,2);//轿车 二级分类

        $van_cate['cate_1'] = $cateMo->getCarCateByLevel(2,1);//货车 一级分类
        $van_cate['cate_2'] = $cateMo->getCarCateByLevel(2,2);//货车 二级分类

        $tran_cate['cate_1'] = $cateMo->getCarCateByLevel(3,1);//物流 一级分类
        $tran_cate['cate_2'] = $cateMo->getCarCateByLevel(3,2);//物流 二级分类

        $this->assign('car_cate',$car_cate);
        $this->assign('van_cate',$van_cate);
        $this->assign('tran_cate',$tran_cate);

        $this->main_template('pc/index/mountings','配件求购');
    }
    //vin查询
    public function vinQuery(){
        $data = $this->user;
        $this->assign('data',$data);
        $this->main_template('pc/index/vinQuery','vin查询');
    }
    //圈子
    public function circle(){
        $do = $this->getRequest('do','home');
        switch ($do){
            case 'home':
                $fun = 'home';

                $data = array();
                break;
            case 'collect':
                if($this->user){
                    $fun = 'collect';

                    $data = array();
                }else{
                    header('Location:/login');exit;
                }
                break;
            case 'mine':
                if($this->user){
                    $fun = 'mine';

                    $data = array();
                }else{
                    header('Location:/login');exit;
                }
                break;
            default:
                header('Location:/def/circle?do=home');exit;
        }
        $this->assign('data',$data);
        $this->assign('func1',$fun);
        $this->main_template('pc/index/circle','圈子');
    }
    //产品详情
    public function product(){
        if($this->user){
            $id = $this->getRequest('ID','');
            $data = model('web.product')->getProductInfo($id);
//        dump($data);die;
            if($data){
                $this->assign('data',$data);
                $title = '产品详情';
                $myCollectFirms = array();
                $isCollectProduct = false;
                if($this->user){
                    //获取收藏记录
                    $collectMo = model('web.collect','mysql');
                    $myCollectFirms   = $collectMo->getMyCollectStore(1,$this->user['id']);
                    //判断是否收集了该产品
                    $isCollectProduct = $collectMo->isCollectProduct(1,$this->user['id'],$data['id']);
                }
                $this->assign('myCollectFirms',$myCollectFirms);
                $this->assign('isCollectProduct',$isCollectProduct);
                $this->main_template('pc/index/product',$title,true,false);
            }else{
                $this->assign('xieyi',array('value'=>''));
                $this->main_template('pc/index/xieyi','该产品不存在',true,false);
            }
        }else{
            header("Location:/login");
        }
    }
    //求购详情
    public function buyView(){
        if($this->user){
            $id = $this->getRequest('ID','');
            if($id){
                $data = model('web.product')->buyInfo($id);
                if($data){
                    $firm = model('web.firms')->firmInfo($data['firms_id']);
                    if($firm && $firm['qq']){
                        $firm['qq'] = explode(',',$firm['qq']);
                    }
                    if($firm){
                        $this->assign('firm',$firm);
                    }else{
                        $this->assign('msg','店铺信息不存在');
                        $this->template('pc/index/error');
                        die;
                    }
                }else{
                    $this->assign('msg','该求购信息不存在');
                    $this->template('pc/index/error');
                    die;
                }

                //获取收藏记录
                $collectMo = model('web.collect','mysql');
                if($this->user){
                    $myCollectFirms   = $collectMo->getMyCollectStore(1,$this->user['id']);
                    $this->assign('myCollectFirms',$myCollectFirms);
                }
                $this->assign('data',$data);
            }
            $title = '求购详情';
            $this->main_template('pc/index/buyView',$title,true,false);
        }else{
            header("Location:/login");
        }

    }
    //促销活动
    public function activities(){
        $id = $this->getRequest('ID',0);
        $articleMo = model('web.article','mysql');
        //如果不存在id,加载促销活动列表
        if($id){
            $activity = $articleMo->getOneActivity($id);
            if($activity){
                $this->assign('new',$activity);
                //促销活动详情
                $this->main_template('pc/index/activity',$activity['title']);
            }else{
                $this->assign('xieyi',array('value'=>''));
                $this->main_template('pc/index/xieyi','该促销活动走失了');
            }
        }else{
            $page     = $this->getRequest('page',1);
            $start    = ($page-1)*6;
            $news = $articleMo->getActivity(6,$start);
            $this->assign('news',$news);
            //促销活动列表
            $this->main_template('pc/index/activities','促销活动列表');
        }
    }
    //新闻资讯
    public function news(){
        $id = $this->getRequest('ID',0);
        //如果不存在id,加载新闻资讯列表
        $articleMo = model('web.article','mysql');
        if($id){
            $new = $articleMo->getOneNews($id);
            if($new){
                $this->assign('new',$new);
                //新闻资讯详情
                $this->main_template('pc/index/new',$new['title']);
            }else{
                $this->assign('xieyi',array('value'=>''));
                $this->main_template('pc/index/xieyi','该新闻走失了');
            }
        }else{
            $page     = $this->getRequest('page',1);
            $start    = ($page-1)*6;
            $news = $articleMo->getNews(6,$start);
            $this->assign('news',$news);
            //新闻资讯列表
            $this->main_template('pc/index/news');
        }
    }
    //新手上路
    public function newbie(){
        $id       = $this->getRequest('ID',0);
        $articleMo = model('web.article','mysql');
        //如果不存在id,加载新手上路列表
        if($id){
            $new = $articleMo->getOneNewbie($id);
            $this->assign('new',$new);
            //新手上路详情
            $this->main_template('pc/index/newbie');
        }else{
            $keywords07 = $this->getRequest('keywords07','');
            $page       = $this->getRequest('page',1);
            $start      = ($page-1)*10;
            //分页工具
            $res = $articleMo->getNewbie($keywords07,10,$start);
            $pageTool = model('tools.page','mysql');
            $pageHtml = $pageTool->pager($res['count'],$page,10,'keywords07='.$keywords07."&");
            $this->assign('news',$res['list']);
            $this->assign('count',$res['count']);
            $this->assign('pageHtml',$pageHtml);
            $this->assign('keywords07',$keywords07);
            //新手上路列表
            $this->main_template('pc/index/newbieList');
        }
    }
    //消息列表
    public function notices(){
        if($this->user){

            $this->main_template('pc/index/notices');
        }else{
            header("Location:/login");
        }

    }
    //店铺详情
    public function store(){
        if($this->user){
            $id       = $this->getRequest('ID',0);//店铺ID
            $title = '店铺详情';
            $firmsMo = $this->model('web.firms','mysql');
            $firmsInfo = $firmsMo->getFirmInfoByEnID($id);
            if($firmsInfo){

                if($this->user['EnterpriseID']==$id){

                }elseif($firmsInfo['type']==2&&strtotime($this->user['vip_time'])<time()&&$this->user['type']==1){
                    $this->assign('xieyi',array('value'=>''));
                    $this->main_template('pc/index/xieyi','无权限查看');
                    exit;
                }elseif($firmsInfo['type']==2&&$this->user['type']==2){
                    $this->assign('xieyi',array('value'=>''));
                    $this->main_template('pc/index/xieyi','无权限查看');
                    exit;
                }

                $myCollectFirms = array();
                if($this->user){
                    //获取收藏记录
                    $collectMo = model('web.collect','mysql');
                    $myCollectFirms = $collectMo->getMyCollectStore(1,$this->user['id']);
                }
                $this->assign('myCollectFirms',$myCollectFirms);
                $this->assign('firmsInfo',$firmsInfo);
                $this->main_template('pc/index/store',$firmsInfo['companyname'],false,false);

            }else{
                $this->assign('xieyi',array('value'=>''));
                $this->main_template('pc/index/xieyi','该厂商不存在');
            }
        }else{
            header('Location:/login');
        }


    }

    //店铺产品
    public function storeProduct(){
        $ID     = $this->getRequest('ID','');
        $cateMo = model('web.category','mysql');
        $title = $cateMo->table('firms')->field('id,companyname')->where(array('EnterpriseID'=>$ID))->getOne();
        if($title){
            $cate_1 = $cateMo->getProCateByLevel(1);
            $cate_2 = $cateMo->getProCateByLevel(2);
            $this->assign('ID008',$ID);
            $this->assign('proCate',array('cate_1'=>$cate_1,'cate_2'=>$cate_2));
            $this->main_template('pc/index/storeProduct',$title['companyname'],false,false);
        }else{
            $this->assign('xieyi',array('value'=>''));
            $this->main_template('pc/index/xieyi','该厂商不存在');
        }

    }

    //公司简介
    public function info(){
        $iniMo = model('web.ini','mysql');
        $info  = $iniMo->getInfo();
        $this->assign('info',$info);
        $this->main_template('pc/index/info');
    }
    //服务协议
    public function xieyi(){
        $articleMo = model('web.article','mysql');
        $xieyi = $articleMo->getFuWuXieYi();
        $this->assign('xieyi',$xieyi);
        $this->main_template('pc/index/xieyi','服务协议');
    }


    /*发布圈子（分享邀请码）*/
    public function inviteCode(){
        if($this->user){
            $user = $this->user;
//            dump($user);die;
            $this->main_template('pc/person/inviteCode','分享到标题',false,false);
        }else{
            header("Location:/login");
        }
    }

}