<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>@yield('title') | {{YunShop::app()->account['name']}} - 后台管理系统</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="_token" content="{{ csrf_token() }}"/>
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{static_url('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{static_url('css/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" href="{{static_url('assets/css/demo.css')}}">
    <link rel="stylesheet" href="{{static_url('js/dist/select2/select2.css')}}">
    <link rel="stylesheet" href="{{static_url('css/honeySwitch.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{static_url('assets/css/material-dashboard.css?v=1.2.1')}}">
    <link rel="stylesheet" href="{{static_url('yunshop/libs/font-awesome/4.5.0/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{static_url('assets/css/google-font.css?family=Roboto:300,400,500,700|Material+Icons')}}" />

    <!-- Ionicons -->
    <link rel="stylesheet" href="{{static_url('yunshop/libs/ionicons/2.0.1/css/ionicons.min.css')}}">
    <!-- Theme style -->
    {{--<link rel="stylesheet" href="{{static_url('yunshop/dist/css/AdminLTE.css')}}">--}}

    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link rel="stylesheet" href="{{static_url('yunshop/dist/css/skins/skin-red.min.css')}}">
    <link href="{{static_url('resource/css/common.css')}}" rel="stylesheet">


    {{--loding--}}
    {{--<link href="{{static_url('yunshop/dist/css/load/load.css')}}" rel="stylesheet">--}}
    <link rel="stylesheet" type="text/css" href="{{static_url('css/webstyle.css')}}">
    @yield('css')
    {!! yz_header('admin') !!}

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>var require = { urlArgs: 'v={{time()}}' };</script>

    <script type="text/javascript">
        const protocolStr = document.location.protocol;
        switch (protocolStr) {
            case 'https:':
                // 指定https访问类型，具体见百度地图API加载方式：http://www.jiazhengblog.com/blog/2011/06/28/284/
                window.HOST_TYPE = '2'
                break
            default:
                break
        }

      if(navigator.appName == 'Microsoft Internet Explorer'){
        if(navigator.userAgent.indexOf("MSIE 5.0")>0 ||
          navigator.userAgent.indexOf("MSIE 6.0")>0 ||
          navigator.userAgent.indexOf("MSIE 7.0")>0)
        {
          alert('您使用的 IE 浏览器版本过低, 推荐使用 Chrome 浏览器或 IE8 及以上版本浏览器.');
        }
      }

      window.sysinfo = {
        'is_new': '{{IMS_FAMILY == "v" ? (IMS_VERSION > 1.4 ? 1:0):1}}',
        'uniacid': '{{YunShop::app()->uniacid}}',
        'acid': '{{YunShop::app()->acid}}',
        'openid': '{{YunShop::app()->openid}}',
        'uid': '{{YunShop::app()->uid}}',
        'siteroot': '{!! YunShop::app()->siteroot !!}',
        'static_url': '{{static_url('')}}',
        'siteurl': '{!! YunShop::app()->siteurl !!}',
        'attachurl': '{{YunShop::app()->attachurl}}',
        'attachurl_local': '{{YunShop::app()->attachurl_local}}',
        'attachurl_remote': '{{YunShop::app()->attachurl_remote}}',

        'cookie' : {'pre': '{{YunShop::app()->config['cookie']['pre']}}'},
        'get_address' : '{!! yzWebUrl("address.get-address") !!}'

      };

    </script>

    <!-- jQuery 2.2.0 -->
    <script src="{{static_url('js/jquery-2.2.3.min.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/general.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/clipboard.min.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/bootstrap-switch.min.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/select2/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/honeySwitch.js')}}"></script>

{{--    <script src="{{static_url('assets/js/bootstrap.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{static_url('assets/js/material.min.js')}}" type="text/javascript"></script>

    {{--<script src="//vuejs.org/js/vue.min.js"></script>--}}
    <script src="{{ static_url('yunshop/vue/js/vue.min.js') }}"></script>
    {{--<script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>--}}
    <script src="{{ static_url('yunshop/vue/js/vue.js') }}"></script>
    <script src="{{static_url('js/vue-count-to.min.js')}}" type="text/javascript"></script>
    {{--<script src="https://cdn.bootcss.com/vue-resource/1.5.0/vue-resource.js"></script>--}}
    <script src="{{ static_url('yunshop/vue/js/vue-resource.js') }}"></script>

    {{--<link href="https://cdn.bootcss.com/element-ui/2.3.1/theme-chalk/index.css" rel="stylesheet">--}}
    <link href="{{ static_url('yunshop/element-ui/2.10.1/css/element-ui_2.10.1_theme-chalk_index.css') }}" rel="stylesheet">
    {{--<script src="https://cdn.bootcss.com/element-ui/2.4.0/index.js"></script>--}}
    <script src="{{ static_url('yunshop/element-ui/2.10.1/js/element-ui_2.10.1_index.js') }}"></script>

    <script src="{{static_url('assets/js/perfect-scrollbar.jquery.min.js')}}" type="text/javascript"></script>
    <!-- Library for adding dinamically elements -->
    <script src="{{static_url('assets/js/arrive.min.js')}}" type="text/javascript"></script>
    <!-- Forms Validations Plugin -->
    <script src="{{static_url('assets/js/jquery.validate.min.js')}}"></script>
    <!-- Promise Library for SweetAlert2 working on IE -->
    <script src="{{static_url('assets/js/es6-promise-auto.min.js')}}"></script>
    <!--  Plugin for Date Time Picker and Full Calendar Plugin-->
    <script src="{{static_url('assets/js/moment.min.js')}}"></script>
    <!--  Charts Plugin, full documentation here: https://gionkunz.github.io/chartist-js/ -->
    <script src="{{static_url('assets/js/chartist.min.js')}}"></script>
    <!--  Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
    <script src="{{static_url('assets/js/jquery.bootstrap-wizard.js')}}"></script>
    <!--  Notifications Plugin, full documentation here: http://bootstrap-notify.remabledesigns.com/    -->
    <script src="{{static_url('assets/js/bootstrap-notify.js')}}"></script>
    <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
    <script src="{{static_url('assets/js/bootstrap-datetimepicker.js')}}"></script>
    <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
    <script src="{{static_url('assets/js/jquery-jvectormap.js')}}"></script>
    <!-- Sliders Plugin, full documentation here: https://refreshless.com/nouislider/ -->
    <script src="{{static_url('assets/js/nouislider.min.js')}}"></script>
    <!--  Google Maps Plugin    -->
    <!--  Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
    <script src="{{static_url('assets/js/jquery.select-bootstrap.js')}}"></script>
    <!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
    <script src="{{static_url('assets/js/jquery.datatables.js')}}"></script>
    <!-- Sweet Alert 2 plugin, full documentation here: https://limonte.github.io/sweetalert2/ -->
    <script src="{{static_url('assets/js/sweetalert2.js')}}"></script>
    <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
    <script src="{{static_url('assets/js/jasny-bootstrap.min.js')}}"></script>
    <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
    <script src="{{static_url('assets/js/fullcalendar.min.js')}}"></script>
    <!-- Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
    <script src="{{static_url('assets/js/jquery.tagsinput.js')}}"></script>
    <!-- Material Dashboard javascript methods -->
{{--    <script src="{{static_url('assets/js/material-dashboard.js')}}"></script>--}}

    {{--<script src="{{static_url('js/echarts.js')}}" type="text/javascript"></script>--}}

    <!-- import iView -->
    {{--<script src="https://cdn.bootcss.com/axios/0.18.0/axios.min.js"></script>--}}
    <script src="{{ static_url('yunshop/axios/js/axios_0.18.0_axios.min.js') }}"></script>
    {{--<script src="https://cdn.bootcss.com/iview/2.14.0/iview.min.js"></script>--}}
    <script src="{{ static_url('yunshop/iview/js/iview_2.14.0_iview.min.js') }}"></script>
    {{--<link href="https://cdn.bootcss.com/iview/2.14.0/styles/iview.css" rel="stylesheet">--}}
{{--    <link href="{{ static_url('yunshop/iview/css/iview_2.14.0_styles_iview.css') }}" rel="stylesheet">--}}

    @php $util_js = 'util'; @endphp
    @if (config('app.framework') == 'platform')
        @php $util_js = 'utils'; @endphp
    @endif
    <script type="text/javascript" src="{{static_url('resource/js/app/'.$util_js.'.js?time=3232')}}"></script>
    <script type="text/javascript" src="{{static_url('resource/js/require.js')}}"></script>
    @section('utilJs')
        <script type="text/javascript">
            u_url = 'static/resource/js/app/';
            util_url = '';
            util_js = 'util';
            @if (config('app.framework') == 'platform')
                util_js = 'utils';
                util_url = '/' + u_url + util_js;
                require.config({
                    paths:{
                        utils:util_url
                    }
                });
            @else
                util_url = '/addons/yun_shop/' + u_url + util_js;
                require.config({
                    paths:{
                        util:util_url
                    }
                });
            @endif
        </script>
    @show
    @if (config('app.framework') == 'platform')
    <script type="text/javascript" src="{{static_url('js/app/yz_config.js')}}"></script>
    @else
        <script type="text/javascript" src="{{static_url('js/app/config.js')}}"></script>
    @endif
    <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

</head>

<body>
<div id="loading">
    <div id="loading-center">
        <div id="loading-center-absolute">
            <div class="object" id="object_four"></div>
            <div class="object" id="object_three"></div>
            <div class="object" id="object_two"></div>
            <div class="object" id="object_one"></div>
        </div>
    </div>
</div>
<div class="wrapper">

    <!-- Main Header -->
    {{--@php--}}
        {{--global $_W;--}}
        {{--$menu_list = \app\common\helpers\Cache::get('menu_list'.$_W['uid']);--}}
    {{--@endphp--}}
    {{--@if (!\app\common\helpers\Cache::has('menu_list'.$_W['uid']))--}}
        {{--@php \Log::debug('----缓存顶端和左端 menu----'); ob_start(); @endphp--}}
    @include('layouts.mainSidebar')
    @include('layouts.mainMenu')
    {{--@php--}}
        {{--$cache['menu'] = ob_get_contents();--}}
        {{--$cache['uid'] = $_W['uid'];--}}
        {{--\app\common\helpers\Cache::forever('menu_list'.$_W['uid'], $cache);--}}
        {{--ob_implicit_flush(false);--}}
    {{--@endphp--}}
    {{--@else--}}
        {{--@php \Log::debug('----读取顶端和左端 menu----'); echo $menu_list['menu']; @endphp--}}
    {{--@endif--}}

    @if (\app\backend\modules\menu\Menu::current()->isShowSecondMenu())
        @include('layouts.secondSidebar')
    @else
        <style>
            .main-panel{
                width: calc(100% - 96px) ;
            }
            .vue-page{
                width: calc(100% - 116px) ;
            }
        </style>
    @endif
    @include('layouts.mainHeader')
            <!-- Left side column. contains the logo and sidebar -->

            <!-- Content Wrapper. Contains page content -->

    {{--<div class="content-wrapper">
        <section class="content-header">

            <h6>
            </h6>
        </section>
        @include('public.admin.message')
        <section class="content">
            @yield('content')
        </section>
    </div>--}}
    <!-- /.content-wrapper -->


    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    {{--<div class="control-sidebar-bg"></div>--}}



<!-- REQUIRED JS SCRIPTS -->

<!-- Bootstrap 3.3.6 -->
<!-- AdminLTE App -->
<script src="{{static_url('yunshop/dist/js/app.min.js')}}"></script>

<!-- dataTables -->
<script src="{{static_url('yunshop/dist/js/common.js')}}"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
@yield('js')
        <!-- Main Footer -->
@include('layouts.mainFooter')
{!! yz_footer('admin') !!}
</div>
<!-- ./wrapper -->
<script>
    if (Clipboard.isSupported()) {
        var clipboard =  new Clipboard('.js-clip');
        clipboard.on('success', function(e) {
            //alert('复制成功');
            swal({
                title: "复制成功",
                buttonsStyling: false,
                confirmButtonClass: "btn btn-success"
            });
            //swal('Any fool can use a computer')
            e.clearSelection();
        });
    } else {
        $('.js-clip').each(function () {
            util.clip(this, $(this).attr('data-url'));
        });
    }
    $(".yz-menu").click(function(){
        console.log('sidebar', $(".sidebar"));
        $(".sidebar").toggle();
    });
</script>

<script type='text/javascript'>
    function getkey(a,maxpage) {
        pms =  Number(document.getElementById('jump').value);
        inits = "{!! YunShop::app()->script_name . '?' . http_build_query($_REQUEST) !!}";
        reg=/page=(\w+)/gi;
        str=inits.match(reg);
        // console.log(pms);
        // console.log(maxpage)
        initsurl=inits.replace(reg,'page=');
        if(pms > maxpage){
            pms = maxpage;
        } else if(pms < 1){
            pms = 1;
        }
        a.href = initsurl + pms
    }
</script>
</body>
</html>
