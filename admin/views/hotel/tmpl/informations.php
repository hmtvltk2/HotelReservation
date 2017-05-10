<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

$appSettings = JHotelUtil::getApplicationSettings();
$dirs = JHotelUtil::languageTabs();
?>
<input type="hidden" name="informationId" value="<?php echo $this->item->informations->id ?>" />
	<fieldset>
	<legend><?php echo JText::_( 'LNG_IMPORTANT_INFORMATION' ,true); ?></legend>
		<table class="admintable">
			<TR>
				<TD  class="key"><?php echo JText::_('LNG_CHECK_IN',true); ?>:</TD>
				<TD  align=left>
					<select name="check_in" class="chosenAttribute">
						<?php for($i=0;$i<24;$i++) {
							$j= $i.":00";	
							?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->informations->check_in)==0?'selected="selected"':''?>><?php echo $j?></option>
							<?php $j= $i.":30";	?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->informations->check_in)==0?'selected="selected"':''?>><?php echo $j?></option>
						<?php } ?>
					</select>
				</TD>
			</TR>
			<TR>
				<TD  class="key"><?php echo JText::_('LNG_CHECK_OUT',true); ?>:</TD>
				<TD  align=left>
					<select name="check_out" class="chosenAttribute">
						<?php for($i=0;$i<24;$i++) {
							$j= $i.":00";	
							?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->informations->check_out)==0?'selected="selected"':''?>><?php echo $j?></option>
							<?php $j= $i.":30";	?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->informations->check_out)==0?'selected="selected"':''?>><?php echo $j?></option>
						<?php } ?>
					</select>
				</TD>
			</TR>
			<TR>
				<TD  class="key"><?php echo JText::_('LNG_PARKING',true); ?>:</TD>
                <TD  align=left id="parking" class="radio btn-group btn-group-yesno">
                    <input
                        type		= "radio"
                        name		= "parking"
                        id			= "parking0"
                        value		= '1'
                        <?php echo $this->elements->parking==true? " checked " :""?>
                        accesskey	= "Y"

                        />
                    <label id="label_parking0"
                           class="labelYes"
                           for="parking0"><?php echo JText::_('LNG_STR_YES',true); ?></label>
                    &nbsp;
                    <input
                        type		= "radio"
                        name		= "parking"
                        id			= "parking1"
                        value		= '0'
                        <?php echo $this->elements->parking==false? " checked " :""?>
                        accesskey	= "N"
                        />
                    <label
                        class="labelNo"
                        id="label_parking1"
                           for="parking1"><?php echo JText::_('LNG_STR_NO',true); ?></label>
				</TD>
                <td id="tdBelowRadioButtons">
                    <div>
                        <?php echo JText::_('LNG_PRICE',true); ?> <input type="input" value="<?php echo $this->item->informations->price_parking?>"  name="price_parking" size="7"/>
                        &nbsp;
                        <?php echo JText::_('LNG_PERIOD',true); ?> &nbsp;
                        <input type="input" id="inputHotelview" value="<?php echo $this->item->informations->parking_period?>"  name=parking_period />
                    </div></td>
			</TR>
			<TR>
				<TD  class="key"><?php echo JText::_('LNG_PETS',true); ?>:</TD>
                <TD  align=left id="pets" class="radio btn-group btn-group-yesno">
                    <input
                        type		= "radio"
                        name		= "pets"
                        id			= "pets0"
                        value		= '1'
                        <?php echo $this->elements->allowPets==true? " checked " :""?>
                        accesskey	= "Y"

                        />
                    <label
                        class="labelYes"
                        id="label_pets0"
                           for="pets0"><?php echo JText::_('LNG_STR_YES',true); ?></label>
                    &nbsp;
                    <input
                        type		= "radio"
                        name		= "pets"
                        id			= "pets1"
                        value		= '0'
                        <?php echo $this->elements->allowPets==false? " checked " :""?>
                        accesskey	= "N"
                        />
                    <label
                        class="labelNo"
                        id="label_pets1"
                           for="pets1"><?php echo JText::_('LNG_STR_NO',true); ?></label>
				</TD>
                <td id="tdBelowRadioButtons">
                    <div>
                        <?php echo JText::_('LNG_PRICE',true); ?> <input type="input" value="<?php echo $this->item->informations->price_pets?>" name="price_pets" size="7"/>
                        <input type="input" placeholder="Pet info" id="inputHotelview1" value="<?php echo $this->item->informations->pet_info?>" name="pet_info" size="27"/>
                    </div>
                </td>
			</TR>
			
			<TR>
				<TD  class="key"><?php echo JText::_('LNG_CITY_TAX',true); ?>:</TD>
				<TD  align=left>
					<input type="text" class="validate[required,custom[number]] text-input" id="city_tax" name="city_tax" size="10" value="<?php echo $this->item->informations->city_tax ?>">
						<input  type="checkbox" name="city_tax_percent" value="1" <?php echo $this->item->informations->city_tax_percent == 1?'checked':'' ?>>(%)
				</TD>
				
				
			</TR>
			<TR>
				<TD  class="key"><?php echo JText::_('LNG_NUMBER_OF_ROOMS'); ?>:</TD>
				<TD align=left>
					<input type="text" name="number_of_rooms" id="number_of_rooms" class="validate[required,custom[integer],min[1]] input-text" size="10" value="<?php echo $this->item->informations->number_of_rooms ?>">
				</TD>
			</TR>
			<TR>
				<TD class="key"><?php echo JText::_('LNG_CANCELATION'); ?>:</TD>
				<td id="info"><?php echo JText::_('LNG_CANCELATION_DAYS'); ?>:
					<select id="cancellation_days" class="chosenAttribute" name="cancellation_days">
						<?php for($i=1;$i<100;$i++) {
								?>
							<option value="<?php echo $i?>" <?php echo $i==$this->item->informations->cancellation_days ?'selected="selected"':''?>><?php echo $i?></option>
						<?php } ?>
					</select>
					<?php echo JText::_('LNG_CANCELATION_DAYS_ALLOWED_BEFORE_ARRIVAL'); ?>
					<br/>
					<input type="checkbox" value="1" name="uvh_agree" id="uvh_agree" <?php echo $this->item->informations->uvh_agree == 1?'checked':''?>> <label for="uvh_agree"><?php echo JText::_('LNG_AGREE_WITH_UVH'); ?></label>
					<br/>


                    <?php
                    $j=0;

                    echo JHtml::_('tabs.start', 'tab_language_id', $options);

                    foreach( $dirs  as $_lng ){
                        $langName= JHotelUtil::languageNameTabs($_lng);

                        echo JHtml::_('tabs.panel',  $langName, 'tab'.$j);
                        $langContent = isset($this->cancellationCondition[$_lng])?$this->cancellationCondition[$_lng]:"";
                        ?>
                        <textarea class='inputbox' id='cancellation_conditions_<?php echo $_lng;?>' name='cancellation_conditions_<?php echo $_lng;?>' rows=6 cols=128> <?php echo $langContent?></textarea>

                    <?php
                    }
                    ?>

				</td>
			</TR>
			<TR>
				<TD  class="key"><?php echo JText::_('LNG_INTERNET_WIFI',true); ?>:</TD>
                <TD  align=left id="wifi" class="radio btn-group btn-group-yesno">
                    <input
                        type		= "radio"
                        name		= "wifi"
                        id			= "wifi0"
                        value		= '1'
                        <?php echo $this->elements->wifi==true? " checked " :""?>
                        accesskey	= "Y"

                        />
                    <label
                        class="labelYes"
                        id="label_wifi0"
                           for="wifi0"><?php echo JText::_('LNG_STR_YES',true); ?></label>
                    &nbsp;
                    <input
                        type		= "radio"
                        name		= "wifi"
                        id			= "wifi1"
                        value		= '0'
                        <?php echo $this->elements->wifi==false? " checked " :""?>
                        accesskey	= "N"
                        />
                    <label
                        class="labelNo"
                        id="label_wifi1"
                           for="wifi1"><?php echo JText::_('LNG_STR_NO',true); ?></label>

				</TD>
                <td id="tdBelowRadioButtons">
                    <div>
                        <?php echo JText::_('LNG_PRICE',true); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="input" value="<?php echo $this->item->informations->price_wifi?>" name="price_wifi" size="5" />
                        &nbsp;
                        <?php echo JText::_('LNG_PERIOD',true); ?>
                        &nbsp;
                        <input type="input" id="inputHotelview" value="<?php echo $this->item->informations->wifi_period?>" name="wifi_period" />
                    </div>
                </td>
			</TR>
			<TR>
				<TD  class="key"><?php echo JText::_('LNG_SUITABLE_FOR_DISABLED',true); ?>:</TD>
                <TD  align=left id="suitable_disabled" class="radio btn-group btn-group-yesno">

                    <input
                        type		= "radio"
                        name		= "suitable_disabled"
                        id			= "suitable_disabled0"
                        value		= '1'
                        <?php echo $this->elements->suitableDisabled==true? " checked " :""?>
                        accesskey	= "Y"

                        />
                    <label
                        class="labelYes"
                        id="label_suitable_disabled0"
                           for="suitable_disabled0"><?php echo JText::_('LNG_STR_YES',true); ?></label>
                    &nbsp;
                    <input
                        type		= "radio"
                        name		= "suitable_disabled"
                        id			= "suitable_disabled1"
                        value		= '0'
                        <?php echo $this->elements->suitableDisabled==false? " checked " :""?>
                        accesskey	= "N"
                        />
                    <label
                        class="labelNo"
                        id="label_suitable_disabled1"
                           for="suitable_disabled1"><?php echo JText::_('LNG_STR_NO',true); ?></label>
                </TD>
			</TR>
			<TR>
				<TD class="key"><?php echo JText::_('LNG_PUBLIC_TRANSPORTATION',true); ?>:</TD>
				<TD align=left id="public_transport" class="radio btn-group btn-group-yesno">

                    <input
                        type		= "radio"
                        name		= "public_transport"
                        id			= "public_transport0"
                        value		= '1'
                        <?php echo $this->elements->publicTransport==true? " checked " :""?>
                        accesskey	= "Y"

                        />
                    <label
                        class="labelYes"
                        id="label_public_transport0"
                           for="public_transport0"><?php echo JText::_('LNG_STR_YES',true); ?></label>
                    &nbsp;
                    <input
                        type		= "radio"
                        name		= "public_transport"
                        id			= "public_transport1"
                        value		= '0'
                        <?php echo $this->elements->publicTransport==false? " checked " :""?>
                        accesskey	= "N"
                        />
                    <label
                        class="labelNo"
                        id="label_public_transport1"
                           for="public_transport1"><?php echo JText::_('LNG_STR_NO',true); ?></label>
                </TD>
			</TR>
			<TR>
				<TD  class="key"><?php echo JText::_('LNG_HOTEL_PAYMENT_OPTIONS',true); ?>:</TD>
				<TD  align=left>
					<div id="paymentOption-holder" class="option-holder">
						<?php
							echo $this->paymentoptions->displayPaymentOptions( $this->item->paymentOptions, $this->item->selectedPaymentOptions );
						?>
					</div>
				<?php 
					if (checkUserAccess(JFactory::getUser()->id,"manage_options")){
				?>			
					<div class="manage-option-holder">
						<a href="javascript:" onclick="showManagePaymentOptions()"><?php  echo isset($this->item->hotel_id) ? JText::_('LNG_MANAGE_PAYMENT_OPTIONS',true):"" ?></a>
					</div>		
				<?php 
					}
				?>			
				</TD>
			</TR>
			<TR>
				<TD  class="key"><?php echo JText::_('LNG_CHILDREN_AGE_CATEGORY',true); ?>:</TD>
				<TD  id="info" align=left>
                    <?php
                    $j=0;

                    echo JHtml::_('tabs.start', 'tab_language_id', $options);

                    foreach( $dirs  as $_lng ){
                        $langName= JHotelUtil::languageNameTabs($_lng);

                        echo JHtml::_('tabs.panel',  $langName, 'tab'.$j);
                        $langContent = isset($this->childrenCategory[$_lng])?$this->childrenCategory[$_lng]:"";
						$langContent = empty($langContent)?$this->item->informations->children_category:$langContent; 
                        if (checkUserAccess(JFactory::getUser()->id,"children_category")) {
                            ?><textarea class='inputbox' id='children_category_<?php echo $_lng;?>' name='children_category_<?php echo $_lng;?>' rows=6 cols=128> <?php echo $langContent?></textarea>

                        <?php
                        }
                    }
                    ?>
				</TD>
			</TR>
		</table>
	</fieldset>

