/**
 * Front-end project submission form (My Project Blog page).
 */
(function () {
	'use strict';

	var form = document.getElementById('blog-form');
	if (!form) {
		return;
	}

	var maxDetails = 800;
	var maxTestimony = 600;
	var imageInput = document.getElementById('featured-image');
	var videoInput = document.getElementById('featured-video');
	var imageName = document.getElementById('uploadImageFile');
	var videoName = document.getElementById('uploadVideoFile');
	var detailsField = document.getElementById('blog-details');
	var testimonyField = document.getElementById('business-user');
	var detailsCounter = document.getElementById('rchars_bd');
	var testimonyCounter = document.getElementById('rchars_ut');

	function updateCounter(field, max, counter) {
		if (!field || !counter) {
			return;
		}
		var remaining = max - field.value.length;
		counter.textContent = remaining + ' character(s) remaining';
	}

	if (detailsField && detailsCounter) {
		detailsField.addEventListener('input', function () {
			updateCounter(detailsField, maxDetails, detailsCounter);
		});
		updateCounter(detailsField, maxDetails, detailsCounter);
	}

	if (testimonyField && testimonyCounter) {
		testimonyField.addEventListener('input', function () {
			updateCounter(testimonyField, maxTestimony, testimonyCounter);
		});
		updateCounter(testimonyField, maxTestimony, testimonyCounter);
	}

	function isImageFile(file) {
		return /^image\/(jpeg|jpg|png|gif)$/i.test(file.type);
	}

	function isMediaFile(file) {
		return /^image\//i.test(file.type) || /^video\//i.test(file.type);
	}

	if (imageInput && imageName) {
		imageInput.addEventListener('change', function () {
			var file = imageInput.files && imageInput.files[0];
			if (!file) {
				imageName.value = '';
				return;
			}
			if (!isImageFile(file)) {
				window.alert('Please upload an image file only.');
				imageInput.value = '';
				imageName.value = '';
				return;
			}
			imageName.value = file.name;
		});
	}

	if (videoInput && videoName) {
		videoInput.addEventListener('change', function () {
			var file = videoInput.files && videoInput.files[0];
			if (!file) {
				videoName.value = '';
				return;
			}
			if (!isMediaFile(file)) {
				window.alert('Please upload an image or video file only.');
				videoInput.value = '';
				videoName.value = '';
				return;
			}
			if (file.size > 20000000) {
				window.alert('The uploaded video exceeds 20MB.');
				videoInput.value = '';
				videoName.value = '';
				return;
			}
			videoName.value = file.name;
		});
	}

	form.addEventListener('submit', function (event) {
		var blogName = document.getElementById('blog-name');
		var blogDetails = document.getElementById('blog-details');
		var businessValue = document.getElementById('business-value');

		if (!blogName || !blogName.value.trim()) {
			event.preventDefault();
			window.alert('Please enter project name.');
			return;
		}
		if (!blogDetails || !blogDetails.value.trim()) {
			event.preventDefault();
			window.alert('Please enter project details.');
			return;
		}
		if (!businessValue || !businessValue.value.trim()) {
			event.preventDefault();
			window.alert('Please enter business value.');
			return;
		}
		if (videoInput && videoInput.files && videoInput.files[0] && videoInput.files[0].size > 20000000) {
			event.preventDefault();
			window.alert('The uploaded video exceeds 20MB.');
		}
	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
})();
