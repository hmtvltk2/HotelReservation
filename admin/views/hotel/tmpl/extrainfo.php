<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="page-characteristics">
	<br style="font-size: 1px;" />
	<fieldset>
		<legend>
			
		<?php echo JText::_( 'HOTEL_COMMISION' ,true); ?></legend>
		<table class="admintable" cellspacing="1">
			<TR>
				<TD width=10%  class="key"><?php echo JText::_('LNG_HOTEL_NUMBER',true); ?>:</TD>
				<TD  align=left>
					<input type="text" name="hotel_number"
					id="hotel_number" 
					value='<?php echo isset($this->item->hotel_number) ? $this->item->hotel_number:''?>' 
					size=20
					maxlength=255 
					 /> 
				</TD>
			</TR>
			<TR>
				<TD width=10%  class="key"><?php echo JText::_('LNG_COMMISSION',true); ?>:</TD>
				<TD  align=left>
					<input type="text" 
					name="commission"
					id="commission" 
					value='<?php echo $this->item->commission?>' 
					size=10
					maxlength=255 
					class="validate[required,custom[integer]] text-input"
					 /> (%)
				</TD>
			</TR>
			<TR>
				<TD width=10%  class="key"><?php echo JText::_('LNG_RESERVATION_COSTS',true); ?>:</TD>
				<TD  align=left>
					<input 
						type		= "text"
						name		= "reservation_cost_val"
						id			= "reservation_cost_val"
						value		= '<?php echo $this->item->reservation_cost_val!=0? $this->item->reservation_cost_val :''?>'
						size		= 10
						maxlength	= 128
						class="validate[required,custom[number]] text-input"
					/>
				</TD>
			</TR>
			<TR>
				<TD width=10%  class="key"><?php echo JText::_('LNG_RESERVATION_CHARGE_PERCENT',true); ?>:</TD>
				<TD  align=left>
					<input 
						type		= "text"
						name		= "reservation_cost_proc"
						id			= "reservation_cost_proc"
						value		= '<?php echo $this->item->reservation_cost_proc!=0? $this->item->reservation_cost_proc : ''?>'
						size		= 10
						maxlength	= 128
						class="validate[required,custom[number]] text-input"
						
					/> (%)
				</TD>
			</TR>
			<TR>
				<TD width=10%  class="key"><?php echo JText::_('LNG_RECOMMENDED',true); ?>:</TD>
                <TD  align=left id="recommended" class="radio btn-group btn-group-yesno">

                    <input
                        type		= "radio"
                        name		= "recommended"
                        id			= "recommended0"
                        value		= '1'
                        <?php echo $this->elements->recommended==true? " checked " :""?>
                        accesskey	= "Y"

                        />
                    <label
                        class="labelYes"
                        id="label_recommended0"
                           for="recommended0"><?php echo JText::_('LNG_STR_YES',true); ?></label>
                    &nbsp;
                    <input
                        type		= "radio"
                        name		= "recommended"
                        id			= "recommended1"
                        value		= '0'
                        <?php echo $this->elements->recommended==false? " checked " :""?>
                        accesskey	= "N"
                        />
                    <label
                        class="labelNo"
                        id="label_recommended1"
                           for="recommended1"><?php echo JText::_('LNG_STR_NO',true); ?></label>
				</TD>
			</TR>
		</table>
	</fieldset>
</div>

