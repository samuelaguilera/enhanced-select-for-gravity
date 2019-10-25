	jQuery(document).bind('gform_post_render', function(){
		jQuery(document).ready(function ($) {
			$('.gform_wrapper .gform_body .gform_fields .gfield_selectize select').selectize();
		});
	});