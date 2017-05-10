<?php 
/*------------------------------------------------------------------------
# JHotelReservation
# author CMSJunkie
# copyright Copyright (C) 2013 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/hotel_reservation/?p=1
# Technical Support:  Forum Multiple - http://www.cmsjunkie.com/forum/joomla-multiple-hotel-reservation/?p=1
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
$editor =JFactory::getEditor();

?>

			<fieldset>
			<legend ><?php echo JText::_( 'LNG_PRICE_SETUP' ,true); ?></legend>
			<TABLE class='admintable'  width=100%>
				<TR>
					<TD width=10%   class="key" ><?php echo JText::_('LNG_DISPLAY_PRICE',true); ?>:</TD>
					<td>
					<div class="controls">
						<fieldset id="category_view_fld" class="radio btn-group btn-group-yesno">
							<input type="radio" class="labelYes validate[required]" name="show_price_per_person" id="show_price_per_person" value="1" <?php echo $this->elements->show_price_per_person==1? 'checked="checked"' :""?> />
							<label class="labelYes_1 btn" style=" margin-right: 0 !important;" for="show_price_per_person"><?php echo JText::_('LNG_PER_PERSON')?></label>
							<input type="radio" class="validate[required]" name="show_price_per_person" id="show_price_per_person1" value="0" <?php echo $this->elements->show_price_per_person==0? 'checked="checked"' :""?> />
							<label class="labelNo btn" for="show_price_per_person1"><?php echo JText::_('LNG_PER_ROOM')?></label>
							<input type="radio" class="validate[required]" name="show_price_per_person" id="show_price_per_person2" value="2" <?php echo $this->elements->show_price_per_person==2? 'checked="checked"' :""?> />
							<label class="labelYes btn" id="totalPrice" for="show_price_per_person2"><?php echo JText::_('LNG_DISPLAY_WHOLE_PRICE')?></label>
						</fieldset>
					</div>
					</TD>
				</TR>
				<TR>
					<TD width=10%   class="key" ><?php echo JText::_('LNG_CHARGE_ONLY_RESERVATION_COST',true); ?>:</TD>
                    <td  id="charge_only_reservation_cost" class="radio btn-group btn-group-yesno">
                    	<br>
                        <input
                            type		= "radio"
                            name		= "charge_only_reservation_cost"
                            id			= "charge_only_reservation_cost0"
                            value		= '1'
                            <?php echo $this->elements->charge_only_reservation_cost==true? " checked " :""?>
                            accesskey	= "Y"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"

                            />

                        <label
                            class="labelYes"
                            id="label_charge_only_reservation_cost0"
                            for="charge_only_reservation_cost0"><?php echo JText::_('LNG_YES',true); ?></label>

                        &nbsp;
                        <input
                            type		= "radio"
                            name		= "charge_only_reservation_cost"
                            id			= "charge_only_reservation_cost1"
                            value		= '0'
                            <?php echo $this->elements->charge_only_reservation_cost==false? " checked " :""?>
                            accesskey	= "N"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"

                            />
                        <label
                            class="labelNo"
                            id="label_charge_only_reservation_cost1"
                            for="charge_only_reservation_cost1"><?php echo JText::_('LNG_NO',true); ?></label>
					</TD>
				</TR>
			</TABLE>
		</fieldset>
		
		<fieldset>
			<legend><?php echo JText::_( 'LNG_PAYMENT_METHODS_PAYFLOW_PRO_SETUP' ,true); ?></legend>
			<TABLE class='admintable'  width=100%>
				<TR>
					<TD width=10%   class="key" ><?php echo JText::_('LNG_ENABLE_PAYMENT',true); ?>:</TD>
					<td  id="is_enable_payment" class="radio btn-group btn-group-yesno">
						<input 
							type		= "radio"
							name		= "is_enable_payment"
							id			= "is_enable_payment0"
							value		= '1'
							<?php echo $this->item->is_enable_payment==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<label
                            class="labelYes"
                            id="label_is_enable_payment0"
                            for="is_enable_payment0"><?php echo JText::_('LNG_YES',true); ?></label>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_enable_payment"
							id			= "is_enable_payment1"
							value		= '0'
							<?php echo $this->item->is_enable_payment==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
                        <label
                            class="labelNo"
                            id="label_is_enable_payment1"
                            for="is_enable_payment1"><?php echo JText::_('LNG_NO',true); ?></label>
					</td>
					<TD>
						<?php echo JText::_('LNG_BY_SELECT_YES_YOU_CLIENTS_ARE_REQUIRED_TO_HAVE_A_CREDIT_CARD_IN_ORDER_TO_MAKE_RESERVATIONS',true)?>
					</TD>
				</TR>
			</TABLE>
		</fieldset>	
			<fieldset>
			<legend><?php echo JText::_( 'LNG_INVOICE_SETUP' ,true); ?></legend>
			<TABLE class='admintable'  width=100%>
				<tr>
					<td width=10%  class="key" ><?php echo JText::_('LNG_SEND_INVOICE_ONLY_TO_EMAIL',true); ?>:</td>
                    <td id="send_invoice_to_email" class="radio btn-group btn-group-yesno" colspan="2">
                        <input
                            type		= "radio"
                            name		= "send_invoice_to_email"
                            id			= "send_invoice_to_email0"
                            value		= '1'
                            <?php echo $this->elements->send_invoice_to_email==true? " checked " :""?>
                            accesskey	= "Y"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"
                            />

                        <label
                            class="labelYes"
                            id="label_send_invoice_to_email0"
                            for="send_invoice_to_email0"><?php echo JText::_('LNG_YES',true); ?></label>

                        &nbsp;
                        <input
                            type		= "radio"
                            name		= "send_invoice_to_email"
                            id			= "send_invoice_to_email1"
                            value		= '0'
                            <?php echo $this->elements->send_invoice_to_email==false? " checked " :""?>
                            accesskey	= "N"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"
                            />
                        <label
                            class="labelNo"
                            id="label_send_invoice_to_email1"
                            for="send_invoice_to_email1"><?php echo JText::_('LNG_NO',true); ?></label>
					</td>
                    <td id="tdBelowRadioButtons">
                        <input
                            type		= "email"
                            name		= "invoice_email"
                            id			= "invoice_email"
                            value		= "<?php echo $this->item->invoice_email; ?>"
                            />
                    </td>
                </tr>
			</TABLE>
		</fieldset>
	
		
	
