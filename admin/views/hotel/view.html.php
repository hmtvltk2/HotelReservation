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

class JHotelReservationViewHotel extends JHotelReservationAdminView
{
	function display($tpl = null)
	{
			$item	    =$this->get('Data');
			$this->item =  $item;
			$this->item->ignored_dates = JHotelUtil::formatToDefaultDate($this->item->ignored_dates);

			$this->lodgingtypes = $this->getModel('lodgingtypes');
			$this->facilities = $this->getModel('facilities');
			$this->accomodationtypes = $this->getModel('accomodationtypes');
			$this->environmenttypes = $this->getModel('environmenttypes');
			$this->paymentoptions = $this->getModel('paymentoptions');
			$this->regiontypes = $this->getModel('regiontypes');

			JToolBarHelper::title(    'J-Hotel Reservation : '.( $item->hotel_id > 0? JText::_( "LNG_EDIT",true) : JText::_('LNG_ADD_NEW',true) ).' '.JText::_('LNG_HOTEL',true), 'generic.png' );

			$elements = new stdClass();
			//hotel important informations
            $elements->allowPets = $item->informations->pets;
            $elements->parking = $item->informations->parking;
            $elements->wifi = $item->informations->wifi;
            $elements->publicTransport = $item->informations->public_transport;
            $elements->suitableDisabled = $item->informations->suitable_disabled;

            $this->appSettings = JHotelUtil::getApplicationSettings();
			//recommended
            $elements->recommended = $item->recommended;
			$this->elements = $elements;

			$hoteltranslationsModel = new JHotelReservationLanguageTranslations();
			$this->translations = $hoteltranslationsModel->getAllTranslations(HOTEL_TRANSLATION, $this->item->hotel_id);
            $this->cancellationCondition = $hoteltranslationsModel->getAllTranslations(CANCELLATION_CONDITIONS, $this->item->informations->id);
            $this->childrenCategory = $hoteltranslationsModel->getAllTranslations(CHILDREN_CATEGORY, $this->item->informations->id);


            // Check for errors.
            if (count($errors = $this->get('Errors')))
            {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }
        $this->addToolbar($this->item);
        $this->includeFunctions($this->appSettings->google_map_key);
        parent::display($tpl);

    }
	function addToolbar($item){
	
		$canDo = JHotelReservationHelper::getActions();

		JRequest::setVar( 'hidemainmenu', 1);

		if ($canDo->get('core.create') && ($item->hotel_state==0 || isSuperUser(JFactory::getUser()->id))){
			JToolBarHelper::apply('hotel.apply');
			JToolBarHelper::save('hotel.save');
		}
		JToolBarHelper::cancel('hotel.cancel');

		JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#add-edit-hotel');

	}
	
	function includeFunctions($map_Key){
        JHTML::_('stylesheet', 	JURI::root().'components/'.getBookingExtName().'/assets/js/validation/css/template.css');
        JHTML::_('script', 	    JURI::root().'components/'.getBookingExtName().'/assets/js/jquery.selectlist.js');
        JHTML::_('script', 	    JURI::root().'components/'.getBookingExtName().'/assets/js/manageHotels.js');
		$tag = JHotelUtil::getJoomlaLanguage();

        //Drop Zone js and styles file for image uploads
        JHTML::_('script',      JURI::root().'components/'.getBookingExtName().'/assets/js/jquery.upload.js');
        JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/js/dropzone/dropzone.css');
        JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/js/dropzone/basic.css');
        JHTML::_('script', 	    'components/'.getBookingExtName().'/assets/js/dropzone/dropzone.js');
        JHTML::_('script',      'components/'.getBookingExtName().'/assets/js/dropzone/jhotelImageUploader.js');
		$key= "";
		if(!empty($map_Key))
			$key="&key=".$map_Key;
		JHtml::_('script', 'https://maps.google.com/maps/api/js?sensor=true&libraries=places'.$key);

	}
}

