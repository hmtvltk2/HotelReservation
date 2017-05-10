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


class JHotelReservationControllerTax extends JControllerForm
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * save a record (and redirect to main page)
     * @return void
     */
    function save($key= null , $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $app      = JFactory::getApplication();
        $model = $this->getModel('Tax');
        $post = JRequest::get( 'post' );
        $data = JRequest::get( 'post' );
        $context  = 'com_jhotelreservation.edit.tax';
        $task     = $this->getTask();
        $taxId = JRequest::getInt('id');
        $hotelId = JRequest::getInt('hotel_id');

        if (!$model->save($post)){
            // Save the data in the session.
            $app->setUserState('com_jhotelreservation.edit.tax.data', $data);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($taxId), false));

            return false;
        }

        $this->setMessage(JText::_('LNG_TAX_SAVED'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the row data in the session.
                $taxId = $model->getState($this->context . '.id');
                $hotelId = $model->getState($this->context . '.hotel_id');
                $this->holdEditId($context, $taxId,$hotelId);
                $app->setUserState('com_jhotelreservation.edit.tax.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($taxId).'&hotel_id='.$post['hotel_id'], false));
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $taxId, $hotelId);
                $app->setUserState('com_jhotelreservation.edit.tax.data', null);
                // Redirect to the list screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend().'&hotel_id='.$post['hotel_id'], false));
                break;
        }

    }



    function edit($key = null, $urlVar = null)
    {

        $app = JFactory::getApplication();
        $context = 'com_jhotelreservation.edit.tax';
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
        $context = 'com_jhotelreservation.edit.tax';
        $post = JRequest::get('post');
        $taxId = JRequest::getVar('tax_id', 0 ,'');

        $hotelId = JRequest::getInt('hotel_id');
        $result = parent::add();

        //initialise variables
        if ($result) {
            $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=tax' . $this->getRedirectToItemAppend($taxId).'&hotel_id='.$post['hotel_id'], false));
        }

        return $result;
    }


    function state()
    {
        $model = $this->getModel('Tax');
        $get = JRequest::get( 'post' );
        if( !isset($get['hotel_id']) )
            $get['hotel_id'] = 0;

        if ($model->state()) {
            $msg = JText::_( '' ,true);
        } else {
            $msg = JText::_('LNG_ERROR_CHANGE_TAX_STATE',true);
        }


        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=taxes&hotel_id='.$get['hotel_id'], $msg );
    }

    /**
     * cancel editing a record
     * @return void
     */
    function cancel($key= null)
    {
        $msg = JText::_('LNG_OPERATION_CANCELLED',true);
        $post 		= JRequest::get( 'post' );
        if( !isset($post['hotel_id']) )
            $post['hotel_id'] = 0;
        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=taxes&hotel_id='.$post['hotel_id'], $msg );
    }
}