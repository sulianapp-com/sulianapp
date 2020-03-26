<div class="ulleft-nav">
<div class="addtit-name"><a  class="btn btn-success ng-scope"  href="{{yzWebUrl('goods.goods.create')}}"><i class="fa fa-plus"></i> 发布商品</a></div>
<ul class="nav nav-tabs">
    <li class="active" ><a href="{{yzWebUrl('goods.goods.index')}}">出售中商品</a></li>
    <li ><a href="{{yzWebUrl('shop/goods', array('status'=>0))}}">已下架商品</a></li>
    <li ><a href="{{yzWebUrl('goods.category.index')}}">商品分类</a></li>
    <li ><a href="{{yzWebUrl('goods.brand.index')}}">商品品牌</a></li>
    <li ><a href="{{yzWebUrl('goods.comment.index')}}">评价管理</a></li>
    <li class="step"></li>

</ul>
</div>
