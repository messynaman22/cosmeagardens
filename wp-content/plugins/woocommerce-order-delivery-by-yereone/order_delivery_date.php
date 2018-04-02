<?php

/*
  Plugin Name: WooCommerce Order Delivery Date
  Plugin URI: https://www.yereone.com
  Description: This plugin allows customers to choose their preferred Order Delivery Date in Product page
  Author: YereOne Company
  Version: 1.0.0
  Author URI: https://www.yereone.com
 */


register_uninstall_hook(__FILE__, 'orddd_lite_deactivate');

if (!class_exists('Yc_Order_Delivery_Date')) {

    class Yc_Order_Delivery_Date {

        public $specific_delivery_dates;
        public $plugin_folder_url;

        public function __construct() {
            register_activation_hook(__FILE__, array($this, 'plugin_activation'));
            add_action('wp_enqueue_scripts', array($this, 'load_front_styles_and_scripts'));
            add_action('woocommerce_single_product_summary', array($this, 'add_datepicker_in_single_product_page'), 30);
            add_action('wp_ajax_add_delivery_date_session_var', array($this, 'add_delivery_date_session'));
            add_action('wp_ajax_nopriv_add_delivery_date_session_var', array($this, 'add_delivery_date_session'));
            add_filter('woocommerce_add_to_cart_validation', array($this, 'validate_delivery_date_after_add_to_cart'), 10, 5);
            add_action('woocommerce_new_order', array($this, 'add_order_delivery_date_meta'), 1, 1);
            add_action('wp', array($this, 'unset_delivery_date'), 1);
            add_action('woocommerce_calculate_totals', array($this, 'calculate_order_total_price'));
            $this->plugin_folder_url = plugin_dir_url(__FILE__);
            $this->get_specific_delivery_dates();

            if (is_admin()) {
                add_action('admin_menu', array($this, 'register_setting_page'));
                add_action('admin_init', array($this, 'register_plugin_settings'));
                add_action('admin_enqueue_scripts', array($this, 'load_admin_styles_and_scripts'));
                add_action('wp_ajax_save_specific_delivery', array($this, 'save_specific_delivery_date_and_price'));
                add_action('wp_ajax_remove_specific_delivery', array($this, 'remove_specific_delivery_row'));
                add_filter('manage_edit-shop_order_columns', array($this, 'add_order_delivery_date_column'));
                add_action('manage_shop_order_posts_custom_column', array($this, 'show_order_delivery_date_value'), 2);
                add_filter('manage_edit-shop_order_sortable_columns', array($this, 'delivery_date_sort'));
            }

            add_shortcode('get_yc_datepicker', array($this, 'add_custom_datepicker_in_single_product_page'));
        }

        public function calculate_order_total_price() {
            session_start();
            global $woocommerce;

            //$date = $_SESSION['delivery_date'];
            //$delivery_price = $this->get_delivery_price_by_date($date);
            //$woocommerce->cart->cart_contents_total = $woocommerce->cart->cart_contents_total + $delivery_price;
//            echo "<pre>";
//            print_r($woocommerce->cart);
//            exit;
            return $woocommerce;
        }

        public function get_delivery_price_by_date($date) {
            global $wpdb;

            $date = str_replace('/', '-', $date);
            $new_date = date("Y-m-d", strtotime($date));

            $sql = "SELECT * FROM " . $wpdb->prefix . "specific_delivery_dates_and_prices WHERE specific_date='" . $new_date . "'";

            $delivery_data = $wpdb->get_row($sql, ARRAY_A);
            if (!empty($delivery_data)) {
                return $delivery_data['specific_price'];
            } else {
                $date = explode('-', $date);
                $date_true_format = sprintf("%04d-%02d-%02d", $date[0], $date[1], $date[2]);
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

        public function plugin_activation() {
            global $wpdb;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "specific_delivery_dates_and_prices" . " (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                delivery_title VARCHAR(255) NOT NULL,
                specific_date VARCHAR(255) NOT NULL,
                specific_price VARCHAR(255) NOT NULL,
                UNIQUE KEY id (id)
                );";
            dbDelta($sql);
        }

        public function get_specific_delivery_dates() {
            global $wpdb;
            $sql = 'SELECT * FROM ' . $wpdb->prefix . 'specific_delivery_dates_and_prices';
            $this->specific_delivery_dates = $wpdb->get_results($sql, ARRAY_A);
        }

        /*         * ****************************************************************************************************************
         * ************************************************** BACKEND SIDE *************************************************
         * **************************************************************************************************************** */

        public function load_admin_styles_and_scripts() {
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('admin-page-scripts', $this->plugin_folder_url . '/js/admin.js');
            wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
            wp_enqueue_style('admin-page-styles', $this->plugin_folder_url . '/css/admin.css');
        }

        public function register_setting_page() {
            add_menu_page('Delivery Dates', 'Delivery Dates', 'manage_options', 'yc_order_delivery_dates', array($this, 'yc_order_delivery_date_setting_page'), 'dashicons-calendar-alt', 6);
        }

        public function register_plugin_settings() {
            register_setting('yc-order-delivery-date-settings-group', 'sunday_delivery_price');
            register_setting('yc-order-delivery-date-settings-group', 'monday_delivery_price');
            register_setting('yc-order-delivery-date-settings-group', 'tuesday_delivery_price');
            register_setting('yc-order-delivery-date-settings-group', 'wednesday_delivery_price');
            register_setting('yc-order-delivery-date-settings-group', 'thursday_delivery_price');
            register_setting('yc-order-delivery-date-settings-group', 'friday_delivery_price');
            register_setting('yc-order-delivery-date-settings-group', 'saturday_delivery_price');
            register_setting('yc-order-delivery-text', 'delivery_text');
        }

        public function yc_order_delivery_date_setting_page() {
            include_once __DIR__ . '/templates/admin-settings.php';
        }

        public function get_specific_delivery_dates_list() {
            if (!empty($this->specific_delivery_dates)) {
                $output_html = '';
                foreach ($this->specific_delivery_dates as $delivery_date) {
                    $output_html .= '<tr valign="top">
                                        <th scope="row"></th>
                                        <td><input type="text" value="' . $delivery_date['delivery_title'] . '" readonly /></td>
                                        <td><input type="text" value="' . $delivery_date['specific_date'] . '" readonly /></td>
                                        <td><input type="text" value="' . $delivery_date['specific_price'] . '" readonly /></td>
                                        <td>
                                            <input class="remove_specific_delivery_date_and_price button button-primary" delivery_date_data_id="' . $delivery_date['id'] . '" type="button" value="Remove Date"/>
                                        </td>
                                     </tr>';
                }
            }
            return $output_html;
        }

        public function save_specific_delivery_date_and_price() {
            global $wpdb;
            $data = array("delivery_title" => $_POST['delivery_title'], "specific_date" => $_POST['specific_date'], "specific_price" => $_POST['specific_price']);
            $wpdb->insert(
                    $wpdb->prefix . "specific_delivery_dates_and_prices", $data
            );
            $output_html = '<tr valign="top">
                                <th scope="row" class="">Saved <span class="dashicons dashicons-yes"></span></th>
                                <td><input type="text" value="' . $_POST['delivery_title'] . '" readonly /></td>
                                <td><input type="text" value="' . $_POST['specific_date'] . '" readonly /></td>
                                <td><input type="text" value="' . $_POST['specific_price'] . '" readonly /></td>
                                <td>
                                    <input class="remove_specific_delivery_date_and_price button button-primary" type="button" delivery_date_data_id="' . $wpdb->insert_id . '" value="Remove Date"/>
                                </td>
                            </tr>';
            echo $output_html;
            wp_die();
        }

        public function remove_specific_delivery_row() {
            global $wpdb;
            $wpdb->delete($wpdb->prefix . "specific_delivery_dates_and_prices", array('id' => $_POST['delivery_id']));
            wp_die();
        }

        public function add_order_delivery_date_column($columns) {
            $new_columns = (is_array($columns)) ? $columns : array();
            $new_columns['delivery_date'] = 'Delivery Date';
            return $new_columns;
        }

        public function show_order_delivery_date_value($column) {
            global $post;
            $data = get_post_meta($post->ID);
            if ($column == 'delivery_date') {
                echo isset($data['delivery_date']) ? str_replace(' - ', '/', $data['delivery_date'][0]) : '';
            }
        }

        public function delivery_date_sort($columns) {
            $custom = array(
                'delivery_date' => 'delivery_date',
            );
            return wp_parse_args($custom, $columns);
        }

        public function add_order_delivery_date_meta($order_id) {
            session_start();
            update_post_meta($order_id, 'delivery_date', $_SESSION['delivery_date']);
            unset($_SESSION['delivery_date']);
            unset($_SESSION['product_id_for_delivery']);
        }

        /*         * ****************************************************************************************************************
         * ************************************************* FRONTEND SIDE *************************************************
         * **************************************************************************************************************** */

        public function load_front_styles_and_scripts() {
            if (is_single()) {
                ?>

                <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
                <?php

                //if( !wp_style_is( 'bootstrap.css' )             && !wp_style_is( 'bootstrap.min.css' ) )            wp_enqueue_style('bootstrap.css', plugins_url( '/woocommerce-order-delivery-by-yereone/lib/bootstrap-3.3.6-dist/css/bootstrap.min.css', dirname(__FILE__) ) );
                if (!wp_style_is('glDatePicker.default.css') && !wp_style_is('glDatePicker.default.min.css') || true)
                    wp_enqueue_style('glDatePicker.css', $this->plugin_folder_url . '/lib/glDatePicker-2.0/styles/glDatePicker.default.css');
                //if( !wp_script_is( 'jquery.js' )                && !wp_script_is( 'jquery.min.js' ) )               wp_enqueue_script('jQuery.min.js', 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js' );
                //if( !wp_script_is( 'bootstrap.js' )             && !wp_script_is( 'bootstrap.min.js' ) )            wp_enqueue_script('bootstrap.js', plugins_url( '/woocommerce-order-delivery-by-yereone/lib/bootstrap-3.3.6-dist/js/bootstrap.js', dirname(__FILE__) ) );
                if (!wp_script_is('glDatePicker.js') && !wp_script_is('glDatePicker.min.js') || true)
                    wp_enqueue_script('glDatePicker.js', $this->plugin_folder_url . '/lib/glDatePicker-2.0/glDatePicker.js');
                wp_enqueue_script('front-scripts', $this->plugin_folder_url . '/js/front.js');
                wp_enqueue_style('front-styles', $this->plugin_folder_url . '/css/front.css');
            }
        }

        public function add_datepicker_in_single_product_page() {
            include_once __DIR__ . '/templates/datepicker-popup.php';
        }

        public function add_custom_datepicker_in_single_product_page() {
            include_once __DIR__ . '/templates/shortcode-datepicker-popup.php';
        }

        public function get_specific_delivery_dates_for_script() {
            if (!empty($this->specific_delivery_dates)) {
                $output_script = '';
                foreach ($this->specific_delivery_dates as $delivery_date) {
                    $date = str_replace('-', ' ,', $delivery_date['specific_date']);
                    $date = explode(',', $date);
                    $date[1] = $date[1] - 1;
                    $date = implode(",", $date);
                    $output_script .= '{
											date: new Date(' . $date . '),
											data: { message: "' . $delivery_date['delivery_title'] . '\n Price - ' . $delivery_date['specific_price'] . '" },
										},';
                }
            }
            return $output_script;
        }

        public function get_specific_delivery_dates_for_table() {
            if (!empty($this->specific_delivery_dates)) {
                $output_html = '';
                $successRow = true;
                foreach ($this->specific_delivery_dates as $delivery_date) {
                    $rowClass = $successRow == true ? 'table-success' : 'table-active';
                    $output_html .= '<tr class="' . $rowClass . '">
										<td>' . $delivery_date['specific_date'] . '</td>
										<td>' . $delivery_date['delivery_title'] . '</td>
										<td>' . $delivery_date['specific_price'] . '</td>
									</tr>';
                    $successRow = $successRow == true ? false : true;
                }
            }
            return $output_html;
        }

        public function add_delivery_date_session() {
            session_start();
            $_SESSION['delivery_date'] = $_POST['delivery_date'];
        }

        public function validate_delivery_date_after_add_to_cart($passed, $product_id, $quantity, $variation_id = '', $variations = '') {
            session_start();

            $delivery_date = isset($_SESSION['delivery_date']) ? $_SESSION['delivery_date'] : '';
            $hr = (gmdate("H") + 2);
            if ($delivery_date == '') {
                $passed = false;
                wc_add_notice(__('<b><font color="red">Please Select Delivery Date</font></b>', 'textdomain'), 'error');
            }
            if ($delivery_date == gmdate("d/m/Y") and ( $hr >= 14)) {
                $passed = false;
                wc_add_notice(__('<b><font color="red">Products can\'t be delivered after 2pm local time. Please call the store or select the following date.</font></b>', 'textdomain'), 'error');
            }
            return $passed;
        }

        public function unset_delivery_date() {
            session_start();
            global $post;
            if (is_singular('product') && $_SESSION['product_id_for_delivery'] != $post->ID) {
                unset($_SESSION['delivery_date']);
            }
        }

    }

}

$delivery_date_object = new Yc_Order_Delivery_Date();

//add_shortcode( 'get_yc_datepicker', array( 'Yc_Order_Delivery_Date', 'add_datepicker_in_single_product_page' ) );
