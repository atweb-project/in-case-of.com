Komento.module('komento.upload', function($) {
	var module = this;

	Komento.require()
	.library('plupload')
	.view(
		'comment/form/uploadheader',
		'comment/form/uploadrow'
	)
	.done(function() {
		Komento.Controller(
			'UploadForm',
		{
			defaults: {
				uploadUrl: $.indexUrl + '?option=com_komento&controller=file&task=upload',
				uploadedId: [],
				'{uploader}': '.uploaderForm',
				'{uploadButton}': '.uploadButton',
				'{uploadArea}': '.uploadArea',
				'{uploadQueue}': '.uploadQueue',
				'{queueHeader}': '.queueHeader',
				'{queueRow}': '.queueRow',
				'{removeFile}': '.removeFile',
				'{fileCounter}': '.fileCounter',
				view: {
					uploadheader: 'comment/form/uploadheader',
					uploadrow: 'comment/form/uploadrow'
				}
			}
		},
		function(self) { return {
			init: function() {
				self.uploader().implement(
					'plupload',
					{
						settings: {
							url: self.options.uploadUrl,
							drop_element: 'uploadArea',
							max_file_size: Komento.options.config.upload_max_size + 'mb',
							filters: [
								{title: 'Allowed File Type', extensions: Komento.options.config.upload_allowed_extension}
							]
						},
						'{uploader}': '.uploaderForm',
						'{uploadButton}': '.uploadButton'
					},
					function() {
						self.plupload = this.plupload;

						if(self.plupload.runtime == 'html5') {
							self.enableDragDrop();
						}
					}
				);
			},

			"{uploader} FilesAdded": function(el, event, uploader, file) {
				self.addFiles(file);
			},

			'{uploader} UploadComplete': function(el, event, uploader, files) {
				$('.queueRow').each(function(index, item) {
					self.removeRow($(item).attr('id'));
				});
				self.kmt.form.postComment();
			},

			'{uploader} FileUploaded': function(el, event, uploader, file, response) {
				if( response.status == 1 ) {
					self.options.uploadedId.push(response.id);
				}
			},

			'{uploader} QueueChanged': function(el, event, uploader) {
				self.fileCounter().text(uploader.files.length);
			},

			'{uploader} Error': function(el, event, uploader, error) {
				self.kmt.form.clearNotification();
				switch(error.code) {
					case -600:
						self.kmt.form.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_MAX_FILE_SIZE', Komento.options.config.upload_max_size + 'mb'));
						break;
					case -601:
						self.kmt.form.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_FILE_EXTENSION', Komento.options.config.upload_allowed_extension));
				}
			},

			'{removeFile} click': function(el) {
				var id = el.parents('tr').attr('id');
				self.removeRow(id);
			},

			enableDragDrop: function() {
				self.uploadArea().append($.language('COM_KOMENTO_FORM_OR_DROP_FILES_HERE'));
			},

			startUpload: function() {
				if(self.plupload.files.length > 0) {
					self.plupload.start();
				} else {
					self.kmt.form.postComment();
				}
			},

			addFiles: function(file) {
				/* file = [{
					name,
					type,
					tmp_name,
					size
				}]*/

				$.each(file, function(index, item) {

					self.kmt.form.clearNotification();

					// check for file count before proceeding
					if(self.plupload.files.length > Komento.options.config.upload_max_file) {
						self.plupload.removeFile(item);
						self.kmt.form.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_MAX_FILE_ITEM', Komento.options.config.upload_max_file));
						return true;
					}

					// check for file status before proceeding
					if(item.status != 1) {
						return true;
					}

					var size = parseInt(item.size / 1024);

					var html = self.view.uploadrow({
						id: item.id,
						filename: item.name,
						size: size
					});

					html.data('fileitem', item);

					if(self.queueRow().length == 0) {
						self.addHeader();
					}

					self.uploadQueue().append(html);
				});
			},

			addHeader: function() {
				var html = self.view.uploadheader({
					maxcounter: Komento.options.config.upload_max_file
				});
				self.uploadQueue().append(html);
			},

			removeHeader: function() {
				self.uploadQueue().find('thead').remove();
				self.uploadQueue().find('tbody').remove();
			},

			removeRow: function(id) {
				$('#' + id).remove();

				if(self.queueRow().length == 0) {
					self.removeHeader();
				}

				var file = self.plupload.getFile(id);
				self.plupload.removeFile(file);
			}
		} });
		module.resolve();
	});
});
