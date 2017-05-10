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

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');


class JHotelReservationModelInvoice extends JModelAdmin
{

    function __construct()
    {
        parent::__construct();
    }


    /**
     * @var		string	The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_JHOTELRESERVATION_INVOICE';

    /**
     * Model context string.
     *
     * @var		string
     */
    protected $_context		= 'com_jhotelreservation.invoice';


    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       2.5
     */
    public function getTable($type = 'Invoices', $prefix = 'JTable' , $config = array())
    {
        return JTable::getInstance($type,$prefix,$config);
    }

    /**
     * Method to get the record form.
     *
     * @param       array   $data           Data for the form.
     * @param       boolean $loadData       True if the form is to load its own data (default case), false if not.
     * @return      mixed   A JForm object on success, false on failure
     * @since       2.5
     */
    public function getForm($data = array(), $loadData = true)
    {
        //Get the form
        $form = $this->loadForm('com_jhotelreservation.invoice','invoice',array('control'=> 'jform','load_data' => $loadData));
        if(empty($form))
        {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return      mixed   The data for the form.
     * @since       2.5
     */
    protected function loadFormData()
    {
        //Check the session for previously entered form data
        $data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.invoice.data' ,array());
        if (empty($data))
        {
            $data = $this->getItem();
        }
        return $data;
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication('administrator');

        // Load the User state.
        $pk = (int) JRequest::getInt('id',0,'');
        if(!$pk)
            $pk = (int) JRequest::getInt('invoiceId', 0 ,'');
        $this->setState('invoice.invoiceId', $pk);

        if (!($hotelId = $app->getUserState('com_jhotelreservation.edit.invoice.hotel_id'))) {
            $hotelId = JRequest::getInt('hotel_id', '0');
            //dmp("a: ".$hotelId);
        }
        $app->setUserState('com_jhotelreservation.edit.invoice.hotel_id',$hotelId);

        $app->setUserState('com_jhotelreservation.edit.invoice.invoiceId',$pk);

        $this->setState('invoice.hotel_id', $hotelId);

    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object	A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     */
    protected function canDelete($record)
    {
        return true;
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object	A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     */
    protected function canEditState($record)
    {
        return true;
    }

    /**
     * Method to create mounthly invoices
     */
    function createMonthlyInvoices(){
        echo ("Creating Monthly Invoices");
        $hotelTable = $this->getTable('Hotels',"Table");
        $confirmationsTable = $this->getTable('confirmations',"Table");

        if(JRequest::getVar('date')!=''){
            $date = JRequest::getVar('date');
        }
        else{
            $date = date("d-m-Y");
        }


        $startInvoiceDate = date("Y-m-d", mktime(00, 00, 00, date('m',strtotime( $date)), 01));
        $endInvoiceDate  = date("Y-m-d", mktime(23, 59, 59, date('m',strtotime($date))+1, 00));

        $startDate = date("Y-m-d", mktime(00, 00, 00, date('m',strtotime( $date))-1, 01));
        $endDate  = date("Y-m-d", mktime(23, 59, 59, date('m',strtotime( $date)), 00));

        //get all invoices for current month that was generated for previous month.
        $hotels = $hotelTable->getAllHotelsWithoutMonthlyInvoice($startInvoiceDate, $endInvoiceDate);
        foreach($hotels as $hotel){
            try	{
                //save hotel invoice
                $invoice = new stdClass();
                $invoice->date = date("Y-m-d",strtotime( $date));
                $invoice->hotelId = $hotel->hotel_id;
                $invoiceDetails = array();

                $this->storeInvoice($invoice);
                $reservations = $confirmationsTable->getHotelMonthlyReservations($hotel->hotel_id, $startDate, $endDate);
                
                echo ("Create invoice for hotel ".$hotel->hotel_name.":".$hotel->hotel_id);
                $commission = 0;
                $amount = 0;
                if(isset($reservations) && is_array($reservations)){
                    foreach($reservations as $reservation){
                        $invoiceDetail = new stdClass();
                        $invoiceDetail->invoiceId = $invoice->id;
                        $invoiceDetail->reservationId=$reservation->confirmation_id;
                        $invoiceDetail->name= $reservation->first_name.' '.$reservation->last_name;
                        $invoiceDetail->arrival= $reservation->start_date;
                        $invoiceDetail->departure= $reservation->end_date;
                        $invoiceDetail->voucher= isset($reservation->voucher)?$reservation->voucher:'';
                        $invoiceDetail->status= 0;
                        $reservationCost = 0;
                        $extrasCommission = 0;
                        
                        if(isset($hotel->reservation_cost_val)){
                            $reservationCost = $hotel->reservation_cost_val;
                        }
                        if($reservation->offer_id!=0)
                            $reservationCost = $reservation->offer_reservation_cost_val;
                        $invoiceDetail->amount= $reservation->total - $reservationCost;
                        $reservationAmount =  $invoiceDetail->amount; 
                        $reservationCommision = $hotel->commission;
                        if($reservation->offer_id!=0)
                            $reservationCommision = $reservation->offer_commission;
	                    //Extras included in a resevation which have a commission defined will be added
	                    // to the resevation Commission
	                    if(isset($reservation->extra_commission) && $reservation->extra_commission>=0){
	                    	//subtract the 
	                    	$reservationAmount -= $reservation->extra_option_price;
	                    	// extras commission will apply to the extras amount
		                    $extrasCommission =  round($reservation->extra_option_price * ($reservation->extra_commission/100),2);
	                    }

                        $invoiceDetail->initialAmount= $invoiceDetail->amount;
                        $invoiceDetail->commission =$reservationCommision;

                        $invoiceDetail->commissionAmount = $reservationAmount * ($reservationCommision/100);
                        $invoiceDetail->commissionAmount += $extrasCommission;
                        $invoiceDetail->commissionAmount = round($invoiceDetail->commissionAmount,2);

                        $amount += $invoiceDetail->amount;
                        $commission += $invoiceDetail->commissionAmount;

                        $row = $this->getTable("InvoiceDetails","JTable");
                        if (!$row->bind($invoiceDetail))
                        {
                            throw( new Exception($this->_db->getErrorMsg()) );
                            $this->setError($this->_db->getErrorMsg());
                        }

                        // Make sure the record is valid
                        if (!$row->check())
                        {
                            throw( new Exception($this->_db->getErrorMsg()) );
                            $this->setError($this->_db->getErrorMsg());
                        }

                        // Store the web link table to the database
                        if (!$row->store())
                        {
                            throw( new Exception($this->_db->getErrorMsg()) );
                            $this->setError($this->_db->getErrorMsg());
                        }
                        $invoiceDetail->id = $this->_db->insertid();
                        $invoiceDetails[]= $invoiceDetail;
                    }

                    $invoice->commissionAmount = round($commission,2);
                    $invoice->reservationAmount = round($amount,2);
                    //do not apply vat for Germany and Belgium
                    if($hotel->country_id==20 || $hotel->country_id==54){
                        $invoice->amount = round($commission ,2);
                    } else if($hotel->country_id==161){
                        $invoice->amount = round(($commission + $commission * VAT_HOLLAND/100),2);
                    }else{
                        $invoice->amount = round(($commission + $commission * VAT/100),2);
                    }
                    
                    $this->storeInvoice($invoice);
                    $invoice->invoiceDetails = $invoiceDetails;

                    $this->issueInvoice($invoice, 0);
                }
            }catch (Exception $ex){
                //TODO threat exception
                dmp($ex);
            }
        }
    }

    /**
     * Method to store an invoice
     * @param $invoice
     * @return mixed
     * @throws Exception
     */
    function storeInvoice($invoice){
        $invoiceTable = $this->getTable("Invoices","JTable");
        if (!$invoiceTable->bind($invoice))
        {
            throw( new Exception($this->_db->getErrorMsg()) );
            $this->setError($this->_db->getErrorMsg());
        }

        // Make sure the record is valid
        if (!$invoiceTable->check())
        {
            throw( new Exception($this->_db->getErrorMsg()) );
            $this->setError($this->_db->getErrorMsg());
        }

        // Store the web link table to the database
        if (!$invoiceTable->store())
        {
            throw( new Exception($this->_db->getErrorMsg()) );
            $this->setError($this->_db->getErrorMsg());
        }

        if($this->_db->insertid())
            $invoice->id = $this->_db->insertid();
        //save invoice details

        return $invoice;
    }

    /**
     *  Method to send an invoice after it is issued(Method: issueInvoice)
     */
    function sendInvoice($data){
        $invoiceTable = $this->getTable("Invoices","JTable");
        $invoice = $invoiceTable->getInvoice($data["invoiceId"]);
        $invoice->approvalDate = date("Y-m-d");
        $invoice->approvalName = $data["approvalName"];

        return $this->issueInvoice($invoice, 1);
    }


    /**
     *
     * @param $hotel
     * @param $email
     * @param $status
     * @return bool
     */
    function sendInvoiceEmail($hotel, $email, $status)
    {

        $mode		 = 1 ;//html mode enabled
        $ret = true;
        if($status ==0){//send booking list email
        	$sendToEmail = $hotel->email; 
        	if(!empty($hotel->booking_list_email))
        		$sendToEmail =  $hotel->booking_list_email; 
        	
            $ret = EmailService::sendEmail(
                $email->company_email,
                $email->company_name,
                $email->company_email,
                $sendToEmail,
                null,
                null,
                $email->subject,
                $email->content,
                $mode
            );
        }else{//send invoice email 
            $appSettings = JHotelUtil::getApplicationSettings();
            $emailAddress='';
            if($appSettings->send_invoice_to_email)
                $emailAddress = $appSettings->invoice_email;
            else
                $emailAddress = $hotel->email;
            $ret = EmailService::sendEmail(
                $email->company_email,
                $email->company_name,
                $email->company_email,
                $emailAddress,
                null,
                null,
                $email->subject,
                $email->content,
                $mode
            );
        }

        return $ret;
    }

    function issueInvoices(){
    	$invoiceTable = $this->getTable("Invoices");
    	$invoices = $invoiceTable->getOpenInvoices();
    
    	foreach($invoices as $invoice){
    		$invoice->approvalDate = date("Y-m-d");
    		//dmp($invoice);
    		$this->issueInvoice($invoice, 1);
    	}
    }

    /**
     * Method to issue an invoice
     * @param $invoice
     * @param $status
     * @return bool
     * @throws Exception
     */
    function issueInvoice($invoice, $status){
        $invoice->status = $status;
        $hotelTable = $this->getTable('Invoices');
        $hotel = $hotelTable->getHotel($invoice->hotelId);

        $email = $this->prepareInvoiceEmail($hotel, $invoice);
        if(!isset($email))
            return false;

        $invoice->content = $email->content;
        $this->storeInvoice($invoice);

        $result = true;
        if($invoice->reservationAmount >0){
            $result = $this->sendInvoiceEmail($hotel, $email, $status);
        }

        return $result;
    }

    /**
     * Method to prepare an Invoice Email before it is sent
     * @param $hotel
     * @param $invoice
     * @return stdClass
     */
    function prepareInvoiceEmail($hotel, $invoice)
    {
        $invoiceTable = $this->getTable("Invoices","JTable");

        
        $email = new stdClass();
        $template = "Invoice Email";
        if(isset($invoice->invoiceDetails))
            $template = "Bookings List";
       
       	$templ = EmailService::getEmailTemplate(null,$invoice->hotelId,$template);
        
        if( $templ ==null ){
            echo (("<span class='red'>No template found for hotel: ".$hotel->hotel_name."</span>"));
            return null;
        }

        $applicationSettings = $invoiceTable->getAppSettings();

        $email->content = $this->prepareEmail($hotel, $invoice, $templ->email_content, $applicationSettings);
        $email->subject = $templ->email_subject;

        $email->company_email = $applicationSettings->company_email;
        $email->company_name = $applicationSettings->company_name;
        
     
        return $email;
    }


    /**
     * Method to get a menu item.
     *
     * @param   integer	The id of the menu item to get.
     *
     * @return  mixed  Menu item data object on success, false on failure.
     */
    public function getItem($itemId = null)
    {
        $itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('invoice.invoiceId');

        $false	= false;

        // Get a menu item row instance.
        $table = $this->getTable("Invoices","JTable");

        // Attempt to load the row.
        $return = $table->load($itemId);

        // Check for a table object error.
        if ($return === false && $table->getError())
        {
            $this->setError($table->getError());
            return $false;
        }

        $properties = $table->getProperties(1);
        $value = JArrayHelper::toObject($properties, 'JObject');

        $invoiceDetailsTable = $this->getTable('InvoiceDetails','JTable');
        $value->details = $invoiceDetailsTable->getInvoiceDetails($itemId);

        return $value;
    }


    /**
     * Get Hotel Id based on selection in the Invoices View
     */
    function getHotelId()
    {
        $hotel_id = JRequest::getVar('hotel_id',  0, '');
        $this->setHotelId($hotel_id);
        return $hotel_id;
    }

    function setHotelId($hotel_id)
    {
        // Set id and wipe data
        $this->_hotel_id	= $hotel_id;
    }

    /**
     * Function to get the hotel data based on hotel_id
     * @return mixed
     */
    function getHotel()
    {
        $app		= JFactory::getApplication();
        $hotelId = $app->getUserStateFromRequest('filter.hotel_id', 'hotel_id', '1', 'cmd');
        $hotel = JHotelUtil::getHotel($hotelId);
        return $hotel;
    }


    /**
     * Method to Store Invoice details and status
     * @param $data
     * @return bool
     */
    function store($data){
        $result = true;
        $ids = $data["detailIds"];
        $statuses= $data["detailStatus"];
	    $isNew = empty($data["invoiceId"]);


        $amount = 0;
        $commission= 0;
        $hotelTable = $this->getTable('Invoices','JTable');

	    $changes  = '';
	    $invoiceId = 0;
	    if(!$isNew)
		    $invoiceId = JHotelUtil::getStringIDConfirmation($data["invoiceId"]);


        for($i=0;$i<count($ids);$i++){
            $status = $statuses[$i];
            $invoiceDetailsTable = $this->getTable("InvoiceDetails","JTable");
            $invoiceDetail = $invoiceDetailsTable->getInvoiceDetail($ids[$i]);

	        $isNewInvoiceDetails = empty($ids[$i]);

	        if(!$isNew  && !$isNewInvoiceDetails){
		        $changes = $this->checkInvoiceDetailsChanges($invoiceDetail,$data,$changes,$status,$invoiceId);
	        }

            $invoiceDetail->status = $status;

            if($status == 1){
                $invoiceDetail->amount = $invoiceDetail->initialAmount;
                $invoiceDetail->commissionAmount = $invoiceDetail->amount *($invoiceDetail->commission/100);
            }else if($status == 2){
                $invoiceDetail->amount = 0;
                $invoiceDetail->commissionAmount =0;
            }else if($status ==3){
                $invoiceDetail->amount = $data["newamount-".$invoiceDetail->id];
                $invoiceDetail->commissionAmount = $invoiceDetail->amount *($invoiceDetail->commission/100);
            }

            $amount += $invoiceDetail->amount;
            $commission += $invoiceDetail->commissionAmount;

            try {
                if (!$invoiceDetailsTable->bind($invoiceDetail))
                {
                    throw( new Exception($this->_db->getErrorMsg()) );
                    $this->setError($this->_db->getErrorMsg());
                }

                // Make sure the record is valid
                if (!$invoiceDetailsTable->check())
                {
                    throw( new Exception($this->_db->getErrorMsg()) );
                    $this->setError($this->_db->getErrorMsg());
                }

                // Store the web link table to the database
                if (!$invoiceDetailsTable->store())
                {
                    throw( new Exception($this->_db->getErrorMsg()) );
                    $this->setError($this->_db->getErrorMsg());
                }
            }catch( Exception $ex ){
                dmp($ex);
                //exit();
                return false;
            }
        }
	    

        $invoiceTable = $this->getTable("Invoices","JTable");
        $invoice = $invoiceTable->getInvoice($data["invoiceId"]);

	    if(!$isNew){
		    $status = $data["agreed"];
		    $changes = $this->checkInvoiceDetailsChanges($invoice,$data,$changes,$status,$invoiceId,false);
		    //changes only for a single invoice
		    if(!empty($changes))
			    $this->sendEmailInvoiceDetailsChangeLog($changes);
	    }

        $invoice->approvalName = $data["approvalName"];
        $invoice->agreed = $data["agreed"];
        $invoice->commissionAmount = round($commission,2);
        $invoice->reservationAmount = round($amount,2);
        
        $hotel = $hotelTable->getHotel($invoice->hotelId);
        //no VAT for Germany and Belgium
        if($hotel->country_id==20 || $hotel->country_id==54){
            $invoice->amount = round($commission ,2);
        } else if($hotel->country_id==161){
            //different VAT for Holland
            $invoice->amount = round(($commission + $commission * VAT_HOLLAND/100),2);
        }else {
            $invoice->amount = round(($commission + $commission * VAT/100),2);
        }



        try {
            if (!$invoiceTable->bind($invoice))
            {
                throw( new Exception($this->_db->getErrorMsg()) );
                $this->setError($this->_db->getErrorMsg());
            }

            // Make sure the record is valid
            if (!$invoiceTable->check())
            {
                throw( new Exception($this->_db->getErrorMsg()) );
                $this->setError($this->_db->getErrorMsg());
            }

            // Store the web link table to the database
            if (!$invoiceTable->store())
            {
                throw( new Exception($this->_db->getErrorMsg()) );
                $this->setError($this->_db->getErrorMsg());
            }
        }catch( Exception $ex ){
            dmp($ex);
            //exit();
            return false;
        }
        //exit;
        return $result;
    }


	protected function setInvoiceStatusLabel($status){
		$statusLabel = '';

		switch($status){
			case 1 :
				$statusLabel = JText::_('LNG_AGREED',true);
				break;
			case 2:
				$statusLabel = JText::_('LNG_NO_SHOW_NO_CHARGE',true);
				break;
			case 3:
				$statusLabel = JText::_('LNG_NO_SHOW_CANCELATION',true);
				break;
		}

		return $statusLabel;
	}

    /**
     * Method to prepare Email template
     * to send with an invoice
     * @param $hotel
     * @param $invoice
     * @param $templEmail
     * @param $appSettings
     * @return string
     */
    function prepareEmail($hotel, $invoice, $templEmail, $appSettings)
    {

        $invoiceHotelDetails = $hotel->hotel_name." <br> ".$hotel->hotel_address." <br> ".$hotel->hotel_city.", ".$hotel->hotel_county." <br> ".$hotel->country_name;

        $invoiceFields = $this->generateInvoiceFieldsHTML($invoice);

        $bookingsList = $this->generateBookingsListHTML($invoice);

        $templEmail = str_replace("[company_logo]", "<img src='".JURI::root().PATH_PICTURES.$appSettings->logo_path."' alt='logo'>",				$templEmail);

        $templEmail = str_replace(htmlspecialchars(EMAIL_INVOICE_DATE),				JHotelUtil::convertToFormat($invoice->date),				$templEmail);
        $templEmail = str_replace(EMAIL_INVOICE_DATE, 								JHotelUtil::convertToFormat($invoice->date),				$templEmail);

        $templEmail = str_replace(htmlspecialchars(EMAIL_INVOICE_NUMBER), 			$invoice->id,				$templEmail);
        $templEmail = str_replace(EMAIL_INVOICE_NUMBER, 							$invoice->id,				$templEmail);

        $templEmail = str_replace(htmlspecialchars(EMAIL_HOTEL_NUMBER), 			$hotel->hotel_number,		$templEmail);
        $templEmail = str_replace(EMAIL_HOTEL_NUMBER, 								$hotel->hotel_number,		$templEmail);


        $templEmail = str_replace(htmlspecialchars(EMAIL_INVOICE_HOTEL_DETAILS),	$invoiceHotelDetails, 		$templEmail);
        $templEmail = str_replace(EMAIL_INVOICE_HOTEL_DETAILS,						$invoiceHotelDetails, 		$templEmail);

        $templEmail = str_replace(htmlspecialchars(EMAIL_BOOKINGS_LIST),			$bookingsList, 		$templEmail);
        $templEmail = str_replace(EMAIL_BOOKINGS_LIST,								$bookingsList, 		$templEmail);

        $templEmail = str_replace(htmlspecialchars(EMAIL_INVOICE_FIELDS),			$invoiceFields,				$templEmail);
        $templEmail = str_replace(EMAIL_INVOICE_FIELDS,								$invoiceFields, 			$templEmail);

        $templEmail = str_replace(htmlspecialchars(EMAIL_COMPANY_NAME),				$appSettings->company_name,	$templEmail);
        $templEmail = str_replace(EMAIL_COMPANY_NAME,								$appSettings->company_name,	$templEmail);

        return "<html><body>".$templEmail.'</body></html>';
    }


    /**
     * Method to generate HTML Invoce Fields
     * @param $invoice
     * @return string
     */
    function generateInvoiceFieldsHTML($invoice){
        $hotelTable = $this->getTable('Invoices');
        $hotel = $hotelTable->getHotel($invoice->hotelId);
        $vat = VAT;
        if($hotel->country_id==20 || $hotel->country_id==54){
            $vat = 0;
        } else if($hotel->country_id==161){
            //different VAT for Holland
            $vat = VAT_HOLLAND;
        }


        $style = "\"border:1px solid  #333\"";
        $invoiceFields= "<table  cellspacing='0' cellpadding='5'>
									<tr>
										<td style=$style>
											".JText::_('LNG_DESCRIPTION',true)."
										</td>
										<td style=$style>
											".JText::_('LNG_AMOUNT_EXCL_VAT',true)."
										</td>
										<td style=$style>
											".JText::_('LNG_VAT',true)."
										</td>
										<td style=$style>
											".JText::_('LNG_VAT_AMOUNT',true)."
										</td>
										<td style=$style>
											".JText::_('LNG_AMOUNT_INCL_VAT',true)."
										</td>
									</tr>
									<tr>
										<td style=$style>
											".JText::_('LNG_COMMISSION',true)."
										</td>
										<td style=$style>
											 &#8364; ".round($invoice->commissionAmount,2)."
										</td>
										<td style=$style>
											 ".$vat." %
										</td>
										<td style=$style>
											&#8364; ".round(($invoice->commissionAmount * $vat/100),2)."
										</td>
										<td style=$style>
											&#8364; ".round(($invoice->commissionAmount + $invoice->commissionAmount * $vat/100),2)."
										</td>
									</tr>
									</table
								";
//        dmp($invoiceFields);
        return $invoiceFields;
    }

    /**
     *
     * @param $invoice
     * @return string
     */
    function generateBookingsListHTML($invoice){
        //if invoice details are defined only invoice details are sent
        $bookingsList = '';
        $style = "\"border:1px solid  #333\"";
        //dmp($invoice->invoiceDetails);
        if(isset($invoice->invoiceDetails)){
            foreach($invoice->invoiceDetails as $detail){
                $bookingsList=$bookingsList."<TR>
											<TD style=$style>
												". $detail->reservationId."
											</TD>
											<TD style=$style>". $detail->name."</TD>
											<TD style=$style>". JHotelUtil::convertToFormat($detail->arrival)."</TD>
											<TD style=$style>". JHotelUtil::convertToFormat($detail->departure)."</TD>
											<TD style=$style nowrap='nowrap'> &#8364; ". $detail->amount." </TD>
											<TD style=$style nowrap='nowrap'> &#8364; ". $detail->commissionAmount."</TD>
										</TR>
										";
            }

            $bookingsList="<TABLE width=\"100%\" cellspacing=\"0\" cellpadding=\"6\" class=\"adminlist\" align=center border=0>
										<thead>
											<th style=$style width='10%' align=center><B>". ucfirst(JText::_('LNG_RESERVATION_NUMBER',true)) ."</B></th>
											<th style=$style width='30%' align=center ><B>".ucfirst(JText::_('LNG_NAME',true)) ."</B></th>
											<th style=$style width='20%' align=center><B>". ucfirst(JText::_('LNG_ARRIVAL',true)) ."</B></th>
											<th style=$style width='20%' align=center><B>". ucfirst(JText::_('LNG_DEPARTURE',true)) ."</B></th>
											<th style=$style width='10%' align=center><B>". ucfirst(JText::_('LNG_AMOUNT',true)) ."</B></th>
											<th style=$style width='10%' align=center><B>". ucfirst(JText::_('LNG_COMMISSION',true)) ."</B></th>
										</thead>
										<tbody>".$bookingsList."
											<tr>
												<td colspan=\"3\">&nbsp;</td>
												<td  nowrap='nowrap' align=\"right\"><strong>". JText::_('LNG_TOTAL',true).": </strong></td>
												<td  nowrap='nowrap' id=\"total-amount\" style=\"border-top:1px solid #333\"> &#8364; ". $invoice->reservationAmount ."</td>
												<td  nowrap='nowrap' id=\"total-commission\"style=\"border-top:1px solid #333\"> &#8364; ". $invoice->commissionAmount ."</td>
											</tr>
									</tbody>
								</TABLE>
					";
        }
        return $bookingsList;
    }


    function exportInvoiceCsv(){
        $csv_output = $this->getHotelInvoiceCSV();
        ob_clean();
        $fileName = "hotel_invoice";
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m-d") . ".csv");
        header( "Content-disposition: filename=".$fileName.".csv");
        print $csv_output;
    }

    /**
     * @return string generate a csv file with all the invoices for hotels ,
     * based on the start date and end date of the hotel
     * @throws Exception
     */
    public function getHotelInvoiceCSV(){
        //default delimiter not user selected
	    $appSettings = JHotelUtil::getInstance()->getApplicationSettings();
	    $delimiter = $appSettings->delimiter;

        //get the hotel id selected by the user in the hotels list
        $app		= JFactory::getApplication();
        $hotelId    = $app->getUserStateFromRequest('filter.hotel_id', 'hotel_id', '1', 'cmd');
        $invoiceId  =  $this->getState('invoice.invoiceId');

        $invoiceTable = $this->getTable('Invoices','JTable');

        //jtable method to get the data for csv export
        $hotels =  $invoiceTable->getInvoicesForExport($hotelId,$startDate=null,$endDate=null,$invoiceId);

        $csv_output ="Hotel name".$delimiter."Invoice id".$delimiter."Reservation id".$delimiter."Guest name".$delimiter."Invoice approval name".$delimiter."Arrival".$delimiter."Departure".$delimiter."Voucher".$delimiter."Status".$delimiter."Amount".$delimiter."CommissionAmount";

        $csv_output = $csv_output."\n";

        //  because the result is only one object  from object list array for only one hotel
        //  only the first array of the object list array is needed
        //  if it is set otherwise an empty object will be used
        $hotel = isset($hotels[0])?$hotels[0]:new stdClass();
            //will use only one hotel object to get the invoices for that hotel
            foreach($hotel->hotelsInvoices as $hotelInvoices) {
            	$commissionAmount = JHotelUtil::fmt($hotelInvoices->commissionAmount,2);
                $csv_output .= $hotel->hotel_name.$delimiter.$hotelInvoices->id . $delimiter . "\"$hotelInvoices->reservationId\"". $delimiter . "\"$hotelInvoices->guestName\"" .$delimiter . "\"$hotelInvoices->approvalName\"" . $delimiter . "\"$hotelInvoices->arrival\"" . $delimiter . "\"$hotelInvoices->departure\"" . $delimiter. "$hotelInvoices->voucher" . $delimiter . "\"$hotelInvoices->status\"" . $delimiter . "\"$hotelInvoices->amount\"" . $delimiter."\"$commissionAmount\"" . $delimiter;
                $csv_output .= "\n";
        }

        return $csv_output;
    }

	/**
	 * @param      $invoiceDetail
	 * @param      $data
	 * @param      $changes
	 * @param      $status
	 * @param      $invoiceId
	 * @param bool $invoiceDetailChangeLog
	 *
	 * return the string that contains the changes about invoice Details,
	 * if $invoiceDetailChangeLog is set to false the function is used only for invoice changes like approval name and agree status
	 *
	 *@return string
	 */
	public function checkInvoiceDetailsChanges($invoiceDetail,$data,$changes,$status,$invoiceId,$invoiceDetailChangeLog = true){
		if($invoiceDetailChangeLog)
		{
			$data["newamount-" . $invoiceDetail->id] = round((float)$data["newamount-" . $invoiceDetail->id],3);

			$invoiceDetail->amount = round((float)$invoiceDetail->amount,3);

			// only changes on the amount when status is 3 and unchanged
			if ( $status == 3 && $invoiceDetail->amount != $data["newamount-" . $invoiceDetail->id] )
			{

				$changes .= "Invoice # " . $invoiceId . " Invoice Details: Reservation Id # " . $invoiceDetail->reservationId . " and Name <b>'" . $invoiceDetail->name . "'</b> with Status <b>'" . JText::_( 'LNG_NO_SHOW_CANCELATION', true ) . "'</b> changed amount from <b>" . (float)$invoiceDetail->amount . "</b> to <b>" . $data["newamount-" . $invoiceDetail->id] . "</b>" . "<br>";

			}

			//changes for statuses only
			if ( $invoiceDetail->status != $status && !empty($invoiceDetail->status))
			{
				$newStatusLabel = $this->setInvoiceStatusLabel( $status );
				$oldStatusLabel = $this->setInvoiceStatusLabel( $invoiceDetail->status );

				$changes .= "Invoice # " . $invoiceId . " Invoice Details: Reservation Id # " . $invoiceDetail->reservationId . " and Name <b>'" . $invoiceDetail->name . "'</b> changed Status from <b>'" . $oldStatusLabel . "'</b> to <b>'" . $newStatusLabel . "'</b>" . "<br>";

				//if changed status is 3 , show the new amount defined
				if ( $status == 3 )
				{
					$changes .= "Invoice #" . $invoiceId . " Invoice Details: Reservation Id # " . $invoiceDetail->reservationId . " and Name <b>'" . $invoiceDetail->name . "'</b> new Amount is <b>" . $data["newamount-" . $invoiceDetail->id] . "</b>" . "<br>";
				}
			}
		}else{

			//change log only for invoice and not invoice details
			if ( $invoiceDetail->approvalName != $data["approvalName"] )
			{
				$invoiceDetail->approvalName = !empty($invoiceDetail->approvalName)?$invoiceDetail->approvalName:JText::_('LNG_EMPTY');
				$data["approvalName"]        = !empty($data["approvalName"])?$data["approvalName"]:JText::_('LNG_EMPTY');

				$changes .= "Invoice # " . $invoiceId ." changed Approval Name from <b>'" .$invoiceDetail->approvalName. "'</b> to <b>'" .$data["approvalName"]."'</b>" . "<br>";
			}


			if ( $invoiceDetail->agreed != $status)
			{
				switch ($status){
					case 1:
						$changes .= "Invoice # " . $invoiceId . " status <b>'" . JText::_('LNG_AGREE_WITH_TERMS',true) . "'</b> is checked"."<br>";
						break;

					default:
						$changes .= "Invoice # " . $invoiceId." status <b>'" . JText::_('LNG_AGREE_WITH_TERMS',true) . "'</b> is unchecked"."<br>";
						break;
				}
			}

		}

		return $changes;
	}


	/**
	 * @param $email
	 * @param $hotel_name
	 *
	 * method to send the notification email to the hotel admin
	 * after changes for invoice details are in place
	 *
	 * @return mixed
	 */
	function sendEmailInvoiceDetailsChangeLog($changeLog){

		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();

		//content of the email
		$content    = $changeLog;
		$from       = $appSettings->company_email;
		$fromName   = $appSettings->company_name;
		$isHtml     = true;
		$subject    = JText::_("LNG_INVOICE_DETAILS_CHANGE_LOG");
		// notifications sent only to hotel administrator
		$bcc        = null;
		$toEmail    = $from;

		return EmailService::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml);
	}
}