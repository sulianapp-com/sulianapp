<!-- mylink start -->
    <div id="modal-mylink" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" style="width: 720px;">
            <div class="modal-content">
                <div class="modal-header" style="padding: 5px;">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <ul class="nav nav-pills" role="tablist">
                        <li role="presentation" class="active" style="display: block;"><a aria-controls="link_system" role="tab" data-toggle="tab" href="#link_system" aria-expanded="true">系统页面</a></li>
                        <li role="presentation" style="display: block;"><a aria-controls="link_goods" role="tab" data-toggle="tab" href="#link_goods" aria-expanded="false">商品链接</a></li>
                        <li role="presentation" style="display: block;"><a aria-controls="link_cate" role="tab" data-toggle="tab" href="#link_cate" aria-expanded="false">商品分类</a></li>
                        @if (!empty($mylink_data['designer']))
                            <li role="presentation" style="display: block;"><a aria-controls="link_diy" role="tab" data-toggle="tab" href="#link_diy" aria-expanded="false">DIY页面</a></li>
                        @endif
                        <li role="presentation" style="display: block;"><a aria-controls="link_diy" role="tab" data-toggle="tab" href="#link_article" aria-expanded="false">营销文章</a></li>
                        @if (p('coupon'))
                        <li role="presentation" style="display: block;"><a aria-controls="link_diy" role="tab" data-toggle="tab" href="#link_article" aria-expanded="false">超级券页面</a></li>
                        @endif
                        <li role="presentation" style="display: block;"><a aria-controls="link_other" role="tab" data-toggle="tab" href="#link_other" aria-expanded="false">自定义链接</a></li>
                    </ul>   
                </div>
                 <div class="modal-body tab-content">
                     <div role="tabpanel" class="tab-pane link_system active" id="link_system">
                         <div class="mylink-con">
                            <div class="page-header">
                            <h4><i class="fa fa-folder-open-o"></i> 商城页面链接</h4>
                        </div>
                        <div id="fe-tab-link-li-11" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 11)" data-href="{{ yzWebUrl('shop.index') }}">商城首页</div>
                        <div id="fe-tab-link-li-12" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 12)" data-href="{php echo $this->createMobileUrl('shop/category')}">分类导航</div>
                        <div id="fe-tab-link-li-13" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 13)" data-href="{php echo $this->createMobileUrl('shop/list')}">全部商品</div>
                        <div id="fe-tab-link-li-14" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 14)" data-href="{php echo $this->createMobileUrl('shop/notice')}">公告页面</div>
                        <div class="page-header">
                            <h4><i class="fa fa-folder-open-o"></i> 会员中心链接</h4>
                        </div>
                        <div id="fe-tab-link-li-21" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 21)" data-href="{php echo $this->createMobileUrl('member')}">会员中心</div>
                        <div id="fe-tab-link-li-22" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 22)" data-href="{php echo $this->createMobileUrl('order')}">我的订单</div>
                        <div id="fe-tab-link-li-23" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 23)" data-href="{php echo $this->createMobileUrl('shop/cart')}">我的购物车</div>
                        <div id="fe-tab-link-li-24" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 24)" data-href="{php echo $this->createMobileUrl('shop/favorite')}">我的收藏</div>
                        <div id="fe-tab-link-li-25" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 25)" data-href="{php echo $this->createMobileUrl('shop/history')}">我的足迹</div>
                        <div id="fe-tab-link-li-26" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 26)" data-href="{php echo $this->createMobileUrl('member/recharge')}">会员充值</div>
                        <div id="fe-tab-link-li-27" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 27)" data-href="{php echo $this->createMobileUrl('member/log')}">余额明细</div>
                        <div id="fe-tab-link-li-28" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 28)" data-href="{php echo $this->createMobileUrl('member/withdraw')}">余额提现</div>
                        <div id="fe-tab-link-li-29" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 29)" data-href="{php echo $this->createMobileUrl('shop/address')}">我的收货地址</div>

                        <div class="page-header">
                            <h4><i class="fa fa-folder-open-o"></i> 分销链接</h4>
                        </div>
                        
                        <div id="fe-tab-link-li-31" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 31)" data-href="{php echo $this->createPluginMobileUrl('commission')}">分销中心</div>
                        <div id="fe-tab-link-li-32" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 32)" data-href="{php echo $this->createPluginMobileUrl('commission/register')}">成为分销商</div>
                        <div id="fe-tab-link-li-33" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 33)" data-href="{php echo $this->createPluginMobileUrl('commission/myshop')}">我的小店</div>
                        <div id="fe-tab-link-li-34" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 34)" data-href="{php echo $this->createPluginMobileUrl('commission/withdraw')}">分销佣金</div>
                        <div id="fe-tab-link-li-35" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 35)" data-href="{php echo $this->createPluginMobileUrl('commission/order')}">分销订单</div>
                        <div id="fe-tab-link-li-36" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 36)" data-href="{php echo $this->createPluginMobileUrl('commission/team')}">我的团队</div>
                        <div id="fe-tab-link-li-37" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 37)" data-href="{php echo $this->createPluginMobileUrl('commission/log')}">佣金明细</div>
                        <div id="fe-tab-link-li-38" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 38)" data-href="{php echo $this->createPluginMobileUrl('commission/myshop',array('op'=>'set'))}">小店设置</div>
                        <div id="fe-tab-link-li-39" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 39)" data-href="{php echo $this->createPluginMobileUrl('commission/myshop',array('op'=>'select'))}">自选商品</div>

                        <div class="page-header">
                            <h4><i class="fa fa-folder-open-o"></i> 分红链接</h4>
                        </div>
                        
                        <div id="fe-tab-link-li-40" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 40)" data-href="{php echo $this->createPluginMobileUrl('bonus')}">分红中心</div>
                        <div id="fe-tab-link-li-41" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 41)" data-href="{php echo $this->createPluginMobileUrl('bonus/withdraw')}">分红佣金</div>
                        <div id="fe-tab-link-li-42" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 42)" data-href="{php echo $this->createPluginMobileUrl('bonus/order')}">分红订单</div>
                        <div id="fe-tab-link-li-43" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 43)" data-href="{php echo $this->createPluginMobileUrl('bonus/team')}">我的下线</div>
                        <div id="fe-tab-link-li-44" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 44)" data-href="{php echo $this->createPluginMobileUrl('bonus/log')}">分红明细</div>
                        <div id="fe-tab-link-li-45" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 45)" data-href="{php echo $this->createPluginMobileUrl('bonus/ordercount_area')}">区域订单</div>


                        <div class="page-header">
                            <h4><i class="fa fa-folder-open-o"></i> 超级券链接</h4>
                        </div>
                        
                        <div id="fe-tab-link-li-46" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 46)" data-href="{php echo $this->createPluginMobileUrl('coupon')}">优惠券领取中心</div>
                        <div id="fe-tab-link-li-47" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 47)" data-href="{php echo $this->createPluginMobileUrl('coupon/my')}">我的优惠券</div>

                            <div class="page-header">
                                <h4><i class="fa fa-folder-open-o"></i> 其它插件页面</h4>
                            </div>
                            {{--@if (p('return'))
                            <div id="fe-tab-link-li-48" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 48)" data-href="{php echo $this->createPluginMobileUrl('return/return_log')}">全返明细</div>
                            @endif
                            @if (p('supplier'))
                            <div id="fe-tab-link-li-49" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 49)" data-href="{php echo $this->createPluginMobileUrl('supplier/af_supplier')}">供应商申请</div>
                             @endif
                            @if (p('ranking'))
                             <div id="fe-tab-link-li-50" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 50)" data-href="{php echo $this->createPluginMobileUrl('ranking/ranking')}">排行榜</div>
                            @endif
                            @if (p('creditshop'))
                             <div id="fe-tab-link-li-51" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 51)" data-href="{php echo $this->createPluginMobileUrl('creditshop')}">积分商城</div>
                             @endif--}}
                        </div>
                     </div>
                     <div role="tabpanel" class="tab-pane link_goods" id="link_goods">
                         <div class="input-group">
                             <input type="text" class="form-control" name="keyword" value="" id="select-good-kw" placeholder="请输入商品名称进行搜索 (多规格商品不支持一键下单)">
                             <span class="input-group-btn"><button type="button" class="btn btn-default" id="select-good-btn">搜索</button></span>
                         </div>
                         <div class="mylink-con" id="select-goods" style="height:266px;"></div>
                     </div>
                     <div role="tabpanel" class="tab-pane link_cate" id="link_cate">
                         <div class="mylink-con">
                             @foreach ($mylink_data['goodcates'] as $goodcate)
                                @if (empty($goodcate['parentid']))
                                    <div class="mylink-line">
                                        {{ $goodcate['name'] }}
                                        <div class="mylink-sub">
                                            <a href="javascript:;" class="mylink-nav" data-href="{php echo $this->createMobileUrl('shop/list',array('pcate'=>$goodcate['id']))}">选择</a>
                                        </div>
                                    </div>

                                    @foreach ($mylink_data['goodcates'] as $goodcate2)
                                        @if ($goodcate2['parentid'] == $goodcate['id'])
                                            <div class="mylink-line">
                                                <span style='height:10px; width: 10px; margin-left: 10px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                                {{ $goodcate2['name'] }}
                                                <div class="mylink-sub">
                                                    <a href="javascript:;" class="mylink-nav" data-href="{php echo $this->createMobileUrl('shop/list',array('pcate'=>$goodcate['id'],'ccate'=>$goodcate2['id']))}">选择</a>
                                                </div>
                                            </div>
                                            @foreach ($$mylink_data['goodcates'] as $goodcate3)
                                                @if ($goodcate3['parentid'] == $goodcate2['id'])
                                                    <div class="mylink-line">
                                                        <span style='height:10px; width: 10px; margin-left: 30px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                                        {{ $goodcate3['name'] }}
                                                        <div class="mylink-sub">
                                                            <a href="javascript:;" class="mylink-nav" data-href="{php echo $this->createMobileUrl('shop/list',array('pcate'=>$goodcate['id'],'ccate'=>$goodcate2['id'],'tcate'=>$goodcate3['id']))}">选择</a>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                             @endforeach
                         </div>
                     </div>
                     @if (!empty($mylink_data['designer']))
                     <div role="tabpanel" class="tab-pane link_cate" id="link_diy">
                         <div class="mylink-con">
                             @foreach ($mylink_data['diypages'] as $diypage)
                                <div class="mylink-line">
                                    @if ($diypage['pagetype'] == '4')
                                        <label class="label label-danger" style="margin-right:5px;">其他</label>
                                    @elseif ($diypage['pagetype'] == '1')
                                        @if ($diypage['setdefault'] == 1)
                                            <label class="label label-success" style="margin-right:5px;">默认首页</label>
                                        @else
                                            <label class="label label-primary" style="margin-right:5px;">首页</label>
                                        @endif
                                    @endif
                                    {{ $diypage['pagename'] }}
                                    <div class="mylink-sub">
                                        <a href="javascript:;" class="mylink-nav" data-href="{php echo $this->createPluginMobileUrl('designer',array('pageid'=>$diypage['id']))}">选择</a>
                                    </div>
                                </div>
                             @endforeach
                         </div>
                     </div>
                     @endif
                     <div role="tabpanel" class="tab-pane link_cate" id="link_article">
                         <div class="input-group">
                             <span class="input-group-addon" style='padding:0px; border: 0px;'>
                                 <select class="form-control tpl-category-parent" name="article_category" id="select-article-ca" style='width: 150px; border-radius: 4px 0px 0px 4px; border-right: 0px;'>
                                     <option value="" selected="selected">全部分类</option>
                                     @foreach ($mylink_data['categorys'] as $category)
                                        <option value="{{ $category['id'] }}">{{ $category['category_name'] }}</option>
                                     @endforeach
                                 </select>
                             </span>
                             <input type="text" class="form-control" value="" id="select-article-kw" placeholder="请输入文章标题进行搜索">
                             <span class="input-group-btn"><button type="button" class="btn btn-default" id="select-article-btn">搜索</button></span>
                         </div>
                         <div class="mylink-con" style="height:266px;">
                             <div class="mylink-line">
                                 <label class="label label-primary" style="margin-right:5px;">文章列表</label>
                                 {{ $mylink_data['article_sys']['article_title'] }}
                                 <div class="mylink-sub">
                                     <a href="javascript:;" class="mylink-nav" data-href="{php echo $this->createPluginMobileUrl('article',array('method'=>'article'))}">选择</a>
                                 </div>
                             </div>
                             <div id="select-articles"></div>
                         </div>
                     </div>
                     <div role="tabpanel" class="tab-pane link_cate" id="link_other">
                         <div class="mylink-con" style="height: 150px;">
                             <div class="form-group" style="overflow: hidden;">
                                 <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="line-height: 34px;">链接地址</label>
                                 <div class="col-sm-9 col-xs-12">
                                     <textarea name="mylink_href" class="form-control" style="height: 90px; resize: none;" placeholder="请以http://开头"></textarea>   
                                 </div>
                             </div>
                             <div class="form-group" style="overflow: hidden; margin-bottom: 0px;">
                                 <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="line-height: 34px;"></label>
                                 <div class="col-sm-9 col-xs-12">
                                     <div class="btn btn-primary mylink-nav2" style="margin-left: 20px; width: auto; overflow: hidden; margin-left: 0px;"> 插入 </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
            </div>
        </div>
    </div>
