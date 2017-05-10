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
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

if (!checkUserAccess(JFactory::getUser()->id,"manage_reservations")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getComponentName(), $msg );
}

class JHotelReservationViewReservation extends JViewLegacy
{
	
	protected $item;
	protected $state;
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$languageTag    = JRequest::getVar('_lang');
		$this->item		= $this->get('Item');
		$this->state		= $this->get('State');
		$this->changeLogs = $this->get('ChangeLogs');

		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$this->roomTypes 	= $this->get('RoomTypesOptions');
		$this->guestTypes = JHotelReservationHelper::getGuestTypes();
		
		$hotels		= $this->get('Hotels');
		$this->hotels = checkHotels(JFactory::getUser()->id,$hotels);

		$hoteltranslationsModel = new JHotelReservationLanguageTranslations();
		$this->room_name_translation = $hoteltranslationsModel->getAllTranslationtByLanguage(ROOM_NAME,$languageTag);
		
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

        $this->includes();
        $this->addToolbar();
        parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);

		$user = JFactory::getUser();
		$isNew = false;

		JToolbarHelper::title(JText::_($isNew ? 'LNG_NEW_RESERVATION' : 'LNG_EDIT_RESERVATION', true), 'menu.png');

		$hotelId = JRequest::getVar('hotel_id');

		if( $hotelId > 0 || $this->state->get('reservation.hotel_id') > 0 )
		{
			JToolbarHelper::apply( 'reservation.apply' );

			JToolbarHelper::save( 'reservation.save' );
		}

		JToolbarHelper::cancel('reservation.cancel', 'JTOOLBAR_CLOSE');

		JToolbarHelper::divider();

		if (JRequest::getVar('layout') == 'edit' && JRequest::getVar('reservationId')>0) {
			JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#editing-a-reservation');
		}
		else{
			JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#add-reservations');
		}

	}

    function includes() {
	    $doc = JFactory::getDocument();
	    $doc->addScript( JURI::root() . 'components/' . getBookingExtName() . '/assets/js/reservation.js' );
	    JHtml::_('stylesheet', 	'components/'.getBookingExtName().'/assets/js/selectize/css/selectize.bootstrap3.min.css');
	    JHtml::_('script', 	    'components/'.getBookingExtName().'/assets/js/selectize/js/standalone/selectize.min.js');

    }
}