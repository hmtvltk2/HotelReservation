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
			<fieldset class="form-horizontal">
					<legend><?php echo JText::_('LNG_SEO_SETTINGS'); ?></legend>
					<TABLE class='admintable'  width=100%>

						<TR>
							<td align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_SEO',true)?> :</TD>
							<TD nowrap colspan=2 id="enable_seo" class="radio btn-group btn-group-yesno">
								<input
									type		= "radio"
									name		= "enable_seo"
									id			= "enable_seo0"
									value		= '1'
									<?php echo $this->item->enable_seo==true? " checked " :""?>
									accesskey	= "Y"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
								/>
								<label
									class="labelYes"
									id="label_enable_seo0"
									for="enable_seo0"><?php echo JText::_('LNG_YES'); ?></label>
								&nbsp;
								<input
									type		= "radio"
									name		= "enable_seo"
									id			= "enable_seo1"
									value		= '0'
									<?php echo $this->item->enable_seo==false? " checked " :""?>
									accesskey	= "N"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
								/>
								<label
									class="labelNo"
									id="label_enable_seo1"
									for="enable_seo1"><?php echo JText::_('LNG_NO'); ?></label>
							</TD>
						</TR>
						<tr>
							<td align="left" class="key" >	
								<?php echo JText::_('LNG_MENU_ITEM_ID'); ?>:
							</td>
							<td align="left" nowrap>
								<input type='text' size=28 maxlength=7  id='menu_id' name = 'menu_id' value='<?php echo $this->item->menu_id?>'>
							</td> 
						</tr>
						
				</table>
		</fieldset>