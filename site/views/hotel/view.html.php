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

jimport( 'joomla.application.component.view');
JHTML::_('stylesheet', 'administrator/components/'.getBookingExtName().'/assets/styles/tabs.css');
JHTML::_('stylesheet', 'components/'.getBookingExtName().'/assets/style/gallery/touchTouch.css');
JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/js/bootstrap-datepicker/css/bootstrap-datepicker3.css');
JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/style/responsiveRooms.css');

$tag = JHotelUtil::getJoomlaLanguage();
JHTML::_('script',	'components/com_jhotelreservation/assets/js/search.js');
JHTML::_('script', 	'components/'.getBookingExtName().'/assets/js/gallery/touchTouch.jquery.js');
JHTML::_('script', 	'components/'.getBookingExtName().'/assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js');
JHTML::_('script', 	'components/'.getBookingExtName().'/assets/js/commentingjs/commenting.js');
JHTML::_('script', 	'components/'.getBookingExtName().'/assets/js/bootstrap-datepicker/locales/bootstrap-datepicker.'.$tag.'.min.js');
JHTML::_('script', 	'components/'.getBookingExtName().'/assets/js/jhotelmap.js');


class JHotelReservationViewHotel extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->hotel = $this->get("Item");
		$this->state = $this->get('State');

		$this->offers = $this->get("Offers");
		$this->rooms = $this->get("Rooms");
        $this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();


        $this->hotelBreadCrumb = $this->get("HotelBreadCrumb");

        $this->user = JFactory::getUser();
        $this->reviewCommentId =  JRequest::getVar('selected');


        if ($this->appSettings->room_view == 2 ){
            $this->addStyles();
        }


        if($this->appSettings->is_enable_reservation==0){
			JHotelUtil::getInstance()->showUnavailable();
		}
		$this->userData =  UserDataService::getUserData();
		

		UserDataService::prepareUserViewedProperties();
		$this->currencies = CurrencyService::getAllCurrencies();
		
		$this->pagination = $this->get('ReviewsPagination');
	
		parent::display($tpl);
	}

    function addStyles(){
        JHTML::_('stylesheet', 'components/'.getBookingExtName().'/assets/style/room_combined.css');
    }
}
?>
