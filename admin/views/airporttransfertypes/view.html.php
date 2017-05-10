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

if (!checkUserAccess(JFactory::getUser()->id,"manage_airport_transfers")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}



class JHotelReservationViewAirportTransferTypes extends JHotelReservationAdminView
{
    protected $hotel_id;
    protected $items;
    protected $hotels;
    protected $pagination;
    protected $state;

	function display($tpl = null)
	{
		
		
			$hotel_id =  $this->get('HotelId');
			$this->hotel_id =  $hotel_id; 
			
			$items		= $this->get('Items');
			$this->items =  $items;

            $this->pagination	= $this->get('Pagination');
            $this->state		= $this->get('State');

            // Check for errors.
            if (count($errors = $this->get('Errors')))
            {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }
            
            $languageTag = JRequest::getVar('_lang');
            $hoteltranslationsModel = new JHotelReservationLanguageTranslations();
            $this->transfer_name_translation = $hoteltranslationsModel->getAllTranslationtByLanguage(AIRPORT_TRANSFER_TRANSLATION_NAME,$languageTag);
            $this->transfers_translation = $hoteltranslationsModel->getAllTranslationtByLanguage(AIRPORT_TRANSFER_TRANSLATION,$languageTag);
            
            $hotels		= $this->get('Hotels');
			$hotels = checkHotels(JFactory::getUser()->id,$hotels);
			$this->hotels =  $hotels; 

        $this->addToolbar($hotel_id);
		parent::display($tpl);
	}

    protected function addToolbar($hotel_id)
    {
        $canDo = JHotelReservationHelper::getActions();
        JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_('LNG_MANAGE_AIRPORT_TRANSFER_TYPES',true), 'generic.png' );
        //JHotelReservationHelper::addSubmenu('airporttransfertypes');

        if( $hotel_id > 0 )
        {
            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('airporttransfertype.add');
                JToolBarHelper::editList('airporttransfertype.edit');
            }
            if ($canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'airporttransfertypes.delete', JText::_('LNG_DELETE', true));
            }
        }
        JToolBarHelper::custom( 'airporttransfertypes.back', JHotelUtil::getDashBoardIcon(), 'home', 'Back', false, false );
        JToolBarHelper::custom('airlines.display', JHotelUtil::getDashBoardIcon(),'Airlines','Airlines',false,false);
        JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#airport-transfer');
    }
}