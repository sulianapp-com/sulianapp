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
function cascdeInit(v1,v2,v3,v4){
   getProvinceData(v1,v2,v3,v4);
}

// 获取省数据
function getProvinceData(v1,v2,v3,v4){
    $.ajax({
        url:window.sysinfo.get_address,
        data:{type:'province'},
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data){
            _provinceNetworkData = data;
            getCityData(v1,v2,v3,v4);
        }
    }) 
}

// 获取城市数据
function getCityData(v1,v2,v3,v4){

    $.ajax({
        url:window.sysinfo.get_address,
        data:{type:'city',parentid:v1},
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data){
            //console.log(data)
            _cityNetworkData = data;
            getDistrictData(v1,v2,v3,v4);
        }
    })  
}

// 获取区数据
function getDistrictData(v1,v2,v3,v4){

    $.ajax({
        url:window.sysinfo.get_address,
        data:{type:'district',parentid:v2},
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data){
            //console.log(data)
            _districtNetworkData = data;
            getStreetData(v1,v2,v3,v4);
        }
    }) 
}

// 获取街道
function getStreetData(v1,v2,v3,v4){

    $.ajax({
        url:window.sysinfo.get_address,
        data:{type:'street',parentid:v3},
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data){
            //console.log(data)
            _streetNetworkData = data;
            setProvinceData(v1,v2,v3,v4);
        }
    }) 
}
// 设置省
function setProvinceData(v1,v2,v3,v4){
    var _html = "";
    _html += '<option value="0">请选择省份</option>';
    for (var i = 0; i < _provinceNetworkData.length; i++) {
        var _selected = '';
        if (v1 == _provinceNetworkData[i].id) {
            _selected = 'selected="selected"';
        }
        _html += '<option '+_selected+' value="'+_provinceNetworkData[i].id+'">'+_provinceNetworkData[i].areaname+'</option>';
    }
    $("#sel-provance").html(_html);
    setCityData(v1,v2,v3,v4);
}

//设置市
function setCityData(v1,v2,v3,v4){
    var _html = '<option value="0">请选择城市</option>';
    console.log(_cityNetworkData)
    for (var i = 0; i < _cityNetworkData.length; i++) {
        var _selected = '';
        if (v2 == _cityNetworkData[i].id) {
            _selected = 'selected="selected"';
        }
        _html += '<option '+_selected+' value="'+_cityNetworkData[i].id+'">'+_cityNetworkData[i].areaname+'</option>';
    }

    $("#sel-city").html(_html);
    setDistrictData(v1,v2,v3,v4);
}

//设置区
function setDistrictData(v1,v2,v3,v4){
    var _html = "";
    _html += '<option value="0">请选择区域</option>';
    for (var i = 0; i < _districtNetworkData.length; i++) {
        var _selected = '';
        if (v3 == _districtNetworkData[i].id) {
            _selected = 'selected="selected"';
        }
        _html += '<option '+_selected+' value="'+_districtNetworkData[i].id+'">'+_districtNetworkData[i].areaname+'</option>';
    }
    $("#sel-area").html(_html);
    setStreetData(v1,v2,v3,v4);
}

//设置区
function setStreetData(v1,v2,v3,v4){
    var _html = "";
    _html += '<option value="0">请选择街道</option>';
    for (var i = 0; i < _streetNetworkData.length; i++) {
        var _selected = '';
        if (v4 == _streetNetworkData[i].id) {
            _selected = 'selected="selected"';
        }
        _html += '<option '+_selected+' value="'+_streetNetworkData[i].id+'">'+_streetNetworkData[i].areaname+'</option>';
    }
    $("#sel-street").html(_html);
}
/*
//依据省设置城市，县，街道
*/
function selectCity() {
    var _provanceid = $("#sel-provance").val();
    getCityData(_provanceid);
    setProvinceData(_provanceid);
}

function selectcounty() {
    var _provanceid = $("#sel-provance").val();
    var _cityid = $("#sel-city").val();
    getCityData(_provanceid,_cityid);
    setProvinceData(_provanceid,_cityid);
}
function selectstreet() {
    var _provanceid = $("#sel-provance").val();
    var _cityid = $("#sel-city").val();
    var _areaid = $("#sel-area").val();
    getDistrictData(_provanceid,_cityid,_areaid);
    setProvinceData(_provanceid,_cityid,_areaid);
}
