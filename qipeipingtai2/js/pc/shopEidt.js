/**
 * Created by Administrator on 2017/6/5.
 */
/*点击上传*/
function upImgs(){
    var len = $('.banner').find('.positingLeft').length;
    if(len == 3){
        layer.msg('最多只能上传3张banner图片');
        return false;
    }
    //$('#upImgToken').val(imgToken);
    //$('#upImg').find('input[name="file"]').click();
}

function upImg(i) {
    var id = '#upImgToken'+i;
    //图片上传
    var wordUploader = WebUploader.create({
        // 选完文件后，是否自动上传。
        auto: true,
        // swf文件路径
        swf: '/js/webuploader-0.1.5/Uploader.swf',
        // 文件接收服务端。
        server: '/tools/baiduUploadForHome',
        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: id,
        sendAsBinary: true,
        // 只允许选择文件类型。
        accept: {
            title: 'Applications',
            extensions: 'jpg,png,jpeg,png',
            mimeTypes: '.jpg,.png,.jpeg,.png'
        }
    });

    //文件上传成功后的处理，在界面上的呈现，显示
    wordUploader.on('uploadSuccess', function (file, response) {
        layer.closeAll('loading'); //关闭加载层
        var idToken = 'upImgToken'+i;
        if(idToken=='upImgToken1'){
            //头像图片
            $('.cover').find('img').attr('src',response.httpUrl);
        }else if(idToken=='upImgToken2'){
            //banner图片
            var len = $('.banner').find('.positingLeft').length;
            if(len >2){
                upImgs();
                return false;
            }
            var str =  '<div class="positingLeft">';
            str += '<img src="'+response.httpUrl+'" class="bannerShopImg" alt="">';
            str += '<a href="javascript:;" onclick="delBanner(this)">';
            str += '<img src="/images/qpxm/pc/person/shopInfo/x.png" class="xBtn" alt=""></a></div>';
            if(len < 1){
                $('.banner').append(str);
            }else{
                $('.banner').find('.positingLeft:last').after(str);
            }
        }else if(idToken=='upImgToken3'){
            var len = $('.wechat_pic').find('img').length;
            if(len < 1){
                var str =  '<img src="'+response.httpUrl+'" class="w70h70" alt="">';
                $('.wechat_pic').append(str);
            }else{
                $('.wechat_pic').find('img').attr('src',response.httpUrl);
            }
        }
        layer.msg('上传成功');
    });
    //文件上传失败
    wordUploader.on('uploadError', function (file, response) {
        layer.closeAll('loading'); //关闭加载层
        //失败提示
        layer.msg('上传失败');
    });

    //文件上中
    wordUploader.on('uploadProgress', function (file, percentage) {
        layer.load(0, {shade: false});
    });
}

/*删除banner图片*/
function delBanner(obj){
    $(obj).parent().remove();
    layer.msg('删除成功');
}

/*添加输入框*/
function addPhone(obj){
    var addToken = $(obj).attr('addToken');
    if(addToken=='linePhone'){
        var str =  '<div class="linePhone">';
        str += '<input type="text" value="" class="shopEditInput">';
        str += '<span><a href="javascript:;" onclick="delPhone(this)">';
        str += '<img src="/images/qpxm/pc/person/shopInfo/jian.png" alt=""></a></span></div>';
        $('.linePhone:last').after(str);
        return false;
    }else if(addToken=='zuoJi'){
        var str =  '<div class="zuoJi">';
        str += '<input type="text" value="" class="shopEditInput">';
        str += '<span><a href="javascript:;" addToken="zuoJi" onclick="delPhone(this)">';
        str += '<img src="/images/qpxm/pc/person/shopInfo/jian.png" alt=""></a></span></div>';
        $('.zuoJi:last').after(str);
        return false;
    }else if(addToken=='qq'){
        var str =  '<div class="qq">';
        str += '<input type="text" value="" class="shopEditInput"><span>';
        str += '<a href="javascript:;" addToken="qq" onclick="delPhone(this)">';
        str += '<img src="/images/qpxm/pc/person/shopInfo/jian.png" alt=""></a></span></div>';
        $('.qq:last').after(str);
        return false;
    }

}
/*删除输入框*/
function delPhone(obj){
    $(obj).parent().parent().remove();
}


// 百度地图API功能
var map = new BMap.Map("allmap");
map.centerAndZoom(new BMap.Point(116.331398,39.897445),16);
map.enableScrollWheelZoom(true);

// 用经纬度设置地图中心点
function theLocation(x,y){
    map.clearOverlays();
    var new_point = new BMap.Point(x,y);
    var marker = new BMap.Marker(new_point);  // 创建标注
    map.addOverlay(marker);              // 将标注添加到地图中
    map.panTo(new_point);
    //marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
}
function showInfo(e){
    coordinate(e.point.lng,e.point.lat);        //存入坐标
    theLocation(e.point.lng,e.point.lat)
}

// 创建地址解析器实例
var myGeo = new BMap.Geocoder();
// 将地址解析结果显示在地图上,并调整地图视野
function keyword(){
    var keywords = $('#suggestId').val();
    if(keywords){
        myGeo.getPoint(keywords, function(point){
            if (point) {
                coordinate(point.lng,point.lat);                //存入坐标
                map.centerAndZoom(point, 16);
                map.addOverlay(new BMap.Marker(point));
            }else{
                layer.msg("您选择地址没有解析到结果!");
            }
        }, "北京市");
    }else{
        layer.msg("请先填写地理名称");
    }
}

/*点击事件*/
map.addEventListener("click", showInfo);
var geoc = new BMap.Geocoder();
map.addEventListener("click", function(e){
    var pt = e.point;
    geoc.getLocation(pt, function(rs){
        //var addComp = rs.addressComponents;
        //var adress = addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber;
        //$('#suggestId').val(adress);
    });
});

/*存入坐标*/
function coordinate(x,y){
    $('#suggestId').attr('latitude',y);
    $('#suggestId').attr('longitude',x);
}