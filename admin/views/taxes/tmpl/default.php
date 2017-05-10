<?php defined('_JEXEC') or die('Restricted access'); ?>
 
<?php
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


JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

?>

<form action="<?php echo JRoute::_('index.php?option='.getBookingExtName()); ?>" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<fieldset>
			<legend><?php echo JText::_('LNG_MANAGE_TAXES',true); ?></legend>
			<center>
				<div style='text-align:left'>
					<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
					<select name='hotel_id' id='hotel_id' style='width:300px'
						onchange ='
									var form 	= document.adminForm; 
									var obView	= document.createElement("input");
									obView.type = "hidden";
									obView.name	= "view";
									obView.value= "taxes";
									form.appendChild(obView);
									form.submit();
									'
					>
						<option value=0 <?php echo $this->hotel_id ==0? 'selected' : ''?>><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
						<?php
						foreach($this->hotels as $hotel )
						{
						?>
						<option value='<?php echo $hotel->hotel_id?>' 
							<?php echo $this->hotel_id ==$hotel->hotel_id||(count($this->hotels)==1)? 'selected' : ''?>
						>
							<?php 
								echo stripslashes($hotel->hotel_name);
								echo (strlen($hotel->country_name)>0? ", ".$hotel->country_name : "");
								echo stripslashes(strlen($hotel->hotel_city)>0? ", ".$hotel->hotel_city : "");
							?>
						</option>
						<?php
						}
						?>
					</select>
					<hr>
				</div>
				<TABLE class="table adminlist">
					<thead>
						<th width='1%'>#</th>
						<th width='1%'  align=center>&nbsp;</th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_NAME',true); ?></B></th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_TYPE',true); ?></B></th>
						<th width='30%' align=center ><B><?php echo JText::_('LNG_DESCRIPTION',true); ?></B></th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_VALUE',true); ?></B></th>
						<th width='1%' align=center><B><?php echo JText::_('LNG_AVAILABLE',true); ?></B></th>
					</thead>
					<tbody>
					<?php
					$nrcrt = 1;
					foreach($this->items as $i =>$tax) {
						if ($tax->hotel_id === $this->hotel_id) {
							?>
							<TR class="row<?php echo $i%2 ?>"
								onmouseover="this.style.cursor='hand';this.style.cursor='pointer'"
								onmouseout="this.style.cursor='default'"
								>
								<TD align=center><?php echo $nrcrt++?></TD>
								<td align="center">
									<?php echo Jhtml::_('grid.id', $i, $tax->tax_id); ?>
								</td>

								<TD align=left>

									<a href='<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=tax&layout=edit&tax_id=' . $tax->tax_id . '&hotel_id=' . $this->hotel_id)?>'
									   title="<?php echo JText::_('LNG_CLICK_TO_EDIT', true); ?>"
										>
										<B><?php echo $tax->tax_name?></B>
									</a>
								</TD>
								<TD align=left>
									<a href='<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=tax&layout=edit&tax_id=' . $tax->tax_id . '&hotel_id=' . $this->hotel_id)?>'
									   title="<?php echo JText::_('LNG_CLICK_TO_EDIT', true); ?>"
										>
										<B><?php echo $tax->tax_type == 'Fixed' ? JText::_('LNG_AMOUNT', true) : JText::_('LNG_PERCENT', true);?></B>
									</a>
								</TD>
								<TD align=left><?php echo $tax->tax_description?></TD>
								<TD align=center><?php echo $tax->tax_value . ($tax->tax_type == 'Percent' ? " %" : "")?></TD>
                                <?php if ($tax->is_available == false) { ?>
                                    <td class="center">
                                        <a class="btn btn-micro hasTooltip" href="javascript:void(0);"
                                           onclick="return listItemTask('cb<?php echo $i ?>','tax.state')"
                                           title="Set Tax Available"><i class="icon-unpublish"></i></a>
                                    </td>
                                <?php } else { ?>
                                    <td class="center">
                                        <a class="btn btn-micro hasTooltip" href="javascript:void(0);"
                                           onclick="return listItemTask('cb<?php echo $i ?>','tax.state')"
                                           title="Set Tax Unavailable">
                                            <i class="icon-publish"></i>
                                        </a>
                                    </td>
                                <?php } ?>

							</TR>
						<?php
						}
					}
					?>
					</tbody>
				</TABLE>
			</center>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tax_id" value="" />
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	<input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			if (task != 'taxes.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>'))
			{
				Joomla.submitform(task);
			}
		};

		jQuery(document).ready(function()
				{
					var hotelId=jQuery('#hotel_id').val();
					var refreshScreen=jQuery('#refreshScreen').val();
					var nrHotels = jQuery('#hotel_id option').length;
					if(hotelId>0 && refreshScreen=="" && parseInt(nrHotels)==2){
						jQuery('#refreshScreen').val("true");
						jQuery("#hotel_id").trigger('change');	
					}
				});	
	</script>
</form>