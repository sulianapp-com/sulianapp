<ul class="treeview-menu {{in_array($item,\app\backend\modules\menu\Menu::current()->getCurrentItems()) ? 'menu-open' : ''}}">
    @foreach($childs as $key=>$value)
        @if(can($key))
            @if(isset($value['child']) && array_child_kv_exists($value['child'],'menu',1))
                <li class="{{in_array($key,\app\backend\modules\menu\Menu::current()->getCurrentItems()) ? 'active' : ''}}">
                    <a href="javascript:void(0);">
                        <i class="fa {{array_get($value,'icon','fa-circle-o') ?: 'fa-circle-o'}}"></i>
                        <span>{{$value['name'] or ''}}</span>
                        <span class="pull-right-container">
                           <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    @include('layouts.childMenu',['childs'=>$value['child']])
                </li>
            @elseif($value['menu'] == 1)
                <li class="{{in_array($key,\app\backend\modules\menu\Menu::current()->getCurrentItems()) ? 'active' : ''}}">
                    <a href="{{isset($value['url']) ? yzWebFullUrl($value['url']) : ''}}{{$value['url_params'] or ''}}">
                        <i class="fa {{array_get($value,'icon','fa-circle-o') ?: 'fa-circle-o'}}"></i>
                        <span>{{$value['name'] or ''}}</span>
                    </a>
                </li>
            @endif
        @endif
    @endforeach
</ul>
