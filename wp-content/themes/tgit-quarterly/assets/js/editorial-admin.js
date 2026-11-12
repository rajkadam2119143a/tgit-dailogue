/**
 * Media pickers for Quarterly Recognition metabox.
 */
(function ($) {
	'use strict';

	function bindPicker(btnSel, clearSel, inputSel, previewSel, isFile) {
		var $btn = $(btnSel);
		if (!$btn.length) {
			return;
		}
		var frame;

		$btn.on('click', function (e) {
			e.preventDefault();
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: isFile ? 'Select certificate file' : 'Select certificate image',
				button: { text: 'Use this file' },
				multiple: false,
			});
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				$(inputSel).val(att.id);
				if (inputSel === '#tgit_testimony_media_id') {
					$('#tgit_testimony_media_url').val(att.url || '');
				}
				if (isFile) {
					$('#tgit_cert_file_name').text(att.filename || att.url || '');
					$(clearSel).show();
				} else {
					var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
					if (inputSel === '#tgit_testimony_media_id' && att.url && /\.(mp4|webm|ogg|mov)$/i.test(att.url)) {
						$(previewSel).html($('<video>', { src: att.url, controls: true, css: { maxWidth: 320, height: 'auto' } }));
					} else {
						$(previewSel).html(url ? $('<img>', { src: url, css: { maxWidth: 240, height: 'auto' } }) : '');
					}
					$(clearSel).show();
				}
			});
			frame.open();
		});

		$(clearSel).on('click', function (e) {
			e.preventDefault();
			$(inputSel).val('0');
			if (inputSel === '#tgit_testimony_media_id') {
				$('#tgit_testimony_media_url').val('');
			}
			if (isFile) {
				$('#tgit_cert_file_name').text('');
			} else {
				$(previewSel).empty();
			}
			$(this).hide();
		});
	}

	$(function () {
		bindPicker('#tgit_featured_img_btn', '#tgit_featured_img_clear', '#tgit_featured_image_id', '#tgit_featured_img_preview', false);
		bindPicker('#tgit_cert_img_btn', '#tgit_cert_img_clear', '#tgit_certificate_image_id', '#tgit_cert_img_preview', false);
		bindPicker('#tgit_cert_file_btn', '#tgit_cert_file_clear', '#tgit_certificate_file_id', null, true);
		bindPicker('#tgit_testimony_media_btn', '#tgit_testimony_media_clear', '#tgit_testimony_media_id', '#tgit_testimony_media_preview', false);
	});
})(jQuery);
