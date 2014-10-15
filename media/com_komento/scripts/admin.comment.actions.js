Komento.module("admin.comment.actions",function(a){var b=this;Komento.require().library("dialog").script("komento.common","admin.language").done(function(){var c={};Komento.options.jversion=="1.5"?(c.published="images/tick.png",c.unpublished="images/publish_x.png"):(c.published="templates/bluestork/images/admin/tick.png",c.unpublished="templates/bluestork/images/admin/publish_x.png"),Komento.actions={loadReplies:function(b){var c=a(".kmt-row").length;Komento.ajax("admin.views.comments.loadreplies",{parentId:b,startCount:c},{success:function(c){a("#kmt-"+b).after(c).find(".linked-cell").text("-"),a(".kmt-row").each(function(a,b){var c=a%2;b.removeClass("row1","row0").addClass("row"+c)}),a("#toggle").attr("onClick","checkAll("+a(".kmt-row").length+");")}})},submit:function(b,c){a(".foundryDialog").length!=0&&a(".foundryDialog").controller().close(),Komento.actions.affectchild=c;var d=[],e=[];a('input[type="checkbox"]:checked').each(function(c,f){if(f.value!=""){d.push(f.value),e.push(a("#kmt-"+f.value));var g;b=="unstick"||b=="stick"?g="sticked":g="published",a("#kmt-"+f.value).find("."+g+"-cell a img").attr("src",Komento.options.spinner)}}),Komento.ajax("admin.views.comments."+b,{ids:d,affectchild:c},{success:function(){var c=[],d=[];a.each(e,function(a,e){Komento.actions[b](e),e.attr("childs")>0&&c.push(1),e.attr("parentid")!=0&&d.push(1)}),(b=="publish"&&d.length>0||b!="publish"&&c.length>0)&&Komento.actions[b+"Dialog"]()},fail:function(){}})},publish:function(b){var d=b.find(".published-cell a").attr("onclick").replace("unpublish","publish").replace("publish","unpublish");b.find(".published-cell a").attr("onclick",d).attr("title",a.language("COM_KOMENTO_UNPUBLISH_ITEM")),b.find(".published-cell a img").attr("src",c.published),Komento.actions.publishParent(b),Komento.actions.publishChild(b)},publishParent:function(b){if(!b.exists())return;var d=b.find(".published-cell a").attr("onclick").replace("unpublish","publish").replace("publish","unpublish");b.find(".published-cell a").attr("onclick",d).attr("title",a.language("COM_KOMENTO_UNPUBLISH_ITEM")),b.find(".published-cell a img").attr("src",c.published),b.attr("parentid")!=0&&Komento.actions.publishParent(a("#kmt-"+b.attr("parentid")))},publishChild:function(b){if(!b.exists())return;var d=b.find(".published-cell a").attr("onclick").replace("unpublish","publish").replace("publish","unpublish");b.find(".published-cell a").attr("onclick",d).attr("title",a.language("COM_KOMENTO_UNPUBLISH_ITEM")),b.find(".published-cell a img").attr("src",c.published);if(Komento.actions.affectchild==1&b.attr("childs")>0){var e=b.attr("id").split("-")[1];Komento.actions.publishChild(a('.kmt-row[parentid="'+e+'"]'))}},publishDialog:function(){a.dialog(a.language("COM_KOMENTO_PARENT_PUBLISHED"))},publishParentDialog:function(){a.dialog(a.language("COM_KOMENTO_PARENT_PUBLISHED"))},unpublish:function(b){if(!b.exists())return;var d=b.find(".published-cell a").attr("onclick").replace("unpublish","publish");b.find(".published-cell a").attr("onclick",d).attr("title",a.language("COM_KOMENTO_PUBLISH_ITEM")),b.find(".published-cell a img").attr("src",c.unpublished);if(b.attr("childs")>0){var e=b.attr("id").split("-")[1];Komento.actions.unpublish(a('.kmt-row[parentid="'+e+'"]'))}},unpublishDialog:function(){a.dialog(a.language("COM_KOMENTO_CHILD_UNPUBLISHED"))},stick:function(a){var b=a.find(".sticked-cell a").attr("onclick").replace("stick","unstick");a.find(".sticked-cell a").attr("onclick",b),a.find(".sticked-cell a img").attr("src","/administrator/components/com_komento/assets/images/sticked.png")},stickDialog:function(){},unstick:function(a){var b=a.find(".sticked-cell a").attr("onclick").replace("unstick","stick");a.find(".sticked-cell a").attr("onclick",b),a.find(".sticked-cell a img").attr("src","/administrator/components/com_komento/assets/images/unsticked.png")},unstickDialog:function(){}},Komento.prepare={checkChild:function(){var b=[];return a('input[type="checkbox"]:checked').each(function(c,d){d.value!=""&&a("#kmt-"+d.value).attr("childs")>0&&b.push(1)}),b.length>0?!0:!1},remove:function(){var b,c;Komento.prepare.checkChild()?(b=a.language("COM_KOMENTO_CONFIRM_DELETE_AFFECT_ALL_CHILD"),c='<button onclick="Komento.prepare.removeall()">'+a.language("COM_KOMENTO_DELETE_ALL_CHILD")+"</button>",c+='<button onclick="Komento.prepare.removesingle()">'+a.language("COM_KOMENTO_DELETE_MOVE_CHILD_UP")+"</button>"):(b=a.language("COM_KOMENTO_CONFIRM_DELETE"),c='<button onclick="Komento.prepare.removeall()">'+a.language("COM_KOMENTO_DELETE_COMMENT")+"</button>");var d='<div style="text-align: center;"><p>'+b+"</p>"+c+"</div>";a.dialog(d)},removeall:function(){prepareSubmit("remove",1)},removesingle:function(){prepareSubmit("remove",0)},publish:function(){if(Komento.prepare.checkChild()){var b=a.language("COM_KOMENTO_CONFIRM_PUBLISH_AFFECT_ALL_CHILD"),c='<button onclick="Komento.prepare.publishall()">'+a.language("COM_KOMENTO_PUBLISH_ALL_CHILD")+"</button>";c+='<button onclick="Komento.prepare.publishsingle()">'+a.language("COM_KOMENTO_PUBLISH_SINGLE")+"</button>";var d='<div style="text-align: center;"><p>'+b+"</p>"+c+"</div>";a.dialog(d)}else Komento.actions.submit("publish",1)},publishall:function(){Komento.actions.submit("publish",1)},publishsingle:function(){Komento.actions.submit("publish",0)},unpublish:function(){Komento.actions.submit("unpublish",1)},stick:function(){Komento.actions.submit("stick",1)},unstick:function(){Komento.actions.submit("unstick",1)}},window.submitbutton=function(a){Komento.prepare[a]()},window.prepareSubmit=function(b,c){a(".foundryDialog").length!=0&&a(".foundryDialog").controller().close(),document.adminForm.affectchild.value=c,submitform(b)},window.listItemTask=function(a,b){var c=document.adminForm,d=c[a];if(d){for(var e=0;!0;e++){var f=c["cb"+e];if(!f)break;f.checked=!1}d.checked=!0,c.boxchecked.value=1,submitbutton(b)}return!1},b.resolve()})});