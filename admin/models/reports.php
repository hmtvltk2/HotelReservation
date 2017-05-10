<?php


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Reports Model.
 *
 */
class JHotelReservationModelReports extends JModelList
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
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
	
		$startDate = $this->getUserStateFromRequest($this->context.'filter.start_date', 'filter_start_date', '');
		if(!isset($startDate) || $startDate==''){
			$startDate = date('Y-m-01');
		}
		
		$startDate = JHotelUtil::convertToMysqlFormat($startDate);
		$this->setState('filter.start_date', $startDate);

		$endDate = $this->getUserStateFromRequest($this->context.'filter.end_date', 'filter_end_date', '');
		if(!isset($endDate) || $endDate==''){
			$endDate= date('Y-m-t');
		}

		$endDate = JHotelUtil::convertToMysqlFormat($endDate);
		$this->setState('filter.end_date', $endDate);
		
		$roomType = $this->getUserStateFromRequest($this->context.'.room_type', 'filter_room_type', 0, 'int', true);
		$this->setState('filter.room_type', $roomType);
		
		
		$type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', "advanced", 'string', true);
		$this->setState('filter.type', $type);
		
		$hotel_id = JRequest::getVar('hotel_id', null);
		if ($hotel_id) {
			if ($hotel_id != $app->getUserState($this->context.'.filter.hotel_id')) {
				$app->setUserState($this->context.'.filter.hotel_id', $hotel_id);
				JRequest::setVar('limitstart', 0);
			}
		}
		else {
			$hotel_id = $app->getUserState($this->context.'.filter.hotel_id');
	
			if (!$hotel_id) {
				$hotel_id = 0;
			}
		}
		//display all items
		JRequest::setVar('limit', 1000);
		$this->setState('filter.hotel_id', $hotel_id);
	
		// List state information.
		parent::populateState('r.room_id', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.

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
		$rooms = parent::getItems();
		
		// Getting the following metric by joins is WAY TOO SLOW.
		// Faster to do three queries for very large menu trees.

		// If emtpy or an error, just return.
		if (empty($rooms))
		{
			return array();
		}
		
		$startDate = JHotelUtil::convertToMysqlFormat($this->getState('filter.start_date'));
		$endDate = JHotelUtil::convertToMysqlFormat($this->getState('filter.end_date'));
		
		$result = array();

        $lang = JFactory::getLanguage();
        $languageTag = $lang->getTag();
        $translationTable = new JHotelReservationLanguageTranslations();

        foreach($rooms as $room){

            $roomTranslations = $translationTable->getObjectTranslation(ROOM_NAME,$room->room_id,$languageTag);
            $room->room_name = empty($roomTranslations->content)?$room->room_name:$roomTranslations->content;

			$query="select * from #__hotelreservation_rooms_rate_prices r
			where rate_id=$room->rate_id and '$startDate'<= date and date<='$endDate'"  ;
			//dmp($query);
			$roomRateDetails = $this->_getList( $query );
			$room->roomRateDetails = $roomRateDetails;
			
			$room->nrRoomsAvailable = $room->availability;
			//dmp($roomRateDetails);
			$days = array();
			for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
				$available = true;
				$dayString = date("Y-m-d", $d);
				
				if(count($roomRateDetails)){
					foreach($roomRateDetails as $roomRateDetail){
						if($roomRateDetail->date == $dayString){
							if( $roomRateDetail->lock_arrival == 1){
								$available = false;
							}
							$room->nrRoomsAvailable = $roomRateDetail->availability;
							//dmp($dayString);
							//dmp($room->nrRoomsAvailable);
						}
					}
				}
				$day = array(
						'nrRooms'	=> $room->nrRoomsAvailable,
						'available'	=> $available
				);
				
				$days[$dayString]= $day;
				$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
			}
			$room->days = $days;
			//$result[$room_id]= $room;
		}		

		// Add the items to the internal cache.
		$this->cache[$store] = $rooms;

		return $this->cache[$store];
	}

	public function getModel($name = 'Report', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
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
		$query->select($this->getState('list.select', 'r.room_name,rr.room_id,rr.availability,rr.id as rate_id '));
		$query->from($db->quoteName('#__hotelreservation_rooms').' AS r');

		
		$query->join('INNER', '#__hotelreservation_rooms_rates rr on r.room_id = rr.room_id');
		
		
		// Filter on the published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('r.is_available = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(r.is_available IN (0, 1))');
		}
		
		
		// Filter the items over the menu id if set.
		$hotelId = $this->getState('filter.hotel_id');
		if (!empty($hotelId)) {
			$query->where('r.hotel_id = '.$hotelId);
			//dmp($hotelId);
		}
		$roomType = $this->getState('filter.room_type');
		if(is_numeric($roomType) && $roomType!=-0){
			$query->where('r.room_id = '.$roomType);
		}
		
		//$query->group('r.room_id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'r.room_id')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
	
	function getHotels()
	{
        $hotelsTable = $this->getTable("Hotels");
        $hotels = $hotelsTable->getAllHotels();
        return $hotels;
	}
	
	function getRoomTypesOptions(){
		$hotelId = $this->getState('filter.hotel_id');
        $languageTag = JRequest::getVar('_lang');

        $translationTable = new JHotelReservationLanguageTranslations();
		$query = " SELECT *
					FROM #__hotelreservation_rooms
					where hotel_id = $hotelId		
					ORDER by room_name ";
		//$this->_db->setQuery( $query );
		$rooms = $this->_getList( $query );
		
		$options = array();
		foreach($rooms as $room){
            $roomTranslations = $translationTable->getObjectTranslation(ROOM_NAME,$room->room_id,$languageTag);
            $room->room_name = empty($roomTranslations->content)?$room->room_name:$roomTranslations->content;
			$options[]	= JHtml::_('select.option', $room->room_id, $room->room_name);
		}
		return $options;
	}
	
	function getBookingsPerDay()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select all fields from the table.
		$query->select($this->getState('list.select', 'hc.*'));
		$query->from($db->quoteName('#__hotelreservation_confirmations').' AS hc');
		
		$query->select('hcr.room_id');
		$query->join('INNER', '#__hotelreservation_confirmations_rooms hcr on hc.confirmation_id= hcr.confirmation_id');
		
		$startDate = $this->getState('filter.start_date');
		if(isset($startDate)){
			$startDate=JHotelUtil::convertToMysqlFormat($startDate);
			$query->where('hc.end_date >= '.$db->quote($startDate));
		}
			
		$endDate = $this->getState('filter.end_date');
		if(isset($endDate)){
			$endDate=JHotelUtil::convertToMysqlFormat($endDate);
			$query->where('hc.start_date <= '.$db->quote($endDate));
		}
		
		$roomType = $this->getState('filter.room_type');
		if(is_numeric($roomType) && $roomType!=-0){
			$query->where('hcr.room_id = '.$roomType);
		}
		
		// Filter the items over the menu id if set.
		$hotelId = $this->getState('filter.hotel_id');
		if (!empty($hotelId)) {
			$query->where('hc.hotel_id = '.$hotelId);
		}
	
		
		$query->where('hc.reservation_status <> '.CANCELED_ID);
		//echo $query->dmp();
		$reservationInfos = $this->_getList( $query );
		
		//echo $query->dmp();
		//dmp($reservationInfos);
		
		$reservedRooms = array();
		
		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
			$dayString = date("Y-m-d", $d);
			foreach($reservationInfos as $reservationInfo){
				if( strtotime($reservationInfo->start_date)<= $d && $d<strtotime($reservationInfo->end_date) ){
					//dmp("r: ".$dayString);
					if(!isset($reservedRooms[$reservationInfo->room_id]) || !isset($reservedRooms[$reservationInfo->room_id][$dayString]) ){
						$reservedRooms[$reservationInfo->room_id][$dayString] = 0;
					}
				
					$reservedRooms[$reservationInfo->room_id][$dayString] = $reservedRooms[$reservationInfo->room_id][$dayString] +1;
				}
			}
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
		
		return $reservedRooms;
	}
	
	function getAvailabilityReport(){
	
		$rooms = $this->getItems();
		//dmp($rooms);
		$bookings = $this->getBookingsPerDay();
		//dmp($bookings);
		$report = array();
		$startDate = $this->getState('filter.start_date');
		$endDate = $this->getState('filter.end_date');
		
		//dmp($startDate);
		//dmp($endDate);
		
		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
			$dayString = date("Y-m-d", $d);
			$info = array();
			foreach($rooms as $room){
				$available = $room->days[$dayString]["available"];
				$nrRooms = $room->days[$dayString]["nrRooms"];
				$nrBookings = isset($bookings[$room->room_id][$dayString])?$bookings[$room->room_id][$dayString]:0;
				$info[$room->room_id] = array($nrRooms, $nrBookings, $available);
			}
			$report[$dayString]= $info;
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
		return $report;
	}
	
	function getOfferReport(){
        $offersTable = JTable::getInstance('Offers','JTable');
		$offersBooking =  $offersTable->getOfferBookingSituation();
		$offersView =  $offersTable->getOfferViewSituation();
	
		$report= array();
		$media_referers = array();
	
		if(isset($offersBooking) && count($offersBooking)>0){
			foreach ($offersBooking as $ob){
				if(!isset($report[$ob->voucher]))
					$report[$ob->voucher] = array();
				if(!isset($report[$ob->voucher][$ob->offer_id]))
					$report[$ob->voucher][$ob->offer_id] = array();
				$report[$ob->voucher][$ob->offer_id]['info'] = $ob->hotel_name. ' - '.$ob->offer_name;
				$report[$ob->voucher][$ob->offer_id]['bookings'][$ob->media_referer] = $ob->nrBookings;
				$media_referers[$ob->media_referer]= 1;
			}
		}
	
		foreach ($offersView as $ob){
			if(!isset($report[$ob->voucher]))
				$report[$ob->voucher] = array();
			if(!isset($report[$ob->voucher][$ob->offer_id]))
				$report[$ob->voucher][$ob->offer_id] = array();
			$report[$ob->voucher][$ob->offer_id]['info'] = $ob->hotel_name. ' - '.$ob->offer_name;
			$report[$ob->voucher][$ob->offer_id]['views'][$ob->media_referer] = $ob->view_count;
			$media_referers[$ob->media_referer]=1;
		}
	
		foreach($media_referers as $key=>$value)
			$report['media_refers'][]=$key;
	
	
		return $report;
	}
	
	function getDetailedAvailabilityReport(){
		$report = array();
		$startDate = JHotelUtil::convertToMysqlFormat($this->getState('filter.start_date'));
		$endDate = JHotelUtil::convertToMysqlFormat($this->getState('filter.end_date'));
		$hotelId= $this->getState('filter.hotel_id');
		$query = "select hc.confirmation_id, hc.start_date, hc.end_date,hc.first_name, hc.last_name, hc.confirmation_details ,hcr.*,hsr.status_reservation_name
				  from #__hotelreservation_confirmations hc
				  inner join #__hotelreservation_confirmations_rooms hcr on hcr.confirmation_id 		= hc.confirmation_id
				  inner join #__hotelreservation_status_reservation hsr on hsr.status_reservation_id	= hc.reservation_status
				  where ((hc.start_date >='$startDate' and hc.end_date <='$endDate') or ('$startDate' between hc.start_date and hc.end_date) or ('$endDate' between hc.start_date and hc.end_date)) and hc.hotel_id = $hotelId
				  order by hc.start_date, hc.end_date
					";
		
		$bookings = $this->_getList( $query );
// 		dmp($query);
//		dmp($bookings);
		//dmp(count($bookings));
		if(count($bookings)){
			foreach($bookings as $booking){
				if(!isset($report[$booking->room_id])){
					$report[$booking->room_id][] = array($booking);
					//dmp("add first id:".$booking->confirmation_id);
				}else{
					$levels = &$report[$booking->room_id];
					$index = 0;
					$added = false;
					foreach($levels as $i=>&$level){
						$lastBooking = end($level);
						if(strtotime($lastBooking->end_date) <=strtotime($booking->start_date)){
							$level[] = $booking;
							$added = true;
							//dmp($level);
							//dmp("Add B". $booking->confirmation_id." L:".$i." C: ".count($level));
							break;
						}
					}
					
					if(!$added){
						//debug_zval_dump($booking);
						//dmp("create new level  add: ".$booking->confirmation_id);
						$report[$booking->room_id][] = array($booking);
					}
				}
			}
		}
		return $report;
	}
}
