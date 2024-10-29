jQuery( document ).ready(function() {

    jQuery('#hrd_post_meta_tag_keywords').amsifySuggestags({
		type : 'amsify',
		defaultTagClass: 'AMTK_post_meta_tag_keywords'
	});


	var el = jQuery("#hrd_checkbox");

	jQuery(document).on("click",el,function() {
	    hrd_checkbox_status(el)
	});

	function hrd_checkbox_status(el) {
    	if(el.is(":checked")){
			jQuery("#hrd_post_meta_tag_keywords").siblings(".amsify-suggestags-area").find("input.amsify-suggestags-input").attr("readonly", true);
		}else{
			jQuery("#hrd_post_meta_tag_keywords").siblings(".amsify-suggestags-area").find("input.amsify-suggestags-input").attr("readonly", false);
		}

	
	}

	hrd_checkbox_status(el);


}); // document ready block