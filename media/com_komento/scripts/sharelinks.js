Komento.module("sharelinks",function(a){var b=this;a.sharelinks=function(b,c,d,e){var f=this[c].call(a(b),c,d,e)},a.fn.sharelinks=function(b,c,d){var e=this;if(!e.attr("loaded")){e.attr("loaded",1);if(!b)var b=e.attr("type");if(!c){var f,g,h;e.attr("url")&&(f=encodeURIComponent(e.attr("url"))),e.attr("title")&&(g=encodeURIComponent(e.attr("title"))),e.attr("content")&&(h=encodeURIComponent(e.attr("content")));var c={url:f,title:g,content:h}}a(document).ready(function(){var d=function(b,c,d){a.sharelinks.cleanup.call(b),b.bind("click",function(e){a.sharelinks.popup.call(b,d,c)})};a.sharelinks[b].call(e,b,c,d)})}},a.sharelinks.facebook=function(a,b,c){var d=this,e="http://www.facebook.com/sharer.php?s=100",f,g,h,i;b.url&&(f=b.url),b.title?(g=b.title,b.content&&(h=b.content)):b.content&&(g="Comments",h=b.content),b.image&&(i=b.image),e+="&p[url]="+f+"&p[title]="+g+"&p[summary]="+h+"&p[images]="+i,c(d,e,a)},a.sharelinks.twitter=function(a,b,c){var d=this,e="http://twitter.com/intent/tweet",f,g;b.url&&(f=b.url),b.title?g=b.title:b.content&&(g=b.content),e+="?url="+f+"&text="+g,c(d,e,a)},a.sharelinks.googleplus=function(a,b,c){var d=this,e="http://plus.google.com/share",f;b.url&&(f=b.url),e+="?url="+f,c(d,e,a)},a.sharelinks.linkedin=function(a,b,c){var d=this,e="http://linkedin.com/shareArticle?mini=true",f,g,h;b.url&&(f=b.url),b.title?(g=b.title,b.content&&(h=b.content)):b.content&&(g=b.content),e+="&url="+f+"&title="+g+"&summary="+h,c(d,e,a)},a.sharelinks.pinterest=function(a,b,c){var d=this,e="http://pinterest.com/pin/create/button/",f,g,h=b.image;b.url&&(f=b.url),b.title?g=b.title:b.content&&(g=b.content),e+="?media="+h+"&url="+f+"&description="+g,c(d,e,a)},a.sharelinks.tumblr=function(a,b,c){var d=this,e="http://www.tumblr.com/share/link",f,g,h;b.url&&(f=b.url),b.title?(g=b.title,b.content&&(h=b.content)):b.content&&(g=b.content),e+="?url="+f+"&name="+g+"&description="+h,c(d,e,a)},a.sharelinks.digg=function(a,b,c){var d=this,e="http://digg.com/submit",f,g;b.url&&(f=b.url),b.title?g=b.title:b.content&&(g=b.content),e+="?url="+f+"&title="+g,c(d,e,a)},a.sharelinks.delicious=function(a,b,c){var d=this,e="http://delicious.com/post",f,g;b.url&&(f=b.url),b.title?g=b.title:b.content&&(g=b.content),e+="?url="+f+"&title="+g,c(d,e,a)},a.sharelinks.reddit=function(a,b,c){var d=this,e="http://reddit.com/submit",f,g;b.url&&(f=b.url),b.title?g=b.title:b.content&&(g=b.content),e+="?url="+f+"&title="+g,c(d,e,a)},a.sharelinks.stumbleupon=function(a,b,c){var d=this,e="http://www.stumbleupon.com/submit",f,g;b.url&&(f=b.url),b.title?g=b.title:b.content&&(g=b.content),e+="?url="+f+"&title="+g,c(d,e,a)},a.sharelinks.indentica=function(a,b,c){var d=this,e="http://identi.ca/index.php?action=bookmarkpopup",f,g;b.url&&(f=b.url),b.title?g=b.title:b.content&&(g=b.content),e+="&url="+f+"&title="+g,c(d,e,a)},a.sharelinks.stumpedia=function(a,b,c){var d=this,e="http://www.stumpedia.com/submit",f;b.url&&(f=b.url),e+="?url="+f,c(d,e,a)},a.sharelinks.technorati=function(a,b,c){var d=this,e="http://technorati.com/faves";add,b.url&&(add=b.url),e+="?add="+url,c(d,e,a)},a.sharelinks.blogmarks=function(a,b,c){var d=this,e="http://blogmarks.net/my/new.php?mini=1",f,g;b.url&&(f=b.url),b.title?g=b.title:b.content&&(g=b.content),e+="&url="+f+"&title="+g,c(d,e,a)},a.sharelinks.dialog=function(b){a.dialog(b)},a.sharelinks.cleanup=function(){var a=this;a.removeAttr("url").removeAttr("type").removeAttr("title").removeAttr("content").removeAttr("image").removeAttr("commentid").attr("loaded",1)},a.sharelinks.popup=function(a,b){var c="menubar=0,resizable=0,scrollbars=0,";c+="width=660,height=320",window.open(b,"",c)},b.resolve()});