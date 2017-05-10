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


class JHotelReservationControllerCurrency extends JControllerForm
{

    function __construct()
    {
        parent::__construct();
    }



    /**
     * save a record (and redirect to main page)
     * @return void
     */
    function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $app      = JFactory::getApplication();
        $model = $this->getModel('Currency');
        $post = JRequest::get( 'post' );
        $data = JRequest::get( 'post' );
        $context  = 'com_jhotelreservation.edit.currency';
        $task     = $this->getTask();
        $currencyId = JRequest::getInt('currency_id');

        if (!$model->save($post)){
            // Save the data in the session.
            $app->setUserState('com_jhotelreservation.edit.currency.data', $data);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($currencyId), false));

            return false;
        }

        $this->setMessage(JText::_('LNG_SAVE_SUCCESS'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the row data in the session.
                $currencyId = $model->getState($this->context . '.id');
                $this->holdEditId($context, $currencyId);
                $app->setUserState('com_jhotelreservation.edit.currency.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($currencyId), false));
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $currencyId);
                $app->setUserState('com_jhotelreservation.edit.currency.data', null);

                // Redirect to the list screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
                break;
        }

    }


    /**
     * Method to edit an existing record.
     *
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key
     * (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if access level check and checkout passes, false otherwise.
     *
     * @since   1.6
     */
    function edit( $key = null , $urlVar = null) {

        $app      = JFactory::getApplication();
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
        $context	= 'com_jhotelreservation.edit.currency';



        $result = parent::add();
        // Initialise variables.
        if ($result) {
            $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=currency&'.$this->getRedirectToItemAppend(), false));
        }

        return $result;
    }

    /**
     * cancel editing a record
     * @return void
     */
    public function cancel($key = null)
    {
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));

        // Initialise variables.
        $app = JFactory::getApplication();
        $context = 'com_jhotelreservation.edit.currency';
        $result = parent::cancel();
    }
}