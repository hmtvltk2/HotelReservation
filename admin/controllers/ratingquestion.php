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


class JHotelReservationControllerRatingQuestion extends JControllerForm
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
        $model = $this->getModel('RatingQuestion');
        $post = JRequest::get( 'post' );
        $context  = 'com_jhotelreservation.edit.ratingquestion';
        $task     = $this->getTask();
        $airlineId = JRequest::getInt('id');

        if (!$model->save($post)){
            // Save the data in the session.
            $app->setUserState('com_jhotelreservation.edit.ratingquestion.data', $post);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($airlineId), false));

            return false;
        }
        $post["review_question_id"] = $model->setState($this->context.'.review_question_id');
        $airlineId =  $post["review_question_id"];
        $model->saveRatingQuestions($post);
        $this->setMessage(JText::_('LNG_SAVE_SUCCESS'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the row data in the session.
                //$airlineId = $model->getState($this->context . '.review_question_id');
                $this->holdEditId($context, $airlineId);
                $app->setUserState('com_jhotelreservation.edit.airline.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($airlineId), false));
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $airlineId);
                $app->setUserState('com_jhotelreservation.edit.ratingquestion.data', null);
                // Redirect to the list screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
                break;
        }

    }


    /**
     * Method to Edit e Record
     * @param null $key
     * @param null $urlVar
     */
    function edit($key = null, $urlVar=null){
        $app = JFactory::getApplication();
        $context = 'com_jhotelreservation.edit.ratingquestion';
        $result = parent::edit();

        return true;
    }


    /**
     * function to add a record currency
     * @param null $key
     * @param null $urlVar
     * @return mixed
     */
    function add()
    {
        $app = JFactory::getApplication();
        $context = 'com_jhotelreservation.edit.ratingquestion';
        $result = parent::add();

        $airporttransfertypeId = JRequest::getVar('review_question_id', 0 ,'');


        //initialise variables
        if ($result) {
            $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=ratingquestion' . $this->getRedirectToItemAppend($airporttransfertypeId), false));
        }

        return $result;
    }

    /**
     * cancel editing a record
     * @return void
     */
    public function cancel($key = null)
    {
        $msg = JText::_('LNG_OPERATION_CANCELLED',true);
        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=ratingquestions', $msg );
    }
}
