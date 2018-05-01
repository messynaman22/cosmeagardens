<?php
add_action('wp_enqueue_scripts', 'child_theme_enqueue_styles', 101);
define('ETHEME_DOMAIN', 'cosmeagardens');
function child_theme_enqueue_styles() {
	
	
    wp_enqueue_style('bootstrap-css', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css');
    wp_enqueue_style('select2-css', get_stylesheet_directory_uri() . '/assets/css/select2.css');

    wp_enqueue_script('custom-js');
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'));
    wp_localize_script('custom-js', 'customJS', array('ajaxurl' => admin_url('admin-ajax.php')));
	if ( is_child_theme() ) {
				$child_theme = wp_get_theme( get_stylesheet() );
				wp_enqueue_style( 'storefront-child-style', get_stylesheet_uri(), array(), $child_theme->get( 'Version' ) );
			}

    wp_enqueue_script('select2-js', get_stylesheet_directory_uri() . '/assets/js/select2.js', array('jquery'), false, true);
}

function remove_loop_button() {
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
}

add_action('init', 'remove_loop_button');

function get_occasions($title = '') {
    global $wpdb;
    if ($title != "") {
        $sql = 'SELECT * FROM ' . $wpdb->prefix . 'occasions where title="' . $title . '" order by message_title asc';
    } else {
        $sql = 'SELECT * FROM ' . $wpdb->prefix . 'occasions order by message_title asc';
    }
    return $wpdb->get_results($sql, ARRAY_A);
}

function get_occasions_list() {

    $occasions = get_occasions();
    $output_html = '';
    foreach ($occasions as $occasion) {
        $output_html .= '<tr valign="top">
                                    <th scope="row"></th>
                                    <td><input type="text" value="' . $occasion['title'] . '" readonly /></td>
                                    <td><input type="text" value="' . $occasion['message_title'] . '" readonly /></td>
                                    <td><textarea readonly cols="80">' . $occasion['message'] . '</textarea></td>
                                    <td>
                                        <input class="remove_occasion button button-primary" occasion_id="' . $occasion['id'] . '" type="button" value="Remove Occasion"/>
                                    </td>
                                 </tr>';
    }
    return $output_html;
}

add_action("wp_ajax_ocasion_form", "ocasion_form"); // when logged in
add_action("wp_ajax_nopriv_ocasion_form", "ocasion_form"); //when logged out 

function ocasion_form() {
    $val = $_POST['name'];
    $occasions = get_occasions($val);
    $arrData = '';
    foreach ($occasions as $occasion) {
        $arrData .= '<option value="' . $occasion['message'] . '">' . $occasion['message_title'] . '</option>';
    }
    echo json_encode($arrData);
    exit;
}

function get_delivery_price_by_date($date) {
    global $wpdb;

    $date = str_replace('/', '-', $date);
    $new_date = date("Y-m-d", strtotime($date));

    $sql = "SELECT * FROM " . $wpdb->prefix . "specific_delivery_dates_and_prices WHERE specific_date='" . $new_date . "'";

    $delivery_data = $wpdb->get_row($sql, ARRAY_A);
    if (!empty($delivery_data)) {
        return $delivery_data['specific_price'];
    } else {
        $date = explode('-', $date);
        $date_true_format = sprintf("%02d-%02d-%02d", $date[0], $date[1], $date[2]);
        $week_day = date('l', strtotime($date_true_format));

        switch ($week_day) {
            case 'Monday':
                return get_option('monday_delivery_price');
                ;
                break;
            case 'Tuesday':
                return get_option('tuesday_delivery_price');
                break;
            case 'Wednesday':
                return get_option('wednesday_delivery_price');
                break;
            case 'Thursday':
                return get_option('thursday_delivery_price');
                break;
            case 'Friday':
                return get_option('friday_delivery_price');
                break;
            case 'Saturday':
                return get_option('saturday_delivery_price');
                break;
            case 'Sunday':
                return get_option('sunday_delivery_price');
                break;
        }
    }
}

add_action('woocommerce_after_shop_loop_item', 'replace_add_to_cart');

function replace_add_to_cart() {
    global $product;
    $link = $product->get_permalink();
    echo do_shortcode('<a href="' . $link . '" class="button addtocartbutton">View Product</a>');
}

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 27);

remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
add_action('woocommerce_after_shop_loop_item', 'iconic_template_loop_add_to_cart', 5);


add_action('woocommerce_single_product_summary', 'woocommerce_get_prod_price', 25);

function woocommerce_get_prod_price() {
    global $product;
    echo '<div class="prod_price">Price: ' . $product->get_price_html() . '</div>';
}

add_action('pre_get_posts', 'custom_pre_get_posts_query');

function custom_pre_get_posts_query($q) {

    if (!is_admin()) {
        if (!$q->is_main_query()) {
            return;
        }
        if (!$q->is_post_type_archive()) {
            return;
        }
        if (is_shop()) {
            $q->set('tax_query', array(array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => array('personalise-your-gift'), // Don't display products in the knives category on the shop page
                    'operator' => 'NOT IN'
            )));
        }

        remove_action('pre_get_posts', 'custom_pre_get_posts_query');
    }
}

add_action('woocommerce_proceed_to_shopping', 'woo_add_continue_shopping_button_to_cart');

