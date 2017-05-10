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

if (!checkUserAccess(JFactory::getUser()->id,"manage_reservations")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}
JHTML::_('stylesheet', 	JURI::root().'components/com_jhotelreservation/assets/style/responsiveMaterialTable.css');

class JHotelReservationViewReservations extends JHotelReservationAdminView
{
	
	protected $items;
	protected $pagination;
	protected $state;
	protected $hotels;
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$lang 		= JFactory::getLanguage();
		$this->items		= $this->get('Items');

		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->roomTypes 	= $this->get('RoomTypesOptions');
        $this->paymentMethods = $this->get('PaymentMethods');
        $this->reservationStatuses = JHotelReservationHelper::getReservationStatuses();
		$this->paymentStatuses = JHotelReservationHelper::getPaymentStatuses();
        $this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();

        $hotels		= $this->get('Hotels');
		$this->hotels = checkHotels(JFactory::getUser()->id,$hotels);
		
		$layout = JRequest::getVar("layout");
		if(isset($layout)){
			$tpl = $layout;
		}
		$this->setLayout('default');

		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
	
		if($tpl=='print')
			JToolBarHelper::custom( 'reservations', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true),false, false );
		else
			$this->addToolbar();
			
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
	
		JToolBarHelper::title(JText::_('LNG_MANAGE_RESERVATIONS',true), 'menumgr.png');
	
		JToolBarHelper::addNew('reservation.add');
		JToolBarHelper::editList('reservation.edit');
		JToolBarHelper::deleteList('', 'reservations.delete', 'JTOOLBAR_DELETE');
		JToolBarHelper::custom( 'reservations.exportToCSV', JHotelUtil::getExportIcon(),'home', JText::_('LNG_EXPORT_CSV'),false, false );
		JToolBarHelper::custom('reservations.cancelFromCsv', 'cancel', 'cancel', JText::_('LNG_BATCH_CANCEL'), false, false );
		JToolBarHelper::custom('reservations.printReservations', 'print', 'print', JText::_('LNG_PRINT'), false, false );
		JToolBarHelper::custom( 'jhotelreservation.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true),false, false );
		JToolBarHelper::divider();

		JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#managing-reservations');
	}
}