//分页插件
/**
 2014-08-05 ch
 **/
(function($){
    var ms = {
        init:function(obj,args){
            return (function(){
                ms.fillHtml(obj,args);
                ms.bindEvent(obj,args);
            })();
        },
        //填充html
        fillHtml:function(obj,args){
            return (function(){
                obj.empty();
                //将传过来的值转换为整型
                args.pageCount = parseInt(args.pageCount);
                args.current = parseInt(args.current);
                //上一页
                if(args.current > 1){
                    //obj.append('<button class="btn btn-white previous" type="button"><i class="fa fa-chevron-left"></i></button>');
                    obj.append('<button class="btn btn-white shouYe" style="margin-right: 7px;" type="button">首页</button><button class="btn btn-white previous" type="button">上一页</button>');
                }else{
                    obj.remove('.shouYe');
                    obj.append('<button class="btn btn-white disabled" style="margin-right: 7px;" type="button">首页</button><button class="btn btn-white disabled" type="button">上一页</button>');
                }

                //中间页码
                if(args.current != 1 && args.current >= 4 && args.pageCount != 4){
                    obj.append('<button class="btn btn-white tcdNumber">'+1+'</button>');
                }

                if(args.current-2 > 2 && args.current <= args.pageCount && args.pageCount > 5){
                    obj.append('<button class="btn btn-white">...</button>');
                }
                var start = args.current -2,end = args.current+2;
                if((start > 1 && args.current < 4)||args.current == 1){
                    end++;
                }
                if(args.current > args.pageCount-4 && args.current >= args.pageCount){
                    start--;
                }
                for (;start <= end; start++) {
                    if(start <= args.pageCount && start >= 1){
                        if(start != args.current){
                            obj.append('<button class="btn btn-white tcdNumber 2">'+ start +'</button>');
                        }else{
                            obj.append('<button class="btn btn-primary active">'+ start +'</button>');
                        }
                    }
                }
                if(args.current + 2 < args.pageCount - 1 && args.current >= 1 && args.pageCount>5){
                    obj.append('<button class="btn btn-white">...</button>');
                }
                if(args.current != args.pageCount && args.current < args.pageCount -2  && args.pageCount != 4){
                    obj.append('<button class="btn btn-white tcdNumber">'+args.pageCount+'</button>');
                }

                //下一页
                if(args.current < args.pageCount){
                    obj.append('<button class="btn btn-white next" type="button">下一页</button><button class="btn btn-white zuiHou" style="margin-left: 7px;" type="button">末页</button>');
                }else{
                    obj.remove('.zuiHou');
                    obj.append('<button class="btn btn-white disabled" type="button">下一页</button><button class="btn btn-white disabled" style="margin-left: 7px;" type="button">末页</button>');
                }
            })();
        },
        //绑定事件
        bindEvent:function(obj,args){
            return (function(){
                obj.off('click',"button.tcdNumber");
                obj.on("click","button.tcdNumber",function(){
                    var current = parseInt($(this).text());
                    ms.fillHtml(obj,{"current":current,"pageCount":args.pageCount});
                    if(typeof(args.backFn)=="function"){
                        args.backFn(current);
                    }
                });
                //首页
                obj.off('click',"button.shouYe");
                obj.on("click","button.shouYe",function(){
                    //var current = parseInt(obj.children("button.active").text());
                    ms.fillHtml(obj,{"current":1,"pageCount":args.pageCount});
                    //if(typeof(args.backFn)=="function"){
                    //    args.backFn(current-1);
                    //}
                });
                //上一页
                obj.off('click',"button.previous");
                obj.on("click","button.previous",function(){
                    var current = parseInt(obj.children("button.active").text());
                    ms.fillHtml(obj,{"current":current-1,"pageCount":args.pageCount});
                    if(typeof(args.backFn)=="function"){
                        args.backFn(current-1);
                    }
                });
                //下一页
                obj.off('click',"button.next");
                obj.on("click","button.next",function(){
                    var current = parseInt(obj.children("button.active").text());
                    ms.fillHtml(obj,{"current":current+1,"pageCount":args.pageCount});
                    if(typeof(args.backFn)=="function"){
                        args.backFn(current+1);
                    }
                });
                //末页
                obj.off('click',"button.zuiHou");
                obj.on("click","button.zuiHou",function(){
                    //var current = parseInt(obj.children("button.active").text());
                    //console.log(current);
                    ms.fillHtml(obj,{"current":args.pageCount,"pageCount":args.pageCount});
                    //if(typeof(args.backFn)=="function"){
                    //    args.backFn(current+1);
                    //}
                });
            })();
        }
    };

    $.fn.createPage = function(options){
        var args = $.extend({
            pageCount : 10,
            current : 1,
            backFn : function(){}
        },options);
        ms.init(this,args);
    }
})(jQuery);

//代码整理：懒人之家 www.lanrenzhijia.com