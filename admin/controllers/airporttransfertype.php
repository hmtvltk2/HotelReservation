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


class JHotelReservationControllerAirportTransferType extends JControllerForm
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
        $model = $this->getModel('Airporttransfertype');
        $post = JRequest::get( 'post' );
        $data = JRequest::get( 'post' );
        $context  = 'com_jhotelreservation.edit.airporttransfertype';
        $task     = $this->getTask();
        $airportTransferTypeId = JRequest::getInt('id');
        $hotelId = JRequest::getInt('hotel_id');

        if (!$model->save($post)){
            // Save the data in the session.
            $app->setUserState('com_jhotelreservation.edit.airporttransfertype.data', $data);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($airportTransferTypeId), false));

            return false;
        }
        $data["airport_transfer_type_id"] = $model->setState($this->context.'.airport_transfer_type_id');
        $airportTransferTypeId =  $data["airport_transfer_type_id"];
        $model->saveTranslations($data);

        $this->setMessage(JText::_('LNG_SAVE_SUCCESS'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the row data in the session.
                $hotelId = $model->getState($this->context . '.hotel_id');
                $this->holdEditId($context, $airportTransferTypeId,$hotelId);
                $app->setUserState('com_jhotelreservation.edit.airporttransfertype.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($airportTransferTypeId).'&hotel_id='.$post['hotel_id'], false));
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $airportTransferTypeId, $hotelId);
                $app->setUserState('com_jhotelreservation.edit.airporttransfertype.data', null);
                // Redirect to the list screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend().'&hotel_id='.$post['hotel_id'], false));
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
        $context = 'com_jhotelreservation.edit.airporttransfertype';
        $result = parent::edit();

        $urlVar = $app->getUserStateFromRequest('filter.hotel_id', 'hotel_id', '1', 'cmd');
        $recordId	= JRequest::getVar('cid', array(), '', 'array');
        $recordId = $recordId[0];
        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item
                . $this->getRedirectToItemAppend($recordId).'&hotel_id='.$urlVar, false
            )
        );

        return true;
    }

    /**
     * function to add a record currency
     * @param null $key
     * @param null $urlVar
     * @return mixed
     */
    function add($key = null, $urlVar = null)
    {
        $app		= JFactory::getApplication();
        $context	= 'com_jhotelreservation.edit.airporttransfertype';
        $post = JRequest::get('post');

        $airporttransfertypeId = JRequest::getVar('airport_transfer_type_id', 0 ,'');


        $result = parent::add();
        // Initialise variables.
        if ($result) {
            $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=airporttransfertype&'.$this->getRedirectToItemAppend($airporttransfertypeId).'&hotel_id='.$post['hotel_id'], false));
        }
        return $result;
    }

    /**
     * cancel editing a record
     * @return void
     */
    function cancel($key= null)
    {
        $post = JRequest::get( 'post' );
        if( !isset($post['hotel_id']) )
            $post['hotel_id'] = 0;
        $msg = JText::_('LNG_OPERATION_CANCELLED',true);
        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=airporttransfertypes&hotel_id='.$post['hotel_id'], $msg );
    }
}