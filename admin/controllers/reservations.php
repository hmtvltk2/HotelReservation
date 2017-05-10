<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 CMSJunkie,  All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport( 'joomla.application.component.controlleradmin' );
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'reservations'.DS.'view.html.php' );


/**
 * The Reservation List Controller
 *
 */
class JHotelReservationControllerReservations extends JControllerAdmin
{
	
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unsetDefault',	'setDefault');
	}
	/**
	 * Display the view
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.

	 */
	public function display($cachable = false, $urlparams = false)
	{
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
	 */
	public function getModel($name = 'Reservation', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Removes an reservation
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));

		// Get items to remove from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_('COM_JBUSINESSDIRECTORY_NO_RESERVATION_SELECTED',true));
		} else {
			// Get the model.
			$model = $this->getModel();
			
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			
			// Remove the items.
			if (!$model->delete($cid)) {
				$this->setMessage($model->getError());
			} else {
				$this->setMessage(JText::plural('COM_JHOTELRESERVATION_N_RESERVATIONS_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jhotelreservation&view=reservations');
	}
	
	
	function changeStatus()
	{
		$model = $this->getModel();
		$model->populateState();
	
		if (!$model->setStatus())	{
			$msg = JText::_( 'LNG_STATUS_CHANGED_SUCCESSFULLY' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_RESERVATION_STATUS',true);
		}
	
		$this->setMessage($msg);
		$this->setRedirect('index.php?option=com_jhotelreservation&view=reservations');
	}
	
	function changePaymentStatus()
	{
		
		$model = $this->getModel();
		$post = JRequest::get( 'post' );
		
		if ($model->setPaymentStatus($post["reservationId"], $post["paymentStatusId"]))	{
			$msg = JText::_( 'LNG_STATUS_CHANGED_SUCCESSFULLY' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_PAYMENT_STATUS',true);
		}
	
		$this->setMessage($msg);
		$this->setRedirect('index.php?option=com_jhotelreservation&view=reservations');
	}
	
	
	function sendEmail(){
		$model = $this->getModel();
		$post = JRequest::get( 'post' );
		
		if ($model->sendEmail($post["reservationId"]))	{
			$model->saveEmailCounterToChangeLog($post["reservationId"]);
			$msg = JText::_( 'LNG_EMAIL_SENT_SUCCESSFULLY' ,true);
		} else {
			$msg = JText::_('LNG_EMAIL_NOT_SENT',true);
		}
		
		$this->setMessage($msg);
		$this->setRedirect('index.php?option=com_jhotelreservation&view=reservations');
	}
	
	function sendClientInvoiceEmail(){
		$model = $this->getModel();
		$post = JRequest::get( 'post' );
	
		if ($model->sendClientInvoiceEmail($post["reservationId"]))	{
			$msg = JText::_( 'LNG_EMAIL_SENT_SUCCESSFULLY' ,true);
		} else {
			$msg = JText::_('LNG_EMAIL_NOT_SENT',true);
		}
	
		$this->setMessage($msg);
		$this->setRedirect('index.php?option=com_jhotelreservation&view=reservations');
	}
	
	
	public function cancelFromCsv(){
		$model = $this->getModel('reservations');
		$view  = $this->getView('Reservations');
		$view->setModel( $model, true );  // true is for the default model;
		$view->setLayout('import');
		$view->display('import');
	}	
	
	public function exportToCsv(){
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('reservations');
		$model->exportToCsv();
		exit;
	}
	
	
	public function batchCancelFromCsv(){
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$config = new JConfig();
		$dest = $config->tmp_path;
	
		$app      = JFactory::getApplication();
		$data = JRequest::get( 'post' );
		$model = $this->getModel("reservations");
		$config = new JConfig();
		$dest = $config->tmp_path;
	
		$dest = $model->uploadFile("csvFile", $data,$dest);
		if($model->batchCancelReservations($dest,$data["delimiter"]))
			$msg = JText::_( 'LNG_RESERVATION_CANCELED_SUCCESSFULLY' ,true);
		else 
			$msg = JText::_( 'LNG_RESERVATION_CANCELED_ERROR' ,true);
		
		$this->setRedirect('index.php?option=com_jhotelreservation&view=reservations',$msg);
	}
	
	public function printReservations(){
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$data = JRequest::get( 'post' );
		$model = $this->getModel('reservations');
		$model->populateState();
		$view  = $this->getView('Reservations');
		$view->setModel( $model, true );  // true is for the default model;
		$view->setLayout('print');
		JRequest::setVar('hidemainmenu',true);
		
		$view->display('print');
		
		
	}
	
}
