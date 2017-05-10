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


class JHotelReservationViewTax extends JHotelReservationAdminView
{
	protected $item;
	protected $hotel_id;
	protected $hotel;
	protected $state;

	public function display($tpl = null)
	{
			$item = $this->get('Item');
			$this->item =  $item;

			$this->state = $this->get('State');
			
			$hotel_id =  $this->get('HotelId'); 
			$this->hotel_id =  $hotel_id; 
			
			$hotel		= $this->get('Hotel'); 
			$this->hotel =  $hotel;

        $this->addToolbar();
		parent::display($tpl);
    }

    protected function addToolbar()
    {
		$canDo = JHotelReservationHelper::getActions();

		JToolBarHelper::title(    'J-Hotel Reservation : '.( $this->item->tax_id > 0? JText::_( "LNG_EDIT",true) : JText::_("LNG_ADD_NEW" ,true) ).' '.JText::_('LNG_TAX',true), 'generic.png' );
		if ($canDo->get('core.edit')) {
			JToolBarHelper::apply('tax.apply');
			JToolBarHelper::save('tax.save');
		}
		JToolBarHelper::cancel('tax.cancel');

		JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#manage-taxes');
	}
}