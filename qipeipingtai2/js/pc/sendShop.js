
/*删除照片*/
function delPicture(obj){
    layer.confirm('确定删除吗？', {
        btn: ['再想想','删除'] //按钮
    }, function(){
        layer.closeAll();
    }, function(){
        $(obj).parent().parent().parent().find('.upImgDivToken').css('background','url("/images/qpxm/pc/person/approveInfo/bgImg.png")');
        $(obj).parent().parent().parent().find('.upImgDivToken').html('');
        layer.closeAll();
    });
}

/*提交求购数据*/
function tiJiao(){
    var data = {};
    var len = $('.shiXiaoList').length;
    for(var i=0; i<len; ++i){
        if($('.shiXiaoList').eq(i).find('a').attr('token')==1){
            data.limitation = $('.shiXiaoList').eq(i).attr('data_day'); //时效
            break;
        }
    }
    data.car_group_id = $('#fourTypeId').val();                          //车系四级id
    data.frame_number = $('#cheJiaHao').text();                          //车架号
    data.vin_pic = '';
    if($('.vin_pic').find('img').length>0){
        data.vin_pic = $('.vin_pic').find('img').attr('src');               //VIN封面图片
    }
    var l = $('.pic_url').find('.vinImgDiv').length;
    var pic_url = [];
    for(var i=0; i<l; ++i){
        var pic = $('.pic_url').find('.vinImgDiv').eq(i).find('img').attr('src');
        if(pic){
            pic_url.push(pic);
        }
    }
    data.want_buy_pic = pic_url.join(",");                              //相关图片,'，'分割
    data.memo = $('.beiZhuArea').val();                                 //备注说明
    var length = $('.want_buy_list').length;
    var wantList  = [];
    var k =0;
    for(var i=0; i<length; ++i){
        var oneTypeId = $('.want_buy_list').eq(i).find('.oneTypeMoBan option:selected').attr('data_id');
        var twoTypeId = $('.want_buy_list').eq(i).find('.TwoTypeMoBan option:selected').attr('data_id');
        var num       = $('.want_buy_list').eq(i).find('input[type="number"]').val();
        var list_memo = $('.want_buy_list').eq(i).find('input[type="text"]').val();
        if(!twoTypeId){
            layer.msg('第'+(Number(i)+1)+'条数据的配件类别数据不完整');
            return false;
        }
        if(num>0){
            //一级类型id '|want_list|' 二级类型id '|want_list|' 购买数量 '|want_list|' 备注说明
            wantList[k] = oneTypeId+'|want_list|'+twoTypeId+'|want_list|'+num+'|want_list|'+list_memo;
            k += 1;
        }else{
            layer.msg('请正确填写购买数量');
            return false;
        }
    }
    data.want_buy_list = wantList.join("#want_list#");              //"#want_list#" 连接数组
    data.companyType   = $('#companyType').val();                   //选择的车系属于的商家类型
    if(!data.car_group_id){
        layer.msg('请选择车系');
        return false;
    }
    if(!data.companyType){
        layer.msg('数据错误，请刷新页面重试');
        return false;
    }
    //if(!data.vin_pic){
    //    layer.msg('请上传VIN照片封面');
    //    return false;
    //}
    //if(!data.want_buy_pic){
    //    layer.msg('请至少上传一张相关照片');
    //    return false;
    //}
    if(!data.want_buy_list){
        layer.msg('请至少添加一个采购清单');
        return false;
    }
    $.ajax({
        url:"/pc.product/insertShop",    //请求的url地址
        dataType:"json",   //返回格式为json
        async:true,//请求是否异步，默认为异步，这也是ajax重要特性
        data:{"data":data},    //参数值
        type:"POST",   //请求方式
        success:function(req){
            if(req.status == 1){
                layer.msg('提交发布成功');
                setTimeout(function(){
                   location.href='/person/shoping';
                },1000);
            }else{
                layer.msg('提交失败');
                return false;
            }
        },
        error:function(){
            layer.msg('出错了');
        }
    });
}

