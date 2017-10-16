/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : car

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-05-18 19:04:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `article_activity`
-- ----------------------------
DROP TABLE IF EXISTS `article_activity`;
CREATE TABLE `article_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '促销活动',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `art_ID` varchar(20) DEFAULT NULL COMMENT '活动ID',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `face_img` varchar(255) DEFAULT NULL COMMENT '仅支持jpg、png格式图片',
  `content` text COMMENT '正文',
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='促销活动';

-- ----------------------------
-- Records of article_activity
-- ----------------------------
INSERT INTO `article_activity` VALUES ('1', '1', '3453533', '活动标题活动标题活动标题', '/data/upload/test_img/QQ截图20170516112021.jpg', '活动内容', '1', '2017-05-16 13:56:27', null);
INSERT INTO `article_activity` VALUES ('2', '2', '2323424', '活动标题活动标题活动标题', '/data/upload/test_img/QQ20170516092241.png', '活动内容', '1', '2017-05-16 13:56:30', null);
INSERT INTO `article_activity` VALUES ('3', '3', '3453334', '活动标题活动标题活动标题活动活动标题活动标题活动标题活动标题活动标题活动标题活动标题活动标题活动标题活动标题活动标题活动标题', '/data/upload/test_img/QQ截图20170516112021.jpg', '活动内容活动内容活动内容', '1', '2017-05-16 13:56:33', null);
INSERT INTO `article_activity` VALUES ('4', '4', '1213213', '活动标题活动标题活动标题活动活动标题活动标题活动标题活动标题活动标题活动标题活动标题活动标题活动标题活动标题活动标题活动标题', '/data/upload/test_img/QQ20170516092241.png', '活动内容活动内容活动内容', '2', '2017-05-16 14:31:55', null);

-- ----------------------------
-- Table structure for `article_newbie`
-- ----------------------------
DROP TABLE IF EXISTS `article_newbie`;
CREATE TABLE `article_newbie` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '新手上路',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `art_ID` varchar(20) DEFAULT NULL COMMENT '问题ID',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `content` text COMMENT '详情正文',
  `create_time` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='新手上路';

-- ----------------------------
-- Records of article_newbie
-- ----------------------------

-- ----------------------------
-- Table structure for `article_news`
-- ----------------------------
DROP TABLE IF EXISTS `article_news`;
CREATE TABLE `article_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '新闻资讯',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `art_ID` varchar(20) DEFAULT NULL COMMENT '资讯ID',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `face_img` varchar(255) DEFAULT NULL COMMENT '仅支持jpg、png格式图片',
  `content` text COMMENT '正文',
  `create_time` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='新闻资讯';

-- ----------------------------
-- Records of article_news
-- ----------------------------
INSERT INTO `article_news` VALUES ('1', '1', '436465', '汽车灯光全扫描:不只有远光灯才讨人厌', '/data/upload/test_img/QQ截图20170516112021.jpg', '【PConline 杂谈】汽车灯光是老生常谈的话题了，鉴于早前《汽车玻璃全扫面》一文获得不少认可，所以今天我们来聊聊汽车灯光。汽车不同灯光的使用场景不尽相同，即便是开车很久的老司机也未必能正确使用所有灯光，反而有些时候恰恰由于所谓“常识”而祸害他人。\r\n\r\n汽车灯光全扫描:不只有远光灯才讨人厌\r\n汽车灯光全扫描:不只有远光灯才讨人厌\r\n\r\n　　真正意义上的车联网时代还未到来，车与车之间的有效交流依旧仅停留在汽车灯光，驾驶员看灯行驶，可有效地预判周边车辆动态轨迹。那么你要是不恰当运用灯光或干脆不用灯光，造成的错误信息在一定程度上会误导对方，导致交通事故发生概率徒增。\r\n\r\n汽车玻璃全扫描:老司机也不一定全懂它\r\n\r\n　　“全扫描”是PConline汽车科技一个关注某个零件的特色栏目，如后续出现汽车轮胎全扫描、汽车尾翼全扫描之类的都是有可能的。为了让文章更全面，文中难免会出现陈年内容，不喜勿喷哈。\r\n\r\n　　接下来你将看到以下信息：\r\n\r\n 　　1| 汽车灯的演变史；\r\n 　　2| 汽车灯的种类与应用场景；\r\n 　　3| 汽车灯光的信息化趋势；\r\n\r\n▍汽车灯的科技演变\r\n\r\n　　我们已经不是第一次聊到汽车灯光的演变。在之前的文章中亦有提及（猛戳下图即可阅读），概括地来说，汽车照明灯是从无到煤油灯、再到氙气灯、后发展到LED灯，总的发展主线是：越来越亮。\r\n\r\n奥迪\r\n\r\n奥迪 \r\n（图注：）氙气大灯\r\n\r\n汽车照明大灯发展历程\r\n类型	应用时间	缺点\r\n煤油灯	1886年-1904年	亮度不足\r\n乙炔灯	1905年-1925年	易被雨水浇灭且产生腐蚀性强的碱石灰（氢氧化钙）\r\n钨丝白炽灯	约1960年时期启用	钨丝汽化遇冷会使灯罩发黑\r\n卤素灯	约1960年时期启用	亮度不满足日益复杂的行车环境\r\n氙气大灯	1995年	易造成炫光\r\nLED灯	2007年	需有效散热\r\n　　汽车集成科技元素日益加持下，汽车大灯越来越智能，市场上推出可随动转向汽车大灯，减少拐角时的盲区；也推出了可单独开关的多LED大灯，让你任何时候都不会被吐槽为远光狗；亦有避免直射行人或动物眼睛的贴心控光技术，尽显人文关怀。\r\n\r\n▍该开哪盏灯很关键\r\n\r\n　　首先我们先来聊一下汽车灯光种类与它们适用环境，资格老的老司机可以略过这章节。各类灯光若在不恰当使用会给周边路人或车辆带来各种“不良反应”：\r\n\r\n汽车灯光 \r\n（图注：常见拨杆式灯光控制模块）\r\n\r\n1.行车灯\r\n　　行车灯又称“日间行车灯”，顾名思义是其即便是像大白天光照环境一样要开启的灯光。这种灯本身设定就不是为了照明，而是一种信号灯，让人辨识到这是一辆正在运行的车辆。外国研究表明，开启日间行车灯能有效较低12.4%的交通事故，同时较低26.4%车祸死亡率。目前市场销售的新车大多都配置上了日间行车灯。\r\n\r\n　　不良反应：按照技术参数规定，汽车日间行车灯发光强度不得小于400cd，不应大于800cd，但有好事者为了酷炫，不但私自改装灯光颜色，还特意调高了亮度，其危害不亚于远光灯。\r\n\r\n2.近光灯\r\n　　近光灯是我们夜间行车最常用的灯光种类，属于一种近距离照明灯，照射范围为30-40米左右。\r\n\r\n　　不良反应：现在路面上SUV众多，由于SUV底盘较高，所以相对应近光灯的水平位置会高于其他轿车，近距离与轿车会车时，近光灯投射角度过高形同于远光灯！所以可调近光灯角度的车型尽可能调低角度。另外，在光线较好的路段，我们会忘了开大灯，这时候也是很危险的，在其他车辆的后视镜里，你的车已经近乎隐形了。\r\n\r\n汽车灯光 \r\n（图注：不恰当用灯会危害他人）\r\n\r\n3.远光灯\r\n　　远光灯同样是重要的照明灯，它是相对于近光灯进行差异化设定，照射角度更高、照射距离更远。多用于无路灯照明的高速公路与昏暗的郊区道路，而且使用的前提是前方没有前车与来车。\r\n\r\n　　不良反应：远光灯的危害相信上过路的娃都有所感受，开启远光灯会车时，会让对方短暂型失明（生理解释是：由于眼球具有应激性，人眼受到强光刺激后，瞳孔由正常的5-8mm自动收缩至1mm左右，使得眼球进光量瞬时减至原来的1/30，出现类似夜盲的情况），在这种情况下，对方根本无法判断来车轮廓以及路况，尤其是拐弯处会车更加危险，要不强行盲行，要不就被逼停；还有另一种情况是后方车辆开启远光灯，前车的两侧与车内后视镜会因此造成大面积光晕，无法判断车后情况，干扰行驶安全；在大雾天也别天真地以为开启远光灯能看更清楚，灯光在雾里造成漫反射，更加看不清了。\r\n\r\n4.转向灯\r\n　　转向灯通过不间断频闪用以警示的信号灯，一般是在转向或变换车道时使用。转向灯以醒目的黄色光示人，闪亮的方式向人强调它的存在感。灯厂奥迪为了强化效果，推出的动感转向灯，并应用至量产车型。\r\n\r\n　　不良反应：变道、拐弯不打转向灯是常见危险驾驶行为，不打招呼就强行变道其他车辆前方，让人置身于追尾危险中。打转向灯其实是件很绅士的行为，打声招呼，人家也更愿意让你先行。\r\n\r\n汽车灯光 \r\n（图注：汽车用灯得看场景）\r\n\r\n5.危险灯\r\n　　危险灯俗称“双闪灯”或“双跳灯”，其开关一般被设计在最显眼的位置，因为它作用非常重要。\r\n\r\n　　不良反应：婚礼车队齐刷刷开警示灯亦是遗留下来的陋习，因为在非恰当情况时使用警示灯，会丧失转向灯的提示作用，危害其他车辆行驶安全。\r\n\r\n6.雾灯\r\n　　较之以上几类灯种，雾灯使用率相对低频。雾灯分前后雾灯，相对应的标识不一样，需要注意区分。前雾灯一般为明亮的黄色，后雾灯则为醒目的红色，雾灯光线拥有亮度高、穿透性强的特点。工作模式一般为三档：前后雾灯关闭/仅开前雾灯/前后雾灯打开。\r\n\r\n　　不良反应：该灯仅限于大雾天时开启，由于其亮度高，其他情况时最好不要开启，避免对其他驾驶员造成眩光干扰。\r\n\r\n▍灯光变化是汽车界的手语了\r\n\r\n　　介绍完那么多类型的灯，可以得知它们的角色不仅仅只是照明的作用，更多地作用是向外界传递更多信息，我们日常驾车已然默契地培养出这样一套适用于汽车的灯光“手语”：\r\n\r\n「双闪」：我的车可能出了点问题，请远离我；\r\n「交替开闭远光灯」：代替喇叭示意路人或车辆注意；\r\n「点踩刹车亮起尾部刹车灯」：提醒后车注意车距；\r\n......等等\r\n\r\n　　像林肯MKX配备尊贵感十足的迎宾灯，量产车的光照效果只是简单的将十字架Logo投射在地面上，大家有没有想过若投影的内容更加丰富，是否可能为未来共享汽车埋下伏笔呢？\r\n\r\n林肯迎宾灯 \r\n（图注：林肯的迎宾灯）\r\n\r\n　　未来的共享汽车必然是强调人与车之间的交流，早前，宝马发布一众品牌100周年系列概念车，其中的宝马Mini全新概念车便大玩灯光秀，车头盖上配有大号灯具，可以在车前小范围区域投影出不同颜色的图案信息。官方称概念车装备有身份传感器，能自动识别走进汽车的人是谁。主打共享概念赋予这盏灯在人车交流上更多的想象空间。\r\n\r\n宝马mini概念车\r\n（图注：宝马Mini全新概念车可在车头前方投射图案）\r\n\r\n　　奔驰对外亦公布一款高科技大灯，除了智能照明外，还有一项名为“数字光束”的灯光技术，车辆大灯能向地面投射各种信息，例如导航信息，车距辅助线，简直就是加强版的抬头显示......\r\n\r\n汽车灯光 \r\n（图注：奔驰的数字光束）\r\n\r\n　　当然，这些灯肯定都是造价不菲......希望大家开车注意安全，该亮的灯该亮，该灭的灯也该灭。', '2017-05-16 14:16:52', '1', null);
INSERT INTO `article_news` VALUES ('2', null, '456466', '勒索病毒入侵 竟导致这两家汽车工厂停工', '/data/upload/test_img/QQ截图20170516111947.jpg', '【PConline 资讯】法国当地时间5月13日，雷诺汽车表示其计算机系统遭到目前臭名昭著的网络勒索病毒攻击，旗下几个工厂已经暂时停工。\r\n\r\n　　当地时间5月13日，雷诺位于法国桑杜维尔和斯洛文尼亚新梅斯托的工厂的生产工作都已经暂停，两家工厂主要生产包括雷诺Twingo、Clio和Trafic。不过由于正处周末，法国工厂并没有处于产能全开的状态，所以病毒带来的影响较小。而斯洛文尼亚的工厂则是主动停产以“避避风头”，等到病毒得到有效控制后再恢复生产。\r\n\r\n雷诺Clio\r\n雷诺Clio\r\n\r\n　　而雷诺的日本盟友可就没这么幸运了。日产汽车在英国桑德兰的工厂就受到了该病毒的攻击，好几个系统受到牵连，据当地媒体报道这家工厂已经停工。桑德兰的这家工厂体量挺大，拥有7000名员工，病毒攻击前正在生产包括日产聆风、逍客和英菲尼迪Q30等等车型。\r\n\r\n日产聆风\r\n日产聆风\r\n\r\n　　编辑点评：此次网络勒索病毒的攻击范围极广，涵盖了全球范围内的多数计算机。而攻击汽车工厂的话，有点黑道大哥收保护费才允许继续生产的意思。', '2017-05-16 14:17:00', '1', null);
INSERT INTO `article_news` VALUES ('3', null, '345664', '念叨那么久 VVT-i和i-VTEC究竟有啥区别?', '/data/upload/test_img/QQ截图20170516135505.jpg', '【PConline 杂谈】“我的卡罗拉有VVT-i，可变气门正时系统能根据实际情况调节气门正时位置，提高动力表现哈哈哈。”\r\n\r\n　　“噢，可是我的思域有i-VTEC，除了可变气门正时外还能升程，你跟我比？”\r\n\r\n　　都说本田大法好，丰田就真的比不上么？来看看这两家的看家法宝究竟是什么。\r\n\r\n1\r\n\r\n　　不过首先，我们先来看看什么是奥拓循环和阿特金森循环。\r\n\r\n　　目前绝大多数的汽油发动机都是奥拓循环，由德国工程师尼古拉斯·奥拓于1876年发明的基于此循环的内燃机，并且申请了专利。\r\n\r\n典型的奥拓循环示意图\r\n典型的奥拓循环示意图\r\n\r\n　　不过，发动机的动力很快就达到一个相对的极限，想要继续压榨动力的话就得用新的技术。除了现在常见的涡轮增压、机械增压外，另一个能够显著提高燃油利用率的就是提高发动机的压缩比。\r\n\r\n　　什么是压缩比呢？简单的理解就是压缩过程中，气缸的最大体积和最小体积之比，也就是活塞在压缩前到达最低点时活塞上方气缸体积和压缩过程中活塞到达最高点时活塞上方气缸体积的比值。\r\n\r\n　　而膨胀比就是在做功时，活塞运动时气缸最大体积和最小体积之比啦。\r\n\r\n1\r\n\r\n　　奥拓循环中，压缩比等于膨胀比。\r\n\r\n　　如果膨胀比大于压缩比，意味着燃料燃烧后气体的膨胀做功将更加完全，燃烧效率更大。而这个就是阿特金森循环的精髓所在。但是早期的阿特金森循环采用的机械结构非常复杂，制造成本和难度都很高，更别说常规的发动机维护。\r\n\r\n　　而目前的阿特金森循环则采用电力控制，推迟进气门的关闭时间，在活塞压缩的过程中有部分油气从进气口中排出，通过减少进气量来达到膨胀比大于压缩比的目的。\r\n\r\n　　简单地说，阿特金森循环的膨胀比大于压缩比，燃油经济性更好。但是由于是通过减少进气量来达到这个效果，所以发动机并没有全力输出。\r\n\r\n什么是VVT-i？\r\n\r\n　　常规发动机中，气门由凸轮轴带动，进排气门的时间都是固定的。固定的进排气节奏在一定条件下其实阻碍了发动机效率的提升，毕竟发动机所需的空气，以及需要排出的气体并不是随转速(凸轮轴)的改变而同步改变的。\r\n\r\n1\r\n\r\n　　不懂的话，想想你跑步的时候，急促的呼吸和过慢的呼吸都会导致自己身体的舒适度下降。在长跑的不同阶段，我们需要调整相应的呼吸频率来迎合身体对于氧气的需求。而对于汽车发动机而言，进气的多少同样取决于发动机实际的需求。如果能够根据发动机的需求来调节进气量，那将直接提升动力表现和燃料的燃烧效率。\r\n\r\n　　发动机处在较低转速时，不需要过多的气体进入，排气的时候也不需要太长的时间。太长的排气时间反而可能会导致发动机反向吸入废气，更加影响效率。而在较高转速时，发动机一个工作冲程仅仅需要千分之几秒，进气不足、排气不净的问题会直接导致发动机效率的降低。\r\n\r\n1\r\n\r\n　　VVT全名为可变气门正时(Variable Valve Timing)。在ECU中存储有不同状况下气门的最佳参数，而ECU从发动机各个部位的传感器中获取发动机的实时参数，包括转速、进气量、节气门位置等等，对应存储的数据，通过控制凸轮轴正时液压控制阀来执行反馈控制，控制气门的开启和关闭时间来匹配发动机的最佳工作状态，减少耗油量和废气排放，并且提高发动机的工作效率。\r\n\r\n　　而VVT-i其实就是多了个智能(intelligent)。发动机由低速向高速转换时，ECU自动将机油压向进气凸轮轴驱动齿轮内的小涡轮，使其旋转一定的角度，控制凸轮轴在60度的范围内前后旋转，改变进气门开启的时刻。由于机油量的增减是连续的，所以气门开启关闭的角度调节也是连续的。\r\n\r\n　　一般说的VVT-i只调节进气的时间长短。而双VVT-i则同时调节进、排气的时间。\r\n\r\n什么是i-VTEC？\r\n\r\n　　首先，VTEC全称“Variable Valve Timing and Lift Electronic Control”。除了VVT-i上有的可调节气门开闭时间之外，由于在同一根凸轮轴上有两三种不同角度的凸轮，使得在不同的转速区有着对应不同的凸轮的使用。凸轮的种类不同直接使得气门开启的幅度不同(进气量的不同)，这好比就是一扇窗，VVT只控制窗的开启时间，而VTEC则同时控制着窗的开启时间和开启的幅度。\r\n\r\n左下角的就是VTEC介入前，右上角则是VTEC介入\r\n左下角的就是VTEC介入前，右上角是VTEC介入后\r\n\r\n　　简单地说，同一根凸轮轴上的不同凸轮，在转速达到一定程度后，电磁阀激活，不同凸轮轴吸附在一起，作用的变成了更凸的那个轮轴。而在低转速时，电磁阀关闭，作用的则是没那么凸的轮轴。凸轮轴的这个“凸”的程度，决定的就是气门开启的幅度。\r\n\r\n1\r\n\r\n　　不过由于高低凸轮轴的不同，使得VTEC的介入非常突然，这也是众多视频中，采用了VTEC技术的本田在达到某个转速后动力提升非常明显，要是改装后马力大幅度提升，VTEC的存在简直就是“没有如蜗牛，有了窜天猴”。\r\n\r\n\r\n\r\n　　所以在此基础上，本田的i-VTEC则能够连续改变凸轮轴的结合角度，使得气门开启的角度变化更加平缓，不会再有“背后被人踹一脚”的感觉。\r\n\r\n为什么前面要说到阿特金森循环？\r\n\r\n　　因为最新的丰田VVT-iw，后缀的w意味着wide，也就是更宽的调节角度，达到了-30—50度的调节范围。由于凸轮轴加入了锁止装置，使得其能够固定在某个角度进行气门的开闭，能够固定延长气门的关闭时间，随着活塞到达最低点后的压缩，部分气体从进气口排出，制造出阿特金森循环的效果。\r\n\r\n　　这套系统主要用于目前的皇冠2.0T车型和卡罗拉的1.2T车型，使得小排量发动机“省油的时候开阿特金森，要马力的时候气门全开，进气量给你最充足”。', '2017-05-16 14:17:03', '1', null);
INSERT INTO `article_news` VALUES ('4', null, '345646', '比亚迪向美国交付了18米铰链式电动大巴', '/data/upload/test_img/QQ截图20170516135513.jpg', '【PConline 资讯】近日，比亚迪在北美亮相了首台纯电动环卫卡车，与此同时，比亚迪正式向北美市场交付全球首台18米长的纯电动铰链式大巴，注意哦，是全球首台！\r\n\r\n　　客户方为美国加州羚羊谷交通运输局AVTA (Antelope Valley Transit Authority)，在2016年2月与比亚迪达成订单，向比亚迪购买85台纯电动大巴，其中，包含了13台18米长的纯电动铰链式大巴，它想以此实现车队2018年全面电动化，从而成为北美首支100%电动大巴车队。\r\n\r\n比亚迪 \r\n首台交付AVTA的纯电动铰链式大巴，将帮助该公司打造100%纯电动化车队\r\n\r\n　　根据比亚迪官方资料显示：截至目前，比亚迪纯电动大巴、卡车及出租车足迹已遍布美国东西两岸，包括田纳西、马里兰、德克萨斯、密苏里和加利福利亚等30多个州。其兰卡斯特工厂目前已聘用约570名当地员工，年产350台大巴/卡车，随着业务的不断发展壮大，年产能将增至1500台，并将继续创造上千个就业岗位。\r\n\r\n　　编辑点评：近日大家的民族自豪感可谓是满满的，先是国产航母下水，后有中国大飞机C919首飞，比亚迪这小小电动车亦是大步地跨进北美市场，你觉得自豪吗？看好这事吗？', '2017-05-16 14:17:06', '1', null);
INSERT INTO `article_news` VALUES ('5', null, '456466', '比亚迪向美国交付了18米铰链式电动大巴', '/data/upload/test_img/QQ截图20170516111947.jpg', '比亚迪向美国交付了18米铰链式电动大巴比亚迪向美国交付了18米铰链式电动大巴比亚迪向美国交付了18米铰链式电动大巴比亚迪向美国交付了18米铰链式电动大巴比亚迪向美国交付了18米铰链式电动大巴比亚迪向美国交付了18米铰链式电动大巴比亚迪向美国交付了18米铰链式电动大巴比亚迪向美国交付了18米铰链式电动大巴比亚迪向美国交付了18米铰链式电动大巴比亚迪向美国交付了18米铰链式电动大巴', '2017-05-16 14:17:08', '1', null);