function woo_add_continue_shopping_button_to_cart() {
    $shop_page_url = get_permalink(woocommerce_get_page_id('shop'));
    echo ' <a href="' . $shop_page_url . '" class="checkout-button continue button alt wc-forward">Continue Shopping</a>';
}

add_filter('woocommerce_add_to_cart_redirect', 'wc_custom_cart_redirect');

function wc_custom_cart_redirect() {
    return get_term_link('personalise-your-gift', 'product_cat');
}

add_action('template_redirect', 'add_product_to_cart');

function add_product_to_cart() {
    session_start();
    $product_id_ses = $_SESSION['curr_item'];
    
    if (isset($_POST['is_gifts_page'])) {

        $items = WC()->cart->get_cart();
        end($items);

        $get_key_last = key($items);

        $get_quantity = $items[$get_key_last]['quantity'];
        $get_product_id = $items[$get_key_last]['product_id'];
        $get_variation_id = $items[$get_key_last]['variation_id'];
        $get_variation_data = $items[$get_key_last]['variation'];

        foreach (WC()->cart->get_cart() as $key => $item) {
            if ($item['product_id'] == $_REQUEST['product_id']) {
                $key_to_remove = $key;
            }
        }

        WC()->cart->remove_cart_item($key_to_remove);
        WC()->cart->add_to_cart($_REQUEST['product_id'], $get_quantity, $get_variation_id, $get_variation_data);


        if (isset($_POST['products_ids'])) {

            foreach ($_POST['products_ids'] as $product_id => $status) {

                $found = false;
                //check if product already in cart
                if (sizeof(WC()->cart->get_cart()) > 0) {
                    foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                        $_product = $values['data'];
                        if ($_product->id == $product_id)
                            $found = true;
                    }
                    WC()->cart->add_to_cart($product_id);
                } else {
                    WC()->cart->add_to_cart($product_id);
                }
                $_SESSION['gift'][$product_id_ses]['common'][] = $product_id;
            }
        }

        if ($_POST['occasion'] != '') {
            $_SESSION['gift'][$product_id_ses]['occasion'] = $_POST['occasion'];
        }
        if ($_POST['card_message'] == 'have_message' && $_POST['gift_message'] != "") {
            $_SESSION['gift'][$product_id_ses]['gift_message'] = $_POST['gift_message'];
        }

        if ($_POST['delivery_date'] != '') {
            $_SESSION['delivery_date'] = $_POST['delivery_date'];
        }
        unset($_SESSION['product_id_for_delivery']);
        wp_redirect(get_permalink(wc_get_page_id('cart')));
    }
}

function bbloomer_split_product_individual_cart_items() {
    $cart_item_data = array();

    if (!empty($_REQUEST['delivery_date'])) {
        $cart_item_data['custom_option']['delivery_date'] = $_REQUEST['delivery_date'];
    } else {
        $cart_item_data['custom_option']['delivery_date'] = $_SESSION['delivery_date'];
    }

    $new_product_id = $_REQUEST['product_id'];
    $delivery_price = get_delivery_price_by_date($_REQUEST['delivery_date']);
    $cart_item_data['cstm_price'] = $delivery_price;

    if (isset($_REQUEST['products_ids'])) {
        $new_products_ids = implode(", ", array_keys($_REQUEST['products_ids']));
    }
    if (!empty($_REQUEST['product_id'])) {
        $cart_item_data['custom_option']['main_product_id'] = $_REQUEST['product_id'];
    }
    if (!empty($_REQUEST['products_ids'])) {
        $cart_item_data['custom_option']['products_ids'] = $new_products_ids;
    }
    if (!empty($_REQUEST['occasion'])) {
        $cart_item_data['custom_option']['occasion'] = $_REQUEST['occasion'];
    }
    if (!empty($_REQUEST['gift_message'])) {
        $cart_item_data['custom_option']['gift_message'] = $_REQUEST['gift_message'];
    }

    $cart_item_data['unique_key'] = md5(microtime() . rand());
    $_SESSION['curr_item'] = $_REQUEST['product_id'];

    return $cart_item_data;
}

add_filter('woocommerce_add_cart_item_data', 'bbloomer_split_product_individual_cart_items', 11, 2);

function first_test() {
    if ($_POST['delivery_date'] != '') {

        $arr['custom_option']['delivery_date'] = $_POST['delivery_date'];
    } else {
        $arr['custom_option']['delivery_date'] = "Blank";
    }
    return $arr;
}

add_filter('woocommerce_is_sold_individually', '__return_true');

function woo_remove_all_quantity_fields($return, $product) {
    return true;
}

add_filter('woocommerce_is_sold_individually', 'woo_remove_all_quantity_fields', 10, 2);

// **********************************************************************// 
// ! Custom Static Blocks Post Type
// **********************************************************************// 

add_action('init', 'et_register_static_blocks');

