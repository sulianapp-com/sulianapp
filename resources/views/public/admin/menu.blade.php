<!-- start: Main Menu -->
<style type="text/css">

    .nav.nav-sidebar li a .fa-angle-down{display: block !important;}
    .sidebar-left{}
</style>

<div class="sidebar sidebar-left">

    <div class="sidebar-collapse">
        <div class="sidebar-header t-center">
            <a><img class="text-logo" src="{{static_url('resource/images/logo.png')}}"></a>
        </div>
        <div class="sidebar-menu">

            <ul class="nav nav-sidebar">

                @foreach(\app\backend\modules\menu\Menu::current()->getItems() as $keyOne=>$valueOne)
                    @if(isset($valueOne['menu']) && $valueOne['menu'] == 1)
                <li>
                    @if(isset($valueOne['child']) && array_child_kv_exists($valueOne['child'],'menu',1))
                    <a href="javascript:;" i="1"><i class="fa {{$valueOne['icon'] or ''}}"></i><span class="text">{{$valueOne['name'] or ''}}</span> <span class="fa fa-angle-down pull-right"></span></a>
                    <ul class="nav sub">
                        @foreach($valueOne['child'] as $keyTwo=>$valueTwo)
                        <li>
                            @if(isset($valueTwo['child']) && array_child_kv_exists($valueTwo['child'],'menu',1))
                            <a href="javascript:;" i=2><i class="iconfont {{$valueTwo['icon'] or 'icon-dian'}}"></i><span class="text">{{$valueTwo['name'] or ''}}</span><span class="fa fa-angle-down pull-right"></span></a>
                            <ul class="nav sub third">
                                @foreach($valueTwo['child'] as $keyThird=>$valueThird)
                                <li><a href="{{yzWebFullUrl(isset($valueThird['url']) ?$valueThird['url']: '') or 'javascript:void(0)'}}"><i class="iconfont {{$valueThird['icon'] or 'icon-dian'}}"></i><span class="text"> {{$valueThird['name'] or ''}}</span></a></li>
                                @endforeach
                            </ul>
                                @else
                                <a href="{{yzWebFullUrl(isset($valueTwo['url']) ?$valueTwo['url']: '') or 'javascript:void(0)'}}"><i class="iconfont {{$valueTwo['icon'] or 'icon-dian'}}"></i><span class="text">{{$valueTwo['name'] or ''}}</span></a>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                        @else
                        <a href="{{yzWebFullUrl(isset($valueOne['url']) ?$valueOne['url']:'') or ''}}"><i class="fa {{$valueOne['icon'] or ''}}"></i><span class="text">{{$valueOne['name'] or ''}}</span></a>
                    @endif
                </li>
                    @endif
                @endforeach
            </ul>

        </div>
    </div>


</div>
<!-- end: Main Menu -->

<script language='javascript'>
  require(['bootstrap'], function ($) {
    $("[i]").click(function () {

      $(this).parent().parent().children('li').find('ul').slideUp();
      $(this).next().slideToggle();
    });
  })
</script>