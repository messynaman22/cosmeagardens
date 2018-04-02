jQuery(document).ready(function() {
	
	var ajax_file_url = jQuery('#ajax-file-url').attr('ajax-file-url');
	
    jQuery('#save_specific_delivery_date_and_price').click(function(){
        var specific_delivery_title = jQuery('#specific_delivery_title').val();
        var specific_delivery_date  = jQuery('#specific_delivery_date').val();
        var specific_delivery_price = jQuery('#specific_delivery_price').val();
    	jQuery.post( ajax_file_url, {action:"save_specific_delivery", delivery_title : specific_delivery_title, specific_date : specific_delivery_date, specific_price : specific_delivery_price},function(r){
    	  jQuery('#specific_delivery_dates_list').append(r);
       });
    });

    jQuery('#specific_delivery_dates_list').on('click','.remove_specific_delivery_date_and_price', function(){
        var remove_button = jQuery(this);
    	var specific_delivery_id = jQuery(this).attr('delivery_date_data_id');
    	jQuery.post( ajax_file_url, {action: "remove_specific_delivery", delivery_id : specific_delivery_id},function(r){
			var parentTr = jQuery(remove_button).parent('td').parent('tr');
			jQuery( parentTr ).fadeOut( "slow", function() {
				jQuery( parentTr ).remove();
			});
        });
    });
    
    jQuery('.MyDate').datepicker({
        dateFormat : 'yy-mm-dd'
    });
});