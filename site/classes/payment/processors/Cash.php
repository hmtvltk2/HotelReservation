<?php 

class Cash implements IPaymentProcessor {
	
	var $type;
	var $name;
	
	public function initialize($data){
		if(isset($data->type))
			$this->type =  $data->type;
		if(isset($data->name))
			$this->name =  $data->name;	
	}
	
	public function getPaymentGatewayUrl(){
	
	}
	
	public function getPaymentProcessorHtml(){
		$html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
		<li>
		    ".JText::_('LNG_CASH_PROC_INFO',true)."
		    </li>
		</ul>";
		
		return $html;
	}
	
	public function getHtmlFields() {
		$html  = '';
		return $html;
	}
	
	public function processTransaction($data){
		$result = new stdClass();
		$result->transaction_id = 0;
		$result->amount =  $data->reservationData->appSettings->charge_only_reservation_cost==1? $data->cost: $data->total;
		$result->payment_date = date("Y-m-d");
		$result->response_code = 0;
		$result->confirmation_id = $data->confirmation_id;
		$result->currency=  $data->reservationData->hotel->hotel_currency;
		$result->processor_type = $this->type;
		$result->payment_status = JHP_PAYMENT_STATUS_PENDING;
		$result->status = PAYMENT_SUCCESS;
		
		return $result;
	}
	

	public function getPaymentDetails($paymentDetails, $amount, $cost){
		echo JText::_('LNG_PROCESSOR_CASH');
	}
}