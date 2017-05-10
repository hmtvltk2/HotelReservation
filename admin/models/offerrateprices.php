<?php


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once 'reports.php';
/**
 * Menu List Model for Rooms.
 *
 */
class JHotelReservationModelOfferRatePrices extends JModelList
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

		$offerId= JRequest::getVar('offer_id', null);
		if(isset($offerId)){
			$app->setUserState('com_jhotelreservation.edit.offer.offerId',$offerId);
		}
		
		$hotelId= JRequest::getVar('hotel_id', null);
		if(isset($hotelId)){
			$app->setUserState('com_jhotelreservation.edit.offer.hotelId',$hotelId);
		}
		//dmp($hotelId);
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
	
	public function getTable($type = 'OfferRatePrices', $prefix = 'JTable', $config = array())
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
	
		// Getting the following metric by joins is WAY TOO SLOW.
		// Faster to do three queries for very large menu trees.

		// If emtpy or an error, just return.
		$month = $this->getState('filter.month');
		$year = $month<date("n")? date("Y")+1 : date("Y");
		
		if (empty($items))
		{
		
			return $this->getDefaultRates($month, $year);
		}else{
			foreach($items as $item){
				$item->booked = 0;
				$item->available = 0;
			}
		}
		
		$startDate  = date("Y-m-d",mktime(0,0,0,$month,1,$year));
		$endDate = date("Y-m-d",mktime(0,0,0,$month+1,0,$year));
		
		$this->setState('filter.start_date', JHotelUtil::convertToFormat($startDate));
		$this->setState('filter.end_date', JHotelUtil::convertToFormat($endDate));
		

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
		$query->from($db->quoteName('#__hotelreservation_offers_rate_prices').' AS r');

		
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
		->from(' #__hotelreservation_offers_rate_prices ');
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
		$query	= $db->getQuery(true)
		->select('*')
		->from('#__hotelreservation_offers_rates')
		->where('id='.$rateId);
		$db->setQuery($query,0,1);
		$rate = $db->loadObject();
	
		return $rate;
	}
	
	
	public function getDefaultRates($month, $year){
		$nrDays = date('t', mktime(0,0,0, $month,1, $year));
		$rates = array();
		$rate = $this->getRate();
		for($i=1;$i<=$nrDays;$i++){
			$rateInfo =  new stdClass();
			$rateInfo->isNew = true;
			$rateInfo->date = date("Y-m-d",mktime(0,0,0,$month,$i,$year));
			$dayMonth = date("N",strtotime($rateInfo->date));
			$string_price = "price_".$dayMonth;
			$rateInfo->price = $rate->$string_price;
			$rateInfo->child_price = $rate->child_price;
			$rateInfo->single_use_price = $rateInfo->price + $rate->single_balancing;
			if($rate->price_type==0){
				$rateInfo->single_use_price = $rateInfo->price - $rate->single_balancing;
			}
			$string_pers_price = "extra_pers_price_".$dayMonth;
			$rateInfo->extra_pers_price = $rate->$string_pers_price;
			$rates[] = $rateInfo;
		}
		
		return $rates;
	}
	
	/**
	 * 
	 * @param unknown_type $rateId
	 * @param unknown_type $month
	 * @param unknown_type $year
	 */
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
	
	/**
	 * Save custom rates by quick setup
	 * If a rate belongs to a month that have no custom rates defined, all custom rates for that month are generated based on default rate
	 *
	 * @param unknown_type $data
	 */
	function quickSetup($data){
		//dmp($data);
		for( $d = strtotime($data["start_date"]);$d <= strtotime($data["end_date"]); ){
			$dayString = date("Y-m-d", $d);
	
			$weekDay = date("N", $d);
				
			if(!in_array($weekDay, $data["week_day"])){
				$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
				continue;
			}
			
			$table = $this->getTable();
			$rateObj = $table->getRateDetails($data["rate_id"], $dayString);
	
			if(!isset($rateObj)){
				$month = date("m",$d);
				$year = date("Y",$d);
				$this->saveDefaultRates($data["rate_id"],$month, $year);
				$table = $this->getTable();
				$rateObj = $table->getRateDetails($data["rate_id"], $dayString);
			}
				
			if(isset($data["price"]) && strlen($data["price"])>0)
				$rateObj->price = $data["price"];
			if(isset($data["single_use_price"]) && strlen($data["single_use_price"])>0)
				$rateObj->single_use_price = $data["single_use_price"];
			if(isset($data["child_price"]) && strlen($data["child_price"])>0)
				$rateObj->child_price = $data["child_price"];
			if(isset($data["extra_pers_price"]) && strlen($data["extra_pers_price"])>0)
				$rateObj->extra_pers_price = $data["extra_pers_price"];
			
	
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
