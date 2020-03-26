<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>


@foreach ($filtering as $parent)
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-1 control-label"></label>
    <div class="col-sm-9 col-xs-12 chks" >

        <label class="checkbox-inline">
            <input type="checkbox" onclick="quang(this)" value="{{$parent['id']}}" /> {{$parent['name']}}
        </label>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div id="{{$parent['id']}}" class="col-sm-9 col-xs-12 chks" >
        @foreach ($parent['value'] as $son)
        <label class="checkbox-inline">
            <input type="checkbox" class='duo' name="widgets[filtering][]" value="{{$son['id']}}" @if ( in_array( $son['id'],$goods_filter)) checked="true" @endif  /> {{$son['name']}}
        </label>
        @endforeach
    </div>
</div>
@endforeach


<script type="text/javascript">

    function quang(obj)
    {
        var parent_id = $(obj).val();
        var xuan =  $("#"+ parent_id).find('.duo');

       if ($(obj).prop('checked')) {
            for (var i = 0; i < xuan.length; i++) {
                $(xuan[i]).prop('checked', true);
             }
       } else {
            for (var i = 0; i < xuan.length; i++) {
                $(xuan[i]).prop('checked', false);
            }
       }
    }
   
</script>