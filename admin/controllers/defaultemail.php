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


class JHotelReservationControllerDefaultEmail extends JControllerForm
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
        $model = $this->getModel('DefaultEmail');
        $post = JRequest::get( 'post' );
        $data = JRequest::get( 'post' );

        $post['email_default_content'] = JRequest::getVar('email_default_content', '', 'post', 'string', JREQUEST_ALLOWHTML);

        $context  = 'com_jhotelreservation.edit.defaultemail';
        $task     = $this->getTask();
        $emailId = JRequest::getInt('email_default_id');


        if (!$model->save($post)){
            // Save the data in the session.
            $app->setUserState('com_jhotelreservation.edit.defaultemail.data', $data);
            $model->saveEmailContent($post);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($emailId), false));

            return false;
        }
        //$post["email_default_id"] = $model->_default_email_id;
        $this->setMessage(JText::_('LNG_EMAIL_SAVED'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the row data in the session.
                $emailId = $model->getState($this->context . '.id');
                $this->holdEditId($context, $emailId);
                $app->setUserState('com_jhotelreservation.edit.defaultemail.data', null);
                $model->saveEmailContent($post);

                // Redirect back to the edit screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($emailId), false));
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $emailId);
                $app->setUserState('com_jhotelreservation.edit.defaultemail.data', null);
                $model->saveEmailContent($post);

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
        $context = 'com_jhotelreservation.edit.defaultemail';
        $result = parent::edit();

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
        $context	= 'com_jhotelreservation.edit.defaultemail';

        $result = parent::add();
        // Initialise variables.
        if ($result) {
            $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=defaultemail&'.$this->getRedirectToItemAppend(), false));
        }
        return $result;
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
        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=defaultemails', $msg );

    }
}