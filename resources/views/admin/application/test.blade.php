<!DOCTYPE html>
<html>
<head>
    <title>阿里OSS</title>
    <meta charset="utf-8"/>
</head>
<body>

    <form class="form-horizontal" method="post" enctype="multipart/form-data" action="/index.php/admin/application/temp/">
<!-- <form name=theform > -->
    <input type="radio" name="myradio" value="local_name" checked=true/> 上传文件名字保持本地文件名字
    <input type="radio" name="myradio" value="random_name"/> 上传文件名字是随机文件名, 后缀保留
</form>

<h4>您所选择的文件列表：</h4>
<div id="ossfile">你的浏览器不支持flash,Silverlight或者HTML5！</div>

<br/>


<div id="container">
    <a id="selectfiles" href="javascript:void(0);" class='btn'>选择文件</a>
    <a id="postfiles" href="javascript:void(0);" class='btn'>开始上传</a>
</div>

<pre id="console"></pre>

<p>&nbsp;</p>

<script type="text/javascript" src="{{ asset('assets/plupload/js/plupload.full.min.js') }}"></script>
<script>
    var expire = 0; //初始化过期时间

    /**
     * 发送ajax请求的函数
     * @returns Json 服务器返回的签名
     */
    function send_request() {
        var xmlhttp = null;
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (xmlhttp != null) {
            var serverUrl = '{{ url('upload/key?dir=') }}';
            xmlhttp.open("GET", serverUrl, false);
            xmlhttp.send(null);
            return xmlhttp.responseText;
        } else {
            alert("Your browser does not support XMLHTTP.");
        }
    }

    /**
     * 选择服务器端保存文件名的方式
     */
    function check_object_radio() {
        var tt = document.getElementsByName('myradio');
        for (var i = 0; i < tt.length; i++) {
            if (tt[i].checked) {
                g_object_name_type = tt[i].value;
                break;
            }
        }
    }

    /**
     * 从服务器获取签名之后,定义全局变量
     * @returns {boolean}
     */
    function get_signature() {
        //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
        now = timestamp = Date.parse(new Date()) / 1000;
        if (expire < now + 3) {
            body = send_request(); //发送ajax请求
            var obj = eval("(" + body + ")");
            //定义全局变量,值为服务器返回来的值
            host = obj['host'];
            policyBase64 = obj['policy'];
            accessid = obj['accessid'];
            signature = obj['signature'];
            expire = parseInt(obj['expire']);
            callbackbody = obj['callback'];
            key = obj['dir'];
            return true;
        }
        return false;
    }

    /**
     * 生成随机文件名,不一定能唯一
     * @param len  名字的长度
     * @returns {string}
     */
    function random_string(len) {
        len = len || 32;
        var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
        var maxPos = chars.length;
        var pwd = '';
        for (i = 0; i < len; i++) {
            pwd += chars.charAt(Math.floor(Math.random() * maxPos));
        }
        return pwd;
    }

    /**
     *
     * @param filename
     * @returns {string}
     */
    function get_suffix(filename) {
        pos = filename.lastIndexOf('.');
        suffix = '';
        if (pos != -1) {
            suffix = filename.substring(pos)
        }
        return suffix;
    }

    /**
     * 根据选择文件名的方式处理文件名
     * @param filename
     * @returns {string}
     */
    function calculate_object_name(filename) {
        if (g_object_name_type == 'local_name') {
            g_object_name += "${filename}"
        }
        else if (g_object_name_type == 'random_name') {
            suffix = get_suffix(filename);
            g_object_name = key + random_string(10) + suffix
        }
        return '';
    }

    //获取上传文件的名字
    function get_uploaded_object_name(filename) {
        if (g_object_name_type == 'local_name') {
            tmp_name = g_object_name;
            tmp_name = tmp_name.replace("${filename}", filename);
            return tmp_name;
        }
        else if (g_object_name_type == 'random_name') {
            return g_object_name;
        }
    }

    //设置文件上传到OSS需要的参数
    function set_upload_param(up, filename, ret) {
        if (ret == false) {  //如果没有签名,去请求请求签名
            ret = get_signature()
        }
        g_object_name = key; //
        if (filename != '') {
            suffix = get_suffix(filename);
            calculate_object_name(filename)
        }
        new_multipart_params = {
            'key': g_object_name,
            'policy': policyBase64,
            'OSSAccessKeyId': accessid,
            'success_action_status': '200', //让服务端返回200,不然，默认会返回204
            'callback': callbackbody,
            'signature': signature
        };

        up.setOption({
            'url': host,
            'multipart_params': new_multipart_params
        });

        up.start();
    }

    //文件上传对象
    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',  //设置支持上传的协议
        browse_button: 'selectfiles',
        //multi_selection: false,       //是否允许多文件上传
        container: document.getElementById('container'),
        flash_swf_url: '{{ asset('plupload/js/Moxie.swf') }}',
        silverlight_xap_url: '{{ asset('plupload/js/Moxie.xap') }}',
        url: 'http://oss.aliyuncs.com',

        //文件过滤规则
        filters: {
            mime_types: [ //只允许上传图片和zip文件
                {title: "Image files", extensions: "jpg,gif,png,bmp"},
                {title: "Zip files", extensions: "zip,rar"}
            ],
            max_file_size: '3mb', //最大只能上传3mb的文件
            prevent_duplicates: true //不允许选取重复文件
        },

        //执行上传的对象
        init: {
            //一初始化就执行的函数
            PostInit: function () {
                document.getElementById('ossfile').innerHTML = '';
                document.getElementById('postfiles').onclick = function () {
                    set_upload_param(uploader, '', false);
                    return false;
                };
            },

            //一选择文件就执行的函数
            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    document.getElementById('ossfile').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ')<b></b>'
                            + '<div class="progress"><div class="progress-bar" style="width: 0%"></div></div>'
                            + '</div>';
                });
            },

            //上传之前执行的函数
            BeforeUpload: function (up, file) {
                check_object_radio();  //选择文件名
                set_upload_param(up, file.name, true);  //设置上传参数
            },

            //上传进度条
            UploadProgress: function (up, file) {
                var d = document.getElementById(file.id);
                d.getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                var prog = d.getElementsByTagName('div')[0];
                var progBar = prog.getElementsByTagName('div')[0]
                progBar.style.width = 2 * file.percent + 'px';
                progBar.setAttribute('aria-valuenow', file.percent);
            },

            //执行文件上传
            FileUploaded: function (up, file, info) {
                if (info.status == 200) {
                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = 'upload to oss success, object name:' + get_uploaded_object_name(file.name) + ' 回调服务器返回的内容是:' + info.response;
                }
                else if (info.status == 203) {
                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '上传到OSS成功，但是oss访问用户设置的上传回调服务器失败，失败原因是:' + info.response;
                }
                else {
                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = info.response;
                }
            },

            //报错信息
            Error: function (up, err) {
                if (err.code == -600) {
                    document.getElementById('console').appendChild(document.createTextNode("\n选择的文件太大了,可以根据应用情况，在upload.js 设置一下上传的最大大小"));
                }
                else if (err.code == -601) {
                    document.getElementById('console').appendChild(document.createTextNode("\n选择的文件后缀不对,可以根据应用情况，在upload.js进行设置可允许的上传文件类型"));
                }
                else if (err.code == -602) {
                    document.getElementById('console').appendChild(document.createTextNode("\n这个文件已经上传过一遍了"));
                }
                else {
                    document.getElementById('console').appendChild(document.createTextNode("\nError xml:" + err.response));
                }
            }
        }
    });

    uploader.init();

</script>
</body>
</html>