/*分类选择完成后，点击完成*/
function queDing(){
    var len = $('.alertListDiv').length;
    var ids   = [];
    var names = [];
    var data_pid = [];
    if(len > 0){
        for(var i=0; i<len; ++i){
            var check = $('.alertListDiv').eq(i).find('input[type=radio]').is(':checked');
            if(check){
                var data_id   = $('.alertListDiv').eq(i).attr('data_id');
                var data_name = $('.alertListDiv').eq(i).find('.floLeft').text();
                var pid       = $('.alertListDiv').eq(i).attr('data_pid');
                names.push(data_name);
                ids.push(data_id);
                data_pid.push(pid);
            }
        }
        var shangJia = '';
        if(ids){
            var l = $('.tableTr').length;
            var oneCheck = '';
            for(var i=0; i<l; ++i){
                var c = $('.tableTr').eq(i).find('input[name="oneType"]').is(':checked');
                if(c){
                    oneCheck = $('.tableTr').eq(i).find('input[name="oneType"]').attr('data-id');
                    shangJia = $('.tableTr').eq(i).find('.floLeft').text();
                    break;
                }
            }
            $('#companyType').val(oneCheck);
            console.log(oneCheck);
            var oneClass = '.carTypesShop'+oneCheck;
            var len = $(erClass).length;
            var ll  = $('.alertType').length;
            var typeText = [];
            for(var i=0; i<ll; ++i){
                for(var j=0; j<data_pid.length; ++j){
                    var pid = $('.alertType').eq(i).attr('data_id');
                    if(pid == data_pid[j]){
                        var parentId = $('.alertType').eq(i).attr('data_pid');
                        var typeObj  = {}
                        var pName = $('.alertType').eq(i).text();
                        typeObj.name = pName+'/'+names[j];
                        typeObj.threePid = parentId;
                        typeText.push(typeObj);
                    }
                }
            }
            var erClass = '.carThreeTypeDiv'+oneCheck;
            var le = $(erClass).find('.upImgsListDiv').length;  //二级分类的数量
            var erTypeText = [];
            for(var i=0; i<le; ++i){
                for(var j=0; j<typeText.length; ++j){
                    var erId = $(erClass).find('.upImgsListDiv').eq(i).attr('data_id');
                    if(erId == typeText[j].threePid){
                        var typeObj  = {};
                        var erPid  = $(erClass).find('.upImgsListDiv').eq(i).attr('data_pid');
                        var erName = $(erClass).find('.upImgsListDiv').eq(i).find('.textCenter').text();
                        typeObj.threePid = typeText[j].threePid;
                        typeObj.erPid    = erPid;
                        typeObj.name     = erName+'/'+typeText[j].name;
                        erTypeText.push(typeObj);
                    }
                }
            }
            var oneClass = '.carTypesShop'+oneCheck;
            var lenn = $(oneClass).length;                      //一级分类的CLASS数量
            for(var i=0; i<lenn; ++i){
                for(var j=0; j<erTypeText.length; ++j){
                    var oneId = $(oneClass).eq(i).attr('data_id');
                    if(oneId == erTypeText[j].erPid){
                        var oneName = $(oneClass).eq(i).find('.seekTypes').text();
                        erTypeText[j].name = oneName+'/'+erTypeText[j].name;
                    }
                }
            }
            $('#cheXi').html('');
            $('#shangjia').html('');
            var spanStr = '';
            for(var i=0; i<erTypeText.length; ++i){
                spanStr += '<span style="margin-right: 15px;">'+erTypeText[i].name+'</span>'
            }
            $('#cheXi').html(spanStr);      //回显车系内容
            $('#shangjia').html(shangJia);  //回显商家内容
            ids = ids.join(",");
            $('#fourTypeId').val(ids);
            $('#myModal').modal('hide');
        }else{
            layer.msg('操作失败');
            $('#myModal').modal('hide');
        }
    }else{
        layer.msg('操作失败');
        $('#myModal').modal('hide');
    }
}

/*一级分类中选择input*/
function checkThisOne(obj){
    $('.checkOneImg').attr('src','/images/qpxm/pc/product/noCheck.png');
    $(obj).find('.checkOneImg').attr('src','/images/qpxm/pc/product/yesCheck.png');
}
/*二级分类中点击选中当前input*/
function checkThis(obj){
    $('.checkImg').attr('src','/images/qpxm/pc/product/noCheck.png');
    $(obj).find('.checkImg').attr('src','/images/qpxm/pc/product/yesCheck.png');
}
/*手动点击选择*/
function activeDiv(obj){
    $('.swiper-slide').removeClass('swiper-slide-active');
    $(obj).addClass('swiper-slide-active');
    var pid = $(obj).attr('data_id');
    erJiShow(pid);
}

