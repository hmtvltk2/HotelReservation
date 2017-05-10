<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

?>
<input type="hidden" name="contactId" value="<?php echo $this->item->contact->id ?>" />
<fieldset>
	<legend><?php echo JText::_( 'LNG_MARKETING_SALES' ,true); ?></legend>
	<table class="admintable">
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_CONTACT'); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="mcontact" id="mcontact" class="input-text" size="10" value="<?php echo $this->item->contact->mcontact ?>">
			</TD>
		</TR>
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_PHONE',true); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="mphone" id="mphone" class="input-text" size="10" value="<?php echo $this->item->contact->mphone ?>">
			</TD>
		</TR>
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_EMAIL',true); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="memail" id="memail" class="input-text" size="10" value="<?php echo $this->item->contact->memail ?>">
			</TD>
		</TR>
	</table>
</fieldset>

<fieldset>
	<legend><?php echo JText::_( 'LNG_RESERVATIONS' ,true); ?></legend>
	<table class="admintable">
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_CONTACT'); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="rcontact" id="rcontact" class="input-text" size="10" value="<?php echo $this->item->contact->rcontact ?>">
			</TD>
		</TR>
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_PHONE',true); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="rphone" id="rphone" class="input-text" size="10" value="<?php echo $this->item->contact->rphone ?>">
			</TD>
		</TR>
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_EMAIL',true); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="remail" id="remail" class="input-text" size="10" value="<?php echo $this->item->contact->remail ?>">
			</TD>
		</TR>
        <TR>
            <TD  class="key"><?php echo JText::_('LNG_BOOKING_EMAIL',true); ?>:</TD>
            <TD  align=left>
                <input type="text" size="40"  name="booking_email" id="booking_email" class="input-text" size="10" value="<?php echo $this->item->contact->booking_email ?>">
            </TD>
        </TR>
        <TR>
            <TD  class="key"><?php echo JText::_('LNG_BOOKING_LIST_EMAIL',true); ?>:</TD>
            <TD  align=left>
                <input type="text" size="40"  name="booking_list_email" id="booking_list_email" class="input-text" size="10" value="<?php echo $this->item->contact->booking_list_email ?>">
            </TD>
        </TR>
	</table>
</fieldset>

<fieldset>
	<legend><?php echo JText::_( 'LNG_ADMINISTRATION' ,true); ?></legend>
	<table class="admintable">
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_CONTACT'); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="acontact" id="acontact" class="input-text" size="10" value="<?php echo $this->item->contact->acontact ?>">
			</TD>
		</TR>
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_PHONE',true); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="aphone" id="aphone" class="input-text" size="10" value="<?php echo $this->item->contact->aphone ?>">
			</TD>
		</TR>
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_EMAIL',true); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="aemail" id="aemail" class="input-text" size="10" value="<?php echo $this->item->contact->aemail ?>">
			</TD>
		</TR>
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_EMAIL_INVOICE',true); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="aemailinv" id="aemailinv" class="input-text" size="10" value="<?php echo $this->item->contact->aemailinv ?>">
			</TD>
		</TR>
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_VAT',true); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="vatno" id="vatno" class="input-text" size="10" value="<?php echo $this->item->contact->vatno ?>">
			</TD>
		</TR>
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_IBAN',true); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="iban" id="iban" class="input-text" size="10" value="<?php echo $this->item->contact->iban ?>">
			</TD>
		</TR>
		<TR>
			<TD  class="key"><?php echo JText::_('LNG_CHAMBER_OF_COMMERCE',true); ?>:</TD>
			<TD  align=left>
				<input type="text" size="40"  name="chamber_commerce" id="chamber_commerce" class="input-text" size="10" value="<?php echo $this->item->contact->chamber_commerce ?>">
			</TD>
		</TR>
	</table>
</fieldset>