-- ----------------------------
-- Table structure for `banner`
-- ----------------------------
DROP TABLE IF EXISTS `banner`;
CREATE TABLE `banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'banner管理',
  `type` tinyint(1) DEFAULT NULL COMMENT '1顶部banner 2腰部banner',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `img` varchar(255) DEFAULT NULL COMMENT '顶部banner：仅支持jpg、png格式图片，建议尺寸w=750px，h=330px，不得超过1M\r\n腰部banner：仅支持jpg、png格式图片，建议尺寸w=1200px,h=100px不得超过1M',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `url_type` tinyint(1) DEFAULT '5' COMMENT '1促销活动 2新闻资讯 3新手上路 4外部链接 5无连接',
  `url` varchar(255) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='banner管理';

-- ----------------------------
-- Records of banner
-- ----------------------------
INSERT INTO `banner` VALUES ('1', '1', null, '/data/upload/test_img/QQ20170516092510.jpg', '暗示法萨芬撒地方撒', '4', 'http://mengzian.top', '2017-05-16 10:51:31', '2017-05-16 10:51:31');
INSERT INTO `banner` VALUES ('2', '2', null, '/data/upload/test_img/QQ20170516092359.png', '按时法师法师法', '4', 'http://mengzian.top', '2017-05-16 10:51:45', '2017-05-16 10:51:45');
INSERT INTO `banner` VALUES ('3', '1', null, '/data/upload/test_img/QQ截图20170516111920.jpg', '暗示法萨芬三', '4', 'http://mengzian.top', '2017-05-16 10:51:57', '2017-05-16 10:51:57');
INSERT INTO `banner` VALUES ('4', '1', null, '/data/upload/test_img/QQ截图20170516111947.jpg', '阿萨是否三', '4', 'http://mengzian.top', '2017-05-16 10:52:24', '2017-05-16 10:52:24');
INSERT INTO `banner` VALUES ('5', '2', null, '/data/upload/test_img/QQ20170516092359.png', '暗示法萨芬大', '4', 'http://one.mengzian.top', '2017-05-16 10:52:10', '2017-05-16 10:52:10');
INSERT INTO `banner` VALUES ('6', '1', null, '/data/upload/test_img/QQ截图20170516112021.jpg', 'dsafdsdfvdf', '5', 'http://mengzian.top', '2017-05-16 11:22:38', null);

-- ----------------------------
-- Table structure for `base_ini`
-- ----------------------------
DROP TABLE IF EXISTS `base_ini`;
CREATE TABLE `base_ini` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '基础配置',
  `name` varchar(60) DEFAULT NULL COMMENT '配置名称',
  `value` text,
  `create_time` datetime DEFAULT NULL,
  `updata_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='基础配置';

