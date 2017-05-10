function addingClasses(toLabels){
    jQuery(toLabels).each(function()
    {
        var inputId0 = jQuery('#'+jQuery(this).attr('id') + '0');
        var inputId1 = jQuery('#'+jQuery(this).attr('id') + '1');
        var inputId2 = jQuery('#'+jQuery(this).attr('id') + '2');

        var labelId0 = jQuery('#label_'+  jQuery(this).attr('id') + '0');
        var labelId1 = jQuery('#label_' + jQuery(this).attr('id') + '1');
        var labelId2 = jQuery('#label_' + jQuery(this).attr('id') + '2');




        if(jQuery(inputId1).is(':checked')){
            jQuery(labelId0).removeClass('active btn-success');
            jQuery(labelId2).removeClass('active btn-success');
            jQuery(labelId1).addClass('active btn-danger');
        }

        if(jQuery(inputId0).is(':checked'))
        {
            jQuery(labelId1).removeClass('active btn-danger');
            jQuery(labelId2).removeClass('active btn-success');
            jQuery(labelId0).addClass('active btn-success');
        }

        if(jQuery(inputId2).is(':checked'))
        {
            jQuery(labelId1).removeClass('active btn-danger');
            jQuery(labelId0).removeClass('active btn-success');
            jQuery(labelId2).addClass('active btn-success');

        }
    });
}

function responsiveRatePrices(priceDays,priceTable,price){
    if (jQuery(window).width() <= 782) {
        jQuery(priceDays).addClass('RoomTablePriceDays RoomTableDays');
        jQuery(priceTable).addClass('RoomTablePrices');
        jQuery(price).attr('size', '15');
        console.log("Screen size changed");
        console.log(jQuery(window).width());
    } else {
        jQuery(priceDays).removeClass('RoomTablePriceDays RoomTableDays');
        jQuery(priceTable).removeClass('RoomTablePrices');
        jQuery(price).attr('size', '10');
        console.log("Screen size in ELSE statement");
        console.log(jQuery(window).width());
    }
}

