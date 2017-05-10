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



if (!checkUserAccess(JFactory::getUser()->id,"manage_email_templates")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}
JHTML::_('stylesheet', 	JURI::root().'components/com_jhotelreservation/assets/style/responsiveMaterialTable.css');

class JHotelReservationViewEmails extends JHotelReservationAdminView
{

    protected $hotel_id;
    protected $items;
    protected $hotels;
    protected $state;
    protected $pagination;

	function display($tpl = null)
	{
        $hotel_id =  $this->get('HotelId');

        $this->hotel_id =  $hotel_id;

        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');

        $items		= $this->get('Items');
        $this->items =  $items;
        $this->hoteltranslationsModel = new JHotelReservationLanguageTranslations();


        $hotels		= $this->get('Hotels');
        $hotels = checkHotels(JFactory::getUser()->id,$hotels);

        $this->hotels =  $hotels;

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        //adds the toolbar to the emails view
        $this->addToolbar();

		parent::display($tpl);
	}


    protected function addToolbar()
    {
        $canDo = JHotelReservationHelper::getActions();

        JToolBarHelper::title(  'J-Hotel Reservation : '. JText::_('LNG_MANAGE_EMAILS_TEMPLATES',true), 'generic.png' );
        //JHotelReservationHelper::addSubmenu('emailtemplates');

        /**
         * If the user has not selected any hotels to display their emails
         * the new,edit and delete button are not shown in the toolbar
         */
        if($this->hotel_id>0) {
            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('email.add');
                JToolBarHelper::editList('email.edit');
            }
            if ($canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'emails.delete', JText::_('LNG_DELETE', true));
            }
        }

        JToolBarHelper::custom( 'defaultemails.display', JHotelUtil::getEmailDefaultIcon(), 'home', JText::_('LNG_MANAGE_EMAILS_DEFAULT'), false, false );
        JToolBarHelper::custom( 'emails.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
        JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#manage-email-templates');


    }
}