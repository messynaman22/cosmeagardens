!function(t){"use strict";t(function(){t("#shipping_city").val(""),t(".continue_delivery.btn-success").click(function(e){e.preventDefault(),t("#have_message").is(":checked")?t.trim(t("#gift_message").val())?t("form").submit():alert("You did not enter a gift message. If you want to continue without entering a gift message, please select the 'No Gift Message' radio button."):t("form").submit()})})}(jQuery),jQuery(function(t){t("#shipping_city").prop("readonly",!0),t("#shipping_city").on("change",function(e){var i=t(this).val();t("div#divLoading").addClass("show"),t.ajax({type:"post",context:this,dataType:"json",url:headJS.ajaxurl,data:{action:"set_postcode_by_city",city:i},async:!1,success:function(e){"sucess"==e.result?(t("#shipping_postcode").val(e.data),t("div#divLoading").removeClass("show")):(t("#shipping_postcode").val(e.data),t("div#divLoading").removeClass("show"))}})}),t(".wpb_wrapper p:empty").remove(),t(".prdctfltr_filter .widget-title").click(function(){t(this).toggleClass("expend")})});