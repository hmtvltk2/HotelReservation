<?php
/**
* @copyright	Copyright (C) 2008-2016 CMSJunkie. All rights reserved.
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

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';


class JHotelReservationViewViewedProperties extends JViewLegacy
{
	function display($tpl = null){

		$items	    =$this->get('Items');
		$this->items =  $items;

		$this->user = JFactory::getUser();

		$this->appSettings = JHotelUtil::getApplicationSettings();

		$this->addScripts();
		parent::display($tpl);
	}

	function addScripts(){
		JHtml::_('stylesheet',  'administrator/components/'.getBookingExtName().'/assets/styles/responsivegrid.css');
		JHtml::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/styles/joomlatabs.css');
		JHtml::_('stylesheet', 	'components/'.getBookingExtName().'/assets/style/general.css');
		JHtml::_('stylesheet', 	'components/'.getBookingExtName().'/assets/style/form.css');
	}

}
?>
