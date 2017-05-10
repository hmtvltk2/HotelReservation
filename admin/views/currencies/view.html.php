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

jimport('joomla.application.component.view');

if (!checkUserAccess(JFactory::getUser()->id,"currency_settings")){
    $msg = "You are not authorized to access this resource";
    $this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}

class JHotelReservationViewCurrencies extends JHotelReservationAdminView
{

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * @param null $tpl
     */
    function display($tpl = null)
    {
        $items = $this->get('Items');
        $this->items =  $items;
        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');

        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        //set the toolbar
        $this->addToolBar();

        parent::display($tpl);
    }

    protected function addToolBar()  {

        $canDo = JHotelReservationHelper::getActions();

        JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_('LNG_CURRENCY_SETTINGS',true), 'generic.png' );

        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('currency.add');
            JToolBarHelper::editList('currency.edit');
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'currencies.delete', JText::_('LNG_DELETE', true));
        }
        JToolBarHelper::custom( 'currencies.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
        JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#currency-settings');

    }
}