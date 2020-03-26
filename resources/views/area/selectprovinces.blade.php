<style type='text/css'>
    .province { float:left; position:relative;width:185px; height:35px; line-height:35px;border:1px solid #fff;}
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
            <div class="modal-body" style='height:370px;' >
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
            var _this = $(this);
            if(_this.find('ul').text().length == 0){
                $.get('{!! yzWebUrl("area.area.select-city") !!}', {
                    parent_id: $(this).data('parent-id')
                }, function(dat){
                    _this.find('ul').html(dat);
                });
            }
            _this.find('ul').show();
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
            }
        });
        $('.city').click(function(){
            var checked = $(this).get(0).checked;
            var cityall = $(this).parent().parent().parent().parent().find('.cityall');
            if(checked){
                cityall.get(0).checked = true;
            }
            var count = cityall.parent().parent().find('.city:checked').length;
            if(count>0){
                cityall.next().html("(" + count + ")")    ;
            }
            else{
                cityall.next().html("");
            }
        });
    });
    function clearSelects(){
        $('.city').attr('checked',false).removeAttr('disabled');
        $('.cityall').attr('checked',false).removeAttr('disabled');
        $('.citycount').html('');
    }
    function selectAreas(){
        clearSelects();
        var old_citys = $('#areas').html().split(';');
        $('.city').each(function(){
            var parentcheck = false;
            for(var i in old_citys){
                if(old_citys[i]==$(this).attr('city')){
                    parentcheck = true;
                    $(this).get(0).checked = true;
                    break;
                }
            }
            if(parentcheck){
                $(this).parent().parent().parent().parent().find('.cityall').get(0).checked=  true;
            }
        });
        $("#modal-areas").modal();
        var citystrs = '';
        var cityids = '';
        $('#btnSubmitArea').unbind('click').click(function(){
            $('.city:checked').each(function(){
                citystrs+= $(this).attr('city') +";";
                cityids+= $(this).attr('city_id') +",";
            });
            $('#areas').html(citystrs);
            $("#selectedareas").val(citystrs);
            $("#selectedareaids").val(cityids);
        })
    }
</script>