/**
 * Created by Administrator on 2017/6/8.
 */
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
        var str      = '<img src="'+response.httpUrl+'" style="width: 70px;height: 70px;" alt="">';
            str      += '<a href="javascript:;" onclick="delImgs(this)"><img src="/images/qpxm/pc/person/shopInfo/x.png" class="xImg" alt=""></a>';
        if(id == '#upImgToken1'){
            $('.main_icon_1').html(str);
            $('.main_icon_1').css('background','');
        }else if(id == '#upImgToken2'){
            $('.main_icon_2').html(str);
            $('.main_icon_2').css('background','');
        }else if(id == '#upImgToken3'){
            $('.main_icon_3').html(str);
            $('.main_icon_3').css('background','');
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


/*点击上传*/
//function upPhotos(obj){
//    var classToken = $(obj).attr('data_token');
//    $('#classToken').val(classToken);
//    $('#upImg').find('input[name="file"]').click();
//}

/*点击删除图片*/
function delImgs(obj){
    $(obj).parent().css('background',"url('/images/qpxm/pc/person/shopInfo/tianjiazp_s.png')");
    $(obj).parent().css('background-size',"cover");
    $(obj).parent().html('');
}

//绘制名片
function setCard(img1,img2,name,linkMan,phone,tel,qq,address,main_icon_1,main_icon_2,main_icon_3,tokenUse){
    //address += '范德萨空间的时刻垃圾发电是你们，车系；';
    if(address){
        if(address.length > 38){
            address = address.slice(0,38)+'...';
        }
    }
    if(name){
        if(name.length > 20){
            name = name.slice(0,20)+'...';
        }
    }
    var c=document.getElementById("myCanvas");
    var ctx=c.getContext("2d");

    var image = new Image();
    image.src = img1;
    ctx.drawImage(image,0,0,520,320);

    ctx.font="22px Verdana";
    ctx.fillStyle = "#000";
    ctx.fillText(name,20,40);
    if(tokenUse==1){
        var image1 = new Image();
        image1.src = img2;
        ctx.drawImage(image1,410,170,100,100);

        var image2 = new Image();
        image2.src = main_icon_1;
        ctx.save(); // 保存当前ctx的状态
        ctx.arc(50,80,25,0,2*Math.PI); //画出圆
        ctx.clip(); //裁剪上面的圆形
        ctx.drawImage(image2,25,55,50,50); // 在刚刚裁剪的园上画图
        ctx.restore(); // 还原状态

        if(!main_icon_1){
            var con_2 = 50;
        }else{
            var con_2 = 115;
        }
        var image3 = new Image();
        image3.src = main_icon_2;
        ctx.save(); // 保存当前ctx的状态
        ctx.arc(con_2,80,25,0,2*Math.PI); //画出圆
        ctx.clip(); //裁剪上面的圆形
        ctx.drawImage(image3,con_2-25,55,50,50); // 在刚刚裁剪的园上画图
        ctx.restore(); // 还原状态
        if(!main_icon_1 && !main_icon_2){
            var con_3 = 50;
        }else if(!main_icon_1 || !main_icon_2){
            var con_3 = 115;
        }else{
            var con_3 = 180;
        }
        var image4 = new Image();
        image4.src = main_icon_3;
        ctx.save(); // 保存当前ctx的状态
        ctx.arc(con_3,80,25,0,2*Math.PI); //画出圆
        ctx.clip(); //裁剪上面的圆形
        ctx.drawImage(image4,con_3-25,55,50,50); // 在刚刚裁剪的园上画图
        ctx.restore(); // 还原状态

        ctx.font="18px Verdana";
        ctx.fillText("联系人："+linkMan,220,80);
        ctx.fillText("手机："+phone,20,145);
        ctx.fillText("电话："+tel,20,175);
        ctx.fillText("Q Q："+qq,20,205);
        //ctx.fillText("地址："+address,20,235);
        writeTextOnCanvas(ctx,30,43,'地址：'+address,20,235);
        ctx.fillText("本名片由<<汽配群>>生成",120,295);
    }else{
        var image1 = new Image();
        image1.src = img2;
        ctx.drawImage(image1,20,135,100,100);

        var image2 = new Image();
        image2.src = main_icon_1;
        ctx.save(); // 保存当前ctx的状态
        ctx.arc(250,80,25,0,2*Math.PI); //画出圆
        ctx.clip(); //裁剪上面的圆形
        ctx.drawImage(image2,225,55,50,50); // 在刚刚裁剪的园上画图
        ctx.restore(); // 还原状态

        if(!main_icon_1){
            var con_2 = 250;
        }else{
            var con_2 = 310;
        }
        var image3 = new Image();
        image3.src = main_icon_2;
        ctx.save(); // 保存当前ctx的状态
        ctx.arc(con_2,80,25,0,2*Math.PI); //画出圆
        ctx.clip(); //裁剪上面的圆形
        ctx.drawImage(image3,con_2-25,55,50,50); // 在刚刚裁剪的园上画图
        ctx.restore(); // 还原状态
        if(!main_icon_1 && !main_icon_2){
            var con_3 = 250;
        }else if(!main_icon_1 || !main_icon_2){
            var con_3 = 310;
        }else{
            var con_3 = 370;
        }
        var image4 = new Image();
        image4.src = main_icon_3;
        ctx.save(); // 保存当前ctx的状态
        ctx.arc(con_3,80,25,0,2*Math.PI); //画出圆
        ctx.clip(); //裁剪上面的圆形
        ctx.drawImage(image4,con_3-25,55,50,50); // 在刚刚裁剪的园上画图
        ctx.restore(); // 还原状态

        ctx.font="18px Verdana";
        ctx.fillText("联系人："+linkMan,20,80);
        ctx.fillText("手机："+phone,140,145);
        ctx.fillText("电话："+tel,140,175);
        ctx.fillText("Q Q："+qq,140,205);
        //ctx.fillText("地址："+address,20,235);
        writeTextOnCanvas(ctx,30,43,'地址：'+address,140,235);
        ctx.fillText("本名片由<<汽配群>>生成",120,295);
    }

}

/*点击下载图片*/
//function downImg(){
//    var myCanvas = document.getElementById("myCanvas");
//    var image = myCanvas.toDataURL("image/png").replace("image/png", "image/octet-stream;");
//    $('#base64').val(image);
//    console.log(image);
//    var save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
//    console.log(save_link);
//    save_link.href = image;
//    save_link.download = '企业名片.png';
//    console.log(save_link);
//    var event = document.createEvent('MouseEvents');
//    console.log(event);
//    console.log(event.path['0'].href);
//    event.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
//    save_link.dispatchEvent(event);
//
//}

//ctx_2d        getContext("2d") 对象
//lineheight    段落文本行高
//bytelength    设置单字节文字一行内的数量
//text          写入画面的段落文本
//startleft     开始绘制文本的 x 坐标位置（相对于画布）
//starttop      开始绘制文本的 y 坐标位置（相对于画布）
function writeTextOnCanvas(ctx_2d, lineheight, bytelength, text ,startleft, starttop){
    function getTrueLength(str){//获取字符串的真实长度（字节长度）
        var len = str.length, truelen = 0;
        for(var x = 0; x < len; x++){
            if(str.charCodeAt(x) > 128){
                truelen += 2;
            }else{
                truelen += 1;
            }
        }
        return truelen;
    }
    function cutString(str, leng){//按字节长度截取字符串，返回substr截取位置
        var len = str.length, tlen = len, nlen = 0;
        for(var x = 0; x < len; x++){
            if(str.charCodeAt(x) > 128){
                if(nlen + 2 < leng){
                    nlen += 2;
                }else{
                    tlen = x;
                    break;
                }
            }else{
                if(nlen + 1 < leng){
                    nlen += 1;
                }else{
                    tlen = x;
                    break;
                }
            }
        }
        return tlen;
    }
    for(var i = 1; getTrueLength(text) > 0; i++){
        var tl = cutString(text, bytelength);
        ctx_2d.fillText(text.substr(0, tl).replace(/^\s+|\s+$/, ""), startleft, (i-1) * lineheight + starttop);
        text = text.substr(tl);
    }
}



//绘制名片（汽修厂）
function setCardQiXiu(img1,img2,name,linkMan,phone,tel,qq,address,major,tokenUse,tokenType){
    if(address){
        if(address.length > 38){
            address = address.slice(0,38)+'...';
        }
    }
    if(name){
        if(name.length > 20){
            name = name.slice(0,20)+'...';
        }
    }
    if(major){
        if(major.length > 50){
            major = major.slice(0,50)+'...';
        }
    }
    var c=document.getElementById("myCanvas");
    var ctx=c.getContext("2d");

    var image = new Image();
    image.src = img1;
    ctx.drawImage(image,0,0,520,320);

    if(tokenUse==1){
        var image1 = new Image();
        image1.src = img2;
        ctx.drawImage(image1,405,170,100,100);
        ctx.font="22px Verdana";
        if(tokenType == 3){
            var co = "#fff";
        }else{
            var co = "#000";
        }
        ctx.fillStyle = co;
        ctx.fillText(name,20,40);

        ctx.font="16px Verdana";
        ctx.fillText("联系人："+linkMan,20,70);
        writeTextOnCanvas(ctx,30,55,'主营：'+major,20,100);
        ctx.fillText("手机："+phone,20,160);
        ctx.fillText("电话："+tel,20,190);
        ctx.fillText("Q Q："+qq,20,220);
        //ctx.fillText("地址："+address,20,235);
        writeTextOnCanvas(ctx,30,43,'地址：'+address,20,250);
        ctx.fillText("本名片由<<汽配群>>生成",140,305);
    }else{
        var image1 = new Image();
        image1.src = img2;
        ctx.drawImage(image1,20,150,100,100);
        ctx.font="22px Verdana";
        if(tokenType == 3){
            var co = "#fff";
        }else{
            var co = "#000";
        }
        ctx.fillStyle = co;
        ctx.fillText(name,20,40);

        ctx.font="16px Verdana";
        ctx.fillText("联系人："+linkMan,20,70);
        writeTextOnCanvas(ctx,30,55,'主营：'+major,20,100);
        ctx.fillText("手机："+phone,140,155);
        ctx.fillText("电话："+tel,140,185);
        ctx.fillText("Q Q："+qq,140,215);
        //ctx.fillText("地址："+address,20,235);
        writeTextOnCanvas(ctx,30,43,'地址：'+address,140,245);
        ctx.fillText("本名片由<<汽配群>>生成",140,305);
    }

}