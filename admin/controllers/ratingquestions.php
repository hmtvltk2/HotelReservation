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

jimport('joomla.application.component.controlleradmin');

class JHotelReservationControllerRatingQuestions extends JControllerAdmin
{

    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->registerTask('saveOrderAjax','saveorder');
    }

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  object  The model.
     *
     * @since   1.6
     */
    public function getModel($name ='RatingQuestion', $prefix = 'JHotelReservationModel' , $config = array('ignore_request'=>true))
    {
        $model = parent::getModel($name, $prefix,$config);
        return $model;
    }

    /**
     * Method to delete a Rating Question
     */
    function deleteratingquestions(){
        $ratingQuestions = $this->getModel('ratingquestion');
        $msg = $ratingQuestions->deleteratingquestions();
        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=ratingquestions', $msg );
    }

    /**
     * Change Question Order
     */
    function changequestionorder(){
        $ratingQuestions = $this->getModel('ratingquestion');
        $msg = $ratingQuestions->changequestionorder();
        echo $msg;
        exit;
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @since   3.0
     */
    public function saveOrderAjax()
    {
        $pks = JRequest::getVar('cid', array(), '', 'array');
        $order =JRequest::getVar('order', array(), '', 'array');


        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel("RatingQuestions");

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        JFactory::getApplication()->close();
    }

    /**
     * Method to go back to the JHotelreservation Dashboard
     */
    function back() {
        $this->setRedirect('index.php?option='.getBookingExtName().'&task=hotelratings.menuhotelratings');
    }
}