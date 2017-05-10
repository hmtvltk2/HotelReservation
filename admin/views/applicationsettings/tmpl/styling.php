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
			<legend><?php echo JText::_( 'LNG_FRONTEND_STYLING' ,true); ?></legend>
			<TABLE class='admintable'  width=100%>
				<tr>
					<TD nowrap class="key" ><?php echo JText::_('LNG_HOTEL_COMPONENT_STYLE',true); ?> :</TD>
					<TD nowrap colspan=2 >
						<select
							id		= 'css_style'
							name	= 'css_style'>
							<?php
							for($i = 0; $i <  count( $this->item->css_styles ); $i++)
							{
								$css_style = basename($this->item->css_styles[$i]); 
							?>
							<option
								value = '<?php echo $css_style?>' 
								<?php echo $css_style == $this->item->css_style ? "selected" : ""?>
							> 
								<?php echo $css_style?>
							</option>
							<?php
							}
							?>
							
						</select>
					</td>
				</tr>
				<tr>
					<td align="left" class="key" nowrap ><?php echo JText::_('LNG_ROOM_VIEW_TYPE',true)?> :</td>
					<td>
						<div class="controls">
							<fieldset id="category_view_fld" class="radio btn-group btn-group-yesno">
								<input type="radio" class="validate[required]" name="room_view" id="room_view" value="0" <?php echo $this->item->room_view==0? 'checked="checked"' :""?> />
								<label class="labelYes_1 btn" for="room_view" style="margin-right: 0 !important;"><?php echo JText::_('LNG_ROOM_VIEW_1')?></label>
								<input type="radio" class="validate[required]" name="room_view" id="room_view1" value="1" <?php echo $this->item->room_view==1? 'checked="checked"' :""?> />
								<label class="labelNo btn" for="room_view1"><?php echo JText::_('LNG_ROOM_VIEW_2')?></label>
                                <input type="radio" class="validate[required]" name="room_view" id="room_view2" value="2" <?php echo $this->item->room_view==2? 'checked="checked"' :""?>/>
                                <label class="labelYes_1 btn" for="room_view2"><?php echo JText::_('LNG_ROOM_VIEW_3')?></label>
							</fieldset>
						</div>
					</td>
				</tr>
			</table>
	</fieldset>
