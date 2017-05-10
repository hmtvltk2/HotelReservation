<?php defined('_JEXEC') or die('Restricted access'); 

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
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.tooltip');

?>
<form action="<?php echo JRoute::_('index.php?option='.getBookingExtName().'&layout=edit&id=' . (int) $this->item->tax_id.'&hotel_id='.(int) $this->hotel_id); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset>
		<legend><?php echo JText::_('LNG_TAX_DETAILS',true); ?></legend>
		<center>
		<div style='text-align:left'>
			<strong>
				<?php echo JText::_('LNG_HOTEL',true)?> : 
				<?php
				foreach($this->hotel as $h){
					echo stripslashes($h->hotel_name);
					echo(strlen($h->country_name) > 0 ? ", " . $h->country_name : "");
					echo stripslashes(strlen($h->hotel_city) > 0 ? ", " . $h->hotel_city : "");
				}

				/*
                 * Checks if the tax is new and doesn't belong to a hotel
                 * Assign the hotel id from hotels to the hotel_id field of taxes
                 */
				if($this->item->tax_id == null && $this->item->hotel_id == null)
				{
					$this->item->hotel_id = $this->hotel_id;
				}
				?>
			</strong>
			<hr>
		</div>
		<TABLE class="admintable" align=center border=0>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_TYPE',true); ?> :</TD>
				<TD nowrap colspan=2 align=left>
					<select
						id 		= "tax_type"
						name	= "tax_type"
					>
						<option <?php echo $this->item->tax_type=='Fixed'? "selected" : ""?> value='Fixed'><?php echo JText::_('LNG_AMOUNT',true); ?></option>
						<option <?php echo $this->item->tax_type=='Percent'? "selected" : ""?> value='Percent'><?php echo JText::_('LNG_PERCENT',true); ?></option>
					</select>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_NAME',true); ?>:</TD>
				<TD nowrap width=1% align=left>
					<input 
						type		= "text"
						name		= "tax_name"
						class       ="validate[required] text-input"
						id			= "tax_name"
						value		= '<?php echo $this->item->tax_name?>'
						size		= 50
						maxlength	= 128
						
					/>
				</TD>
				<TD>&nbsp;</TD>
			</TR>
			
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_VALUE',true); ?> :</TD>
				<TD nowrap align=left>
					<input 
						type		= "text"
						name		= "tax_value"
						id			= "tax_value"
						value		= '<?php echo $this->item->tax_value?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:right'
					/>
					
				</TD>
				<TD align=left><?php echo JText::_( 'LNG_TAX_PRICE_PERCENT' ,true); ?></TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_TAX_APPLY_TO_EXTRAS',true); ?> :</TD>
		        <TD id="apply_to_extras" class="radio btn-group btn-group-yesno">
                        <input
                            type		= "radio"
                            name		= "apply_to_extras"
                            id			= "apply_to_extras0"
                            value		= '1'
                            <?php echo intVal($this->item->apply_to_extras)==1?" checked='checked' ":""?>
                            accesskey	= "Y"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"
                            />
                        
                       <label class="labelYes" id="label_apply_to_extras0" for="apply_to_extras0"><?php echo JText::_( 'LNG_YES' ,true); ?> </label>
                        &nbsp;
                        <input
                            type		= "radio"
                            name		= "apply_to_extras"
                            id			= "apply_to_extras1"
                            value		= '0'
                            <?php echo intVal($this->item->apply_to_extras)==0? " checked='checked' ":"" ?>
                            accesskey	= "N"
                            onmouseover	= "this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	= "this.style.cursor='default'"
                            />
                        <label
                            class="labelNo"
                               id="label_apply_to_extras1"
                               for="apply_to_extras1"><?php echo JText::_( 'LNG_NO' ,true); ?></label>
                    </TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_DESCRIPTION',true); ?> :</TD>
				<TD nowrap colspan=2 ALIGN=LEFT>
					<textarea id='tax_description' name='tax_description' rows=10 cols=70><?php echo $this->item->tax_description?></textarea>
				</TD>
			</TR>
		</TABLE>
	</fieldset>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="tax.edit" />
	<input type="hidden" name="tax_id" value="<?php echo $this->item->tax_id ?>" />
	<input type="hidden" name="hotel_id" value="<?php echo $this->item->hotel_id ?>" />
	<input type="hidden" name="controller" value="tax>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<script>
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'tax.save' || pressbutton == 'tax.apply') {

			jQuery("form[name='adminForm']").validationEngine('attach');
			if (!jQuery("form[name='adminForm']").validationEngine('validate')) {
				return false;
			}

			submitform( pressbutton );
			return;
		} else {
			jQuery('#adminForm').validationEngine('detach');
			submitform( pressbutton );
		}
	}
</script>
