/**
 * Created by Administrator on 2017/5/11.
 */
/*回到顶部*/
function goTop(){
    var speed=200;//滑动的速度
    $('body,html').animate({ scrollTop: 0 }, speed);
    return false;
}
/*点击QQ或电话*/
function showF(obj){
    if($(obj).next().css("display")=='block'){
        $(obj).next().hide();
    }else{
        $(obj).next().show();
    }
}
/*点击半截二维码显示完整二维码*/
function showEr(obj){
    $(obj).find('img').attr('src','/images/qpxm/pc/supperList/backEr.png');
    $(obj).attr('onclick','hideEr(this)');
    $(obj).parent().find('.dianErImgDiv').show();
}
function hideEr(obj){
    $(obj).find('img').attr('src','/images/qpxm/pc/supperList/banErWeiM.png');
    $(obj).attr('onclick','showEr(this)');
    $(obj).parent().find('.dianErImgDiv').hide();
}

//初始化瀑布流开始
//var loading = false;  //状态标记
//$(document.body).infinite(20).on("infinite", function() {
//    $('#jiaZai').show();
//    if(loading) return;
//    loading  = true;
//    setTimeout(function() {
//        for(var i=0; i<3; ++i){
//            var str = data();
//            $('.jingXS:last').after(str);
//        }
//        loading  = false;
//        $('#jiaZai').hide();
//    }, 1000);
//});
