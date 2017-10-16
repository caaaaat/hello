 //选取图片的来源，拍照和相册  
function showActionSheet(conf){  
  var divid = conf.id;  
      var actionbuttons=[{title:"拍照"},{title:"相册选取"}];  
      var actionstyle={title:"选择照片",cancel:"取消",buttons:actionbuttons};  
      plus.nativeUI.actionSheet(actionstyle, function(e){  
            if(e.index==1){  
                appendByCamera();
            }else if(e.index==2){  
                appendByGallery();  
            }  
      } );  
       }  

var files={};
// 上传文件
function upload(){
	if(files.length<=0){
		plus.nativeUI.alert("没有添加上传文件！");
		return;
	}
	loading('上传中');
	var task=plus.uploader.createUpload(imgUploadImg, 
		{method:"POST"},
		function(t,status){ //上传完成
			closeLoading();			
			if(status==200){
				sw.jcon(t.responseText);	
				var rData   = JSON.parse(t.responseText);
					sw.toast(rData.msg);
					
				var rStatus = rData.status;
					if(rStatus==200){
						$("#imgSrc").attr('src',imgUrl+rData.url);
						//将数据保存到本地
						plus.storage.setItem('face_pic',rData.url);
						//刷新父级标记
						plus.storage.setItem('pIImg','1');//父级刷新标记	
					}
			}else{
				sw.toast("上传失败："+status);				
			}
			
		}
	);
	task.addFile(files.path,{key:files.name});	
	task.start();
}
// 拍照添加文件
function appendByCamera(){
	plus.camera.getCamera().captureImage(function(p){
		appendFile(p);
	});	
}
// 从相册添加文件
function appendByGallery(){
	plus.gallery.pick(function(p){
        appendFile(p);
    });
}
// 添加文件
function appendFile(p){
	files = {name:"uploadkey",path:p};
	upload();
}
// 产生一个随机数
function getUid(){
	return Math.floor(Math.random()*100000000+10000000).toString();
}