function xiangZuo(){
    var oldL = activeWZ();
    mySwiper.slidePrev();
    var newL = activeWZ();
    $('.swiper-slide').removeClass('swiper-slide-active');
    if(oldL < (newL+7)){
        $('.swiper-slide').eq(oldL).addClass('swiper-slide-active');
    }else{
        $('.swiper-slide').eq(newL+6).addClass('swiper-slide-active');
    }
    var pid = $('.swiper-slide-active').attr('data_id');
    erJiShow(pid);
}
function xiangYou(){
    $('.swiper-slide-active').removeClass('swiper-slide-active');
    var oldL = activeWZ();
    mySwiper.slideNext();
    var newL = activeWZ();
    if(oldL > newL){
        $('.swiper-slide').eq(oldL).addClass('swiper-slide-active');
    }
    var pid = $('.swiper-slide-active').attr('data_id');
    erJiShow(pid);
}
/*获取当前选中的位置*/
function activeWZ(){
    var len = $('.swiper-slide').length;
    var l   = '';
    for(var i=0; i<len; ++i){
        if($('.swiper-slide').eq(i).hasClass('swiper-slide-active')){
            l = i;
            break;
        }
    }
    return l;
}
/*弹出分类选择框*/
function layerKuang1(){
    $('#myModal').modal('show');
}
/*点击下一步*/
function nextBu(){
    $('.carShopTypes').removeClass('swiper-slide');
    $('.carShopTypes').hide();  //隐藏所有一级分类
    $('.carThreeType').hide();  //隐藏所有二级分类
    var type = $('.shangJiaType:checked').attr('data-id');
    var oneClass = '.carTypesShop'+type;    //一级分类的class
    var twoClass = '.carThreeTypeDiv'+type; //二级分类的class
    $('.oneBu').hide();         //隐藏第一步
    $('.twoBu').show();
    $(oneClass).show();         //显示第二部中对应的一级分类
    $(oneClass).addClass('swiper-slide');
    $(twoClass).show();         //显示第二部中对应的一级分类(所有数据(未筛选))
    mySwiper = new Swiper('#aaa',{
        autoplay : false,//可选选项，自动滑动
        loop : false,//可选选项，开启循环
        slidesPerView : 7//个数
    });
    $('.swiper-slide').eq(0).click();
    $('.swiper-wrapper').css('transition-duration','0ms');
    $('.swiper-wrapper').css('transform','translate3d(0px, 0px, 0px)');

}
function erJiShow(pid){
    $('.upImgsListDiv').hide();
    var type = $('.shangJiaType:checked').attr('data-id');
    var twoClass = '.carThreeTypeDiv'+type; //二级分类的class
    $(twoClass).find('.carThreeTypeList'+pid).show();
}
/*第二步中点击上一步*/
function prevBu(){
    $('.oneBu').show();
    $('.twoBu').hide();
}
/*第二步中的下一步*/
function nextNextBu(){
    var type = $('.shangJiaType:checked').attr('data-id');
    var types = $('.carThreeTypeDiv'+type).find("input[name='types']:checked").val();
    if(types){
        $.ajax({
            url:"/pc.product/getThreeAndFourByTwo",    //请求的url地址
            dataType:"json",   //返回格式为json
            async:true,//请求是否异步，默认为异步，这也是ajax重要特性
            data:{"type":type,"id":types},    //参数值
            type:"POST",   //请求方式
            success:function(req){
                $('#threeFourType').html('');
                if(req.status == 1){
                    if(req.list.length>0){
                        for(var i=0; i<req.list.length; ++i){
                            var s= '';
                            s += '<div class="alertType" data_pid="'+ req.list[i]['pid'] +'" data_id="'+ req.list[i]['id'] +'">'+ req.list[i]['name'] +'</div>';
                            if(req.list[i]['child'].length>0){
                                for(var j=0; j<req.list[i]['child'].length; ++j){
                                    s += '<div class="alertListDiv" data_pid="'+ req.list[i]['child'][j]['pid'] +'" data_id="'+ req.list[i]['child'][j]['id'] +'">';
                                    s += '<label class="alertLabel">';
                                    s += '<div class="floLeft">'+ req.list[i]['child'][j]['name'] +'</div>';
                                    s += '<div class="alertCheckDiv">';
                                    s += '<input type="radio" name="type" class="alertCheck">';
                                    s += '</div>';
                                    s += '</label>';
                                    s += '<div style="clear: both;"></div>';
                                    s += '</div>';
                                }
                                $('#threeFourType').append(s);
                            }
                        }
                    }else{
                        var str = '<div style="text-align: center;font-size: 18px;margin-top: 50px;">暂时没有对应的数据...</div>';
                        $('#threeFourType').append(str);
                    }
                    $('.oneBu').hide();
                    $('.twoBu').hide();
                    $('.threeBu').show();
                }else{
                    layer.msg(req.msg);
                    return false;
                }
            },
            error:function(){
                layer.msg('出错了');
            }
        });
    }else{
        layer.msg('请选择一种分类');
        return false;
    }
}
/*第三步中的上一步*/
function prevprevBu(){
    $('.oneBu').hide();
    $('.twoBu').show();
    $('.threeBu').hide();
}

/*删除分类列*/
function delQingDan(obj){
    $(obj).parent().parent().remove();
}

function companyType(obj){
    var token = $(obj).attr('token');
    console.log(token);
    if(!token){
        var len = $('.shiXiaoList').length;
        for(var i=0; i<len; ++i){
            $('.shiXiaoList').eq(i).find('a').attr('token','');
            $('.shiXiaoList').eq(i).find('img').attr('src','/images/qpxm/pc/person/vip/noXuan.png');
        }
        $(obj).find('img').attr('src','/images/qpxm/pc/person/vip/yesXuan.png');
        $(obj).attr('token',1);
    }
}