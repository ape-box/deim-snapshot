/*
 * jQuery File Upload Plugin JS Example 6.0.3
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */

$(function () {
	'use strict';

	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload();

	// Load existing files:
	$.getJSON($('#fileupload').prop('action'), function (files) {
		var fu = $('#fileupload').data('fileupload'),
			template;
		fu._adjustMaxNumberOfFiles(-files.length);
		template = fu._renderDownload(files)
			.appendTo($('#fileupload .files'));
		// Force reflow:
		fu._reflow = fu._transition && template.length &&
			template[0].offsetWidth;
		template.addClass('in');
	});
	$('#fileupload').fileupload('option', {
		maxFileSize: 5000000,
		acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
	});

	// Enable iframe cross-domain access via redirect page:
	var redirectPage = window.location.href.replace(
		/\/[^\/]*$/,
		'/cors/result.html?%s'
	);
	$('#fileupload').bind('fileuploadsend', function (e, data) {
		if (data.dataType.substr(0, 6) === 'iframe') {
			var target = $('<a/>').prop('href', data.url)[0];
			if (window.location.host !== target.host) {
				data.formData.push({
					name: 'redirect',
					value: redirectPage
				});
			}
		}
	});

});

function show_modal(src) {
	jQuery('#myModal img#preview').attr('src', src);
	jQuery('#myModal').modal('show');
}
