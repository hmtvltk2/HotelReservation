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
$need_all_fields = true;
$currency = JHotelUtil::getCurrencyDisplay($this->userData->currency,null,null);

?>
<div id="hotel_reservation">
<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationsteps.php'; ?>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=paymentoptions'.JHotelUtil::getItemIdS()) ?>" method="post" name="userForm" id="userForm">
	<input type="hidden" name="task" 	 id="task" 		value="paymentoptions.processPayment" />
	<input type="hidden" name="hotel_id" id="hotel_id"	value="<?php echo $this->hotel->hotel_id?>" />
	<input type="hidden" name="reservedItems" id="reservedItems" value="<?php echo  implode($this->userData->reservedItems); ?>" />
	<input name="processor_id"  id="processor_id" type="hidden" value="">
	
	<div class="hotel_reservation">
		<div class="hoteInnerContainer">

			<table width="100%" cellspacing="0" class="left" >
				<TR>
					<TD valign=top colspan=1>
						<?php echo $this->reservationDetails->reservationInfo?>
					</TD>
				</TR>
				<TR>
					<TD valign=top colspan=1>
						&nbsp;
					</TD>
				</TR>
				<TR>
					<TD align=left colspan=1>
						<?php echo JText::_('LNG_OVERVIEW_RESERVATION_INFO');?> 
					</TD>
				</TR>
				<?php if($this->appSettings->enable_discounts &&  $this->reservationDetails->showDiscounts || !empty($this->userData->discount_code ) ){?>
				<TR>
					<TD valign=top colspan=1>
						<fieldset class="dicount-code-block">
					  		  <h3><?php echo JText::_('LNG_DISCOUNT_CODE');?></h3>
					    	<div class="div_large_margin">
					    		
					            <label for="coupon_code"><?php echo JText::_('LNG_DISCOUNT_TXT');?></label>
					          	<input type="text" size="40" value="<?php echo $this->userData->discount_code ?>" name="discount_code" id="discount_code" class="input-text noSubmit"> &nbsp;
					          	<button class="ui-hotel-button grey" name="checkRates" type="submit" onclick="applyDiscountCode();">
									<i class="fa fa-check green"></i>
									<span class="ui-button-text">
										<?php echo JText::_('LNG_APPLY');?>							
									</span>
								</button>
					        </div>
					    </fieldset>
					</TD>
				</TR>
				<?php } ?>
				<TR>
					<TD align=left colspan=1>	
						<?php 
							echo TaxService::getCityTaxInfo($this->hotel->informations,$currency);
						?> 
					</TD>
				</TR>
				<?php
					$parkingInfo = HotelService::getHotelParkingInfoStatus($this->hotel,$currency);
					echo $parkingInfo;

				?>

				<?php 
					$isSuperUser = isSuperUser(JFactory::getUser()->id);
					$showPaymentOption = false;
					if(($isSuperUser && SHOW_PAYMENT_ADMIN_ONLY==1) || SHOW_PAYMENT_ADMIN_ONLY==0)
						$showPaymentOption = true;
				?>
				<?php if($this->appSettings->is_enable_payment){ ?>
				<TR>
					<TD align=left colspan=1>
						<div id="payment-errors" class="red"></div>
					</TD>
				</TR>
				<tr style="display:<?php echo $showPaymentOption?"block":"none" ?>" >
					<td colspan="10">
						<strong><?php echo JText::_("LNG_PAYMENT_METHODS");?></strong>
						<dl class="sp-methods" id="checkout-payment-method-load">
							<?php
								if(count($this->paymentMethods)==0 && $this->appSettings->is_enable_payment){
									echo "<label class='red'>".JText::_('LNG_PAYMENT_PROCESSORS_DESC')."</span>";
								}
									
							    foreach ($this->paymentMethods as $method){
							?>
							    <dt>
							    	<div class="styledRadio">
							        	<input id="p_method_<?php echo $method->type ?>" value="<?php echo $method->type ?>" type="radio" name="payment_method" title="<?php echo $method->name ?>" onclick="switchMethod('<?php echo $method->type ?>','<?php echo $method->id?>')"<?php if($this->state->get("payment.payment_method")==$method->type): ?> checked="checked"<?php endif; ?> class="validate[required] radio" />
										<label for="p_method_<?php echo $method->type ?>"></label>							        	
							        </div>
								    <img class="payment-icon" src="<?php echo JURI::base() ."components/".getBookingExtName().'/assets/img/payment/'.strtolower($method->type).'.gif' ?>"  />
							        <label for="p_method_<?php echo $method->type ?>"><?php echo $method->name ?> </label>
							    </dt>
							<?php if ($html = $method->getPaymentProcessorHtml()){ ?>
								<dd>
									<?php echo $html; ?>
								</dd>
							<?php } ?>
						<?php } ?>
						</dl>
					</td>
				</tr>
				<?php } ?>	
				<TR >
					<TD valign=top align=left>
						<BR>
						<div class='div_reservation_policies_title tr_with_dspl_none'><?php echo JText::_('LNG_RESERVATION_POLICIES');?></div>
						<div class='div_reservation_policies_info tr_with_dspl_none'>
							<?php echo JText::_('LNG_RESERVATION_POLICIES_DETAILS');?>
						</div>
						<div>
							<div class="styledCheckbox">
							<input 
								type 		='checkbox'
								id			= 'is_accept_policies'
								name		= 'is_accept_policies'
								class		= 'validate[required]'
							>&nbsp;
								<label for="is_accept_policies"></label>
							</div> 
							
							<a href="javascript:void(0);" id="linkShowHide" onclick="showTerms('conditions')"><?php echo JText::_('LNG_AGREE_WITH_TERMS')?></a>
		
							
	                        <div id="conditions" class="terms-conditions tr_with_dspl_none">
	                            <div id="dialog-container">
	                                <div style="margin-bottom: -75px !important;padding-bottom: 39px">
	                                    <h3 class="title"> <?php echo JText::_('LNG_TERMS_AND_CONDITIONS');?></h3>
	                                </div>
	                                    <div class="dialogContent">
	
	                                    <div class="dialogContentBody" id="dialogContentBody">
	                                        <div>
	                                        <?php
	                                        $termsandConditionsContent = $this->hoteltranslationsModel->getObjectTranslation(TERMS_AND_CONDITIONS_TRANSLATION, $this->appSettings->applicationsettings_id, JRequest::getVar('_lang'));
	                                        echo isset($termsandConditionsContent) ? $termsandConditionsContent->content : "" ; ?>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
						</div>
					</TD>
				</TR>
			</table>
		</div>	
		<div class="clearfix"></div>
		
		<div CLASS='div-buttons'>
			<table width='100%' align=center>
				<tr>
					<td align=left>
						<button class="ui-hotel-button ui-hotel-button grey" value="checkRates" name="checkRates" type="button" onclick="formBack()">
							<span class="ui-button-text"><?php echo JText::_('LNG_BACK')?></span></button>
						</button>
					</td>
					<td class="right">
						<button class="ui-hotel-button " name="checkRates" type="submit" onclick="return checkContinue();">
							<i class="fa fa-check"></i>
							<span class="ui-button-text">
								<?php echo JText::_('LNG_MAKE_RESERVATION');?>
							</span>
						</button>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<script>
		jQuery(document).ready(function(){
			jQuery(function(){
				jQuery("input.noSubmit").keypress(function(e){
			         var k=e.keyCode || e.which;
			         if(k==13){
			             e.preventDefault();
			         }
			     });
			 });
		});
		
		function checkContinue()
		{
			var is_ok	= false;
			var form 	= document.forms['userForm'];

			jQuery('#userForm').validationEngine('attach');				
			if(!jQuery('#userForm').validationEngine('validate'))
				return false;
			
			return true;
		}

		function applyDiscountCode(){
			jQuery("#task").val("paymentoptions.applyDiscount");
			jQuery("#userForm").submit();
		}
		
		
		
		function formBack() 
		{
			var form 	= document.forms['userForm'];
			form.task.value	="paymentoptions.back2";
			form.submit();
		}

		function showTerms(divId)
        {
            jQuery('#' + divId).toggle();
            return false;
        }

		function switchMethod(method,processorId){
			jQuery("#checkout-payment-method-load ul").each(function(){
				jQuery(this).hide();
			});
			jQuery('#userForm').validationEngine('hide');				
			jQuery('#userForm').validationEngine('detach');		
			jQuery('#processor_id').val(processorId);
			//console.debug(method);
			jQuery("#payment_form_"+method).show();
		}

		var addText = '<?php echo JText::_('LNG_ADD')?> ';

		jQuery('#discount_code').selectize({
			plugins: ['remove_button','restore_on_backspace'],
			delimiter: ',',
			persist: false,
			render: {
				option_create: function(data, escape) {
					return '<div class="create span6">' + addText + '<strong>' + escape(data.input) + '</strong>&hellip;</div>';
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
</form>
</div>



