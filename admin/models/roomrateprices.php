<?php


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once 'reports.php';

/**
 * Menu List Model for Rooms.
 *
 */
class JHotelReservationModelRoomRatePrices extends JModelList
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
				'room_id', 'r.id',
				'title', 'a.title',
				'menutype', 'a.menutype',
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
	
		$month = $this->getUserStateFromRequest($this->context.'.published', 'filter_month', date("n"));
		$this->setState('filter.month', $month);
		
		
		
		$rate_id = JRequest::getVar('rate_id', null);
		if (!isset($rate_id)) {
			if ($rate_id != $app->getUserState($this->context.'.filter.rate_id')) {
				$app->setUserState($this->context.'.filter.rate_id', $rate_id);
			} else {
				$rate_id = $app->getUserState($this->context.'.filter.rate_id');
		
				if (!$rate_id) {
					$rate_id = 0;
				}
			}
		}
		$this->setState('filter.rate_id', $rate_id);
		
		JRequest::setVar('limit', 0);
	
		// List state information.
		parent::populateState('r.date', 'asc');
	}
	
	public function getTable($type = 'RoomRatePrices', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.

		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.rate_id');
	
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
		//if (!empty($this->cache[$store]))
		//{
		//	return $this->cache[$store];
		//}

		// Load the list items.
		$items = parent::getItems();
	
		// If emtpy or an error, just return.
		$month = $this->getState('filter.month');
		$year = $month<date("n")? date("Y")+1 : date("Y");
		
		$startDate  = date("Y-m-d",mktime(0,0,0,$month,1,$year));
		$endDate = date("Y-m-d",mktime(0,0,0,$month+1,0,$year));
		$app = JFactory::getApplication('administrator');
		
		$hotelId = $app->getUserState('com_jhotelreservation.rooms.filter.hotel_id');
		$roomId = $app->getUserState('com_jhotelreservation.edit.room.room_id');
		$rId = JRequest::getVar("room_id",null);
		if(!empty($rId)){
			$roomId = $rId;
			$app->setUserState('com_jhotelreservation.edit.room.room_id',$roomId);
		}
		
		if (empty($items)){	
			$items =  $this->getDefaultRates($month, $year);
			$this->setState('filter.newRates', true);
		}
	
		$this->setState('filter.start_date', JHotelUtil::convertToFormat($startDate));
		$this->setState('filter.end_date', JHotelUtil::convertToFormat($endDate));
		
		JRequest::setVar("filter_start_date",$startDate);
		JRequest::setVar("filter_end_date",$endDate);
		JRequest::setVar("filter_room_type",$endDate);
		JRequest::setVar("hotel_id",$hotelId);
		JRequest::setVar("filter_room_type",$roomId);
	
		$hotelAvailability = HotelService::getHotelAvailabilyPerDay($hotelId, $startDate, $endDate);
		//dmp($roomId);
		
		$report = new JHotelReservationModelReports();
		$availabilty = $report->getAvailabilityReport();
		//dmp($availabilty); 
		//dmp($roomId);
		foreach($items as $item){
			//dmp($item->date);
			
			
			if(isset($availabilty[$item->date][$roomId]))
				$item->booked = $availabilty[$item->date][$roomId][1];
			else
				$item->booked = 0;
			//dmp($item->booked);
			if(isset($availabilty[$item->date][$roomId]))
				$item->available = intval($item->availability) - intval($availabilty[$item->date][$roomId][1]);
			else
				$item->available = $item->availability;
			
			if($item->available<0) $item->available = 0;
				
			$item->isHotelAvailable = $hotelAvailability[$item->date];
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
		$query->select($this->getState('list.select', 'r.*'));
		$query->from($db->quoteName('#__hotelreservation_rooms_rate_prices').' AS r');

		
		// Filter the items over the menu id if set.
		$rateId = $this->getState('filter.rate_id');
		if (!empty($rateId)) {
			$query->where('r.rate_id = '.$rateId);
		}
		
		// Filter the items over the date set.
		$month = $this->getState('filter.month');
		if (!empty($month)) {
			$year = $month<date("n")? date("Y")+1 : date("Y");
			$query->where('r.date >= '.$db->quote(date("Y-m-d",mktime(0,0,0,$month,1,$year))));
			$query->where('r.date <= '.$db->quote(date("Y-m-d",mktime(0,0,0,$month+1,0,$year))));
		}
		
		$query->group('r.id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'r.date')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 */
	public function save($data)
	{
		if(!$this->deleteOldRatePrices($data['filter_month'], $data["rate_id"])){
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}
		$month = $data['filter_month'];
		$year = $month<date("n")? date("Y")+1 : date("Y");
		$nrDays = date('t', mktime(0,0,0, $month,1, $year));
		for($i=1;$i<=$nrDays;$i++){
			$rateInfo =  array();
			$rateInfo["date"] = date("Y-m-d",mktime(0,0,0,$month,$i,$year));
			$rateInfo["rate_id"] = $data["rate_id"];
			$rateInfo["price"] = $data["price"][$i];
			$rateInfo["child_price"] = $data["child_price"][$i];
			$rateInfo["single_use_price"] = $data["single_use_price"][$i];
			$rateInfo["extra_pers_price"] = $data["extra_pers_price"][$i];
			$rateInfo["availability"] = $data["availability"][$i];
			$rateInfo["min_days"] = $data["min_days"][$i];
			$rateInfo["max_days"] = $data["max_days"][$i];
			$rateInfo["lock_arrival"] = isset($data["lock_arrival"][$i])?1:0;
			$rateInfo["lock_departure"] = isset($data["lock_departure"][$i])?1:0;
			
			$table = $this->getTable();
			// Bind the data.
			if (!$table->bind($rateInfo)) {
				$this->setError($table->getError());
				return false;
			}
		
			// Check the data.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}
		
			// Store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
			
		}
		
		// Clean the cache
		//$this->cleanCache();
		return true;
	}
	function saveCustomChildCategoryPrices($data){
	
		if(!$this->deleteOldCategoryRatePrices($data['filter_month'], $data["rate_id"])){
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}
		$month = $data['filter_month'];
		$year = $month<date("n")? date("Y")+1 : date("Y");
		$nrDays = date('t', mktime(0,0,0, $month,1, $year));
	
		$app = JFactory::getApplication('administrator');
		$childrenCategories = ChildrenCategoryService::getChildrenCategories($app->getUserState('com_jhotelreservation.rooms.filter.hotel_id'));
	
		for($i=1;$i<=$nrDays;$i++){
			$rateInfo =  array();
			$rateInfo["date"] = date("Y-m-d",mktime(0,0,0,$month,$i,$year));
			$rateInfo["rate_id"] = $data["rate_id"];
			foreach($childrenCategories as $childrenCategory){
				$rateInfo["price"] = $data["child_price_".$childrenCategory->id][$i];
				$rateInfo["category_id"] = $childrenCategory->id;
				$table = $this->getTable("childcategorycustomprices");
	
				// Bind the data.
				if (!$table->bind($rateInfo)) {
					$this->setError($table->getError());
					return false;
				}
				// Check the data.
				if (!$table->check()) {
					$this->setError($table->getError());
					return false;
				}
				// Store the data.
				if (!$table->store()) {
					$this->setError($table->getError());
					return false;
				}
			}
		}
	
		return true;
	
	}
	
	
	function deleteOldCategoryRatePrices($month, $rateId ){
		// Create a new query object.
		$db		= $this->getDbo();
		$rateId = $this->getState('filter.rate_id');
		$query	= $db->getQuery(true)
		->delete("")
		->from(' #__hotelreservation_children_categories_rate_prices ');
		$query->where('rate_id='.$rateId);
	
		if (!empty($month)) {
			$year = $month<date("n")? date("Y")+1 : date("Y");
			$query->where('date >= '.$db->quote(date("Y-m-d",mktime(0,0,0,$month,1,$year))));
			$query->where('date <= '.$db->quote(date("Y-m-d",mktime(0,0,0,$month+1,0,$year))));
		}
		//exit;
		$db->setQuery($query);
		$result = $db->query();
	
		return $result;
	}
	
	function deleteOldRatePrices($month, $rateId ){
		// Create a new query object.
		$db		= $this->getDbo();
		$rateId = $this->getState('filter.rate_id');
		$query	= $db->getQuery(true)
		->delete("")
		->from(' #__hotelreservation_rooms_rate_prices ');
		$query->where('rate_id='.$rateId);
		
		if (!empty($month)) {
			$year = $month<date("n")? date("Y")+1 : date("Y");
			$query->where('date >= '.$db->quote(date("Y-m-d",mktime(0,0,0,$month,1,$year))));
			$query->where('date <= '.$db->quote(date("Y-m-d",mktime(0,0,0,$month+1,0,$year))));
		}
		//exit;
		$db->setQuery($query);
		$result = $db->query();
		
		return $result;
	}
	
	
	public function getRate(){
		// Create a new query object.
		$db		= $this->getDbo();
		$rateId = $this->getState('filter.rate_id');
		//dmp($rateId);
		$query	= $db->getQuery(true)
		->select('*')
		->from('#__hotelreservation_rooms_rates')
		->where('id='.$rateId);
		$db->setQuery($query,0,1);
		//echo $query->dmp();
		$rate = $db->loadObject();
	
		return $rate;
	}
	
	
	public function getDefaultRates($month, $year){
		$nrDays = date('t', mktime(0,0,0, $month,1, $year));
		$rates = array();
		$rate = $this->getRate();
		//dmp($rate);
		for($i=1;$i<=$nrDays;$i++){
			$childrenCategoryPrices = ChildrenCategoryService::getChildrenCategoryPrices($this->getState('filter.rate_id'));
			$childrenCategoryPricesTemp = $childrenCategoryPrices;
			
			$rateInfo =  new stdClass();
			$rateInfo->date = date("Y-m-d",mktime(0,0,0,$month,$i,$year));
			$dayMonth = date("N",strtotime($rateInfo->date));
			$string_price = "price_".$dayMonth;
			$rateInfo->price = $rate->$string_price;
			$rateInfo->child_price = $rate->child_price;
			$rateInfo->single_use_price = $rateInfo->price + $rate->single_balancing;
			if($rate->price_type==0){
				$rateInfo->single_use_price = $rateInfo->price - $rate->single_balancing;
			}
			$rateInfo->extra_pers_price = $rate->extra_pers_price;
			$rateInfo->availability = $rate->availability;
			$rateInfo->min_days = $rate->min_days;
			$rateInfo->max_days = $rate->max_days;
			$rateInfo->lock_arrival = 0;
			$rateInfo->lock_departure = 0;
			$rateInfo->booked = 0;
			$rateInfo->available = 0;
			
			foreach ($childrenCategoryPricesTemp as $childrenCategoryPrice){
				$childrenCategoryPrice->date = $rateInfo->date;
			}
			$rateInfo->childrenCategoryPrices = $childrenCategoryPricesTemp;
				
			$rates[] = $rateInfo;
		}
		
		return $rates;
	}
	
	public function saveDefaultRates($rateId,$month, $year){
		$this->populateState();
		$this->getState();
		$this->setState('filter.rate_id',$rateId);
		$rates = $this->getDefaultRates($month, $year);
		
		foreach ($rates as $rate){
			$table = $this->getTable();

			$rateInfo =  array();
			$rateInfo["date"] = $rate->date;
			$rateInfo["rate_id"] = $rateId;
			$rateInfo["price"] = $rate->price;
			$rateInfo["child_price"] = $rate->child_price;
			$rateInfo["single_use_price"] = $rate->single_use_price;
			$rateInfo["extra_pers_price"] =  $rate->extra_pers_price;
			$rateInfo["availability"] = $rate->availability;
			$rateInfo["min_days"] = $rate->min_days;
			$rateInfo["max_days"] = $rate->max_days;
			$rateInfo["lock_arrival"] = $rate->lock_arrival;
			$rateInfo["lock_departure"] = $rate->lock_departure;
				
			// Bind the data.
			if (!$table->bind($rateInfo)) {
				$this->setError($table->getError());
				return false;
			}
			
			// Check the data.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}
			
			// Store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
		}
	}
	
	
	function getNumberOfBookingsPerDay($roomId, $startDate, $endDate){
		$reservedRooms = array();

		$query = "select hcr.room_id, c.rooms, c.start_date, c.end_date
			from #__hotelreservation_confirmations_rooms hcr
			left join #__hotelreservation_confirmations c on c.confirmation_id= hcr.confirmation_id
			where (c.end_date >='$startDate' and c.start_date <'$endDate') and c.reservation_status <> ".CANCELED_ID." and hcr.room_id = $roomId";

		$this->_db->setQuery($query);
		$reservationInfos = $this->_db->loadObjectList();

		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
			$dayString = date("Y-m-d", $d);
			foreach($reservationInfos as $reservationInfo){
					if( strtotime($reservationInfo->start_date)== $d || ($d<strtotime($reservationInfo->end_date) && $d>strtotime($reservationInfo->start_date)) ){
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
	
	//function to check rate validity for cubilis
	public function checkRateData($rates){
		$messages = array();
		foreach($rates as $rate){
			//check if Rate plan id is valid;
			$roomRateObj = $this->getTable("roomrate");
			$roomRateObj->load($rate->rateId);
			if(empty($roomRateObj->id)){
				//array_push($messages, "Invalid rate id -". $rate->rateId);
				array_push($messages, "Invalid InvCode");
				break;
			}

			//check if room is valid;
			if($roomRateObj->room_id!=$rate->roomId){
				//array_push($messages, "Invalid room id -". $rate->roomId." for rate plan id-".$rate->rateId);
				array_push($messages, "Invalid InvCode");
				break;
			}
		}

		return $messages;
	}
	
	public function saveCustomDates($rates){
		$messages = array();
		foreach($rates as $rate){
			$bookings = $this->getNumberOfBookingsPerDay($rate->roomId,$rate->startDate,$rate->endDate );
		
			for( $d = strtotime($rate->startDate);$d < strtotime($rate->endDate); ){
				$dayString = date("Y-m-d", $d);
				$nrBookings = 0;
				if(isset($bookings[$rate->roomId][$dayString])){
					$nrBookings = $bookings[$rate->roomId][$dayString];
				}
				$table = $this->getTable();
				$rateObj = $table->getRateDetails($rate->rateId, $dayString);
				
				if(!isset($rateObj)){
					$month = date("m",$d);
					$year = date("Y",$d);
					$this->saveDefaultRates($rate->rateId,$month, $year);
					//dmp("create default rates");
					$table = $this->getTable();
					$rateObj = $table->getRateDetails($rate->rateId, $dayString);
					//dmp($rateObj);
				}
								
				$rateInfo =  array();
				$rateInfo["id"]= $rateObj->id; 
				$rateInfo["date"] = $dayString;
				$rateInfo["rate_id"] = $rate->rateId;
				$rateInfo["price"] = $rate->rate;
				$rateInfo["single_use_price"] = $rate->single_use;
				$rateInfo["availability"] = $nrBookings+ $rate->nrRoomsAvailable;
				$rateInfo["min_days"] = $rate->minDays;
				$rateInfo["max_days"] =$rate->maxDays;
				$rateInfo["lock_arrival"] = $rate->lockForArrival;
				$rateInfo["lock_departure"] = $rate->lockForDeparture;
				
				if(!$this->saveRate($rateInfo))
					array_push($messages, "Cannot save rate". $rateObj->id);
				$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
			}
		}
		return $messages;
	}
	
	/**
	 * Save custom rates by quick setup 
	 * If a rate belongs to a month that have no custom rates defined, all custom rates for that month are generated based on default rate
	 * 
	 * @param unknown_type $data
	 */
	function quickSetup($data){
		dmp($data);
		for( $d = strtotime($data["start_date"]);$d <= strtotime($data["end_date"]); ){
			$dayString = date("Y-m-d", $d);
		
			$table = $this->getTable();
			$rateObj = $table->getRateDetails($data["rate_id"], $dayString);

			if(!isset($rateObj)){
				$month = date("m",$d);
				$year = date("Y",$d);
				$this->saveDefaultRates($data["rate_id"],$month, $year);
				$table = $this->getTable();
				$rateObj = $table->getRateDetails($data["rate_id"], $dayString);
			}
		
			$weekDay = date("N", $d);
			
			if(!in_array($weekDay, $data["week_day"])){
				$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
				continue;
			}
			
			if(isset($data["price"]) && strlen($data["price"])>0)
				$rateObj->price = $data["price"];
			if(isset($data["single_use_price"]) && strlen($data["single_use_price"])>0)
				$rateObj->single_use_price = $data["single_use_price"];
			if(isset($data["availability"]) && strlen($data["availability"])>0)
				$rateObj->availability = $data["availability"];
			if(isset($data["extra_pers_price"]) && strlen($data["extra_pers_price"])>0)
				$rateObj->extra_pers_price = $data["extra_pers_price"];
			if(isset($data["min_days"]) && strlen($data["min_days"])>0)
				$rateObj->min_days = $data["min_days"];
			if(isset($data["max_days"]) && strlen($data["max_days"])>0)
				$rateObj->max_days = $data["max_days"];
			if(isset($data["child_price"]) && strlen($data["child_price"])>0)
				$rateObj->child_price = $data["child_price"];
			if(isset($data["lock_arrival"]) && strlen($data["lock_arrival"])>0)
				$rateObj->lock_arrival = $data["lock_arrival"];
			if(isset($data["lock_departure"]) && strlen($data["lock_departure"])>0)
				$rateObj->lock_departure = $data["lock_departure"];
		
			$app = JFactory::getApplication('administrator');
			$childrenCategories = ChildrenCategoryService::getChildrenCategories($app->getUserState('com_jhotelreservation.rooms.filter.hotel_id'));

			$this->saveRate($rateObj);
			
						
			foreach($childrenCategories as $childrenCategory){
				if(!empty($data["child_price_".$childrenCategory->id])){
					$table = $this->getTable("childcategorycustomprices");
					$rateObj->id = $table->getRateDetails($data["rate_id"], $dayString,$childrenCategory->id)->id;
					$tmp = "child_price_".$childrenCategory->id;
					$rateObj->price = $data["child_price_".$childrenCategory->id];
					$rateObj->category_id = $childrenCategory->id;
						
					$this->saveCustomChildRate($rateObj);
				}
			}
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}

		return true;
	}
	
	function saveRate($rateInfo){
		$table = $this->getTable();
		// Bind the data.
		if (!$table->bind($rateInfo)) {
			$this->setError($table->getError());
			return false;
		}
		
		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}
		
		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
		
		return true;
	}
	
	function saveCustomChildRate($rateInfo){
		$table = $this->getTable("childcategorycustomprices");
		// Bind the data.
		if (!$table->bind($rateInfo)) {
			$this->setError($table->getError());
			return false;
		}
	
		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}
	
		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
	
		return true;
	}
	
	function getEditInfo(){
		$app = JFactory::getApplication('administrator');
		
		$tableHotel = $this->getTable('hotels','Table');
		
		$language = JFactory::getLanguage();
		$language_tag 	= $language->getTag();
		
		$hotelId = $app->getUserState('com_jhotelreservation.rooms.filter.hotel_id');
		$roomId = $app->getUserState('com_jhotelreservation.edit.room.room_id');
		$translations = new JHotelReservationLanguageTranslations();
		$roomTranslation = $translations->getObjectTranslation(ROOM_NAME,$roomId,$language_tag);
				
		$hotelObj = $tableHotel->getHotel($hotelId);

		$editInfo = JText::_('LNG_HOTEL').":<b>".$hotelObj->hotel_name."</b><br>".JText::_('LNG_ROOM').":<b>".strip_tags($roomTranslation->content)."</b><br>";
				
		return $editInfo;
	}
}