<div id="showPaymentOptionsNewFrm" style="display:none;">
  		<div id="popup_container">
    <!--Content area starts-->

    		<div class="head">
      		    <div class="head_inner">
               <h2> <?php echo JText::_('LNG_MANAGE_PAYMENT_OPTIONS',true); ?></h2>
               <a href="#" class="cancel_btn" onclick="closePopup();"><span class="cancel_icon">&nbsp;</span><?php echo JText::_('LNG_CANCEL',true); ?></a></div>
            </div>
            <div class="content">
                    <div class="descriptions" >

                       <div id="content_section_tab_data1">
                       	<span id="frm_error_msg_paymentOption" class="text_error" style="display: none;"></span> 
						<div class="row" id="paymentOption-container">
						</div>
						 
					 	<div class="option-row">
							<a href="javascript:" onclick="addNewPaymentOption(0,'')"><?php echo JText::_('LNG_ADD_NEW_PAYMENT_OPTION',true); ?></a>
						</div>
						<div class="proceed_row">
                           <!--button sec starts-->
                              <button name="btnSave" id="btnSave" onclick="savePaymentOptions(this.form);" type="submit" class="submit">    
                                     <span><span><?php echo JText::_('LNG_SAVE',true)?></span></span>
                              </button>
                              <input value="<?php echo JText::_('LNG_CANCEL',true); ?>" class="cancel" name="btnCancel" id="btnCancel" onclick="closePopup();" type="button">
                          </div>
                          <!--button sec ends-->
                        </div>
                        <div class="buttom_sec" id="frmPaymentOptionsFormSubmitWait" style="display: none;"> <span class="error_msg" style="background-image: none; color: rgb(0, 0, 0) ! important;">Please wait...</span> </div>
            </div>
          </div>
          </div>
     </div>        
