<?php


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Menu List Model for Rooms.
 *
 */
class JHotelReservationModelReservations extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array	An optional associative array of configuration settings.
	 *
	 * @see		JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'confirmation_id', 'c.confirmation_id',
				'name', 'c.first_name',
				'hotel', 'h.hotel_name',
				'voucher', 'c.voucher',
				'created', 'c.created',
				'start_date', 'c.start_date',
				'end_date', 'c.end_date',
				'created', 'c.created',
				'created', 'c.created',
				'created', 'c.created',
			);
		}

		parent::__construct($config);
	}

	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
	
		$search = $this->getUserStateFromRequest($this->context.'.search', 'filter_search', '');
		$this->setState('filter.search', $search);
		
		$voucher = $this->getUserStateFromRequest($this->context.'.voucher', 'filter_voucher', '');
		$this->setState('filter.voucher', $voucher);
		
		$startDate = $this->getUserStateFromRequest($this->context.'.start_date', 'filter_start_date', '');
		$this->setState('filter.start_date', $startDate);
		
		$endDate = $this->getUserStateFromRequest($this->context.'.end_date', 'filter_end_date', '');
		$this->setState('filter.end_date', $endDate);
		
		$status = $this->getUserStateFromRequest($this->context.'.status', 'filter_status', '');
		$this->setState('filter.status', $status);
		
		$paymentStatus = $this->getUserStateFromRequest($this->context.'.payment_status', 'filter_payment_status', '');
		$this->setState('filter.payment_status', $paymentStatus);

        $paymentMethods = $this->getUserStateFromRequest($this->context.'.payment_methods', 'filter_payment_methods', '');
        $this->setState('filter.payment_methods', $paymentMethods);

		$hotelId = $this->getUserStateFromRequest($this->context.'.hotel_id', 'filter_hotel_id', '');
		$this->setState('filter.hotel_id', $hotelId);
		
		$roomType = $this->getUserStateFromRequest($this->context.'.room_type', 'filter_room_type', '');
		$this->setState('filter.room_type', $roomType);
		
		$limit = JRequest::getVar("limit",0);
		if($limit==0){
			JRequest::setVar("limit",50);
		}
		
		// List state information.
		parent::populateState('c.confirmation_id', 'desc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.

		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.hotel_id');
	
		return parent::getStoreId($id);
	}
	
	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();
		
		// Getting the following metric by joins is WAY TOO SLOW.
		// Faster to do three queries for very large menu trees.

		// If emtpy or an error, just return.
		if (empty($items))
		{
			return array();
		}
		

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'c.confirmation_id,c.hotel_id, c.start_date, c.end_date, c.first_name,c.cancellation_notes ,
										c.last_name, c.reservation_status, c.voucher, c.created, c.adults, c.children,c.rooms, c.total,c.email,c.address,c.city,c.state_name,c.country,c.postal_code,c.total_cost,c.language_tag'));
		$query->from($db->quoteName('#__hotelreservation_confirmations').' AS c');
		
		$query->select('h.hotel_name');
		$query->join('LEFT', '#__hotelreservation_hotels AS h ON c.hotel_id=h.hotel_id');

		$query->select(' sum(cr.adults) as total_adults,sum(cr.children) as total_children');
		$query->join('LEFT', '#__hotelreservation_confirmations_rooms  AS cr ON c.confirmation_id=cr.confirmation_id');
		
		$query->select(' sum(cr.adults) as total_adults,sum(cr.children) as total_children');
		$query->join('LEFT', '#__hotelreservation_rooms  AS hr ON hr.room_id=cr.room_id');
		
		$query->select(' GROUP_CONCAT(distinct hltr.content) as roomNames');
		$query->join('LEFT', '( select * from #__hotelreservation_language_translations where type='.ROOM_NAME.' and language_tag="'.JHotelUtil::getLanguageTag().'") AS hltr ON cr.room_id=hltr.object_id');
				
		$query->select('s.status_reservation_name, s.bkcolor, s.color, s.is_modif');
		$query->join('LEFT', '#__hotelreservation_status_reservation AS s ON c.reservation_status=s.status_reservation_id');
		
		$query->select('min(cp.payment_status) as payment_status, (cp.amount) as amount_paid,cp.processor_type,cp.payment_method');
		$query->join('LEFT', '#__hotelreservation_confirmations_payments as cp on c.confirmation_id= cp.confirmation_id');

		//if other than super user restrict hotels
		$userId	= JFactory::getUser()->id;
		if(!(isSuperUser($userId) || isManager($userId))){
			$query->join('INNER', $db->quoteName('#__hotelreservation_user_hotel_mapping').' AS hum ON h.hotel_id=hum.hotel_id');
			$query->where("hum.user_id = ".$userId);
		}
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (is_numeric($search)) {
				$query->where("c.confirmation_id=$search");
			}
			else {
				$query->where("(c.first_name LIKE '%$search%' or c.last_name LIKE '%$search%' or h.hotel_name LIKE '%$search%')");
			}
		}
		
		// Filter by search in title.
		$searchVoucher = $this->getState('filter.voucher');
		if (!empty($searchVoucher)) {
				//dmp($searchVoucher); 
				$query->where("c.voucher LIKE '%".$searchVoucher."%'");
		}
		
		$searchStartDate= $this->getState('filter.start_date');
		$searchEndDate= $this->getState('filter.end_date');
		
		if (!empty($searchEndDate) && !empty($searchStartDate)) {
			$query->where("c.start_date between '".JHotelUtil::convertToMysqlFormat($searchStartDate)."' and '".JHotelUtil::convertToMysqlFormat($searchEndDate)."'");
		}
		else if (!empty($searchStartDate)) {
			$query->where("c.start_date >= '".JHotelUtil::convertToMysqlFormat($searchStartDate)."'");
		}
		
		// Filter the items over the menu id if set.
		$hotelId = $this->getState('filter.hotel_id');
		if (!empty($hotelId)) {
			$query->where('h.hotel_id = '.$hotelId);
		}
		
		// Filter the items over the menu id if set.
		$roomId = $this->getState('filter.room_type');
		if (!empty($roomId)) {
			$query->where('cr.room_id = '.$roomId);
		}
		
		// Filter the items over the menu id if set.
		$status = $this->getState('filter.status');
		if (!empty($status)) {
			$query->where('s.status_reservation_id = '.$status);
		}
		
		// Filter the items over the menu id if set.
		$payment_status = $this->getState('filter.payment_status');
		if ($payment_status!=-1 && $payment_status!="") {
			$query->where('cp.payment_status = '.$db->quote($payment_status));
		}

		$payment_methods = $this->getState('filter.payment_methods');
        if ($payment_methods!=-1 && $payment_methods!="") {
            $query->where('cp.processor_type = '.$db->quote($payment_methods));
        }
		
		$query->group('c.confirmation_id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'c.confirmation_id')).' '.$db->escape($this->getState('list.direction', 'ASC')));
		return $query;
	}
	
	function getHotels()
	{
        $hotelsTable = $this->getTable("Hotels");
        $hotels = $hotelsTable->getAllHotels();
        return $hotels;
	}
	
	function getRoomTypesOptions(){
		$options = array();
        $languageTag = JRequest::getVar('_lang');

        $translationTable = new JHotelReservationLanguageTranslations();
		$hotelId = $this->getState('filter.hotel_id');
		if(!$hotelId){
			return $options;
		}
		$query = " SELECT *
					FROM #__hotelreservation_rooms
					where hotel_id = $hotelId
					ORDER by room_name ";
		$rooms = $this->_getList( $query );
	
		foreach($rooms as $room){
            $roomTranslations = $translationTable->getObjectTranslation(ROOM_NAME,$room->room_id,$languageTag);
            $room->room_name = empty($roomTranslations->content)?$room->room_name:$roomTranslations->content;
			$options[]	= JHtml::_('select.option', $room->room_id, $room->room_name);
		}
		return $options;
	}
	
	function getStatusReservation()
	{
		$query = ' SELECT *, status_reservation_id as value, status_reservation_name as text FROM #__hotelreservation_status_reservation ORDER BY `order` ';
		$res = $this->_getList( $query );
		return $res;
	}
	
	function changeReservationStatus($reservationId, $statusId){
		//set reservation state
		$query = 	" UPDATE #__hotelreservation_confirmations SET reservation_status = $statusId WHERE confirmation_id = ".$reservationId;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}
	
	function changePaymentStatus($reservationId, $paymentStatusId){
		
	}
	public function exportToCSV(){
		$this->reservationStatuses = JHotelReservationHelper::getReservationStatuses();
		$this->paymentStatuses = JHotelReservationHelper::getPaymentStatuses();
		$this->populateState();
		$reservations = $this->getItems();
        $extraReservationFields = $this->getReservationExportData();
		$reservationDiscounts   = $this->getReservationDiscounts();
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$appDelimiter = $appSettings->delimiter;
		$csv_output = "id".$appDelimiter."name".$appDelimiter."hotel".$appDelimiter."voucher".$appDelimiter."check in".$appDelimiter."check out".$appDelimiter."created at".$appDelimiter."adults".$appDelimiter."children".$appDelimiter."rooms".$appDelimiter."status".$appDelimiter."payment status".$appDelimiter."customer email".$appDelimiter."customer address".$appDelimiter."city".$appDelimiter."state".$appDelimiter."country".$appDelimiter."postal code".$appDelimiter."room_name".$appDelimiter."offer".$appDelimiter."extra_option".$appDelimiter."airport Transfer".$appDelimiter."taxes".$appDelimiter."commissions".$appDelimiter."reservation cost".$appDelimiter."payment method".$appDelimiter."discounts"."\n";
		$hoteltranslationsModel = new JHotelReservationLanguageTranslations();
				
        $languageTag = JRequest::getVar( '_lang');
		$offerNameTranslations = $hoteltranslationsModel->getAllTranslationtByLanguageArray(OFFER_NAME,$languageTag);
		$transferTranslations = $hoteltranslationsModel->getAllTranslationtByLanguageArray(AIRPORT_TRANSFER_TRANSLATION_NAME,$languageTag);

		foreach($reservations as $item){

            $tax                    = '';
            $offer                  = new stdClass();
            $airportTransferName    = new stdClass();
            $discount         = '';
            $room_name              = '';
            $commission             = '';
            $extraOption            = '';
            $paymentMethod = empty($item->payment_method)?"":" - ".$item->payment_method;
            $paymentMethod = $item->processor_type.$paymentMethod;            

            $item->confirmation_id = (int)$item->confirmation_id;

            if(array_key_exists((int)$item->confirmation_id,$extraReservationFields)){

                $offer_id                   = $extraReservationFields[$item->confirmation_id]->offer_id;
                $extra_option_id            = $extraReservationFields[$item->confirmation_id]->extra_option_id;
                if(isset($extra_option_id)) {
                    $extra_option_id = explode(',', $extra_option_id);
                }
                $airport_transfer_type_id   = $extraReservationFields[$item->confirmation_id]->airport_transfer_type_id;
                $offer = empty($offerNameTranslations[$offer_id])?$extraReservationFields[$item->confirmation_id]->offer_name:$offerNameTranslations[$offer_id]["content"];
                
                $delimiter = '';
                if(isset($extra_option_id)) {
                    foreach ($extra_option_id as $k => $extra_option) {
	                    $extra_option = explode("|",$extra_option);
                        $extra_option_name = $hoteltranslationsModel->getObjectTranslation(EXTRA_OPTION_NAME, $extra_option[0], $item->language_tag);
                        $extrasName = !empty($extra_option_name->content)?$extra_option_name->content:$extra_option[1];
                        $extraOption .= $delimiter.$extrasName;
                        $delimiter = '| ';
                    }
                }
                
                
                $airportTransferName    = $transferTranslations[$airport_transfer_type_id];
                $city_tax_percent       = $extraReservationFields[$item->confirmation_id]->city_tax_percent;
                $city_tax               = $extraReservationFields[$item->confirmation_id]->city_tax;
                $tax                    = $city_tax_percent==1?$city_tax.'%':$city_tax;
                
                //discounts
                $discount   = '';
	            if(array_key_exists((int)$item->confirmation_id,$reservationDiscounts)){
		            $discount    = $reservationDiscounts[$item->confirmation_id]->discount;
	            }

                $room_name              = $extraReservationFields[$item->confirmation_id]->room_name;
                $commission             = $extraReservationFields[$item->confirmation_id]->commission.'%';
            }
			$reservationStatus          = $this->reservationStatuses[$item->reservation_status];
			$paymentStatus              = $this->paymentStatuses[$item->payment_status];

            $airportTransferName = !empty($airportTransferName->content)?$airportTransferName->content:$extraReservationFields[$item->confirmation_id]->airport_transfer_type_name;
            $customerAddress = str_replace(',', ' ',$item->address);
			
            $reservationcost = $item->total_cost;
            $csv_output .= "\"$item->confirmation_id\"".$appDelimiter."\"$item->first_name $item->last_name\"".$appDelimiter."\"$item->hotel_name\"".$appDelimiter."\"$item->voucher\"".$appDelimiter."\"$item->start_date\"".$appDelimiter."\"$item->end_date\"".$appDelimiter."\"$item->created\"".$appDelimiter."\"$item->total_adults\"".$appDelimiter."\"$item->total_children\"".$appDelimiter."\"$item->rooms\"".$appDelimiter."\"$reservationStatus\"".$appDelimiter."\"$paymentStatus\"".$appDelimiter."\"$item->email\"".$appDelimiter."\"$customerAddress\"".$appDelimiter."\"$item->city\"".$appDelimiter."\"$item->state_name\"".$appDelimiter."\"$item->country\"".$appDelimiter."\"$item->postal_code\"".$appDelimiter."\"$room_name\"".$appDelimiter."\"$offer\"".$appDelimiter."\"$extraOption\"".$appDelimiter."\"$airportTransferName\"".$appDelimiter."\"$tax\"".$appDelimiter."\"$commission\"".$appDelimiter."\"$reservationcost\"".$appDelimiter."\"$paymentMethod\"".$appDelimiter."\"$discount\"".$appDelimiter;
            $csv_output .= "\n";
		}
		ob_clean();

		$fileName = "jhotel_reservations_listing";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$fileName.".csv");
		print $csv_output;
	}
		
	function uploadFile($fileName, &$data, $dest){
	
		//Retrieve file details from uploaded file, sent from upload form
		$file = JRequest::getVar($fileName, null, 'files', 'array');
	
		if($file['name']=='')
		return true;
	
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');
			
		//Clean up filename to get rid of strange characters like spaces etc
		$fileNameSrc = JFile::makeSafe($file['name']);
		$data[$fileName] =  $fileNameSrc;
	
		$src = $file['tmp_name'];
		$dest = $dest."/".$fileNameSrc;
	
		//dump($src);
		//dump($dest);
		//exit;
		$result =  JFile::upload($src, $dest);
	
		if($result)
		return $dest;
	
		return null;
	}
	
	
	function batchCancelReservations($filePath, $delimiter){

		$row = 1;
		if (($handle = fopen($filePath, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 3000, $delimiter)) !== FALSE) {
				$reservation = array();
				if($row==1){
					$header = $data;
					$row++;
					continue;
				}
				$num = count($data);
				//dump($data);
				//echo "<p> $num fields in line $row: <br /></p>\n";
				$row++;
				for ($c=0; $c < $num; $c++) {
					$reservation[strtolower($header[$c])]= $data[$c];
				}	

				$table = $this->getTable("Confirmations");
				if(intval($reservation['reservation_id'])>0){
					$reservation['reservation_id'] = intval($reservation['reservation_id']);
					if($table->setStatus($reservation['reservation_id'], CANCELED_ID)){
						$table->updateCancelationComments($reservation['reservation_id'], $reservation['cancellation_note']);
						$this->sendCancellationEmail($reservation['reservation_id']);
					}
				}
			}
			fclose($handle);
		}
		$result = new stdClass();
		return $result;
	
	}
	
	function sendCancellationEmail($reservationId){
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->getReservation($reservationId,-1);
		$sentResult = true;
		if($reservationDetails->hotelId>0)
			 $sentResult = EmailService::sendCancelationEmail($reservationDetails);
		return $sentResult; 
	}

    function getPaymentMethods(){
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('type,name');
        $query->from($db->quoteName('#__hotelreservation_payment_processors'));

        $query->group('id');

        // Add the list ordering clause.
        $query->order($db->escape('id'));
        $db->setQuery( $query );
        $result = $db->loadObjectList();
        $paymentMethod = array();
        for($i =0; $i < count($result); $i++){
            $paymentMethod[$result[$i]->type] = $result[$i]->name;
        }

        return $paymentMethod;
    }


    /**
     * Separate Method to not interfere with method @getListQuery and the search filters
     * @return array method to get the additional data for the reservation export to csv
     */
    function getReservationExportData(){

	    $reservationTable = $this->getTable('Confirmations');
	    $result = $reservationTable->getReservationExportData();

        $new_result = array();
        foreach($result as $k=>$value){
            $new_result[$value->confirmation_id] = $result[$k];
        }
        return $new_result;
    }

	/**
	 * Separate Method to not interfere with method @getReservationExportData from Confirmations table and the csv generation
	 * @return array method to get the additional data about discounts for the reservation export to csv
	 */
	function getReservationDiscounts(){
		$reservationTable = $this->getTable('RoomDiscounts','JTable');
		$result = $reservationTable->getReservationDiscounts();

		$new_result = array();
		foreach($result as $k=>$value){
			$new_result[$value->confirmation_id] = $result[$k];
		}
		return $new_result;
	}
}
