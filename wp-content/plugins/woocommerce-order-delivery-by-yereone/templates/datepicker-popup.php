<?php global $post; ?>
<h4 style=" font-size: 20px; margin-bottom: auto; margin-top:25px" class="meta-title"><span class="steps">2</span><span><?php _e('Delivery Date', ETHEME_DOMAIN); ?></span></h4>
<div class="picker-wrapper">

<p>Delivery Date:<input name="order-selected-date" id="mydate" class="datepicker_modal_open mydatepckmd" placeholder="DD/MM/YYYY" gldp-id="mydate" data-toggle="modal" data-target="#datepickerModal" /></p>
</div>
<!-- Modal -->
<div class="modal fade" id="datepickerModal" tabindex="-1" role="dialog" aria-labelledby="datepickerModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<div id="ajax-file-url" ajax-file-url="<?php echo site_url();?>/wp-admin/admin-ajax.php"></div>
				<div id="product_id" product-id="<?php echo $post->ID ?>"></div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
				<h4 class="modal-title" id="datepickerModalLabel">Pick up Date</h4>
			</div>
			<div class="modal-body">
				<div id="calendar" gldp-el="mydate" style="height:250px; position:absolute;"></div>
                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" style="height:300px !important; overflow:auto">
                <p><?php echo get_option('delivery_text'); ?></p>
							
						</div>
			</div>
			<div class="modal-footer">
            
            <div class="delivery_price_text" style="padding-bottom:15px; width:100%; float:left; font-size:16px;">
             <div class="deliverytext_left"><strong>Standard: Flat rate delivery per city</strong></div>
  <!--<div class="deliverytext_right"><strong>Premium: Standard delivery <span style="font-family:verdana;">+</span> €<?php echo esc_attr( get_option('sunday_delivery_price') ); ?></strong></div>-->
   </div>
  <div style="clear:both"></div>
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingOne">
							<h4 class="panel-title text-left">
								<a role="button" id="btn-showhide" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
									Delivery Prices
								</a>
							</h4>
						</div>
						<!--jj-->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function() {

	jQuery("body").click(function(){
		jQuery("body #calendar").children(".special").children("span").html("Special");
		jQuery("body #calendar").children(".sun").children("span").html("Standard");
		jQuery("body #calendar").children(".sat").children("span").html("Standard");
	});

	var currentYear = new Date().getFullYear();
	currentYear += 3;
	

	
    jQuery("#mydate").glDatePicker({
    	showAlways: true,
    	hideOnClick: true,
		selectableDateRange: [
		        { from: new Date(), to: new Date (currentYear, 0, 1) },
		    ],
    	specialDates: [
    		<?php echo $this->get_specific_delivery_dates_for_script(); ?>
    	],
    	onClick: function(target, cell, date, data) {
    		var year  = date.getFullYear();
        	var month = date.getMonth()+1;
        	month     = month.toString().length < 2 ? "0"+month : month;
        	var day   = date.getDate();
        	day       = day.toString().length < 2 ? "0"+day : day;
    		target.val( day + "/" + month + "/" + year );
    	},
		
    });
	
});
(function($){
		
		$("#btn-showhide").click(function(){
			$("#collapseOne").removeAttr('style');
			
		$('#calendar').toggle();
		$('.delivery_price_text').toggle();
		$('.modal-footer').toggleClass('footerhastext');
		if($('.modal-footer').hasClass('footerhastext')){
			$('#btn-showhide').text('Back to Calendar');
			}
			else{
				$('#btn-showhide').text('Delivery Prices');
				}
	
})
	})(jQuery);

</script>
