<div class="panel panel-info">
    <ul class="add-shopnav">
        <li @if(\YunShop::request()->route == 'setting.shop.index') class="active" @endif ><a href="{{ yzWebUrl('setting.shop.index')}}">商城设置</a></li>
        <li @if(\YunShop::request()->route == 'setting.shop.member') class="active" @endif ><a href="{{ yzWebUrl('setting.shop.member')}}">会员设置</a></li>
        <li @if(\YunShop::request()->route == 'setting.shop.order') class="active" @endif ><a href="{{ yzWebUrl('setting.shop.order')}}">订单设置</a></li>
        {{--<li @if(\YunShop::request()->route == 'setting.shop.temp') class="active" @endif ><a href="{{ yzWebUrl('setting.shop.temp')}}">模板设置</a></li>--}}
        <li @if(\YunShop::request()->route == 'setting.shop.category') class="active" @endif><a href="{{ yzWebUrl('setting.shop.category')}}">分类层级</a></li>
        <li @if(\YunShop::request()->route == 'setting.shop.contact') class="active" @endif><a href="{{ yzWebUrl('setting.shop.contact')}}">联系方式</a></li>
        <li @if(\YunShop::request()->route == 'setting.shop.sms') class="active" @endif> <a href="{{ yzWebUrl('setting.shop.sms')}}">短信设置</a></li>
        <li @if(\YunShop::request()->route == 'setting.slide') class="active" @endif> <a href="{{ yzWebUrl('setting.slide')}}">幻灯片</a></li>
        <li @if(\YunShop::request()->route == 'setting.coupon.index') class="active" @endif> <a href="{{ yzWebUrl('setting.coupon.index')}}">优惠券</a></li>
        <li @if(\YunShop::request()->route == 'setting.form.index') class="active" @endif> <a href="{{ yzWebUrl('setting.form.index')}}">会员资料自定义表单</a></li>
        {{--<li ><a href="{php echo $this->createWebUrl('sysset',array('op'=>'shop'))}">公告管理</a></li>
        <li ><a href="{php echo $this->createWebUrl('sysset',array('op'=>'shop'))}">幻灯片管理</a></li>
        <li ><a href="{php echo $this->createWebUrl('sysset',array('op'=>'shop'))}">广告管理</a></li>--}}
        <li @if(\YunShop::request()->route == 'setting.shop.protocol') class="active" @endif> <a href="{{ yzWebUrl('setting.shop.protocol')}}">注册协议</a></li>
    </ul>
</div>