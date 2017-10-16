//图片地址
var imgUrl = 'http://app.7pqun.com'; 
//var imgUrl = 'http://192.168.2.21';  
//图片上传地址
//var imgUploadImg =  'http://119.23.215.135/api.sev.upload/uploadImg';  
var imgUploadImg = imgUrl+'/api.sev.upload/uploadImg';  

var cardBg1 = '../../../image/card-bg1.jpg';//名片背景
var cardBg2 = '../../../image/card-bg2.jpg';//名片背景
var cardBg3 = '../../../image/card-bg3.jpg';//名片背景
var cardBg4 = '../../../image/card-bg4.jpg';//名片背景

var logBg  = imgUrl+'/images/pub/log.png';//分享log 
var apkUrl = 'http://app.7pqun.com/apk/mobile.apk';             


var contentnomoreStr = '<div class="sw-footer"><span class="sw-footer-t">没有更多数据了</span></div>'; 
var contentnomoreStr1 = '<div class="sw-footer"><span class="sw-footer-t">没有更多数据了<br>温馨提示：产品的上传和编辑请在PC网站后台进行操作。网址：www.7pqun.com</span></div>'; 
var contentrefreshStr = '<i class="mui-spinner sw-loading"></i>正在加载...';

   
/**
 *mui监听li点击事件
 */
mui("#loadList").on('tap', 'ul', function (event) {
	this.click();
});

mui("#loadList").on('tap', 'li', function (event) { 
	this.click();
});

mui("#loadList").on('tap', 'div', function (event) {
	this.click();
});

mui("#loadList").on('tap', 'span', function (event) {
	this.click();
});

mui("#loadList").on('tap', 'img', function (event) {
	this.click();
});
/**
 * 打开新页面
 * @param url 跳转页面 
 * @param id  页面id   
 * @param aniShow 跳转方式      
 * @param extras 传递参数
 *  
 */  
function openView(url,id,aniShow,extras){    
			if(unsafe_tap()) return;  // 调用代码
			//非plus环境，直接走href跳转
			if(!mui.os.plus){
				location.href = url;
				return;   
			}   
		
			var webview_style = {};
			var extras  = extras?extras:{};
			var aniShow = aniShow?aniShow:"pop-in";
			var webview = plus.webview.create(url,id,webview_style,extras);
				webview.addEventListener("titleUpdate",function () {
					setTimeout(function () {
						webview.show(aniShow,200);
					},100);
				});
 		}
  

var http = {};
//var httpUrl = 'http://192.168.1.168';
var httpUrl = imgUrl;
/**
 * 请求数据
 * @param mod 模型
 * @param fun 方法
 * @param postData 参数
 * @param success 成功处理
 * @param error   错误处理 
 */
http.load = function(mod,fun,postData,success,error)
{
	var url = httpUrl+'/'+mod+'/'+fun; //请求地址
	console.log(url)
	mui.ajax(url,{
		data:postData,
		dataType:'json',//服务器返回json格式数据 
		type:'post',//HTTP请求类型
		timeout:3000,//超时时间设置为10秒；
		//headers:{'Content-Type':'application/json'},	              
		success:function(data){ 
			success(data);
		},
		error:function(xhr,type,errorThrown){
			if(error) error(xhr,type,errorThrown);
		}
	});
}


/**
 *加载数据
 * @param inData 数据
 * @param isLogin 判断是否需要登录 
 * 
 */
function loadInfo(inData,isLogin,isDown){
//	sw.jcon(inData)
	var postData = inData.postData; 
	var mod      = inData.mod;//模型
    var fun      = inData.fun;//方法
    var tpl      = inData.tpl;//列表模板    
    var listId   = inData.listId;//列表容器    
    var isDown   = isDown?isDown:false;
    var isload = true;
	   if(isLogin){//需要判断是否登录
		   	//监控页面是否登录
			if(UserInfo.has_login()==false){ 
					isload = false;
			}	   	
	   }
    
	if(isload){   
		//如果已经登陆 获取登录后的数据
		http.load(mod,fun,postData,function(rData){//请求成功
   			sw.jcon(rData)
 			if(rData.status==200){	    
 				
 			    //将数据输出到页面     
				var data   = rData.list;	
				sw.jcon(data) 
			 	var payTpl = $('#'+tpl).html();	 		
		        laytpl(payTpl).render(data,function(render){           
		            $('#'+listId).append(render);   
		        });	
		        
				//处理加载逻辑
				var count    = parseInt(rData.count);//数据总条数
				var page     = parseInt(rData.page);
				var pageSize = parseInt(rData.pageSize);
		    	var showLen  = page*pageSize;//统计当前应该显示多少条
				
				//判断条数是否足够
		        if(count<=showLen){//加载动图隐藏
		        	
		           	mui('#loadList').pullRefresh().endPullupToRefresh(true); //参数为true代表没有更多数据了。
		        }else{
		        
		        	mui('#loadList').pullRefresh().endPulldownToRefresh(false);
					mui('#loadList').pullRefresh().refresh(true); //参数为true代表没有更多数据了。
					
		        	$("#page").val(page+1); 
		        }
		        //如果条数为 0 出现提示语言
		        if(count==0&&page==1){
		           	mui('#loadList').pullRefresh().endPullupToRefresh(true); //参数为true代表没有更多数据了。
		        }
 			   		   
 			}else{ 
 				sw.toast(rData.msg); 	 				
 				mui('#loadList').pullRefresh().disablePullupToRefresh(true);//隐藏加载提示
 			}
 			
 			if(isDown){
 				isDown.endPullDownToRefresh();
 			}
 			
			
 		},function(xhr,type,errorThrown){//请求失败 
			//无网络提示               
			//sw.toast('请求数据失败，请检查网络后重试'); 
			mui('#loadList').pullRefresh().disablePullupToRefresh();//隐藏加载提示
 		})
	}
}


