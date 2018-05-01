jQuery(document).ready(function () {

    jQuery("#shipping_postcode_cstm").select2({
        minimumInputLength: 2
    }).on('change', function (e) {
        var postcode = jQuery(this).val();
        jQuery.ajax({
            type: "post",
            context: this,
            dataType: "json",
            url: customJS.ajaxurl,
            data: {
                action: "set_address_detials_by_zipcode",
                postcode: postcode
            },
            async: false,
            success: function (response) {
                if (response.result == 'sucess') {
                    jQuery('#shipping_country').val(response.zipcode_country);
                    jQuery('#shipping_state').val(response.zipcode_state);
                    jQuery('#shipping_city').val(response.zipcode_city);
                    jQuery('#shipping_address_1').val(response.zipcode_address_1);
                    jQuery("#shipping_address_1_cstm").select2().val(response.post_id).trigger('change.select2');
                    jQuery('#shipping_address_2').val(response.zipcode_address_2);
                }
            }
        })


        jQuery('#shipping_postcode').val(jQuery(this).val());
        //jQuery('#shipping_postcode').trigger('change');
        jQuery('body').trigger('update_checkout');
    });


    jQuery("#shipping_address_1_cstm").select2({
        minimumInputLength: 2
    }).on('change', function (e) {
        var post_id = jQuery(this).val();
        jQuery.ajax({
            type: "post",
            context: this,
            dataType: "json",
            url: customJS.ajaxurl,
            data: {
                action: "set_zipcode_detials_by_address",
                post_id: post_id
            },
            async: false,
            success: function (response) {
                if (response.result == 'sucess') {
                    jQuery('#shipping_country').val(response.zipcode_country);
                    jQuery('#shipping_state').val(response.zipcode_state);
                    jQuery('#shipping_city').val(response.zipcode_city);
                    jQuery('#shipping_address_1').val(response.zipcode_address_1);
                    jQuery("#shipping_postcode_cstm").select2().val(response.postal_code).trigger('change.select2');
                    jQuery('#shipping_address_2').val(response.zipcode_address_2);
                    jQuery('#shipping_postcode').val(response.postal_code);
                }
            }
        })
        jQuery('body').trigger('update_checkout');
    });



    jQuery("#occasion_select_section").on('change', function () {
        jQuery(".message_detail #gift_message").val('');
        jQuery(".message_detail #complimentary_gift_message_select").val('');
        jQuery("#have_message").removeAttr('disabled');
        var selectedGroupe = jQuery("#occasion_select_section option:selected").val();

        if (selectedGroupe != "" && selectedGroupe != "undefined") {

            //jQuery('#ocasion_list').html('<img src=' + loader + '>');
            jQuery("#complimentary_gift_message_select option").attr('disabled', 'disabled');
            jQuery("." + selectedGroupe).removeAttr('disabled');

            jQuery.ajax({
                type: "post",
                context: this,
                url: customJS.ajaxurl,
                data: {
                    action: "ocasion_form",
                    name: selectedGroupe,
                },
                success: function (data) {
                    var resultdata = jQuery.parseJSON(data);
                    jQuery('#complimentary_gift_message_select').html('<option value="">Select Message</option>' + resultdata);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("some error");
                }
            });
        } else {
            jQuery('#no_message').attr('checked', 'checked');
            jQuery('#have_message').attr('disabled', 'disabled');
            jQuery('.message_detail').hide();
        }
    });

    jQuery("#have_message").click(function () {
        var status = jQuery(this).attr('checked');
        if (status) {
            jQuery(".message_detail").show(300);
        }
    });

    jQuery("#no_message").click(function () {
        jQuery(".message_detail #gift_message").val('');
        jQuery(".message_detail #complimentary_gift_message_select").val('');
        var status = jQuery(this).attr('checked');
        if (status) {
            jQuery(".message_detail").hide(300);
        }
    });

    jQuery('html').click(function (e) {
        jQuery('[data-toggle="popover"]').popover('hide');
    });

    jQuery('[data-toggle="popover"]').popover({
        html: true,
        trigger: 'manual',
        placement: 'top'
    }).hover(function () {
        jQuery(this).popover('show');
    }, function () {
        jQuery(this).popover('hide');
    });

    var maxLength = 150;
    jQuery('#gift_message').keyup(function () {
        var length = jQuery(this).val().length;
        var length = maxLength - length;
        jQuery('#charcount').text(length + " characters left.");
    });

    jQuery("#complimentary_gift_message_select").change(function () {
        var message = jQuery("#complimentary_gift_message_select option:selected").val();
        jQuery(".message_detail #gift_message").val(message);
    });


    jQuery(".variation_cstm_option").on("change", function (e) {
        var variation_val = jQuery(this).val();
        jQuery('#bouquet-size').val(variation_val).trigger('change');
    });

    jQuery(".radio-image-label").on("click", function (e) {
        jQuery("label.radio-image-label").removeClass('selected-radio-image');
        jQuery(this).addClass('selected-radio-image');
        var package = jQuery('.selected-radio-image > .prod-label').text();
        var variation_id = jQuery('.selected-radio-image > .product-option-option-percentage-price').val();
        var select_index = '';
        switch (package) {
            case 'Classic':
                select_index = 1;
                var text_msg = "Classic sized arrangements will be composed similarly to the picture shown on our website.";
                jQuery('.product-option-appended-message').html(text_msg);
                break;
            case 'Deluxe':
                select_index = 2;
                var text_msg = "Deluxe sized arrangements feature the same color scheme, design and floral components as the premium size, but may contain less blooms and be smaller in size.";
                jQuery('.product-option-appended-message').html(text_msg);
                break;
            case 'Premium':
                select_index = 3;
                var text_msg = "Premium sized arrangements are designed with more blooms and greater selectivity, producing a bouquet with even greater volume and vibrance.";
                jQuery('.product-option-appended-message').html(text_msg);
                break;

        }

        jQuery("#bouquet-size option:selected").removeAttr("selected");
        jQuery('select#bouquet-size>option:eq(' + select_index + ')').attr('selected', true);
        jQuery(".single_variation_wrap").show();
    });


//    jQuery(".postcodefetch").on("change", function (e) {
//        jQuery('.postcodefetch').trigger('click');
//    });


})