-- ----------------------------
-- Records of base_ini
-- ----------------------------
INSERT INTO `base_ini` VALUES ('1', '经销商VIP配置', '[{\"sort\":\"1\",\"number\":\"1\",\"money\":\"100\"},{\"sort\":\"2\",\"number\":\"3\",\"money\":\"280\"},{\"sort\":\"3\",\"number\":\"6\",\"money\":\"540\"}]', '2017-05-15 16:55:13', null);
INSERT INTO `base_ini` VALUES ('2', '刷新点配置', '{\"proportion\":{\"ref\":\"2\",\"money\":\"1\"},\"select\":[{\"sort\":\"1\",\"number\":\"1\",\"money\":\"100\"},{\"sort\":\"2\",\"number\":\"3\",\"money\":\"280\"},{\"sort\":\"3\",\"number\":\"6\",\"money\":\"560\"}]}', '2017-05-15 16:55:13', null);
INSERT INTO `base_ini` VALUES ('3', '服务城市配置', null, '2017-05-15 16:55:13', null);
INSERT INTO `base_ini` VALUES ('4', '客服电话QQ配置', '{\"qq\":\"844692974\",\"tel\":\"135-5139-5944\"}', '2017-05-15 16:55:13', null);
INSERT INTO `base_ini` VALUES ('5', '服务协议配置', '1. 特别提示\r\n1.1\r\n北京新浪互联信息服务有限公司(Beijing SINA Internet Information Service Co. Ltd.)、新浪网技术（中国）有限公司(Sina.com Technology (China) Co., Ltd.)及相关关联企业（以下合称“新浪”）同意按照本协议的规定及其不时发布的操作规则提供基于互联网以及移动网的相关服务（以下称“网络服务”），为获得网络服务，服务使用人（以下称“用户”）应当同意本协议的全部条款并按照页面上的提示完成全部的注册程序。用户在进行注册程序过程中点击“同意”按钮即表示用户完全接受本协议项下的全部条款。\r\n1.2\r\n用户注册成功后，新浪将给予每个用户一个用户帐号及相应的密码，该用户帐号和密码由用户负责保管；用户应当对以其用户帐号进行的所有活动和事件负法律责任。\r\n1.3\r\n用户注册成功后，在使用新浪/新浪微博服务的过程中，新浪公司有权基于用户的操作行为进行非商业性的调查研究。\r\n2. 服务内容\r\n2.1\r\n新浪网络服务的具体内容由新浪根据实际情况提供，例如博客、播客、在线音乐、搜索、手机图片铃声下载、交友、论坛(BBS)、聊天室、电子邮件、发表新闻评论等。\r\n2.2\r\n新浪提供的部分网络服务（例如手机图片铃声下载、电子邮件等）为收费的网络服务，用户使用收费网络服务需要向新浪支付一定的费用。对于收费的网络服务，新浪会在用户使用之前给予用户明确的提示，只有用户根据提示确认其愿意支付相关费用，用户才能使用该等收费网络服务。如用户拒绝支付相关费用，则新浪有权不向用户提供该等收费网络服务。\r\n2.3\r\n用户理解，新浪仅提供相关的网络服务，除此之外与相关网络服务有关的设备（如个人电脑、手机、及其他与接入互联网或移动网有关的装置）及所需的费用（如为接入互联网而支付的电话费及上网费、为使用移动网而支付的手机费）均应由用户自行负担。	\r\n3. 服务变更、中断或终止\r\n3.1\r\n鉴于网络服务的特殊性，用户同意新浪有权随时变更、中断或终止部分或全部的网络服务（包括收费网络服务及免费网络服务）。如变更、中断或终止的网络服务属于免费网络服务，新浪无需通知用户，也无需对任何用户或任何第三方承担任何责任；如变更、中断或终止的网络服务属于收费网络服务，新浪应当在变更、中断或终止之前事先通知用户，并应向受影响的用户提供等值的替代性的收费网络服务，如用户不愿意接受替代性的收费网络服务，就该用户已经向新浪支付的服务费，新浪应当按照该用户实际使用相应收费网络服务的情况扣除相应服务费之后将剩余的服务费退还给该用户。\r\n3.2\r\n用户理解，新浪需要定期或不定期地对提供网络服务的平台（如互联网网站、移动网络等）或相关的设备进行检修或者维护，如因此类情况而造成收费网络服务在合理时间内的中断，新浪无需为此承担任何责任，但新浪应尽可能事先进行通告。\r\n3.3\r\n如发生下列任何一种情形，新浪有权随时中断或终止向用户提供本协议项下的网络服务【该网络服务包括但不限于收费及免费网络服务（其中包括基于广告模式的免费网络服务）】而无需对用户或任何第三方承担任何责任： \r\n3.3.1 用户提供的个人资料不真实；\r\n\r\n3.3.2 用户违反本协议中规定的使用规则；\r\n\r\n3.3.3 用户在使用收费网络服务时未按规定向新浪支付相应的服务费。\r\n\r\n3.4\r\n如用户注册的免费网络服务的帐号在任何连续90日内未实际使用，或者用户注册的收费网络服务的帐号在其订购的收费网络服务的服务期满之后连续180日内未实际使用，则新浪有权删除该帐号并停止为该用户提供相关的网络服务。 3.5\r\n用户注册的免费微博帐号在任何连续90日内未实际使用，或者用户注册的收费网络服务的帐号在其订购的收费网络服务的服务期满之后连续180日内未实际使用，则新浪有权删除该帐号并停止为该用户提供相关的网络服务。 \r\n3.6\r\n用户注册的免费微博帐号昵称如存在违反法律法规或国家政策要求，或侵犯任何第三方合法权益的情况，新浪有权收回该账号昵称。 \r\n4. 使用规则\r\n4.1\r\n用户在申请使用新浪网络服务时，必须向新浪提供准确的个人资料，如个人资料有任何变动，必须及时更新。\r\n4.2\r\n用户不应将其帐号、密码转让或出借予他人使用。如用户发现其帐号遭他人非法使用，应立即通知新浪。因黑客行为或用户的保管疏忽导致帐号、密码遭他人非法使用，新浪不承担任何责任。\r\n4.3 \r\n用户同意新浪有权在提供网络服务过程中以各种方式投放各种商业性广告或其他任何类型的商业信息（包括但不限于在新浪网站的任何页面上投放广告），并且，用户同意接受新浪通过电子邮件或其他方式向用户发送商品促销或其他相关商业信息。\r\n4.4\r\n新浪音乐免费在线试听服务是新浪免费向用户提供的正版在线音乐服务，用户同意新浪在提供新浪音乐免费在线试听服务时可以通过适当方式在相关页面投放商业性广告。\r\n4.5\r\n对于用户通过新浪网络服务（包括但不限于论坛、BBS、新闻评论、个人家园）上传到新浪网站上可公开获取区域的任何内容，用户同意新浪在全世界范围内具有免费的、永久性的、不可撤销的、非独家的和完全再许可的权利和许可，以使用、复制、修改、改编、出版、翻译、据以创作衍生作品、传播、表演和展示此等内容（整体或部分），和/或将此等内容编入当前已知的或以后开发的其他任何形式的作品、媒体或技术中。\r\n4.6\r\n用户在使用新浪网络服务过程中，必须遵循以下原则：\r\n4.6.1 遵守中国有关的法律和法规；\r\n\r\n4.6.2 遵守所有与网络服务有关的网络协议、规定和程序；\r\n\r\n4.6.3 不得为任何非法目的而使用网络服务系统；\r\n\r\n4.6.4 不得以任何形式使用新浪网络服务侵犯新浪的商业利益，包括并不限于发布非经新浪许可的商业广告；\r\n\r\n4.6.5 不得利用新浪网络服务系统进行任何可能对互联网或移动网正常运转造成不利影响的行为；\r\n\r\n4.6.6 不得利用新浪提供的网络服务上传、展示或传播任何虚假的、骚扰性的、中伤他人的、辱骂性的、恐吓性的、庸俗淫秽的或其他任何非法的信息资料；\r\n\r\n4.6.7 不得侵犯其他任何第三方的专利权、著作权、商标权、名誉权或其他任何合法权益；\r\n\r\n4.6.8 不得利用新浪网络服务系统进行任何不利于新浪的行为；\r\n\r\n4.7\r\n新浪有权对用户使用新浪网络服务【该网络服务包括但不限于收费及免费网络服务（其中包括基于广告模式的免费网络服务）】的情况进行审查和监督(包括但不限于对用户存储在新浪的内容进行审核)，如用户在使用网络服务时违反任何上述规定，新浪或其授权的人有权要求用户改正或直接采取一切必要的措施（包括但不限于更改或删除用户张贴的内容等、暂停或终止用户使用网络服务的权利）以减轻用户不当行为造成的影响。\r\n4.8\r\n新浪针对某些特定的新浪网络服务的使用通过各种方式（包括但不限于网页公告、电子邮件、短信提醒等）作出的任何声明、通知、警示等内容视为本协议的一部分，用户如使用该等新浪网络服务，视为用户同意该等声明、通知、警示的内容。\r\n\r\n5. 新浪微博客管理规定\r\n5.1\r\n用户注册新浪微博客账号，制作、发布、传播信息内容的，应当使用真实身份信息，不得以虚假、冒用的居民身份信息、企业注册信息、组织机构代码信息进行注册。\r\n5.2\r\n如用户违反前述5.1条之约定，依据相关法律、法规及国家政策要求，新浪有权随时中止或终止用户对新浪微博客网络服务的使用且不承担违约责任。\r\n5.3\r\n新浪将建立健全用户信息安全管理制度、落实技术安全防控措施。新浪将对用户使用新浪微博客网络服务过程中涉及的用户隐私内容加以保护。\r\n\r\n6. 知识产权\r\n6.1\r\n新浪提供的网络服务中包含的任何文本、图片、图形、音频和/或视频资料均受版权、商标和/或其它财产所有权法律的保护，未经相关权利人同意，上述资料均不得在任何媒体直接或间接发布、播放、出于播放或发布目的而改写或再发行，或者被用于其他任何商业目的。所有这些资料或资料的任何部分仅可作为私人和非商业用途而保存在某台计算机内。新浪不就由上述资料产生或在传送或递交全部或部分上述资料过程中产生的延误、不准确、错误和遗漏或从中产生或由此产生的任何损害赔偿，以任何形式，向用户或任何第三方负责。\r\n6.2\r\n新浪为提供网络服务而使用的任何软件（包括但不限于软件中所含的任何图象、照片、动画、录像、录音、音乐、文字和附加程序、随附的帮助材料）的一切权利均属于该软件的著作权人，未经该软件的著作权人许可，用户不得对该软件进行反向工程（reverse engineer）、反向编译（decompile）或反汇编（disassemble）。\r\n\r\n7. 隐私保护\r\n7.1\r\n保护用户隐私是新浪的一项基本政策，新浪保证不对外公开或向第三方提供单个用户的注册资料及用户在使用网络服务时存储在新浪的非公开内容，但下列情况除外：\r\n\r\n7.1.1 事先获得用户的明确授权；\r\n\r\n7.1.2 根据有关的法律法规要求；\r\n\r\n7.1.3 按照相关政府主管部门的要求；\r\n\r\n7.1.4 为维护社会公众的利益；\r\n\r\n7.1.5 为维护新浪的合法权益。\r\n\r\n7.2\r\n新浪可能会与第三方合作向用户提供相关的网络服务，在此情况下，如该第三方同意承担与新浪同等的保护用户隐私的责任，则新浪有权将用户的注册资料等提供给该第三方。\r\n7.3\r\n在不透露单个用户隐私资料的前提下，新浪有权对整个用户数据库进行分析并对用户数据库进行商业上的利用。\r\n\r\n8. 免责声明\r\n8.1\r\n用户明确同意其使用新浪网络服务所存在的风险将完全由其自己承担；因其使用新浪网络服务而产生的一切后果也由其自己承担，新浪对用户不承担任何责任。\r\n8.2\r\n新浪不担保网络服务一定能满足用户的要求，也不担保网络服务不会中断，对网络服务的及时性、安全性、准确性也都不作担保。\r\n8.3\r\n新浪不保证为向用户提供便利而设置的外部链接的准确性和完整性，同时，对于该等外部链接指向的不由新浪实际控制的任何网页上的内容，新浪不承担任何责任。\r\n8.4\r\n对于因不可抗力或新浪不能控制的原因造成的网络服务中断或其它缺陷，新浪不承担任何责任，但将尽力减少因此而给用户造成的损失和影响。\r\n8.5\r\n用户同意，对于新浪向用户提供的下列产品或者服务的质量缺陷本身及其引发的任何损失，新浪无需承担任何责任：\r\n\r\n8.5.1 新浪向用户免费提供的各项网络服务；\r\n\r\n8.5.2 新浪向用户赠送的任何产品或者服务；\r\n\r\n8.5.3 新浪向收费网络服务用户附赠的各种产品或者服务。\r\n\r\n\r\n9. 违约赔偿\r\n9.1\r\n如因新浪违反有关法律、法规或本协议项下的任何条款而给用户造成损失，新浪同意承担由此造成的损害赔偿责任。\r\n9.2\r\n用户同意保障和维护新浪及其他用户的利益，如因用户违反有关法律、法规或本协议项下的任何条款而给新浪或任何其他第三人造成损失，用户同意承担由此造成的损害赔偿责任。\r\n\r\n10. 协议修改\r\n10.1\r\n新浪有权随时修改本协议的任何条款，一旦本协议的内容发生变动，新浪将会直接在新浪网站上公布修改之后的协议内容，该公布行为视为新浪已经通知用户修改内容。新浪也可通过其他适当方式向用户提示修改内容。\r\n10.2\r\n如果不同意新浪对本协议相关条款所做的修改，用户有权停止使用网络服务。如果用户继续使用网络服务，则视为用户接受新浪对本协议相关条款所做的修改。\r\n\r\n11. 通知送达\r\n11.1\r\n本协议项下新浪对于用户所有的通知均可通过网页公告、电子邮件、手机短信或常规的信件传送等方式进行；该等通知于发送之日视为已送达收件人。\r\n11.2 \r\n用户对于新浪的通知应当通过新浪对外正式公布的通信地址、传真号码、电子邮件地址等联系信息进行送达。\r\n\r\n12. 法律管辖\r\n12.1 \r\n本协议的订立、执行和解释及争议的解决均应适用中国法律并受中国法院管辖。\r\n12.2\r\n如双方就本协议内容或其执行发生任何争议，双方应尽量友好协商解决；协商不成时，任何一方均可向新浪所在地的人民法院提起诉讼。\r\n\r\n13. 其他规定\r\n13.1\r\n本协议构成双方对本协议之约定事项及其他有关事宜的完整协议，除本协议规定的之外，未赋予本协议各方其他权利。\r\n13.2\r\n如本协议中的任何条款无论因何种原因完全或部分无效或不具有执行力，本协议的其余条款仍应有效并且有约束力。\r\n13.3 \r\n本协议中的标题仅为方便而设，在解释本协议时应被忽略。\r\n1. 特别提示\r\n1.1\r\n北京新浪互联信息服务有限公司(Beijing SINA Internet Information Service Co. Ltd.)、新浪网技术（中国）有限公司(Sina.com Technology (China) Co., Ltd.)及相关关联企业（以下合称“新浪”）同意按照本协议的规定及其不时发布的操作规则提供基于互联网以及移动网的相关服务（以下称“网络服务”），为获得网络服务，服务使用人（以下称“用户”）应当同意本协议的全部条款并按照页面上的提示完成全部的注册程序。用户在进行注册程序过程中点击“同意”按钮即表示用户完全接受本协议项下的全部条款。\r\n1.2\r\n用户注册成功后，新浪将给予每个用户一个用户帐号及相应的密码，该用户帐号和密码由用户负责保管；用户应当对以其用户帐号进行的所有活动和事件负法律责任。\r\n1.3\r\n用户注册成功后，在使用新浪/新浪微博服务的过程中，新浪公司有权基于用户的操作行为进行非商业性的调查研究。\r\n2. 服务内容\r\n2.1\r\n新浪网络服务的具体内容由新浪根据实际情况提供，例如博客、播客、在线音乐、搜索、手机图片铃声下载、交友、论坛(BBS)、聊天室、电子邮件、发表新闻评论等。\r\n2.2\r\n新浪提供的部分网络服务（例如手机图片铃声下载、电子邮件等）为收费的网络服务，用户使用收费网络服务需要向新浪支付一定的费用。对于收费的网络服务，新浪会在用户使用之前给予用户明确的提示，只有用户根据提示确认其愿意支付相关费用，用户才能使用该等收费网络服务。如用户拒绝支付相关费用，则新浪有权不向用户提供该等收费网络服务。\r\n2.3\r\n用户理解，新浪仅提供相关的网络服务，除此之外与相关网络服务有关的设备（如个人电脑、手机、及其他与接入互联网或移动网有关的装置）及所需的费用（如为接入互联网而支付的电话费及上网费、为使用移动网而支付的手机费）均应由用户自行负担。	\r\n3. 服务变更、中断或终止\r\n3.1\r\n鉴于网络服务的特殊性，用户同意新浪有权随时变更、中断或终止部分或全部的网络服务（包括收费网络服务及免费网络服务）。如变更、中断或终止的网络服务属于免费网络服务，新浪无需通知用户，也无需对任何用户或任何第三方承担任何责任；如变更、中断或终止的网络服务属于收费网络服务，新浪应当在变更、中断或终止之前事先通知用户，并应向受影响的用户提供等值的替代性的收费网络服务，如用户不愿意接受替代性的收费网络服务，就该用户已经向新浪支付的服务费，新浪应当按照该用户实际使用相应收费网络服务的情况扣除相应服务费之后将剩余的服务费退还给该用户。\r\n3.2\r\n用户理解，新浪需要定期或不定期地对提供网络服务的平台（如互联网网站、移动网络等）或相关的设备进行检修或者维护，如因此类情况而造成收费网络服务在合理时间内的中断，新浪无需为此承担任何责任，但新浪应尽可能事先进行通告。\r\n3.3\r\n如发生下列任何一种情形，新浪有权随时中断或终止向用户提供本协议项下的网络服务【该网络服务包括但不限于收费及免费网络服务（其中包括基于广告模式的免费网络服务）】而无需对用户或任何第三方承担任何责任： \r\n3.3.1 用户提供的个人资料不真实；\r\n\r\n3.3.2 用户违反本协议中规定的使用规则；\r\n\r\n3.3.3 用户在使用收费网络服务时未按规定向新浪支付相应的服务费。\r\n\r\n3.4\r\n如用户注册的免费网络服务的帐号在任何连续90日内未实际使用，或者用户注册的收费网络服务的帐号在其订购的收费网络服务的服务期满之后连续180日内未实际使用，则新浪有权删除该帐号并停止为该用户提供相关的网络服务。 3.5\r\n用户注册的免费微博帐号在任何连续90日内未实际使用，或者用户注册的收费网络服务的帐号在其订购的收费网络服务的服务期满之后连续180日内未实际使用，则新浪有权删除该帐号并停止为该用户提供相关的网络服务。 \r\n3.6\r\n用户注册的免费微博帐号昵称如存在违反法律法规或国家政策要求，或侵犯任何第三方合法权益的情况，新浪有权收回该账号昵称。 \r\n4. 使用规则\r\n4.1\r\n用户在申请使用新浪网络服务时，必须向新浪提供准确的个人资料，如个人资料有任何变动，必须及时更新。\r\n4.2\r\n用户不应将其帐号、密码转让或出借予他人使用。如用户发现其帐号遭他人非法使用，应立即通知新浪。因黑客行为或用户的保管疏忽导致帐号、密码遭他人非法使用，新浪不承担任何责任。\r\n4.3 \r\n用户同意新浪有权在提供网络服务过程中以各种方式投放各种商业性广告或其他任何类型的商业信息（包括但不限于在新浪网站的任何页面上投放广告），并且，用户同意接受新浪通过电子邮件或其他方式向用户发送商品促销或其他相关商业信息。\r\n4.4\r\n新浪音乐免费在线试听服务是新浪免费向用户提供的正版在线音乐服务，用户同意新浪在提供新浪音乐免费在线试听服务时可以通过适当方式在相关页面投放商业性广告。\r\n4.5\r\n对于用户通过新浪网络服务（包括但不限于论坛、BBS、新闻评论、个人家园）上传到新浪网站上可公开获取区域的任何内容，用户同意新浪在全世界范围内具有免费的、永久性的、不可撤销的、非独家的和完全再许可的权利和许可，以使用、复制、修改、改编、出版、翻译、据以创作衍生作品、传播、表演和展示此等内容（整体或部分），和/或将此等内容编入当前已知的或以后开发的其他任何形式的作品、媒体或技术中。\r\n4.6\r\n用户在使用新浪网络服务过程中，必须遵循以下原则：\r\n4.6.1 遵守中国有关的法律和法规；\r\n\r\n4.6.2 遵守所有与网络服务有关的网络协议、规定和程序；\r\n\r\n4.6.3 不得为任何非法目的而使用网络服务系统；\r\n\r\n4.6.4 不得以任何形式使用新浪网络服务侵犯新浪的商业利益，包括并不限于发布非经新浪许可的商业广告；\r\n\r\n4.6.5 不得利用新浪网络服务系统进行任何可能对互联网或移动网正常运转造成不利影响的行为；\r\n\r\n4.6.6 不得利用新浪提供的网络服务上传、展示或传播任何虚假的、骚扰性的、中伤他人的、辱骂性的、恐吓性的、庸俗淫秽的或其他任何非法的信息资料；\r\n\r\n4.6.7 不得侵犯其他任何第三方的专利权、著作权、商标权、名誉权或其他任何合法权益；\r\n\r\n4.6.8 不得利用新浪网络服务系统进行任何不利于新浪的行为；\r\n\r\n4.7\r\n新浪有权对用户使用新浪网络服务【该网络服务包括但不限于收费及免费网络服务（其中包括基于广告模式的免费网络服务）】的情况进行审查和监督(包括但不限于对用户存储在新浪的内容进行审核)，如用户在使用网络服务时违反任何上述规定，新浪或其授权的人有权要求用户改正或直接采取一切必要的措施（包括但不限于更改或删除用户张贴的内容等、暂停或终止用户使用网络服务的权利）以减轻用户不当行为造成的影响。\r\n4.8\r\n新浪针对某些特定的新浪网络服务的使用通过各种方式（包括但不限于网页公告、电子邮件、短信提醒等）作出的任何声明、通知、警示等内容视为本协议的一部分，用户如使用该等新浪网络服务，视为用户同意该等声明、通知、警示的内容。\r\n\r\n5. 新浪微博客管理规定\r\n5.1\r\n用户注册新浪微博客账号，制作、发布、传播信息内容的，应当使用真实身份信息，不得以虚假、冒用的居民身份信息、企业注册信息、组织机构代码信息进行注册。\r\n5.2\r\n如用户违反前述5.1条之约定，依据相关法律、法规及国家政策要求，新浪有权随时中止或终止用户对新浪微博客网络服务的使用且不承担违约责任。\r\n5.3\r\n新浪将建立健全用户信息安全管理制度、落实技术安全防控措施。新浪将对用户使用新浪微博客网络服务过程中涉及的用户隐私内容加以保护。\r\n\r\n6. 知识产权\r\n6.1\r\n新浪提供的网络服务中包含的任何文本、图片、图形、音频和/或视频资料均受版权、商标和/或其它财产所有权法律的保护，未经相关权利人同意，上述资料均不得在任何媒体直接或间接发布、播放、出于播放或发布目的而改写或再发行，或者被用于其他任何商业目的。所有这些资料或资料的任何部分仅可作为私人和非商业用途而保存在某台计算机内。新浪不就由上述资料产生或在传送或递交全部或部分上述资料过程中产生的延误、不准确、错误和遗漏或从中产生或由此产生的任何损害赔偿，以任何形式，向用户或任何第三方负责。\r\n6.2\r\n新浪为提供网络服务而使用的任何软件（包括但不限于软件中所含的任何图象、照片、动画、录像、录音、音乐、文字和附加程序、随附的帮助材料）的一切权利均属于该软件的著作权人，未经该软件的著作权人许可，用户不得对该软件进行反向工程（reverse engineer）、反向编译（decompile）或反汇编（disassemble）。\r\n\r\n7. 隐私保护\r\n7.1\r\n保护用户隐私是新浪的一项基本政策，新浪保证不对外公开或向第三方提供单个用户的注册资料及用户在使用网络服务时存储在新浪的非公开内容，但下列情况除外：\r\n\r\n7.1.1 事先获得用户的明确授权；\r\n\r\n7.1.2 根据有关的法律法规要求；\r\n\r\n7.1.3 按照相关政府主管部门的要求；\r\n\r\n7.1.4 为维护社会公众的利益；\r\n\r\n7.1.5 为维护新浪的合法权益。\r\n\r\n7.2\r\n新浪可能会与第三方合作向用户提供相关的网络服务，在此情况下，如该第三方同意承担与新浪同等的保护用户隐私的责任，则新浪有权将用户的注册资料等提供给该第三方。\r\n7.3\r\n在不透露单个用户隐私资料的前提下，新浪有权对整个用户数据库进行分析并对用户数据库进行商业上的利用。\r\n\r\n8. 免责声明\r\n8.1\r\n用户明确同意其使用新浪网络服务所存在的风险将完全由其自己承担；因其使用新浪网络服务而产生的一切后果也由其自己承担，新浪对用户不承担任何责任。\r\n8.2\r\n新浪不担保网络服务一定能满足用户的要求，也不担保网络服务不会中断，对网络服务的及时性、安全性、准确性也都不作担保。\r\n8.3\r\n新浪不保证为向用户提供便利而设置的外部链接的准确性和完整性，同时，对于该等外部链接指向的不由新浪实际控制的任何网页上的内容，新浪不承担任何责任。\r\n8.4\r\n对于因不可抗力或新浪不能控制的原因造成的网络服务中断或其它缺陷，新浪不承担任何责任，但将尽力减少因此而给用户造成的损失和影响。\r\n8.5\r\n用户同意，对于新浪向用户提供的下列产品或者服务的质量缺陷本身及其引发的任何损失，新浪无需承担任何责任：\r\n\r\n8.5.1 新浪向用户免费提供的各项网络服务；\r\n\r\n8.5.2 新浪向用户赠送的任何产品或者服务；\r\n\r\n8.5.3 新浪向收费网络服务用户附赠的各种产品或者服务。\r\n\r\n\r\n9. 违约赔偿\r\n9.1\r\n如因新浪违反有关法律、法规或本协议项下的任何条款而给用户造成损失，新浪同意承担由此造成的损害赔偿责任。\r\n9.2\r\n用户同意保障和维护新浪及其他用户的利益，如因用户违反有关法律、法规或本协议项下的任何条款而给新浪或任何其他第三人造成损失，用户同意承担由此造成的损害赔偿责任。\r\n\r\n10. 协议修改\r\n10.1\r\n新浪有权随时修改本协议的任何条款，一旦本协议的内容发生变动，新浪将会直接在新浪网站上公布修改之后的协议内容，该公布行为视为新浪已经通知用户修改内容。新浪也可通过其他适当方式向用户提示修改内容。\r\n10.2\r\n如果不同意新浪对本协议相关条款所做的修改，用户有权停止使用网络服务。如果用户继续使用网络服务，则视为用户接受新浪对本协议相关条款所做的修改。\r\n\r\n11. 通知送达\r\n11.1\r\n本协议项下新浪对于用户所有的通知均可通过网页公告、电子邮件、手机短信或常规的信件传送等方式进行；该等通知于发送之日视为已送达收件人。\r\n11.2 \r\n用户对于新浪的通知应当通过新浪对外正式公布的通信地址、传真号码、电子邮件地址等联系信息进行送达。\r\n\r\n12. 法律管辖\r\n12.1 \r\n本协议的订立、执行和解释及争议的解决均应适用中国法律并受中国法院管辖。\r\n12.2\r\n如双方就本协议内容或其执行发生任何争议，双方应尽量友好协商解决；协商不成时，任何一方均可向新浪所在地的人民法院提起诉讼。\r\n\r\n13. 其他规定\r\n13.1\r\n本协议构成双方对本协议之约定事项及其他有关事宜的完整协议，除本协议规定的之外，未赋予本协议各方其他权利。\r\n13.2\r\n如本协议中的任何条款无论因何种原因完全或部分无效或不具有执行力，本协议的其余条款仍应有效并且有约束力。\r\n13.3 \r\n本协议中的标题仅为方便而设，在解释本协议时应被忽略。', '2017-05-15 16:55:13', null);
INSERT INTO `base_ini` VALUES ('6', '分享送刷新点配置', '{\"invitation\":\"50\",\"invited\":\"200\"}', '2017-05-15 16:55:13', null);
INSERT INTO `base_ini` VALUES ('7', '汽修厂拨打数量分级配置', '[{\"lv\":\"1\",\"min\":\"0\",\"max\":\"100\"},{\"lv\":\"2\",\"min\":\"110\",\"max\":\"300\"},{\"lv\":\"3\",\"min\":\"310\",\"max\":\"\"}]', '2017-05-15 16:55:13', null);
INSERT INTO `base_ini` VALUES ('8', '业务员提成配置', '{\"commission\":{\"new_relation\":\"10\",\"relation\":\"1\",\"recharge\":\"0\"},\"frequency\":[{\"lv\":\"1\",\"money\":\"0.2\"},{\"lv\":\"2\",\"money\":\"0.3\"},{\"lv\":\"3\",\"money\":\"0.4\"}]}', '2017-05-15 16:55:13', null);
INSERT INTO `base_ini` VALUES ('9', '公司简介', '中兴通讯是全球领先的综合通信解决方案提供商。公司通过为全球160多个国家和地区的电信运营商和企业网客户提供创新技术与产品解决方案，让全世界用户享有语音、数据、多媒体、无线宽带等全方位沟通。公司成立于1985年，在香港和深圳两地上市，是中国最大的通信设备上市公司。\r\n \r\n　　中兴通讯拥有通信业界最完整的、端到端的产品线和融合解决方案，通过全系列的无线、有线、业务、终端产品和专业通信服务，灵活满足全球不同运营商和企业网客户的差异化需求以及快速创新的追求。2014年中兴通讯实现营业收入814.7亿元人民币，净利润26.3亿元人民币，同比增长94%。目前，中兴通讯已全面服务于全球主流运营商及企业网客户，智能终端发货量位居美国前四，并被誉为“智慧城市的标杆企业”。\r\n \r\n　　中兴通讯坚持以持续技术创新为客户不断创造价值。公司在美国、法国、瑞典、印度、中国等地共设有20个全球研发机构，近3万名国内外研发人员专注于行业技术创新；PCT专利申请量近5年均居全球前三，2011、2012年PCT蝉联全球前一。公司依托分布于全球的107个分支机构，凭借不断增强的创新能力、突出的灵活定制能力、日趋完善的交付能力赢得全球客户的信任与合作。\r\n \r\n　　中兴通讯为联合国全球契约组织成员，坚持在全球范围内贯彻可持续发展理念，实现社会、环境及利益相关者的和谐共生。我们运用通信技术帮助不同地区的人们享有平等的通信自由；我们将“创新、融合、绿色”理念贯穿到整个产品生命周期，以及研发、生产、物流、客户服务等全流程，为实现全球性降低能耗和二氧化碳排放不懈努力。我们还在全球范围内开展社区公益和救助行动，参加了印尼海啸、海地及汶川地震等重大自然灾害救助，并成立了中国规模最大的“关爱儿童专项基金”。\r\n \r\n　　未来，中兴通讯将继续致力于引领全球通信产业的发展，应对全球通信领域更趋日新月异的挑战。', null, null);

