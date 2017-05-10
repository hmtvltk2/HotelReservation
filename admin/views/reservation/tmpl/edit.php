<?php
/**
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
$app = JFactory::getApplication();
$dirs = JHotelUtil::languageTabs();
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'reservation.cancel' || validateForm())
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	};

	function validateForm(){
		//validate rooms
		if(jQuery(".roomrate").length==0){
			alert("Please add at least one room")
			return false;
		}
	
		var admin = jQuery('#adminForm');
		admin.validationEngine('attach');
		return admin.validationEngine('validate')?true:false;
	}



	function changeOptionState(id){
		jQuery("#"+id).toggle();
		
	}
	function upateExtraOption(){
		//just to be 
	}

	var dateFormat = '<?php echo $this->appSettings->dateFormat; ?>';
	var language = '<?php echo JHotelUtil::getJoomlaLanguage();?>';
	var formatToDisplay = calendarFormat(dateFormat);
</script>

<form name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reservation&layout=edit'); ?>" method="post"  class="form-horizontal">
	
	<?php if($this->state->get("reservation.id")==0){?>
		<div style='text-align:left'>
			<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
			
			 <select name="hotel_id" id ="hotel_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
					<?php echo JHtml::_('select.options', $this->hotels, 'hotel_id', 'hotel_name', $this->state->get('reservation.hotel_id'));?>
			</select>
			
			<hr>
		</div>
	<?php } ?>
	<?php
	if( $this->state->get('reservation.hotel_id') > 0 || $this->state->get("reservation.id") )
	{
	?>
	
	<fieldset class="adminform reservation reservation-box">
		<legend><?php echo JText::_('LNG_RESERVATION_DETAILS'); ?></legend>
		<TABLE class="admintable">
			<tr>
				<td width=10% nowrap class="key"><?php echo JText::_('LNG_ARIVAL'); ?> </td>
				<td class="reservation-details" id="reservationCalendar">
					<?php 
						if(!$this->state->get("reservation.id")){
							$startDate = JHotelUtil::convertToFormat($this->item->reservationData->userData->start_date);
                            ?>
                    <div  class="calendarDisplay">
                    <input class="form-control"
                                   id="start_date"
                                   data-provide="datepicker"
                                   name="start_date"
                                   value ="<?php echo  $startDate==$this->appSettings->defaultDateValue?'': $startDate;?>"
                                   type="text">

                            <button type="button" class="btn" id="start_date_img"><i class="icon-calendar"></i></button>
                        </div>
                            <?php
						}else{
							echo JHotelUtil::getDateGeneralFormat($this->item->reservationData->userData->start_date);
						?>
							<a id="showChangeDates" href="javascript:showChangeDates();"><?php echo JText::_('LNG_CHANGE_DATES')?></a>
							<input type="hidden" name="start_date" id="start_date" value="<?php echo $this->item->reservationData->userData->start_date ?>" />
							
						<?php }?>
				</td>
			</tr>
					
			<tr>
				<td width=10% nowrap class="key"><?php echo JText::_('LNG_DEPARTURE'); ?> </td>
				<td class="reservation-details" id="reservationCalendar">
					<?php 
						if(!$this->state->get("reservation.id")){
							$endDate = JHotelUtil::convertToFormat($this->item->reservationData->userData->end_date);
                            ?>
                    <div  class="calendarDisplay">
                    <input class="form-control"
                                   id="end_date"
                                   data-provide="datepicker"
                                   name="end_date"
                                   value ="<?php echo $endDate==$this->appSettings->defaultDateValue?'': $endDate; ?>"
                                   type="text">
                            <button type="button" class="btn" id="end_date_img"><i class="icon-calendar"></i></button>
                        </div>
                            <?php
						}else {
							echo JHotelUtil::getDateGeneralFormat($this->item->reservationData->userData->end_date);
						?>
							<input type="hidden" name="end_date" id="end_date" value="<?php echo $this->item->reservationData->userData->end_date ?>" />
						
						<?php }?>
				</td>
			</tr>
			<tr>
				<td width=10% nowrap class="key"><?php echo JText::_('LNG_ARRIVAL_TIME'); ?> </td>
				<td>
					<?php

					$this->item->reservationData->userData->arrival_time = $this->item->reservationData->userData->arrival_time ==""?$this->item->reservationData->hotel->informations->check_in:$this->item->reservationData->userData->arrival_time;
					?>
					<select name="arrival_time">
						<?php for($i=0;$i<24;$i++) {
							$j= $i.":00";	
							?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->reservationData->userData->arrival_time)==0?'selected="selected"':''?>><?php echo $j?></option>

							<?php $j= $i.":30";	?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->reservationData->userData->arrival_time)==0?'selected="selected"':''?>><?php echo $j?></option>

						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_REMARKS'); ?></td>
				<td><textarea name='remarks' id='remarks' rows="3" cols="25" ><?php echo $this->item->reservationData->userData->remarks?></textarea></td>
				<TD>&nbsp;</TD>
			</tr>
			<?php if ($app->isAdmin()) {?>	
			<tr>
				<td class="key"><?php echo JText::_('LNG_AMDIN_REMARKS'); ?></td>
				<td><textarea name='remarks_admin' id='remarks_admin' rows="3" cols="25" ><?php echo $this->item->reservationData->userData->remarks_admin?></textarea></td>
				<TD>&nbsp;</TD>
			</tr>
			<?php }?>	
		
			<tr>
				<td class="key"><?php echo JText::_('LNG_VOUCHER'); ?></td>
				<td><input type="text" name="voucher" id="voucher" size="50" value="<?php echo $this->item->reservationData->userData->voucher ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_DISCOUNT_CODE'); ?></td>
				<td>
					<input type="text" class="input-text discount" name="discount_code" id="discount_code" size="50" value="<?php echo $this->item->reservationData->userData->discount_code ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_ID'); ?></td>
				<td><?php echo $this->item->reservationData->userData->confirmation_id ?></td>
				<TD>&nbsp;</TD>
			</tr>	
		</TABLE>
	</fieldset>
	<fieldset class="adminform reservation reservation-box">
		<legend><?php echo JText::_('LNG_EDIT_GUEST_DETAILS'); ?></legend>
		<TABLE class="admintable">
			<tr>
				<td class="key">
					<?php echo JText::_('LNG_GENDER_TYPE');?> <span class="mand">*</span>
				</td>
				<td id="guest_type" class="radio btn-group btn-group-yesno gender-type">
					<?php 
						echo JHtml::_( 'select.radiolist', $this->guestTypes, 'guest_type','', 'value', 'text',  $this->item->reservationData->userData->guest_type,'guest_type');
					?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_FIRST_NAME'); ?></td>
				<td><input type="text" name="first_name" id="first_name" size="50" value="<?php echo $this->item->reservationData->userData->first_name ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_LAST_NAME'); ?></td>
				<td><input type="text" name="last_name" id="last_name" size="50" value="<?php echo $this->item->reservationData->userData->last_name ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_BILLING_ADDRESS'); ?></td>
				<td><input type="text" name="address" id="address" size="50" value="<?php echo $this->item->reservationData->userData->address ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_POSTAL_CODE'); ?></td>
				<td><input type="text" name="postal_code" id="postal_code" size="50" value="<?php echo $this->item->reservationData->userData->postal_code ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_CITY'); ?></td>
				<td><input type="text" name="city" id="city" size="50" value="<?php echo $this->item->reservationData->userData->city ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_STATE'); ?></td>
				<td><input type="text" name="state_name" id="state_name" size="50" value="<?php echo $this->item->reservationData->userData->state_name ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_COUNTRY'); ?></td>
				<td><input type="text" name="country" id="country" size="50" value="<?php echo $this->item->reservationData->userData->country ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_TELEPHONE_NUMBER'); ?></td>
				<td><input type="text" name="phone" class="validate[custom[phone]]" id="phone" size="50" value="<?php echo $this->item->reservationData->userData->phone ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_EMAIL'); ?></td>
				<td><input type="text" name="email" class="validate[custom[email]]" id="email" size="50" value="<?php echo $this->item->reservationData->userData->email ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_COMPANY_NAME'); ?></td>
				<td><input type="text" name="company_name" id="company_name" size="50" value="<?php echo $this->item->reservationData->userData->company_name ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
		</TABLE>
		
		<table class="admintable">
			<?php if(isset($this->item->reservationData->userData->guestDetails)){ ?>
				<?php foreach($this->item->reservationData->userData->guestDetails as $guestDetail){?>
					<tr>
						<TD  align=left>
						 <label for="first_name"><?php echo JText::_('LNG_FIRST_NAME');?></label> <span class="mand">*</span><br/>
							<input class="req-field" 
								type 			= 'text'
								name			= 'guest_first_name[]'
								id				= 'guest_first_name'
								size			= 25
								value			= "<?php echo $guestDetail->first_name?>">
						</TD>	
						<td>
							<label for="guest_last_name"><?php echo JText::_('LNG_LAST_NAME');?></label> <span class="mand">*</span><br/>
							<input  class="req-field"
								type 			= 'text'
								name			= 'guest_last_name[]'
								id				= 'guest_last_name'
								size			= 25
								value			= "<?php echo $guestDetail->last_name?>">
						</td>
					
						<td><label for="guest_identification_number"><?php echo JText::_('LNG_PASSPORT_NATIONAL_ID');?></label><BR/>
							<input class=""
								type 			= 'text'
								name			= 'guest_identification_number[]'
								id				= 'guest_identification_number'
								size			= 25
								value			= "<?php echo $guestDetail->identification_number?>">
						</td>
					</tr>
				<?php }?>
			<?php } ?>
		</table>
	</fieldset>
	<fieldset class="adminform reservation left" id="reservation-rooms">
		<legend><?php echo JText::_('LNG_ADD_ROOM'); ?></legend>

		<?php if(count($this->roomTypes) > 0 ) {?>
		<dl>
			<dt><?php echo JText::_('LNG_ROOM',true)?>:</dt> 
			<dd>
             	<select name="rooms" id="rooms">
                	 <?php echo JHtml::_('select.options', $this->roomTypes, 'value', 'text', 0);?>
				</select>
			 </dd>	

			<dl>
			<dt><?php echo JText::_('LNG_ADULTS',true)?>:</dt> 
			<dd>
				<select name="adults" id="adults">
					<?php for($i=0; $i<=4;$i++){?>
						<option	value="<?php echo $i?>" <?php echo $i==2 ?'selected="selected"':''?>><?php echo $i?></option>
					<?php } ?>
				</select>
			</dd>
			
			<?php if($this->appSettings->show_children){?>
			<dt>
				<?php echo JText::_('LNG_CHILDREN',true)?>:
			</dt>
			<dd>
				<select name="children" id="children">
				 	<?php for($i=0; $i<=4;$i++){?>
						<option	value="<?php echo $i?>" <?php echo $i==0 ?'selected="selected"':''?> ><?php echo $i?></option>
					<?php } ?>
				</select>
			</dd> 
			<?php } ?>
		</dl>
		<div class="">
			<button id="btnAddRoom" onclick="addRoom(); return false;" class="ui-hotel-button">
				<span class="ui-button-text">
					<?php echo JText::_('LNG_ADDROOM',true)?>
				</span>
			</button>
		</div>
		<?php } else {
			?>
			<div class="noRooms">
			<?php
			echo "<label>".JText::_('LNG_NO_ROOMS_DEFINED') ."</label>";
			?>
			<a href='<?php echo JRoute::_('index.php?option=' . getBookingExtName() . '&view=rooms&hotel_id='.$this->state->get('reservation.hotel_id')) ?>'
			   target="_blank"
			   title="<?php echo JText::_('LNG_CLICK_TO_DEFINE_ROOMS', true) ?>">
				<b>
					<?php echo JText::_('LNG_CLICK_TO_DEFINE_ROOMS', true) ?>
				</b>
			</a>
			</div>
		<?php } ?>
     </fieldset>
     <fieldset class="adminform reservation left" id="reservation-rooms">
		<legend><?php echo JText::_('LNG_RESERVATION_ROOMS'); ?></legend>   
		<?php
		$isCustomPrice = false;

		if(isset($this->item->rooms)){
			$current= 1; 
			foreach ($this->item->rooms as $room){
				?>
				<div id="<?php echo $room->offer_id."-".$room->room_id."-".$room->current?>">

							<?php
							// to be absolutely sure that the room name gets translated in the language that it it loaded
							if(isset($this->room_name_translation)) {
								$room_name       = JHotelUtil::printTranslatedValues( $this->room_name_translation, $room->room_id, $room->room_name );
								$room->room_name = $room_name;
							}

							if($current>1)
								echo "<hr>";
							$buff = RoomService::getRoomHtmlContent($room, $this->item->reservationData->userData->start_date, $this->item->reservationData->userData->end_date,$this->appSettings->show_children);
							echo $buff;
							$extras = ExtraOptionsService::parseRervationExtraOptions($this->item->reservationData->userData->extraOptionIds);
							$extras = ExtraOptionsService::getHotelExtraOptions( $this->item->reservationData->userData->hotelId, $this->item->reservationData->userData->start_date, $this->item->reservationData->userData->end_date, $extras, $room->room_id, $room->offer_id, $onlySelected = true,$this->item->reservationData->userData->confirmation_id);

						?>
								<div class="extra-options">
										<h2><?php echo JText::_('LNG_EXTRA_OPTIONS',true)?></h2>
										<div>
											<div id="extra-options-container">
												<?php
													echo ExtraOptionsService::getExtraOptionsHTML($extras,$this->item->reservationData->userData->reservedItems,$this->item->reservationData->userData->currency->name,$room->current,$this->item->reservationData->userData->confirmation_id,$this->item->reservationData->userData->start_date, $this->item->reservationData->userData->end_date);
												?>
											</div>
										</div>
								</div>

					<?php
							$current++;

							?>

				</div>
				<?php
				}
				
			}
		?>
     </fieldset>
	
	<?php if (isset($this->item->paymentInformation)){?>
		<div class="clearfix"></div>
		<fieldset class="adminform reservation right clearfix" id="paymentDetails">
			<legend> 
					<?php echo JText::_('LNG_PAYMENT_DETAILS',true)?>
			</legend>
			<div>
				<?php echo $this->item->paymentInformation;?>
			</div>
		</fieldset>
	<?php }?>

	
	<input type="hidden" name="hotelId" id="hotelId" value="<?php echo $this->state->get('reservation.hotel_id'); ?>" />
	<input type="hidden" name="reservationId" value="<?php echo $this->item->reservationData->userData->confirmation_id ?>" />
	<input type="hidden" name="totalPaid" value="<?php echo $this->item->reservationData->userData->totalPaid  ?>" />
	<input type="hidden" name="update_price_type" id="update_price_type" value="<?php echo $isCustomPrice==true?"2":"";?>" />
	<input type="hidden" name="current" id="current" value="<?php echo isset($this->item->rooms)? count($this->item->rooms) +1 : 1 ?>" />
	
	
	<?php } ?>
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="option" value="<?php echo getBookingExtName() ?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	
</form>

<div id="change-dates" class="change-dates" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
			<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<h3 class="title"> <?php echo JText::_('LNG_CHANGE_DATES');?></h3>
			<div class="dialogContentBody" id="dialogContentBody">
				<table>
					<tr>
						<td width=10% nowrap class="key"><?php echo JText::_('LNG_ARIVAL'); ?> </td>
						<td class="reservation-details" id="reservationCalendar">
							<?php $startDate = JHotelUtil::convertToFormat($this->item->reservationData->userData->start_date)?>
                            <div  class="calendarDisplay">
                            <input class="form-control"
                                   id="start_date_i"
                                   data-provide="datepicker"
                                   name="start_date_i"
                                   style="display: inline !important;"
                                   value ="<?php echo  $startDate==$this->appSettings->defaultDateValue?'': $startDate;?>"
                                   type="text">

                            <button type="button" class="btn" style="margin-left: -40px !important;" id="start_date_img"><i class="icon-calendar"></i></button>
                            </div>
						</td>
					</tr>
					<tr>
						<td width=10% nowrap class="key"><?php echo JText::_('LNG_DEPARTURE'); ?> </td>
						<td class="reservation-details" id="reservationCalendar">
							<?php $endDate = JHotelUtil::convertToFormat($this->item->reservationData->userData->end_date)?>
                            <div  class="calendarDisplay">
                            <input class="form-control"
                                   id="end_date_i"
                                   data-provide="datepicker"
                                   name="end_date_i"
                                   style="display: inline !important;"
                                   value ="<?php echo $endDate ==$this->appSettings->defaultDateValue?'': $endDate;?>"
                                   type="text">
                            <button type="button" class="btn"  style="margin-left: -40px !important;" id="end_date_img"><i class="icon-calendar"></i></button>
                            </div>
						</td>
					</tr>
					<tr>
					<?php if ($app->isAdmin()) {?>					
					<tr>
						<td colspan="2">
							<?php echo JText::_('LNG_PRICE_CHOOSE') ?>?
							</br>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input id="price_type1" type="radio" value="1" name="price_type">
							<label id="price_type1-lbl" class="radiobtn" for="price_type1"><?php echo JText::_('LNG_RETRIEVE_DAY_PRICES') ?></label>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input id="price_type2" type="radio" value="2" name="price_type">
							<label id="price_type2-lbl" class="radiobtn" for="price_type2"><?php echo JText::_('LNG_APPLY_CURRENT_PRICES') ?></label>
						</td>
					</tr>
					<?php }	?>
					<tr>
						<td colspan="2">
							<button id="btnChangeDates" class="ui-hotel-button clearfix right" onclick="changeDates(); return false">
								<span class="ui-button-text">
									<?php echo JText::_('LNG_CHANGE_DATES');?>
								</span>
							</button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<script language="javascript" type="text/javascript">
		jQuery(document).ready(function() {
            jQuery.fn.datepicker.defaults.language = language;
            jQuery.fn.datepicker.defaults.format = formatToDisplay;

            var hotelId = jQuery('#hotel_id').val();
            var refreshScreen = jQuery('#refreshScreen').val();
            var nrHotels = jQuery('#hotel_id option').length;
            if (refreshScreen == "" && parseInt(nrHotels) == 2) {
                jQuery('#hotel_id :nth-child(2)').prop('selected', true);
                jQuery('#refreshScreen').val("true");
                jQuery("#hotel_id").trigger('change');
            }
        });
                jQuery("#start_date, #end_date,#start_date_i, #end_date_i").datepicker({
                    autoclose: true,
                    language: language,
                    format: formatToDisplay
                });
                jQuery("#start_date_img").click(function(){
                    jQuery("#start_date").focus();
                });
                jQuery("#end_date_img").click(function(){
                    jQuery("#end_date").focus();
                });

                jQuery("#start_date_img").click(function(){
                    jQuery("#start_date_i").focus();
                });
                jQuery("#end_date_img").click(function(){
                    jQuery("#end_date_i").focus();
                });



		var addText = '<?php echo JText::_('LNG_ADD')?> ';

		jQuery('#discount_code').selectize({
			plugins: ['remove_button','restore_on_backspace'],
			delimiter: ',',
			persist: false,
			render: {
				option_create: function(data, escape) {
					return '<div class="create">' + addText + '<strong>' + escape(data.input) + '</strong>&hellip;</div>';
				}
			},
			create: function(input) {
				return {
					value: input,
					text: input
				}
			}
		});
</script>

