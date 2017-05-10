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

class JHotelReservationControllerPaymentOptions extends JControllerLegacy
{
	function __construct()
	{
		$this->log = Logger::getInstance();
		parent::__construct();
	}
	
	function showPaymentOptions(){
		JRequest::setVar("view","paymentoptions");
		parent::display();
	}
	
	function applyDiscount(){
		$data = JRequest::get("post");
	
		UserDataService::setDiscountCode($data["discount_code"]);
	
		JRequest::setVar("view","paymentoptions");
		parent::display();
	}
	
	function processPayment(){
		
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$paymentMethod = JRequest::getVar("payment_method");
		$processorId = JRequest::getVar("processor_id",-1);
		
		if(!$appSettings->is_enable_payment){
			$paymentMethod="cash";
		}
		
		$paymentModel = $this->getModel("PaymentOptions");
		$reservationDetails = $paymentModel->getReservationDetails();
		$reservationDetails->paymentMethod = $paymentMethod;

		$confirmationModel = $this->getModel("Confirmation");
		$confirmationId = $confirmationModel->saveConfirmation($reservationDetails);
		$reservationDetails->confirmation_id = $confirmationId;
		if($confirmationId == -1){
			$this->setMessage(JText::_('LNG_NO_ROOMS_AVAILABLE',true));
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=paymentoptions', $msg));
			return;
		}   
		
		//set current reservation id for security check
		$componentName = getBookingExtName();
		JFactory::getApplication()->setUserState( "$componentName.reservation_id", $confirmationId);

		$processor = PaymentService::createReservationPaymentProcessor($paymentMethod,$processorId);
		$paymentDetails = $processor->processTransaction($reservationDetails);
		$paymentDetails->processor_id = $processorId;
		PaymentService::addPayment($paymentDetails);
	
		if($paymentDetails->status==PAYMENT_REDIRECT){
			$document = JFactory::getDocument();
			$viewType = $document->getType();
			$view = $this->getView("paymentoptions", $viewType, '', array('base_path' => $this->basePath, 'layout' => "redirect"));
			$view->paymentProcessor = $processor;
			$view->display("redirect");
		}		
		else if($paymentDetails->status==PAYMENT_IFRAME){
			$document = JFactory::getDocument();
			$viewType = $document->getType();
			$view = $this->getView("paymentoptions", $viewType, '', array('base_path' => $this->basePath, 'layout' => "iframe"));
			$view->paymentProcessor = $processor;
			$view->display("iframe");
		}else if($paymentDetails->status==PAYMENT_SUCCESS){
			$reservationDetails = $confirmationModel->getReservation($confirmationId);
			$confirmationModel->sendConfirmationEmail($reservationDetails);
			UserDataService::initializeReservationData();
			UserDataService::initializeExcursions();
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=confirmation&task=confirmation.viewConfirmation&reservationId='.$confirmationId, false));
		}else if($paymentDetails->status==PAYMENT_WAITING){
			$reservationDetails = $confirmationModel->getReservation($confirmationId);
			$confirmationModel->sendConfirmationEmail($reservationDetails);
			UserDataService::initializeReservationData();
			UserDataService::initializeExcursions();
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=confirmation&task=confirmation.viewConfirmation&reservationId='.$confirmationId, false));
		}else if($paymentDetails->status==PAYMENT_ERROR){
			$app = JFactory::getApplication();
			$app->enqueueMessage($paymentDetails->error_message, 'warning');
			BookingService::cancelReservation($confirmationId);
			JRequest::setVar("view","paymentoptions");
			parent::display();
		}
	}
	
	function processResponse(){
		$this->log->LogDebug("process response");
		$data = JRequest::get( 'post' );
		$this->log->LogDebug(serialize($data));
		
		$processorType = JRequest::getVar("processor");
		$processor = PaymentService::createPaymentProcessor($processorType);
		$paymentDetails = $processor->processResponse($data);
		
		//$this->processAutomaticResponse();
		if($paymentDetails->status == PAYMENT_CANCELED || $paymentDetails->status == PAYMENT_ERROR){
			PaymentService::updatePayment($paymentDetails);
			BookingService::cancelReservation($paymentDetails->confirmation_id);
			$msg=JText::_("LNG_BUCKAROO_".$paymentDetails->response_code);
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=paymentoptions', false),$msg);
		}else{
			if(empty($paymentDetails->confirmation_id)){
				$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=hotels&task=hotels.searchHotels', false));
			}
			else{
				$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=confirmation&reservationId='.$paymentDetails->confirmation_id, false));
				UserDataService::initializeReservationData();
			}
		}
	}
	
