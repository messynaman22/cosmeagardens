jQuery(document).ready(function () {

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
                    jQuery('#complimentary_gift_message_select').html('<option value="">Select Message</option>'+resultdata);
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

})