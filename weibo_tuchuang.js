window.onload = function(){
    var div = document.getElementById('weibo_tuchuang_post');
    var input = document.getElementById('weibo_tuchuang_input');
    var ed = null;
    if (document.getElementById('content') && document.getElementById('content').type == 'textarea'){
        ed = document.getElementById('content');
    }
    
    document.ondragenter = document.ondrop = document.ondragover = function(e){
        e.preventDefault();
        div.style.display = 'block';
    }
    
    div.ondragenter = function(e){
        div.innerHTML = '宝贝，现在可以放开我了~~~';
        e.preventDefault();
    }
    
    div.ondragleave = function(e){
        div.innerHTML = '讨厌，人家的深处在这里啦~~~';
        e.preventDefault();
    }
    
    div.ondragover = function(e){
        e.preventDefault();
    }
    
    var upLoad = function(file){
		var xhr = new XMLHttpRequest();
		var data;
        var upLoadFlag = true;

		if(upLoadFlag === false){
			alert('哎哟，人家正在上传啦~~~');
			return false;
		}
		if(!file){
			alert('您都没脱人家怎么上啊？');
			return false;
		}
		if(file && file.type.indexOf('image') === -1){
			alert('非图片文件什么的最讨厌了~~~');
			return false;
		}

		data = new FormData();
		data.append('weibo_tuchuang', file);
		xhr.open("POST", weibo_tuchuang_post_url);
        xhr.send(data);
		upLoadFlag = false;
		div.innerHTML = '人家正在上传啦~~~';
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4 && xhr.status == 200){
				upLoadFlag = true;
				div.innerHTML = '将图片拖拽到此区域上传';
				QTags.insertContent("\n"+xhr.responseText+"\n");
			}
		}
    }
    
    var dropHandler = function(e){
		var file;
		e.preventDefault();
		file = e.dataTransfer.files && e.dataTransfer.files[0];
		upLoad(file);
	}
    
    var inputFileHandler = function(){
		var file = input.files[0];
		upLoad(file);
	}
    
    document.body.addEventListener('drop', function(e) {
	　dropHandler(e);
	}, false);
    input.addEventListener('change', function() {
		inputFileHandler();
	}, false);
}