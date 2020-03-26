<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>简单的html5 File测试 for pic2base64</title> 
<style> 
</style> 
<script> 
    window.onload = function(){ 
        var input = document.getElementById("demo_input"); 
        var result= document.getElementById("result"); 
        var img_area = document.getElementById("img_area"); 
        if ( typeof(FileReader) === 'undefined' ){
            result.innerHTML = "抱歉，你的浏览器不支持 FileReader，请使用现代浏览器操作！"; 
            input.setAttribute('disabled','disabled'); 
        }else{
            input.addEventListener('change',readFile,false);
        } 
    }
    function readFile(){
        var file = this.files[0]; 
        // //这里我们判断下类型如果不是图片就返回 去掉就可以上传任意文件   
        // if(!/image\/\w+/.test(file.type)){
        //     alert("请确保文件为图像类型"); 
        //     return false; 
        // }
        var reader = new FileReader(); 
        reader.readAsDataURL(file); 
        console.log();
        reader.onload = function(e){ 
                result.innerHTML = this.result; 
                img_area.innerHTML = '<div class="sitetip">图片img标签展示：</div><img src="'+this.result+'" alt=""/>'; 
        }
    } 
</script> 
</head>

<body> 
    <form class="form-horizontal" method="post" enctype="multipart/form-data" action="/index.php/admin/application/test/">
    <input type="file" value="file" id="demo_input" /> 
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <textarea name="file" id="result" rows=30 cols=300></textarea> 
    <p id="img_area"></p> 
    <input type="submit" value="提交">
</form>
</body> 
</html>