-- ----------------------------
-- Table structure for `car_group`
-- ----------------------------
DROP TABLE IF EXISTS `car_group`;
CREATE TABLE `car_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '经销商及车系表',
  `type` tinyint(1) DEFAULT NULL COMMENT '1.轿车商家 2.货车商家 3.物流货运',
  `pid` int(11) DEFAULT NULL COMMENT '父级id',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `name` varchar(60) DEFAULT NULL,
  `level` tinyint(1) DEFAULT NULL COMMENT '层级 1,2,3,4 ... ',
  `img` varchar(128) DEFAULT NULL COMMENT '二级分类需要图片',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='经销商及车系表';

-- ----------------------------
-- Records of car_group
-- ----------------------------
INSERT INTO `car_group` VALUES ('1', '1', '0', '1', '德国车系', '1', null);
INSERT INTO `car_group` VALUES ('2', '1', '0', '2', '美国车系', '1', null);
INSERT INTO `car_group` VALUES ('3', '1', '1', '1', '宝马1', '2', '/images/header/user-def.jpg');
INSERT INTO `car_group` VALUES ('4', '1', '1', '2', '宝马2', '2', '/images/login/bg.jpg');
INSERT INTO `car_group` VALUES ('6', '1', '3', '1', '进口宝马', '3', null);
INSERT INTO `car_group` VALUES ('7', '1', '3', '2', '宝马', '3', null);
INSERT INTO `car_group` VALUES ('8', '1', '6', '1', 'X5', '4', null);
INSERT INTO `car_group` VALUES ('9', '1', '6', '2', 'X7', '5', null);
INSERT INTO `car_group` VALUES ('10', '2', '0', '1', '国产货车', '1', null);
INSERT INTO `car_group` VALUES ('11', '1', '0', '3', '意大利车系', '1', null);
INSERT INTO `car_group` VALUES ('12', '1', '1', '3', '宝马3', '2', '/data/upload/20170518/e09a96bedb5f9c30e5db4a298da0b79b.jpg');

-- ----------------------------
-- Table structure for `circle`
-- ----------------------------
DROP TABLE IF EXISTS `circle`;
CREATE TABLE `circle` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '圈子及评论',
  `vid` varchar(20) DEFAULT NULL COMMENT '圈子ID',
  `content` varchar(255) DEFAULT NULL COMMENT '评论和回复的限制字数长度为200字',
  `imgs` text COMMENT '上传图片最多9张(回复不能上传图片)',
  `parent_id` int(11) DEFAULT NULL,
  `level` tinyint(3) DEFAULT NULL COMMENT '层级',
  `type` tinyint(1) DEFAULT NULL COMMENT '用户类型 1企业厂商，2业务员',
  `fu_id` int(11) DEFAULT NULL COMMENT 'type=1:关联厂商表;\r\ntype=2:关联业务员表',
  `comments` int(11) DEFAULT '0' COMMENT '评论数',
  `create_time` datetime DEFAULT NULL,
  `collection` int(11) DEFAULT NULL COMMENT '收藏数',
  `area` varchar(30) DEFAULT NULL COMMENT '发布城市',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='圈子及评论';

-- ----------------------------
-- Records of circle
-- ----------------------------
INSERT INTO `circle` VALUES ('3', null, '23432432', null, '2', '2', '2', '2', '0', '2017-05-14 13:36:27', null, null);
INSERT INTO `circle` VALUES ('4', null, 'fggd', null, '2', '2', '1', '1', '0', '2017-05-14 13:36:27', null, null);
INSERT INTO `circle` VALUES ('7', null, 'qe', null, '2', '2', '1', '4', '0', '2017-05-14 13:36:27', null, null);
INSERT INTO `circle` VALUES ('8', null, 'qwe', null, '2', '2', '2', '1', '0', '2017-05-14 13:36:27', null, null);
INSERT INTO `circle` VALUES ('2', '413213423', '/data/upload/20170515/29931c2bf6ecd93cb0d27f3cc78936fb.jpg,/data/upload/20170515/29931c2bf6ecd93cb0d27f3cc78936fb.jpg,/data/upload/20170515/29931c2bf6ecd93cb0d27f3cc78936fb.jpg,/data/upload/20170515/29931c2bf6ecd93cb0d27f3cc78936fb.jpg,/data/upload/201705', '/data/upload/20170515/29931c2bf6ecd93cb0d27f3cc78936fb.jpg,/data/upload/20170515/29931c2bf6ecd93cb0d27f3cc78936fb.jpg,/data/upload/20170515/29931c2bf6ecd93cb0d27f3cc78936fb.jpg', null, '1', '1', '1', '4', '2017-05-14 13:36:27', null, '成都');

-- ----------------------------
-- Table structure for `collect_circle`
-- ----------------------------
DROP TABLE IF EXISTS `collect_circle`;
CREATE TABLE `collect_circle` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '圈子收藏表',
  `circle_id` int(11) DEFAULT NULL COMMENT 'circle表id',
  `type` tinyint(1) DEFAULT NULL COMMENT '用户类型 1企业厂商，2业务员',
  `fu_id` int(11) DEFAULT NULL COMMENT 'type=1:关联厂商表;\r\ntype=2:关联业务员表',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='圈子收藏表';

-- ----------------------------
-- Records of collect_circle
-- ----------------------------

-- ----------------------------
-- Table structure for `collect_firms`
-- ----------------------------
DROP TABLE IF EXISTS `collect_firms`;
CREATE TABLE `collect_firms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '收藏店铺',
  `type` tinyint(1) DEFAULT NULL COMMENT '用户类型 1企业厂商，2业务员',
  `fu_id` int(11) DEFAULT NULL COMMENT 'type=1:关联厂商表;\r\ntype=2:关联业务员表',
  `firms_id` int(11) DEFAULT NULL COMMENT '收藏的厂商id  firms表',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收藏店铺';

