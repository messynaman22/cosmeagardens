<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>
</div>
<div class="bg_light_pink">
            <?php if(is_front_page()){?>
      <div class="mobi-info-text container">
         
            <?php echo et_get_block(24998); ?>
         
      </div>
      <?php }?>
         <div class="subscribe_form_backend">
      
         <div class="news_letter"><?php echo do_shortcode('[mc4wp_form id="25000"]');?></div>
        
         </div>
         
         <div class="container satisfaction_gurantee">
<div class="col-md-4">
<?php 
echo et_get_block(25003);
?>
</div>
<div class="col-sm-6">
<div class="delivery_content_top">
<?php 
echo et_get_block(25004);
?>
<div class="clearfix"></div>
<div class="delivery_content_bottom">
    
 <?php
 if(is_product() ){
/* ------- Testimonials ----*/
echo et_get_block(25001);
 }else{
	 echo et_get_block(25002);
	 }
?>
</div>
</div>
</div>
<div class="col-sm-6 col-md-2 cards">
<?php 
echo et_get_block(25005);
?>
</div>
</div>
<div class="container custom_menu_footer">
<?php 
echo et_get_block(25019);
?>
</div>
<?php if(is_front_page()){?>
<div class="footer-text">
   
      <div class="container footer-2-bottom">
         <div class="footer_text">
            <?php echo et_get_block(24998); ?>
         </div>
      </div>
      
   </div>
<?php }?>
<div class="copyright_text">
<div class="container">
<div class="row">
<div class="col-sm-6">
<p>Copyright &copy; <?php echo date('Y');?> CosmeaGardens.com | All Rights Reserved</p>
</div>
<div class="col-sm-6 footer_bottom_links">
<p class="font-lato"><a class="bookmark" href="https://www.cosmeagardens.com" title="Cosmeagardens" rel="sidebar">Bookmark</a> <a class="wp-colorbox-inline cboxElement" href="#inline_content">Site Feedback</a> <a href="https://www.cosmeagardens.com/privacy-notice/">Privacy Policy</a> <a href="https://www.cosmeagardens.com/terms-of-use/">Terms of Use</a> <a href="tel:+1-303-499-7111">+357.24.638777</a></p>
</div>
</div>
</div>
</div>

</div>
<?php wp_footer(); ?>
<div style="display: none;">
<div id="inline_content" style="padding:30px">
<h1>Site Feedback</h1>
<p><?php echo do_shortcode('[contact-form-7 id="22362" title="Site Feedback"]');?></p>
</div>
</div>
</body>
</html>
