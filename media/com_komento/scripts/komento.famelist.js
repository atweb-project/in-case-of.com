Komento.module("komento.famelist",function(a){var b=this;Komento.require().library("effects/highlight","effects/fade","effects/drop").image(Komento.options.spinner).script("sharelinks","markitup","komento.common","komento.commentitem").done(function(){Komento.Controller("FameList",{defaults:{"{commentList}":".kmt-list","{stickList}":".stickList .kmt-list","{loveList}":".loveList .kmt-list","{commentItem}":".kmt-item","{stickItem}":".stickList .kmt-list .kmt-item","{loveItem}":".loveList .kmt-list .kmt-item","{noComment}":".kmt-empty-comment","{loadMore}":".loadMore","{navs}":".navs","{tabs}":".tabs","{commentText}":".commentText","{commentInfo}":".commentInfo","{commentForm}":".commentForm","{deleteButton}":".deleteButton","{editButton}":".editButton","{saveEditButton}":".saveEditButton","{editForm}":".editForm","{editInput}":".editInput","{replyButton}":".replyButton","{shareBox}":".shareBox","{reportButton}":".reportButton","{statusButton}":".statusButton","{statusOptions}":".statusOptions","{publishButton}":".publishButton","{unpublishButton}":".unpublishButton","{stickButton}":".stickButton","{likeButton}":".likeButton","{likesCounter}":".likesCounter","{parentLink}":".parentLink","{parentContainer}":".parentContainer","{socialButton}":".socialButton",view:{editForm:"comment/item/edit.form",deleteDialog:"dialogs/delete.affectchild",publishDialog:"dialogs/publish.affectchild",unpublishDialog:"dialogs/unpublish.affectchild",deleteAttachment:"dialogs/delete.attachment"}}},function(b){return{init:function(){b.navs().length>0&&b.navs().eq(0).trigger("click")},ajaxinit:function(){b.generateSharelinks()},generateSharelinks:function(){var c=function(){b.socialButton().each(function(b,c){a(c).sharelinks()})};b.generateShortLinks(c)},generateShortLinks:function(c){var d=function(d){b.socialButton().each(function(b,c){if(!a(c).attr("loaded")){var e=a(c).attr("commentid"),f=d+"#kmt"+e;a(c).attr("url",f),a(c).parents(".kmt-share-balloon").find(".short-url").val(f)}}),c&&c()};Komento.shortenLink?d(Komento.shortenLink):a.shortenlink(Komento.contentLink,d)},"{navs} click":function(c){var d=a(c).attr("func"),e=a(c).attr("tab"),f=a("."+e);f.attr("loaded")||(b[d](),(e=="stickList"&&Komento.options.konfig.enforce_live_stickies!="1"||e=="loveList"&&Komento.options.konfig.enforce_live_lovies!="1")&&f.attr("loaded",1)),b.tabs().hide(),f.show(),b.navs().removeClass("active"),c.addClass("active")},loadMainList:function(){b.loadComments("main")},loadStickList:function(){Komento.options.acl.read_stickies==1&&Komento.options.config.enable_stickies==1&&Komento.options.konfig.enable_ajax_load_stickies==1&&b.loadComments("stickies")},loadLoveList:function(){Komento.options.acl.read_lovies==1&&Komento.options.config.enable_lovies==1&&Komento.options.konfig.enable_ajax_load_lovies==1&&b.loadComments("lovies")},loadComments:function(c){var d,e,f,g;f=0;switch(c){case"stickies":d=".stickList",e=1,g="default",limit=parseInt(Komento.options.config.max_stickies);break;case"lovies":d=".loveList",e="all",g="love",limit=parseInt(Komento.options.config.max_lovies);break;default:d=".mainList",e="all",g="default",limit=parseInt(Komento.options.config.max_comments_per_page)}a(d).html('<div class="loading"><img src="'+Komento.options.spinner+'" />'+a.language("COM_KOMENTO_COMMENTS_LOADING")+"</div>"),Komento.ajax("site.views.komento.loadcomments",{type:c,component:Komento.component,cid:Komento.cid,sticked:e,threaded:f,sort:g,limit:limit,contentLink:Komento.contentLink},{success:function(c){var e=a(c);a(d).html(e),b.ajaxinit()},fail:function(){a(d).text(a.language("COM_KOMENTO_ERROR"))}})},stickComment:function(c){c=a(c),c.children(".kmt-wrap").attr("style","left-margin: 0px !important");var d=c.attr("id").split("-")[1],e=0;b.stickItem().length==0?b.loadStickList():b.stickItem().length<Komento.options.config.max_stickies&&(b.stickItem().each(function(b,f){if(a(f).attr("id").split("-")[1]>d){a(f).before(c),e=1;return}}),e==0&&b.stickList().append(a(c)))},unstickComment:function(a){b.stickList().find("#"+a).remove()},set:function(a){b.item=a.itemset(b.options)},"{deleteButton} click":function(a){b.set(a),b.showDeleteDialog()},"{editButton} click":function(a){b.set(a),a.checkClick()&&(a.loading(),a.checkSwitch()?b.edit(a):b.cancelEdit(a))},"{saveEditButton} click":function(a){b.set(a),b.saveEdit(a)},"{replyButton} click":function(a){b.set(a),Komento.options.konfig.enable_inline_reply==1?a.checkSwitch()?b.reply(a):b.cancelReply(a):b.kmt.form.staticReply(b.item.parentid)},"{reportButton} click":function(a){b.set(a),a.checkClick()&&(a.loading(),a.checkSwitch()?b.reportComment(a):b.cancelreportComment(a))},"{unpublishButton} click":function(){b.set(el),b.childs>0?b.showUnpublishDialog(el):b.unpublishComment(el)},"{stickButton} click":function(a){b.set(a),a.checkClick()&&(a.loading(),a.checkSwitch()?b.stick(a):b.unstick(a))},"{likesCounter} click":function(a){b.set(a),b.showLikesDialog()},"{likeButton} click":function(a){b.set(a),a.checkClick()&&(a.find("span").loading(),a.checkSwitch()?b.like(a):b.unlike(a))},"{parentLink} mouseover":function(c){b.set(c),Komento.options.config.enable_threaded==1?a("#"+b.item.parentid).addClass("kmt-highlight"):(b.item.element.mine.parentContainer.show(),b.item.element.mine.parentContainer.attr("loaded")==0&&(b.item.element.mine.parentContainer.html('<img src="'+Komento.options.spinner+'" />'),b.loadParent()))},"{parentLink} mouseout":function(c){b.set(c),Komento.options.config.enable_threaded==1?a("#"+b.item.parentid).removeClass("kmt-highlight"):b.item.element.mine.parentContainer.hide()},"{parentLink} click":function(c){b.set(c);var d=a("."+b.item.parentid);d.highlight()},"{attachmentDelete} click":function(a){b.set(a),b.showAttachmentDeleteDialog(a)},closeDialog:function(){a(".foundryDialog").length>0&&a(".foundryDialog").controller().close()},edit:function(){var c=b.item.mine.find("#"+b.item.commentid+"-edit");c.length==0?Komento.ajax("site.views.komento.getcommentraw",{id:b.item.id},{success:function(c){b.item.element.mine.editButton.text(a.language("COM_KOMENTO_COMMENT_EDIT_CANCEL")).switchOff().doneLoading().enable();var d=b.view.editForm({commentId:b.item.commentid,commentText:c});b.item.element.mine.commentText.after(d),b.item.element.mine.editForm=a(d),b.item.element.mine.editInput=b.item.element.mine.editForm.find(".editInput"),b.item.mine.data("item",b.item),b.item.element.mine.editInput.markItUp(a.getBBcodeSettings())},fail:function(){b.item.element.mine.editButton.text(a.language("COM_KOMENTO_ERROR")).doneLoading()}}):(c.show(),b.item.element.mine.editButton.text(a.language("COM_KOMENTO_COMMENT_EDIT_CANCEL")).switchOff().doneLoading().enable())},cancelEdit:function(){b.item.element.mine.editButton.text(a.language("COM_KOMENTO_COMMENT_EDIT")).switchOn().doneLoading().enable(),b.item.element.mine.editForm.hide()},saveEdit:function(){Komento.ajax("site.views.komento.editcomment",{id:b.item.id,edittedComment:b.item.element.mine.editInput.val()},{success:function(c,d,e){b.item.element.both.commentText.html(c),b.item.element.both.commentInfo.text(a.language("COM_KOMENTO_COMMENT_EDITTED_BY",d,e)).show(),b.cancelEdit()},fail:function(a){}})},reply:function(){var c=a(".commentForm");a(".formAlert").hide().text(""),a(b.options["{replyButton}"]).switchOn().find("span").text(a.language("COM_KOMENTO_COMMENT_REPLY")),b.item.element.mine.replyButton.switchOff().find("span").text(a.language("COM_KOMENTO_COMMENT_REPLY_CANCEL")),b.kmt.form.reply(b.item)},cancelReply:function(){a(b.options["{replyButton}"]).switchOn().find("span").text(a.language("COM_KOMENTO_COMMENT_REPLY")),b.kmt.form.cancelReply()},showDeleteDialog:function(){a.dialog({content:b.view.deleteDialog(!0,{childs:b.childs}),afterShow:function(){a(".foundryDialog").find(".delete-affectChild").click(function(){b.deleteComment(1)}),a(".foundryDialog").find(".delete-moveChild").click(function(){b.deleteComment(0)})}})},deleteComment:function(c){a(".foundryDialog").find(".kmt-delete-status").show(),Komento.ajax("site.views.komento.deletecomment",{id:b.item.id,affectChild:c},{success:function(){b.closeDialog(),c?b.deleteChild(b.item.id):b.moveChildUp(b.item.id,b.item.parentid),b.item.both.hide("fade",function(){b.item.both.remove()})},fail:function(){a(".foundryDialog").find(".kmt-delete-status").text(a.language("COM_KOMENTO_ERROR"))}})},deleteChild:function(c){a('li[parentid="'+c+'"]').each(function(){b.deleteChild(a(this).attr("id"))}).hide("fade",function(){a(this).remove()})},moveChildUp:function(c,d){a('li[parentid="'+c+'"]').attr("parentid",d).each(function(){a(this).removeClass("kmt-child-"+a(this).attr("depth")).addClass("kmt-child-"+(a(this).attr("depth")-1)),a(this).attr("depth",a(this).attr("depth")-1),b.moveChildUp(a(this).attr("id"))})},reportComment:function(){Komento.ajax("site.views.komento.action",{id:b.item.id,type:"report",action:"add"},{success:function(){b.item.element.both.reportButton.text(a.language("COM_KOMENTO_COMMENT_REPORTED")).switchOff().doneLoading().enable()},fail:function(){b.item.element.both.reportButton.text(a.language("COM_KOMENTO_ERROR"))}})},cancelreportComment:function(){Komento.ajax("site.views.komento.action",{id:b.item.id,type:"report",action:"remove"},{success:function(){b.item.element.both.reportButton.text(a.language("COM_KOMENTO_COMMENT_REPORT")).switchOn().doneLoading().enable()},fail:function(){b.item.element.both.reportButton.text(a.language("COM_KOMENTO_ERROR"))}})},showUnpublishDialog:function(){a.dialog({content:b.view.unpublishDialog(!0,{childs:b.childs}),afterShow:function(){a(".foundryDialog").find(".unpublish-affectChild").click(function(){b.unpublishComment()})}})},unpublishComment:function(){Komento.ajax("site.views.komento.unpublish",{id:b.item.id},{success:function(){b.closeDialog(),b.unpublishChild(b.item.id),b.item.both.hide("fade",function(){b.item.both.remove()})},fail:function(){}})},unpublishChild:function(c){a('li[parentid="'+c+'"]').each(function(){b.unpublishChild(a(this).attr("id"))}).hide("fade",function(){a(this).remove()})},stick:function(c){Komento.ajax("site.views.komento.stick",{id:b.item.id},{success:function(){b.item.element.mine.stickButton.text(a.language("COM_KOMENTO_COMMENT_UNSTICK")).switchOff().doneLoading().enable(),b.item.mine.addClass("kmt-sticked"),b.kmt.famelist.stickComment(b.item.mine.clone())},fail:function(){b.item.element.mine.stickButton.text(a.language("COM_KOMENTO_ERROR"))}})},unstick:function(){Komento.ajax("site.views.komento.unstick",{id:b.item.id},{success:function(){b.item.element.both.stickButton.text(a.language("COM_KOMENTO_COMMENT_STICK")).switchOn().doneLoading().enable(),b.item.both.removeClass("kmt-sticked"),b.unstickComment(b.item.commentid)},fail:function(){b.stickButton.text(a.language("COM_KOMENTO_ERROR"))}})},like:function(){Komento.ajax("site.views.komento.action",{id:b.item.id,type:"likes",action:"add"},{success:function(){b.item.element.both.likeButton.switchOff().enable().find("span").doneLoading().text(a.language("COM_KOMENTO_COMMENT_UNLIKE"));var c=parseInt(b.item.element.mine.likesCounter.find("span").text())+1;b.item.element.both.likesCounter.find("span").text(c)},fail:function(a){b.item.element.both.likesCounter.find("span").doneLoading().text(a)}})},unlike:function(){Komento.ajax("site.views.komento.action",{id:b.item.id,type:"likes",action:"remove"},{success:function(){b.item.element.both.likeButton.switchOn().enable().find("span").doneLoading().text(a.language("COM_KOMENTO_COMMENT_LIKE"));var c=parseInt(b.item.element.mine.likesCounter.find("span").text())-1;b.item.element.both.likesCounter.find("span").text(c)},fail:function(){b.item.element.both.likesCounter.find("span").doneLoading().text(a.language("COM_KOMENTO_ERROR"))}})},showLikesDialog:function(c){Komento.ajax("site.views.komento.getLikedUsers",{id:b.item.id},{success:function(b){a.dialog({title:a.language("COM_KOMENTO_COMMENT_PEOPLE_WHO_LIKED_THIS"),content:b})}})},loadParent:function(){var c=a("#"+b.item.parentid);if(c.length!=0){var d=c.find(".kmt-avatar:not(.parentContainer > .kmt-avatar)").clone(),e=c.find(".kmt-author:not(.parentContainer > .kmt-author)").clone(),f=c.find(".kmt-time:not(.parentContainer > .kmt-time)").clone(),g=c.find(".commentText:not(.parentContainer > .commentText)").clone();b.item.element.both.parentContainer.html("").append(d).append(e).append(f).append(g)}else{var h=b.item.parentid.split("-")[1];Komento.ajax("site.views.komento.getcomment",{id:h},{success:function(a){b.item.element.both.parentContainer.html(a)},fail:function(){}})}b.item.element.both.parentContainer.attr("loaded",1)},showAttachmentDeleteDialog:function(c){var d=a(c).parents(".attachmentFile").attr("attachmentid"),e=a(c).parents(".attachmentFile").attr("attachmentname");a.dialog({content:b.view.deleteAttachment(!0,{attachmentname:e}),afterShow:function(){a(".foundryDialog").find(".delete-attachment").click(function(){b.closeDialog(),b.deleteFile(d)}),a(".foundryDialog").find(".delete-attachment-cancel").click(function(){b.closeDialog()})}})},deleteFile:function(a){var c=b.item.id;Komento.ajax("site.views.komento.deleteAttachment",{id:c,attachmentid:a},{success:function(){b.item.element.both.attachmentFile.filter(".file-"+a).remove(),b.item.mine.find(".attachmentFile").length==0&&b.item.element.both.attachmentWrap.remove()},fail:function(a){alert("error deleting attachment at "+a)}})}}}),b.resolve()})});