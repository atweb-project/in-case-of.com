Komento.module('komento.bbcode', function($) {
	var module = this;

	$.getBBcodeSettings = function() {
		var settings = {
			previewParserVar: 'data',
			markupSet: []
		};

		if(Komento.options.config.bbcode_bold == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_BOLD'), key:'B', openWith:'[b]', closeWith:'[/b]', className:'kmt-markitup-bold'});
		}

		if(Komento.options.config.bbcode_italic == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_ITALIC'), key:'I', openWith:'[i]', closeWith:'[/i]', className:'kmt-markitup-italic'});
		}

		if(Komento.options.config.bbcode_underline == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_UNDERLINE'), key:'U', openWith:'[u]', closeWith:'[/u]', className:'kmt-markitup-underline'});
		}

		if(Komento.options.config.bbcode_bold == 1 || Komento.options.config.bbcode_italic == 1 || Komento.options.config.bbcode_underline == 1) {
			settings.markupSet.push({separator:'---------------' });
		}

		if(Komento.options.config.bbcode_link == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_LINK'), key:'L', openWith:'[url="[![Link:!:http://]!]"(!( title="[![Title]!]")!)]', closeWith:'[/url]', placeHolder:'Your text to link...', className:'kmt-markitup-link'});
		}

		if(Komento.options.config.bbcode_picture == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_PICTURE'), key:'P', replaceWith:'[img][![Url]!][/img]', className:'kmt-markitup-picture'});
		}

		if(Komento.options.config.bbcode_link == 1 || Komento.options.config.bbcode_picture == 1) {
			settings.markupSet.push({separator:'---------------' });
		}

		if(Komento.options.config.bbcode_bulletlist == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_BULLETLIST'), openWith:'[list]\n', closeWith:'\n[/list]', className:'kmt-markitup-bullet'});
		}

		if(Komento.options.config.bbcode_numericlist == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_NUMERICLIST'), openWith:'[list=[![Starting number]!]]\n', closeWith:'\n[/list]', className:'kmt-markitup-numeric'});
		}

		if(Komento.options.config.bbcode_bullet == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_BULLET'), openWith:'[*]', closeWith:'[/*]', className:'kmt-markitup-list'});
		}

		if(Komento.options.config.bbcode_bulletlist == 1 || Komento.options.config.bbcode_numericlist == 1 || Komento.options.config.bbcode_bullet == 1) {
			settings.markupSet.push({separator:'---------------' });
		}

		if(Komento.options.config.bbcode_quote == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_QUOTE'), openWith:'[quote]', closeWith:'[/quote]', className:'kmt-markitup-quote'});
		}

		if(Komento.options.config.bbcode_clean == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_CLEAN'), className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") }, className:'kmt-markitup-clean'});
		}

		if(Komento.options.config.bbcode_quote == 1 || Komento.options.config.bbcode_clean == 1) {
			settings.markupSet.push({separator:'---------------' });
		}

		if(Komento.options.config.bbcode_smile == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_SMILE'), openWith:':)', className:'kmt-markitup-smile'});
		}

		if(Komento.options.config.bbcode_happy == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_HAPPY'), openWith:':D', className:'kmt-markitup-happy'});
		}

		if(Komento.options.config.bbcode_surprised == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_SURPRISED'), openWith:':o', className:'kmt-markitup-surprised'});
		}

		if(Komento.options.config.bbcode_tongue == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_TONGUE'), openWith:':p', className:'kmt-markitup-tongue'});
		}

		if(Komento.options.config.bbcode_unhappy == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_UNHAPPY'), openWith:':(', className:'kmt-markitup-unhappy'});
		}

		if(Komento.options.config.bbcode_wink == 1) {
			settings.markupSet.push({name: $.language('COM_KOMENTO_BBCODE_WINK'), openWith:';)', className:'kmt-markitup-wink'});
		}

		return settings;
	};

	module.resolve();
});
