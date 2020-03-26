<div class="spec_item_item" style="float:left;margin:0 5px 10px 0;width:250px;">
	<input type="hidden" class="form-control spec_item_show" name="spec_item_show_{{$spec['id']}}[]" VALUE="{{$specitem['show']}}" />
	<input type="hidden" class="form-control spec_item_id" name="spec_item_id_{{$spec['id']}}[]" VALUE="{{$specitem['id']}}" />
	<div class="input-group"  style="margin:10px 0;">
		<span class="input-group-addon">
			<label class="checkbox-inline" style="margin-top:-20px;">
				<input type="checkbox" @if ($specitem['show']==1) checked @endif value="1" onclick='showItem(this)'>
			</label>
		</span>

		<input type="text" onchange="calc()" class="form-control spec_item_title error" name="spec_item_title_{{$spec['id']}}[]" VALUE="{{$specitem['title']}}" />

		<span class="input-group-addon">
			<a href="javascript:;" onclick="removeSpecItem(this)" title='删除'><i class="fa fa-times"></i></a>
	  		<a href="javascript:;" class="fa fa-arrows" title="拖动调整显示顺序" ></a>
		</span>
	</div>

                         <div class="input-group choosetemp" style='margin-bottom: 10px;@if($goods['type']!=3) display:none @endif'>
                        <input type="hidden" name="spec_item_virtual_{{$spec['id']}}[]" value="{{$specitem['virtual']}}" class="form-control spec_item_virtual"  id="temp_id_{{$specitem['id']}}">
                        <input type="text" name="spec_item_virtualname_{{$spec['id']}}[]" value="@if (empty($specitem['virtual']))未选择 @else ($specitem['title2']) @endif" class="form-control spec_item_virtualname" readonly="" id="temp_name_{{$specitem['id']}}">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="button" onclick="choosetemp('{{$specitem['id']}}')">选择虚拟物品</button>
                        </div>
                    </div>

	<div>
		{{--
		{!! app\common\helpers\ImageHelper::tplFormFieldImage('spec_item_thumb_'.$spec['id']."[]",$specitem['thumb']) !!}
		--}}
	</div>
</div>