if(!function_exists('et_register_static_blocks')) {
    function et_register_static_blocks() {
            $labels = array(
                'name' => _x( 'Static Blocks', 'post type general name', ETHEME_DOMAIN ),
                'singular_name' => _x( 'Block', 'post type singular name', ETHEME_DOMAIN ),
                'add_new' => _x( 'Add New', 'static block', ETHEME_DOMAIN ),
                'add_new_item' => sprintf( __( 'Add New %s', ETHEME_DOMAIN ), __( 'Static Blocks', ETHEME_DOMAIN ) ),
                'edit_item' => sprintf( __( 'Edit %s', ETHEME_DOMAIN ), __( 'Static Blocks', ETHEME_DOMAIN ) ),
                'new_item' => sprintf( __( 'New %s', ETHEME_DOMAIN ), __( 'Static Blocks', ETHEME_DOMAIN ) ),
                'all_items' => sprintf( __( 'All %s', ETHEME_DOMAIN ), __( 'Static Blocks', ETHEME_DOMAIN ) ),
                'view_item' => sprintf( __( 'View %s', ETHEME_DOMAIN ), __( 'Static Blocks', ETHEME_DOMAIN ) ),
                'search_items' => sprintf( __( 'Search %a', ETHEME_DOMAIN ), __( 'Static Blocks', ETHEME_DOMAIN ) ),
                'not_found' =>  sprintf( __( 'No %s Found', ETHEME_DOMAIN ), __( 'Static Blocks', ETHEME_DOMAIN ) ),
                'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', ETHEME_DOMAIN ), __( 'Static Blocks', ETHEME_DOMAIN ) ),
                'parent_item_colon' => '',
                'menu_name' => __( 'Static Blocks', ETHEME_DOMAIN )

            );
            $args = array(
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'staticblocks' ),
                'capability_type' => 'post',
                'has_archive' => 'staticblocks',
                'hierarchical' => false,
                'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
                'menu_position' => 8
            );
            register_post_type( 'staticblocks', $args );
    }
}

if(!function_exists('et_get_static_blocks')) {
    function et_get_static_blocks () {
        $return_array = array();
        $args = array( 'post_type' => 'staticblocks', 'posts_per_page' => 50);
		//if ( class_exists( 'bbPress') ) remove_action( 'set_current_user', 'bbp_setup_current_user' );         
		$myposts = get_posts( $args );
        $i=0;
        foreach ( $myposts as $post ) {
            $i++;
            $return_array[$i]['label'] = get_the_title($post->ID);
            $return_array[$i]['value'] = $post->ID;
        } 
        wp_reset_postdata();
		//if ( class_exists( 'bbPress') ) add_action( 'set_current_user', 'bbp_setup_current_user', 10 );
        return $return_array;
    }
}


if(!function_exists('et_show_block')) {
    function et_show_block ($id = false) {
        echo et_get_block($id);
    }
}


