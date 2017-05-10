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
					<legend><?php echo JText::_('LNG_HOTEL_DISPLAY_SETTINGS'); ?></legend>
					<TABLE class='admintable'  width=100%>
						<TR>
							<td align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_HOTEL_TABS',true)?> :</TD>
							<TD nowrap colspan=2 id="enable_hotel_tabs" class="radio btn-group btn-group-yesno">
								<input 
									type		= "radio"
									name		= "enable_hotel_tabs"
									id			= "enable_hotel_tabs0"
									value		= '1'
									<?php echo $this->item->enable_hotel_tabs==true? " checked " :""?>
									accesskey	= "Y"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
		
									
								/>
								<label
		                            class="labelYes"
		                            id="label_enable_hotel_tabs0"
		                            for="enable_hotel_tabs0"><?php echo JText::_('LNG_YES'); ?></label>
								&nbsp;
								<input 
									type		= "radio"
									name		= "enable_hotel_tabs"
									id			= "enable_hotel_tabs1"
									value		= '0'
									<?php echo $this->item->enable_hotel_tabs==false? " checked " :""?>
									accesskey	= "N"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
		
								/>
								<label
		                            class="labelNo"
		                            id="label_enable_hotel_tabs1"
		                            for="enable_hotel_tabs1"><?php echo JText::_('LNG_NO'); ?></label>
							</TD>
						</TR>
						<TR>
							<td align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_HOTEL_RATING',true)?> :</TD>
							<TD nowrap colspan=2 id="enable_hotel_rating"  class="radio btn-group btn-group-yesno">
								<input 
									type		= "radio"
									name		= "enable_hotel_rating"
									id			= "enable_hotel_rating0"
									value		= '1'
									<?php echo $this->item->enable_hotel_rating==true? " checked " :""?>
									accesskey	= "Y"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
								/>
								<label
		                            class="labelYes"
		                            id="label_enable_hotel_rating0"
		                            for="enable_hotel_rating0"><?php echo JText::_('LNG_YES'); ?></label>
								&nbsp;
								<input 
									type		= "radio"
									name		= "enable_hotel_rating"
									id			= "enable_hotel_rating1"
									value		= '0'
									<?php echo $this->item->enable_hotel_rating==false? " checked " :""?>
									accesskey	= "N"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
								/>
								<label
		                            class="labelNo"
		                            id="label_enable_hotel_rating1"
		                            for="enable_hotel_rating1"><?php echo JText::_('LNG_NO'); ?></label>
							</TD>
						</TR>
						
						<TR>
							<td align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_HOTEL_DESCRIPTION',true)?> :</TD>
							<TD nowrap colspan=2 id="enable_hotel_description" class="radio btn-group btn-group-yesno">
								<input 
									type		= "radio"
									name		= "enable_hotel_description"
									id			= "enable_hotel_description0"
									value		= '1'
									<?php echo $this->item->enable_hotel_description==true? " checked " :""?>
									accesskey	= "Y"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
								/>
								<label
		                            class="labelYes"
		                            id="label_enable_hotel_description0"
		                            for="enable_hotel_description0"><?php echo JText::_('LNG_YES'); ?></label>
								&nbsp;
								<input 
									type		= "radio"
									name		= "enable_hotel_description"
									id			= "enable_hotel_description1"
									value		= '0'
									<?php echo $this->item->enable_hotel_description==false? " checked " :""?>
									accesskey	= "N"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
		
								/>
								<label
		                            class="labelNo"
		                            id="label_enable_hotel_description1"
		                            for="enable_hotel_description1"><?php echo JText::_('LNG_NO'); ?></label>
							</TD>
						</TR>
						<TR>
							<td align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_HOTEL_FACILITIES',true)?> :</TD>
							<TD nowrap colspan=2 id="enable_hotel_facilities" class="radio btn-group btn-group-yesno">
								<input 
									type		= "radio"
									name		= "enable_hotel_facilities"
									id			= "enable_hotel_facilities0"
									value		= '1'
									<?php echo $this->item->enable_hotel_facilities==true? " checked " :""?>
									accesskey	= "Y"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
		
									
								/>
								<label
		                            class="labelYes"
		                            id="label_enable_hotel_facilities0"
		                            for="enable_hotel_facilities0"><?php echo JText::_('LNG_YES'); ?></label>
								&nbsp;
								<input 
									type		= "radio"
									name		= "enable_hotel_facilities"
									id			= "enable_hotel_facilities1"
									value		= '0'
									<?php echo $this->item->enable_hotel_facilities==false? " checked " :""?>
									accesskey	= "N"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
		
								/>
								<label
		                            class="labelNo"
		                            id="label_enable_hotel_facilities1"
		                            for="enable_hotel_facilities1"><?php echo JText::_('LNG_NO'); ?></label>
							</TD>
						</TR>
						<TR>
							<td align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_HOTEL_INFO',true)?> :</TD>
							<TD nowrap colspan=2 id="enable_hotel_information" class="radio btn-group btn-group-yesno">
								<input 
									type		= "radio"
									name		= "enable_hotel_information"
									id			= "enable_hotel_information0"
									value		= '1'
									<?php echo $this->item->enable_hotel_information==true? " checked " :""?>
									accesskey	= "Y"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
		
									
								/>
								<label
		                            class="labelYes"
		                            id="label_enable_hotel_information0"
		                            for="enable_hotel_information0"><?php echo JText::_('LNG_YES'); ?></label>
								&nbsp;
								<input 
									type		= "radio"
									name		= "enable_hotel_information"
									id			= "enable_hotel_information1"
									value		= '0'
									<?php echo $this->item->enable_hotel_information==false? " checked " :""?>
									accesskey	= "N"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
		
								/>
								<label
		                            class="labelNo"
		                            id="label_enable_hotel_information1"
		                            for="enable_hotel_information1"><?php echo JText::_('LNG_NO'); ?></label>
							</TD>
						</TR>
						<tr>
							<td align="left" class="key" >
								<?php echo JText::_('LNG_APP_ROOMS_LEFT'); ?>:
							</td>
							<td align="left" nowrap>
								<input type='text' size=28 maxlength=7  id='rooms_left' name ='rooms_left' value='<?php echo $this->item->rooms_left == -1 ? '':$this->item->rooms_left;?>'>
							</td>
							<td align="left">
								<?php echo JText::_('LNG_ROOM_INFO');?>
							</td>
						</tr>
				</table>
			</fieldset>
				
			<fieldset class="form-horizontal">
				<legend><?php echo JText::_('LNG_FEATURES_DISPLAY_SETTINGS'); ?></legend>
				
			<table class='admintable'  width=100%>
				
				<?php if(PROFESSIONAL_VERSION==1){?>
				<tr>
					<td width="10%" align="left" class="key" >
							<?php echo JText::_( 'LNG_ENABLE_OFFERS' ,true); ?>
					</td>
					<td align="left"  id="is_enable_offers" class="radio btn-group btn-group-yesno" >
						<input 
							type		= "radio"
							name		= "is_enable_offers"
							id			= "is_enable_offers0"
							value		= '1'
							<?php echo $this->item->is_enable_offers==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
						/>
						<label
                            class="labelYes"
                               id="label_is_enable_offers0"
                               for="is_enable_offers0"><?php echo JText::_( 'LNG_YES' ,true); ?></label>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_enable_offers"
							id			= "is_enable_offers1"
							value		= '0'
							<?php echo $this->item->is_enable_offers==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
						/>
						<label
                            class="labelNo"
                            id="label_is_enable_offers1"
                            for="is_enable_offers1"><?php echo JText::_( 'LNG_NO' ,true); ?></label>
					</td>
					<td align="left">
                        <?php echo JText::_( 'LNG_INFO_APPLICATION_SET_OFFERS_ON_OFF' ,true); ?>
                    </TD>
				</tr>
				<TR>
					<td align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_EXTRA_OPTIONS',true)?> :</TD>
					<TD nowrap colspan=2 id="is_enable_extra_options" class="radio btn-group btn-group-yesno">
						<input 
							type		= "radio"
							name		= "is_enable_extra_options"
							id			= "is_enable_extra_options0"
							value		= '1'
							<?php echo $this->item->is_enable_extra_options==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
                        <label
                            class="labelYes"
                            id="label_is_enable_extra_options0"
                            for="is_enable_extra_options0"><?php echo JText::_('LNG_YES'); ?></label>
                        &nbsp;
						<input 
							type		= "radio"
							name		= "is_enable_extra_options"
							id			= "is_enable_extra_options1"
							value		= '0'
							<?php echo $this->item->is_enable_extra_options==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
                        <label
                            class="labelNo"
                            id="label_is_enable_extra_options1"
                            for="is_enable_extra_options1"><?php echo JText::_('LNG_NO'); ?></label>
                    </TD>
				</TR>
				
				<TR>
					<td align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_EXCURSIONS',true)?> :</TD>
					<TD nowrap colspan=2 id="enable_excursions" class="radio btn-group btn-group-yesno">
						<input 
							type		= "radio"
							name		= "enable_excursions"
							id			= "enable_excursions0"
							value		= '1'
							<?php echo $this->item->enable_excursions==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
                        <label
                            class="labelYes"
                            id="label_enable_excursions0"
                            for="enable_excursions0"><?php echo JText::_('LNG_YES'); ?></label>
						&nbsp;
						<input 
							type		= "radio"
							name		= "enable_excursions"
							id			= "enable_excursions1"
							value		= '0'
							<?php echo $this->item->enable_excursions==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
                        <label
                            class="labelNo"
                            id="label_enable_excursions1"
                            for="enable_excursions1"><?php echo JText::_('LNG_NO'); ?></label>
					</TD>
				</TR>
				
				
				<TR>
					<td align="left" class="key" nowrap ><?php echo JText::_('LNG_SHOW_AIRPORT_TRANSFER',true)?> :</TD>
					<TD nowrap colspan=2 id="is_enable_screen_airport_transfer" class="radio btn-group btn-group-yesno">
						<input 
							type		= "radio"
							name		= "is_enable_screen_airport_transfer"
							id			= "is_enable_screen_airport_transfer0"
							value		= '1'
							<?php echo $this->item->is_enable_screen_airport_transfer==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
                        <label
                            class="labelYes"
                            id="label_is_enable_screen_airport_transfer0"
                            for="is_enable_screen_airport_transfer0"><?php echo JText::_('LNG_YES'); ?></label>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_enable_screen_airport_transfer"
							id			= "is_enable_screen_airport_transfer1"
							value		= '0'
							<?php echo $this->item->is_enable_screen_airport_transfer==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
                        <label
                            class="labelNo"
                            id="label_is_enable_screen_airport_transfer1"
                            for="is_enable_screen_airport_transfer1"><?php echo JText::_('LNG_NO'); ?></label>
					</TD>
				</TR>

				<tr>
					<td align="left" class="key" nowrap >
						<?php echo JText::_( 'LNG_ENABLE_SPECIAL_DISCOUNTS' ); ?>:
					</td>
					<td align="left" nowrap id="enable_discounts" class="radio btn-group btn-group-yesno">
						<input 
							type		= "radio"
							name		= "enable_discounts"
							id			= "enable_discounts0"
							value		= '1'
							<?php echo $this->item->enable_discounts==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
                        <label
                            class="labelYes"
                            id="label_enable_discounts0"
                            for="enable_discounts0"><?php echo JText::_('LNG_YES'); ?></label>
						&nbsp;
						<input 
							type		= "radio"
							name		= "enable_discounts"
							id			= "enable_discounts1"
							value		= '0'
							<?php echo $this->item->enable_discounts==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
                        <label
                            class="labelNo"
                            id="label_enable_discounts1"
                            for="enable_discounts1"><?php echo JText::_('LNG_NO'); ?></label>
					</td>
				</tr>	
				<?php }?>
				<tr>
					<td align="left" class="key" nowrap ><?php echo JText::_('LNG_SAVE_ALL_GUEST_DATA',true)?> :</TD>
					<TD nowrap colspan=2 id="save_all_guests_data" class="radio btn-group btn-group-yesno" >
						<input 
							type		= "radio"
							name		= "save_all_guests_data"
							id			= "save_all_guests_data0"
							value		= '1'
							<?php echo $this->item->save_all_guests_data==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<label
                            class="labelYes"
                            id="label_save_all_guests_data0"
                            for="save_all_guests_data0"><?php echo JText::_('LNG_YES'); ?></label>
						&nbsp;
						<input 
							type		= "radio"
							name		= "save_all_guests_data"
							id			= "save_all_guests_data1"
							value		= '0'
							<?php echo $this->item->save_all_guests_data==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<label
                            class="labelNo"
                            id="label_save_all_guests_data1"
                            for="save_all_guests_data1"><?php echo JText::_('LNG_NO'); ?></label>
					</TD>
				</tr>
				<tr>
					<td width="10%" align="left" class="key">
							<?php echo JText::_( 'LNG_SHOW_CHILDREN' ,true); ?>
					</td>
					<td align="left" id="show_children"  class="radio btn-group btn-group-yesno">
						<input 
							type		= "radio"
							name		= "show_children"
							id			= "show_children0"
							value		= '1'
							<?php echo $this->item->show_children==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
                        <label
                            class="labelYes"
                            for="show_children0" id="label_show_children0" ><?php echo JText::_( 'LNG_YES' ,true); ?></label>
						&nbsp;
						<input 
							type		= "radio"
							name		= "show_children"
							id			= "show_children1"
							value		= '0'
							<?php echo $this->item->show_children==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<label
                            class="labelNo"
                            for="show_children1" id="label_show_children1" ><?php echo JText::_( 'LNG_NO' ,true); ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="left" class="key" nowrap >
                        <br>
                        <?php echo JText::_( 'LNG_ENABLE_CHILDREN_CATEGORIES'); ?>:
					</td>
					<td align="left" nowrap id="enable_children_categories" class="radio btn-group btn-group-yesno">
						<br>
						<input
							type		= "radio"
							name		= "enable_children_categories"
							id			= "enable_children_categories0"
							value		= '1'
							<?php echo $this->item->enable_children_categories==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
						/>
                        <label class="labelPerPerson" id="label_enable_children_categories0" for="enable_children_categories0">
                        	<?php echo JText::_('LNG_YES'); ?>
                        </label>
						
						<input 
							type		= "radio"
							name		= "enable_children_categories"
							id			= "enable_children_categories1"
							value		= '0'
							<?php echo $this->item->enable_children_categories==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
                        <label class="labelNo" id="label_enable_children_categories1" for="enable_children_categories1">
                            <?php echo JText::_('LNG_NO'); ?>
                        </label>
					</td>
				</tr>
                <tr>
					<td align="left" class="key" nowrap >
                        <br>
                        <?php echo JText::_( 'LNG_CHILDREN_RATES_TYPE'); ?>:
					</td>
					<td align="left" nowrap id="children_rates_type" class="radio btn-group btn-group-yesno">
						<br>
						<input
							type		= "radio"
							name		= "children_rates_type"
							id			= "children_rates_type0"
							value		= '1'
							<?php echo $this->item->children_rates_type==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
						/>
                        <label class="labelYes" id="label_children_rates_type0" for="children_rates_type0">
                        	<?php echo JText::_('LNG_VALUE'); ?>
                        </label>
						
						<input 
							type		= "radio"
							name		= "children_rates_type"
							id			= "children_rates_type1"
							value		= '0'
							<?php echo $this->item->children_rates_type==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
                        <label class="labelNo" id="label_children_rates_type1" for="children_rates_type1">
                            <?php echo JText::_('LNG_PERCENT'); ?>
                        </label>
					</td>
				</tr>
                <tr>
                    <td width="10%" align="left" class="key" nowrap >
                        <?php echo JText::_( 'LNG_ENABLE_MAP'); ?>:
                    </td>
                    <td align="left" nowrap id="enable_map" class="radio btn-group btn-group-yesno">
                        <input
                            type		= "radio"
                            name		= "enable_map"
                            id			= "enable_map0"
                            value		= '1'
                            <?php echo $this->item->enable_map==true? " checked " :""?>
                            accesskey	= "Y"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"
                            />
                        <label class="labelPerPerson" id="label_enable_map0" for="enable_map0">
                            <?php echo JText::_('LNG_YES'); ?>
                        </label>

                        <input
                            type		= "radio"
                            name		= "enable_map"
                            id			= "enable_map1"
                            value		= '0'
                            <?php echo $this->item->enable_map==false? " checked " :""?>
                            accesskey	= "N"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"

                            />
                        <label class="labelNo" id="label_enable_map1" for="enable_map1">
                            <?php echo JText::_('LNG_NO'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="10%" align="left" class="key" nowrap >
                        <?php echo JText::_( 'LNG_ENABLE_BREADCRUMB'); ?>:
                    </td>
                    <td align="left" nowrap id="enable_breadcrumb" class="radio btn-group btn-group-yesno">
                        <input
                            type		= "radio"
                            name		= "enable_breadcrumb"
                            id			= "enable_breadcrumb0"
                            value		= '1'
                            <?php echo $this->item->enable_breadcrumb==1? " checked " :""?>
                            accesskey	= "Y"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"
                            />
                        <label class="labelYes" id="label_enable_breadcrumb0" for="enable_breadcrumb0">
                            <?php echo JText::_('LNG_YES'); ?>
                        </label>

                        <input
                            type		= "radio"
                            name		= "enable_breadcrumb"
                            id			= "enable_breadcrumb1"
                            value		= '0'
                            <?php echo $this->item->enable_breadcrumb==0? " checked " :""?>
                            accesskey	= "N"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"

                            />
                        <label class="labelNo" id="label_enable_breadcrumb1" for="enable_breadcrumb1">
                            <?php echo JText::_('LNG_NO'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
					<td align="left" class="key" nowrap >
                        <br>
                        <?php echo JText::_( 'LNG_CALENDAR_AVAILABILITY_TYPE'); ?>:
					</td>
					<td align="left" nowrap id="calendar_availability_type" class="radio btn-group btn-group-yesno">
						<br>
						<input
							type		= "radio"
							name		= "calendar_availability_type"
							id			= "calendar_availability_type0"
							value		= '1'
							<?php echo $this->item->calendar_availability_type==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
						/>
                        <label class="labelYes" id="label_calendar_availability_type0" for="calendar_availability_type0">
                        	<?php echo JText::_('LNG_CALENDAR_TYPE_DAY'); ?>
                        </label>
						
						<input 
							type		= "radio"
							name		= "calendar_availability_type"
							id			= "calendar_availability_type1"
							value		= '0'
							<?php echo $this->item->calendar_availability_type==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
                        <label class="labelNo" id="label_calendar_availability_type1" for="calendar_availability_type1">
                            <?php echo JText::_('LNG_CALENDAR_TYPE_PERIOD'); ?>
                        </label>
					</td>
				</tr>
				
				<TR>
					<td align="left" class="key" nowrap ><?php echo JText::_('LNG_CURRENCY_DISPLAY',true)?> :</TD>
					<TD nowrap colspan=2 id="currency_display" class="radio btn-group btn-group-yesno">
						<input
							type		= "radio"
							name		= "currency_display"
							id			= "currency_display0"
							value		= '1'
							<?php echo $this->item->currency_display==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
						/>
						<label
							class="labelYes"
							id="label_currency_display0"
							for="currency_display0"><?php echo JText::_('LNG_CODE'); ?></label>
						&nbsp;
						<input
							type		= "radio"
							name		= "currency_display"
							id			= "currency_display1"
							value		= '0'
							<?php echo $this->item->currency_display==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
						/>
						<label
							class="labelNo"
							id="label_currency_display1"
							for="currency_display1"><?php echo JText::_('LNG_SYMBOL'); ?></label>
					</TD>
				</TR>

            </table>

		</fieldset>
		
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('LNG_SEARCH_SETTINGS'); ?></legend>
	<TABLE class='admintable'  width=100%>
		<TR>
			<td align="left" class="key" nowrap alt="dsdas" >
			<div class="control-label">
				<label class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_APPLY_SEARCH_PARAMS');?></strong><br/><?php echo JText::_('LNG_APPLY_SEARCH_PARAMS_DETAILS');?>" title="">
					<?php echo JText::_('LNG_APPLY_SEARCH_PARAMS')?> 
				</label>
			</div>
			</TD>
			<TD nowrap colspan=2 id="apply_search_params" class="radio btn-group btn-group-yesno">
				<input
					type		= "radio"
					name		= "apply_search_params"
					id			= "apply_search_params0"
					value		= '1'
					<?php echo $this->item->apply_search_params==true? " checked " :""?>
					accesskey	= "Y"
					onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
					onmouseout	="this.style.cursor='default'"


				/>
				<label
					class="labelYes"
					id="label_apply_search_params0"
					for="apply_search_params0"><?php echo JText::_('LNG_YES'); ?></label>
				&nbsp;
				<input
					type		= "radio"
					name		= "apply_search_params"
					id			= "apply_search_params1"
					value		= '0'
					<?php echo $this->item->apply_search_params==false? " checked " :""?>
					accesskey	= "N"
					onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
					onmouseout	="this.style.cursor='default'"

				/>
				<label
					class="labelNo"
					id="label_apply_search_params1"
					for="apply_search_params1"><?php echo JText::_('LNG_NO'); ?></label>
			</TD>
		</TR>
		
	</table>
</fieldset>		
		
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('LNG_TAG_MANAGER'); ?></legend>
	<TABLE class='admintable'  width=100%>
		<TR>
			<td align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_GOOGLE_TAG_MANAGER',true)?> :</TD>
			<TD nowrap colspan=2 id="enable_google_tag_manager" class="radio btn-group btn-group-yesno">
				<input
					type		= "radio"
					name		= "enable_google_tag_manager"
					id			= "enable_google_tag_manager0"
					value		= '1'
					<?php echo $this->item->enable_google_tag_manager==true? " checked " :""?>
					accesskey	= "Y"
					onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
					onmouseout	="this.style.cursor='default'"


				/>
				<label
					class="labelYes"
					id="label_enable_google_tag_manager0"
					for="enable_google_tag_manager0"><?php echo JText::_('LNG_YES'); ?></label>
				&nbsp;
				<input
					type		= "radio"
					name		= "enable_google_tag_manager"
					id			= "enable_google_tag_manager1"
					value		= '0'
					<?php echo $this->item->enable_google_tag_manager==false? " checked " :""?>
					accesskey	= "N"
					onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
					onmouseout	="this.style.cursor='default'"

				/>
				<label
					class="labelNo"
					id="label_enable_google_tag_manager1"
					for="enable_google_tag_manager1"><?php echo JText::_('LNG_NO'); ?></label>
			</TD>
		</TR>
		<tr>
			<td align="left" class="key" >
				<?php echo JText::_('LNG_GOOGLE_TAG_MANAGER_ID'); ?>:
			</td>
			<td align="left" nowrap>
				<input type='text' size=28 maxlength=7  id='google_tag_manager_id' name = 'google_tag_manager_id' value='<?php echo $this->item->google_tag_manager_id?>'>
			</td>
		</tr>
	</table>
</fieldset>