/**
 *加载数据
 * @param inData 数据
 * @param isLogin 判断是否需要登录 
 * 
 */
function loadInfoArr(inData,isLogin,self){
	
	var postData = inData.postData; 
	var mod      = inData.mod;//模型
    var fun      = inData.fun;//方法
    var tpl      = inData.tpl;//列表模板    
    var listId   = inData.listId;//列表容器    
    var pageId   = inData.pageId;//页码Id    
    
    var isload = true;  
	   if(isLogin){//需要判断是否登录
		   	//监控页面是否登录
			if(UserInfo.has_login()==false){ 
					isload = false;
			}	   	
	   }
   
	if(isload){ 
		
		//如果已经登陆 获取登录后的数据
		http.load(mod,fun,postData,function(rData){//请求成功
			     
			//sw.jcon(rData)   
 			if(rData.status==200){	  				
 			    //将数据输出到页面     
				var data   = rData.list;	
				
			 	var payTpl = $('#'+tpl).html();	 		
		        laytpl(payTpl).render(data,function(render){           
		            $('#'+listId).append(render); 
		        });	
		        
				//处理加载逻辑
				var count    = parseInt(rData.count);//数据总条数
				var page     = parseInt(rData.page);
				var pageSize = parseInt(rData.pageSize);
		    	var showLen  = page*pageSize;//统计当前应该显示多少条
				
				//判断条数是否足够
		        if(count<=showLen){//加载动图隐藏		        	
		           	self.endPullUpToRefresh(true); //参数为true代表没有更多数据了。
		        }else{	
		        	self.endPullUpToRefresh(false);
		        	$("#"+pageId).val(page+1);
		        }
		        //如果条数为 0 出现提示语言
		        if(count==0&&page==1){
		           	self.endPullUpToRefresh(true); //参数为true代表没有更多数据了。
		        }
 			   		   
 			}else{ 
 				sw.toast(rData.msg); 	 				
 				self.endPullUpToRefresh(true);
 			}
			
 		},function(xhr,type,errorThrown){//请求失败 
 			sw.jcon(xhr) 
			//无网络提示               
//			sw.toast('请求数据失败，请检查网络后重试'); 
			self.endPullUpToRefresh(true);
 		})
	}else{
		sw.toast('请登录后重试'); 
		self.endPullUpToRefresh(true);
	}
}

//打开浏览器
function openBrowser(url) {
	plus.runtime.openURL( url );
}


//打开链接
function linkUrl(url,type){
	
	if(type==4){	
		openBrowser(url);
	}else{
		openView(url,url,'pop-in')
	}
	
}

//加载弹窗
function loading(title){
     var w = plus.nativeUI.showWaiting( title,{width:'90px',height:'90px',background:'rgba(254,82,74,0.8)'});		
}

//关闭原始加载弹窗
function closeLoading(time){
	setTimeout(function(){
				plus.nativeUI.closeWaiting();
			},time)
}



//登录相关
;function UserInfo(){
};

//清除登录信息
UserInfo.clear = function(){
	plus.storage.removeItem('username');
	plus.storage.removeItem('password');
	plus.storage.removeItem('token');
}

//检查是否包含自动登录的信息
UserInfo.auto_login = function(){
	var username = UserInfo.username();
	var pwd = UserInfo.password();
	if(!username || !pwd){
	return false;
	}
	return true;
}

//检查是否已登录
;mui.web_query = function(func_url, params, onSuccess, onError, retry){
	var onSuccess = arguments[2]?arguments[2]:function(){};
	var onError = arguments[3]?arguments[3]:function(){};
	var retry = arguments[4]?arguments[4]:3;
	func_url = 'http://www.xxxxxx.com/ajax/?fn=' + func_url;
	mui.ajax(func_url, {
	data:params,
	dataType:'json',
	type:'post',
	timeout:3000,
	success:function(data){
	if(data.err === 'ok'){
		onSuccess(data);
	}
	else{
		onError(data.code);
	}
	},
	error:function(xhr,type,errorThrown){
		retry--;
		if(retry > 0) return mui.web_query(func_url, params, onSuccess, onError, retry);
		onError('FAILED_NETWORK');
		}
	})
};

