Komento.module('admin.acl.profiles', function($) {
	var module = this;

	Komento.require()
	.script('admin.language', 'komento.common')
	.done(function() {
		Komento.Controller(
			'AclProfiles',
		{
			defaults: {
				type: 'usergroup',
				usergroup: 0,
				showTable: 0,
				'{aclProfile}': '.aclProfiles',
				'{aclTable}': '.aclTable',
				'{showTableButton}': '.showTableButton'
			},
		},
		function(self) { return {
			init: function() {
				self.rule = {
					restricted: {
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
						stick_comment: 0,
						upload_attachment: 0,
						download_attachment: 1,
						delete_attachment: 0
					},
					basic: {
						read_comment: 1,
						read_stickies: 1,
						read_lovies: 1,
						add_comment: 1,
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
						like_comment: 1,
						report_comment: 0,
						share_comment: 1,
						reply_comment: 1,
						stick_comment: 0,
						upload_attachment: 1,
						download_attachment: 1,
						delete_attachment: 0
					},
					author: {
						read_comment: 1,
						read_stickies: 1,
						read_lovies: 1,
						add_comment: 1,
						edit_own_comment: 0,
						delete_own_comment: 0,
						author_edit_comment: 1,
						author_delete_comment: 0,
						author_publish_comment: 1,
						author_unpublish_comment: 1,
						edit_all_comment: 0,
						delete_all_comment: 0,
						publish_all_comment: 0,
						unpublish_all_comment: 0,
						like_comment: 1,
						report_comment: 0,
						share_comment: 1,
						reply_comment: 1,
						stick_comment: 0,
						upload_attachment: 1,
						download_attachment: 1,
						delete_attachment: 0
					},
					admin: {
						read_comment: 1,
						read_stickies: 1,
						read_lovies: 1,
						add_comment: 1,
						edit_own_comment: 1,
						delete_own_comment: 1,
						author_edit_comment: 0,
						author_delete_comment: 0,
						author_publish_comment: 0,
						author_unpublish_comment: 0,
						edit_all_comment: 1,
						delete_all_comment: 0,
						publish_all_comment: 1,
						unpublish_all_comment: 1,
						like_comment: 1,
						report_comment: 1,
						share_comment: 1,
						reply_comment: 1,
						stick_comment: 1,
						upload_attachment: 1,
						download_attachment: 1,
						delete_attachment: 1
					},
					full: {
						read_comment: 1,
						read_stickies: 1,
						read_lovies: 1,
						add_comment: 1,
						edit_own_comment: 1,
						delete_own_comment: 1,
						author_edit_comment: 1,
						author_delete_comment: 1,
						author_publish_comment: 1,
						author_unpublish_comment: 1,
						edit_all_comment: 1,
						delete_all_comment: 1,
						publish_all_comment: 1,
						unpublish_all_comment: 1,
						like_comment: 1,
						report_comment: 1,
						share_comment: 1,
						reply_comment: 1,
						stick_comment: 1,
						upload_attachment: 1,
						download_attachment: 1,
						delete_attachment: 1
					},
					none: {
						read_comment: 0,
						read_stickies: 0,
						read_lovies: 0,
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
						share_comment: 0,
						reply_comment: 0,
						stick_comment: 0,
						upload_attachment: 0,
						download_attachment: 0,
						delete_attachment: 0
					}
				}

				var detectedProfile = 'custom';
				$.each(self.rule, function(profile, rules) {
					var hit = 1;

					$.each(rules, function(name, value) {
						if($('.' + name + ' input').val() != value) {
							hit = 0;
							return false;
						}
					});

					if(hit == 1) {
						detectedProfile = profile;
						return false;
					}
				});

				self.aclProfile().val(detectedProfile);

				if( detectedProfile == 'custom' ) {
					self.renderCustom();
				}
			},

			'{aclProfile} change': function() {
				var profile = self.aclProfile().val();

				if( profile == 'custom' ) {
					self.renderCustom();
				} else {
					self.renderOthers(profile);
				}

				/*profile = profile.charAt(0).toUpperCase() + profile.slice(1);

				self['render' + profile]();*/
			},

			'{showTableButton} click': function() {
				self.options.showTable = 1;
				self.aclTable().show();
				self.showTableButton().parent().parent().remove();
			},

			renderCustom: function() {
				self.aclTable().show();

				if(!self.options.showTable) {
					self.showTableButton().hide();
				}
			},

			renderOthers: function(profile) {
				if(!self.options.showTable) {
					self.aclTable().hide();
					self.showTableButton().show();
				}

				self.render(self.rule[profile]);
			},

			renderFull: function() {
				self.aclProfile().val('full');
				self.renderOthers('full');
			},

			renderNone: function() {
				self.aclProfile().val('none');
				self.renderOthers('none');
			},

			render: function(rule) {
				$.each(rule, function(name, value) {
					var element;
					if( value ) {
						element = $('.' + name + ' .option-enable');
					} else {
						element = $('.' + name + ' .option-disable');
					}

					element.trigger('click');

					$('.' + name + ' input').val(value);
				});
			}
		} });
		module.resolve();
	});
});
