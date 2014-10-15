Komento.module('komento.common', function($) {
var module = this;

Komento.require().library('effects/highlight', 'scrollTo').script('komento.language').done(function() {
	var originalHide = $.fn.hide;
	var originalShow = $.fn.show;
	var originalScrollTo = function( element ) {
		$.scrollTo(element, 500);
	};

	$.fn.hide = function() {
		originalHide.apply(this, arguments);
		this.addClass('hidden');
		return this;
	}

	$.fn.show = function() {
		originalShow.apply(this, arguments);
		this.removeClass('hidden');
		return this;
	}

	$.fn.scroll = function() {
		originalScrollTo(this);

		/*return this.each(function ()
		{
			$('html, body').animate({scrollTop: $(this).offset().top}, 'fast');
		});*/
	};

	$.fn.highlight = function() {
		this.effect("highlight", {color: '#FDFFE0'}, 2000);
		return this;
	};

	$.fn.enable = function() {
		this.removeClass('disabled');
		return this;
	};

	$.fn.disable = function() {
		this.addClass('disabled');
		return this;
	};

	$.fn.switchOn = function() {
		this.removeClass('cancel');
		return this;
	};

	$.fn.switchOff = function() {
		this.addClass('cancel');
		return this;
	};

	$.fn.checkSwitch = function() {
		if(this.hasClass('cancel')) {
			return false;
		} else {
			return true;
		}
	};

	$.fn.checkClick = function() {
		if(this.hasClass('disabled')) {
			return false;
		} else {
			this.addClass('disabled');
			return true;
		}
	};

	$.fn.clearClick = function() {
		this.removeClass('disabled');
		return this;
	};

	$.fn.loading = function() {
		this.addClass('loading');
		return this;
	};

	$.fn.doneLoading = function() {
		this.removeClass('loading');
		return this;
	};

	$.fn.exists = function() {
		return this.length > 0 ? true : false;
	};

	$.fn.acl = function(component, rule, callback) {
		Komento.ajax('site.views.komento.checkAcl', {
			component: component,
			rule: rule
		},
		{
			success: function() {
				return callback && callback();
			}
		});
	};

	$.fn.permission = function(id, action, callback) {
		Komento.ajax('site.views.komento.checkPermission', {
			id: id,
			action: action
		},
		{
			success: function() {
				return callback && callback();
			}
		});
	};

	$.shortenlink = function(url, callback) {
		Komento.ajax('site.views.komento.shortenLink', {
			url: url
		},
		{
			success: function(link) {
				Komento.shortenLink = link;
				return callback && callback(link);
			}
		});
	};

	module.resolve();
});
});
