<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="mangeReservationInfo" class="manage-reservation">

	<form action="<?php echo JRoute::_('index.php') ?>" method="post" name="userForm_new"  class="form-validate">
		<fieldset style="border:1px solid #ccc;padding:10px;">
			<legend><?php echo JText::_('LNG_CUSTOMER_EDIT_RESERVATION_INFO',true); ?></legend>
	
			<dl class="hotel_reservation ">
				<dt>
					<label id="details-lbl" for="details" class="hasTip" title="<?php echo JText::_('LNG_FIRST_NAME',true);?>"><?php echo JText::_('LNG_FIRST_NAME',true);?></label>										
					<span class="mand">*</span>
				</dt>
				<dd>
					<input name='first_name' id='first_name' autocomplete	= 'off' size="50" value="<?php if(isset($this->row)) echo $this->row->first_name?>"/>
				</dd>
				<dt>
					<label id="details-lbl" for="details" class="hasTip" title="<?php echo JText::_('LNG_LAST_NAME',true);?>"><?php echo JText::_('LNG_LAST_NAME',true);?></label>										
					<span class="mand">*</span>
				</dt>
				<dd>
					<input name='last_name' id='last_name' autocomplete	= 'off' size="50" value="<?php if(isset($this->row)) echo $this->row->last_name?>"/>
				</dd>
				<dt>
					<label id="details-lbl" for="details" class="hasTip" title="<?php echo JText::_('LNG_BILLING_ADDRESS',true);?>"><?php echo JText::_('LNG_BILLING_ADDRESS',true);?></label>										
					<span class="mand">*</span>
				</dt>
				<dd>
					<input name='address' id='address' autocomplete	= 'off' size="50" value="<?php if(isset($this->row)) echo $this->row->address?>"/>
				</dd>
				<dt>
					<label id="details-lbl" for="details" class="hasTip" title="<?php echo JText::_('LNG_CITY',true);?>"><?php echo JText::_('LNG_CITY',true);?></label>										
					<span class="mand">*</span>
				</dt>
				<dd>
					<input name='city' id='city' autocomplete	= 'off' size="50" value="<?php if(isset($this->row)) echo $this->row->city?>"/>
				</dd>
				<dt>
					<label id="details-lbl" for="details" class="hasTip" title="<?php echo JText::_('LNG_STATE',true);?>"><?php echo JText::_('LNG_STATE',true);?></label>										
					<span class="mand">*</span>
				</dt>
				<dd>
					<input name='state_name' id='state_name' autocomplete	= 'off' size="50" value="<?php if(isset($this->row)) echo $this->row->state_name?>"/>
				</dd>
				<dt>
					<label id="details-lbl" for="details" class="hasTip" title="<?php echo JText::_('LNG_COUNTRY',true);?>"><?php echo JText::_('LNG_COUNTRY',true);?></label>										
					<span class="mand">*</span>
				</dt>
				<dd>
					<input name='country' id='country' autocomplete	= 'off' size="50" value="<?php if(isset($this->row)) echo $this->row->country?>"/>
				</dd>
				<dt>
					<label id="details-lbl" for="details" class="hasTip" title="<?php echo JText::_('LNG_POSTAL_CODE',true);?>"><?php echo JText::_('LNG_POSTAL_CODE',true);?></label>										
					<span class="mand">*</span>
				</dt>
				<dd>
					<input name='postal_code' id='postal_code' autocomplete	= 'off' size="50" value="<?php if(isset($this->row)) echo $this->row->postal_code?>"/>
				</dd>
				<dt>
					<label id="details-lbl" for="details" class="hasTip" title="<?php echo JText::_('LNG_TELEPHONE_NUMBER',true);?>"><?php echo JText::_('LNG_TELEPHONE_NUMBER',true);?></label>										
					<span class="mand">*</span>
				</dt>
				<dd>
					<input name='tel' id='tel' autocomplete	= 'off' size="50" value="<?php if(isset($this->row)) echo $this->row->tel?>"/>
				</dd>
				
				<dt>
					<label id="details-lbl" for="details" class="hasTip" title="<?php echo JText::_('LNG_EMAIL',true);?>"><?php echo JText::_('LNG_EMAIL',true);?></label>										
					<span class="mand">*</span>
				</dt>
				<dd>
					<input name='email' id='email' autocomplete	= 'off' size="50" value="<?php if(isset($this->row)) echo $this->row->email?>"/>
				</dd>
			</dl>
		</fieldset>
		<div>
			<button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT',true); ?></span></button>
			<?php echo JText::_('COM_JHOTELRESERVATION_OR',true); ?>
			<a href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL',true); ?>"><?php echo JText::_('JCANCEL',true); ?></a>
	
			<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
			<input type="hidden" name="task" value="saveReservationInfo" />
			<input type="hidden" name="controller" value="customeraccount" />
			<input type="hidden" name="view" value="customeraccount" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
