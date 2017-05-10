<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

define('UC_4B_URL_DEVEL',      'https://tpv2.4b.es/simulador/teargral.exe');
define('UC_4B_URL_PROD',       'https://tpv.4b.es/tpvv/teargral.exe');
define('UC_4B_IP_DEVEL',       '194.224.159.57');
define('UC_4B_IP_PROD',        '194.224.159.47');
define('UC_4B_STATUS_DEVEL',   0);
define('UC_4B_STATUS_PROD',    1);
define('UC_4B_LANGUAGE',       'es');



class JHotelReservationModelProcessorFourB extends JModelLegacy{
	
	
	function __construct()
	{
		$this->modelVariables = new JHotelReservationModelVariables();
		parent::__construct();
	}
	
	function processFourBDetails(){
	
		$data = array();
		JHotelReservationModelVariables::writeMessage(" call function process4BDetails() ");
		$post = JRequest::get( 'GET' );
	
		$confirmationId   = $post['order'];
		$data['order_id']   = $post['order'];
		$data['store']   = $post['store'];
		/*$data['pszTxnDate']   = $post['pszTxnDate'];
		$data['tipotrans']   = $post['tipotrans'];
		$data['coderror']   = $post['coderror'];
		$data['deserror']   = $post['deserror'];
		$data['MAC']   = $post['MAC'];*/
	
		JHotelReservationModelVariables::writeMessage("Confirmation ID: ".$confirmationId);
		JHotelReservationModelVariables::writeMessage("store ID: ".$data['store']);
		list($errors, $message) = $this->validatePetition($data);

		if ($errors) {
			echo $message;
			JHotelReservationModelVariables::writeMessage("Error validating petition".$message);
		}
		else {
	
			try
			{
				JRequest::setVar( 'tip_oper',4);
				$this->modelVariables = new JHotelReservationModelVariables();
				if(!$this->modelVariables->load($confirmationId, null, null)){
					JHotelReservationModelVariables::writeMessage("Cannot load variables model");
					break;
				}
				// If no errors, show order details to the 4b petition.
				$table = $this->getTable('confirmations');
				$table->load($confirmationId);
				
				
				$price = round( $table->total,2)*100;
				$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->modelVariables->month_start, $this->modelVariables->day_start,$this->modelVariables->year_start )	);
				$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->modelVariables->month_end, $this->modelVariables->day_end,$this->modelVariables->year_end )	);
				
				/**
				 * Inform the platform: To simplify the integration, we will always tell
				 * the payment platform that we have one single item, with the total price.
				 * The name of the store as saved in settings is used as the name of this
				 * global product.
				 */
				$currency = '978'; // EUR currency code.
				$items    = 1;     // Read big comment above.
				$shop     = JText::_('LNG_RESERVATION',true)." : ".$this->modelVariables->itemAppSettings->company_name.' >> '.$datas.' | '.$datae;
				JHotelReservationModelVariables::writeMessage("price ".$currency.$price.$shop);
				
				echo 'M'.$currency.$price."\r\n";
				echo $items."\r\n";
				echo $confirmationId."\r\n";    // Reference number.
				echo $shop."\r\n";  // Description.
				echo '1'."\r\n";    // Quantity.
				echo $price."\r\n"; // Price.
				exit;
			}
			catch( Exception $e )
			{
				JHotelReservationModelVariables::writeMessage("there was an error in processing process4BDetails()= ");
				
				print_r($e);
			}
		}
	}
	

	
	
	function validatePetition($data) {
		$errors  = FALSE;
		$message = 'No error';
		$configuration = $this->getProcessorConfiguration();
		$serverIP = $_SERVER['REMOTE_ADDR'];
		
		if (!is_numeric($data['order_id'])) {
			$errors  = TRUE;
			$message = 'Order ID not numeric.';
		}
		else if ($data['store'] != $configuration->paymentprocessor_password) {
			$errors  = TRUE;
			$message = 'Invalid Store Code received.';
		}
		else if ($configuration->paymentprocessor_mode =='live') {
			if ($serverIP != UC_PASAT4B_IP_PROD) {
				$errors  = TRUE;
				$message = 'Invalid origin IP address.';
			}
		}
	
		return array($errors,$message);
		
	}
	function getProcessorConfiguration(){
		$query = " 	SELECT *
											FROM #__hotelreservation_paymentprocessors 
											WHERE is_available = 1 AND paymentprocessor_type = '".PROCESSOR_4B."'
											ORDER BY paymentprocessor_name
											";
		$this->_db->setQuery( $query );
		$configuration = $this->_db->loadObject();
		return $configuration;
	}
	
	function updatePayment($confirmationId){
		JHotelReservationModelVariables::writeMessage(" call function updatePayment() ");
		JRequest::setVar( 'tip_oper',5 );
		$modelVariables = new JHotelReservationModelVariables();
		try
		{
			$table = $this->getTable('confirmations');
			$table->load($confirmationId);
			if(!$modelVariables->load($confirmationId, null, null)){
				JHotelReservationModelVariables::writeMessage(JText::_('LNG_CANNOT_LOAD_MODEL',true)) ;
				exit;
			}
			if( !$modelVariables->changePaymentStatusPending($confirmationId, $table->total)){
				JHotelReservationModelVariables::writeMessage(JText::_('LNG_CANNOT_CHANGE_STATUS_PAYMENT',true)) ;
				exit;
			}
	
			$modelVariables->writeAllInfos();
			$modelConfirmations = new JHotelReservationModelConfirmations();
	
			if( !$modelConfirmations->store($modelVariables,false)  ){
				JHotelReservationModelVariables::writeMessage(JText::_('LNG_CANNOT_STORE_CONFIRMATION',true)) ;
				exit;
			}
	
	
			$modelVariables->addUser();
			$modelVariables->sendEmail($modelVariables->reservation_status);
			JHotelReservationModelVariables::writeMessage(" end function updatePayment() ");
				
			die;
		}
		catch( Exception $e )
		{
			JHotelReservationModelVariables::writeMessage("Error occured processing updatePayment() function. The transaction will be rolled back. ");
			JHotelReservationModelVariables::writeMessage("Error: ".$e);
			//dmp($e);
			//$modelVariables->rollbackTransaction();
			JRequest::setVar( 'tip_oper', 0 );
			$this->display();
			return;
		}
	}	
	
	function cancelPayment($confirmationId){
		JHotelReservationModelVariables::writeMessage('processing cancelPayment() function') ;
		try
		{
			JRequest::setVar( 'tip_oper',4);
			$modelVariables = new JHotelReservationModelVariables();
			if(	!$modelVariables->load(	$confirmationId, null, null)){
				JHotelReservationModelVariables::writeMessage('processErr') ;
				exit;
			}
			
			//canceled, sending email
			if( $modelVariables->itemAppSettings->is_email_notify_canceled_pending == true )
				$modelVariables->sendCancelPendingEmail($confirmationId, CANCELED_PENDING_ID);
			//~canceled, sending email
	
			JHotelReservationModelVariables::deletePendingConfirmation(
			$confirmationId,
			null,
			null,
			false,
			false
			);
		}
		catch( Exception $e )
		{
			JHotelReservationModelVariables::writeMessage("Error accured processing cancelPayment() function. ");
		}
		die;
	}
}