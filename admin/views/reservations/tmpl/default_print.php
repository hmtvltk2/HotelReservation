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

$user		= JFactory::getUser();
$userId		= $user->get('id');

?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        var printButton = jQuery('div.printbutton a');
        printButton.addClass('btn btn-primary');
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reservations');?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" id="task" name="task" value="" />

</form>

<div class="printbutton clear">
	<a onclick="window.print()" href="javascript:void(0);">Print</a>
</div>
</br></br>

<div class="clear">
	<table width="100%">
		<?php 
		$reservationService = new ReservationService();
		foreach($this->items as $itemReservation) {
			$item = $reservationService->getReservation($itemReservation->confirmation_id, $itemReservation->hotel_id, false);
				
			?>
		<tr>
			<td> <?php echo $item->reservationInfo ?></td>
		</tr>
		<tr>
			<td><br />
				<table style="width: 100%;" border="0" cellspacing="0"
					cellpadding="0">
					<thead>
						<tr>
							<th
                                class="reservationHeader"
								align="left" bgcolor="#d9e5ee" width="48.5%"><?php echo JText::_('LNG_GUEST_INFORMATIONS',true);?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td
                                class="reservationHeader_1"
								valign="top"> <?php echo $item->billingInformation ?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td><br />
				<table style="width: 100%;" border="0" cellspacing="0"
					cellpadding="0">
					<thead>
						<tr>
							<th
                                class="reservationHeader_2"

								align="left" bgcolor="#d9e5ee" width="48.5%"><?php echo JText::_('LNG_PAYMENT_INFORMATION',true);?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td
                                class="reservationHeader_3"
								valign="top"><?php echo $item->paymentInformation ?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<?php }?>
	</table>
</div>
 