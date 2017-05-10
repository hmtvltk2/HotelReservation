<?php
/**
 * @package JHotelReservation
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

//no direct accees
defined ('_JEXEC') or die ('Resticted aceess');
?>

<div id="jhotelreservation-wrap" class="clearfix">
	<div class="dir-box-icon-wrapper">
		<div class="styled-select small">
			<select id='hotelCurrency' name='hotelCurrency' class='select_hotelreservation'>
				<?php
				foreach($currencies as $currency)
					{
					?>
					<option value='<?php echo $currency->description."_".$currency->currency_symbol?>' <?php echo isset($userData->currency->name) && $userData->currency->name==$currency->description ? " selected " : ""?> >
						<?php echo $currency->description?>
					</option>
					<?php
					}
				?>
			</select>
		</div>
	</div>
</div>

<script type="text/javascript">
//change currency based on selection  

jQuery(document).ready(function() {

	 jQuery("#hotelCurrency").change(function(){
	     var siteRoot = '<?php echo JURI::root();?>';
	     var compName = '<?php echo getBookingExtName();?>';
	     var task = '<?php echo JFactory::getApplication()->input->get('task');?>';
	     var controller = '<?php echo JFactory::getApplication()->input->get('view');?>';
	     var url = siteRoot+'index.php?option='+compName+'&task=currency.setCurrency&currencySelector='+jQuery("#hotelCurrency").val();
 
		 jQuery("form").each(function () {
			 var activeForm = jQuery(this);
			 formName = activeForm.attr('name'); 

			 if(formName=='userForm' || formName=='adminForm' || formName=='searchForm'){
				console.debug(formName);
				getData(formName,url,controller+"."+task);
				return false;//exit each loop
			 }
			 
	   	 });

		 
   	 });

	 getData = function(formName,url,task) {
			jQuery.ajax({
			    type: "POST",  
			    url: url,
			    data: null,
			    dataType: "html",
			    cache: false,
			    async : false,
			    success: function(data) {
			    	jQuery("#task").val(task);
			    	jQuery("#resetSearch").val("");
			    	setTimeout(function(){console.debug(task);jQuery("form#"+formName).submit();}, 0);
			    	
			    }
			})
		};
	    	 
	
});


</script>

