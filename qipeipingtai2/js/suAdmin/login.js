
$(function(){
    //choose_bg();
    keyLogin();
    //用户登录
    $("#login_ok").bind('click',function(){
        doLogin();
        $("#login_ok").attr("disabled", true);
    });
});

function keyLogin(){
    if(event.keyCode == "13")
    {
        document.getElementById("login_ok").click();
    }
}
//生成一个时间戳
function genTimestamp(){
    var time = new Date();
    return time.getTime();
}
function changeCode(){
    $("#captcha_img").attr("src", "/?m=tools&a=imgCode&t="+genTimestamp());
}
//随机更换登录背景
function choose_bg() {
    var bg = Math.floor(Math.random() * 9 + 1);
    $('body').css('background-image', 'url(images/loginbg_0'+ bg +'.jpg)');
}

//登录输入监控
$(function(){
    var btn   = $("#login_ok");
    btn.attr("disabled", true);
    $("#login_form").find('input').blur(function(){
        var value = $(this).val();
        value = $.trim(value);
        var id    = $(this).attr("id");
        switch (id){
            case 'j_username':
                if(!value){
                    $("#tip").html('请输入登录帐号');
                    btn.attr("disabled", true);
                }else{
                    $("#tip").html('');
                }
                break;
            case 'j_password':
                if(!value){
                    $("#tip").html('请输入登录密码');
                    btn.attr("disabled", true);
                }else{
                    $("#tip").html('');
                }
                break;
            case 'j_captcha':
                if(!value){
                    $("#tip").html('请输入验证码');
                    btn.attr("disabled", true);
                }else{
                    var code = $("#j_captcha").val();
                    //console.log(code);
                    var url = '/suAdmin/checkCode';
                    $.post(url,{code:code},function(rdata){
                        //console.log(rdata.status);
                        if(rdata.massageCode == 'success'){
                            $("#tip").html('');
                        }else{
                            $("#tip").html(rdata.massage);
                            btn.attr("disabled", true);
                        }
                    },'json');
                }
                break;
        }
    });
    //获取焦点事件
    $("#login_form").find('input').focus(function(){
        btn.attr("disabled", false);
        $("#tip").html('');
    })

});



//登录
function doLogin(){
    var name     = $("#j_username").val();
    var pwd      = $("#j_password").val();
    var code     = $("#j_captcha").val();
    var remember = $("#j_remember").is(":checked");
    name = $.trim(name);
    pwd = $.trim(pwd);
    if(name && pwd && code){
        var url = '/suAdmin/doLogin';
        $.post(url,{name:name,pwd:pwd,code:code,remember:remember},function(rdata){
            console.log(rdata);
            if(rdata.massageCode =='success'){
                $("#tip").html(rdata.massage);
                setTimeout(function(){
                    location.href = '/suadmin/main';
                },500);
            }else{
                $("#tip").html(rdata.massage);
                setTimeout(function(){
                    $("#login_ok").attr("disabled", false);
                },1000);

            }
        },'json')
    }
}