if(!function_exists('et_get_block')) {
    function et_get_block($id = false) {
    	if(!$id) return;
    	
    	$output = false;
    	
    	$output = wp_cache_get( $id, 'et_get_block' );
    	
	    if ( !$output ) {
	   
	        $args = array( 'include' => $id,'post_type' => 'staticblocks', 'posts_per_page' => 1);
	        $output = '';
	        $myposts = get_posts( $args );
	        foreach ( $myposts as $post ) {
	        	setup_postdata($post);
				
	        	$output = do_shortcode(get_the_content($post->ID));
	        	
				$shortcodes_custom_css = get_post_meta( $post->ID, '_wpb_shortcodes_custom_css', true );
				if ( ! empty( $shortcodes_custom_css ) ) {
					$output .= '<style type="text/css" data-type="vc_shortcodes-custom-css">';
					$output .= $shortcodes_custom_css;
					$output .= '</style>';
				}
	        }
	        wp_reset_postdata();
	        
	        wp_cache_add( $id, $output, 'et_get_block' );
	    }
	    
        return $output;
   }
}
if (!function_exists('etheme_top_links')) {

	function etheme_top_links($args = array()) {
		extract(shortcode_atts(array(
		'popups' => true
		), $args));
        ?>
        <ul class="links">
           
            
                <?php if (is_user_logged_in()) : ?>
                    <?php if (class_exists('Woocommerce')): ?> 
                        <li class="my-account-link"><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>"><?php _e('My Account', ETHEME_DOMAIN); ?></a></li>
                    <?php endif; ?>
                    <li class="logout-link"><a href="<?php echo wp_logout_url(home_url()); ?>"><?php _e('Logout', ETHEME_DOMAIN); ?></a></li>
                <?php else : ?>
                    <?php
                    $reg_id = etheme_tpl2id('et-registration.php');
                    $reg_url = get_permalink($reg_id);
                    ?>    
                    <?php if (class_exists('Woocommerce')): ?>
                        <li class="login-link">
                            <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>"><?php _e('Log In', ETHEME_DOMAIN); ?></a>
                            <?php if ($popups) : ?>
                                <div class="login-popup">
                                    <div class="popup-title">
                                        <span><?php _e('Login Form', ETHEME_DOMAIN); ?></span>
                                    </div>

                                    <form method="post" class="login" action="<?php echo get_the_permalink(get_option('woocommerce_myaccount_page_id')); ?>">

                                        <?php do_action('woocommerce_login_form_start'); ?>

                                        <p class="form-row form-row-first">
                                            <label for="username"><?php _e('Enter Your E-mail Address', 'woocommerce'); ?> <span class="required">*</span></label>
                                            <input type="text" class="input-text" name="username" id="username" />
                                        </p>
                                        <p class="form-row form-row-last">
                                            <label for="password"><?php _e('Enter Your Password', 'woocommerce'); ?> <span class="required">*</span></label>
                                            <input class="input-text" type="password" name="password" id="password" />
                                        </p>
                                        
<div class="clear"></div>
                                        <p class="form-row">
                                            <?php wp_nonce_field('woocommerce-login'); ?>
                                            <input type="submit" class="button" name="login" value="<?php _e('Login', 'woocommerce'); ?>" />
                                        </p>
<p><a href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php _e('Forgot your password?', ETHEME_DOMAIN); ?></span></a></p>
<p class="text-center"><strong>&mdash; Or sign in with &mdash;</strong></p>
                                        <?php do_action('woocommerce_login_form'); ?>
                                        <div class="clear"></div>

                                        <?php do_action('woocommerce_login_form_end'); ?>

                                    </form>

                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($reg_id)): ?>
                        <li class="register-link">
                            <a href="<?php echo $reg_url; ?>"><?php _e('Create Account', ETHEME_DOMAIN); ?></a>
                            <?php if ($popups) : ?>
                                <div class="register-popup">
                                    <div class="popup-title">
                                        <span><?php _e('Register Form', ETHEME_DOMAIN); ?></span>
                                    </div>
                                    <?php et_register_form(); ?>
                                    <h2>Benefits of Registration</h2>
                                    <ul>
                                    <li>Fast, secure checkout</li>
                                    <li>Earn award points</li>
                                    <li>Exclusive promotions &amp; offers</li>
                                    <li>Remember important dates</li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
           
        </ul>
        <?php
        wp_nav_menu(array(
        'theme_location' => '',
        'menu' => 'top bar menu',
        'container' => 'div',
        'container_class' => 'menu-top-bar-menu-container',
        'container_id' => '',
        'menu_class' => 'links',
        'menu_id' => '',
        'echo' => true,
        'fallback_cb' => 'wp_page_menu',
        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'depth' => 0,
        'walker' => '',
        ));
        ?>
        <?php
	}

}
function etheme_get_option($key, $setting = null,$doshortcode = true) {
  	global $options;
    if ( function_exists( 'ot_get_option' ) ) {
    	if($doshortcode && is_string(ot_get_option( $key,$setting ))){
        	$result = do_shortcode(ot_get_option( $key,$setting ));
    	}else{
        	$result =  ot_get_option( $key,$setting );
    	}
    	return apply_filters('et_option_'.$key, $result);
    }
    
}
if(!function_exists('et_get_mobile_menu')) {
	function et_get_mobile_menu($menu_id = 'mobile-menu') {

        $custom_menu = etheme_get_custom_field('custom_nav');
        $one_page_menu = '';
        if(etheme_get_custom_field('one_page')) $one_page_menu = ' one-page-menu';

        if(!empty($custom_menu) && $custom_menu != '') {
            $output = false;
            $output = wp_cache_get( $custom_menu, 'et_get_mobile_menu' );
            if ( !$output ) {
                ob_start(); 
                
                wp_nav_menu(array(
                    'menu' => $custom_menu,
                    'before' => '',
                    'container_class' => 'menu-mobile-container'.$one_page_menu,
                    'after' => '',
                    'link_before' => '',
                    'link_after' => '',
                    'depth' => 4,
                    'fallback_cb' => false,
                    'walker' => new Et_Navigation_Mobile
                ));
                
                $output = ob_get_contents();
                ob_end_clean();
                
                wp_cache_add( $custom_menu, $output, 'et_get_mobile_menu' );
            }
            
            echo $output;
            return;
        }

		if ( has_nav_menu( $menu_id ) ) {
	    	$output = false;
	    	$output = wp_cache_get( $menu_id, 'et_get_mobile_menu' );
            
		    if ( !$output ) {
			    ob_start(); 
			    
				wp_nav_menu(array(
                    'container_class' => $one_page_menu,
					'theme_location' => 'mobile-menu',
                    'walker' => new Et_Navigation_Mobile
				)); 
				
				$output = ob_get_contents();
				ob_end_clean();
				
		        wp_cache_add( $menu_id, $output, 'et_get_mobile_menu' );
		    }
		    
	        echo $output;
		} else {
			?>
				<br>
				<h4 class="a-center">Set your main menu in <em>Appearance &gt; Menus</em></h4>
			<?php
		}
	}
}


if(!function_exists('et_get_favicon')) {
    function et_get_favicon() {
        $icon = etheme_get_option('favicon');
        if($icon == '') {
            $icon = get_template_directory_uri().'/images/favicon.ico'; 
        }
        return $icon;
    }
}


if(!function_exists('et_get_menus_options')) {
    function et_get_menus_options() {
        $menus = array();
        $menus[] = array("label"=>"Default","value"=>"");
        $nav_terms = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
        foreach ( $nav_terms as $obj ) {
            $menus[] = array("label" => $obj->name ,"value" => $obj->slug);
        }
        return $menus;
    }
}
// **********************************************************************// 
// ! Search form 
// **********************************************************************// 

if(!function_exists('etheme_search_form')) {
    function etheme_search_form() {
    	?>
            <div class="header-search ">
                
                    <div class="et-search-trigger search-dropdown">
                        <div><i class="fa fa-search"></i> <span class="search_label">Search</span></div>
                        <form action="https://www.cosmeagardens.com/" id="searchform" class="hide-input" method="get"> 

		<div class="form-horizontal modal-form">

			<div class="form-group has-border">

				<div class="col-xs-12">

					<input placeholder="Search by Item number or Keyword" class="form-control" name="s" id="s" type="text">

					<input name="post_type" value="product" type="hidden">

				</div>

			</div>

			<div class="form-group form-button">

				<button type="submit" class="btn medium-btn btn-black" style="padding:0 !important">Go</button>

			</div>

		</div>

	</form>
                    </div>
               
            </div>
        <?php 
    }
}




