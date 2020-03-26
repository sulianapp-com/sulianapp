/**
 * 空白随鼠标移动
 * @authors Your Name (you@example.org)
 * @date    2014-08-31 09:38:16
 * @version $Id$
 */
var caseFun = Object();
caseFun.txtPos = function()
{
    //字符串上下居中
    $(".j-list-case li").each(function(){
        var objTxt = $(".j-list-case li").find(".txt").children("span");
        var height = objTxt.height();
        objTxt.css("margin-top",($(".j-list-case li").height() - height) / 2 + "px");
    });
}
caseFun.moveFun = function() { //文字框移动
    var moveTime = 200;
    var moveIn = function(obj, direction) {
        switch (direction) {
            case 0:
                obj.css({
                    "top": "-242px",
                    "left": "0"
                });
                break;
            case 1:
                obj.css({
                    "top": "0",
                    "left": "242px"

                });
                break;
            case 2:
                obj.css({
                    "top": "242px",
                    "left": "0"
                });
                break;
            case 3:
                obj.css({
                    "top": "0",
                    "left": "-242px"
                });
                break;
        }
        obj.stop().animate({
            "top": "0",
            "left": "0"
        }, moveTime, 'easeOutSine');
    }
    var moveOut = function(obj, direction) {
        switch (direction) {
            case 0:
                obj.stop().animate({
                    "top": "-242px",
                    "left": "0"
                }, moveTime, 'easeOutSine');
                break;
            case 1:
                obj.stop().animate({
                    "top": "0",
                    "left": "242px"
                }, moveTime, 'easeOutSine');
                break;
            case 2:
                obj.stop().animate({
                    "top": "242px",
                    "left": "0"
                }, moveTime, 'easeOutSine');
                break;
            case 3:
                obj.stop().animate({
                    "top": "0",
                    "left": "-242px"
                }, moveTime, 'easeOutSine');
                break;
        }
    }

    $(".j-list-case li").bind("mouseenter mouseleave", function(e) {
        var obj = $(this)
        var objTxt = obj.find(".txt");
        var w = obj.width();
        var h = obj.height();
        var x = (e.pageX - this.offsetLeft - (w / 2)) * (w > h ? (h / w) : 1);
        var y = (e.pageY - this.offsetTop - (h / 2)) * (h > w ? (w / h) : 1);
        var direction = Math.round((((Math.atan2(y, x) * (180 / Math.PI)) + 180) / 90) + 3) % 4;
        var eventType = e.type;

        if (e.type == 'mouseenter') {
            moveIn(objTxt, direction);
            $(this).children("img").animate({width:"120%",height:"120%",marginTop:"-10%",marginLeft:"-10%"},100);

        } else {
            moveOut(objTxt, direction);
            $(this).children("img").animate({width:"100%",height:"100%",marginTop:"0",marginLeft:"0"},100);
        }
    });

    $('.j-list-case .txt').click(function() { //兼容移动端回退状态
        var obj = $(this);
        moveOut(obj, 0);
    })
}
caseFun.txtPos();
caseFun.moveFun();
