<!-- sidebar: style can be found in sidebar.less -->
<!-- <section  class="sidebar" data-active-color="blue" style="background:#23232f" data-image="../assets/img/sidebar-1.jpg"> -->
<section class="sidebar" data-image="../assets/img/sidebar-1.jpg">
    <div class="sidebar-wrapper">
        <div style="width:96px;text-align:center;height:80px;">
            <img src="{{\Setting::get('shop.shop.logo') ? yz_tomedia(\Setting::get('shop.shop.logo')) : resource_absolute('static/assets/img/default-avatar.png')}}" style="border-radius:50%;width:40px;height:40px;margin:20px;" alt="">
        </div>
        {{--upload_image_local().\Setting::get('shop.shop.logo')--}}
        <ul class="nav">
            @if(in_array(\YunShop::app()->role,['founder','manager','owner']))
                <li class="{{in_array($key,\app\backend\modules\menu\Menu::current()->getCurrentItems()) ? 'active' : ''}}">
                    <a href="{{yzWebUrl('survey.survey.index')}}">
                        <i class="fa fa-archive"></i>
                        <span style=" margin-top: -5px;font-size:14px !important">概况</span>
                    </a>
                </li>
            @else
                <li class="{{in_array($key,\app\backend\modules\menu\Menu::current()->getCurrentItems()) ? 'active' : ''}}">
                    <a href="{{yzWebUrl('index.index')}}">
                        <i class="fa fa-home"></i>
                        <span style=" margin-top: -5px;font-size:14px !important">商城</span>
                    </a>
                </li>
            @endif
            @foreach(\app\backend\modules\menu\Menu::current()->getItems() as $key=>$value)

                @if(isset($value['menu']) && $value['menu'] == 1 && can($key) && $value['left_first_show'] == 1)
                    @if(isset($value['child']) && array_child_kv_exists($value['child'],'menu',1))
                        <li class="{{in_array($key,\app\backend\modules\menu\Menu::current()->getCurrentItems()) ? 'active' : ''}}">
                            <a href="{{ \app\common\services\MenuService::canAccess($key) }}">
                                {{--<a href="{{isset($value['url']) ? yzWebFullUrl($value['url']):''}}{{$value['url_params'] or ''}}">--}}
                                <i class="fa {{array_get($value,'icon','fa-circle-o') ?: 'fa-circle-o'}}"></i>
                                {{--<span class="pull-right-container">--}}
                                {{--<i class="fa fa-angle-left pull-right"></i>--}}
                                {{--</span>--}}
                                <span style=" margin-top: -5px;font-size:14px !important">{{$value['name']}}</span>
                            </a>
                            {{--@include('layouts.childMenu',['childs'=>$value['child'],'item'=>$key])--}}
                        </li>
                    @elseif($value['menu'] == 1)
                        <li class="{{in_array($key,\app\backend\modules\menu\Menu::current()->getCurrentItems()) ? 'active' : ''}}">
                            <a href="{{ \app\common\services\MenuService::canAccess($key) }}">
                                <i class="fa {{array_get($value,'icon','fa-circle-o') ?: 'fa-circle-o'}}"></i>
                                <span style=" margin-top: -5px;font-size:14px !important">{{$value['name'] or ''}}</span>
                            </a>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>
        {{--菜单结束--}}
    </div>
    <!-- Sidebar Menu -->

    <!-- /.sidebar-menu -->
</section>
