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
if (!checkUserAccess(JFactory::getUser()->id,"manage_hotels")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}
JHTML::_('stylesheet', 	JURI::root().'components/com_jhotelreservation/assets/style/responsiveMaterialTable.css');

class JHotelReservationViewHotels extends JHotelReservationAdminView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $appSettings;
	
	function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->items = checkHotels(JFactory::getUser()->id,$this->items);
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->appSettings  = JHotelUtil::getInstance()->getApplicationSettings();
		
		//JHotelReservationHelper::addSubmenu('hotels');
		
		$this->statuses		= JHotelReservationHelper::getStatuses();
		$this->accomodationTypes	= $this->get('AccommodationTypes');
		
		$this->addToolbar(count($this->items));
		
		parent::display($tpl);
	}
	
	function addToolbar($nrHotels){
		
		$canDo = JHotelReservationHelper::getActions();
		
		JToolBarHelper::title('J-HotelReservation : '.JText::_('LNG_MANAGE_HOTELS',true), 'generic.png' );
		JRequest::setVar( 'hidemainmenu', 0);

		if ($canDo->get('core.create') && (ENABLE_SINGLE_HOTEL!=1 || $nrHotels==0)){
			JToolBarHelper::addNew('hotel.edit');
		}
		if ($canDo->get('core.edit')){
			JToolBarHelper::editList('hotel.edit');
		}
		if ($canDo->get('core.delete')){
			JToolBarHelper::deleteList( '', 'hotels.delete', JText::_('LNG_DELETE',true));
		}
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'hotels.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );


		JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#manage-hotels');

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_jhotelreservation');
		}

	}
	

}

