jQuery(document).ready(function ($) {
    "use strict";

    /* Write your custom JS below */
	jQuery(".my-rating").starRating({
	  initialRating: 4,
	  strokeColor: '#894A00',
	  strokeWidth: 10,
	  starSize: 25
	});
	
	

});

document.getElementById("featured-image").onchange = function () {
	var featuredImage	= jQuery('#featured-image').val();
	var fIExt = featuredImage.split('.').pop();
	if (fIExt == "jpg" || fIExt == "JPG" || fIExt == "jpeg" || fIExt == "JPEG" || fIExt == "gif" || fIExt == "GIF" || fIExt == "png" || fIExt == "PNG" ) {
		document.getElementById("uploadImageFile").value = this.files.item(0).name;
	}else{
		alert('Please upload image file only.');
		document.getElementById("uploadImageFile").value = '';
		return false;
	}
};
document.getElementById("featured-video").onchange = function () {
	var featuredVideo	= jQuery('#featured-video').val();
	var fIExtVD = featuredVideo.split('.').pop();
	if (fIExtVD == "mp4" || fIExtVD == "MP4" || fIExtVD == "avi" || fIExtVD == "AVI" || fIExtVD == "FLV" || fIExtVD == "flv" || fIExtVD == "WMV" || fIExtVD == "MOV" || fIExtVD == "mov" || fIExtVD == "jpg" || fIExtVD == "JPG" || fIExtVD == "jpeg" || fIExtVD == "JPEG" || fIExtVD == "gif" || fIExtVD == "GIF" || fIExtVD == "png" || fIExtVD == "PNG" ) {
		document.getElementById("uploadVideoFile").value = this.files.item(0).name;
	}else{
		
		alert('Please upload image or video file only.');
		document.getElementById("uploadVideoFile").value = '';
		return false;
		
	}
};

jQuery("#blog-submit").click(function(){
	var blogName 		= jQuery("#blog-form input[name=blog-name]").val();
	var blogDetails 	= jQuery("#blog-form textarea[name=blog-details]").val();
	var businessUser 	= jQuery("#blog-form textarea[name=business-user]").val();
	var businessValue 	= jQuery("#blog-form input[name=business-value]").val();
	var uploadField 	= document.getElementById("featured-video");
	
	if( blogName.length <= 0 && blogName == '' ){
		
		alert('Please enter blog name.');
		return false;
		
	}else if( blogDetails.length <= 0 && blogDetails == '' ){
		
		alert('Please enter blog detail.');
		return false;
		
	}else if( businessValue.length <= 0 && businessValue == '' ){
		
		alert('Please enter business value.');
		
		return false;
		
	}else if(uploadField.files[0].size > 20000000){
       alert("The uploaded video exceeds 20MB");
 		return false;
	}else{

		return true;
	}	 
});

var maxLength_bd = 800;
jQuery('#blog-details').keyup(function() {
  var textlen = maxLength_bd - jQuery(this).val().length;
  jQuery('#rchars_bd').text(textlen+' '+'Character(s) Remaining');
});
var maxLength_ut = 600;
jQuery('#business-user').keyup(function() {
  var textlen = maxLength_ut - jQuery(this).val().length;
  jQuery('#rchars_ut').text(textlen+' '+'Character(s) Remaining');
});

if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}