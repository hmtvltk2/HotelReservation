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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
?>


<form action="<?php echo JRoute::_('index.php?option='.getBookingExtName()); ?>" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<fieldset>
			<legend><?php echo JText::_('LNG_MANAGE_EMAIL_TEMPLATES'); ?></legend>
				<div id="filter-bar" class="btn-toolbar">
					<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS')?> :</strong>
					<select name='hotel_id' id='hotel_id' style='width:300px'
						onchange ='
									var form = document.adminForm;
									form.elements["view"].value = "emails";
									form.submit();
									'
					>
						<option value=0 <?php echo $this->hotel_id ==0? 'selected' : ''?>><?php echo JText::_('LNG_SELECT_DEFAULT')?></option>
						<?php
						foreach($this->hotels as $hotel )
						{
						?>
						<option value='<?php echo $hotel->hotel_id?>' 
							<?php echo $this->hotel_id ==$hotel->hotel_id? 'selected' : ''?>
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
				</div>
            </fieldset>
				<div class="clearfix"> </div>
				<div  class="responsive_table-responsive-vertical">
	               <table class="responsive_table responsive_table-hover responsive_table-mc-light-blue"  id="itemList">
	                    <thead>
		                    <th width='1%' class=" hidden-phone">#</th>
		                    <th width='1%' class="">&nbsp;</th>
		                    <th><B><?php echo JText::_('LNG_NAME'); ?></B></th>
		                    <th class="hidden-phone"><B><?php echo JText::_('LNG_TYPE'); ?></B></th>
		                    <th class="hidden-phone"><B><?php echo JText::_('LNG_SUBJECT'); ?></B></th>
		                    <th class="hidden-phone"><B><?php echo JText::_('LNG_CONTENT'); ?></B></th>
		                    <th width='1%' class=""><B><?php echo JText::_('LNG_DEFAULT'); ?></B></th>
	                    </thead>
	                    <tbody>
	                    
	                    <?php
	                    $nrcrt = 1;
	
	                    foreach ($this->items as $i => $email)
	                    {
	
	                    if ($email->hotel_id === $this->hotel_id) {
	                    $emailContent = $this->hoteltranslationsModel->getObjectTranslation(EMAIL_TEMPLATE_TRANSLATION, $email->email_id, JRequest::getVar('_lang'));
	                    ?>
	                        <tr class="row<?php echo $i%2 ?>">
	                            <td class=" hidden-phone" data-title="#"><?php echo $nrcrt++ ?></td>
	                            <td class="">
	                                <?php echo Jhtml::_('grid.id',$i , $email->email_id) ?>
	                            </td>
	                            <td align=left class=" has-context" data-title="<?php echo JText::_('LNG_NAME',true)?>">
	                                <a href='<?php echo JRoute::_('index.php?option=' . getBookingExtName() . '&view=email&layout=edit&email_id=' . $email->email_id . '&hotel_id=' . $this->hotel_id) ?>'
	                                   title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>"
	                                    >
	                                    <b><?php echo $email->email_name ?></b>
	                                </a>

	                            </td>
	                            <td class=" hidden-phone" data-title="<?php echo JText::_('LNG_TYPE',true)?>"><?php echo $email->email_type ?></td>
	                            <td class=" hidden-phone" data-title="<?php echo JText::_('LNG_SUBJECT',true)?>"><?php echo $email->email_subject ?></td>
	                            <td
                                    data-title="<?php echo JText::_('LNG_CONTENT',true)?>"
	                                class=" small hidden-phone column"
	                                align=left><?php echo isset($emailContent) ? $emailContent->content : $email->email_content; ?></td>
	                            <td class=" small" data-title="<?php echo JText::_('LNG_DEFAULT',true)?>">
	                                <img border=1
	                                     src="<?php echo JURI::base() . "components/" . getBookingExtName() . "/assets/img/" . ($email->is_default == false ? "unchecked.gif" : "checked.gif") ?>"
	                                     onclick="
	                                     <?php
	                                     if ($email->is_default == false) {
	                                         ?>
	                                         document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=emails.state&email_id='. $email->email_id.'&hotel_id='.$this->hotel_id )?> '
	                                     <?php
	                                     }
	                                     ?>
	                                         "
	                                    />
	
	                            </td>
	                        </tr>
	                        
	                    <?php
	                        }
	                    }
	                    ?>
	                    </tbody>
	                  </table>
	              </div>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="view" value="" />
    <input type="hidden" name="email_id" value="" />
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	<input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	<script language="javascript" type="text/javascript">
			jQuery(document).ready(function()
				{
					var hotelId=jQuery('#hotel_id').val();
					var refreshScreen=jQuery('#refreshScreen').val();
					var nrHotels = jQuery('#hotel_id option').length;
					if(refreshScreen=="" && parseInt(nrHotels)==2){
						jQuery('#hotel_id :nth-child(2)').prop('selected', true); 
						jQuery('#refreshScreen').val("true");
						jQuery("#hotel_id").trigger('change');	
					}
				});

            Joomla.submitbutton = function(task)
            {
                if (task != 'emails.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>'))
                {
                    Joomla.submitform(task);
                }
            }
	</script>
</form>

