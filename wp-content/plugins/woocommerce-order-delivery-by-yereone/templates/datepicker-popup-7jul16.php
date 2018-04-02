<?php global $post; ?>

<b>Select a Delivery Date<font color="red" size="4" >*</font></b><input name="order-selected-date" id="mydate" class="datepicker_modal_open" placeholder="DD/MM/YYYY" gldp-id="mydate" data-toggle="modal" data-target="#datepickerModal" />

<!-- Modal -->
<div class="modal fade" id="datepickerModal" tabindex="-1" role="dialog" aria-labelledby="datepickerModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<div id="ajax-file-url" ajax-file-url="<?php echo site_url();?>/wp-admin/admin-ajax.php"></div>
				<div id="product_id" product-id="<?php echo $post->ID ?>"></div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
				<h4 class="modal-title" id="datepickerModalLabel">Shipping Options</h4>
			</div>
			<div class="modal-body">
				<div id="calendar" gldp-el="mydate" style="height:300px; position:absolute;"></div>
			</div>
			<div class="modal-footer">
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingOne">
							<h4 class="panel-title text-left">
								<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
									Delivery Prices
								</a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
							<div class="panel-body">
								<table class="table">
								  <thead class="thead-default">
									<tr>
									  <th>Special Date</th>
									  <th>What Is Day</th>
									  <th>Delivery Price</th>
									</tr>
								  </thead>
								  <tbody>
									<?php echo $this->get_specific_delivery_dates_for_table(); ?>
								  </tbody>
								</table>
								<table class="table">
								  <thead class="thead-default">
									<tr>
									  <th>Week Day</th>
									  <th>Delivery Price</th>
									</tr>
								  </thead>
								  <tbody>
									<tr valign="top" class="table-success">
                                        <th scope="row">Sunday Delivery</th>
                                        <td><?php echo esc_attr( get_option('sunday_delivery_price') ); ?></td>
                                    </tr>
                                     
                                    <tr valign="top">
                                        <th scope="row">Monday Delivery</th>
                                        <td><?php echo esc_attr( get_option('monday_delivery_price') ); ?></td>
                                    </tr>
                                    
                                    <tr valign="top" class="table-success">
                                        <th scope="row">Tuesday Delivery</th>
                                        <td><?php echo esc_attr( get_option('tuesday_delivery_price') ); ?></td>
                                    </tr>
                                    
                                    <tr valign="top">
                                        <th scope="row">Wednesday Delivery</th>
                                        <td><?php echo esc_attr( get_option('wednesday_delivery_price') ); ?></td>
                                    </tr>
                                    
                                    <tr valign="top" class="table-success">
                                        <th scope="row">Thursday Delivery</th>
                                        <td><?php echo esc_attr( get_option('thursday_delivery_price') ); ?></td>
                                    </tr>
                                    
                                    <tr valign="top">
                                        <th scope="row">Friday Delivery</th>
                                        <td><?php echo esc_attr( get_option('friday_delivery_price') ); ?></td>
                                    </tr>
                                   
                                    <tr valign="top" class="table-success">
                                        <th scope="row">Saturday Delivery</th>
                                        <td><?php echo esc_attr( get_option('saturday_delivery_price') ); ?></td>
                                    </tr>
                                    
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function() {

	jQuery("body").click(function(){
		jQuery("body #calendar").children(".special").children("span").html("Premium");
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
    		target.val( year + "/" + month + "/" + day );
    	}
    });

});

</script>
