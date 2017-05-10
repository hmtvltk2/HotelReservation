<?php
interface iPaymentProcessor
{
	public function initialize($data);
	public function getHtmlFields();
    public function getPaymentProcessorHtml();
    public function getPaymentDetails($paymentDetails, $amount, $cost);
    public function processTransaction($data);
    public function getPaymentGatewayUrl();
}
?>
