Komento.module('admin.acl.recommended', function($) {
	var module = this;

	Komento.require()
	.script('admin.language')
	.done(function() {
		Komento.Controller(
			'AclRecommended',
		{
			defaults: {
				type: 'usergroup',
				usergroup: 0
			},
		},
		function(self) { return {
			init: function() {
				self.rule = {
					read_comment: 1,
					read_stickies: 1,
					read_lovies: 1,
					add_comment: 0,
					edit_own_comment: 0,
					delete_own_comment: 0,
					author_edit_comment: 0,
					author_delete_comment: 0,
					author_publish_comment: 0,
					author_unpublish_comment: 0,
					edit_all_comment: 0,
					delete_all_comment: 0,
					publish_all_comment: 0,
					unpublish_all_comment: 0,
					like_comment: 0,
					report_comment: 0,
					share_comment: 1,
					reply_comment: 0,
					stick_comment: 0
				};

				var usergroup = [18, 19, 20, 21, 23, 24, 25, 29];

				if(self.options.type == 'usergroup' && ((Komento.options.jversion == '1.5' && $.inArray(self.options.usergroup, usergroup)) || (Komento.options.jversion != '1.5' && self.options.usergroup > 0 && self.options.usergroup < 9)))
				{
					switch(self.options.usergroup) {
						case 1:
						case 29:
							self.renderPublic();
							break;
						case 2:
						case 18:
							self.renderRegistered();
							break;
						case 3:
						case 19:
							self.renderAuthor();
							break;
						case 4:
						case 20:
							self.renderEditor();
							break;
						case 5:
						case 21:
							self.renderPublisher();
							break;
						case 6:
						case 23:
							self.renderManager();
							break;
						case 7:
						case 24:
							self.renderAdministrator();
							break;
						case 8:
						case 25:
							self.renderSuperAdministrator();
							break;
					}
				}
			},

			render: function(rule) {
				$.each(rule, function( i, v ) {
					var result = $.language('COM_KOMENTO_NO_OPTION');
					if( v ) {
						result = $.language('COM_KOMENTO_YES_OPTION');
					}

					var html = '<label class="recommended">' + $.language('COM_KOMENTO_ACL_RECOMMENDED') + ': ' + result + '</label>';
					$('.' + i).append(html);
				});
			},

			renderPublic: function() {
				var rule = self.rule;
				self.render(rule);
			},

			renderRegistered: function() {
				var rule = self.rule;

			},

			renderAuthor: function() {
				var rule = self.rule;

			},

			renderEditor: function() {
				var rule = self.rule;

			},

			renderPublisher: function() {
				var rule = self.rule;

			},

			renderManager: function() {
				var rule = self.rule;

			},

			renderAdministrator: function() {
				var rule = self.rule;

			},

			renderSuperAdministrator: function() {
				var rule = self.rule;

			}
		} });
		module.resolve();
	});
});