function get_pwd_hash(pwd){
	var salt = 'hbuilder'; //此处的salt是为了避免黑客撞库，而在md5之前对原文做一定的变形，可以设为自己喜欢的，只要和服务器验证时的salt一致即可。
	return md5(salt + pwd); //此处假设你已经引用了md5相关的库，比如github上的JavaScript-MD5
}

//这里假设你已经通过DOM操作获取到了用户名和密码，分别保存在username和password变量中。
//var username = xxx;
//var password = xxx;
//var pwd_hash = get_pwd_hash(password);

var onSuccess = function(data){
	/*UserInfo.username(username);
	UserInfo.password(pwd_hash);*/
	UserInfo.token(data.token); //把获取到的token保存到storage中
//	var wc = plus.webview.currentWebview();
//	wc.hide('slide-out-bottom'); //此处假设是隐藏登录页回到之前的页面，实际你也可以干点儿别的
}

 

var onError = function(errcode){
	switch(errcode){
		case 'FAILED_NETWORK':
		mui.toast('网络不佳');
		break;
		case 'INVALID_TOKEN':
		wv_login.show();
		break;
		default:
		console.log(errcode);
	}
};

//mui.web_query('get_token', {username:username,password:pwd_hash}, onSuccess, onError, 3);


UserInfo.has_login = function(){
//	var username = UserInfo.username();
//	var pwd = UserInfo.password();
	var token = UserInfo.token();
	if(!token){
	return false;
	}
	return true;
};

UserInfo.username = function(){
	if(arguments.length == 0){
	return plus.storage.getItem('username'); 
	}
	if(arguments[0] === ''){
	plus.storage.removeItem('username');
	return;
	}
	plus.storage.setItem('username', arguments[0]);
};

UserInfo.password = function(){
	if(arguments.length == 0){
	return plus.storage.getItem('password'); 
	}
	if(arguments[0] === ''){
	plus.storage.removeItem('password');
	return;
	}
	plus.storage.setItem('password', arguments[0]);
};

UserInfo.token = function(){
	if(arguments.length == 0){
	return plus.storage.getItem('token'); 
	}
	if(arguments[0] === ''){
	plus.storage.removeItem('token');
	return;
	}
	plus.storage.setItem('token', arguments[0]);
};


//自定义操作函数
var sw = {};
    
/**
 * 清除字符串内空格
 * @param {Object} str
 */
sw.trim = function(str){
	return str.replace(/(^\s+)|(\s+$)/g,"");
}

/**
 * 清除字符串中间空格
 * @param {Object} str
 */
sw.Trim = function(str) 
{ 
var result; 
result = str.replace(/(^\s+)|(\s+$)/g,""); 
result = result.replace(/\s/g,""); 
return result; 
} 

/**
 * 提示窗
 * @param {Object} msg
 * @param {Object} option
 */
sw.toast = function(msg,option){
//	mui.toast(msg,{ duration:'long', type:'div' }) 
var options = option?option:{verticalAlign:'center'}
	plus.nativeUI.toast( msg,options); 
}

/**
 * 打印json
 * @param {Object} jsons
 */
sw.jcon = function(jsons){
	console.log(JSON.stringify(jsons));
}

/**
 * 打印json
 * @param {Object} jsons
 */
dy = function(jsons){
	console.log(JSON.stringify(jsons));
}

//验证类函数

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
 * 验证是否是正整数
 * @param number
 * @returns {boolean}
 */
Verification.integer = function(num)
{
    if(!(/^[0-9]*[1-9][0-9]*$/.test(num))){
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
    if(!code || !/^[1-9][0-9]{5}(19[0-9]{2}|200[0-9]|2010)(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[0-9]{3}[0-9xX]$/i.test(code)){
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


//访问记录
function visitedLog(firmsId){	
	//判断是否登录
   if(UserInfo.has_login()==true){//未登录	   			
	var userType = JsonStorage.getItem('userType');	//2为业务员
	if(userType!=2){
		setTimeout(function(){
			var token  = plus.storage.getItem('token');				
			var postData = {};
				postData.token   = token;
				postData.firmsId = firmsId;  
			//获取数据
			http.load('api.sev.user','visitLog',postData,function(rData){})						
		},3000)
	}   		   	
  }			
}

//拨打记录
function callLog(firmsId,callType){	
	//判断是否登录
   if(UserInfo.has_login()==true){//未登录	   			
	var userType = JsonStorage.getItem('userType');	//2为业务员
	var token  = plus.storage.getItem('token');				
	var postData = {};
		postData.userType = userType;
		postData.phoneType= 3;
		postData.token   = token;
		postData.firmsId = firmsId;  
		postData.callType= callType;  
		dy(postData);
		//获取数据
		http.load('api.sev.user','callLog',postData,function(rData){})						  
   		   	
  }			
}


/**
 * 防止连续点击导致webview打开出错
 * 注：主要用于打开新窗口时候
 * @author 蔡繁荣
 * @version 1.0.1 build 20151220
 */
var tap_first  = null;
function unsafe_tap(){
    if(!tap_first){
        tap_first = new Date().getTime();
        setTimeout(function() {
            tap_first = null;
        }, 1500);
    }else{
        return true;
    }
}
