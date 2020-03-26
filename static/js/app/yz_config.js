require.config({
    baseUrl: '/static/resource/js/app',
    paths: {
        'jquery': window.sysinfo['static_url'] + 'js/jquery-2.2.3.min',
        'jquery.ui': '../lib/jquery-ui-1.10.3.min',
        'jquery.caret': '../lib/jquery.caret',
        'jquery.jplayer': '../../components/jplayer/jquery.jplayer.min',
        'jquery.zclip': '../../components/zclip/jquery.zclip.min',
        'bootstrap': '../lib/bootstrap.min',
        'bootstrap.switch': '../../components/switch/bootstrap-switch.min',
        'angular': '../lib/angular.min',
        'angular.sanitize': '../lib/angular-sanitize.min',
        'underscore': '../lib/underscore-min',
        'chart': '../lib/chart.min',
        'moment': '../lib/moment',
        'filestyle': '../lib/bootstrap-filestyle.min',
        'datetimepicker': '../../components/datetimepicker/jquery.datetimepicker',
        'daterangepicker': '../../components/daterangepicker/daterangepicker',
        'colorpicker': '../../components/colorpicker/spectrum',
        'map': 'https://api.map.baidu.com/getscript?v=2.0&ak=F51571495f717ff1194de02366bb8da9&services=&t=20140530104353',
        'editor': '../../components/tinymce/tinymce.min',
        'kindeditor':'../../components/kindeditor/lang/zh_CN',
        'kindeditor.main':'../../components/kindeditor/kindeditor-min',
        'css': '../lib/css.min',
        'webuploader' : '../../components/webuploader/webuploader.min',
        //'fileUploader' : window.sysinfo['static_url'] + (window.sysinfo['is_new'] !='1' ? 'resource/js/app/fileUploader': 'resource/js/app/fileUploader.min'),
        //视频上传需要
        // 'fileUploader' : window.sysinfo['static_url'] + (window.sysinfo['is_new'] !='1' ? 'resource/js/app/fileUploader': 'js/app/fileUploader'),
        'fileUploader' : window.sysinfo['static_url'] + 'js/app/fileUploader',
        'json2' : '../lib/json2',
        'wapeditor' : './wapeditor',
        'jquery.wookmark': '../lib/jquery.wookmark.min',
        'validator': '../lib/bootstrapValidator.min',
        'select2' : window.sysinfo['static_url'] +'js/dist/select2/select2_locale_zh-CN',
        'clockpicker': '../../components/clockpicker/clockpicker.min',
        'jquery.qrcode': '../lib/jquery.qrcode.min',
        'raty': '../lib/raty.min',
        'district' : '../lib/district',
        'contextMenu':window.sysinfo['static_url'] + 'js/app/contextMenu/jquery.contextMenu',
        'hammer': '../lib/hammer.min',
        'iconset-fontawesome': window.sysinfo['static_url'] + 'yunshop/plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.3.0.min',
        'iconpicker': window.sysinfo['static_url'] + 'yunshop/plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker'
    },
    shim:{

        'jquery.caret': {
            exports: "$",
            deps: ['jquery']
        },
        'jquery.jplayer': {
            exports: "$",
            deps: ['jquery']
        },
        'bootstrap': {
            exports: "$",
            deps: ['jquery']
        },
        'bootstrap.switch': {
            exports: "$",
            deps: ['bootstrap', 'css!../../components/switch/bootstrap-switch.min.css']
        },
        'angular': {
            exports: 'angular',
            deps: ['jquery']
        },
        'angular.sanitize': {
            exports: 'angular',
            deps: ['angular']
        },
        'emotion': {
            deps: ['jquery']
        },
        'chart': {
            exports: 'Chart'
        },
        'filestyle': {
            exports: '$',
            deps: ['bootstrap']
        },
        'daterangepicker': {
            exports: '$',
            deps: ['bootstrap', 'moment', 'css!../../components/daterangepicker/daterangepicker.css']
        },
        'datetimepicker' : {
            exports : '$',
            deps: ['jquery', 'css!../../components/datetimepicker/jquery.datetimepicker.css']
        },
        'kindeditor': {
            deps: ['kindeditor.main', 'css!../../components/kindeditor/themes/default/default.css']
        },
        'colorpicker': {
            exports: '$',
            deps: ['css!../../components/colorpicker/spectrum.css']
        },
        'map': {
            exports: 'BMap'
        },
        'json2': {
            exports: 'JSON'
        },
        'fileUploader': {
            deps: ['webuploader', 'css!../../components/webuploader/webuploader.css', 'css!../../components/webuploader/style.css']
        },
        'wapeditor' : {
            exports : 'angular',
            deps: ['angular.sanitize', 'jquery.ui', 'underscore', 'fileUploader', 'json2', 'datetimepicker']
        },
        'jquery.wookmark': {
            exports: "$",
            deps: ['jquery']
        },
        'validator': {
            exports: "$",
            deps: ['bootstrap']
        },
        'iconset-fontawesome':{
            exports: "$",
            deps: ['bootstrap','css!'+ window.sysinfo['static_url'] +'yunshop/plugins/bootstrap-iconpicker/icon-fonts/font-awesome-4.2.0/css/font-awesome.min.css']
        },
        'iconpicker':{
            exports: "$",
            deps: ['bootstrap','iconset-fontawesome','css!'+ window.sysinfo['static_url'] +'yunshop/plugins/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css']
        },
        'select2': {
            deps: ['css!'+ window.sysinfo['static_url'] +'js/dist/select2/select2.css', window.sysinfo['static_url'] +'js/dist/select2/select2.min.js']
        },
        'clockpicker': {
            exports: "$",
            deps: ['css!../../components/clockpicker/clockpicker.min.css', 'bootstrap']
        },
        'jquery.qrcode': {
            exports: "$",
            deps: ['jquery']
        },
        'district' : {
            exports : "$",
            deps : ['jquery']
        },
        'hammer' : {
            exports : 'hammer'
        },
        'contextMenu': {
            exports: "$",
            deps: ['jquery','css!'+window.sysinfo['static_url'] + 'js/app/contextMenu/jquery.contextMenu']
        }
    }
});