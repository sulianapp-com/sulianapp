  {{--<footer class="main-footer">--}}
    {{--<div class="pull-right hidden-xs">--}}
        {{--Yun Shop--}}
    {{--</div>--}}
    {{--<strong>Copyright &copy; 2017 {{\Config::get('module.name')}}.</strong> All rights reserved.--}}

  {{--</footer>--}}
  <!--   Core JS Files   -->
  {{--<script src="{{static_url('yunshop/dist/js/common.js')}}../assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>--}}

  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <!--<script src="{{static_url('assets/js/demo.js')}}"></script>
  <script type="text/javascript">
      $(document).ready(function() {

          // Javascript method's body can be found in assets/js/demos.js
          demo.initDashboardPageCharts();
      });
  </script>-->

  <script type="text/javascript">
      require(['bootstrap'],function(){
      });
  </script>
  @if(YunShop::app()->role == 'founder' && config('app.env') == 'production')
  <script type="text/javascript">
    var checkUrl = "{!! yzWebUrl('update.check') !!}";
    var todoUrl = "{!! yzWebUrl('update.index') !!}";
    var pirateUrl = "{!! yzWebFullUrl('update.pirate') !!}";
    function check_yun_shop_upgrade() {
      require(['util'], function (util) {
        if (util.cookie.get('check_yun_shop_upgrade')) {
          return;
        }

        $.post(checkUrl, function (result) {
          if (-1 == result.updated) {
              window.location.href = pirateUrl;
          }

          if (result && result.updated != '0') {
             var html = '<div class="container" id="check_yun_shop_upgrade" style=" position: fixed;margin: auto;bottom: 0px;z-index: 999;">\
              <div class="row">\
              <div class="alert alert-danger">\
              <button type="button" class="close" data-dismiss="alert" onclick="check_yun_shop_upgrade_hide()" aria-hidden="true">×</button>\
            <h4><i class="icon fa fa-check"></i> 系统更新提示</h4>\
            商城检测到新版本:' + result.last_version + ' 请<a href="' + todoUrl + '"> 点击这里 </a> 更新到最新版本 \
              </div>\
              </div>\
              </div>';
            $('.main-footer').append(html);
          }
        });
      });
    }
    function check_yun_shop_upgrade_hide() {
      require(['util'], function (util) {
        util.cookie.set('check_yun_shop_upgrade', 1, 3600);
        $('#check_yun_shop_upgrade').remove();
      });
    }
    $(function () {
        //check_yun_shop_upgrade();
    });


  </script>
  @endif