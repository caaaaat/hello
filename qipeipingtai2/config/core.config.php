<?php
/**
 * 配置文件
 */
$config = array(
    'domainName'        => 'http://119.23.215.135',//当前域名
    'defaultCon'        => 'm',
    'defaultAct'        => 'a',
    'gzip'              => false,
    /*微信配置*/
    'weixin'            => array(
                        'TOKEN'         => '',
                        'APPID'         => '',
                        'APPSECRET'     => '',
                        'APPTOKEN'      => '',
                        'EncodingAESKey'=> '',
                        'cacheType'     => 'mysql'
    ),
    /*数据库配置*/
    'dbns'              => array(
                       1=> array(
                           'dbdrive'    => 'mysqli',
//                           'dbhost'     => '127.0.0.1',
//                           'dbhost'     => '192.168.2.22',
//                           'dbhost'     => '192.168.2.21',
//                           'dbhost'     => '192.168.2.2',
<<<<<<< HEAD
                           'dbhost'     => '119.23.215.135',
//                           'dbuser'     => 'ydly',
//                           'dbpwd'      => '!@ydly@!',
                           'dbname'     => 'car',
                           'dbport'     => '3306',
=======
                           'dbname'     => 'car',
                           'dbport'     => '3306',
                           'dbuser'     => 'root',
                           'dbpwd'      => 'root',
                           'dbhost'     => '119.23.215.135',
>>>>>>> 3bbb99633b5a999fbb23d83321226bf5ca447a83
                           'dbuser'     => 'car',
                           'dbpwd'      => '!@car@!',
                           'dbpccon'    => false
                       ),
                       2=> array(
                           'dbdrive'    => 'mongodb',
                           'dbhost'     => '',
                           'dbname'     => '',
                           'dbport'     => '',
                           'dbuser'     => '',
                           'dbpwd'      => '',
                           'dbpccon'    => false
                       ),
                        3=> array(
                            'dbdrive'    => 'redis',
                            'dbhost'     => '',
                            'dbname'     => '',
                            'dbport'     => '6379',
                            'dbuser'     => '',
                            'dbpwd'      => '',
                            'dbpccon'    => false
                        )
    ),
    /*环信配置*/
    'huanxin'           => array(
                        'client_id'      => 'YXA6nSEgMG25EeavgqMurK8P2w',
                        'client_secret'  => 'YXA6wfFXLASlyHZDjMfUDRZ7p1sEzeg',
                        'org_name'       => 'wwwu6ucom',
                        'app_name'       => 'ydly',
                        'default_pwd'    => '123'
    ),
    /*融云配置*/
    'rongyun'          => array(
                        'AppKey'=>'qd46yzrf44y4f',
                        'AppSecret'=>'dyYIiDfqbx'
    ),
    /*百度地图配置*/
    'bmap'              => array(
                        'ak'            => 'MasrxSBSfRTUNMnck3fejCBc7amXYjPn'
    ),
    /*smtp配置*/
    'smtp'             => array(
                        'sitename'      =>'旅游',
                        'state'         => 1,
                        'server'        => 'smtp.exmail.qq.com',
                        'port'          => 465,
                        'auth'          => 1,
                        'username'      => 'server@scydgl.com',
                        'password'      => 'Ss520123',
                        'charset'       => 'utf8',
                        'mailfrom'      => 'server@scydgl.com',
                        'ssl'           => true

    ),
    /*cookie域名配置*/
    'cookeDomain'       => '',
);

return $config;
