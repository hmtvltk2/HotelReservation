<?php // no direct access
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

defined('_JEXEC') or die('Restricted access');

?>

<div id="user-options">
<h3 class="title">
<?php echo JTEXT::_("LNG_CLIENT_SETTINGS") ?>
	</h3>
	<div class="user-options-container">
		<ul>
			<li class="option-button">
			 	<a class="box box-inset" href="<?php  echo JRoute::_('index.php?option=com_jhotelreservation&task=customeraccount.managereservations') ?>">
			 		<h3>
			 			<?php echo JTEXT::_("LNG_USER_RESERVATIONS") ?>
 					</h3>
                    <img alt="Manage Reservation" src="<?php echo JURI::root().'/administrator/components/'.getBookingExtName().'/assets/img/reservations.png';?>">
 					<p class="box-reservations"><?php echo JTEXT::_("LNG_USER_RESERVATIONS_INFO") ?>&nbsp;</p>
 				</a>
			</li>
			<li class="option-button">
			 	<a class="box box-inset" href="<?php  echo JRoute::_('index.php?option=com_users&view=profile&layout=edit'); ?>">
			 		<h3>
			 			<?php echo JTEXT::_("LNG_ADD_MODIFY_USER_DATA") ?>
	 					</h3>
                    <img  alt="Edit User" src="<?php echo JURI::root().'/administrator/components/'.getBookingExtName().'/assets/img/useraccess.png'; ?>">
	 					<p class="box-user-account"> <?php echo JTEXT::_("LNG_ADD_MODIFY_USER_DATA_INFO") ?>
							<?php
							for($i=0; $i<10;$i++)
							{
								echo "\t";
							}?>
						</p>
 				</a>
			</li>
			
		</ul>
	</div>
</div>