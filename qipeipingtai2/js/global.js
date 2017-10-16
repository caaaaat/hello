//+-------------------------------------------------
/**
 * ajax数据提交
 * api.load('palt.def','index',{a:1,b:1},function(rdata){
 *      console.log(rdata);
 * });
 */
var api = {};
api.load = function(mod,act,data,funOk,funEr){
    var url = '/?m='+mod+'&a='+act;
    $.post(url,data,function(rdata){
        funOk(rdata);
    },'json');
};
//+--------------

//+-------------------------------------------------
/**
 * 自定义JSON本地储存
 *  var jsonData = [{b:2},{a:1}];
 *  JsonStorage.setItem('key',jsonData);
 *  JsonStorage.getItem('key');
 */
var JsonStorage = {};
//获取
JsonStorage.getItem = function(key){
    var rest = null;
    var val = window.localStorage[key];
    if(val){
        var rest = JSON.parse(val);
    }
    return rest;
};
//设置
JsonStorage.setItem = function(key,val){
    val = JSON.stringify(val);
    window.localStorage[key] = val;
};
//移除
JsonStorage.removeItem = function(key){
    window.localStorage.removeItem(key);
};
//+--------------

//+-------------------------------------------------
//定义js文件加载
function loadJs(file)
{
    var oHead       = document.getElementsByTagName('head').item(0);
    var oScript     = document.createElement("script");
    oScript.type    = "text/javascript";
    oScript.src     = file;
    oHead.appendChild(oScript);
    //console.log(file);
}
//+--------------

//+-------------------------------------------------
//判断当前浏览器是否是PC版浏览器
function isPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
        "SymbianOS", "Windows Phone",
        "iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}
//+--------------

//+-------------------------------------------------
/**
 * 检测浏览器是否是微信浏览器
 * @returns {boolean}
 */
function isWeiXin(){
    var ua = window.navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}
/**
 * 提示消息
 * @param msg
 */
function showMsg(msg){
    top.layer.msg(msg);
}

/**
 * 加载Iframe
 * @param title 弹窗标题
 * @param url
 */
function layerIframe(title,url,panas){

    var area = ['100%','100%'];//默认全屏
    if(isPC()){//判断是否是电脑端
        area = ['86%','90%']
        //area = ['1000px','90%']
    }
    if(!panas) var panas = {};
    panas.type          = panas.type ? panas.type : 2 ;
    panas.title         = panas.title ? panas.title : title ;
    panas.shadeClose    = panas.shadeClose ? panas.shadeClose :true;
    panas.shift         = panas.shift ? panas.shift : 2;
    panas.maxmin        = panas.maxmin ? panas.maxmin :false;
    panas.area          = panas.area ? panas.area : area;
    panas.content       = panas.content ? panas.content : url;
    panas.shade         = panas.shade ? panas.shade : false;
    //panas.skin          =  'layui-layer-rim';
    //加载iframe弹窗
    top.layer.open(panas);
}

/**
 * loading
 * @param msg 自定义文本
 * @param time 自定义时间 默认3s
 * 可使用 layer.closeAll(); 关闭
 */
function loading(msg,time){
    time = time?time:3000;
    //提示层
    layer.msg('<div class="sk-spinner sk-spinner-wave">' +
        '<div class="sk-rect1" style="margin-right: 3px;background-color: #fff"></div>' +
        '<div class="sk-rect2" style="margin-right: 3px;background-color: #fff"></div>' +
        '<div class="sk-rect3" style="margin-right: 3px;background-color: #fff"></div>' +
        '<div class="sk-rect4" style="margin-right: 3px;background-color: #fff"></div>' +
        '<div class="sk-rect5" style="margin-right: 3px;background-color: #fff"></div>' +
        '</div>'+msg, {
        time: time //默认设置3s关闭
    });
}

/**
 * string 扩展，检查字符中是否存在某个字符串
 * @param str
 * @returns {boolean}
 */
String.prototype.hasStrs = function (str){
    if(this.indexOf(str)<0) return false;
    else return true;
};
/**
 * 新增一个tab窗口
 * @param title
 * @param url
 */
function addTabWin(title,url)
{
    var item = $('#diy-menu');
    item.attr('href',url);
    item.find('.nav-label').html(title);
    item.click();
}

var Verification = {};
/**
 * 验证是否是邮箱
 * @param email
 * @returns {boolean}
 */
Verification.isEmail = function(email){
    if (email.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|org|net)$/)!= -1){
        return true;
    }else {
        return false;
    }
};
/**
 * 验证是否手机号
 * @param tel
 * @returns {boolean}
 */
Verification.isTel = function(tel)
{
    if(!(/^1[34578]\d{9}$/.test(tel))){
        return false;
    }else{
        return true;
    }
};
/**
 * 验证身份证
 * @param code
 * @returns {boolean}
 * @constructor
 */
Verification.IdentityCodeValid = function(code) {
    var re = {};
    re.info = '身份证有效';
    re.status = true;

    var city={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江 ",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北 ",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏 ",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外 "};
    var tip = "";
    var pass= true;
    //if(!code || !/^[1-9][0-9]{5}(19[0-9]{2}|200[0-9]|2010)(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[0-9]{3}[0-9xX]$/i.test(code)){
    if(!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/.test(code)){
        re.info = "身份证号格式错误";
        re.status = false;
    }

    else if(!city[code.substr(0,2)]){
        re.info = "身份证地址编码错误";
        re.status = false;
    }
    else{
        //18位身份证需要验证最后一位校验位
        if(code.length == 18){
            code = code.split('');
            //∑(ai×Wi)(mod 11)
            //加权因子
            var factor = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
            //校验位
            var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
            var sum = 0;
            var ai = 0;
            var wi = 0;
            for (var i = 0; i < 17; i++)
            {
                ai = code[i];
                wi = factor[i];
                sum += ai * wi;
            }
            var last = parity[sum % 11];
            if(parity[sum % 11] != code[17]){
                re.info   = "身份证校验位错误";
                re.status = false;
            }
        }
    }
    return re;
};

function getChecked(name) {
    var itemArr = [];
    var auth    = document.getElementsByName(name+"[]");
    console.log(auth);
    for(var i=0;i<auth.length;i++){
        if(auth[i].checked){
            itemArr.push(auth[i].value);
        }
    }
    return itemArr;
}
/**
 * 随机 生成一个 ID
 * @returns {string}
 */
function getRandomId()
{
    var str = ['0','1','2','3','4','5','6','7','8','9',
        'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
    ];

    var item = '';
    for (var i = 0; i<5 ; i++){
        item += str[Math.ceil(Math.random()*61)]
    }

    return item +'_'+ Math.ceil(Math.random()*61) ;
}
