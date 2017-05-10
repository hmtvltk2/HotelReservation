<?php
/*------------------------------------------------------------------------
# JHotelReservation
# author CMSJunkie
# copyright Copyright (C) 2013 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/hotel_reservation/?p=1
# Technical Support:  Forum Multiple - http://www.cmsjunkie.com/forum/joomla-multiple-hotel-reservation/?p=1
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

if (!checkUserAccess(JFactory::getUser()->id,"application_settings")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}


class JHotelReservationViewApplicationSettings extends JHotelReservationAdminView
{
	function display($tpl = null)
	{
		$item = $this->get('Data'); 
		$this->item =  $item;
        $hoteltranslationsModel = new JHotelReservationLanguageTranslations();
        $this->translations = $hoteltranslationsModel->getAllTranslations(TERMS_AND_CONDITIONS_TRANSLATION, $this->item->applicationsettings_id);

        $this->attributeConfiguration = JHotelUtil::getAttributeConfiguration();
		$elements = new stdClass();
		//hotel important informations
        $elements->show_price_per_person = $this->item->show_price_per_person;
        $elements->charge_only_reservation_cost= $this->item->charge_only_reservation_cost;
        $elements->send_invoice_to_email = $this->item->send_invoice_to_email;
		$this->elements = $elements;
		$this->languages = $this->get('Languages');
		$this->delimiters = $this->get('Delimiters');
		
		parent::display($tpl);
        $this->addScripts();
        $this->addToolbar();
	}
	
	function addToolbar(){
		$canDo = JHotelReservationHelper::getActions();
	
		JToolBarHelper::title(   JText::_('LNG_APPLICATION_SETTINGS',true), 'generic.png' );
		if ($canDo->get('core.create')){
			JToolBarHelper::apply('applicationsettings.apply');
			JToolBarHelper::save('applicationsettings.save');
		}
		JToolBarHelper::cancel('applicationsettings.cancel');
		
		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_jhotelreservation');
		}
		//JHotelReservationHelper::addSubmenu('applicationsettings');
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#application-settings' );

	}

    function addScripts(){
        JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/js/dropzone/dropzone.css');
        JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/js/dropzone/basic.css');
        JHTML::_('script', 	    'components/'.getBookingExtName().'/assets/js/dropzone/dropzone.js');
        JHTML::_('script',      'components/'.getBookingExtName().'/assets/js/dropzone/jhotelImageUploader.js');
    }

}