<?php
/**
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (WC()->cart->get_cart_contents_count() == 0) {
    header('location:' . home_url());
}

$product_id = $_SESSION['curr_item'];
$delivery_date = $_SESSION['delivery_date'];
$get_pro_ids = get_field('personal_gift_product', $product_id);

get_header('shop');

global $post;

if (isset($_SESSION['gift'][$product_id])) {
    $selected = $_SESSION['gift'][$product_id]['occasion'];
    $gift_message = $_SESSION['gift'][$product_id]['gift_message'];
} else {
    $selected = "";
    $gift_message = "";
}
?>

<form method="post" action="">
    <input type="hidden" value="yes" name="is_gifts_page"/>
    <input type="hidden" value="<?php echo $delivery_date; ?>" name="delivery_date"/>
    <input type="hidden" value="<?php echo $product_id; ?>" name="product_id" />
    <div class="container">
        <?php //checkout_steps_section('2'); ?>
        <div class="page-content">
            <div class="row">
                <div class="content main-products-loop">
                    <div class="container">
                        <?php do_action('woocommerce_archive_description'); ?>
                    </div>
                    <?php if (have_posts()) : ?>

                        <?php if (woocommerce_products_will_display() && false): ?>
                            <div class="filter-wrap" style="border:1px solid green">
                                <div class="filter-content">
                                    <?php
                                    /**
                                     * woocommerce_before_shop_loop hook
                                     *
                                     * @hooked woocommerce_result_count - 20
                                     * @hooked woocommerce_catalog_ordering - 30
                                     * @hooked et_grid_list_switcher - 35
                                     */
                                    do_action('woocommerce_before_shop_loop');
                                    ?>
                                </div>
                            </div>
                        <?php endif ?>
                        <div class="product-navigation clearfix">
                            <h4 style=" font-size: 20px; margin-bottom: auto;" class="meta-title"><span>Personalise your gift</span></h4>
                            <span class="page-sub-heading">Choose on from our selected items below to complement your gift</span>
                        </div>
                        <?php if (!empty($get_pro_ids)) { ?>

                            <?php
                            foreach ($get_pro_ids as $kk => $vv) {
                                $product = wc_get_product($vv->ID);
                                ?>

                                <div class="col-sm-2">
                                    <div class="product-thumb personalize-gift">
                                        <input id="prod-<?php echo $product->post->ID; ?>" name="products_ids[<?php echo $product->post->ID; ?>]" type="checkbox" />
                                        <label for="prod-<?php echo $product->post->ID; ?>">
                                            <img class="img-responsive" src="<?php echo wp_get_attachment_url(get_post_thumbnail_id($product->post->ID)); ?>"/>
                                        </label>
                                        <button type="button" class="btn btn-lg btn-success information-btn" data-toggle="popover" title="<?php $product->post->post_title; ?> - Detail" data-content='<?php echo esc_attr($product->post->post_content) . "<br/>" . $product->post->post_excerpt; ?>'>i</button>
                                    </div>
                                    <div>
                                        <p class="prod-title">
                                            <span><?php echo $product->post->post_title; ?></span>
                                        </p>
                                        <p class="text-center prod-price">
                                            <i class="fa fa-eur" aria-hidden="true"></i><?php echo $product->get_price(); ?>.00
                                        </p>
                                    </div>							
                                </div>
                                <?php
                            }
                        } else {
                            ?>   
                            <?php woocommerce_product_loop_start(); ?>
                            <?php woocommerce_product_subcategories(); ?>

                            <?php while (have_posts()) : the_post(); //var_dump($post);  ?>
                                <?php $product = new WC_Product(get_the_ID()); ?>
                                <div class="col-sm-2">
                                    <div class="product-thumb personalize-gift">
                                        <input name="products_ids[<?php echo $product->id; ?>]" type="checkbox" />
                                        <img class="img-responsive" src="<?php echo wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>"/>
                                        <button type="button" class="btn btn-lg btn-success information-btn" data-toggle="popover" title="<?php the_title(); ?> - Detail" 
                                                data-content='<?php echo esc_attr($product->post->post_content) . "<br/>" . $product->post->post_excerpt ?>'>i</button>
                                    </div>
                                    <div>
                                        <p class="prod-title">
                                            <span><?php the_title(); ?></span>
                                        </p>
                                        <p class="text-center prod-price">
                                            $<?php echo $product->get_price(); ?>.00
                                        </p>
                                    </div>							
                                </div>

                            <?php endwhile; // end of the loop.      ?>

                            <?php
                            woocommerce_product_loop_end();
                        }
                        ?>

                        <?php
                        /**
                         * woocommerce_after_shop_loop hook
                         *
                         * @hooked woocommerce_pagination - 10
                         */
                        //do_action('woocommerce_after_shop_loop');
                        ?>

                    <?php elseif (!woocommerce_product_subcategories(array('before' => woocommerce_product_loop_start(false), 'after' => woocommerce_product_loop_end(false)))) : ?>

                        <?php wc_get_template('loop/no-products-found.php'); ?>

                    <?php endif; ?>

                    <?php
                    /**
                     * woocommerce_after_main_content hook
                     *
                     * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
                     */
                    //do_action('woocommerce_after_main_content');
                    ?>

                </div>

                <?php if (!$full_width) get_sidebar('shop'); ?>

            </div>

            <div class="product-navigation clearfix">
                <h4 style=" font-size: 20px; margin-bottom: auto;" class="meta-title"><span>Card Message</span></h4>
            </div>
            <div class="occasion_section">
                <div class="message_section">
                    <select id="occasion_select_section" name="occasion" class="occasion-select-section" style="margin-bottom:15px">
                        <option value="">Select Occasion</option>
                        <?php
                        $occasions = get_occasions();
                        $already_have = array();
                        foreach ($occasions as $key => $occasion) {
                            if (!in_array($occasion['title'], $already_have)) {
                                $option_classs = $occasion['title'];
                                $already_have[] = $occasion['title'];
                                ?>
                                <option value="<?php echo $option_classs; ?>" <?php if ($option_classs == $selected) { ?> selected="selected"<?php } ?>><?php echo $occasion['title'] ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <input id="no_message" type="radio" class="input-radio"  name="card_message" value="no_message" data-order_button_text="" checked="checked">
                    <label for="no_message"> No Gift Message </label>
                    <div class="">

                    </div>
                    <input id="have_message" type="radio" class="input-radio" name="card_message" disabled="disabled"  value="have_message" data-order_button_text="">
                    <label  for="gift_message"> Complimentary Gift Message <span style="color:#A11193">(150 characters limit)</span></label>
                    <div class="message_detail" style="margin-bottom:-25px; position:relative; display:none">
                        <div id="ocasion_list">
                            <select id="complimentary_gift_message_select">
                                <option value="">Select Message</option>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                        <p class="message_section-or">- OR -</p>
                        <div class="clearfix"></div>
                        <textarea id="gift_message" name="gift_message" maxlength="150" rows="5" style="width: 100%; max-width:400px; padding: 9px 12px;" placeholder="No more than 150 characters"><?php echo (!empty($gift_message) ? $gift_message : ''); ?> </textarea> 
                        <div id="charcount"></div>
                        <div class="clearfix"></div>
                    </div>
                    <input type="submit" name="submit" class="cont_btn" value="Continue to Shopping Cart" />
                </div>
            </div>
        </div>
    </div>
</form>
<?php get_footer('shop'); ?>