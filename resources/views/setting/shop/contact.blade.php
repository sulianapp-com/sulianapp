@extends('layouts.base')

@section('content')

    <script type="text/javascript">
        function formcheck() {
            var thumb = /\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/;
            var numerictype = /^(0|[1-9]\d*)$/; //整数验证
            var mobile = /((\d{11})|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)/;
            if ($(':input[name="contact[phone]"]').val() != '') {
                if (!mobile.test($(':input[name="contact[phone]"]').val())) {
                    Tip.focus(':input[name="contact[phone]"]', '请输入正确的格式');
                    return false;
                }
            }

            return true;

        }
    </script>
<div class="w1200 m0a">
<div class="rightlist">

    @include('layouts.tabs')
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            <div class='panel-body'>  
      
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">客服电话</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="contact[phone]" class="form-control" value="{{ $set['phone'] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">所在地址</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="contact[address]" class="form-control" value="{{ $set['address'] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城简介</label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea name="contact[description]" class="form-control richtext" cols="70">{{ $set['description'] }}</textarea>
                    </div>
                </div>
                
                       <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success " onclick="return formcheck()" />
                     </div>
            </div>

            </div>
        </div>     
    </form>
</div>
</div>
@endsection
