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

JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/js/datepicker/css/layout.css');

JHTML::_('script', 	JURI::root().'components/'.getBookingExtName().'/assets/js/datepicker/js/eye.js');
JHTML::_('script', 	JURI::root().'components/'.getBookingExtName().'/assets/js/datepicker/js/datepicker.js');
JHTML::_('script', 	JURI::root().'components/'.getBookingExtName().'/assets/js/datepicker/js/utils.js');
JHTML::_('script', JURI::root().'components/'.getBookingExtName().'/assets/js/datepicker/js/layout.js');


class JHotelReservationViewAvailability extends JHotelReservationAdminView
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
        $languageTag = JRequest::getVar('_lang');
		$this->items		= $this->get('Items');
		$this->state		= $this->get('State');

		$app = JFactory::getApplication('administrator');
		$hotelId = $app->getUserState('com_jhotelreservation.availability.filter.hotel_id');
		$this->hotelId = $hotelId;
		$this->hasCubilis = UserService::isChannelManagerSet($hotelId,CHANNEL_MANAGER_CUBILIS);

		$hotels		= $this->get('Hotels');
		$this->hotels = checkHotels(JFactory::getUser()->id,$hotels);

        $this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();

        $roomtranslationsModel = new JHotelReservationLanguageTranslations();
        $this->room_name_translation = $roomtranslationsModel->getAllTranslationtByLanguage(ROOM_NAME,$languageTag);
		
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

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
		$canDo = JHotelReservationHelper::getActions();
		
		JToolBarHelper::title(JText::_('LNG_AVAILABILITY_SECTION',true), 'menumgr.png');
	
		JToolBarHelper::apply('availability.saveHotelAvailability');
		
		JToolBarHelper::custom( 'hotels.back', JHotelUtil::getDashboardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
		
		JToolBarHelper::divider();

		//JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#availability');

		
	}
}