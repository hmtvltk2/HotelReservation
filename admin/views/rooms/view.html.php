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

class JHotelReservationViewRooms extends JHotelReservationAdminView
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
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
	
		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();

        $hoteltranslationsModel = new JHotelReservationLanguageTranslations();
        $this->room_name_translation = $hoteltranslationsModel->getAllTranslationtByLanguage(ROOM_NAME,$languageTag);

		$hotels		= $this->get('Hotels');
		$this->hotels = checkHotels(JFactory::getUser()->id,$hotels);
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
	
		parent::display($tpl);
		$this->addToolbar();
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{		
		$canDo = JHotelReservationHelper::getActions();
		
	
		JToolBarHelper::title(JText::_('LNG_MANAGE_ROOMS',true), 'menumgr.png');
	
		if ($canDo->get('core.create') && $this->state->get('filter.hotel_id')>0){
			JToolBarHelper::addNew('room.add');
			JToolBarHelper::editList('room.edit');
		}
		JToolBarHelper::custom('childcategories.display', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_CHILDREN_CATEGORIES', true), false, false);
		JToolBarHelper::divider();
		JToolBarHelper::publishList('rooms.publishList', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::unpublishList('rooms.unPublishList', 'JTOOLBAR_UNPUBLISH', true);
		
		if ($canDo->get('core.delete')){
			JToolBarHelper::deleteList('', 'rooms.delete', 'JTOOLBAR_DELETE');
		}
		JToolBarHelper::custom( 'hotels.back', JHotelUtil::getDashboardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
		
		JToolBarHelper::divider();
		
		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_jhotelreservation');
		}

        JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#manage-rooms');
		//JHotelReservationHelper::addSubmenu('rooms');
		
	}

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
            'r.ordering' => JText::_('JGRID_HEADING_ORDERING'),
            'r.name' => JText::_('LNG_NAME'),
        );
    }
}