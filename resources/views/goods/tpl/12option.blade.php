<style type="text/css">
	.spectable td,.spectable th {border:1px solid #ccc; vertical-align: middle;text-align:center;}
	.spectable th { font-weight: bold;}
	.spectableinput { text-align: center;}
	.f {border-color: #b94a48;-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);}
	.table.table-bordered tr th,.table.table-bordered tr td{overflow:hidden; text-overflow:ellipsis;}
</style>
<div class="form-group">
       <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否启用{{$lang['shopoption']}}</label>
        <div class="col-sm-9 col-xs-12">
	<label class="checkbox-inline">
		<input type="checkbox" id="hasoption" value="1" name="hasoption" @if ($goods['hasoption']==1)checked @endif />启用{{$lang['shopoption']}}
	</label>
	  <span class="help-block">启用{{$lang['shopoption']}}后，商品的价格及库存以商品规格为准,库存设置为0则不显示,-1为不限制</span>
           </div>

    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否启用新规格模板</label>
    <div class="col-sm-9 col-xs-12">
        <label class="checkbox-inline">
            <input type="checkbox" id="opt_switch" value="1" name="opt_switch" @if ($goods['opt_switch']==1)checked @endif />启用规格模板
        </label>

        <span class="help-block">新规格模板只在设置2组规格及不支持线下核销时显示</span>
    </div>
</div>
 
<div id='tboption' style="@if ($goods['hasoption']!=1)display:none @endif">
	<div class="alert alert-info">
		1. 拖动规格可调整规格显示顺序, 更改规格及规格项后请点击下方的【刷新规格项目表】来更新数据。<br/>
		2. 每一种规格代表不同型号，例如颜色为一种规格，尺寸为一种规格，如果设置多规格，手机用户必须每一种规格都选择一个规格项，才能添加购物车或购买。
	</div>
	<div id='specs'>
		@foreach ($allspecs as $spec)
		@include('goods.tpl.spec')
		@endforeach
	</div>
	<table class="table">
		<tr>
			<td>
				<h4><a href="javascript:;" class='btn btn-primary' id='add-spec' onclick="addSpec()" style="margin-top:10px;margin-bottom:10px;" title="添加规格"><i class='fa fa-plus'></i> 添加规格</a> 
				<a href="javascript:;" onclick="calc()" title="刷新规格项目表" class="btn btn-primary"><i class="fa fa-refresh"></i> 刷新规格项目表</a>
			</h4>
			</td>
		</tr>
	</table>
	<div class="panel-body table-responsive" id="options" style="padding:0;">
		{!! $html !!}
	</div>
</div>
      
       <div id="modal-module-chooestemp" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:0px auto;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                    <h3>选择虚拟物品模板</h3>
                                </div>
                                <div class="modal-body">
                                      <div class="form-group">
                                        <label class="col-xs-12 col-sm-3 col-md-2 control-label" style='width: 150px'>选择模板:</label>
                                        <div class="col-sm-9 col-xs-12" style='width: 380px'>
                                               <select class="form-control tpl-category-parent">
                                                    @foreach ($virtual_types as $virtual_type)
                                                        <option value="{{$virtual_type['id']}}">{{$virtual_type['usedata']}}/{{$virtual_type['alldata']}} | {{$virtual_type['title']}}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <span class="btn btn-primary span2" onclick="addtemp()">确认选择</span>
                                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                                </div>
                            </div>
                        </div>
        </div>
      
      
<script language="javascript">
	$(function(){
		$('#specs').sortable({
			stop: function(){
				window.optionchanged = true;
			}
		});
		$('.spec_item_items').sortable(
				{
					handle:'.fa-arrows',
					stop: function(){
						window.optionchanged = true;
					}
				}
			);
		$("#hasoption").click(function(){
			var obj = $(this);
			if (obj.get(0).checked){
				$("#tboption").show();
			}else{
				$("#tboption").hide();
			}
		});
	})
	function addDataSpec(){
		$("#add-spec").html("正在处理...").attr("disabled", "true").toggleClass("btn-primary");
		var url = "{php echo $this->createWebUrl('shop/tpl',array('tpl'=>'spec_data'))}";
		$.ajax({
			"url": url,
			success:function(data){
				$("#add-spec").html('<i class="fa fa-plus"></i> 添加规格').removeAttr("disabled").toggleClass("btn-primary"); ;
				$('#specs').append(data);
				var len = $(".add-specitem").length -1;
				$(".add-specitem:eq(" +len+ ")").focus();

				window.optionchanged = true;
			}
		});
	}
	function addSpec(){
        var len = $(".spec_item").length;
   
        if(type==3 && virtual==0 && len>=1){
            util.message('您的商品类型为：虚拟物品(卡密)的多规格形式，只能添加一种规格！');
            return;
        }
                    
		$("#add-spec").html("正在处理...").attr("disabled", "true").toggleClass("btn-primary");
		var url = "{php echo $this->createWebUrl('goods.goods.getSpecTpl',array('tpl'=>'spec'))}";
		$.ajax({
			"url": url,
			success:function(data){
				$("#add-spec").html('<i class="fa fa-plus"></i> 添加规格').removeAttr("disabled").toggleClass("btn-primary"); ;
				$('#specs').append(data);
				var len = $(".add-specitem").length -1;
				$(".add-specitem:eq(" +len+ ")").focus();
                                                                                
				window.optionchanged = true;
			}
		});
	}
	function removeSpec(specid){
		if (confirm('确认要删除此规格?')){
			$("#spec_" + specid).remove();
			window.optionchanged = true;
		}
	}
	function addSpecItem(specid){
	$("#add-specitem-" + specid).html("正在处理...").attr("disabled", "true");
		var url = "{php echo$this->createWebUrl('goods.goods.getSpecItemTpl',array('tpl'=>'specitem'))}" + "&specid=" + specid;
		$.ajax({
			"url": url,
			success:function(data){
				$("#add-specitem-" + specid).html('<i class="fa fa-plus"></i> 添加规格项').removeAttr("disabled");
				$('#spec_item_' + specid).append(data);
				var len = $("#spec_" + specid + " .spec_item_title").length -1;
				$("#spec_" + specid + " .spec_item_title:eq(" +len+ ")").focus();
				window.optionchanged = true;
                if(type==3 && virtual==0){
                    $(".choosetemp").show();
                }
			}
		});
	}
	function removeSpecItem(obj){
		$(obj).parent().parent().parent().remove();
	}
    
	function calc(){
		window.optionchanged = false;
		var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
		var specs = [];
		if($('.spec_item').length<=0){
			$("#options").html('');
			return;
		}
		$(".spec_item").each(function(i){
			var _this = $(this);

			var spec = {
				id: _this.find(".spec_id").val(),
				title: _this.find(".spec_title").val()
			};
		
			var items = [];
			_this.find(".spec_item_item").each(function(){
				var __this = $(this);
				var item = {
					id: __this.find(".spec_item_id").val(),
					title: __this.find(".spec_item_title").val(),
	                virtual: __this.find(".spec_item_virtual").val(),
					show:__this.find(".spec_item_show").get(0).checked?"1":"0"
				}
				items.push(item);
			});
			spec.items = items;
			specs.push(spec);
		});
		specs.sort(function(x,y){
			if (x.items.length > y.items.length){
				return 1;
			}
			if (x.items.length < y.items.length) {
				return -1;
			}
		});

		var len = specs.length;
		var newlen = 1; 
		var h = new Array(len); 
		var rowspans = new Array(len); 
		for(var i=0;i<len;i++){
			html+="<th style='width:8%;'>" + specs[i].title + "</th>";
			var itemlen = specs[i].items.length;
			if(itemlen<=0) { itemlen = 1 };
			newlen*=itemlen;
			h[i] = new Array(newlen);
			for(var j=0;j<newlen;j++){
				h[i][j] = new Array();
			}
			var l = specs[i].items.length;
			rowspans[i] = 1;
			for(j=i+1;j<len;j++){
				rowspans[i]*= specs[j].items.length;
			}
		}

		html += '<th class="info" style="width:13%;"><div><div style="padding-bottom:10px;text-align:center;font-size:16px;">库存</div><div class="input-group"><input type="text" class="form-control option_stock_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';

		html += '<th class="success" style="width:30%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">{{$lang[marketprice]}}</div><div class="input-group"><input type="text" class="form-control option_marketprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
		html+='<th class="warning" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">市场价格</div><div class="input-group"><input type="text" class="form-control option_productprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
		html+='<th class="danger" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">成本价格</div><div class="input-group"><input type="text" class="form-control option_costprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
		
		html+='<th class="warning" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">红包价格</div><div class="input-group"><input type="text" class="form-control option_redprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_redprice\');"></a></span></div></div></th>';
	    html+='<th class="primary" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">{{$lang[shopnumber]}}</div><div class="input-group"><input type="text" class="form-control option_goodssn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_goodssn\');"></a></span></div></div></th>';
	    html+='<th class="danger" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品条码</div><div class="input-group"><input type="text" class="form-control option_productsn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productsn\');"></a></span></div></div></th>';
		html+='<th class="info" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">重量（克）</div><div class="input-group"><input type="text" class="form-control option_weight_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';

		html+='</tr></thead>';
		
		for(var m=0;m<len;m++){
			var k = 0,kid = 0,n=0;
			for(var j=0;j<newlen;j++){
				var rowspan = rowspans[m]; 
				if( j % rowspan==0){
					h[m][j]={title: specs[m].items[kid].title, virtual: specs[m].items[kid].virtual,html: "<td rowspan='" +rowspan + "'>"+ specs[m].items[kid].title+"</td>\r\n",id: specs[m].items[kid].id};
				}
				else{
					h[m][j]={title:specs[m].items[kid].title,virtual: specs[m].items[kid].virtual, html: "",id: specs[m].items[kid].id};	
				}
				n++;
				if(n==rowspan){
				kid++; if(kid>specs[m].items.length-1) { kid=0; }
				n=0;
				}
			}
		}
	 
		var hh = "";
		for(var i=0;i<newlen;i++){
			hh+="<tr>";
			var ids = [];
			var titles = [];    
	                                    var virtuals = [];
			for(var j=0;j<len;j++){
				hh+=h[j][i].html; 
				ids.push( h[j][i].id);
				titles.push( h[j][i].title);
	                                                      virtuals.push( h[j][i].virtual);
			}
			ids =ids.join('_');
			titles= titles.join('+');
		
			var val ={ id : "",title:titles, stock : "",costprice : "",productprice : "",marketprice : "",weight:"",productsn:"",goodssn:"",virtual:virtuals,redprice:"" };
			if( $(".option_id_" + ids).length>0){
				val ={
					id : $(".option_id_" + ids+":eq(0)").val(),
					title: titles,
					stock : $(".option_stock_" + ids+":eq(0)").val(),
					costprice : $(".option_costprice_" + ids+":eq(0)").val(),
					productprice : $(".option_productprice_" + ids+":eq(0)").val(),
					marketprice : $(".option_marketprice_" + ids +":eq(0)").val(),
	                goodssn : $(".option_goodssn_" + ids +":eq(0)").val(),
	                productsn : $(".option_productsn_" + ids +":eq(0)").val(),
					weight : $(".option_weight_" + ids+":eq(0)").val(),
	                virtual : virtuals,
	                redprice : $(".option_redprice_" + ids+":eq(0)").val(),
				}
			}
			hh += '<td class="info">'
			hh += '<input name="option_stock_' + ids +'[]"type="text" class="form-control option_stock option_stock_' + ids +'" value="' +(val.stock=='undefined'?'':val.stock )+'"/></td>';
			hh += '<input name="option_id_' + ids+'[]"type="hidden" class="form-control option_id option_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
			
			hh += '<input name="option_ids[]"type="hidden" class="form-control option_ids option_ids_' + ids +'" value="' + ids +'"/>';
			hh += '<input name="option_title_' + ids +'[]"type="hidden" class="form-control option_title option_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
			
	        hh += '<input name="option_virtual_' + ids +'[]"type="hidden" class="form-control option_title option_title_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
			hh += '</td>';
			hh += '<td class="success"><input name="option_marketprice_' + ids+'[]" type="text" class="form-control option_marketprice option_marketprice_' + ids +'" value="' +(val.marketprice=='undefined'?'':val.marketprice )+'"/>';

			hh += '</td>';
			hh += '<td class="warning"><input name="option_productprice_' + ids+'[]" type="text" class="form-control option_productprice option_productprice_' + ids +'" " value="' +(val.productprice=='undefined'?'':val.productprice )+'"/></td>';
			hh += '<td class="danger"><input name="option_costprice_' +ids+'[]" type="text" class="form-control option_costprice option_costprice_' + ids +'" " value="' +(val.costprice=='undefined'?'':val.costprice )+'"/></td>';
			
			hh += '<td class="warning"><input name="option_redprice_' +ids+'[]" type="text" class="form-control option_redprice option_redprice_' + ids +'" " value="' +(val.redprice=='undefined'?'':val.redprice )+'"/></td>';
	        hh += '<td class="primary"><input name="option_goodssn_' +ids+'[]" type="text" class="form-control option_goodssn option_goodssn_' + ids +'" " value="' +(val.goodssn=='undefined'?'':val.goodssn )+'"/></td>';
	        hh += '<td class="danger"><input name="option_productsn_' +ids+'[]" type="text" class="form-control option_productsn option_productsn_' + ids +'" " value="' +(val.productsn=='undefined'?'':val.productsn )+'"/></td>';
			hh += '<td class="info"><input name="option_weight_' + ids +'[]" type="text" class="form-control option_weight option_weight_' + ids +'" " value="' +(val.weight=='undefined'?'':val.weight )+'"/></td>';
			hh += "</tr>";
		}
		html+=hh;
		html+="</table>";
		$("#options").html(html);
	}
function setCol(cls){
	$("."+cls).val( $("."+cls+"_all").val());
}
function showItem(obj){
	var show = $(obj).get(0).checked?"1":"0"; 
	$(obj).next().val(show);
}
function nofind(){
	var img=event.srcElement;
	img.src="./resource/image/module-nopic-small.jpg";
	img.onerror=null; 
}

    function choosetemp(id){
    $('#modal-module-chooestemp').modal();
    $('#modal-module-chooestemp').data("temp",id);
}function addtemp(){
    var id = $('#modal-module-chooestemp').data("temp");
    var temp_id = $('#modal-module-chooestemp').find("select").val();
    var temp_name = $('#modal-module-chooestemp option[value='+temp_id+']').text();
    //alert(temp_id+":"+temp_name);
    $("#temp_name_"+id).val(temp_name);
    $("#temp_id_"+id).val(temp_id);
    $('#modal-module-chooestemp .close').click();
    window.optionchanged = true;
}
</script>