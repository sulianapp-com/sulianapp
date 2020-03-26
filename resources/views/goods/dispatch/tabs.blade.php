<div class="ulleft-nav">
<div class="addtit-name"> 配送方式及运费</div>
<ul class="nav nav-tabs">
    {ifp 'shop.dispatch.view'}<li><a href="{php echo $this->createWebUrl('goods.dispatch.index');}">运费模板设置</a></li>{/if}
    {ifp 'shop.refundaddress.view'}<li><a href="{php echo $this->createWebUrl('shop/refundaddress', array('status'=>0))}">退货地址设置</a></li>{/if}
    <li class="step"></li>
    {ifp 'verify.keyword'}<li><a href="{php echo $this->createPluginWebUrl('verify/keyword')}">核销设置</a></li>{/if}
    {ifp 'verify.store'}<li><a href="{php echo $this->createPluginWebUrl('verify/store')}">核销门店管理</a></li>{/if}
    {ifp 'verify.store'}<li><a href="{php echo $this->createPluginWebUrl('verify/category')}">门店分类管理</a></li>{/if}
    {ifp 'verify.saler'}<li><a href="{php echo $this->createPluginWebUrl('verify/saler')}">核销员管理</a></li>{/if}
    {ifp 'verify.withdraw'}<li><a href="{php echo $this->createPluginWebUrl('verify/withdraw')}">提現申請</a></li>{/if}

</ul>
</div>

