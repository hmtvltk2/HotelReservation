<?php


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once 'reports.php';

/**
 * Menu List Model for Excursions.
 *
 */
class JHotelReservationModelExcursionRatePrices extends JModelList
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
				'excursion_id', 'r.id',
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
	
	public function getTable($type = 'ExcursionRatePrices', $prefix = 'JTable', $config = array())
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
		
		$hotelId = $app->getUserState('com_jhotelreservation.excursions.filter.hotel_id');
		$excursionId = $app->getUserState('com_jhotelreservation.edit.excursion.id');
		if(empty($excursionId)){
			$excursionId = JRequest::getVar("excursion_id");
		}
		$excursionId = $excursionId[0];
		
		if (empty($items)){	
			$items =  $this->getDefaultRates($month, $year);
			$this->setState('filter.newRates', true);
		}
	
		$this->setState('filter.start_date', JHotelUtil::convertToFormat($startDate));
		$this->setState('filter.end_date', JHotelUtil::convertToFormat($endDate));
		
		JRequest::setVar("filter_start_date",$startDate);
		JRequest::setVar("filter_end_date",$endDate);
		JRequest::setVar("filter_excursion_type",$endDate);
		JRequest::setVar("hotel_id",$hotelId);
		
		foreach($items as $item){
			//dmp($item->date);
			$item->booked = 0;
			$item->available = 0;
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
		$query->from($db->quoteName('#__hotelreservation_excursion_rate_prices').' AS r');

		
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
		$nrDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		for($i=1;$i<=$nrDays;$i++){
			$rateInfo =  array();
			
			for($j=2;$j<=10;$j++){
				if(isset($data["price"][$i."_".$j])&&$data["price"][$i."_".$j]>0){
					$rateInfo["price_$j"] = $data["price"][$i."_".$j];
				}
			}
			
			$rateInfo["date"] = date("Y-m-d",mktime(0,0,0,$month,$i,$year));
			$rateInfo["rate_id"] = $data["rate_id"];
			$rateInfo["price"] = $data["price"][$i];
			$rateInfo["child_price"] = $data["child_price"][$i];
			$rateInfo["availability"] = $data["availability"][$i];
			$rateInfo["min_days"] = $data["min_days"][$i];
			$rateInfo["max_days"] = $data["max_days"][$i];
			
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
	
	function deleteOldRatePrices($month, $rateId ){
		// Create a new query object.
		$db		= $this->getDbo();
		$rateId = $this->getState('filter.rate_id');
		$query	= $db->getQuery(true)
		->delete("")
		->from(' #__hotelreservation_excursion_rate_prices ');
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
		->from('#__hotelreservation_excursion_rates')
		->where('id='.$rateId);
		$db->setQuery($query,0,1);
		//echo $query->dmp();
		$rate = $db->loadObject();
	
		return $rate;
	}
	
	
	public function getDefaultRates($month, $year){
		$nrDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$rates = array();
		$rate = $this->getRate();
		//dmp($rate);
		for($i=1;$i<=$nrDays;$i++){
			$rateInfo =  new stdClass();
			$rateInfo->date = date("Y-m-d",mktime(0,0,0,$month,$i,$year));
			$dayMonth = date("N",strtotime($rateInfo->date));
			$string_price = "price_".$dayMonth;
			$rateInfo->price = $rate->$string_price;
			$rateInfo->child_price = $rate->child_price;
			$rateInfo->availability= $rate->availability;
			$rateInfo->min_days = $rate->min_days;
			$rateInfo->max_days = $rate->max_days;
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
			$rateInfo["availability"] = $rate->availability;
			$rateInfo["min_days"] = $rate->min_days;
			$rateInfo["max_days"] = $rate->max_days;
				
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
	
	
	function getNumberOfBookingsPerDay($excursionId, $startDate, $endDate){
		$reservedExcursions = array();

		$query = "select hcr.excursion_id, hcr.excursions, hcr.datas, hcr.datae
			from #__hotelreservation_confirmations_excursions hcr
			left join #__hotelreservation_confirmations c on c.confirmation_id= hcr.confirmation_id
			where (hcr.datae >='$startDate' and hcr.datas <'$endDate') and c.reservation_status <> ".CANCELED_ID." and hcr.excursion_id = $excursionId";

		$this->_db->setQuery($query);
		$reservationInfos = $this->_db->loadObjectList();

		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
			$dayString = date("Y-m-d", $d);
			foreach($reservationInfos as $reservationInfo){
				if( strtotime($reservationInfo->datas)<= $d && $d<strtotime($reservationInfo->datae) ){
					if(!isset($reservedExcursions[$reservationInfo->excursion_id]) || !isset($reservedExcursions[$reservationInfo->excursion_id][$dayString]) ){
						$reservedExcursions[$reservationInfo->excursion_id][$dayString] = 0;
					}
					$reservedExcursions[$reservationInfo->excursion_id][$dayString] = $reservedExcursions[$reservationInfo->excursion_id][$dayString] +1;
				}
			}
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}

		return $reservedExcursions;
	}
	
	public function saveCustomDates($rates){
		foreach($rates as $rate){
			$bookings = $this->getNumberOfBookingsPerDay($rate->excursionId,$rate->startDate,$rate->endDate );
			dmp($bookings);	
		
			for( $d = strtotime($rate->startDate);$d < strtotime($rate->endDate); ){
				$dayString = date("Y-m-d", $d);
				$nrBookings = 0;
				if(isset($bookings[$rate->excursionId][$dayString])){
					$nrBookings = $bookings[$rate->excursionId][$dayString];
				}
				dmp($nrBookings);
				$table = $this->getTable();
				//dmp($rate->rateId); 
				$rateObj = $table->getRateDetails($rate->rateId, $dayString);
				//dmp($rateObj);
				
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
				$rateInfo["availability"] = $nrBookings+ $rate->nrExcursionsAvailable;
				$rateInfo["min_days"] = $rate->minDays;
				$rateInfo["max_days"] =$rate->maxDays;
				$rateInfo["lock_arrival"] = $rate->lockForArrival;
				$rateInfo["lock_departure"] = $rate->lockForDeparture;
				
				$this->saveRate($rateInfo);
				
				$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
			}
		}
		
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
			if(isset($data["availability"]) && strlen($data["availability"])>0)
				$rateObj->availability = $data["availability"];
			if(isset($data["min_days"]) && strlen($data["min_days"])>0)
				$rateObj->min_days = $data["min_days"];
			if(isset($data["max_days"]) && strlen($data["max_days"])>0)
				$rateObj->max_days = $data["max_days"];
			if(isset($data["child_price"]) && strlen($data["child_price"])>0)
				$rateObj->child_price = $data["child_price"];
			if(isset($data["price_2"]) && strlen($data["price_2"])>0)
				$rateObj->price_2 = $data["price_2"];
			if(isset($data["price_3"]) && strlen($data["price_3"])>0)
				$rateObj->price_3 = $data["price_3"];
			if(isset($data["price_4"]) && strlen($data["price_4"])>0)
				$rateObj->price_4 = $data["price_4"];
			if(isset($data["price_5"]) && strlen($data["price_5"])>0)
				$rateObj->price_5 = $data["price_5"];
			if(isset($data["price_6"]) && strlen($data["price_6"])>0)
				$rateObj->price_6 = $data["price_6"];
			if(isset($data["price_7"]) && strlen($data["price_7"])>0)
				$rateObj->price_7 = $data["price_7"];
			if(isset($data["price_8"]) && strlen($data["price_8"])>0)
				$rateObj->price_8 = $data["price_8"];
			if(isset($data["price_9"]) && strlen($data["price_9"])>0)
				$rateObj->price_9 = $data["price_9"];
			if(isset($data["price_10"]) && strlen($data["price_10"])>0)
				$rateObj->price_10 = $data["price_10"];
			if(isset($data["price_11"]) && strlen($data["price_11"])>0)
				$rateObj->price_11 = $data["price_11"];
			if(isset($data["price_12"]) && strlen($data["price_12"])>0)
				$rateObj->price_12 = $data["price_12"];
			if(isset($data["price_13"]) && strlen($data["price_13"])>0)
				$rateObj->price_13 = $data["price_13"];
			if(isset($data["price_14"]) && strlen($data["price_14"])>0)
				$rateObj->price_14 = $data["price_14"];
			if(isset($data["price_16"]) && strlen($data["price_16"])>0)
				$rateObj->price_16 = $data["price_16"];
			if(isset($data["price_17"]) && strlen($data["price_17"])>0)
				$rateObj->price_17 = $data["price_17"];
			$this->saveRate($rateObj);
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
}
