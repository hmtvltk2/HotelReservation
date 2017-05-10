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
JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/style/responsiveRooms.css');

class JHotelReservationViewHotels extends JViewLegacy
{
	function display($tpl = null){
		$this->hotels = $this->get("Hotels");
		$this->totalHotels = $this->get("TotalHotels");
		
		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		if($this->appSettings->is_enable_reservation==0){
			JHotelUtil::getInstance()->showUnavailable();
		}
		//if a single hotel redirect to hotel description
		if(count($this->hotels)==1 && ENABLE_SINGLE_HOTEL == 1){
			$hotelLink = JHotelUtil::getHotelLink($this->hotels[0]);
			$app =JFactory::getApplication();
			$app->redirect($hotelLink);
		}
		
		JRequest::setVar('showFilter',1);

		$voucher = JRequest::getVar('voucher');
		$this->voucher =  $voucher;

		
		$pagination =$this->get('Pagination');
		$this->pagination =  $pagination;

		$session = JFactory::getSession();
		$this->userData =  $_SESSION['userData'];
				
		$this->searchFilter = $this->get('SearchFilter');
        $this->hotelTypes = $this->get("HotelTypes");

		parent::display($tpl);
	}
}
?>