/**
 * undocumented
 */
function et_is_blog () {
	global  $post;
	$posttype = get_post_type($post );
	return ( ((is_archive()) || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag())) && ( $posttype == 'post')  ) ? true : false ;
}
 
 
function etheme_get_custom_field($field, $postid = false) {
	global $post;
	if ( null === $post && !$postid) return FALSE;
	if(!$postid) {
		$postid = $post->ID;
	} 
	$page_for_posts = get_option( 'page_for_posts' );
	$custom_field = get_post_meta($postid, $field, true);
	
	if(is_array($custom_field)) {
		$custom_field = $custom_field[0];
	}
	if ( $custom_field ) {
		return stripslashes( wp_kses_decode_entities( $custom_field ) );
	}
	else {
		return FALSE;
	}
}
function etheme_custom_field($field) {
	echo etheme_get_custom_field($field);
}

if (!function_exists('etheme_logo')) {

	function etheme_logo($fixed_header = false) {
		$logoimg = '';
		if ($logoimg == '') {
			$logoimg = etheme_get_option('logo');
		}

		$custom_logo = '';

		if ($custom_logo != '') {
			$logoimg = $custom_logo;
		}

		if ($fixed_header) {
			$logoimg = etheme_get_option('logo_fixed');
		}
        ?>
        <?php if ($logoimg): ?>
            <a href="<?php echo home_url(); ?>"><img src="<?php echo $logoimg ?>" alt="<?php bloginfo('description'); ?>" /></a>
        <?php else: ?>
            <a href="<?php echo home_url(); ?>"><img src="<?php echo get_stylesheet_directory_uri() . '/img/logo.png'; ?>" alt="<?php bloginfo('name'); ?>"></a>
        <?php
        endif;
        do_action('etheme_after_logo');
	}

}
if(!function_exists('etheme_top_cart')) {
	function etheme_top_cart($load_cart = false) {
        global $woocommerce;
		?>
		
			<div class="shopping-container" <?php if(etheme_get_option('favicon_badge')) echo 'data-fav-badge="enable"' ?>>
				<div class="shopping-cart-widget" id='basket'>
					<a href="<?php echo esc_url($woocommerce->cart->get_cart_url()); ?>" class="cart-summ" data-items-count="<?php echo $woocommerce->cart->cart_contents_count; ?>">
					<div class="cart-bag">
						<i class='ico-sum'></i>
						<?php et_cart_number(); ?>
					</div>
					
					<?php et_cart_total(); ?>
						
					</a>
				</div>

				<div class="cart-popup-container">
					<div class="cart-popup">
						<?php 
							if($load_cart) {
								woocommerce_mini_cart();
							} else {
								echo '<div class="widget_shopping_cart_content"></div>';
							}
						?>
					</div>
				</div>
			</div>
			

    <?php
	}
}
if(!function_exists('et_cart_number')) {
	function et_cart_number() {
        global $woocommerce;
        ?>
			<span class="badge-number"><?php echo $woocommerce->cart->cart_contents_count; ?></span>
        <?php
	}
}
if(!function_exists('et_cart_total')) {
	function et_cart_total() {
        global $woocommerce;
        ?>
			<span class='shop-text'><?php _e('Shopping Cart', ETHEME_DOMAIN) ?>: <span class="total"><?php echo $woocommerce->cart->get_cart_subtotal(); ?></span></span> 
        <?php
	}
}

add_action( 'wp_print_styles', 'my_deregister_styles', 100 );

