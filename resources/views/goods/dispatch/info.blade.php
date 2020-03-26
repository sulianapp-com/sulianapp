@extends('layouts.base')
@section('content')
@section('title', trans('配送模板详情'))
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">配送方式设置</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data"
              onsubmit='return formcheck()'>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="dispatch[display_order]" class="form-control"
                                   value="{{ $dispatch->display_order }}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>配送方式名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" id='dispatchname' name="dispatch[dispatch_name]" class="form-control"
                                   value="{{   $dispatch->dispatch_name }}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否为默认快递模板</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='dispatch[is_default]' id="isdefault1" value='1'
                                       @if ( $dispatch->is_default == 1 )checked @endif /> 是
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='dispatch[is_default]' id="isdefault0" value='0'
                                       @if ( $dispatch->is_default == 0 )checked @endif /> 否
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">计费方式</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='dispatch[calculate_type]' value='0'
                                       @if ( $dispatch->calculate_type == 0 )checked @endif /> 按重量计费
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='dispatch[calculate_type]' value='1'
                                       @if ( $dispatch->calculate_type == 1 ) checked @endif /> 按件计费
                            </label>
                        </div>
                    </div>

                    <div class="form-group dispatch0">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">配送区域</label>
                        <div class="col-sm-9 col-xs-12">
                            <table class="show_h" areas="0">
                                <thead>
                                <tr>
                                    <th style="height:40px;width:400px;">运送到</th>
                                    <th style="width:120px;">首重(克)</th>
                                    <th style="width:120px;">首费(元)</th>
                                    <th style="width:120px;">续重(克)</th>
                                    <th style="width:120px;">续费(元)</th>
                                </tr>
                                </thead>
                                <tbody class='tbody-areas'>

                                <tr>
                                    <td style="padding:10px;">
                                        全国 [默认运费]
                                        <input type="hidden" value="全国 [默认运费]" class="form-control"
                                               name="dispatch[areas]">

                                    </td>
                                    <td class=" text-center">
                                        <input type="number" value="{{ $dispatch->first_weight ? $dispatch->first_weight :0 }}" class="form-control"
                                               name="dispatch[first_weight]" style="width:100px;"></td>
                                    <td class=" text-center">
                                        <input type="text" value="{{ $dispatch->first_weight_price ? $dispatch->first_weight_price : 0 }}"
                                               class="form-control" name="dispatch[first_weight_price]"
                                               style="width:100px;"></td>
                                    <td class=" text-center">
                                        <input type="number" value="{{ $dispatch->another_weight ? $dispatch->another_weight : 0 }}"
                                               class="form-control" name="dispatch[another_weight]"
                                               style="width:100px;"></td>
                                    <td class=" text-center">
                                        <input type="text" value="{{ $dispatch->another_weight_price ? $dispatch->another_weight_price : 0 }}"
                                               class="form-control" name="dispatch[another_weight_price]"
                                               style="width:100px;"></td>
                                    <td class=""></td>
                                </tr>
                                @foreach($dispatch->weight_data as $key=>$weight)
                                    <tr>
                                        <td style="padding:10px;" class="{{$key}}">
                                            <span class="areas">{{$weight['areas']}}</span>
                                            <input type="hidden" value="{{$weight['areas']}}" class="form-control areas-name"
                                                   name="dispatch[weight][{{$key}}][areas]">
                                            <input type="hidden" value="{{$weight['area_ids']}}" class="form-control areas-ids"
                                                   name="dispatch[weight][{{$key}}][area_ids]">
                                            <a href='javascript:;' onclick='editArea(this)' random="{{$key}}">编辑</a>
                                        </td>
                                        <td class=" text-center">
                                            <input type="number" value="{{ $weight['first_weight'] ? $weight['first_weight'] : 0 }}"
                                                   class="form-control"
                                                   name="dispatch[weight][{{$key}}][first_weight]" style="width:100px;">
                                        </td>
                                        <td class=" text-center">
                                            <input type="text" value="{{ $weight['first_weight_price'] ? $weight['first_weight_price'] : 0}}"
                                                   class="form-control"
                                                   name="dispatch[weight][{{$key}}][first_weight_price]"
                                                   style="width:100px;"></td>
                                        <td class=" text-center">
                                            <input type="number" value="{{ $weight['another_weight'] ? $weight['another_weight'] : 0}}"
                                                   class="form-control"
                                                   name="dispatch[weight][{{$key}}][another_weight]"
                                                   style="width:100px;"></td>
                                        <td class=" text-center">
                                            <input type="text" value="{{ $weight['another_weight_price'] ? $weight['another_weight_price'] :0}}"
                                                   class="form-control"
                                                   name="dispatch[weight][{{$key}}][another_weight_price]"
                                                   style="width:100px;">
                                        </td>
                                        <td><a href="javascript:;" onclick="$(this).parent().parent().remove()"><i class='fa fa-remove'></i></td>
                                    </tr>
                                @endforeach


                                </tbody>
                            </table>
                            <!--=================================================-->
                            <table class='show_n' areas="1">
                                <thead>
                                <tr>
                                    <th style="height:40px;width:400px;">运送到</th>
                                    <th class="show_n" style="width:120px;">首件(个)</th>
                                    <th class="show_n" style="width:120px;">运费(元)</th>
                                    <th class="show_n" style="width:120px;">续件(个)</th>
                                    <th class="show_n" style="width:120px;">续费(元)</th>
                                </tr>
                                </thead>
                                <tbody class='tbody-areas'>
                                <tr>
                                    <td style="padding:10px;">
                                        全国 [默认运费]
                                        <input type="hidden" value="全国 [默认运费]" class="form-control"
                                               name="dispatch[areas]">
                                    </td>
                                    <td class=" text-center">
                                        <input type="number" value="{{ $dispatch->first_piece ? $dispatch->first_piece : 0 }}" class="form-control"
                                               name="dispatch[first_piece]" style="width:100px;"></td>
                                    <td class=" text-center">
                                        <input type="text" value="{{ $dispatch->first_piece_price ? $dispatch->first_piece_price : 0 }}"
                                               class="form-control" name="dispatch[first_piece_price]"
                                               style="width:100px;"></td>
                                    <td class=" text-center">
                                        <input type="number" value="{{ $dispatch->another_piece ? $dispatch->another_piece : 0 }}" class="form-control"
                                               name="dispatch[another_piece]" style="width:100px;"></td>
                                    <td class=" text-center">
                                        <input type="text" value="{{ $dispatch->another_piece_price ? $dispatch->another_piece_price : 0 }}"
                                               class="form-control" name="dispatch[another_piece_price]"
                                               style="width:100px;"></td>
                                    <td></td>
                                </tr>

                                @foreach($dispatch->piece_data as $key=>$piece)

                                    <tr>
                                        <td style="padding:10px;" class="{{$key}}">
                                            <span class="areas">{{$piece['areas']}}</span>

                                            <input type="hidden" value="{{$piece['areas']}}" class="form-control areas-name"
                                                   name="dispatch[piece][{{$key}}][areas]">
                                            <input type="hidden" value="{{$piece['area_ids']}}" class="form-control areas-ids"
                                                   name="dispatch[piece][{{$key}}][area_ids]">
                                            <a href='javascript:;' onclick='editArea(this)' random="{{$key}}">编辑</a>
                                        </td>
                                        <td class=" text-center">
                                            <input type="number" value="{{ $piece['first_piece'] ? $piece['first_piece'] :0 }}"
                                                   class="form-control"
                                                   name="dispatch[piece][{{$key}}][first_piece]" style="width:100px;">
                                        </td>
                                        <td class=" text-center">
                                            <input type="text" value="{{ $piece['first_piece_price'] ? $piece['first_piece_price'] :0 }}"
                                                   class="form-control"
                                                   name="dispatch[piece][{{$key}}][first_piece_price]"
                                                   style="width:100px;"></td>
                                        <td class=" text-center">
                                            <input type="number" value="{{ $piece['another_piece'] ? $piece['another_piece'] :0 }}"
                                                   class="form-control" name="dispatch[piece][{{$key}}][another_piece]"
                                                   style="width:100px;"></td>
                                        <td class=" text-center">
                                            <input type="text" value="{{ $piece['another_piece_price'] ? $piece['another_piece_price'] :0 }}"
                                                   class="form-control"
                                                   name="dispatch[piece][{{$key}}][another_piece_price]"
                                                   style="width:100px;">
                                        </td>
                                        <td>
                                            <a href='javascript:;' onclick='$(this).parent().parent().remove()'>
                                                <i class='fa fa-remove'></i>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <!--====================================================================================================-->
                            <a class='btn btn-default' href="javascript:;" onclick='selectAreas()'><span
                                        class="fa fa-plus"></span> 新增配送区域</a>
                            <span class='help-block show_h'
                                  @if ( $dispatch->dispatch_type == 1 ) style='display:block' @endif>根据重量来计算运费，当物品不足《首重重量》时，按照《首重费用》计算，超过部分按照《续重重量》和《续重费用》乘积来计算</span>
                            <span class='help-block show_n'
                                  @if ( $dispatch->dispatch_type == 0 ) style='display:block' @endif>根据件数来计算运费，当物品不足《首件数量》时，按照《首件费用》计算，超过部分按照《续件数量》和《续件费用》乘积来计算</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'>
                            <input type='radio' name='dispatch[enabled]' value='1'
                                   @if ( $dispatch->enabled == 1 ) checked @endif /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='dispatch[enabled]' value='0'
                                   @if ( $dispatch->enabled == 0 ) checked @endif /> 否
                        </label>
                    </div>
                </div>
                <div class="form-group"></div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" name="submit" value="提交" class="btn btn-success "
                               onclick="return formcheck()"/>
                        <input type="button" name="back" onclick='history.back()' value="返回列表"
                               class="btn btn-default back"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        var calculateType = '{{ $dispatch['calculate_type'] ?: 0 }}';
        var random = "{{$random}}";
        function show_type(flag) {
            calculateType = flag;
            if (flag == 1) {
                $('.weight').css("display", "none");
                $('.fnum').css("display", "");
                $('.show_h').hide();
                $('.show_n').show();
            } else {
                $('.weight').css("display", "");
                $('.fnum').css("display", "none");
                $('.show_h').show();
                $('.show_n').hide();
            }
        }
        $(function () {
            show_type(calculateType);
            $(':radio[name="dispatch[calculate_type]"]').click(function () {
                var val = $(this).val();
                show_type(val);
            })
        });
        function clearSelects() {
            $('.city').attr('checked', false).removeAttr('disabled');
            $('.cityall').attr('checked', false).removeAttr('disabled');
            $('.citycount').html('');
        }
        function selectAreas() {
            clearSelects();

            $("#modal-areas").modal();
            var citystrs = '';
            var citystrIds = '';
            $('#btnSubmitArea').unbind('click').click(function () {
                random++;
                $('.city:checked').each(function () {
                    citystrs += $(this).attr('city') + ";";
                    citystrIds += $(this).attr('city_id') + ";";
                });
                if (calculateType == 0) {
                    var content = `
                        <tr  class="show_h">
                        <td style="padding:10px;" name="areas" class="${random}">
                            <span class="areas">${citystrs}</span>
                            <input type="hidden"  value="${citystrs}" class="form-control areas-name" name="dispatch[weight][${random}][areas]">
                            <input type="hidden"  value="${citystrIds}" class="form-control areas-ids" name="dispatch[weight][${random}][area_ids]">
                            <a href='javascript:;' onclick='editArea(this)' random="${random}">编辑</a>

                            </td>

                            <td class=" text-center">
                              <input type="number" value="{{ $dispatch->first_weight }}" class="form-control" name="dispatch[weight][${random}][first_weight]" style="width:100px;"></td>
                            <td class=" text-center">
                              <input type="text" value="{{ $dispatch->first_weight_price }}" class="form-control" name="dispatch[weight][${random}][first_weight_price]"  style="width:100px;"></td>
                            <td class=" text-center">
                              <input type="number" value="{{ $dispatch->another_weight }}" class="form-control" name="dispatch[weight][${random}][another_weight]"  style="width:100px;"></td>
                            <td class=" text-center">
                              <input type="text" value="{{ $dispatch->another_weight_price }}" class="form-control" name="dispatch[weight][${random}][another_weight_price]"  style="width:100px;"></td>
                            <td><a href='javascript:;' onclick='$(this).parent().parent().remove()'><i class='fa fa-remove'></i></td>
                        </tr>
                    `;
                    $("[areas=" + calculateType + "]").children(".tbody-areas").append(content);
                } else {
                    var content = `
                        <tr class="show_n" class="${random}">
                            <td style="padding:10px;">
                            <span class="areas">${citystrs}</span>
                            <input type="hidden"  value="${citystrs}" class="form-control areas-name" name="dispatch[piece][${random}][areas]">
                            <input type="hidden"  value="${citystrIds}" class="form-control areas-ids" name="dispatch[piece][${random}][area_ids]">
                            <a href='javascript:;' onclick='editArea(this)' random="${random}">编辑</a>
                            </td>
                            <td class=" text-center">
                              <input type="number" value="{{ $dispatch->first_piece }}" class="form-control" name="dispatch[piece][${random}][first_piece]" style="width:100px;"></td>
                            <td class=" text-center">
                              <input type="text" value="{{ $dispatch->first_piece_price }}" class="form-control" name="dispatch[piece][${random}][first_piece_price]"  style="width:100px;"></td>
                            <td class=" text-center">
                              <input type="number" value="{{ $dispatch->another_piece }}" class="form-control" name="dispatch[piece][${random}][another_piece]"  style="width:100px;"></td>
                            <td class=" text-center">
                              <input type="text" value="{{ $dispatch->another_piece_price }}" class="form-control" name="dispatch[piece][${random}][another_piece_price]"  style="width:100px;"></td>
                            <td><a href='javascript:;' onclick='$(this).parent().parent().remove()'><i class='fa fa-remove'></i></td>
                        </tr>
                    `;
                    $("[areas=" + calculateType + "]").children(".tbody-areas").append(content);
                }

            })
        }

        function editArea(btn){
            current = $(btn).attr('random');
            clearSelects();
            var old_citys = $(btn).prev().val().split(';');
            $('.city').each(function(){
                var parentcheck = false;
                for(var i in old_citys){
                    if(old_citys[i]==$(this).attr('city_id')){
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
            var citystrIds = '';
            $('#btnSubmitArea').unbind('click').click(function(){
                $('.city:checked').each(function(){
                    citystrs += $(this).attr('city') + ";";
                    citystrIds += $(this).attr('city_id') + ";";
                });
                $('.' + current + ' .areas').html(citystrs);
                $('.' + current + ' .areas-name').val(citystrs);
                $('.' + current + ' .areas-ids').val(citystrIds);


            });
            var currents = getCurrents(current);
            currents = currents.split(';');
            var citys = "";
            $('.city').each(function(){
                var parentdisabled =false;
                for(var i in currents){
                    if(currents[i]!='' && currents[i]==$(this).attr('city_id')){
                        $(this).attr('disabled',true);
                        $(this).parent().parent().parent().parent().find('.cityall').attr('disabled',true);
                    }
                }

            });
        }

        function getCurrents(withOutRandom){
            var citys = "";
            $('.citys').each(function(){
                var crandom = $(this).prev().val();
                if(withOutRandom && crandom==withOutRandom){
                    return true;
                }
                citys+=$(this).val();
            });
            return citys;
        }


    </script>
    @include('area.dispatchselectprovinces')
@endsection