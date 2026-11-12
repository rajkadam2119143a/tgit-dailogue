jQuery(document).ready(function ($) {
    "use strict"; 

    /* Write your custom JS below */
	jQuery('.modal').on('shown.bs.modal', function (e) {
		 var button = jQuery(e.relatedTarget); 
		 var url = button.data("url");
		 //console.log(url);
		 jQuery(this).find('.modal-content img').attr("src",url);
		 jQuery(this).find('.facebook').attr("href","https://www.facebook.com/sharer/sharer.php?u="+url);
		 jQuery(this).find('.twitter').attr("href","https://twitter.com/intent/tweet?url="+url);
		 jQuery(this).find('.linkedin').attr("href","https://www.linkedin.com/shareArticle?mini=true&url="+url);
		 jQuery(this).find('.whatsapp').attr("href","https://wa.me/?text="+url);
		$('#myModal').find('.download').attr("href", url);
	});


});