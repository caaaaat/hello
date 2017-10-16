/**
 * Created by Administrator on 2017/5/22.
 */

// 百度地图API功能
var map = new BMap.Map("map");    // 创建Map实例
map.centerAndZoom(new BMap.Point(116.404, 39.915), 11);  // 初始化地图,设置中心点坐标和地图级别
map.setCurrentCity("北京");          // 设置地图显示的城市 此项是必须设置的
map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放.

var point = new BMap.Point(116.404, 39.915);
map.centerAndZoom(point, 15);

var marker = new BMap.Marker(point);  // 创建标注

map.addOverlay(marker);               // 将标注添加到地图中
marker.enableDragging();//允许拖拽
//marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
//拖拽结束事件
marker.addEventListener("dragend", function(e){
    console.log(e);
    setPoint(e)
});

function theLocation(n){
    var city = $(n).val();
    //console.log(city);
    if(city != ""){
        map.centerAndZoom(city,12);      // 用城市名设置地图中心点
    }
}

function get_Position(){
    var keyword = $('#position').val();

    var city    = $('#city').val();
    // 创建地址解析器实例
    var myGeo = new BMap.Geocoder();
    // 将地址解析结果显示在地图上，并调整地图视野
    myGeo.getPoint(keyword, function(point){
        if (point) {
            map.clearOverlays();
            map.centerAndZoom(point, 16);
            //创建标注
            var marker = new BMap.Marker(point) ;
            map.addOverlay(marker);

            marker.enableDragging();//允许拖拽
            //拖拽结束事件
            marker.addEventListener("dragend", function(e){
                setPoint(e) ;
            });

            $('#lng').val(point['lng']);
            $('#lat').val(point['lat']);
        }else {
            $('#lng').val('');
            $('#lat').val('');
        }
    }, city);

}


function setPoint(e) {
    var geo = new BMap.Geocoder();

    var pt = e.point;

    geo.getLocation(pt, function(rs){
        var addComp = rs.addressComponents;
        var point   = rs.point;
        var address = addComp.province + addComp.city + addComp.district  + addComp.street  + addComp.streetNumber;
        $('#position').val(address);
        $('#lng').val(point.lng);
        $('#lat').val(point.lat);


    });

}