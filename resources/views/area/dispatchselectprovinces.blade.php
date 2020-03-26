<style type='text/css'>
    .province { float:left; position:relative;width:175px; height:35px; line-height:35px;border:1px solid #fff;}
    .province:hover { border:1px solid #f7e4a5;border-bottom:1px solid #fffec6; background:#fffec6;}
    .province .cityall { margin-top:10px;}
    .province ul { list-style: outside none none;position:absolute;padding:0;background:#fffec6;border:1px solid #f7e4a5;display:none;
        width:auto; width:300px; z-index:999999;left:-1px;top:32px;}
    .province ul li  { float:left;min-width:60px;margin-left:20px; height:30px;line-height:30px; }
</style>
<div id="modal-areas"  class="modal fade" tabindex="-1">
    <div class="modal-dialog" style='width: 970px;'>
        <div class="modal-content">
            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择区域</h3></div>
            <div class="modal-body" style='height:320px;' >
                @foreach ($parents as $value)
                    @if ($value['areaname'] == '请选择省份') {{--{php continue }--}} @endif
                    <div class='province' data-parent-id="{{ $value['id'] }}">
                        <label class='checkbox-inline' style='margin-left:20px;'>
                            <input type='checkbox' class='cityall' /> {{ $value['areaname'] }}
                            <span class="citycount" style='color:#ff6600'></span>
                        </label>
                        <ul></ul>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <a href="javascript:;" id='btnSubmitArea' class="btn btn-success" data-dismiss="modal" aria-hidden="true">确定</a>
                <a href="javascript:;" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>
    </div>
</div>
<script language='javascript'>
    $(function(){
        $('.province').mouseover(function(){
            //由于页面出现点击省份而地区正在加载时，地区没有被选择的情况
            //改为同步请求
            $.ajaxSettings.async = false;
            var _this = $(this);
            if(_this.find('ul').text().length == 0){
                $.get('{!! yzWebUrl("area.area.select-city") !!}', {
                    parent_id: $(this).data('parent-id')
                }, function(dat){
                    _this.find('ul').html(dat);
                });
            }
            _this.find('ul').show();
            //ajax同步请求完毕后再重新设置回异步
            $.ajaxSettings.async = true;
        }).mouseout(function(){
            $(this).find('ul').hide();
        });
        $('.cityall').click(function(){
            var checked = $(this).get(0).checked;
            var citys = $(this).parent().parent().find('.city');
            citys.each(function(){
                $(this).get(0).checked = checked;
            });
            var count = 0;
            if(checked){
                count =  $(this).parent().parent().find('.city:checked').length;
            }
            if(count>0){
                $(this).next().html("(" + count + ")")    ;
            }
            else{
                $(this).next().html("");
                //注意，如果为没有子区域选择，则父区域要取消选择，这里可能没必要，但是还是加上的稳
                $($(this).get(0)).attr('checked',false).removeAttr('checked');
            }
        });

    });

</script>