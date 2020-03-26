@extends('layouts.base')

@section('title','403')

@section('pageHeader','错误')

@section('pageDesc','没有权限')

@section('content')

    <div class="error-page">
        <h2 class="headline text-yellow"> 403</h2>

        <div class="error-content" style="padding-top: 30px">
            <h3><i class="fa fa-warning text-yellow"></i>  没有权限.</h3>

            <p>
                没有权限.
                此时你可以返回<a href="/"> 首页 </a> 或者返回 <a href="javascript:go(-1)"> 上一页 </a>.
            </p>

        </div>
        <!-- /.error-content -->
    </div>
    <!-- /.error-page -->


@endsection


@section('js')

@endsection