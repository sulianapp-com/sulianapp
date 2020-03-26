$(function(){
    var uploader = new plupload.Uploader({
        browse_button : 'browse', //触发文件选择对话框的按钮，为那个元素id
        url : '/admin/ad/upload', //服务器端的上传页面地址
        flash_swf_url : '/plugins/plupload/js/Moxie.swf', //swf文件，当需要使用swf方式进行上传时需要配置该参数
        silverlight_xap_url : '/plugins/plupload/js/Moxie.xap', //silverlight文件，当需要使用silverlight方式进行上传时需要配置该参数
        drop_element : 'drop',
        multi_selection:false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        filters: {
          mime_types : [ //只允许上传图片和zip文件
            { title : "Image files", extensions : "jpg,gif,png" }
          ],
          max_file_size : '400kb', //最大只能上传400kb的文件
          prevent_duplicates : true //不允许选取重复文件
        }
    });

    uploader.bind('FilesAdded',function(uploader,files){
        var file_name = files[0].name; //文件名
        //构造html来更新UI
        previewImage(files[0],function(imgsrc){
            $(".preview").attr('src',imgsrc);
        });
        uploader.start();
    });

    uploader.bind('FileUploaded',function(uploader,file,responseObject){
        $("input[name='image']").val(responseObject['response']);
    });
    uploader.init();

    function previewImage(file,callback){//file为plupload事件监听函数参数中的file对象,callback为预览图片准备完成的回调函数
        if(!file || !/image\//.test(file.type)) return; //确保文件是图片
        if(file.type=='image/gif'){//gif使用FileReader进行预览,因为mOxie.Image只支持jpg和png
            var fr = new mOxie.FileReader();
            fr.onload = function(){
                callback(fr.result);
                fr.destroy();
                fr = null;
            }
            fr.readAsDataURL(file.getSource());
        }else{
            var preloader = new mOxie.Image();
            preloader.onload = function() {
                preloader.downsize( 300, 300 );//先压缩一下要预览的图片,宽300，高300
                var imgsrc = preloader.type=='image/jpeg' ? preloader.getAsDataURL('image/jpeg',80) : preloader.getAsDataURL(); //得到图片src,实质为一个base64编码的数据
                callback && callback(imgsrc); //callback传入的参数为预览图片的url
                preloader.destroy();
                preloader = null;
            };
            preloader.load( file.getSource() );
        }   
    }
})