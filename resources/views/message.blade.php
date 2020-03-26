@extends('layouts.base')

@section('content')
    <div class="jumbotron clearfix alert alert-{{$status}}" style="top: 30px;width: 70%;margin: auto;">
        <div class="row">
            {{--<div class="alert alert-danger">
                <button type="button" aria-hidden="true" class="close">
                    <i class="material-icons">close</i>
                </button>
                <span><b> Danger - </b> This is a regular notification made with ".alert-danger"</span>
            </div>--}}

            <div class="col-xs-12 col-sm-3 col-lg-2">
                <i class="fa fa-5x
                @if($status=='success') fa-check-circle @endif
                @if($status=='danger') fa-times-circle @endif
                @if($status=='info') fa-info-circle @endif
                @if($status=='warning') fa-exclamation-triangle @endif
                        "></i>
            </div>
            <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">


                <p>{!! $message !!}</p>

                @if($redirect)
                    <p><a style="text-decoration: none" href="{!! $redirect !!}">如果你的浏览器没有自动跳转，请点击此链接</a></p>
                    <script type="text/javascript">
                        setTimeout(function () {
                            location.href = "{!! $redirect !!}";
                        }, 3000);
                    </script>
                @else
                    <script type="text/javascript">
                      setTimeout(function () {
                        history.go(-1);
                      }, 3000);
                    </script>
                    <p>[<a style="text-decoration: none" href="javascript:history.go(-1);">点击这里返回上一页</a>] &nbsp; [<a href="{{yzWebUrl('index.index')}}">首页</a>]</p>
                @endif
            </div>
        </div>
    </div>
    <style>
        .main-panel{
            width:100%;
        }
        .sidebar2{display:none !important;}
    </style>
@endsection
