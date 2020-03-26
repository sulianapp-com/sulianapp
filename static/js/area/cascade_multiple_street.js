/**
 * @name        jQuery Cascdejs plugin
 * @author      rayyang
 * @version     1.0
 * @level       4级
 */

//首先需要初始化
var _provinceNetworkData =null;
var _cityNetworkData =null;
var _districtNetworkData =null;
var _streetNetworkData =null;
var datas = [];
function cascdeInit(v1,v2,v3,v4,v5){
    getProvinceData(v1,v2,v3,v4,v5);
}

// 获取省数据
function getProvinceData(v1,v2,v3,v4,v5){
    datas[v5] = [];

    $.ajax({
        url:window.sysinfo.get_address,
        data:{type:'province'},
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data){
            datas[v5]['_provinceNetworkData'] = data;
            getCityData(v1,v2,v3,v4,v5);
        }
    })
}

// 获取城市数据
function getCityData(v1,v2,v3,v4,v5){

    $.ajax({
        url:window.sysinfo.get_address,
        data:{type:'city',parentid:v1},
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data){
            //console.log(data)
            datas[v5]['_cityNetworkData'] = data;
            getDistrictData(v1,v2,v3,v4,v5);
        }
    })
}

// 获取区数据
function getDistrictData(v1,v2,v3,v4,v5){

    $.ajax({
        url:window.sysinfo.get_address,
        data:{type:'district',parentid:v2},
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data){
            //console.log(data)
            datas[v5]['_districtNetworkData'] = data;
            getStreetData(v1,v2,v3,v4,v5);
        }
    })
}

// 获取街道
function getStreetData(v1,v2,v3,v4,v5){

    $.ajax({
        url:window.sysinfo.get_address,
        data:{type:'street',parentid:v3},
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data){
            //console.log(data)
            datas[v5]['_streetNetworkData'] = data;
            setProvinceData(v1,v2,v3,v4,v5);
        }
    })
}
// 设置省
function setProvinceData(v1,v2,v3,v4,v5){
    var _html = "";
    _html += '<option value="0">请选择省份</option>';
    _provinceNetworkData = datas[v5]['_provinceNetworkData'];

    for (var i = 0; i < _provinceNetworkData.length; i++) {
        var _selected = '';
        if (v1 == _provinceNetworkData[i].id) {
            _selected = 'selected="selected"';
        }
        _html += '<option '+_selected+' value="'+_provinceNetworkData[i].id+'">'+_provinceNetworkData[i].areaname+'</option>';
    }
    $("#sel-provance"+v5).html(_html);
    setCityData(v1,v2,v3,v4,v5);
}

//设置市
function setCityData(v1,v2,v3,v4,v5){
    var _html = '<option value="0">请选择城市</option>';
    console.log(_cityNetworkData)
    _cityNetworkData = datas[v5]['_cityNetworkData'];
    for (var i = 0; i < _cityNetworkData.length; i++) {
        var _selected = '';
        if (v2 == _cityNetworkData[i].id) {
            _selected = 'selected="selected"';
        }
        _html += '<option '+_selected+' value="'+_cityNetworkData[i].id+'">'+_cityNetworkData[i].areaname+'</option>';
    }
    $("#sel-city"+v5).html(_html);
    setDistrictData(v1,v2,v3,v4,v5);
}

//设置区
function setDistrictData(v1,v2,v3,v4,v5){
    var _html = "";
    _html += '<option value="0">请选择区域</option>';
    _districtNetworkData = datas[v5]['_districtNetworkData'];
    for (var i = 0; i < _districtNetworkData.length; i++) {
        var _selected = '';
        if (v3 == _districtNetworkData[i].id) {
            _selected = 'selected="selected"';
        }
        _html += '<option '+_selected+' value="'+_districtNetworkData[i].id+'">'+_districtNetworkData[i].areaname+'</option>';
    }
    $("#sel-area"+v5).html(_html);
    setStreetData(v1,v2,v3,v4,v5);
}

//设置区
function setStreetData(v1,v2,v3,v4,v5){
    var _html = "";
    _html += '<option value="0">请选择街道</option>';
    _streetNetworkData = datas[v5]['_streetNetworkData'];
    for (var i = 0; i < _streetNetworkData.length; i++) {
        var _selected = '';
        if (v4 == _streetNetworkData[i].id) {
            _selected = 'selected="selected"';
        }
        _html += '<option '+_selected+' value="'+_streetNetworkData[i].id+'">'+_streetNetworkData[i].areaname+'</option>';
    }
    $("#sel-street"+v5).html(_html);
}
/*
//依据省设置城市，县，街道
*/
function selectCity(count) {
    var _provanceid = $("#sel-provance"+count).val();

    getCityData(_provanceid, '', '', '', count);
    setProvinceData(_provanceid, '', '', '', count);
}

function selectcounty(count) {
    var _provanceid = $("#sel-provance"+count).val();
    var _cityid = $("#sel-city"+count).val();
    getCityData(_provanceid,_cityid, '', '', count);
    setProvinceData(_provanceid,_cityid, '', '', count);
}
function selectstreet(count) {
    var _provanceid = $("#sel-provance"+count).val();
    var _cityid = $("#sel-city"+count).val();
    var _areaid = $("#sel-area"+count).val();
    getDistrictData(_provanceid,_cityid,_areaid, '', count);
    setProvinceData(_provanceid,_cityid,_areaid, '', count);
}
