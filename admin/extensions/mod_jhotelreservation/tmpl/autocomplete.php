<?php 
JHTML::_('stylesheet', 	'https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');
?>

<script>

	jQuery.widget( "custom.catcomplete", jQuery.ui.autocomplete, {

	 _renderItemData: function( ul, item ) {
		 	return jQuery( "<li></li>" )
                .data( "ui-autocomplete-item", item )
                .append( "<a><span class='suggest-label'>" + item.label + " </span><div class='hotels-number'>"+item.nr_hotels+" "+'<?php echo strtolower(JText::_("LNG_HOTELS")) ?>'+"</div></a>" )
                    .appendTo( ul );
        },
        
	  _renderMenu: function( ul, items ) {
	   var self = this,
	    currentCategory = "";
	   jQuery.each( items, function( index, item ) {
	    if ( item.category != currentCategory ) {
	     ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
	     currentCategory = item.category;
	    }
	    self._renderItemData( ul, item );
	   });
	  }
	 });

	jQuery("#keyword").catcomplete({
		source: "<?php echo JRoute::_('index.php?option=com_jhotelreservation&task=hotels.getSuggestionsList') ?>",
		minLength: 2,
		select: function( event, ui ) {
			searchHotels(ui.item.label, ui.item.value, ui.item.category);
			jQuery(this).val(ui.item.label);
			return false;
		}
	});

	function searchHotels(label, value, category){
		jQuery("#searchType").val(category);
		jQuery("#searchId").val(value);

		if(category=="<?php echo JText::_("LNG_ACCOMMODATION_TYPES")?>"){
			jQuery("#filterParams").val("accommodationTypeId="+value);
		}

		if(category=="<?php echo JText::_("LNG_THEMES")?>"){
			jQuery("#filterParams").val("themeId="+value);
		}
		
		jQuery('form').attr('autocomplete', 'off');
	}
 </script>