Komento.module('komento.commenttools', function($) {
var module = this;

Komento.require()
	.script('komento.commentlist', 'komento.common')
	.done(function() {
		Komento.Controller(
			'CommentTools',
			{
				defaults: {
					'{commentCounter}': '.commentCounter',
					'{sortOldest}': '.sortOldest a',
					'{sortLatest}': '.sortLatest a',
					'{sortButton}': '.kmt-sorting a',
					'{adminButton}': '.adminMode a',
					view: {
						newComment: 'notifications/new.comment'
					}
				}
			},
			function(self)
			{ return {
				init: function() {
					if(Komento.options.acl.read_comment == 1 && Komento.options.konfig.enable_ajax_load_list == 1) {
						self.loadCommentList();
					}

					if(Komento.options.konfig.enable_live_notification == 1) {
						self.hasNew = 0;
						self.newCount = 0;
						self.checkNewComment();
					}
				},

				loadCommentList: function() {
					self.reloadComments('default');
				},

				'{sortOldest} click': function(el) {
					if(self.sortButton().checkClick())
					{
						self.sortButton().removeClass('selected');
						el.loading().addClass('selected');
						self.reloadComments('oldest');
					}
				},

				'{sortLatest} click': function(el) {
					if(self.sortButton().checkClick()) {
						self.sortButton().removeClass('selected');
						el.loading().addClass('selected');
						self.reloadComments('latest');
					}
				},

				'{sortLikes} click': function(el) {
					if(self.sortButton().checkClick()) {
						self.sortButton().removeClass('selected');
						el.loading().addClass('selected');
						self.reloadComments('likes');
					}
				},

				'{adminButton} click': function(el) {
					self.reloadComments( 'default', 'all' );
				},

				reloadComments: function(sort, published) {
					$('.commentList').html('<img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_COMMENTS_LOADING'));
					Komento.ajax('site.views.komento.reloadcomments', {
						component: Komento.component,
						cid: Komento.cid,
						contentLink: Komento.contentLink,
						options: {
							sort: sort,
							published: published
						}
					},
					{
						success: function(html, loadedCount, totalCount)
						{
							Komento.sort = sort;
							Komento.loadedCount = loadedCount;
							Komento.totalCount = totalCount;

							var newCommentList = $(html);

							$('.commentList').html(newCommentList).controller().init();

							self.sortButton()
								.enable()
								.doneLoading();

							$('.kmt-notification').remove();
						},

						fail: function()
						{
							$('.commentList').text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				checkNewComment: function() {
					var timer = Komento.options.konfig.live_notification_interval ? parseInt(Komento.options.konfig.live_notification_interval) * 1000 : 30000;
					setTimeout(function() {
						Komento.ajax('site.views.komento.checknewcomment', {
							component: Komento.component,
							cid: Komento.cid,
							total: Komento.totalCount
						},
						{
							success: function(hasNew, newCount) {
								if( hasNew == 1 && newCount > self.newCount ) {
									$('.kmt-notification').remove();

									var notification = self.view.newComment({count: newCount});

									notification.find('.reloadComments').bind('click', function() {
										self.kmt.tools.reloadComments(Komento.sort);
									});

									$('body').append(notification);
								}

								self.newCount = newCount;
								self.hasNew = hasNew;
							},

							fail: function() {

							},

							complete: function() {
								self.kmt.tools.checkNewComment();
							}
						})
					}, timer);
				},

				addCommentCounter: function() {
					var current = self.commentCounter().find('span').text();
					self.commentCounter().find('span').text(parseInt(current) + 1);
				},

				deductCommentCounter: function() {
					var current = self.commentCounter().text();
					self.commentCounter().text(parseInt(current) - 1);
				}
			} }
		);

		module.resolve();
	});
});
