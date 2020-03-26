@extends('layouts.base')

@section('title','404')

@section('pageHeader','错误')

@section('pageDesc','页面找不到')

@section('content')
    <div class="error-page">
        <h2 class="headline text-yellow"> 404</h2>

        <div class="error-content" style="padding-top: 30px">
            <h3><i class="fa fa-warning text-yellow"></i>  页面找不到.</h3>

            <p>
                页面没找到.
                此时你可以返回<a href="/"> 首页 </a>.
            </p>

        </div>
        <!-- /.error-content -->
    </div>
    <!-- /.error-page -->



@endsection


@section('js')

@endsection