-- ----------------------------
-- Records of collect_firms
-- ----------------------------

-- ----------------------------
-- Table structure for `collect_product`
-- ----------------------------
DROP TABLE IF EXISTS `collect_product`;
CREATE TABLE `collect_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '收藏的收藏产品',
  `type` int(1) DEFAULT NULL COMMENT '用户类型 1企业厂商，2业务员',
  `fu_id` int(11) DEFAULT NULL COMMENT 'type=1:关联厂商表;\r\ntype=2:关联业务员表',
  `pro_id` int(11) DEFAULT NULL COMMENT '收藏的产品表id product_list表',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收藏产品';

-- ----------------------------
-- Records of collect_product
-- ----------------------------

-- ----------------------------
-- Table structure for `core_auth`
-- ----------------------------
DROP TABLE IF EXISTS `core_auth`;
CREATE TABLE `core_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限表',
  `modName` varchar(32) DEFAULT NULL COMMENT '模块名',
  `modCode` varchar(32) DEFAULT NULL COMMENT '模块标示',
  `modIco` varchar(20) DEFAULT NULL COMMENT '模块ico',
  `funName` varchar(32) DEFAULT NULL COMMENT '方法名',
  `funCode` varchar(32) DEFAULT NULL COMMENT '方法标示',
  `funIco` varchar(20) DEFAULT NULL COMMENT '方法ico',
  `isMenu` smallint(1) DEFAULT '1' COMMENT '模块是否用于菜单',
  `sort` smallint(3) DEFAULT '999' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of core_auth
-- ----------------------------
INSERT INTO `core_auth` VALUES ('1', '系统配置', 'plat.sys.mod', 'fa-home', '模块管理', 'mods', 'fa-gears', '1', '100');
INSERT INTO `core_auth` VALUES ('2', '管理员配置', 'plat.sys.suUser', 'fa-home', '管理员配置', 'suUser', 'fa-user-plus', '1', '101');
INSERT INTO `core_auth` VALUES ('3', 'VIP到期提醒', 'plat.firms.firms', 'fa-home', 'VIP到期提醒', 'vips', 'fa-users', '1', '102');
INSERT INTO `core_auth` VALUES ('4', '操作日志', 'plat.sys.actionLog', 'fa-home', '操作日志', 'actionLog', 'fa-users', '1', '103');
INSERT INTO `core_auth` VALUES ('5', '系统配置', 'plat.sys.ini', 'fa-home', '经销商及车系配置', 'iniIndex', 'fa-bar-chart', '1', '104');
INSERT INTO `core_auth` VALUES ('6', '系统配置', 'plat.sys.notice', 'fa-home', '公告管理', 'index', 'fa-newspaper-o', '1', '105');
INSERT INTO `core_auth` VALUES ('7', 'banner管理', 'plat.audit.reportforms', 'fa-home', '头部banner', 'suIndex', 'fa-bar-chart', '1', '1');
INSERT INTO `core_auth` VALUES ('8', 'banner管理', 'plat.supplier.visa.order', 'fa-home', '腰部banner', 'index', 'fa-cc-visa', '1', '2');
INSERT INTO `core_auth` VALUES ('9', '业务员工资配置', 'plat.audit.manager', 'fa-home', '工资记录', 'mIndex', 'fa-money', '1', '3');
INSERT INTO `core_auth` VALUES ('10', '业务员工资配置', 'plat.audit.manager', 'fa-home', '财务统计', 'gmIndex', 'fa-money', '1', '4');
INSERT INTO `core_auth` VALUES ('11', '业务员工资配置', 'plat.audit.manager', 'fa-home', '财务流水', 'fdIndex', 'fa-money', '1', '5');
INSERT INTO `core_auth` VALUES ('12', '业务员工资配置', 'plat.supplier.oversee', 'fa-home', '员工个人工资记录', 'starDetail', 'fa-shield', '1', '6');
INSERT INTO `core_auth` VALUES ('13', '业务员管理', 'Plat.sales.sales', 'fa-home', '业务员管理', 'salesIndex', 'fa-vimeo', '1', '7');
INSERT INTO `core_auth` VALUES ('14', '业务员管理', 'supplier.create', 'fa-home', '圈子记录', 'supplier.create', 'fa-shield', '1', '8');
INSERT INTO `core_auth` VALUES ('15', '业务员管理', 'plat.custom.notice', 'fa-home', '关联厂商', 'noticePage', 'fa-vimeo', '1', '9');
INSERT INTO `core_auth` VALUES ('16', '互动圈子', 'plat.supplier.lines', 'fa-home', '评论管理', 'lists', 'fa-cog', '1', '10');
INSERT INTO `core_auth` VALUES ('17', '人工收费', 'plat.supplier.order', 'fa-home', '收费记录', 'index', 'fa-shopping-cart', '1', '11');
INSERT INTO `core_auth` VALUES ('18', '厂商管理', 'plat.custom.line', 'fa-home', '来访记录(经销商)', 'customAudit', 'fa-edit', '1', '12');
INSERT INTO `core_auth` VALUES ('19', '厂商管理', 'plat.supplier.visaProduct', 'fa-home', '求购记录', 'auditIndex', 'fa-edit', '1', '13');
INSERT INTO `core_auth` VALUES ('20', '厂商管理', 'plat.wechat.banner', 'fa-home', '产品信息', 'banner', 'fa-cog', '1', '14');
INSERT INTO `core_auth` VALUES ('21', '厂商管理', 'plat.sys.coupon', 'fa-home', '访问记录', 'couponIndex', 'fa-map', '1', '15');
INSERT INTO `core_auth` VALUES ('22', '厂商管理', 'plat.product.line', 'fa-home', '基础信息', 'lists', 'fa-edit', '1', '16');
INSERT INTO `core_auth` VALUES ('23', '厂商管理', 'plat.wechat.tags', 'fa-home', '刷新点记录', 'tagsIndex', 'fa-tag', '1', '17');
INSERT INTO `core_auth` VALUES ('24', '厂商管理', 'plat.custom.demand', 'fa-home', '关联业务员', 'suDemandIndex', 'fa-vimeo', '1', '18');
INSERT INTO `core_auth` VALUES ('25', '厂商管理', 'plat.product.line', 'fa-home', '圈子记录', 'price', 'fa-edit', '1', '19');
INSERT INTO `core_auth` VALUES ('26', '厂商管理', 'plat.custom.demand', 'fa-home', '认证信息', 'zbDemandPage', 'fa-edit', '1', '20');
INSERT INTO `core_auth` VALUES ('27', '厂商管理', 'plat.product.line', 'fa-home', '邀请记录', 'show', 'fa-edit', '1', '21');
INSERT INTO `core_auth` VALUES ('28', '厂商管理', 'plat.wechat.tags', 'fa-home', 'VIP记录(经销商)', 'tagsAuth', 'fa-tag', '1', '22');
INSERT INTO `core_auth` VALUES ('29', '推送消息', 'plat.supplier.notice', 'fa-home', '推送记录', 'noticePage', 'fa-envelope', '1', '23');
INSERT INTO `core_auth` VALUES ('30', '文章管理', 'plat.supplier.visaProduct', 'fa-home', '查看文章', 'index', 'fa-cc-visa', '1', '24');
INSERT INTO `core_auth` VALUES ('31', '文章管理', 'plat.wechat.attention', 'fa-home', '添加/编辑文章', 'showDataPage', 'fa-user-secret', '1', '25');
INSERT INTO `core_auth` VALUES ('32', '文章管理', 'plat.wechat.honBao', 'fa-home', '新手上路', 'showListToPage', 'fa-money', '1', '26');
INSERT INTO `core_auth` VALUES ('33', '文章管理', 'plat.wechat.honBao', 'fa-home', '其他', 'showListPage', 'fa-money', '1', '27');
INSERT INTO `core_auth` VALUES ('34', '求购记录', 'plat.supplier.manager', 'fa-home', '查看记录', 'index', 'fa-map', '1', '28');
INSERT INTO `core_auth` VALUES ('35', '活动营销', 'plat.supplier.lines', 'fa-home', 'PC友情链接配置', 'leader', 'fa-shield', '1', '29');
INSERT INTO `core_auth` VALUES ('36', '活动营销', 'plat.report.monthReport', 'fa-home', 'PC推荐经销商', 'monthReportChild', 'fa-bar-chart', '1', '30');
INSERT INTO `core_auth` VALUES ('37', '统计分析-厂商统计', 'plat.supplier.visaProduct', 'fa-home', '圈子统计', 'visaMaterialIndex', 'fa-cc-visa', '1', '31');
INSERT INTO `core_auth` VALUES ('38', '统计分析-厂商统计', 'plat.supplier', 'fa-home', '活跃度统计', 'auth', 'fa-shield', '1', '32');
INSERT INTO `core_auth` VALUES ('39', '统计分析-厂商统计', 'plat.supplier.lines', 'fa-home', '访问统计', 'show', 'fa-shield', '1', '33');
INSERT INTO `core_auth` VALUES ('40', '统计分析-厂商统计', 'plat.audit.report', 'fa-home', '产品统计', 'suIndex', 'fa-money', '0', '34');
INSERT INTO `core_auth` VALUES ('41', '认证申请', 'plat.audit.reportforms', 'fa-home', '认证申请', 'suDataPage', 'fa-bar-chart', '0', '35');
INSERT INTO `core_auth` VALUES ('42', '系统配置', null, 'fa-home', 'test', null, 'fa-shield', '0', '36');
INSERT INTO `core_auth` VALUES ('43', '系统配置', null, 'fa-home', 'test', null, 'fa-shield', '0', '37');

