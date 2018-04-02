//jQuery(document).ready(function() {
//	
//	var windowWidth = jQuery(window).width();
//	var ajax_file_url = jQuery('#ajax-file-url').attr('ajax-file-url');
//	var product_id = jQuery('#product_id').attr('product-id');
//	
//	if( windowWidth >= 768){
//		var modalWindowWidth = jQuery(".modal-dialog").width();
//		jQuery("#calendar").css("width", modalWindowWidth - 1 );
//	}else{
//		jQuery("#calendar").css("width", windowWidth - 21 );
//	}
//
//	jQuery("#datepickerModal button.close").click(function(){
//		var selected_delivery_date = jQuery('#mydate').val();
//		jQuery.post( ajax_file_url, {action:"add_delivery_date_session_var", delivery_date : selected_delivery_date, productId : product_id },function(r){
//			console.log(r);
//	   });
//	});
//	
//	jQuery("#datepickerModal").click(function(){
//		var selected_delivery_date = jQuery('#mydate').val();
//		jQuery.post( ajax_file_url, {action:"add_delivery_date_session_var", delivery_date : selected_delivery_date, productId : product_id },function(r){
//			console.log(r);
//	   });
//	});
//	
//});