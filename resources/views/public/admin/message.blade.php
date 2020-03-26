@if (session()->has('flash_notification.message'))
<div class="container" style="    position: fixed;margin: auto;top: 0;left: 0;right: 0;z-index: 10;">
    <div class="row">
        <div class="col-md-9 col-md-offset-3" style="{!! (session()->has('flash_notification.overlay')) ? '':'margin-top:60px;z-index: 999;    margin-left:240px;' !!}">


                @if (session()->has('flash_notification.overlay'))

                    <div id="flash-overlay-modal" class="modal fade flash-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                                    <h4 class="modal-title">{{session('flash_notification.title')}}</h4>
                                </div>

                                <div class="modal-body">
                                    <p>{!! session('flash_notification.message') !!}</p>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-{{session('flash_notification.level')}} {{session()->has('flash_notification.important') ? 'alert-important' : ''}} "
                    >
                        @if (session()->has('flash_notification.important'))
                            <button type="button"
                                    class="close"
                                    data-dismiss="alert"
                                    aria-hidden="true"
                            >&times;</button>
                        @endif
                            {!! session('flash_notification.message') !!}
                    </div>
                @endif

        </div>
    </div>
</div>
<script>
  require(['bootstrap'],function($){
    $('#flash-overlay-modal').modal();
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
  });

</script>
@endif