-- ----------------------------
-- Table structure for `core_auth_copy`
-- ----------------------------
DROP TABLE IF EXISTS `core_auth_copy`;
CREATE TABLE `core_auth_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限表',
  `modName` varchar(32) DEFAULT NULL COMMENT '模块名',
  `modCode` varchar(32) DEFAULT NULL COMMENT '模块标示',
  `modIco` varchar(20) DEFAULT NULL COMMENT '模块ico',
  `funName` varchar(32) DEFAULT NULL COMMENT '方法名',
  `funCode` varchar(32) DEFAULT NULL COMMENT '方法标示',
  `funIco` varchar(20) DEFAULT NULL COMMENT '方法ico',
  `isMenu` smallint(1) DEFAULT '1' COMMENT '模块是否用于菜单',
  `sort` smallint(3) DEFAULT '999' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of core_auth_copy
-- ----------------------------
INSERT INTO `core_auth_copy` VALUES ('1', '系统配置', 'plat.sys.suUser', 'fa-gears', '管理员配置', 'suUser', 'fa-user-plus', '1', '3');
INSERT INTO `core_auth_copy` VALUES ('2', '系统配置', 'plat.sys.job', 'fa-gears', '操作日志', 'job', 'fa-users', '1', '1');
INSERT INTO `core_auth_copy` VALUES ('3', '系统配置', 'plat.sys.auth', 'fa-gears', 'VIP到期提醒', 'auth', 'fa-users', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('4', '系统配置', 'plat.sys.mod', 'fa-gears', '模块管理', 'mods', 'fa-gears', '1', '5');
INSERT INTO `core_auth_copy` VALUES ('6', '未使用', 'plat.store.sale.line', 'fa-shopping-cart', '线路-销售', 'lists', 'fa-shopping-cart', '0', '2');
INSERT INTO `core_auth_copy` VALUES ('7', '未使用', 'plat.store', 'fa-shopping-cart', '子门市全部订单', 'leader', 'fa-shield', '0', '666');
INSERT INTO `core_auth_copy` VALUES ('8', '未使用', 'plat.store.order', 'fa-shopping-cart', '线路-订单', 'index', 'fa-shopping-cart', '0', '3');
INSERT INTO `core_auth_copy` VALUES ('10', '未使用', 'plat.store.manager', 'fa-shopping-cart', '门市管理', 'index', 'fa-map', '0', '1');
INSERT INTO `core_auth_copy` VALUES ('11', '未使用', 'plat.server.handle', 'fa-shield', '投诉处理', 'handle', 'fa-warning', '0', '7');
INSERT INTO `core_auth_copy` VALUES ('12', '未使用', 'plat.server.oversee', 'fa-shield', '质量跟踪', 'oversee', 'fa-shield', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('13', '求购记录', 'plat.supplier.manager', 'fa-user-secret', '查看记录', 'index', 'fa-map', '1', '1');
INSERT INTO `core_auth_copy` VALUES ('14', '互动圈子', 'plat.supplier.lines', 'fa-user-secret', '评论管理', 'lists', 'fa-cog', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('15', '人工收费', 'plat.supplier.order', 'fa-user-secret', '收费记录', 'index', 'fa-shopping-cart', '1', '3');
INSERT INTO `core_auth_copy` VALUES ('16', '统计分析-厂商统计', 'plat.supplier', 'fa-user-secret', '活跃度统计', 'auth', 'fa-shield', '1', '666');
INSERT INTO `core_auth_copy` VALUES ('18', '统计分析-厂商统计', 'plat.supplier.lines', 'fa-user-secret', '访问统计', 'show', 'fa-shield', '1', '666');
INSERT INTO `core_auth_copy` VALUES ('20', '未使用', 'plat.report.order', 'fa-bar-chart', '线路-订单台帐', 'index', 'fa-bar-chart', '0', '1');
INSERT INTO `core_auth_copy` VALUES ('21', '活动营销', 'plat.supplier.lines', 'fa-user-secret', 'PC友情链接配置', 'leader', 'fa-shield', '1', '666');
INSERT INTO `core_auth_copy` VALUES ('22', '未使用', 'plat.base.dest', 'fa-cog', '线路-目的地', 'index', 'fa-cog', '0', '3');
INSERT INTO `core_auth_copy` VALUES ('23', '未使用', 'plat.base.dest', null, '线路-目的地类型', 'type', 'fa-shield', '0', '2');
INSERT INTO `core_auth_copy` VALUES ('24', '未使用', 'plat.base.city', 'fa-cog', '线路-出发城市', 'index', 'fa-cog', '0', '4');
INSERT INTO `core_auth_copy` VALUES ('25', '未使用', 'plat.base.subject', 'fa-cog', '线路-旅游专题', 'index', 'fa-cog', '0', '0');
INSERT INTO `core_auth_copy` VALUES ('26', '厂商管理', 'plat.product.line', 'fa-edit', '基础信息', 'lists', 'fa-edit', '1', '1');
INSERT INTO `core_auth_copy` VALUES ('27', '厂商管理', 'plat.product.line', 'fa-edit', '邀请记录', 'show', 'fa-edit', '1', '555');
INSERT INTO `core_auth_copy` VALUES ('28', '厂商管理', 'plat.product.line', 'fa-edit', '圈子记录', 'price', 'fa-edit', '1', '555');
INSERT INTO `core_auth_copy` VALUES ('29', '未使用', 'plat.store', 'fa-shopping-cart', '本门市全部订单', 'leader', 'fa-shield', '0', '666');
INSERT INTO `core_auth_copy` VALUES ('30', '业务员工资配置', 'plat.audit.manager', 'fa-money', '工资记录', 'mIndex', 'fa-money', '1', '1');
INSERT INTO `core_auth_copy` VALUES ('31', '业务员工资配置', 'plat.audit.manager', 'fa-money', '财务统计', 'gmIndex', 'fa-money', '1', '3');
INSERT INTO `core_auth_copy` VALUES ('32', '业务员工资配置', 'plat.audit.manager', 'fa-money', '财务流水', 'fdIndex', 'fa-money', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('33', '未使用', 'plat.audit.manager', 'fa-money', '线-会计结款', 'acctIndex', 'fa-money', '0', '4');
INSERT INTO `core_auth_copy` VALUES ('34', '未使用', 'plat.audit.report', 'fa-bar-chart', '线路-结算报表', 'index', 'fa-bar-chart', '0', '3');
INSERT INTO `core_auth_copy` VALUES ('38', '统计分析-厂商统计', 'plat.audit.report', 'fa-user-secret', '产品统计', 'suIndex', 'fa-money', '1', '7');
INSERT INTO `core_auth_copy` VALUES ('39', '系统配置', 'plat.audit.manager', 'fa-user-secret', '产品分类配置', 'suList', 'fa-bar-chart', '1', '6');
INSERT INTO `core_auth_copy` VALUES ('40', '系统配置', 'plat.sys.notice', 'fa-gears', '公告管理', 'index', 'fa-newspaper-o', '1', '4');
INSERT INTO `core_auth_copy` VALUES ('41', '未使用', 'plat.report.monthReport', 'fa-bar-chart', '线路-月统计报表', 'monthReport', 'fa-bar-chart', '0', '2');
INSERT INTO `core_auth_copy` VALUES ('42', '系统配置', 'plat.report.monthReport', 'fa-bar-chart', '经销商及车系配置', 'monthReport', 'fa-bar-chart', '1', '5');
INSERT INTO `core_auth_copy` VALUES ('43', '活动营销', 'plat.report.monthReport', 'fa-bar-chart', 'PC推荐经销商', 'monthReportChild', 'fa-bar-chart', '1', '666');
INSERT INTO `core_auth_copy` VALUES ('47', '未使用', 'plat.audit.manager', 'fa-bar-chart', '线路-供应商对账单', 'suList', 'fa-bar-chart', '0', '4');
INSERT INTO `core_auth_copy` VALUES ('48', '未使用', 'plat.contract.manager', 'fa-file-text-o', '合同创建', 'createIndex', 'fa-file-text-o', '0', '3');
INSERT INTO `core_auth_copy` VALUES ('49', '未使用', 'plat.contract.manager', 'fa-file-text-o', '合同审核', 'auditIndex', 'fa-file-text-o', '0', '4');
INSERT INTO `core_auth_copy` VALUES ('50', '未使用', 'plat.report.commission', 'fa-bar-chart', '线路-代理手续费', 'commission', 'fa-bar-chart', '0', '5');
INSERT INTO `core_auth_copy` VALUES ('51', '未使用', 'plat.contract.invoice', 'fa-file-text-o', '发票审核', 'auditIndex', 'fa-file-text-o', '0', '2');
INSERT INTO `core_auth_copy` VALUES ('52', '未使用', 'plat.contract.invoice', 'fa-file-text-o', '发票开据', 'billingIndex', 'fa-file-text-o', '0', '1');
INSERT INTO `core_auth_copy` VALUES ('53', '未使用', 'plat.report.order', 'fa-legal', '线路-财务退款', 'tuiList', 'fa-legal', '0', '2');
INSERT INTO `core_auth_copy` VALUES ('54', '推送消息', 'plat.supplier.notice', 'fa-user-secret', '推送记录', 'noticePage', 'fa-envelope', '1', '4');
INSERT INTO `core_auth_copy` VALUES ('55', '未使用', 'plat.report.detail', 'fa-bar-chart', '线路-交易明细', 'index', 'fa-bar-chart', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('56', '厂商管理', 'plat.wechat.banner', 'fa-gears', '产品信息', 'banner', 'fa-cog', '1', '1');
INSERT INTO `core_auth_copy` VALUES ('57', '厂商管理', 'plat.wechat.tags', 'fa-gears', '刷新点记录', 'tagsIndex', 'fa-tag', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('58', '未使用', 'plat.custom.demand', 'fa-bar-chart', '定制游-需求', 'demandIndex', 'fa-vimeo', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('59', '厂商管理', 'plat.custom.demand', 'fa-user-secret', '关联业务员', 'suDemandIndex', 'fa-vimeo', '1', '8');
INSERT INTO `core_auth_copy` VALUES ('60', '厂商管理', 'plat.custom.line', 'fa-edit', '来访记录(经销商)', 'customAudit', 'fa-edit', '1', '3');
INSERT INTO `core_auth_copy` VALUES ('61', '厂商管理', 'plat.sys.coupon', 'fa-gears', '访问记录', 'couponIndex', 'fa-map', '1', '3');
INSERT INTO `core_auth_copy` VALUES ('62', '未使用', 'plat.custom.order', 'fa-shopping-cart', '定制游-订单', 'stOrderPage', 'fa-vimeo', '0', '7');
INSERT INTO `core_auth_copy` VALUES ('63', '业务员管理', 'plat.custom.order', 'fa-bar-chart', '基础信息', 'suOrderPage', 'fa-vimeo', '1', '9');
INSERT INTO `core_auth_copy` VALUES ('64', '未使用', 'plat.custom.order', 'fa-legal', '定制游-财务退款', 'tuiPage', 'fa-legal', '0', '4');
INSERT INTO `core_auth_copy` VALUES ('65', '未使用', 'plat.audit.audit', 'fa-money', '经理审核', 'mIndex', 'fa-money', '0', '5');
INSERT INTO `core_auth_copy` VALUES ('66', '未使用', 'plat.audit.audit', 'fa-money', '总经理审核', 'gmIndex', 'fa-money', '0', '7');
INSERT INTO `core_auth_copy` VALUES ('67', '未使用', 'plat.audit.audit', 'fa-money', '财务审核', 'fdIndex', 'fa-money', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('68', '未使用', 'plat.audit.audit', 'fa-money', '会计结款', 'acctIndex', 'fa-money', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('69', 'banner管理', 'plat.audit.reportforms', 'fa-user-secret', '底部banner', 'suIndex', 'fa-bar-chart', '1', '15');
INSERT INTO `core_auth_copy` VALUES ('70', '未使用', 'plat.audit.reportforms', 'fa-user-secret', '供应商-结算报表', 'index', 'fa-bar-chart', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('71', '未使用', 'plat.store.visa.order', 'fa-shopping-cart', '签证-订单', 'index', 'fa-cc-visa', '0', '9');
INSERT INTO `core_auth_copy` VALUES ('72', '未使用', 'plat.base.visaDest', 'fa-cog', '签证-目的地', 'index', 'fa-cog', '0', '5');
INSERT INTO `core_auth_copy` VALUES ('73', '未使用', 'plat.base.visaPersonType', 'fa-cog', '签证-人群类型', 'index', 'fa-cog', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('74', '未使用', 'plat.base.visaPapersType', 'fa-cog', '签证-类型', 'index', 'fa-cog', '0', '7');
INSERT INTO `core_auth_copy` VALUES ('75', '未使用', 'plat.base.visaMaterialTemplate', 'fa-cog', '签证-材料模板', 'index', 'fa-cog', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('76', '未使用', 'plat.store.sale.visa', 'fa-bar-chart', '签证-办理', 'listPage', 'fa-cc-visa', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('78', '未使用', 'plat.report.visa.order', 'fa-bar-chart', '签证-订单台帐', 'index', 'fa-bar-chart', '0', '9');
INSERT INTO `core_auth_copy` VALUES ('79', '业务员管理', 'plat.custom.notice', 'fa-user-secret', '关联厂商', 'noticePage', 'fa-vimeo', '1', '10');
INSERT INTO `core_auth_copy` VALUES ('80', '文章管理', 'plat.supplier.visaProduct', 'fa-user-secret', '查看文章', 'index', 'fa-cc-visa', '1', '12');
INSERT INTO `core_auth_copy` VALUES ('81', '厂商管理', 'plat.supplier.visaProduct', 'fa-edit', '求购记录', 'auditIndex', 'fa-edit', '1', '4');
INSERT INTO `core_auth_copy` VALUES ('82', '厂商管理', 'plat.custom.demand', 'fa-edit', '认证信息', 'zbDemandPage', 'fa-edit', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('83', 'banner管理', 'plat.supplier.visa.order', 'fa-cc-visa', '头部banner', 'index', 'fa-cc-visa', '1', '13');
INSERT INTO `core_auth_copy` VALUES ('84', '未使用', 'plat.custom.order', 'fa-legal', '定制游-退款审核', 'tuiAuditPage', 'fa-legal', '0', '3');
INSERT INTO `core_auth_copy` VALUES ('85', '文章管理', 'plat.wechat.attention', 'fa-gears', '添加/编辑文章', 'showDataPage', 'fa-user-secret', '1', '4');
INSERT INTO `core_auth_copy` VALUES ('86', '未使用', 'plat.report.custom', 'fa-user-secret', '定制游-订单', 'index', 'fa-bar-chart', '0', '7');
INSERT INTO `core_auth_copy` VALUES ('87', '未使用', 'plat.report.order', 'fa-legal', '线路-退款审核', 'tuiApply', 'fa-legal', '0', '1');
INSERT INTO `core_auth_copy` VALUES ('88', '未使用', 'plat.report.visa.order', 'fa-legal', '签证-退款审核', 'refundApply', 'fa-legal', '0', '5');
INSERT INTO `core_auth_copy` VALUES ('89', '未使用', 'plat.report.visa.order', 'fa-legal', '签证-财务退款', 'refundMoneyPage', 'fa-legal', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('90', '未使用', 'plat.server.baoXian', 'fa-shield', '保险查看', 'showPage', 'fa-medkit', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('91', '文章管理', 'plat.wechat.honBao', 'fa-gears', '其他', 'showListPage', 'fa-money', '1', '999');
INSERT INTO `core_auth_copy` VALUES ('92', '文章管理', 'plat.wechat.honBao', 'fa-gears', '新手上路', 'showListToPage', 'fa-money', '1', '999');
INSERT INTO `core_auth_copy` VALUES ('93', '厂商管理', 'plat.wechat.tags', 'fa-gears', 'VIP记录(经销商)', 'tagsAuth', 'fa-tag', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('94', '未使用', 'plat.report.channel', 'fa-user-secret', '渠道销售数据', 'saleIndex', 'fa-bar-chart', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('95', '未使用', 'plat.report.channel', 'fa-user-secret', '网点月度统计-端口', 'MonthPortIndex', 'fa-bar-chart', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('96', '未使用', 'plat.report.channel', 'fa-user-secret', '网点月度统计-产品', 'MonthProTypeIndex', 'fa-bar-chart', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('97', '未使用', 'plat.report.channel', 'fa-user-secret', '网点月度统计-维码', 'QRCoderIndex', 'fa-bar-chart', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('98', '统计分析-厂商统计', 'plat.supplier.visaProduct', 'fa-user-secret', '圈子统计', 'visaMaterialIndex', 'fa-cc-visa', '1', '999');
INSERT INTO `core_auth_copy` VALUES ('99', '未使用', 'plat.server.oversee', 'fa-shield', '旅游星级统计', 'star', 'fa-shield', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('100', '业务员工资配置', 'plat.supplier.oversee', 'fa-shield', '员工个人工资记录', 'starDetail', 'fa-shield', '1', '999');
INSERT INTO `core_auth_copy` VALUES ('101', '认证申请', 'plat.audit.reportforms', 'fa-user-secret', '认证记录', 'suDataPage', 'fa-bar-chart', '1', '15');
INSERT INTO `core_auth_copy` VALUES ('102', '未使用', 'plat.audit.reportforms', 'fa-user-secret', '供应商-结算对账单', 'suDataPage', 'fa-bar-chart', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('103', '未使用', 'store.create', '', '门店创建', 'store.create', '', '0', '9');
INSERT INTO `core_auth_copy` VALUES ('104', '业务员管理', 'supplier.create', 'fa-shield', '圈子记录', 'supplier.create', 'fa-shield', '1', '10');
INSERT INTO `core_auth_copy` VALUES ('105', '未使用', 'plat.report.plane', 'fa-bar-chart', '送机报表', 'planeList', 'fa-plane', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('106', '系统配置', 'plat.sys.ini', 'fa-gears', '系统配置', 'index', 'fa-gear', '1', '6');

-- ----------------------------
-- Table structure for `core_user_auth`
-- ----------------------------
DROP TABLE IF EXISTS `core_user_auth`;
CREATE TABLE `core_user_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '职位-权限管理表',
  `userId` int(11) DEFAULT NULL COMMENT '用户id',
  `authId` int(11) DEFAULT NULL COMMENT '权限id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `authId` (`authId`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=18838 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of core_user_auth
-- ----------------------------
INSERT INTO `core_user_auth` VALUES ('18836', '2', '8');
INSERT INTO `core_user_auth` VALUES ('18835', '2', '7');
INSERT INTO `core_user_auth` VALUES ('18834', '2', '6');
INSERT INTO `core_user_auth` VALUES ('18833', '2', '4');
INSERT INTO `core_user_auth` VALUES ('18807', '5', '1');
INSERT INTO `core_user_auth` VALUES ('18808', '5', '2');
INSERT INTO `core_user_auth` VALUES ('18809', '6', '1');
INSERT INTO `core_user_auth` VALUES ('18810', '6', '2');
INSERT INTO `core_user_auth` VALUES ('18811', '6', '3');
INSERT INTO `core_user_auth` VALUES ('18812', '6', '4');
INSERT INTO `core_user_auth` VALUES ('18813', '6', '40');
INSERT INTO `core_user_auth` VALUES ('18814', '6', '106');
INSERT INTO `core_user_auth` VALUES ('18815', '7', '1');
INSERT INTO `core_user_auth` VALUES ('18816', '7', '2');
INSERT INTO `core_user_auth` VALUES ('18817', '7', '3');
INSERT INTO `core_user_auth` VALUES ('18818', '7', '4');
INSERT INTO `core_user_auth` VALUES ('18819', '7', '40');
INSERT INTO `core_user_auth` VALUES ('18820', '7', '106');
INSERT INTO `core_user_auth` VALUES ('18821', '8', '1');
INSERT INTO `core_user_auth` VALUES ('18822', '8', '2');
INSERT INTO `core_user_auth` VALUES ('18823', '8', '3');
INSERT INTO `core_user_auth` VALUES ('18824', '8', '4');
INSERT INTO `core_user_auth` VALUES ('18825', '8', '40');
INSERT INTO `core_user_auth` VALUES ('18826', '8', '106');
INSERT INTO `core_user_auth` VALUES ('18832', '2', '3');
INSERT INTO `core_user_auth` VALUES ('18837', '2', '17');

