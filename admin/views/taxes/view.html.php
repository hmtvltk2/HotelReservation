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

if (!checkUserAccess(JFactory::getUser()->id,"manage_taxes")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}



class JHotelReservationViewTaxes extends JHotelReservationAdminView
{
    protected $hotel_id;
    protected $items;
    protected $hotels;
    protected $state;
    protected $pagination;


    function display($tpl = null)
	{
            $hotel_id = $this->get('HotelId');
			$this->hotel_id =  $hotel_id;

            $this->pagination	= $this->get('Pagination');
            $this->state		= $this->get('State');
			
			$items	= $this->get('Items');
			$this->items =  $items; 
			
			$hotels		= $this->get('Hotels'); 
			$hotels = checkHotels(JFactory::getUser()->id,$hotels);
				
			$this->hotels =  $hotels;

		parent::display($tpl);
        $this->addToolbar();
    }

    protected function addToolbar()
    {
        $canDo = JHotelReservationHelper::getActions();


        JToolBarHelper::title(   'J-HotelReservation : '.JText::_('LNG_MANAGE_TAXES',true), 'generic.png' );
        JRequest::setVar( 'hidemainmenu', 1 );

        if( $this->hotel_id > 0 )
        {
            if ($canDo->get('core.create')) {

                JToolBarHelper::addNew('tax.add');
                JToolBarHelper::editList('tax.edit');
            }
            if ($canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'taxes.delete', JText::_('LNG_DELETE', true));
            }
        }
        JToolBarHelper::custom( 'taxes.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
        JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#manage-taxes');

    }
}