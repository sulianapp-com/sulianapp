var YDB = new YDBOBJ();
// var YDB_UUID = 0;
var YDB_isWXApp = true;
var YDB_GPSla = 0;
var YDB_GPSlo = 0;
if (isApp()) {
    //YDB.GetGPS("DoWithGPS");
    YDB.isWXAppInstalled("installstate");
}

//回调是否安装微信
function installstate(state){
    if(state == 0){
        YDB_isWXApp = false;
    }
}
//回调定位坐标
// function DoWithGPS (la,lo){
//     YDB_GPSla = la;
//     YDB_GPSlo = lo;
// }
//分享回调
function Sharesback(state) {
    YDB.GoBack();
}
//是否为app打开
function isApp(){
    var ua = window.navigator.userAgent.toLowerCase();
    if (ua.indexOf('yunzshop') > -1) {
        return true;
    } else {
        return false;
    }
}