-- ----------------------------
-- Table structure for `firms`
-- ----------------------------
DROP TABLE IF EXISTS `firms`;
CREATE TABLE `firms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '厂商表',
  `uname` varchar(20) DEFAULT NULL COMMENT '昵称',
  `EnterpriseID` varchar(10) NOT NULL COMMENT '企业ID(唯一)',
  `phone` varchar(16) DEFAULT NULL COMMENT '手机号(企业账号)',
  `password` varchar(255) DEFAULT NULL COMMENT '企业登录密码(默认7777777)',
  `type` tinyint(1) DEFAULT NULL COMMENT '1.经销商  2.修理厂',
  `classification` tinyint(1) DEFAULT NULL COMMENT '企业分类 (和type字段相关)\r\n经销商：1.轿车商家 2.货车商家 3.物流货运\r\n汽修厂：4.修理厂    5.快修保养 6.美容店',
  `business` varchar(255) DEFAULT NULL COMMENT '经营范围(当type=1，经销商使用)\r\n和经销商及车系表car_group关联\r\n1个经销商自己最多可以选择3个二级分类经营范围，但后台可配置多个，前端进行动态布局\r\n车系是指同一个经销商下是有4级分类的，只是在用户界面只提供到2级的筛选，该2级就是经营范围，最多可以选3个，但经营范围下的3、4级就不限数量',
  `is_showfactry` tinyint(1) DEFAULT NULL COMMENT '汽修厂轨迹权限  1有 2无',
  `scale` varchar(4) DEFAULT NULL COMMENT '企业规模(大，中，小)(当type=2，修理厂使用)',
  `companyname` varchar(60) DEFAULT NULL COMMENT '企业名称',
  `province` varchar(20) DEFAULT NULL COMMENT '省',
  `city` varchar(20) DEFAULT NULL COMMENT '区',
  `district` varchar(20) DEFAULT NULL COMMENT '区',
  `address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `longitude` float(9,9) DEFAULT NULL COMMENT '经度',
  `latitude` float(9,9) DEFAULT NULL COMMENT '纬度',
  `face_pic` varchar(255) DEFAULT NULL COMMENT '封面  建议200*200px',
  `major` varchar(255) DEFAULT NULL COMMENT '主营',
  `linkMan` varchar(30) DEFAULT NULL COMMENT '联系人',
  `linkPhone` varchar(255) DEFAULT NULL COMMENT '联系手机号(可填多个)',
  `linkTel` varchar(255) DEFAULT NULL COMMENT '座机(可填多个)',
  `qq` varchar(255) DEFAULT NULL COMMENT 'QQ号(可填多个)',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `last_time` datetime DEFAULT NULL COMMENT '最近一次登陆时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态 1.正常 2.禁用',
  `salesman_ids` varchar(255) DEFAULT NULL COMMENT '关联业务员id(sales_user表)，例如:  ,1,2,31,',
  `wechat_pic` varchar(255) DEFAULT NULL COMMENT '微信二维码',
  `info` text COMMENT '企业介绍',
  `is_vip` tinyint(1) DEFAULT NULL COMMENT '是否vip   1是  2否 (汽修厂没有VIP会员)',
  `is_check` tinyint(1) DEFAULT NULL COMMENT '是否认证  1是 2否',
  `refresh_point` int(11) DEFAULT NULL COMMENT '刷新点',
  `is_sales` tinyint(1) DEFAULT '0' COMMENT '是否是推荐经销商 1是 0否',
  `refresh_time` datetime DEFAULT NULL COMMENT '刷新点时间',
  `invite_code` varchar(30) DEFAULT NULL COMMENT '邀请码(唯一)',
  `vip_time` datetime DEFAULT NULL COMMENT 'vip到期时间',
  `factory_grades` tinyint(1) DEFAULT NULL COMMENT '修理厂等级 （1,2,3）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `EnterpriseID` (`EnterpriseID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='厂商表';

-- ----------------------------
-- Records of firms
-- ----------------------------
INSERT INTO `firms` VALUES ('1', null, '464646', '135513', '2342342342', '1', '1', '1', '1', null, '青羊汽贸', '四川', '成都', '青羊区', 'asfsdgfd', null, null, '/data/upload/test_img/QQ截图20170516112021.jpg', '玛莎拉蒂', '战神', '135513', '234', '356346', '2017-05-12 10:48:13', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, '20', '1', '2017-05-16 11:15:35', null, '2017-05-13 11:03:59', null);
INSERT INTO `firms` VALUES ('2', null, '12314324', '13013001300', null, '1', '2', '2', '2', null, '成华汽贸', '四川', '成都', '成华区', 'gdf', null, null, '/data/upload/test_img/QQ截图20170516112021.jpg', '主营业务是指企业为完成其经营目标而从事的日常活动中的主要活动,可根据企业营业执照上规定的主要业务范围确定,例如工业、商品流通企业的主营业务是.', '粉丝', '2532', '53', '6456', '2017-05-12 10:48:15', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '1', '2017-05-14 11:15:39', null, '2017-05-18 11:04:02', null);
INSERT INTO `firms` VALUES ('5', null, '12312', '464', null, '1', '3', '3', '1', null, '超音速汽贸', '四川', null, null, 'gsdfgs', null, null, null, '大众', '大华股份', '523', '4546', '45', '2017-05-12 10:48:17', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '0', null, null, '2017-05-27 11:04:06', null);
INSERT INTO `firms` VALUES ('6', null, '234234', '23424', null, '2', '1', '4', '2', '大', '武侯汽修', '四川', '成都', '武侯区', 'g', null, null, null, '主营业务是指企业为完成其经营目标而从事的日常活动中的主要活动,可根据企业营业执照上规定的主要业务范围确定,例如工业、商品流通企业的主营业务是.', '三哥vf', '42', '4564', '363', '2017-05-12 10:48:20', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '1', '2017-05-16 11:15:51', null, '2017-05-20 11:04:10', null);
INSERT INTO `firms` VALUES ('7', null, '234242', '24234534', null, '2', '2', '1', '1', '中', '特快汽修', '四川', '成都', null, 'dsfgsg', null, null, '/data/upload/test_img/QQ截图20170516112453.jpg', '起亚,北京现代', '荣达', '3423', '5353', '346', '2017-05-12 10:48:23', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '0', null, null, '2017-05-18 11:04:13', null);
INSERT INTO `firms` VALUES ('8', null, '34252', '65765', null, '2', '3', '2', '2', '小', '金堂汽修', '四川', '成都', '金堂县', 'sfg', null, null, null, '奔驰,宝马,保时捷,劳斯莱斯,起亚,北京现代', '津树多枫橘', '4234', '3453', '345', '2017-05-12 10:48:25', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '0', null, null, '2017-05-19 13:51:19', null);

-- ----------------------------
-- Table structure for `firms_banner`
-- ----------------------------
DROP TABLE IF EXISTS `firms_banner`;
CREATE TABLE `firms_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '经销商banner',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `banner_url` varchar(255) DEFAULT NULL COMMENT '建议750*310px',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='经销商banner';

-- ----------------------------
-- Records of firms_banner
-- ----------------------------

-- ----------------------------
-- Table structure for `firms_call_log`
-- ----------------------------
DROP TABLE IF EXISTS `firms_call_log`;
CREATE TABLE `firms_call_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '拨打记录',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `to_firms_id` int(11) DEFAULT NULL COMMENT '拨打的厂商id',
  `create_time` datetime DEFAULT NULL,
  `call_type` tinyint(1) DEFAULT NULL COMMENT '1电话  2qq',
  `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示，1显示 2不显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='厂商拨打记录';

-- ----------------------------
-- Records of firms_call_log
-- ----------------------------

-- ----------------------------
-- Table structure for `firms_card`
-- ----------------------------
DROP TABLE IF EXISTS `firms_card`;
CREATE TABLE `firms_card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '企业名片',
  `firms_type` tinyint(1) DEFAULT NULL COMMENT '厂商类型，1经销商  2修理厂',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `firms_name` varchar(255) DEFAULT NULL COMMENT '企业名称',
  `firms_linkMan` varchar(60) DEFAULT NULL COMMENT '联系人',
  `firms_phone` varchar(255) DEFAULT NULL COMMENT '手机号（多个用 , 分开）',
  `firms_tel` varchar(255) DEFAULT NULL COMMENT '电话号（多个用 , 分开）',
  `firms_QQ` varchar(255) DEFAULT NULL COMMENT 'QQ（多个用 , 分开）',
  `firms_address` varchar(255) DEFAULT NULL COMMENT '地址',
  `firms_QR` varchar(255) DEFAULT NULL COMMENT '二维码',
  `create_time` datetime DEFAULT NULL,
  `template_type` int(11) DEFAULT NULL,
  `main_icon_1` varchar(255) DEFAULT NULL COMMENT '主营图标1 （经销商）',
  `main_icon_2` varchar(255) DEFAULT NULL COMMENT '主营图标2（经销商）',
  `main_icon_3` varchar(255) DEFAULT NULL COMMENT '主营图标3（经销商）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='企业名片';

-- ----------------------------
-- Records of firms_card
-- ----------------------------

-- ----------------------------
-- Table structure for `firms_check`
-- ----------------------------
DROP TABLE IF EXISTS `firms_check`;
CREATE TABLE `firms_check` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '企业认证申请记录表',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `firmsName` varchar(60) DEFAULT NULL COMMENT '公司全称',
  `firmsMan` varchar(255) DEFAULT NULL COMMENT '联系人',
  `firmsTel` varchar(16) DEFAULT NULL COMMENT '手机号码',
  `province` varchar(20) DEFAULT NULL COMMENT '省',
  `city` varchar(20) DEFAULT NULL COMMENT '市',
  `district` varchar(20) DEFAULT NULL COMMENT '区',
  `address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `licence_pic` varchar(255) DEFAULT NULL COMMENT '营业执照',
  `taxes_pic` varchar(255) DEFAULT NULL COMMENT '纳税认证',
  `field_pic` varchar(255) DEFAULT NULL COMMENT '实地认证',
  `brand_pic` varchar(255) DEFAULT NULL COMMENT '商标认证',
  `agents_pic` varchar(255) DEFAULT NULL COMMENT '产品代理认证',
  `create_time` datetime DEFAULT NULL COMMENT '申请时间',
  `update_time` datetime DEFAULT NULL,
  `audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '审核状态，1待审 2通过 3拒绝',
  `reason` varchar(255) DEFAULT NULL COMMENT '审核被拒绝的原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='企业认证申请记录表';

-- ----------------------------
-- Records of firms_check
-- ----------------------------

-- ----------------------------
-- Table structure for `firms_sales_user`
-- ----------------------------
DROP TABLE IF EXISTS `firms_sales_user`;
CREATE TABLE `firms_sales_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '厂商业务员关联表',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `sales_user_di` int(11) DEFAULT NULL COMMENT '业务员id',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='厂商业务员关联表';

-- ----------------------------
-- Records of firms_sales_user
-- ----------------------------
INSERT INTO `firms_sales_user` VALUES ('1', '1', '1', null);
INSERT INTO `firms_sales_user` VALUES ('2', '1', '1', null);
INSERT INTO `firms_sales_user` VALUES ('3', '2', '2', null);
INSERT INTO `firms_sales_user` VALUES ('4', '3', '3', null);
INSERT INTO `firms_sales_user` VALUES ('5', '1', '2', null);

-- ----------------------------
-- Table structure for `firms_visit_log`
-- ----------------------------
DROP TABLE IF EXISTS `firms_visit_log`;
CREATE TABLE `firms_visit_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '厂商访问记录',
  `firms_id` int(11) DEFAULT NULL COMMENT '本厂商id (没有为0)',
  `to_firms_id` int(11) DEFAULT NULL COMMENT '访问的厂商id',
  `create_time` datetime DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示，1显示 2不显示',
  `visit_type` tinyint(1) DEFAULT NULL COMMENT '访问终端（1PC web端  2移动端）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='厂商访问记录';

-- ----------------------------
-- Records of firms_visit_log
-- ----------------------------

-- ----------------------------
-- Table structure for `friendly_link`
-- ----------------------------
DROP TABLE IF EXISTS `friendly_link`;
CREATE TABLE `friendly_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'pc友情链接',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `vname` varchar(20) DEFAULT NULL COMMENT '名称',
  `vurl` varchar(255) DEFAULT NULL COMMENT '友情链接',
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='pc友情链接';

-- ----------------------------
-- Records of friendly_link
-- ----------------------------
INSERT INTO `friendly_link` VALUES ('1', '1', '友情链接11', 'http://mengzian.top', '1', '2017-05-17 13:49:55');
INSERT INTO `friendly_link` VALUES ('2', '5', '友情链接22', 'http://mengzian.top', '1', '2017-05-17 13:50:19');
INSERT INTO `friendly_link` VALUES ('3', '2', '友情链接33', 'http://mengzian.top', '1', '2017-05-17 13:50:23');
INSERT INTO `friendly_link` VALUES ('4', '3', '友情链接44', 'http://mengzian.top', '1', '2017-05-17 13:50:26');
INSERT INTO `friendly_link` VALUES ('5', '4', '友情链接55', 'http://mengzian.top', '1', '2017-05-17 13:50:28');
INSERT INTO `friendly_link` VALUES ('6', '2', '百度', 'http://baidu.com', '1', '2017-05-17 13:58:55');

-- ----------------------------
-- Table structure for `invite_log`
-- ----------------------------
DROP TABLE IF EXISTS `invite_log`;
CREATE TABLE `invite_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '邀请记录',
  `type` int(11) DEFAULT NULL COMMENT '用户类型 1企业厂商，2业务员',
  `fu_id` int(11) DEFAULT NULL COMMENT '邀请人id：\r\ntype=1:关联厂商表;\r\ntype=2:关联业务员表',
  `firms_id` int(11) DEFAULT NULL COMMENT '被邀请厂商id',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='邀请记录';

-- ----------------------------
-- Records of invite_log
-- ----------------------------

-- ----------------------------
-- Table structure for `notice`
-- ----------------------------
DROP TABLE IF EXISTS `notice`;
CREATE TABLE `notice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '推送消息',
  `msg` varchar(255) DEFAULT NULL COMMENT '请简要说明，限制100字',
  `start_time` datetime DEFAULT NULL COMMENT '发布时间',
  `create_time` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='推送消息';

-- ----------------------------
-- Records of notice
-- ----------------------------

-- ----------------------------
-- Table structure for `pay_history`
-- ----------------------------
DROP TABLE IF EXISTS `pay_history`;
CREATE TABLE `pay_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '收费记录表',
  `type` tinyint(1) DEFAULT NULL COMMENT '充值类型  1充值VIP  2充值刷新点 3刷新点消费 4获取新点消费',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `money` decimal(10,2) DEFAULT NULL COMMENT '充值金额（元）',
  `refresh_point` int(11) DEFAULT NULL COMMENT '刷新点数  +100或-100',
  `info` varchar(255) DEFAULT NULL COMMENT '详情',
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id 没有为0',
  `payway` tinyint(4) DEFAULT NULL COMMENT '支付方式 1微信支付  2支付宝支付 3人工收费（当type=3刷新点消费 4获取新点消费 为0）',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态 1成功 2失败',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收费记录表';

-- ----------------------------
-- Records of pay_history
-- ----------------------------

-- ----------------------------
-- Table structure for `product_category`
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品分类表',
  `pid` int(11) DEFAULT NULL COMMENT '父级ID',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `name` varchar(60) DEFAULT NULL COMMENT '分级名称',
  `level` tinyint(1) DEFAULT NULL COMMENT '层级',
  `img` varchar(255) DEFAULT NULL COMMENT '二级分类需要图片',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品分类表';

-- ----------------------------
-- Records of product_category
-- ----------------------------

-- ----------------------------
-- Table structure for `product_list`
-- ----------------------------
DROP TABLE IF EXISTS `product_list`;
CREATE TABLE `product_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品表',
  `proId` varchar(20) DEFAULT NULL COMMENT ' 产品ID(唯一)',
  `proName` varchar(60) DEFAULT NULL COMMENT '产品名称',
  `pro_type` varchar(10) DEFAULT NULL COMMENT '产品类别(新品促销，库存清仓)',
  `pro_cate_1` int(11) DEFAULT NULL COMMENT '关联product_category的一级类别',
  `pro_cate_2` int(11) DEFAULT NULL COMMENT '关联product_category的二级类别',
  `pro_price` decimal(10,2) DEFAULT NULL COMMENT '价格',
  `pro_refresh` int(11) DEFAULT NULL COMMENT '今日刷新数',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `pro_status` tinyint(1) DEFAULT NULL COMMENT '状态  1上架中 2未上架',
  `pro_no` varchar(30) DEFAULT NULL COMMENT '厂商编码',
  `pro_brand` varchar(255) DEFAULT NULL COMMENT '产品品牌',
  `pro_area` varchar(255) DEFAULT NULL COMMENT '产品产地',
  `pro_weight` varchar(255) DEFAULT NULL COMMENT '产品毛重',
  `pro_spec` text COMMENT '产品规格',
  `pro_memo` text COMMENT '备注说明',
  `pro_text` longtext COMMENT '正文',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `pro_info` varchar(255) DEFAULT NULL COMMENT '产品信息：(原厂件，副厂件，高仿件，品牌件)',
  `refresh_time` datetime DEFAULT NULL COMMENT '刷新点时间',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除 1是 0否',
  `car_group` varchar(0) DEFAULT NULL COMMENT '车系 如：欧系/宝马/进口宝马/X9、欧系/奥迪/进口奥迪/A6',
  PRIMARY KEY (`id`),
  UNIQUE KEY `proId` (`proId`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品表';

-- ----------------------------
-- Records of product_list
-- ----------------------------

-- ----------------------------
-- Table structure for `sales_call_log`
-- ----------------------------
DROP TABLE IF EXISTS `sales_call_log`;
CREATE TABLE `sales_call_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '业务员拨打记录',
  `sales_user_id` int(11) DEFAULT NULL COMMENT '业务员id',
  `firms_is` int(11) DEFAULT NULL COMMENT '厂商id',
  `create_time` datetime DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示，1显示 2不显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='业务员拨打记录';

