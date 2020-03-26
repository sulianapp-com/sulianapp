<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级浏览权限</label>
    <div class="col-sm-9 col-xs-12 chks">
        <label class="checkbox-inline">
            <input type="checkbox" class='chkall' name="widgets[privilege][show_levels]" value="" @if ( $privilege['show_levels']==='') checked="true" @endif  /> 全部会员等级
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" class='chksingle' name="widgets[privilege][show_levels][]" value="0" @if ( $privilege['show_levels'] !== '' && is_array($privilege['show_levels']) && in_array('0', $privilege['show_levels'])) checked="true" @endif  />  普通等级
        </label>
        @foreach ( $levels as $level)
        <label class="checkbox-inline">
            <input type="checkbox" class='chksingle' name="widgets[privilege][show_levels][]" value="{{ $level['id'] }}" @if ( $privilege['show_levels'] !== '' && is_array($privilege['show_levels'])  && in_array($level['id'], $privilege['show_levels'])) checked="true" @endif  /> {{ $level['level_name'] }}
        </label>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级购买权限</label>
    <div class="col-sm-9 col-xs-12 chks" >

        <label class="checkbox-inline">
            <input type="checkbox" class='chkall' name="widgets[privilege][buy_levels]" value="" @if ( $privilege['buy_levels'] === '' ) checked="true" @endif  /> 全部会员等级
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" class='chksingle'  name="widgets[privilege][buy_levels][]" value="0" @if ( $privilege['buy_levels'] !== '' && is_array($privilege['buy_levels'])  && in_array('0', $privilege['buy_levels'])) checked="true" @endif  /> 普通等级
        </label>
        @foreach ($levels as $level)
        <label class="checkbox-inline">
            <input type="checkbox" class='chksingle'  name="widgets[privilege][buy_levels][]" value="{{ $level['id'] }}" @if ( $privilege['buy_levels'] !== '' && is_array($privilege['buy_levels']) && in_array($level['id'], $privilege['buy_levels']) ) checked="true" @endif  /> {{ $level['level_name'] }}
        </label>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员组浏览权限</label>
    <div class="col-sm-9 col-xs-12 chks" >
        <label class="checkbox-inline">
            <input type="checkbox" class='chkall' name="widgets[privilege][show_groups]" value="" @if ( $privilege['show_groups'] === '' )checked="true" @endif  /> 全部会员组
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" class='chksingle'  name="widgets[privilege][show_groups][]" value="0" @if ( $privilege['show_groups'] !== '' && is_array($privilege['show_groups']) && in_array('0', $privilege['show_groups'])) checked="true" @endif  /> 无分组
        </label>
        @foreach ($groups as $group)
        <label class="checkbox-inline">
            <input type="checkbox" class='chksingle'  name="widgets[privilege][show_groups][]" value="{{ $group['id'] }}" @if ( $privilege['show_groups'] !== ''  && in_array($group['id'], $privilege['show_groups']) && is_array($privilege['show_groups'])) checked="true" @endif  /> {{ $group['group_name'] }}
        </label>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员组购买权限</label>
    <div class="col-sm-9 col-xs-12 chks" >
        <label class="checkbox-inline">
            <input type="checkbox" class='chkall' name="widgets[privilege][buy_groups]" value="" @if ( $privilege['buy_groups' ] === '' )checked="true" @endif  /> 全部会员组
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" class='chksingle'  name="widgets[privilege][buy_groups][]" value="0" @if ( $privilege['buy_groups'] !== ''  && is_array($privilege['buy_groups']) && in_array('0', $privilege['buy_groups'])) checked="true" @endif  />  无分组
        </label>
        @foreach  ($groups as $group)
        <label class="checkbox-inline">
            <input type="checkbox" class='chksingle'  name="widgets[privilege][buy_groups][]" value="{{ $group['id'] }}" @if ( $privilege['buy_groups'] !== '' &&  is_array($privilege['buy_groups']) && in_array($group['id'], $privilege['buy_groups']) ) checked="true" @endif  /> {{ $group['group_name'] }}
        </label>
        @endforeach

    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">每次限购数量</label>
    <div class="col-sm-9 col-md-2 col-xs-12">
        <input type="text" name="widgets[privilege][once_buy_limit]" id="widgets[privilege][once_buy_limit]" class="form-control" value="{{ $privilege['once_buy_limit'] }}" />
        <span class='help-block'>每次下单限购数量</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员限购总数</label>
    <div class="col-sm-9 col-md-2 col-xs-12">
        <input type="text" name="widgets[privilege][total_buy_limit]" id="widgets[privilege][total_buy_limit]" class="form-control" value="{{ $privilege['total_buy_limit'] }}" />
        <span class='help-block'>会员限购的总数</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员每天限购数量</label>
    <div class="col-sm-9 col-md-2 col-xs-12">
        <input type="text" name="widgets[privilege][day_buy_limit]" id="widgets[privilege][day_buy_limit]" class="form-control" value="{{ $privilege['day_buy_limit'] }}" />
        <span class='help-block'>会员每天限购数量</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员每周限购数量</label>
    <div class="col-sm-9 col-md-2 col-xs-12">
        <input type="text" name="widgets[privilege][week_buy_limit]" id="widgets[privilege][week_buy_limit]" class="form-control" value="{{ $privilege['week_buy_limit'] }}" />
        <span class='help-block'>会员每周限购数量</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员每月限购数量</label>
    <div class="col-sm-9 col-md-2 col-xs-12">
        <input type="text" name="widgets[privilege][month_buy_limit]" id="widgets[privilege][month_buy_limit]" class="form-control" value="{{ $privilege['month_buy_limit'] }}" />
        <span class='help-block'>会员每月限购数量</span>
    </div>
</div>
{{--
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启限时限购</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <label class="radio-inline">
                <input type="radio" name="widgets[privilege][enable_time_limit]" value="1" @if ( $privilege['enable_time_limit'] == 1) checked="true" @endif /> 开启
            </label>
            <label class="radio-inline">
                <input type="radio" name="widgets[privilege][enable_time_limit]" value="0" @if ( empty($privilege) || $privilege['enable_time_limit'] == 0) checked="true" @endif /> 关闭
            </label>
        </div>
    </div>
</div>
<div class="form-group" id="time_limit" style="display: none;">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">限购时间</label>
    <div class="col-sm-4 col-xs-6">
        {!! \app\backend\modules\goods\services\GoodsPrivilegeService::tpl_form_field_date('widgets[privilege][time_begin_limit]', !empty($privilege['time_begin_limit']) ? date('Y-m-d H:i',$privilege['time_begin_limit']) : date('Y-m-d H:i'), 1) !!}
    </div>
    <div class="col-sm-4 col-xs-6">
        {!! \app\backend\modules\goods\services\GoodsPrivilegeService::tpl_form_field_date('widgets[privilege][time_end_limit]', !empty($privilege['time_end_limit']) ? date('Y-m-d H:i',$privilege['time_end_limit']) : date('Y-m-d H:i'), 1) !!}
    </div>
</div>
--}}


{{--<script language='javascript'>
    if ($('input:radio[name="widgets[privilege][enable_time_limit]"]:checked').val() == 1) {
        $('#time_limit').show();
    };
    $('input[name="widgets[privilege][enable_time_limit]').click(function(){
        var discounttype = $('input:radio[name="widgets[privilege][enable_time_limit]"]:checked').val();
        if(discounttype == 1){
            $('#time_limit').show();
        }else{
            $('#time_limit').hide();
        }
    });
</script>--}}
