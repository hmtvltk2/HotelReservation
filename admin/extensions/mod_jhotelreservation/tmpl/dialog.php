<?php 
$locations = modJHotelReservationHelper::getHotelsLocation( );
$db = JFactory::getDBO();

?>


<div id="dialog" title="<?php echo JText::_("LNG_LOCATION")?>" style="display:none">
	<?php 
		$tmpCountry  = "";
		$tmpCounty = "";
		
	    foreach($locations as $idx=>$location){
	    	if($idx%20 == 0)
	    	   	echo "<ul class='locationColumn'>";

	    	if($tmpCountry!= $location->country_name){
	    		echo "<li class='locationColumnHeader'>".$location->country_name."</li>";
	    		$tmpCountry = $location->country_name;
	    	}
	    	if($tmpCounty!= $location->hotel_county){
	    		echo "<li class='locationColumnParent' data-value='".$location->hotel_county."'>".$location->hotel_county."</li>";
	    		$tmpCounty = $location->hotel_county;
	    	}
	    	echo "<li class='locationColumnItem' data-value='".$location->region_name."'>".JText::_("LNG_".strtoupper($location->region_name))."</li>";
	    		    	
	    	if($idx%20 == 19)
	    		echo "</ul>";
	    }
	
	?>
</div>
<script type="text/javascript">
	jQuery(".expandLocationSearch").click(function() {
		jQuery( "#dialog" ).dialog({
			 position: { my: "left top", at: "left bottom", of: jQuery("#keyword") }, 
			 width: "70%",
			 dialogClass: "no-close",
			 buttons: [
			              {
			                  text: "<?php echo JText::_("LNG_CLOSE")?>",
			                  click: function() {
			                	  jQuery( this ).dialog( "close" );
			                  }
			                }
			              ],
			 classes: {
				    "ui-dialog": "expandLocationDialog"
			}
			
    	});
	});

	jQuery(".locationColumnItem").click(function() {
		region = jQuery(this).attr("data-value")
		jQuery("#searchId").val(region);
		jQuery("#keyword").val(region);
		jQuery("#searchType").val("<?php echo JText::_("LNG_PROVINCE_AND_REGION")?>");
		jQuery( "#dialog" ).dialog( "close" );
	});
</script>
