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


class JHotelReservationControllerInvoices extends JControllerAdmin
{
    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the view
     *
     * @param   boolean			If true, the view output will be cached
     * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController		This object to support chaining.
     * @since   1.6
     */
    public function display($cachable = false, $urlparams = false){
        JRequest::setVar( 'view', 'invoices' );
        parent::display();
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
    public function getModel($name='Invoice',$prefix='JHotelReservationModel' , $config = array('ignore_request'=>true))
    {
        $model = parent::getModel($name,$prefix,$config);
        return $model;
    }



    /**
     * Method to go back to the JHotelreservation Dashboard
     */
    function back() {
        $this->setRedirect('index.php?option='.getBookingExtName());
    }

    function exportInvoicesCsv(){
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('Invoices');
        $model->exportInvoicesCsv();
        exit();

    }
}
