/**
 * Created by Administrator on 2017/6/6.
 */
var nowCityName = '';
/*点击选中店铺的定位*/
function dingWeiThis(obj){
    var x = $(obj).parent().parent().parent().parent().attr('longitude');
    var y = $(obj).parent().parent().parent().parent().attr('latitude');
    if(x!='null' && y!='null'){
        map.clearOverlays();        //清除之前所有标注
        var mPoint = new BMap.Point(x,y);
        map.enableScrollWheelZoom(true);
        map.centerAndZoom(mPoint,12);
        getAllZuoBiao()
    }else{
        layer.msg('获取经纬度失败');
    }
}
/*首页列表展示*/
function getList(page,pageSize){
    var index = layer.load(2);
    page = page ? page : 1;
    pageSize = pageSize ? pageSize : 3;
    var wordsKey = $('.wordsKey').val();    //输入框关键字
    var province = $('#s_province option:selected').val();  //省
    if(province == '全部'){
        province = '';
    }
    var city = $('#s_city option:selected').val();          //市
    if(city == '全部'){
        city = '';
    }
    var district = $('#s_county option:selected').val();    //区
    if(district == '全部'){
        district = '';
    }
    var classification = $('#selectType option:selected').val();         //修理厂类型
    var url = '/pc.firm/showCarMend2';
    $.post(url, {
        page: page,
        pageSize: pageSize,
        wordsKey:wordsKey,
        province:province,
        city:city,
        nowCityName:nowCityName,
        district:district,
        classification:classification
    }, function (rdata) {
        layer.close(index);
        var pageInfo = '';
        var p = rdata.page;
        var pageSize = rdata.pageSize;
        var count = rdata.count;
        if (count > 0) {
            var startPage = (p - 1) * pageSize + 1;
            //var endPage  = (p*pageSize);
            var endPage = ((p * pageSize) > count) ? count : (p * pageSize);
//                pageInfo = '显示 ' + startPage + ' 到 ' + endPage + ' 项，共 ' + count + ' 项';
            pageInfo = '第'+startPage+'~'+endPage+'条/共'+count+'条';
        } else {
            pageInfo = '共 0 项';
        }
        $("#pageText").text(pageInfo);
        //分页处理
        var totalPage = count / pageSize;
        //总页码
        totalPage = Math.ceil(totalPage);
        $("#pager").createPage({
            pageCount: totalPage,
            current: p,
            backFn: function (p) {
                $('#pages').val(p);
                getList(p, pageSize);
            }
        });
        showSub(rdata);
    }, 'json');
}



/*获取所有修理厂坐标*/
function getAllZuoBiao(){
    map.clearOverlays();        //清除之前所有标注
    var allZuoBiao = JsonStorage.getItem('allZuoBiao');
    var center = map.getCenter();
    var geoc = new BMap.Geocoder();
    var pt = center;
    geoc.getLocation(pt, function(rs){
        var addComp = rs.addressComponents;
        nowCityName = addComp.city;
        getList(1,3);
    });
    var circle = new BMap.Circle(center,10000,{fillColor:"#F3FBDD", strokeWeight: 1 ,fillOpacity: 0.1, strokeOpacity: 0.1}); //创建圆
    map.addOverlay(circle);
    var c = circle.getCenter();
    var r = circle.getRadius(10000);
    if(allZuoBiao){
        for(var i=0; i<allZuoBiao.length; ++i){
            var point = new BMap.Point(allZuoBiao[i].longitude,allZuoBiao[i].latitude);   //获取坐标对象
            var dis = map.getDistance(point, c);
            if(dis <= r){
                addMarker(point,allZuoBiao[i].companyname,allZuoBiao[i].EnterpriseID);
            }
        }
    }else{
        var classification = $('#selectType option:selected').val();         //修理厂类型
        var url = '/pc.firm/getAllZuoBiao';
        $.post(url, {
        }, function (rdata) {
            var rdata = eval('(' + rdata + ')');
            if(rdata && rdata.length>0){
                JsonStorage.setItem('allZuoBiao',rdata);
                //point与圆心距离小于圆形半径，则点在圆内，否则在圆外
                var c = circle.getCenter();
                var r = circle.getRadius(10000);
                for (var i = 0; i < rdata.length; i ++) {
                    var point = new BMap.Point(rdata[i].longitude,rdata[i].latitude);   //获取坐标对象
                    var dis = map.getDistance(point, c);
                    if(dis <= r){
                        addMarker(point,rdata[i].companyname,rdata[i].EnterpriseID);
                    }
                }
            }
        })

    }
}
// 百度地图API功能
//var xY = JsonStorage.getItem('dingWei');
//console.log(xY);
var map = new BMap.Map("allmap");
var mPoint = new BMap.Point(104.07231500, 30.67402400);
map.enableScrollWheelZoom(true);
map.centerAndZoom(mPoint,12);
var myCity = new BMap.LocalCity();
myCity.get(myFun);
function myFun(result){
    map.clearOverlays();        //清除之前所有标注
    var mPoint = new BMap.Point(result.center.lng,result.center.lat);
    map.enableScrollWheelZoom(true);
    map.centerAndZoom(mPoint,12);
    getAllZuoBiao();
}

/*初始化标注窗口*/
var opts = {
    width:250,
    height:50,
    title:'汽修厂信息'
};

// 用经纬度设置地图中心点
function theLocation(x,y){
    var new_point = new BMap.Point(x,y);
    addMarker(new_point);   //调用创建标注
}

/*创建标注*/
function addMarker(new_point,info,id){
    var marker = new BMap.Marker(new_point);  // 创建标注
    map.addOverlay(marker);                 // 将标注添加到地图中
    if(info){
        addClickHandler(info,marker,id);
    }
}
/*标注内容窗口*/
function addClickHandler(content,marker,id){
    content = content+'<br/><a style="color: green" href="/def/store?ID='+id+'">进入店铺>></a>';
    marker.addEventListener("click",function(e){
            openInfo(content,e)}
    );
}
function openInfo(content,e){
    var p = e.target;
    var point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
    var infoWindow = new BMap.InfoWindow(content,opts);  // 创建信息窗口对象
    map.openInfoWindow(infoWindow,point); //开启信息窗口
}

/*拖动地图结束后加载完成后*/
map.addEventListener("dragend",function(){
    getAllZuoBiao();              //调用检索并添加标注
});