function my_deregister_styles() {
    wp_deregister_style( get_stylesheet_directory_uri().'/style.css' );
}
if (!function_exists('checkout_footer')) {

	function checkout_footer($checkout) {

		$args = array(
		'post_type' => 'testimonial',
		'posts_per_page' => $count,
		'order' => 'ASC',
		);
		$query = new WP_Query;

		$testimonials = $query->query($args);
        ?>
        <div class="testimonials_container">
            <h2>Customer reviews</h2>
            <div id="text-carousel" class="carousel slide" data-ride="carousel">
                <!--<ol class="carousel-indicators">
                    <li data-target="#text-carousel" data-slide-to="0" class="active"></li>
                    <li data-target="#text-carousel" data-slide-to="1"></li>
                    <li data-target="#text-carousel" data-slide-to="2"></li>
                </ol>-->
                <!-- Wrapper for slides -->

                <div class="carousel-inner">

                    <?php
                    $active = true;
                    foreach ($testimonials as $testimonial) :
                    if ($active) {
                            ?>
                            <div class="item active">
                                <div class="carousel-content">
                                    <p><?php echo $testimonial->post_content; ?> - <?php echo $testimonial->post_title; ?></p>
                                </div>
                                <span class="perfect_service">Perfect service! <span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span></span>
                            </div>
                            <?php
                            $active = false;
                    } else {
                            ?>
                            <div class="item">
                                <div class="carousel-content">
                                    <p><?php echo $testimonial->post_content; ?> - <?php echo $testimonial->post_title; ?></p>
                                </div>
                                <span class="perfect_service">Perfect service! <span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span></span>
                            </div>
                            <?php
                    };
                    endforeach;
                    ?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/feefo.png" class="feefo">
                </div>

            </div>


        </div>


        <style>
            .green-link{
                color: #33B56C !important;
            }
            .links_row a{
                font-size: 14px;
                font-weight: 600;
                color: #878787;
            }
            .carousel-content {
                color:black;
                display:flex;
                align-items:center;
            }

            #text-carousel {
                width: 100%;
                height: auto;
                position: relative;
            }
            .carousel-inner>.item{

            }

            .carousel-indicators .active {
                background-color:#33B56C;
            }
            .carousel-indicators li {
                background-color:#6C736F;
            }



            img.guarantee{
                padding: 17px 0px;
            }
            img.testimonials{
                position: absolute;
                bottom: 0;
                right: 0px;
                background-color: white;
                border-radius: 220px;
                width: 80px;
                height: 80px;
            }
        </style>
        <script>

        jQuery(document).ready(function () {
        	jQuery('#shipping_country_field').addClass("form-row-first");
        	jQuery('#shipping_state_field').addClass("form-row-last");

        	jQuery('#shipping_country').click(function () {
        		jQuery('#shipping_state_field').removeClass("form-row-first");
        	});
        	jQuery('#billing_country').click(function () {
        		jQuery('#billing_state_field').removeClass("form-row-first");
        	});
        });

        jQuery('#shipping_first_name').keypress(function (e) {
        	checkKeyPressForLetters(e);
        });
        jQuery('#shipping_last_name').keypress(function (e) {
        	checkKeyPressForLetters(e);
        });
        jQuery('#shipping_address_1').keypress(function (e) {
        	checkKeyPressForNumbersAndLetters(e);
        });
        jQuery('#shipping_address_2').keypress(function (e) {
        	checkKeyPressForNumbersAndLetters(e);
        });
        jQuery('#shipping_city').keypress(function (e) {
        	checkKeyPressForNumbersAndLetters(e);
        });
        jQuery('#shipping_phone').keypress(function (e) {
        	checkKeyPressForNumbers(e);
        });
        jQuery('#shipping_phone-2').keypress(function (e) {
        	checkKeyPressForNumbers(e);
        });


        jQuery('#billing_first_name').keypress(function (e) {
        	checkKeyPressForLetters(e);
        });
        jQuery('#billing_last_name').keypress(function (e) {
        	checkKeyPressForLetters(e);
        });
        jQuery('#billing_address_1').keypress(function (e) {
        	checkKeyPressForNumbersAndLetters(e);
        });
        jQuery('#billing_address_2').keypress(function (e) {
        	checkKeyPressForNumbersAndLetters(e);
        });
        jQuery('#billing_city').keypress(function (e) {
        	checkKeyPressForNumbersAndLetters(e);
        });
        jQuery('#billing_phone').keypress(function (e) {
        	checkKeyPressForNumbers(e);
        });
        jQuery('#billing_phone-2').keypress(function (e) {
        	checkKeyPressForNumbers(e);
        });

        function checkKeyPressForNumbersAndLetters(e) {
        	var regex = new RegExp("^[a-zA-Z0-9 ]+$");
        	var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        	if (regex.test(str)) {
        		return true;
        	}

        	//e.preventDefault();
        	return false;
        }

        function checkKeyPressForLetters(e) {
        	var regex = new RegExp("^[a-zA-Z ]+$");
        	var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        	if (regex.test(str)) {
        		return true;
        	}

        	//e.preventDefault();
        	return false;
        }

        function checkKeyPressForNumbers(e) {
        	var regex = new RegExp("^[0-9+]+$");
        	var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        	if (regex.test(str)) {
        		return true;
        	}

        	//e.preventDefault();
        	return false;
        }
        jQuery('#shipping_state, #billing_state').attr('disabled','true');
        //jQuery('#order_surprise').attr('checked','checked');
		jQuery( '.wpb_wrapper p:empty' ).remove();
        </script>
        <?php
        return $checkout;
	}

}

add_filter('woocommerce_shipping_fields', 'custom_woocommerce_shipping_fields');

