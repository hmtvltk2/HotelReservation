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
if (!checkUserAccess(JFactory::getUser()->id,"about_hotelreservation")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}



class JHotelReservationViewAbout extends JHotelReservationAdminView
{
	function display($tpl = null)
	{
		JToolBarHelper::title('J-Hotel Reservation'.JText::_('LNG_ABOUT',true), 'generic.png');	
		// JRequest::setVar( 'hidemainmenu', 1 );  
		JToolBarHelper::custom( 'jhotelreservation.back', JHotelUtil::getDashBoardIcon(), 'home', 'Back',false, false );
		parent::display($tpl);
	}
	
}