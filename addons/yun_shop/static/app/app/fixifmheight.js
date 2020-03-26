
(function(){var each=function(ary,cb){var key=0;if(ary&&ary.length){while(ary[key]){if(false===cb.call(ary[key],ary[key],key)){return;}
	key+=1;}}},gW=function(a){var b='width',c,d,e;if(document.defaultView&&document.defaultView.getComputedStyle){b=b.replace(/([A-Z]|^ms)/g,"-$1").toLowerCase();if((d=a.ownerDocument.defaultView)&&(e=d.getComputedStyle(a,null))){c=e.getPropertyValue(b)}
	return c}else if(document.documentElement.currentStyle){d=a.currentStyle&&a.currentStyle[b];e=a.style;if(d===null&&e&&(c=e[b])){d=c}
	return d}};setTimeout(function(){each(document.getElementsByTagName('iframe'),function(ifm){var ifmSrc=(ifm.src||'').toLowerCase();if(ifmSrc.indexOf('play.video.qcloud.com')<0&&ifmSrc.indexOf('playvideo.qcloud.com')<0){return;}
	var search=ifm.src,map={};each((search.split('?')[1]||'').split('&'),function(str){var sp=str.split('=');map[sp[0]]=sp[1];});if(!!map['$fileid']){var tw=(gW(ifm)+'').toLowerCase().replace('px','')-0;if(tw>1){if(tw>map['$sw']){tw=map['$sw']-0;}
		var pureGH=tw*(map['$sh']/map['$sw']);ifm.height=pureGH>190?(pureGH|0):190;}}});},300);})();/*  |xGv00|a0b12c29b3a13431306c9a14bac6af98 */
