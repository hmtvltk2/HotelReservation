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
?>
<form action="<?php echo JRoute::_('index.php?option='.getBookingExtName());?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="about" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>

	<table class="adminlist" width="100%">
				<thead>
					<tr>
						<th nowrap="nowrap"><div align="center"><?php echo JText::_('LNG_ABOUT',true)?></div></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
						
						<p align="center">
							<?php echo JText::_('LNG_ABOUT_APPLICATION',true); ?>
						</p>
						  <p align="center">
							<img 
								src = "<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/logo.png"?>"
								
								alt="image"
							>
								&nbsp;</p>
						  <p>&nbsp;</p></td>
					</tr>				
					<tr>
					  <td>&nbsp;</td>
				  </tr>				
				
				</tbody>
			</table>