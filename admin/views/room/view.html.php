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

if (!checkUserAccess(JFactory::getUser()->id,"manage_rooms")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}
JHTML::_( 'behavior.calendar' );

jimport('joomla.application.component.model');  

class JHotelReservationViewRoom extends JHotelReservationAdminView{

	protected $form;
	protected $item;
	protected $state;
	protected $hotel;


	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		//$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');
		$this->state = $this->get('State');
		$this->hotel = $this->get('Hotel');
		$this->roomFeatures	= $this->get('FeatureOptionsRoom');
		$this->childrenCategories = ChildrenCategoryService::getChildrenCategories($this->state->get('room.hotel_id'));
		$this->childrenCategoryPrices = ChildrenCategoryService::getChildrenCategoryPrices($this->item->rate->id);
		
		$this->pictures	= $this->get('RoomPictures');

		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		//get hotel translations
		$hoteltranslationsModel = new JHotelReservationLanguageTranslations();
		$this->translations = $hoteltranslationsModel->getAllTranslations(ROOM_TRANSLATION, $this->item->room_id);
        $this->room_name_translations = $hoteltranslationsModel->getAllTranslations(ROOM_NAME, $this->item->room_id);


        $appSettings = JHotelUtil::getApplicationSettings();
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
		
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->room_id == 0);

		JToolBarHelper::title(JText::_('LNG_ROOM',true)." : ".(!$isNew ? JText::_('LNG_EDIT',true): JText::_('LNG_ADD_NEW',true)), 'menu.png');

		if ($canDo->get('core.create')){
			JToolBarHelper::apply('room.apply');
			JToolBarHelper::save('room.save');
			JToolBarHelper::save2new('room.save2new');
		}
			
		if(!$isNew){
			JToolBarHelper::custom('room.editrateprices', 'edit', 'stats', 'JTOOLBAR_EDIT_RATE_DETAILS',false);
		}
		JToolBarHelper::cancel('room.cancel');
		JToolBarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#manage-rooms');
        JHTML::_('script',      JURI::root().'components/'.getBookingExtName().'/assets/js/jquery.upload.js');
        JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/js/dropzone/dropzone.css');
        JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/js/dropzone/basic.css');
        JHTML::_('script', 	    'components/'.getBookingExtName().'/assets/js/dropzone/dropzone.js');
        JHTML::_('script',      'components/'.getBookingExtName().'/assets/js/dropzone/jhotelImageUploader.js');
    }
}

