<?php

add_action('wp_enqueue_scripts', 'child_theme_enqueue_styles', 101);

function child_theme_enqueue_styles() {
    wp_enqueue_style('bootstrap-css', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css');
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'));
    wp_enqueue_script('custom-js');
    wp_localize_script('custom-js', 'customJS', array('ajaxurl' => admin_url('admin-ajax.php')));
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
