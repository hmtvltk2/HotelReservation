<?php // no direct access
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('_JEXEC') or die('Restricted access');

$isSuperUser = isSuperUser(JFactory::getUser()->id);
$cssDisplay = $isSuperUser?"block":"none";
?>
<div id="hotel_reservation">
<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationsteps.php'; ?>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=guestdetails'.JHotelUtil::getItemIdS()) ?>" method="post" name="userForm" id="userForm">
	<div class="hotel_reservation ">
		<div class="hoteInnerContainer">
	
		<div class="right hidden-tablet hidden-phone">
			<?php 
				jimport('joomla.application.module.helper');
				// this is where you want to load your module position
				$modules = JModuleHelper::getModules('reservation-info');
			
				foreach($modules as $module)
				{
					echo JModuleHelper::renderModule($module);
				}
			?>
		</div>
		<div class="guestDetails">
			<h3><?php echo JText::_('LNG_ACCOUNT_DETAILS');?></h3>
            <?php
            if(JFactory::getUser()->id == 0) { ?>
            <h5><?php echo JText::_('LNG_GUEST_USER_RETRIEVE_DATA')?><a href="javascript:void(0)" onclick="checkUser()"><?php echo JText::_('LNG_CLICK_HERE')?></a>
            </h5>
            <?php } ?>
			<p>
				<span class="mand">*</span> <?php echo JText::_('LNG_MANDATORY_FIELDS');?>
			</p>
			<?php 
			if($this->appSettings->save_all_guests_data)
			{
			?>
				<div>
					<h4><?php echo JText::_('LNG_GUEST_EXTRA_INFO');?></h4>
				</div>
				<div class="">
					<?php foreach($this->userData->guestDetails as $idx=>$guestDetail){?>
							<div class="">
								<div class="col column_2_of_12">
									<?php echo " #".($idx+1)." ".JText::_('LNG_GUEST_DETAILS');?>
									<span class="mand">*</span>
								</div>
							
								<div class="col column_2_of_12" align=left>
									<input class="validate[required] input_Guest_Details"
										type 			= 'text'
										name			= 'guest_first_name[]'
										id				= 'guest_first_name'
										size			= 12
										placeholder    = '<?php echo JText::_('LNG_FIRST_NAME');?>'
										value			= "<?php echo $guestDetail->first_name?>">
								</div>
									
								<div class="col column_2_of_12">
									<input  class="validate[required] input_Guest_Details"
										type 			= 'text'
										name			= 'guest_last_name[]'
										id				= 'guest_last_name'
										size			= 12
										placeholder    = '<?php echo JText::_('LNG_LAST_NAME');?>'
										value			= "<?php echo $guestDetail->last_name?>"
										>
								</div>
							
								<div class="col column_2_of_12">
                                    <input class="input_Guest_Details"
										type 			= 'text'
										name			= 'guest_identification_number[]'
										id				= 'guest_identification_number'
										placeholder    = '<?php echo JText::_('LNG_PASSPORT_NATIONAL_ID');?>'
										size			= 12
										value			= "<?php echo $guestDetail->identification_number?>">
								</div>
							</div>
						<?php } ?>
				</div>
			<?php 		
			} 
			?>
			<div style="clear:left">
            <?php
            //get & render the guest details fields
            echo $this->guestDetailsFields; ?>
		</div>
		</div>
		<div class="clear"></div>
		<div class='div-buttons'>
			<table class="table_With_Width" align=center>
				<tr>
					<td align=left>
						<button class="ui-hotel-button ui-hotel-button grey" value="checkRates" name="checkRates" type="button" onclick="formBack()">
							<span class="ui-button-text">
								<?php echo JText::_('LNG_BACK')?>
							</span>
                        </button>
					</td>
					<td class="right">
						<button class="ui-hotel-button" name="checkRates" type="button" id="Button" onclick="checkContinue()">
							<span class="ui-button-text">
								<?php echo JText::_('LNG_CONTINUE');?>
							</span>
							<i class="fa fa-arrow-circle-right" alt=""></i> 
						</button>
					</td>
				</tr>
			</table>
		</div>
		</div>
    </div>
	<input type="hidden" name="task" id="task" value="guestdetails.addGuestDetails" />
	<input type="hidden" name="hotel_id" id="hotel_id" 	value="<?php echo $this->hotel->hotel_id?>"/>
	<input type="hidden" name="reservedItems" id="reservedItems" value="<?php echo JRequest::getVar("reservedItems") ?>" />
	<input type="hidden" name="current" id="current" value="<?php echo  count($this->userData->reservedItems); ?>" />

	<script>
		function formBack() 
		{
			var form 	= document.forms['userForm'];
			form.task.value	="extraoptions.back1";
			form.submit();
		}

		function showTerms() {
			jQuery.blockUI({
				message: jQuery('#conditions'), css: {
					top: 50 + 'px',
					left: (jQuery(window).width() - 800) / 2 + 'px',
					width: '800px',
					backgroundColor: '#fff'
				}
			});
			jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
		}

		function checkContinue() {
            jQuery('#userForm').validationEngine('attach');
            if(jQuery('#userForm').validationEngine('validate')){
                jQuery("#userForm").submit();
                return true;
            }else{
                return false;
            }
		}

        function checkUser(){
            var form 	= document.forms['userForm'];
            form.task.value	="guestdetails.getPastReservationData";
            form.submit();
        }

	</script>
</form>
</div>

<div id="conditions" class="terms-conditions" style="display:none">
<div id="dialog-container">
<div class="titleBar">
<span class="dialogTitle" id="dialogTitle"></span>
<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
<span title="Cancel" class="closeText">x</span>
</span>
</div>

<div class="dialogContent">
<h3 class="title"> <?php echo JText::_('LNG_TERMS_AND_CONDITIONS',true);?></h3>
<div class="dialogContentBody" id="dialogContentBody">
	<?php echo $this->appSettings->terms_and_conditions?>
</div>
</div>
</div>
	
</div>
<script>
    window.onload = checkGoogleObj();
</script>