</div>

<!-- mylink end -->
<script language="javascript">
    $(function() {
        $("#chkoption").click(function() {
            var obj = $(this);
            if (obj.get(0).checked) {
                $("#tboption").show();
                $(".trp").hide();
            }
            else {
                $("#tboption").hide();
                $(".trp").show();
            }
        });
    })

    $(document).on("click",".nav-link",function(){
        var id = $(this).data("id");
        if(id){
            $("#modal-mylink").attr({"data-id":id});
            $("#modal-mylink").modal();
        }
    });
    $(document).on("click",".mylink-nav",function(){
        var href = $(this).data("href");
        var id = $("#modal-mylink").attr("data-id");
        if(id){
            $("input[data-id="+id+"]").val(href);
            $("#modal-mylink").attr("data-id","");
        }else{
            ue.execCommand('link', {href:href});
        }
        $("#modal-mylink .close").click();
    });
    $(".mylink-nav2").click(function(){
        var href = $("textarea[name=mylink_href]").val();
        if(href){
            var id = $("#modal-mylink").attr("data-id");
            if(id){
                $("input[data-id="+id+"]").val(href);
                $("#modal-mylink").attr("data-id","");
            }else{
                ue.execCommand('link', {href:href});
            }
            $("#modal-mylink .close").click();
            $("textarea[name=mylink_href]").val("");
        }else{
            $("textarea[name=mylink_href]").focus();
            alert("链接不能为空!");
        }
    });
    // ajax 选择商品
    $("#select-good-btn").click(function(){
        var kw = $("#select-good-kw").val();
        $.ajax({
            type: 'POST',
            url: "{php echo $this->createPluginWebUrl('article',array('method'=>'api','apido'=>'selectgoods'))}",
            data: {kw:kw},
            dataType:'json',
            success: function(data){
                //console.log(data);
                $("#select-goods").html("");
                if(data){
                    $.each(data,function(n,value){
                        var html = '<div class="good">';
                              html+='<div class="img"><img src="'+value.thumb+'"/></div>'
                              html+='<div class="choosebtn">';
                              html+='<a href="javascript:;" class="mylink-nav" data-href="'+"{php echo $this->createMobileUrl('shop/detail')}&id="+value.id+'">详情链接</a><br>';
                              if(value.hasoption==0){
                                html+='<a href="javascript:;" class="mylink-nav" data-href="'+"{php echo $this->createMobileUrl('order/confirm')}&id="+value.id+'">下单链接</a>';
                              }
                              html+='</div>';
                              html+='<div class="info">';
                              html+='<div class="info-title">'+value.title+'</div>';
                              html+='<div class="info-price">原价:￥'+value.productprice+' 现价￥'+value.marketprice+'</div>';
                              html+='</div>'
                              html+='</div>';
                        $("#select-goods").append(html);
                    });
                }
           }
        });
    });
    // ajax 选择文章
    $("#select-article-btn").click(function(){
        var category = $("#select-article-ca option:selected").val();
        var keyword = $("#select-article-kw").val();
        $.ajax({
            type: 'POST',
            url: "{php echo $this->createPluginWebUrl('article',array('method'=>'api','apido'=>'selectarticles'))}",
            data: {category:category,keyword:keyword},
            dataType:'json',
            success: function(data){
                //console.log(data);
                $("#select-articles").html("");
                if(data){
                    $.each(data,function(n,value){
                        var html = '<div class="mylink-line">['+value.category_name+'] '+value.article_title;
                              html+='<div class="mylink-sub">';
                              html+='<a href="javascript:;" class="mylink-nav" data-href="'+"{php echo $this->createPluginMobileUrl('article')}&aid="+value.id+'">选择</a>';
                              html+='</div></div>';
                        $("#select-articles").append(html);
                    });
                }
            }
        });
    });
</script>