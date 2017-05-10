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


class JHotelReservationControllerEmail extends JControllerForm
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
        $model = $this->getModel('Email');
        $post = JRequest::get( 'post' );
        $data = JRequest::get( 'post' );
        $context  = 'com_jhotelreservation.edit.email';
        $task     = $this->getTask();
        $emailId = JRequest::getInt('email_id');
        $hotelId = JRequest::getInt('hotel_id');

        //$post['email_content'] = JRequest::getVar('email_content', '', 'post', 'string', JREQUEST_ALLOWRAW);

        if (!$model->save($post)){
            // Save the data in the session.
            $app->setUserState('com_jhotelreservation.edit.email.data', $post);
            $model->saveEmailContent($post);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($emailId), false));

            return false;
        }

        $this->setMessage(JText::_('LNG_SAVE_SUCCESS'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the row data in the session.
                $emailId = $model->getState($this->context . '.id');
                if(empty($post["email_id"]))
                	$post["email_id"] =  $emailId;
                $hotelId = $model->getState($this->context . '.hotel_id');
                $this->holdEditId($context, $emailId,$hotelId);
                $app->setUserState('com_jhotelreservation.edit.email.data', null);
                $model->saveEmailContent($post);

                // Redirect back to the edit screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($emailId).'&hotel_id='.$post['hotel_id'], false));
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $emailId, $hotelId);
                $app->setUserState('com_jhotelreservation.edit.email.data', null);
                if(empty($post["email_id"]))
                	$post["email_id"] = $emailId = $model->getState($this->context . '.id');
                $model->saveEmailContent($post);
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
         $context = 'com_jhotelreservation.edit.email';
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
        $context = 'com_jhotelreservation.edit.email';
        $post = JRequest::get('post');
        //$model = $this->getModel('Email');
        $emailId = JRequest::getVar('email_id', 0 ,'');

        $hotelId = JRequest::getInt('hotel_id');
        $result = parent::add();

        //initialise variables
        if ($result) {
          $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=email' . $this->getRedirectToItemAppend($emailId).'&hotel_id='.$post['hotel_id'], false));
        }

        return $result;
    }

    /**
     * Method to handle the delete button
     * return void
     */
    function delete()
    {
        $table = $this->getModel('Emails');
        $post = JRequest::get( 'post' );
        if( !isset($post['hotel_id']) )
            $post['hotel_id'] = 0;

        if ($table->remove()) {
            $msg = JText::_('LNG_EMAIL_HAS_BEEN_DELETED',true);
        } else {
            $msg = JText::_('LNG_ERROR_DELETE_EMAIL',true);
        }

        $this->setRedirect( 'index.php?option='.getBookingExtName().'view=emails&hotel_id='.$post['hotel_id'], $msg );
    }

    /**
     * cancel editing a record
     * @return void
     */
    function cancel($key = null)
    {
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));

        $post = JRequest::get( 'post' );
        $msg = JText::_('LNG_OPERATION_CANCELLED',true);
        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=emails&hotel_id='.$post['hotel_id'], $msg );

    }
}