-- ----------------------------
-- Records of sales_call_log
-- ----------------------------

-- ----------------------------
-- Table structure for `sales_user`
-- ----------------------------
DROP TABLE IF EXISTS `sales_user`;
CREATE TABLE `sales_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '业务员',
  `uId` varchar(10) DEFAULT NULL COMMENT '业务员ID',
  `uname` varchar(30) DEFAULT NULL COMMENT '昵称',
  `area` varchar(255) DEFAULT NULL COMMENT '管辖区域',
  `password` varchar(255) DEFAULT NULL COMMENT '密码 7777777',
  `phone` varchar(15) DEFAULT NULL COMMENT '联系电话',
  `realname` varchar(255) DEFAULT NULL COMMENT '姓名',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `last_time` datetime DEFAULT NULL COMMENT '最近登录时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态 1正常 2禁止',
  `facepic` varchar(255) DEFAULT NULL COMMENT '头像',
  `base_wage` decimal(10,2) DEFAULT NULL COMMENT '基本工资',
  `subsidies` decimal(10,2) DEFAULT NULL COMMENT '补贴',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uId` (`uId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='业务员';

-- ----------------------------
-- Records of sales_user
-- ----------------------------
INSERT INTO `sales_user` VALUES ('1', '46464', '张学友', '黑龙江/大庆/大同区', 'f1882db0904583fba94712b0a0a5db4e', '13551395944', '46', '2017-05-12 16:46:03', '2017-05-15 10:19:29', '2017-05-12 16:45:54', '1', '/data/upload/20170515/29931c2bf6ecd93cb0d27f3cc78936fb.jpg', '46.00', '4.00');
INSERT INTO `sales_user` VALUES ('2', '50301895', '萨芬公开', '黑龙江/伊春/乌马河区', 'f1882db0904583fba94712b0a0a5db4e', '', '张泽飞', '2017-05-12 17:58:27', '2017-05-15 10:16:04', null, '1', '/data/upload/20170515/4d1b2d0387339be49eee539abb1aeea5.jpg', null, null);
INSERT INTO `sales_user` VALUES ('3', '66825572', '撒发生', '四川', 'f1882db0904583fba94712b0a0a5db4e', '242342', '归属地', '2017-05-12 18:03:21', '2017-05-12 18:03:21', null, '2', null, null, null);

-- ----------------------------
-- Table structure for `sales_wage_log`
-- ----------------------------
DROP TABLE IF EXISTS `sales_wage_log`;
CREATE TABLE `sales_wage_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '业务员工资记录',
  `sales_user_id` int(11) DEFAULT NULL COMMENT '业务员id',
  `year_month` date DEFAULT NULL COMMENT '年月份',
  `base_wage` decimal(10,2) DEFAULT NULL COMMENT '基本工资',
  `subsidies` decimal(10,2) DEFAULT NULL COMMENT '补助',
  `new_firms_money` decimal(10,2) DEFAULT NULL COMMENT '新增关联提成 \r\n明细从firms_sales_user表中获取',
  `factory_use_money` decimal(10,2) DEFAULT NULL COMMENT '汽修厂厂使用频率提成  \r\n针对汽修厂使用频率的提成，是统计关联后30天内，该月达到对应使用等级的关联汽修厂的数量提成，等级同验证厂商中的汽修厂等级定义，如3月1日，关联修理厂满30天的，达到等级1的汽修厂有200个，达到等级2的有60个，达到等级3的有40个，乘以对应的提成单价，已提成的修理厂就不再计入下一个月的提成；\r\n到1号时，没有关联到30天的，这部分汽修厂的提成计入到下一个月的提成',
  `factory_call_money` decimal(10,2) DEFAULT NULL COMMENT '针对汽修厂关联提成，是按照该月来电数（移动端拨打电话+QQ）乘以固定系数算出的',
  `firms_pay_money` decimal(10,2) DEFAULT NULL COMMENT '关联厂商充值提成，是按照该月经销商和汽修厂充值金额乘以固定系数算出的，充值包括了买VIP、充值点数和后台人工增加的充值金额（人工开通VIP或增加刷新点，需增加财务数据）',
  `total` decimal(10,2) DEFAULT NULL COMMENT '合计',
  `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示给业务员  1是 2否\r\n\r\n每月1号统计上1个月的提成和工资；每个月工资由后台审核/确认后才会更新，未更新统计为“0”',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='业务员工资记录';

-- ----------------------------
-- Records of sales_wage_log
-- ----------------------------

-- ----------------------------
-- Table structure for `su_action_log`
-- ----------------------------
DROP TABLE IF EXISTS `su_action_log`;
CREATE TABLE `su_action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户操作表',
  `userId` int(11) DEFAULT NULL COMMENT '操作用户id',
  `user` varchar(16) DEFAULT NULL COMMENT '操作用户',
  `code` varchar(20) DEFAULT NULL COMMENT '操作用户帐号',
  `action` varchar(128) DEFAULT NULL COMMENT '操作',
  `result` char(2) DEFAULT NULL COMMENT '结果',
  `time` datetime DEFAULT NULL COMMENT '操作时间',
  `ip` char(15) DEFAULT NULL COMMENT '操作ip',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of su_action_log
-- ----------------------------
INSERT INTO `su_action_log` VALUES ('89', '1', '超级管理员', 'admin', '停用管理员', '成功', '2017-05-03 10:59:54', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('90', '1', '超级管理员', 'admin', '启用管理员', '成功', '2017-05-04 10:59:57', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('91', '1', '超级管理员', 'admin', '启用管理员', '成功', '2017-05-06 11:01:00', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('92', '1', '超级管理员', 'admin', '重置密码', '成功', '2017-05-08 11:01:04', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('88', '1', '超级管理员', 'admin', '登录系统', '成功', '2017-05-11 10:57:31', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('93', '1', '超级管理员', 'admin', '修改管理员名称', '成功', '2017-05-11 11:03:16', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('94', '1', '超级管理员', 'admin', '修改管理员名称', '成功', '2017-05-11 11:34:44', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('95', '1', '超级管理员', 'admin', '编辑权限', '成功', '2017-05-11 14:37:30', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('96', '1', '超级管理员', 'admin', '编辑权限', '成功', '2017-05-12 10:32:41', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('97', '1', '超级管理员', 'admin', '修改管理员名称', '成功', '2017-05-12 10:32:49', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('98', '1', '超级管理员', 'admin', '停用管理员', '成功', '2017-05-12 10:32:54', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('99', '1', '超级管理员', 'admin', '重置密码', '成功', '2017-05-12 10:33:27', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('100', '1', '超级管理员', 'admin', '启用管理员', '成功', '2017-05-12 14:56:25', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('101', '1', '超级管理员', 'admin', '修改管理员名称', '成功', '2017-05-12 14:56:34', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('102', '1', '超级管理员', 'admin', '停用管理员', '成功', '2017-05-12 14:56:41', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('103', '1', '超级管理员', 'admin', '停用业务员', '成功', '2017-05-12 17:06:40', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('104', '1', '超级管理员', 'admin', '启用业务员', '成功', '2017-05-12 17:07:08', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('105', '1', '超级管理员', 'admin', '重置业务员密码', '成功', '2017-05-12 17:10:09', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('106', '1', '超级管理员', 'admin', '添加业务员', '成功', '2017-05-12 17:58:27', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('107', '1', '超级管理员', 'admin', '添加业务员', '成功', '2017-05-12 18:03:21', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('108', '1', '超级管理员', 'admin', '启用业务员', '成功', '2017-05-12 18:12:04', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('109', '1', '超级管理员', 'admin', '编辑业务员', '成功', '2017-05-15 09:58:16', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('110', '1', '超级管理员', 'admin', '编辑业务员', '成功', '2017-05-15 10:16:04', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('111', '1', '超级管理员', 'admin', '编辑业务员', '成功', '2017-05-15 10:19:29', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('112', '1', '超级管理员', 'admin', '停用业务员', '成功', '2017-05-15 11:33:28', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('113', '1', '超级管理员', 'admin', '删除圈子评论', '成功', '2017-05-15 14:51:05', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('114', '1', '超级管理员', 'admin', '登录系统', '成功', '2017-05-15 16:22:22', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('115', '1', '超级管理员', 'admin', '登录系统', '成功', '2017-05-15 16:24:32', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('116', '1', '超级管理员', 'admin', '登录系统', '成功', '2017-05-15 16:29:45', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('117', '1', '超级管理员', 'admin', '删除圈子评论', '成功', '2017-05-15 16:38:12', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('118', '1', '超级管理员', 'admin', '删除圈子评论', '成功', '2017-05-15 16:43:33', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('119', '1', '超级管理员', 'admin', '修改经销商VIP配置', '成功', '2017-05-16 17:52:20', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('120', '1', '超级管理员', 'admin', '修改刷新点配置', '成功', '2017-05-16 18:23:16', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('121', '1', '超级管理员', 'admin', '修改客服电话QQ配置', '成功', '2017-05-16 18:38:00', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('122', '1', '超级管理员', 'admin', '修改服务协议配置', '成功', '2017-05-17 09:38:34', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('123', '1', '超级管理员', 'admin', '修改分享送刷新点配置', '成功', '2017-05-17 09:58:09', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('124', '1', '超级管理员', 'admin', '修改服务协议配置', '成功', '2017-05-17 11:47:11', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('125', '1', '超级管理员', 'admin', '修改刷新点配置', '成功', '2017-05-17 11:49:13', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('126', '1', '超级管理员', 'admin', '修改刷新点配置', '成功', '2017-05-17 11:58:34', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('127', '1', '超级管理员', 'admin', '修改刷新点配置', '成功', '2017-05-17 12:02:12', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('128', '1', '超级管理员', 'admin', '修改刷新点配置', '成功', '2017-05-17 13:54:02', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('129', '1', '超级管理员', 'admin', '修改刷新点配置', '成功', '2017-05-17 13:54:16', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('130', '1', '超级管理员', 'admin', '修改业务员提成配置', '成功', '2017-05-17 14:17:04', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('131', '1', '超级管理员', 'admin', '增加车系分类', '成功', '2017-05-18 16:08:14', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('132', '1', '超级管理员', 'admin', '删除车系分类', '成功', '2017-05-18 17:22:19', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('133', '1', '超级管理员', 'admin', '修改车系分类名称', '成功', '2017-05-18 17:24:13', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('134', '1', '超级管理员', 'admin', '增加车系分类', '成功', '2017-05-18 18:04:08', '127.0.0.1');

-- ----------------------------
-- Table structure for `su_user`
-- ----------------------------
DROP TABLE IF EXISTS `su_user`;
CREATE TABLE `su_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户表',
  `code` varchar(20) DEFAULT NULL COMMENT '帐号',
  `name` varchar(20) DEFAULT NULL COMMENT '账号名称',
  `pwd` char(32) DEFAULT NULL COMMENT '密码 md5(sha1(''123456'').''sw'')',
  `status` smallint(1) DEFAULT '1' COMMENT '用户禁用与否 1 未禁用(正常) 2 已禁用',
  `session_id` char(26) DEFAULT NULL COMMENT 'sessionId',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of su_user
-- ----------------------------
INSERT INTO `su_user` VALUES ('1', 'admin', '超级管理员', 'c233ea59bbe6f12cec6d48e956ca91bb', '1', '9p8joiuvpcvuh4cfid2mea6h04', null, null);
INSERT INTO `su_user` VALUES ('2', 'admin1', '管理员', '', '1', 'rkvllk9mdudq3k4siir2hrhrn2', '2017-04-28 12:06:42', '2017-04-28 23:26:13');
INSERT INTO `su_user` VALUES ('3', 'admin2', '管理员', '', '2', null, '2017-04-28 15:47:16', '2017-04-28 23:26:02');
INSERT INTO `su_user` VALUES ('4', 'admin3', '管理员', '', '2', null, '2017-04-28 15:47:37', '2017-04-28 15:47:37');
INSERT INTO `su_user` VALUES ('5', 'admin4', '管理员', '54c7db55654c6d4adf6b1ea73a86e159', '2', null, '2017-05-10 18:01:03', '2017-05-10 18:01:03');
INSERT INTO `su_user` VALUES ('6', 'admin5', '管理员', '', '1', null, '2017-05-10 18:05:42', '2017-05-10 18:05:42');
INSERT INTO `su_user` VALUES ('7', 'admin6', '管理员', '', '1', null, '2017-05-10 18:06:26', '2017-05-10 18:06:26');
INSERT INTO `su_user` VALUES ('8', 'admin7', '管理员', '', '1', null, '2017-05-10 18:08:56', '2017-05-10 18:08:56');

-- ----------------------------
-- Table structure for `want_buy`
-- ----------------------------
DROP TABLE IF EXISTS `want_buy`;
CREATE TABLE `want_buy` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '求购表 firms表',
  `firms_id` int(11) DEFAULT NULL COMMENT '商家id',
  `car_group_id` int(11) DEFAULT NULL COMMENT '选择车系  car_group表id',
  `frame_number` varchar(255) DEFAULT NULL COMMENT '车架号  不一定会有',
  `limitation` tinyint(1) DEFAULT NULL COMMENT '时效 天数 (1，2，3) 最多3天',
  `vin_pic` varchar(255) DEFAULT NULL COMMENT 'VIN照片',
  `memo` text COMMENT '备注',
  `create_time` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '状态：1上架，2下架',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除 1是 0否',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='求购表';

-- ----------------------------
-- Records of want_buy
-- ----------------------------

-- ----------------------------
-- Table structure for `want_buy_list`
-- ----------------------------
DROP TABLE IF EXISTS `want_buy_list`;
CREATE TABLE `want_buy_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '求购表采购清单',
  `want_buy_id` int(11) DEFAULT NULL COMMENT 'want_buy表id',
  `pro_cate1` int(11) DEFAULT NULL COMMENT '产品分类 一级分类',
  `pro_cate2` int(11) DEFAULT NULL COMMENT '产品分类 二级分类',
  `amount` int(11) DEFAULT NULL COMMENT '数量',
  `list_memo` text COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='求购表采购清单';

-- ----------------------------
-- Records of want_buy_list
-- ----------------------------

-- ----------------------------
-- Table structure for `want_buy_pic`
-- ----------------------------
DROP TABLE IF EXISTS `want_buy_pic`;
CREATE TABLE `want_buy_pic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '求购相关照片',
  `want_buy_id` int(11) DEFAULT NULL COMMENT 'want_buy表id',
  `pic_url` varchar(255) DEFAULT NULL COMMENT '图片地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='求购相关照片';

-- ----------------------------
-- Records of want_buy_pic
-- ----------------------------
