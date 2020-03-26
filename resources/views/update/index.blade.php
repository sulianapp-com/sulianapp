@extends('layouts.base')

@section('title','商城更新')

@section('content')
    <style>
        .add-snav li {
            height:47px;
        }
        .version {
            color : #aa1111;
        }
    </style>

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active">当前版本 : <span class="version">{{$version}}</span></li>
            <li style="width: 10px"> </li>
            @if(!$list)
                <li class="active"> ( 您已是最新版本! )</li>
            @else
                <li class="active"><div class="btn btn-primary btn-xs updateVersion">更新版本</div></li>
            @endif
        </ul>
    </div>

    <ul class="timeline">

        @foreach($list as $item)
                <!-- timeline time label -->
        <li class="time-label">
        <span class="bg-red">
            {{date('Y-m-d',$item['created_at'])}}
        </span>
        </li>
        <!-- /.timeline-label -->

        <!-- timeline item -->
        <li>
            <!-- timeline icon -->
            <i class="fa fa-clock-o bg-gray"></i>
            <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> {{date('H:i',$item['created_at'])}}</span>

                <h3 class="timeline-header">版本：{{$item['version']}}</h3>

                <div class="timeline-body">
                    <div class="form-group">{!! $item['description'] !!}</div>
                </div>
            </div>
        </li>
        <!-- END timeline item -->
        @endforeach

    </ul>

    <script>

        $(".updateVersion").click(function () {
            var $btn = $(this);
            console.log($btn);
            $btn.button('loading');
            $.ajax({
                        url: '{!! yzWebUrl('update.start-download') !!}',
                        type: 'POST',
                        dataType: 'json'
                    })
                    .done(function (json) {

                        console.log("Downloading finished");
                        console.log(json);
                        $btn.button('reset');
                    })
                    .fail(function (message) {
                        console.log('update.start-download:', message)
                    });

        });

    </script>
@endsection