function custom_woocommerce_shipping_fields($shipping_fields) {

    $args = array(
        'post_type' => 'zipcodes'
    );

    $postalcodelist = get_posts($args);

    $postalcodearr = array();
    $addressarr = array();
    foreach ($postalcodelist as $key => $value) {
        
        $address_val = get_post_meta( $value->ID, 'zipcode_address_1', true );
        
        $postalcodearr[$value->post_title] = $value->post_title; 
        $addressarr[$value->ID] = $address_val; 
        //$postalcodearr[''] = 
    }
    
    $shipping_fields['shipping_gift_title'] = array(
        'type' => 'select',
        'label' => __('Title', 'woocommerce'),
        'required' => false,
        'class' => array('form-row', 'form-row-first', 'form-row-wide'),
        'options' => array(
            '' => 'Select one',
            'option_1' => 'Mr.',
            'option_2' => 'Mrs.',
            'option_3' => 'Ms.',
            'option_4' => 'Dr.'
        )
    );

    $shipping_fields['shipping_location_type'] = array(
        'type' => 'select',
        'label' => __('Location Type', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => true,
        'class' => array('form-row', 'form-row-first'),
        'clear' => false,
        'options' => array(
            '' => 'Select location type',
            'Residence' => 'Residence',
            'Apartment' => 'Apartment',
            'Business' => 'Business',
            'Church' => 'Church',
            'School' => 'School',
            'Hospital' => 'Hospital',
        )
    );

    $shipping_fields['shipping_location_name'] = array(
        'type' => 'text',
        'label' => __('Location Name', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => true,
        'class' => array('form-row', 'form-row-last')
    );


    $shipping_fields['shipping_phone'] = array(
        'type' => 'tel',
        'label' => __('Recipient Telephone 1', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => true,
        'class' => array('form-row', 'form-row-first',),
        'clear' => false
    );

    $shipping_fields['shipping_phone-2'] = array(
        'type' => 'tel',
        'label' => __('Recipient Telephone 2', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => false,
        'class' => array('form-row', 'form-row-last'),
        'clear' => true
    );
    
    $shipping_fields['shipping_address_1_cstm'] = array(
        'type' => 'select',
        'label' => __('Street address', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => true,
        'class' => array('form-row', 'form-row-first',),
        'clear' => false,
        'options' => $addressarr
    );
    

    $shipping_fields['shipping_postcode_cstm'] = array(
        'type' => 'select',
        'label' => __('Zip Code', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => false,
        'class' => array('form-row', 'form-row-first postcodefetch',),
        'clear' => false,
        'options' => $postalcodearr
    );

    $delivery_date = $_SESSION['delivery_date'];
    $hr = (gmdate("H") + 2);

    if ($delivery_date == gmdate("d/m/Y")) {
        $shipping_fields['shipping_prefered_delivery_time'] = array(
            'type' => 'textarea',
            'label' => __('Products can only be delivered after 2PM local time. Please call store if you have any questions.', 'woocommerce'),
            'placeholder' => _x('', 'placeholder', 'woocommerce'),
            'required' => false,
            'class' => array('form-row', 'form-row-first'),
            'clear' => false
        );
    } else {
        $shipping_fields['shipping_prefered_delivery_time'] = array(
            'type' => 'select',
            'label' => __('Prefered Delivery Time', 'woocommerce'),
            'placeholder' => _x('', 'placeholder', 'woocommerce'),
            'required' => false,
            'class' => array('form-row', 'form-row-first'),
            'clear' => false,
            'options' => array(
                '' => 'Select one',
                'before_2_pm' => 'Before 2pm',
                'after_2_pm' => 'After 2pm',
                'funeral' => 'Select for funerals only'
            )
        );
    }
    $shipping_fields['shipping_funeral_time'] = array(
        'type' => 'select',
        'label' => __('Funeral Time', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => false,
        'class' => array('form-row', 'form-row-first'),
        'clear' => true,
        'options' => array(
            '' => 'Select one',
            '8am' => '8am',
            '9am' => '9am',
            '10am' => '10am',
            '11am' => '11am',
            '12pm' => '12pm',
            '1pm' => '1pm',
            '2pm' => '2pm',
            '3pm' => '3pm',
            '4pm' => '4pm',
            '5pm' => '5pm',
            '6pm' => '6pm',
        )
    );

    $shipping_fields['shipping_email'] = array(
        'type' => 'email',
        'label' => __('Recipient Email', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => false,
        'class' => array('form-row', 'form-row-last',),
        'clear' => false
    );


    $shipping_fields['order_surprise'] = array(
        'type' => 'checkbox',
        'value' => '1',
        'label' => __('This is a surprise order <button type="button" class="btn btn-lg btn-success information-btn" data-toggle="popover" title="" data-content="Every order is verified with the recipient prior to delivery. By selecting this surprise box, the florist will not call to verify the address and will leave the flowers at the entrance of the destination in case the recipient is missing. The florist brings no responsibility for any loss or damage." data-original-title="Info">?</button>', 'woocommerce'),
        'class' => array('form-row', 'form-row-wide'),
        'clear' => false,
        'checked' => true
    );
    
    
    return $shipping_fields;
}

add_filter("woocommerce_checkout_fields", "order_shipping_fields");

function order_shipping_fields($fields) {

    $order = array(
        "shipping_gift_title",
        "shipping_first_name",
        "shipping_last_name",
        "shipping_location_type",
        "shipping_location_name",
        "shipping_address_1",
        "shipping_address_1_cstm",
        "shipping_address_2",
        "shipping_country",
        "shipping_state",
        "shipping_city",
        "shipping_postcode",
        "shipping_postcode_cstm",
        "shipping_phone",
        "shipping_phone-2",
        "shipping_prefered_delivery_time",
        "shipping_email",
        "shipping_funeral_time",
        "order_surprise"
    );
    foreach ($order as $field) {
        $ordered_fields[$field] = $fields["shipping"][$field];
    }

    $fields["shipping"] = $ordered_fields;
    return $fields;
}

add_filter('woocommerce_billing_fields', 'custom_woocommerce_billing_fields');

function custom_woocommerce_billing_fields($billing_fields) {


    $billing_fields['billing_email']['class'] = array('form-row', 'form-row-first');

    $billing_fields['billing_email_confirm'] = array(
        'type' => 'email',
        'label' => __('Confirm Email Address', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => true,
        'class' => array('form-row', 'form-row-last'),
        'clear' => true
    );

    $billing_fields['billing_city']['class'] = array('form-row', 'form-row-first');

    $billing_fields['billing_state']['class'] = array('form-row', 'form-row-last');

    $billing_fields['billing_phone'] = array(
        'type' => 'tel',
        'label' => __('Daytime Telephone 1', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => true,
        'class' => array('form-row', 'form-row-first'),
        'clear' => false
    );
    $billing_fields['billing_phone-2'] = array(
        'type' => 'tel',
        'label' => __('Daytime Telephone 2', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => false,
        'class' => array('form-row', 'form-row-last'),
        'clear' => true
    );


    return $billing_fields;
}

add_filter("woocommerce_checkout_fields", "order_billing_fields");

function order_billing_fields($billingfields) {

    $billingorder = array(
        "billing_first_name",
        "billing_last_name",
        "billing_company",
        "billing_email",
        "billing_email_confirm",
        "billing_phone",
        "billing_phone-2",
        "billing_address_1",
        "billing_address_2",
        "billing_country",
        "billing_state",
        "billing_city",
        "billing_postcode",
    );
    foreach ($billingorder as $billingfield) {
        $billingordered_fields[$billingfield] = $billingfields["billing"][$billingfield];
    }

    $billingfields["billing"] = $billingordered_fields;
    return $billingfields;
}

// register custom post type to work with
add_action('init', 'zipcodes_create_post_type');

function zipcodes_create_post_type() {
    // clothes custom post type
    // set up labels
    $labels = array(
        'name' => _x("zipcodes", "post type general name"),
        'singular_name' => _x("zipcodes", "post type singular name"),
        'menu_name' => 'Zip Codes List',
        'add_new' => _x("Add New", "zipcodes item"),
        'add_new_item' => __("Add New Zipcode"),
        'edit_item' => __("Edit Zipcode"),
        'new_item' => __("New Zipcode"),
        'view_item' => __("View Zipcode"),
        'search_items' => __("Search Zipcode"),
        'not_found' => __("No Profiles Found"),
        'not_found_in_trash' => __("No Zipcode Found in Trash"),
        'parent_item_colon' => ''
    );
    register_post_type('zipcodes', array(
        'labels' => $labels,
        'has_archive' => true,
        'public' => true,
        'hierarchical' => true,
        'supports' => array('title', 'custom-fields'),
        /* 'taxonomies' => array( 'post_tag'), */
        'exclude_from_search' => true,
        'capability_type' => 'post',
            )
    );
}

add_action("wp_ajax_set_address_detials_by_zipcode", "set_address_detials_by_zipcode"); // when logged in
add_action("wp_ajax_nopriv_set_address_detials_by_zipcode", "set_address_detials_by_zipcode"); //when logged out

function set_address_detials_by_zipcode() {

    if (!empty($_REQUEST['postcode'])) {

        $post = get_page_by_title($_REQUEST['postcode'], OBJECT, 'zipcodes');
        
        $set_postcode_drp['post_id'] = $post->ID;
        $set_postcode_drp['zipcode_country'] = get_post_meta($post->ID, 'zipcode_country');
        $set_postcode_drp['zipcode_state'] = get_post_meta($post->ID, 'zipcode_state');
        $set_postcode_drp['zipcode_city'] = get_post_meta($post->ID, 'zipcode_city');
        $set_postcode_drp['zipcode_address_1'] = get_post_meta($post->ID, 'zipcode_address_1');
        $set_postcode_drp['zipcode_address_2'] = get_post_meta($post->ID, 'zipcode_address_2');
        $set_postcode_drp['result'] = 'sucess';
    } else {
        $set_postcode_drp['data'] .= '';
        $set_postcode_drp['result'] = 'error';
    }


    $json_result = json_encode($set_postcode_drp);
    $json_result = str_replace("\\/", "/", $json_result);
    echo $json_result;
    die();
}

add_action("wp_ajax_set_zipcode_detials_by_address", "set_zipcode_detials_by_address"); // when logged in
add_action("wp_ajax_nopriv_set_zipcode_detials_by_address", "set_zipcode_detials_by_address"); //when logged out

function set_zipcode_detials_by_address() {

    if (!empty($_REQUEST['post_id'])) {

        //$post = get_page_by_title($_REQUEST['postcode'], OBJECT, 'zipcodes');
        
        $set_postcode_drp['post_id'] = $_REQUEST['post_id'];
        $set_postcode_drp['postal_code'] = get_the_title($_REQUEST['post_id']);
        $set_postcode_drp['zipcode_country'] = get_post_meta($_REQUEST['post_id'], 'zipcode_country');
        $set_postcode_drp['zipcode_state'] = get_post_meta($_REQUEST['post_id'], 'zipcode_state');
        $set_postcode_drp['zipcode_city'] = get_post_meta($_REQUEST['post_id'], 'zipcode_city');
        $set_postcode_drp['zipcode_address_1'] = get_post_meta($_REQUEST['post_id'], 'zipcode_address_1');
        $set_postcode_drp['zipcode_address_2'] = get_post_meta($_REQUEST['post_id'], 'zipcode_address_2');
        $set_postcode_drp['result'] = 'sucess';
    } else {
        $set_postcode_drp['data'] .= '';
        $set_postcode_drp['result'] = 'error';
    }

    $json_result = json_encode($set_postcode_drp);
    $json_result = str_replace("\\/", "/", $json_result);
    echo $json_result;
    die();
}
