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


class JHotelReservationControllerInvoice extends JControllerForm
{

    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    function __construct()
    {
        parent::__construct();
    }

    function edit($key = null , $urlVar= null)
    {
        $app = JFactory::getApplication();
        $context = 'com_jhotelreservation.edit.invoice';
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

    function createMontlyInvoices(){
        $model = $this->getModel('invoice');
        $model->createMonthlyInvoices();
        exit;
    }

    /**
     * save a record (and redirect to main page)
     * @return void
     */
    function save($key = null , $urlVar = null)
    {
        $model = $this->getModel('invoice');
        $task = $this->getTask();
        $post = JRequest::get( 'post' );
	    $post['agreed'] = isset($post['agreed'])?$post['agreed']:0;

        if ($model->store($post))
        {
            $msg = JText::_('LNG_INVOICE_SAVED', true);

                $this->setRedirect('index.php?option=' . getBookingExtName() . '&view=invoices&hotel_id=' . $post['hotel_id'], $msg);

        }
        else
        {
            $msg = "";
            JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_INVOICE',true));
            $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=invoice&layout=edit&invoiceId='.$post['invoiceId'].'&hotel_id='.$post['hotel_id'], '' );
        }

        switch($task){

            case 'apply':
                $msg = JText::_('LNG_INVOICE_SAVED', true);
                $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=invoice&layout=edit&invoiceId='.$post['invoiceId']. '&hotel_id=' . $post['hotel_id'], $msg );
                break;
            default:

                $this->setRedirect('index.php?option=' . getBookingExtName() . '&view=invoices&hotel_id=' . $post['hotel_id'], $msg);
        }

    }

    /**
     * Method to handle the agree & send button
     */
    function send(){
        $model = $this->getModel('Invoice');

        $post = JRequest::get( 'post' );

        if ($model->store($post))
        {
                $model->sendInvoice($post);
                $msg = JText::_('LNG_INVOICE_ISSUED', true);
                $this->setRedirect('index.php?option=' . getBookingExtName() . '&view=invoices&hotel_id=' . $post['hotel_id'], $msg);
        }
        else
        {
            $msg = "";
            JError::raiseWarning( 500, JText::_('LNG_ERROR_ISSUE_INVOICE',true));
            $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=invoices&invoiceId='.$post['invoiceId']. '&hotel_id=' . $post['hotel_id'], '' );
        }
    }

    /**
     *
     */
    function issueInvoices(){
        $model = $this->getModel('Invoice');
        $model->issueInvoices();
        exit;
    }

    /**
     * cancel editing a record
     * @return void
     */
    function cancel($key= null)
    {
        if(JRequest::getVar('task')=='cancel')
            $msg = JText::_('LNG_OPERATION_CANCELLED',true);
        $post 		= JRequest::get( 'post' );
        if( !isset($post['hotel_id']) )
            $post['hotel_id'] = 0;

        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=invoices&hotel_id='.$post['hotel_id'], $msg );
    }

    function back()
    {
        $post 		= JRequest::get( 'post' );
        if( !isset($post['hotel_id']) )
            $post['hotel_id'] = 0;
        $this->setRedirect('index.php?option='.getBookingExtName().'&view=invoices&hotel_id='.$post['hotel_id']);

    }

    function exportInvoiceCsv()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel();
        $model->exportInvoiceCsv();
        exit();
    }
}
