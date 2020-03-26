
<style type="text/css">
    .dropdown.visible-md.visible-lg .dropdown-toggle-name .fa-angle-down {
        display: block !important;
        line-height: 62px;
        margin-left: 5px;
    }

    .navbar .navbar-nav li a:hover {
        background: none;
    }
    .nav.navbar-nav .dropdown .dropdown-toggle-name:hover,#main-menu-name:hover {
        z-index: 9;
        color: #ff5454;
        background-color: #fff !important;
        display: block;
        height: 60px;
        border-bottom: 3px solid #ff5454;
        transition: all .15s ease-in-out;
    }
    .nav.navbar-nav .dropdown .dropdown-toggle-name:focus, #main-menu-name:focus{
        background-color: #fff !important;
        color: #ff5454
    }
    .nav.navbar-nav.navbar-actions.navbar-left .visible-gzh a,.dropdown-toggle-name{
        font-size: 14px;
        color: #333;
        padding: 0 15px;
    }
    #main-menu-name i{
        font-size: 16px;
        margin-right: 10px;
        color: inherit;
    }
    .nav.navbar-nav.navbar-actions.navbar-left .visible-lar{
        border-right: 1px solid #eee;
    }

</style>

<div class="navbar" role="navigation">


    <div class="container-fluid">

        <ul class="nav navbar-nav navbar-actions navbar-left" style="width: auto;">
            <!--<li class="visible-md visible-lg visible-lar "><a href="" id="main-menu-toggle"><i class="fa fa-th-large"></i></a></li>-->
            <li class="visible-md visible-lg visible-lar "><a href="" id="main-menu-index"><i class="fa fa-home"></i></a></li>
            <!-- <li class="visible-xs visible-sm"><a href="" id="sidebar-menu"><i class="fa fa-navicon"></i></a></li>-->

            <li class="visible-gzh "style="margin-right:40px">
                <a class="dropdown-toggle" id="main-menu-name"href="./index.php?c=home&amp;a=welcome&amp;do=ext&amp;m=sz_yi"><i class="iconfont icon-heart"></i> 商城名称</a>
            </li>

        </ul>
        <div class="copyrights">Collect from <a href="" ></a></div>
        <ul class="nav navbar-nav navbar-right" style="width: auto;margin: 0;float: right!important;">

            <li class="dropdown visible-md visible-lg">
                <a href="javascript:void(0)"  class="dropdown-toggle-name" i="system" data-toggle="dropdown" >admin (系统管理员)
                    <span class="fa fa-angle-down pull-right"></span>
                </a>
                <ul class="dropdown-menu" >
                    <li class="dropdown-menu-header">
                        <strong>关于</strong>
                    </li>
                    <li><a href=""><i class="fa fa-user"></i> 我的账号</a></li>
                    <li><a href=""><i class="fa fa-wrench"></i> 系统选项</a></li>
                    <li><a href=""><i class="fa fa-usd"></i> 自动更新 </a></li>
                    <li class="divider"></li>
                    <li><a href=""><i class="fa fa-sign-out"></i> 退出</a></li>
                </ul>
            </li>
            <li><a href=""><i class="fa fa-power-off"></i></a></li>

        </ul>

    </div>

</div>
<!-- end: Header -->
