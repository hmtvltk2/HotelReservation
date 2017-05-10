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


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!checkUserAccess(JFactory::getUser()->id,"updates_hotelreservation")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}

class JHotelReservationViewUpdates extends JHotelReservationAdminView
{
	function display($tpl = null)
	{
        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', true);

		$this->item = JHotelUtil::getApplicationSettings();
		$this->items = $this->get('Items');
		$this->currentVersion = $this->get('CurrentVersion');
		$this->expirationDate= $this->get('ExpirationDate');
		
		
		JToolBarHelper::title('J-Hotel Reservation '.JText::_('LNG_UPDATES',true), 'generic.png');
		JToolbarHelper::custom('updates.saveOrder', 'save', 'save', 'LNG_SAVE_ORDER', true, false);
		JToolbarHelper::custom('updates.update', 'upload', 'upload', 'COM_INSTALLER_TOOLBAR_UPDATE', true, false);
		JToolbarHelper::custom('updates.find', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_FIND_UPDATES', false, false);
		JToolbarHelper::divider();
		JToolBarHelper::custom( 'jhotelreservation.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true),false, false );
		JToolBarHelper::help('', false, DOCUMENTATION_URL . 'hotelreservationadministration.html#update');

		parent::display($tpl);
	}
	
}