	function processPaymentResponse(){
		$this->log->LogDebug("process payment response");
		$data = JRequest::get( 'post' );
		$this->log->LogDebug(serialize($data));
	
		$processorType = JRequest::getVar("processor");
		$processor = PaymentService::createPaymentProcessor($processorType);
		$paymentDetails = $processor->processResponse($data);
	
		//$this->processAutomaticResponse();
		if($paymentDetails->status == PAYMENT_CANCELED || $paymentDetails->status == PAYMENT_ERROR){
			PaymentService::updateReservationPayment($paymentDetails);
			BookingService::cancelReservation($paymentDetails->confirmation_id);
			$msg = $paymentDetails->response_message;
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=paymentoptions', false),$msg);
		}else{
			PaymentService::updateReservationPayment($paymentDetails);

			$confirmationModel = $this->getModel("Confirmation");
			$reservationDetails = $confirmationModel->getReservation($paymentDetails->confirmation_id);
			EmailService::sendConfirmationEmail($reservationDetails, $sendMailOnlyToAdmin);
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=confirmation&reservationId='.$paymentDetails->confirmation_id, false));
			UserDataService::initializeReservationData();
			
			//check if hotels has more rooms available
			$hotelId = $reservationDetails->reservationData->userData->hotelId;
			$startDate = $reservationDetails->reservationData->userData->start_date;
			$endDate = $reservationDetails->reservationData->userData->end_date;
			$isHotelAvailable = HotelService::checkAvailability($hotelId, $startDate, $endDate);
			
			if(!$isHotelAvailable){
				EmailService::sendNoAvailabilityEmail($hotelId, $startDate, $endDate);
			}
		}
	}
	
	function processAutomaticResponse(){
		$this->log->LogDebug("process automatic response");
		$data = JRequest::get('post');
		$this->log->LogDebug(serialize($data));
		
		$processorType = JRequest::getVar("processor");
		$processor = PaymentService::createPaymentProcessor($processorType);
		$paymentDetails = $processor->processResponse($data);
		$this->log->LogDebug("Payment Details: ".serialize($paymentDetails));
		
		if(empty($paymentDetails->confirmation_id)){
			return;
		}
		
		$intialPaymentDetails = PaymentService::getConfirmationPaymentDetails($paymentDetails->confirmation_id);
		$this->log->LogDebug("Initial payment details: ".serialize($intialPaymentDetails));
		
		if($intialPaymentDetails->payment_status==JHP_PAYMENT_STATUS_PAID){
			return;
		}
		
		//prevent e-mails to be send again to hotels and customers
		if($intialPaymentDetails->payment_status == $paymentDetails->payment_status){
			header("HTTP/1.1 200 OK");	
			return;
		}
		
		//check if the response is a reponse for a waiting transaction
		$sendMailOnlyToAdmin = $intialPaymentDetails->payment_status == JHP_PAYMENT_STATUS_WAITING && $paymentDetails->payment_status == JHP_PAYMENT_STATUS_PAID;
		$this->log->LogDebug("Send only to admin ".serialize($sendMailOnlyToAdmin));
		
		
		PaymentService::updatePayment($paymentDetails);
	
		if($paymentDetails->status == PAYMENT_CANCELED || $paymentDetails->status == PAYMENT_ERROR){
			BookingService::cancelReservation($paymentDetails->confirmation_id);
		}else{
			$confirmationModel = $this->getModel("Confirmation");
			$reservationDetails = $confirmationModel->getReservation($paymentDetails->confirmation_id);
			EmailService::sendConfirmationEmail($reservationDetails, $sendMailOnlyToAdmin);

			//check if hotels has more rooms available
			$hotelId = $reservationDetails->reservationData->userData->hotelId;
			$startDate = $reservationDetails->reservationData->userData->start_date;
			$endDate = $reservationDetails->reservationData->userData->end_date;
			$isHotelAvailable = HotelService::checkAvailability($hotelId, $startDate, $endDate);
				
			if(!$isHotelAvailable){
				EmailService::sendNoAvailabilityEmail($hotelId, $startDate, $endDate);
			}
		}
		
		
		//http_response_code(200);
		header("HTTP/1.1 200 OK");	
	}
	
	function processCancelResponse(){
		$this->log->LogDebug("process cancel response ");
		$data = JRequest::get( 'get' );
		$this->log->LogDebug(serialize($data));
		$this->setMessage(JText::_('LNG_OPERATION_CANCELED_BY_USER',true));
		BookingService::cancelReservation($data["confirmationId"]);
	
		$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=paymentoptions', $msg));
	}
	
	function back(){
		UserDataService::removeLastRoom();
		$userData = UserDataService::getUserData();
		if($userData->hotelId>0){
			$hotel = HotelService::getHotel($userData->hotelId);
			$link = JHotelUtil::getHotelLink($hotel);
		}
		else{ 
			$link = (JRoute::_('index.php?option=com_jhotelreservation&view=excursionslisting&task=excursionslisting.searchExcursions', false));
			UserDataService::initializeExcursions();
		}		$this->setRedirect($link);
	}

    function back2(){

        $data = '';
        foreach($_SESSION['userData']->reservedItems as $reservedItems){
            $data = $reservedItems;
        }
        $userData = UserDataService::getUserData();
        $hotel = $userData->hotelId;
        $current = JRequest::getVar("current");
        $reservedItems = JRequest::getVar("reservedItems");
        $hotelId = JRequest::getVar("hotel_id");
        $link = (JRoute::_('index.php?option=com_jhotelreservation&view=guestDetails&task=guestDetails.showGuestDetails&hotelId='.$hotel.'&reservedItems='.$data, false));
        $this->setRedirect($link);
    }
}