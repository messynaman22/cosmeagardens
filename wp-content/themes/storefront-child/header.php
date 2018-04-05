<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div class="mtsnb mtsnb-shown mtsnb-top mtsnb-absolute" id="mtsnb-26702" data-mtsnb-id="26702" style="background-color:#aa00a5;color:#ffffff;">
			<style type="text/css">
			.mtsnb-container-outer {
    position: relative;
}
				.mtsnb { position: absolute; -webkit-box-shadow: 0 3px 4px rgba(0, 0, 0, 0.05);box-shadow: 0 3px 4px rgba(0, 0, 0, 0.05);}
				.mtsnb .mtsnb-container { width: 1080px; font-size: 12px;}
				.mtsnb a { color: #f4a700;}
				.mtsnb .mtsnb-button { background-color: #f4a700;}
				.mtsnb-shown {
    transform: translateY(0%) translate3d(0px, 0px, 0px);
    transition: transform 0.3s ease 0s;
}
.mtsnb {
    backface-visibility: hidden;
    min-height: 30px;
   
    text-align: center;
    top: 0;
    transition: all 0.25s linear 0s;
    width: 100%;
    z-index: 9999;
}
.mtsnb, .mtsnb *, .mtsnb *::before, .mtsnb *::after {
    box-sizing: border-box;
}
.mtsnb {
    line-height: 1;
}
.mtsnb .mtsnb-container {
    line-height: 1.4;
    margin-left: auto;
    margin-right: auto;
    max-width: 100%;
    padding: 10px 60px;
    position: relative;
    width: 1000px;
}


			</style>
			<div class="mtsnb-container-outer">
				<div class="mtsnb-container mtsnb-clearfix">
										<div class="mtsnb-button-type mtsnb-content"><span class="mtsnb-text"><?php echo et_get_block(24987);?></span><a href="" class="mtsnb-link"></a></div>									</div>
							</div>
		</div>

<?php do_action( 'storefront_before_site' ); ?>
<div class="top-bar">
		<div class="container">
				<div class="languages-area">
					<?php if((!function_exists('dynamic_sidebar') || !dynamic_sidebar('languages-sidebar'))): ?>
						<div class="languages">
							<div class="first_tab"><a href="
<?php echo esc_url( home_url( '/' ) ); ?>"><img  alt="CosmeaGardens.com" src="<?php echo get_stylesheet_directory_uri();?>/img/tab_bg.png"></a></div>
							<?php if(!is_checkout()){?><div class="second_tab"><a href="<?php echo $url[0];?>" target="_blank"><img alt="CosmeaGardens - Wedding Blog" src="<?php echo get_stylesheet_directory_uri();?>/img/tab_bg2.png"></a></div><?php }?>
							<div class="clear"></div>
						</div>
					<?php endif; ?>	
				</div>

				<?php if(!is_checkout()){?>
				<div class="top-links">
					<?php etheme_top_links(); ?>
					<?php if((!function_exists('dynamic_sidebar') || !dynamic_sidebar('top-bar-right'))): ?>
					
					<?php endif; ?>	
				</div><?php }?>
		</div>
	</div>
   
  <?php
    if(is_checkout()&& isset($_GET['key'])){?>
    <div id="st-container" class="st-container cont-thankyou">
		
		<?php } else{ ?>
<div id="st-container" class="st-container">
<?php }?>  
 <nav class="st-menu mobile-menu-block">
		<div class="nav-wrapper">
			<div class="st-menu-content">
            <div id="menuclosebutton" class="fa fa-times"></div>
				<div class="mobile-nav">
					<div class="needittoday"><a href="#">
                                        <img src = "<?php echo get_stylesheet_directory_uri(); ?>/img/alarm.png" >
                                        <span class="need-it-today">Need it Today</span>
                                    </a></div>
					<?php 
						et_get_mobile_menu();						
					?>
					
					<?php if (etheme_get_option('top_links')): ?>
						<div class="mobile-nav-heading"><i class="fa fa-user"></i><?php _e('Account', ETHEME_DOMAIN); ?></div>
						<?php etheme_top_links(array('popups' => false)); ?>
					<?php endif; ?>	
                    <ul class="international_delivery">
					<li><a href="https://www.cosmeagardens.com/international-delivery/">International Delivery</a></li>
					</ul>
                    <div class="mobile-callus">
					TEL<a href="tel:+357 24-638777"><span class="num_callus"><img src="<?php echo get_stylesheet_directory_uri();?>/img/icon-callnow.png" alt="Call Now">+357 24-638777</span></a>
                    </div>
					<?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar('mobile-sidebar')): ?>
						
					<?php endif; ?>	
				</div>
			</div>
		</div>
		
	</nav>  
 	<div class="st-pusher" style="background-color:#fff;">
	<div class="st-content">
	<div class="st-content-inner">
	<div class="page-wrapper fixNav-enabled">
    
	
		
<div class="header-wrapper header-type-10  header-scrolling">
    <header class="header main-header">
        <div class="container">
            <div class="navbar" role="navigation">
                <div class="container-fluid">
                    <div id="st-trigger-effects" class="column">
                        <button data-effect="mobile-menu-block" class="menu-icon"></button>
                    </div>
                    <div id="mobile-search">
                    <div class="fa fa-search"></div>
                    </div>
                    
                    <div class="header-logo">
                        <?php etheme_logo(); ?>
                        <span class = "logo_sub_text"><?php echo get_field('text'); ?></span>&nbsp&nbsp <span class = "logo_sub_text_phone green_logo_sub_text_phone ">flowers-plants-gifts </span><span class = "logo_sub_text_phone"><img alt="Call Us" class="phone-img" src="<?php echo get_stylesheet_directory_uri(); ?>/img/phone-icon.png"/><sub style="    font-size: 17px;"> &#43;</sub>357-24-638777
                            <?php //echo get_field('phone_number'); ?>
                        </span> </div>
                    <div class="clearfix visible-md visible-sm visible-xs"></div>
                    <div class="navbar-header navbar-right">
                        <?php
                        if (is_checkout()) {
                            if (!isset($_GET['key'])) {
                                ?>
                                <div class="button_on_checkout">
                                    <a href="<?php echo WC()->cart->get_cart_url(); ?>">Edit shopping cart</a> <span style="padding-left:5px; padding-right:5px">|</span> <a href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>">Continue shopping</a>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="cart_wrapper_custom">
                                <div class="navbar-right">
                                   
                                        <?php etheme_top_cart(); ?>
                                    
                                        <?php etheme_search_form(); ?>
                                    



                                </div>
                                <?php //if(etheme_get_option('top_links')):    ?>
                                <div class="top-links">
                                    <?php //etheme_top_links();    ?>
                                    <a href="#">
                                        <img alt="Need it today" src = "<?php echo get_stylesheet_directory_uri(); ?>/img/alarm.png" >
                                        <span class="need-it-today">Need it Today</span>
                                    </a>
                                </div>
                                <?php //endif;    ?>
                            </div>
                        <?php } ?>
                    </div>
                </div><!-- /.container-fluid -->

            </div>
        </div>
        <div class="container">
            <div class="menu-wrapper">
                <div class="container">


                    <div class="collapse navbar-collapse">
                        <?php  wp_nav_menu(array(
                    'menu' => 'Top Menu',
                    'before' => '',
                    'container_class' => 'menu-main-container',
                    'after' => '',
                    'link_before' => '',
                    'link_after' => '',
                    'depth' => 4,
                    'fallback_cb' => false,
                    //'walker' => new Et_Navigation
                )); ?>
                    </div>
                    <div class="languages-area">
                        <?php if ((!function_exists('dynamic_sidebar') || !dynamic_sidebar('languages-sidebar'))): ?>
                            <div class="languages">
                               

                                <div class="internalional_delivery_block">
                                    <p class="internalional_delivery_flag_block_text"><a style="color:#fff;" href="<?php //echo get_permalink(22553);?>">International Delivery</a></p>
                                    
                                    <div class="clear"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.navbar-collapse --> 
    </header>
</div>

          
   </div></div></div></div>
   
<div id="page" class="hfeed site">


	<div id="content" class="site-content" tabindex="-1">
		<div class="col-full">

		<?php
		/**
		 * Functions hooked in to storefront_content_top
		 *
		 * @hooked woocommerce_breadcrumb - 10
		 */
		do_action( 'storefront_content_top' );
