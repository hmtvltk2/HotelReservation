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

jimport('joomla.application.component.controllerform');


class JHotelReservationControllerRoomDiscount extends JControllerForm
{

    function __construct()
    {
        parent::__construct();
    }


    /**
     * save a record (and redirect to main page)
     * @return void
     */
    function save($key= null , $urlVar = null) {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $app      = JFactory::getApplication();
        $model = $this->getModel('RoomDiscount');
        $post = JRequest::get( 'post' );
        $data = JRequest::get( 'post' );
        $context  = 'com_jhotelreservation.edit.roomdiscount';
        $task     = $this->getTask();
        $discountId = JRequest::getInt('discount_id');
        $hotelId = JRequest::getInt('hotel_id');
        $post['discount_room_ids']	= implode(',', $post['discount_room_ids']);
        $post['offer_ids']	= implode(',', $post['offer_ids']);
        $post['excursion_ids']	= implode(',', $post['excursion_ids']);

        if (!$model->save($post)){
            // Save the data in the session.
            $app->setUserState('com_jhotelreservation.edit.roomdiscount.data', $data);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($discountId), false));

            return false;
        }
        $recordId = $model->getState($this->context . '.discount_id');
        $post["discount_id"]= $recordId;
        $this->setMessage(JText::_('LNG_DISCOUNT_SAVED'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the row data in the session.
                $discountId = $model->getState($this->context .'.discount_id');
                $hotelId = $model->getState($this->context . '.hotel_id');
                $this->holdEditId($context, $discountId,$hotelId);
                $app->setUserState('com_jhotelreservation.edit.roomdiscount.data', null);
                // Redirect back to the edit screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view='. $this->view_item . $this->getRedirectToItemAppend($discountId), false));//. $this->view_item. $this->view_item . $this->getRedirectToItemAppend($discountId). &view=roomdiscount&layout=edit&discount_id=
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $discountId, $hotelId);
                $app->setUserState('com_jhotelreservation.edit.roomdiscount.data', null);
                // Redirect to the list screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
                break;
        }

    }

    /**
     * Method to handle the edit button
     * @param null $key
     * @param null $urlVar
     * @return bool
     */
    function edit($key = NULL, $urlVar = null)
    {
        $app = JFactory::getApplication();
        $context = 'com_jhotelreservation.edit.roomdiscount';
        $result = parent::edit();

        return true;
    }

    /**
     * Method to handle the new button
     * @return mixed
     */
    public function add()
    {
        $app = JFactory::getApplication();
        $context = 'com_jhotelreservation.edit.roomdiscount';
        $result = parent::add();

        //initialise variables
        if ($result) {
            $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=roomdiscount'.$this->getRedirectToItemAppend(), false));
        }

        return $result;
    }

    /**
     * cancel editing a record
     * @return void
     */
    function cancel($key= null)
    {
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));
        $msg = JText::_('LNG_OPERATION_CANCELLED',true);
        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=roomdiscounts', $msg );
    }
}