<div class="wrap">
	<form method="post" action="options.php"> 
		<?php settings_fields( 'yc-order-delivery-date-settings-group' ); ?>
		<?php do_settings_sections( 'yc-order-delivery-date-settings-group' ); ?>
		<table id="week-day-list" class="form-table">
		
			<tr valign="top">
                <th scope="row" colspan="2"><h3>Week Days Delivery Prices</h3></th>
            </tr>
            
            <tr valign="top">
                <th scope="row"></th>
                <td>Delivery Price</td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Sunday Delivery Price</th>
                <td><input type="number" name="sunday_delivery_price" value="<?php echo esc_attr( get_option('sunday_delivery_price') ); ?>" /></td>
            </tr>
             
            <tr valign="top">
                <th scope="row">Monday Delivery Price</th>
                <td><input type="number" name="monday_delivery_price" value="<?php echo esc_attr( get_option('monday_delivery_price') ); ?>" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Tuesday Delivery Price</th>
                <td><input type="number" name="tuesday_delivery_price" value="<?php echo esc_attr( get_option('tuesday_delivery_price') ); ?>" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Wednesday Delivery Price</th>
                <td><input type="number" name="wednesday_delivery_price" value="<?php echo esc_attr( get_option('wednesday_delivery_price') ); ?>" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Thursday Delivery Price</th>
                <td><input type="number" name="thursday_delivery_price" value="<?php echo esc_attr( get_option('thursday_delivery_price') ); ?>" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Friday Delivery Price</th>
                <td><input type="number" name="friday_delivery_price" value="<?php echo esc_attr( get_option('friday_delivery_price') ); ?>" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Saturday Delivery Price</th>
                <td><input type="number" name="saturday_delivery_price" value="<?php echo esc_attr( get_option('saturday_delivery_price') ); ?>" /></td>
            </tr>

			<tr valign="top">
                <th scope="row"><?php submit_button(); ?></th>
            </tr>
			
        </table>
	</form>
	
	<div id="ajax-file-url" style="height: 20px;" ajax-file-url="<?php echo site_url();?>/wp-admin/admin-ajax.php"></div>
	
	<table id="specific_delivery_dates_list" class="form-table">
		
		<tr valign="top">
			<th scope="row" colspan="2"><h3>Specific Delivery Dates List</h3></th>
		</tr>
		
		<tr valign="top">
			<th scope="row"></th>
			<td>Specific Name</td>
			<td>Delivery Date</td>
			<td>Delivery Price</td>
			<th></th>
		</tr>
		
		 <tr valign="top">
			<th scope="row">New Delivery Date</th>
			<td><input id="specific_delivery_title"  type="text"/></td>
			<td><input id="specific_delivery_date" type="text" value="" class="MyDate"/></td>
			<td><input id="specific_delivery_price" type="number" value="10"/></td>
			<td><input id="save_specific_delivery_date_and_price" type="button" value="Save Date" class="button button-primary"/></td>
		</tr>
		
		<?php echo $this->get_specific_delivery_dates_list(); ?>

	</table>
    
    <form method="post" action="options.php">
    <?php settings_fields( 'yc-order-delivery-text' ); ?>
		<?php do_settings_sections( 'yc-order-delivery-text' ); ?> 
    <table id="delivery_text" class="form-table" style=" background: white none repeat scroll 0 0;
    border: 2px dotted;
    border-radius: 20px;
    display: block;
    width:100%;
    max-width: 1020px;
    padding: 15px;">
    <tr><td><h3>Enter the delivery Text</h3></td></tr>
    <tr>
    <td><textarea name="delivery_text" style="width:600px; height:170px"><?php echo esc_attr( get_option('delivery_text') ); ?></textarea></td>
    </tr>
    
    <tr><td><input type="submit" value="SAVE TEXT" /></td></tr>
    </table>
    </form>

</div>