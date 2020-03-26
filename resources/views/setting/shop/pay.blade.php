@extends('layouts.base')
@section('title', trans('基础设置'))

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">

            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">支付方式</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                {{--<div class='alert alert-info'>
                    在开启以下支付方式前，请到 <a href='{php echo url('profile/payment')}'>支付选项</a> 去设置好参数。
                </div>--}}
                {{--<div class="alert alert-warning alert-important">
                    易宝支付，含银联，信用卡等多种支付方式, PC版支付成功后台通知请登录商户后台添加通知地址,<a href="http://www.yeepay.com/" target="_blank">申请及详情请查看这里</a>.
                </div>--}}

                <!-- weixin支付设置 _start -->
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='pay[weixin]' value='1'
                                       @if ($set['weixin'] == 1) checked @endif/>
                                开启
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='pay[weixin]' value='0'
                                       @if ($set['weixin'] == 0) checked @endif />
                                关闭
                            </label>
                            <span class="help-block">标准微信支付、及其他微信支付接口（云收银）总开关</span>
                            <span class="help-block">ps：微信支付授权目录填写路径：域名/addons/yun_shop/</span>
                        </div>
                    </div>
                    <div id='certs' @if (empty($set['weixin'])) style="display:none" @endif>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float:left; width:15%; height:30px;">
                                    <label class='radio-inline' style="padding-left:0px">标准微信支付：</label>
                                </div>
                                <div style="float:left; width:85%; height:30px;">
                                    <label class='radio-inline'>
                                        <input type='radio' name='pay[weixin_pay]' value='1'
                                               @if ($set['weixin_pay'] == 1) checked @endif/>
                                        开启
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='pay[weixin_pay]' value='0'
                                               @if ($set['weixin_pay'] == 0) checked @endif />
                                        关闭
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float:left; width:15%; height:30px;">
                                    <label class='radio-inline' style="padding-left:0px">身份标识(appId)：</label>
                                </div>
                                <div style="float:left; width:85%; height:30px;">
                                    <input type="text" class="form-control" name="pay[weixin_appid]"
                                           value="{{ @$set['weixin_appid'] }}" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float:left; width:15%; height:30px;">
                                    <label class='radio-inline' style="padding-left:0px">身份密钥(appSecret)：</label>
                                </div>
                                <div style="float:left; width:85%; height:30px;">
                                    <input type="text" class="form-control" name="pay[weixin_secret]"
                                           value="{{ @$set['weixin_secret'] }}" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float:left; width:15%; height:30px;">
                                    <label class='radio-inline' style="padding-left:0px">微信支付商户号(mchId)：</label>
                                </div>
                                <div style="float:left; width:85%; height:30px;">
                                    <input type="text" class="form-control" name="pay[weixin_mchid]"
                                           value="{{ @$set['weixin_mchid'] }}" autocomplete="off">
                                </div>
                                <span class="help-block">ps：微信公众号以邮件形式告知</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float:left; width:15%; height:30px;">
                                    <label class='radio-inline' style="padding-left:0px">微信支付密钥(apiSecret)：</label>
                                </div>
                                <div style="float:left; width:85%; height:30px;">
                                    <input type="text" class="form-control" name="pay[weixin_apisecret]"
                                           value="{{ @$set['weixin_apisecret'] }}" autocomplete="off">
                                </div>
                                <span class="help-block">ps：获取路径：微信支付商户平台>账户设置>API安全--设置支付密钥（32位数）</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' name='pay[weixin_version]' value='0'
                                           @if ( empty($set['weixin_version'])) checked @endif/>
                                    文件上传
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' name='pay[weixin_version]' value='1'
                                           @if ( $set['weixin_version'] == 1) checked @endif />
                                    文本上传
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <span class="help-block">ps：微信支付证书获取途径：登录微信商户平台--账户中心--API安全--下载证书</span>
                            </div>
                        </div>

                        <div id="new_weixin_certificate"
                             @if ( empty($set['weixin_version']) || $set['weixin_version'] == 0) style="display: none" @endif>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">CERT证书文件：</label>
                                    </div>
                                    <div style="float:left;">
                                        <textarea id="new_weixin_cert" @if (!empty($set['new_weixin_cert'])) style="display: none" @endif name="pay[new_weixin_cert]" class="form-control rich-text" cols="85"
                                                  rows="5">{{ $set['new_weixin_cert'] }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">KEY密钥文件：</label>
                                    </div>
                                    <div style="float:left;">
                                        <textarea id="new_weixin_key" @if (!empty($set['new_weixin_key'])) style="display: none" @endif name="pay[new_weixin_key]" class="form-control rich-text" cols="85"
                                                  rows="5">{{ $set['new_weixin_key'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($set['new_weixin_cert']) || !empty($set['new_weixin_key']))
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <input type="button" name="from_new_weixin_certificate" class="btn btn-success" value="重新设置" />
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div id="old_weixin_certificate" @if (isset($set['weixin_version']) && $set['weixin_version'] == 1) style="display: none" @endif>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">CERT证书文件：</label>
                                    </div>
                                    <div style="float:left; width:85%; height:30px;">
                                        <input type="hidden" name="pay[weixin_cert]" value="{{ $set['weixin_cert'] }}"/>
                                        <input type="file" name="weixin_cert" class="form-control"/>
                                        <span class="help-block">
                                        @if (!empty($set['weixin_cert']))
                                                <span class='label label-success'>已上传</span>
                                            @else
                                                <span class='label label-danger'>未上传</span>
                                            @endif
                                            下载证书 cert.zip 中的 apiclient_cert.pem 文件
                                    </span>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">


                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">KEY密钥文件：</label>
                                    </div>
                                    <div style="float:left; width:85%; height:30px;">
                                        <input type="hidden" name="pay[weixin_key]" value="{{ $set['weixin_key'] }}"/>

                                        <input type="file" name="weixin_key" class="form-control"/>
                                        <span class="help-block">
                                        @if (!empty($set['weixin_key']))
                                                <span class='label label-success'>已上传</span>
                                            @else
                                                <span class='label label-danger'>未上传</span>
                                            @endif
                                            下载证书 cert.zip 中的 apiclient_key.pem 文件
                                    </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 借用微信支付设置 _start -->
                <!--
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">借用微信支付</label>
                <div class="col-sm-9 col-xs-12">
                    <label class='radio-inline'><input type='radio' name='pay[weixin_jie]' value='1' @if ( $set['weixin_jie']==1)checked @endif/> 开启</label>
                    <label class='radio-inline'><input type='radio' name='pay[weixin_jie]' value='0' @if ( $set['weixin_jie']==0)checked @endif /> 关闭</label>
                    <span class='help-block'>开启借号微信支付，微信支付功能将失效</span>
                </div>

            </div>
            <div id='jie' @if ( empty($set['pay']['weixin_jie'])) style="display:none" @endif>
                <div class="form-group" >
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label must">公众号(AppId)</label>
                    <div class="col-sm-9">
                        <input type="text" name="pay[weixin_jie_appid]" class="form-control" value="{{ $set['weixin_jie_appid'] }}"/>
                    </div>
                </div>
                <div class="form-group" >
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label must">微信支付商户号(Mch_Id)</label>
                    <div class="col-sm-9">
                        <input type="text" name="pay[weixin_jie_mchid]" class="form-control" value="{{ $set['weixin_jie_mchid'] }}"/>
                    </div>
                </div>
                <div class="form-group" >
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label must">微信支付密钥(APIKEY)</label>
                    <div class="col-sm-9">
                        <input type="text" name="pay[weixin_jie_apikey]" class="form-control" value="{{ $set['weixin_jie_apikey'] }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">CERT证书文件</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="pay[weixin_jie_cert]" value="{{ $data['weixin_jie_cert'] }}"/>

                        <input type="file" name="weixin_jie_cert_file" class="form-control" />
                                    <span class="help-block">
                                        @if (!empty($sec['jie_cert']))
                    <span class='label label-success'>已上传</span>
                    @else
                    <span class='label label-danger'>未上传</span>
                    @endif
                        下载证书 cert.zip 中的 apiclient_cert.pem 文件</span>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">KEY密钥文件</label>
    <div class="col-sm-9 col-xs-12">
        <input type="hidden" name="pay[weixin_jie_key]"  value="{{ $data['weixin_jie_key'] }}"/>
                        <input type="file" name="weixin_jie_key_file" class="form-control" />
                                    <span class="help-block">
                                       @if (!empty($sec['jie_key']))
                    <span class='label label-success'>已上传</span>
                    @else
                    <span class='label label-danger'>未上传</span>
                    @endif
                        下载证书 cert.zip 中的 apiclient_key.pem 文件
                    </span>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">ROOT文件</label>
    <div class="col-sm-9 col-xs-12">
        <input type="hidden" name="pay[weixin_jie_root]" value="{{ $data['weixin_jie_root'] }}"/>
                        <input type="file" name="weixin_jie_root_file" class="form-control" />
                                    <span class="help-block">
                                      @if ( !empty($sec['jie_root']))
                    <span class='label label-success'>已上传</span>
                    @else
                    <span class='label label-danger'>未上传</span>
                    @endif
                        下载证书 cert.zip 中的 rootca.pem 文件
                    </span>
    </div>
</div>
</div>
-->
                    <!-- paypal支付设置 _start -->
                <!--
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">Paypal支付</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='pay[paypal_status]' value='1' @if ( $set['paypal_status'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='pay[paypal_status]' value='0' @if ( $set['paypal_status'] == 0) checked @endif /> 关闭</label>
                    </div>
                </div>
                <div id='paypal' @if ( empty($set['paypal_status'])) style="display:none" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div style="float:left; width:15%; height:30px;">
                                <label class='radio-inline'  style="padding-left:0px">商户号：</label>
                            </div>
                            <div style="float:left; width:85%; height:30px;">
                                <label class='radio-inline' style="width:70%;"><input class="col-sm-6" style="width:100%;" type="text" name="pay[paypal_mchid]" value="{{ $set['paypal_mchid'] }}"/></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div style="float:left; width:15%; height:30px;">
                                <label class='radio-inline'  style="padding-left:0px">商户密码：</label>
                            </div>
                            <div style="float:left; width:85%; height:30px;">
                                <label class='radio-inline' style="width:70%;"><input class="col-sm-6" style="width:100%;" type="text" name="pay[paypal_key]" value="{{ $set['paypal_key'] }}"/></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div style="float:left; width:15%; height:30px;">
                                <label class='radio-inline'  style="padding-left:0px">商户支付密钥：</label>
                            </div>
                            <div style="float:left; width:85%; height:30px;">
                                <label class='radio-inline' style="width:70%;"><input class="col-sm-6" style="width:100%;" type="text" name="pay[paypal_signkey]" value="{{ $set['paypal_signkey'] }}"/></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div style="float:left; width:15%; height:30px;">
                                <label class='radio-inline'  style="padding-left:0px">支付币种：</label>
                            </div>
                            <div style="float:left; width:85%; height:30px;">
                                <label class='radio-inline' style="width:70%;"><input class="col-sm-6" style="width:100%;" type="text" name="pay[paypal_currency]" value="{{ $set['paypal_currency'] }}"/></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div style="float:left; width:15%; height:30px;">
                                <label class='radio-inline'  style="padding-left:0px">汇率：</label>
                            </div>
                            <div style="float:left; width:85%; height:30px;">
                                <label class='radio-inline' style="width:70%;"><input class="col-sm-6" style="width:100%;" type="text" name="pay[paypal_currencies]" value="{{ $set['paypal_currencies'] }}"/></label>
                            </div>
                        </div>
                    </div>

                </div>
                -->
                    <!-- paypal支付设置 _end -->

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝支付</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='pay[alipay]' value='1'
                                       @if ( $set['alipay'] == 1) checked @endif/>
                                开启
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='pay[alipay]' value='0'
                                       @if ( $set['alipay'] == 0) checked @endif />
                                关闭
                            </label>
                        </div>
                    </div>

                    <div id='alipay_block' @if (empty($set['alipay'])) style="display:none" @endif>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' name='pay[alipay_pay_api]' value='0'
                                           @if ( empty($set['alipay_pay_api']) || $set['alipay_pay_api'] == 0) checked @endif/>
                                    旧接口
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' name='pay[alipay_pay_api]' value='1'
                                           @if ( $set['alipay_pay_api'] == 1) checked @endif />
                                    新接口
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float:left; width:15%; height:30px;">
                                    <label class='radio-inline' style="padding-left:0px">收款支付宝账号：</label>
                                </div>
                                <div style="float:left; width:85%; height:30px;">
                                    <input type="text" class="form-control" name="pay[alipay_account]"
                                           value="{{ @$set['alipay_account'] }}" autocomplete="off">
                                </div>
                                <span class="help-block">ps：卖家支付宝账号</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float:left; width:15%; height:30px;">
                                    <label class='radio-inline' style="padding-left:0px">合作者身份：</label>
                                </div>
                                <div style="float:left; width:85%; height:30px;">
                                    <input type="text" class="form-control" name="pay[alipay_partner]"
                                           value="{{ @$set['alipay_partner'] }}" autocomplete="off">
                                </div>
                                <span class="help-block">ps：签约的支付宝账号对应的支付宝唯一用户号,以 2088 开头的 16 位纯数字组成</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float:left; width:15%; height:30px;">
                                    <label class='radio-inline' style="padding-left:0px">校验密钥：</label>
                                </div>
                                <div style="float:left; width:85%; height:30px;">
                                    <input type="text" class="form-control" name="pay[alipay_secret]"
                                           value="{{ @$set['alipay_secret'] }}" autocomplete="off">
                                </div>
                                <span class="help-block">ps：支付宝开放平台--账户中心--mapi网关产品密钥--MD5密钥</span>
                            </div>
                        </div>
                    </div>
                <!--
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">易宝支付</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='pay[yeepay]' value='1' @if ( $set['yeepay'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='pay[yeepay]' value='0' @if ( $set['yeepay'] == 0) checked @endif /> 关闭</label>
                    </div>
                </div>
                <div id='yeepay_set' @if (empty($set['yeepay'])) style="display:none" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商户编号</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[yeepay_merchantaccount]" value="{{ $set['yeepay_merchantaccount'] }}"/>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商户密钥</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[yeepay_merchantKey]" value="{{ $set['yeepay_merchantKey'] }}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商户私钥</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[yeepay_merchantPrivateKey]" value="{{ $set['yeepay_merchantPrivateKey'] }}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商户RSA公钥</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[yeepay_merchantPublicKey]" value="{{ $set['yeepay_merchantPublicKey'] }}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">易宝RSA公钥</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[yeepay_PublicKey]" value="{{ $set['yeepay_PublicKey'] }}"/>
                        </div>
                    </div>
                </div>
-->

                <!--
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">货到付款</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='pay[cash]' value='1' @if ( $set['cash'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='pay[cash]' value='0' @if ( $set['cash'] == 0) checked @endif /> 关闭</label>
                    </div>
                </div>
-->
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝提现</label>
                        <input type="hidden" name="pay[secret]" value="1">
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='pay[alipay_withdrawals]' value='1'
                                       @if ( $set['alipay_withdrawals'] == 1) checked @endif/>
                                开启
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='pay[alipay_withdrawals]' value='0'
                                       @if ( $set['alipay_withdrawals'] == 0) checked @endif />
                                关闭
                            </label>
                        </div>
                    </div>
                    <div id='alipay_withdrawals' @if ( empty($set['alipay_withdrawals'])) style="display:none" @endif>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' name='pay[api_version]' value='1'
                                           @if ( empty($set['api_version']) || $set['api_version'] == 1) checked @endif/>
                                    旧接口
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' name='pay[api_version]' value='2'
                                           @if ( $set['api_version'] == 2) checked @endif />
                                    新接口
                                </label>
                            </div>
                        </div>

                        {{--<div id="new_open_alipay"--}}
                             {{--@if ( empty($set['api_version']) || $set['api_version'] == 1) style="display: none" @endif>--}}
                            {{--<div class="form-group">--}}
                                {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                                {{--<div class="col-sm-9 col-xs-12">--}}
                                    {{--<div style="float:left; width:15%; height:30px;">--}}
                                        {{--<label class='radio-inline' style="padding-left:0px">应用ID：</label>--}}
                                    {{--</div>--}}
                                    {{--<div style="float:left; width:85%; height:30px;">--}}
                                        {{--<input class="col-sm-6" type="text" name="pay[alipay_app_id]"--}}
                                               {{--value="{{ $set['alipay_app_id'] }}"/>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            {{--<div class="form-group">--}}
                                {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                                {{--<div class="col-sm-9 col-xs-12">--}}
                                    {{--<div style="float:left; width:15%; height:30px;">--}}
                                        {{--<label class='radio-inline' style="padding-left:0px">开发者私钥：</label>--}}
                                    {{--</div>--}}
                                    {{--<div style="float:left;">--}}
                                        {{--<textarea id="rsa_private_key" @if (!empty($set['rsa_private_key'])) style="display: none" @endif name="pay[rsa_private_key]" class="form-control rich-text" cols="85"--}}
                                                  {{--rows="5">{{ $set['rsa_private_key'] }}</textarea>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            {{--<div class="form-group">--}}
                                {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                                {{--<div class="col-sm-9 col-xs-12">--}}
                                    {{--<div style="float:left; width:15%; height:30px;">--}}
                                        {{--<label class='radio-inline' style="padding-left:0px">支付宝公钥：</label>--}}
                                    {{--</div>--}}
                                    {{--<div style="float:left;">--}}
                                        {{--<textarea id="rsa_public_key" @if (!empty($set['rsa_public_key'])) style="display: none" @endif name="pay[rsa_public_key]" class="form-control rich-text" cols="85"--}}
                                                  {{--rows="5">{{ $set['rsa_public_key'] }}</textarea>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--@if(!empty($set['rsa_public_key']) || !empty($set['rsa_private_key']))--}}
                            {{--<div class="form-group">--}}
                                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                                    {{--<div class="col-sm-9 col-xs-12">--}}
                                        {{--<input type="button" name="rsa_alipay_key" class="btn btn-success" value="重新设置公私钥" />--}}
                                    {{--</div>--}}
                            {{--</div>--}}
                            {{--@endif--}}
                        {{--</div>--}}
                        <div id="old_open_alipay">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">付款账号：</label>
                                    </div>
                                    <div style="float:left; width:85%; height:30px;">
                                        <input class="col-sm-6" type="text" name="pay[alipay_number]"
                                               value="{{ $set['alipay_number'] }}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">付款账户名：</label>
                                    </div>
                                    <div style="float:left; width:85%; height:30px;">
                                        <input class="col-sm-6" type="text" name="pay[alipay_name]"
                                               value="{{ $set['alipay_name'] }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝新接口统一配置</label>
                        <div class="form-group"><div class="col-sm-9 col-xs-12">支付宝支付新接口与支付宝提现新接口的配置</div></div>
                        <div id="alipay_public_api" @if ( empty($set['alipay_withdrawals']) && empty($set['alipay'])) style="display:none" @endif>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%;height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">应用ID：</label>
                                    </div>
                                    <div style="float:left; width:85%; height:30px;">
                                        <input class="col-sm-6" type="text" name="pay[alipay_app_id]"
                                               value="{{ $set['alipay_app_id'] }}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">开发者私钥：</label>
                                    </div>
                                    <div style="float:left;">
                                        <textarea id="rsa_private_key" @if (!empty($set['rsa_private_key'])) style="display: none" @endif name="pay[rsa_private_key]" class="form-control rich-text" cols="85"
                                                  rows="5">{{ $set['rsa_private_key'] }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">支付宝公钥：</label>
                                    </div>
                                    <div style="float:left;">
                                        <textarea id="rsa_public_key" @if (!empty($set['rsa_public_key'])) style="display: none" @endif name="pay[rsa_public_key]" class="form-control rich-text" cols="85"
                                                  rows="5">{{ $set['rsa_public_key'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($set['rsa_public_key']) || !empty($set['rsa_private_key']))
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <input type="button" name="rsa_alipay_key" class="btn btn-success" value="重新设置公私钥" />
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额支付</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='pay[credit]' value='1'
                                       @if ( $set['credit'] == 1) checked @endif/>
                                开启
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='pay[credit]' value='0'
                                       @if ( $set['credit'] == 0) checked @endif />
                                关闭
                            </label>
                        </div>
                    </div>

                    <div id='balance' @if ( empty($set['credit'])) style="display:none" @endif>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float:left; width:15%; height:30px;">
                                    <label class='radio-inline' style="padding-left:0px">支付密码验证：</label>
                                </div>
                                <div style="float:left; width:85%; height:30px;">
                                    <label class='radio-inline'>
                                        <input type='radio'
                                               @if(!(new \app\common\services\sms\SmsSetService())->isCanUse())
                                               disabled=false
                                               @endif
                                               @if ($set['balance_pay_proving'] == 1)
                                               checked
                                               @endif
                                               name='pay[balance_pay_proving]' value='1'/>
                                        开启
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='pay[balance_pay_proving]' value='0'
                                               @if ($set['balance_pay_proving'] == 0) checked @endif />
                                        关闭
                                    </label>
                                    <span class="help-block">开启余额支付密码验证必须配置短信通道，否则不能开启</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--<div class="form-group">--}}
                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信红包提现</label>--}}
                    {{--<div class="col-sm-9 col-xs-12">--}}
                    {{--<label class='radio-inline'><input type='radio' name='pay[weixin_withdrawals]' value='1' @if ( $set['weixin_withdrawals'] == 1) checked @endif/> 开启</label>--}}
                    {{--<label class='radio-inline'><input type='radio' name='pay[weixin_withdrawals]' value='0' @if ( $set['weixin_withdrawals'] == 0) checked @endif /> 关闭</label>--}}
                    {{--</div>--}}
                    {{--</div>--}}

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">找人代付</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='pay[another]' value='1'
                                       @if ( $set['another'] == 1) checked @endif/>
                                开启
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='pay[another]' value='0'
                                       @if ( $set['another'] == 0) checked @endif />
                                关闭
                            </label>
                            <span class="help-block">启用代付功能后，代付发起人（买家）下单后，可将订单分享给小伙伴（朋友圈、微信群、微信好友），请他帮忙付款。</span>
                        </div>
                    </div>

                    <div id='another' @if ( empty($set['another'])) style="display:none" @endif>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float:left; width:15%; height:30px;">
                                    <label class='radio-inline' style="padding-left:0px">发起人求助：</label>
                                </div>

                                <div style="float:left; width:85%; height:30px;">
                                    <input type="text" name="pay[another_share_title]" class="form-control"
                                           value="{{$set['another_share_title']}}" autocomplete="off"
                                           placeholder="土豪大大，跪求代付">
                                    <span class="help-block">默认分享标题：土豪大大，跪求代付</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">银行转账</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='pay[remittance]' value='1'
                                       @if ( $set['remittance'] == 1) checked @endif/>
                                开启
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='pay[remittance]' value='0'
                                       @if ( $set['remittance'] == 0) checked @endif />
                                关闭
                            </label>
                            <span class="help-block">ps：前端转账支付页选择汇款支付，上传支付凭证，后台财务审核</span>
                        </div>
                    </div>
                    <div id='remittance' @if ( empty($set['remittance'])) style="display:none" @endif>
                        <div class="form-group">
                            <div class="form-group">

                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">开户行：</label>
                                    </div>
                                    <div style="float:left; width:85%; height:30px;">
                                        <input type="text" name="pay[remittance_bank]" class="form-control"
                                               value="{{$set['remittance_bank']}}" autocomplete="off"
                                               placeholder="开户行">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">

                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>

                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">开户支行：</label>
                                    </div>
                                    <div style="float:left; width:85%; height:30px;">
                                        <input type="text" name="pay[remittance_sub_bank]" class="form-control"
                                               value="{{$set['remittance_sub_bank']}}" autocomplete="off"
                                               placeholder="开户行">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">

                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">开户名：</label>
                                    </div>
                                    <div style="float:left; width:85%; height:30px;">
                                        <input type="text" name="pay[remittance_bank_account_name]" class="form-control"
                                               value="{{$set['remittance_bank_account_name']}}" autocomplete="off"
                                               placeholder="开户名">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">

                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>

                                <div class="col-sm-9 col-xs-12">
                                    <div style="float:left; width:15%; height:30px;">
                                        <label class='radio-inline' style="padding-left:0px">开户账号：</label>
                                    </div>
                                    <div style="float:left; width:85%; height:30px;">
                                        <input type="text" name="pay[remittance_bank_account]" class="form-control"
                                               value="{{$set['remittance_bank_account']}}" autocomplete="off"
                                               placeholder="开户账号">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">货到付款</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='pay[COD]' value='1'
                                       @if ( $set['COD'] == 1) checked @endif/>
                                开启
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='pay[COD]' value='0'
                                       @if ( $set['COD'] == 0) checked @endif />
                                关闭
                            </label>
                        </div>
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                        </div>
                    </div>

                </div>
                <script language="javascript">
                    $(function () {
                        $(":radio[name='pay[weixin]']").click(function () {
                            if ($(this).val() == 1) {
                                $("#certs").show();
                            }
                            else {
                                $("#certs").hide();
                            }
                        })
                        //微信支付证书
                        $(":radio[name='pay[weixin_version]']").click(function () {
                            if ($(this).val() == 1) {
                                $("#new_weixin_certificate").show();
                                $("#old_weixin_certificate").hide();
                            }
                            else {
                                $("#new_weixin_certificate").hide();
                                $("#old_weixin_certificate").show();
                            }
                        })

                        $(":radio[name='pay[weixin_jie]']").click(function () {
                            if ($(this).val() == 1) {
                                $("#jie").show();
                            }
                            else {
                                $("#jie").hide();
                            }
                        })
                        $(":radio[name='pay[paypal_status]']").click(function () {
                            if ($(this).val() == 1) {
                                $("#paypal").show();
                            }
                            else {
                                $("#paypal").hide();
                            }
                        })
                        $(":radio[name='pay[alipay]']").click(function () {
                            if ($(this).val() == 1) {
                                $("#alipay_block").show();
                                //新接口
                                $("#alipay_public_api").show();
                            }
                            else {
                                $("#alipay_block").hide();
                            }
                        })
                        $(":radio[name='pay[yeepay]']").click(function () {
                            if ($(this).val() == 1) {
                                $("#yeepay_set").show();
                            }
                            else {
                                $("#yeepay_set").hide();
                            }
                        })

                        $(":radio[name='pay[alipay_withdrawals]']").click(function () {
                            if ($(this).val() == 1) {
                                $("#alipay_withdrawals").show();

                                //新接口
                                $("#alipay_public_api").show();
                            }
                            else {
                                $("#alipay_withdrawals").hide();
                            }
                        })
                        $(":radio[name='pay[credit]']").click(function () {
                            if ($(this).val() == 1) {
                                $("#balance").show();
                            }
                            else {
                                $("#balance").hide();
                            }
                        })
                        //支付宝公私钥
                        // $(":radio[name='pay[api_version]']").click(function () {
                        //     if ($(this).val() == 2) {
                        //         $("#new_open_alipay").show();
                        //         $("#old_open_alipay").hide();
                        //     }
                        //     else {
                        //         $("#new_open_alipay").hide();
                        //         $("#old_open_alipay").show();
                        //     }
                        // })
                        $(":radio[name='pay[another]']").click(function () {
                            if ($(this).val() == 1) {
                                $("#another").show();
                            }
                            else {
                                $("#another").hide();
                            }
                        })
                        $(":radio[name='pay[remittance]']").click(function () {
                            if ($(this).val() == 1) {
                                $("#remittance").show();
                            }
                            else {
                                $("#remittance").hide();
                            }
                        })
                        //微信
                        $(":button[name='from_new_weixin_certificate']").click(function () {
                            $('#new_weixin_cert').val('');
                            $('#new_weixin_cert').show();
                            $('#new_weixin_key').val('');
                            $('#new_weixin_key').show();
                            $(":button[name='from_new_weixin_certificate']").hide();
                        })
                        //支付宝
                        $(":button[name='rsa_alipay_key']").click(function () {
                            $('#rsa_private_key').val('');
                            $('#rsa_private_key').show();
                            $('#rsa_public_key').val('');
                            $('#rsa_public_key').show();
                            $(":button[name='rsa_alipay_key']").hide();
                        })
                    })
                </script>
            </form>
        </div>
    </div>
@endsection