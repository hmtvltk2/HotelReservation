<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Menu Item Model for Menus.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_jhotelreservation
 * @version		1.6
 */
class JHotelReservationModelExcursion extends JModelAdmin{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_JHOTELRESERVATION_EXCURSION';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jhotelreservation.excursion';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canEditState($record)
	{
		return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Excursion', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void

	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$pk = (int) JRequest::getInt('id');
		if(!$pk)
			$pk = (int) JRequest::getInt('id');
		$this->setState('excursion.excursion_id', $pk);
	
		if (!($hotelId = $app->getUserState('com_jhotelreservation.edit.excursion.hotel_id'))) {
			$hotelId = JRequest::getInt('hotel_id', '0');
		}
		
		//dmp($hotelId);
		
		//$app->setUserState('com_jhotelreservation.edit.excursion.hotel_id',$hotelId); 
		$app->setUserState('com_jhotelreservation.edit.excursion.excursion_id',$pk);
		$this->setState('excursion.hotel_id', $hotelId);
	} 

	/**
	 * Method to get a menu item.
	 *
	 * @param	integer	The id of the menu item to get.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null)
	{
		// Initialise variables.
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('excursion.excursion_id');
		$false	= false;

		// Get a menu item row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		if (!empty($table->excursion_id)) {
			$this->setState('excursion.hotel_id', $table->hotel_id);	
		}
		
		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');

		$countryTable = $this->getTable('Country','JTable');
		$value->countries = $countryTable->getCountries();

		$rateTable = $this->getTable("ExcursionRate","JTable");
		$query = ' SELECT currency_id, description FROM #__hotelreservation_currencies';
		$value->currencies = $this->_getList( $query );
		
		//dmp($rateTable);
		$keys = array();
		$keys["excursion_id"]=$table->id;
		
		$return = $rateTable->load($keys);
		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}
		
		 // Check for a table object error.
		 if ($return === false && $table->getError()) {
		 	$this->setError($table->getError());
		 	return $false;
		 }
		
		$propertiesRate = $rateTable->getProperties(1);
		$rateValue = JArrayHelper::toObject($propertiesRate, 'JObject');	
		$value->rate = $rateValue;
		
		return $value;
	}

	/**
	 * Method to get the menu item form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jhotelreservation.excursion', 'excursion', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.excursion.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 */
	public function save($data)
	{
		$id	= (!empty($data['id'])) ? $data['id'] : (int)$this->getState('excursion.id');
		$isNew	= true;
		
		
		for( $day=1;$day<=7;$day ++ )
		{
			if( !isset($data["excursion_day_$day"]) )
				$data["excursion_day_$day"] = 0;
		}
		// Get a row instance.
		$table = $this->getTable();
		if(empty($data['ordering']))
			$data['ordering'] =$table->getExcursionOrder();
		
		// Load the row if saving an existing item.
		if ($id > 0) {
			$table->load($id);
			$isNew = false;
		}
		

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		
		$table->name = $data["excursion_name"];
		$table->data_start = JHotelUtil::convertToMysqlFormat($data["data_start"]);
		$table->data_end =  JHotelUtil::convertToMysqlFormat($data["data_end"]);
		
		
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

		$this->setState('excursion.id', $table->id);

		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	public function saveRate($data)
	{
		$id	= (!empty($data['rate_id'])) ? $data['rate_id'] : (int)$this->getState('excursion.rate_id');
		$isNew	= true;
		// Get a row instance.
		$table = $this->getTable("ExcursionRate");

		// Load the row if saving an existing item.
		if ($id > 0) {
			$table->load($id);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		$table->id = $id;

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
		$this->setState('excursion.rate_id', $table->id);	
		
		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	public function savePictures($data){
		//prepare photos
		
		$path_old = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.PATH_EXCURSION_PICTURES.($data['excursion_id']+0)."/");
		$files = glob( $path_old."*.*" );
			
		$data['id'] = $this->getState('excursion.id');
		$path_new = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.PATH_EXCURSION_PICTURES.($data['excursion_id']+0)."/");
		
		$picture_ids 	= array();
		foreach( $data['pictures'] as $value )
		{				
			$row = $this->getTable('ExcursionPictures','JTable');
		
			// dmp($key);
			$pic 						= new stdClass();
			$pic->picture_id		= 0;
			$pic->excursion_id 				= $data['excursion_id'];
			$pic->picture_info		= $value['excursion_picture_info'];
			$pic->picture_path		= $value['excursion_picture_path'];
			$pic->picture_enable	= $value['excursion_picture_enable'];
			$file_tmp = JHotelUtil::makePathFile( $path_old.basename($pic->picture_path) );
			if( !is_file($file_tmp) )
				continue;
		
			if( !is_dir($path_new) )
			{
				if( !@mkdir($path_new) )
				{
					throw( new Exception($this->_db->getErrorMsg()) );
				}
			}
		
			// dmp(($path_old.basename($pic->excursion_picture_path).",".$path_new.basename($pic->excursion_picture_path)));
			// exit;
			if( $path_old.basename($pic->excursion_picture_path) != $path_new.basename($pic->excursion_picture_path) )
			{
				if(@rename($path_old.basename($pic->excursion_picture_path),$path_new.basename($pic->excursion_picture_path)) )
				{
		
					$pic->excursion_picture_path	 = PATH_EXCURSION_PICTURES.($data['excursion_id']+0).'/'.basename($pic->excursion_picture_path);
					//@unlink($path_old.basename($pic->excursion_picture_path));
				}
				else
				{
					throw( new Exception($this->_db->getErrorMsg()) );
				}
			}
			if (!$row->bind($pic))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
				return false;
				
					
			}
				
			// Make sure the record is valid
			if (!$row->check())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
				return false;
				
			}
		
			// Store the web link table to the database
			if (!$row->store())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
				return false;
				
			}
		
			$picture_ids[] = $this->_db->insertid();
		
		
		}
		
		
		$files = glob( $path_new."*.*" );
		foreach( $files as $pic )
		{
			$is_find = false;
			foreach( $data['pictures'] as $value )
			{
				//echo $pic."==".JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.$value['excursion_picture_path']);
				if( $pic == JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.$value['excursion_picture_path']) )
				{
					$is_find = true;
					break;
				}
			}
			/*if( $is_find == false )
				@unlink( JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.DS.$value['excursion_picture_path']) );*/
		}
		$query = " DELETE FROM #__hotelreservation_excursion_pictures
		WHERE excursion_id = '".$data['excursion_id']."'
		".( count($picture_ids)> 0 ? " AND picture_id NOT IN (".implode(',', $picture_ids).")" : "");
		
		// dmp($query);
		// exit;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		//~prepare photos
		return true;
		
	}

	
	public function setHotelMinExcursionPrice(){
		$hotelId = $this->getState('excursion.hotel_id');
		
		$query="select rr.base_adults, rr.price_type, least(rr.price_1, rr.price_2, rr.price_3, rr.price_4, rr.price_5, rr.price_6, rr.price_7) as min_rate, 
      				   min(rrp.price) as min_rate_custom
				from #__hotelreservation_excursions r
				inner join #__hotelreservation_excursions_rates rr on r.id = rr.excursion_id
				left join #__hotelreservation_excursions_rate_prices rrp on rrp.rate_id = rr.id
				where r.is_available = 1 and r.front_display=1 and r.hotel_id= $hotelId
		        group by r.hotel_id";
		//dmp($query);
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObject();
		$price = 0;
		if(!empty($result)){
			$price = $result->min_rate;
			if(isset($result->min_rate_custom) && $price>$result->min_rate_custom){
				$price = $result->min_rate_custom;
			}
			
			if($result->price_type == 0){
				$price = $price / $result->base_adults;
			}
		}
		$query="update #__hotelreservation_hotels set min_excursion_price = $price where hotel_id = $hotelId ";
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			dmp($query);
			dmp("error");
		}
		
	}
	
	/**
	 * Method to delete groups.
	 *
	 * @param	array	An array of item ids.
	 * @return	boolean	Returns true on success, false on failure.
	 */
	public function delete(&$itemIds)
	{
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		JArrayHelper::toInteger($itemIds);

		// Get a group row instance.
		$table = $this->getTable('Excursion','JTable');

		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId) {
		
			if (!$table->delete($itemId))
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param	array	$pks	A list of the primary keys to change.
	 * @param	int		$value	The value of the published state.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	function publish(&$pks, $value = 1)
	{
		// Initialise variables.
		$table		= $this->getTable();
		$pks		= (array) $pks;
	
		// Clean the cache
		$this->cleanCache();
	
		return parent::publish($pks, $value);
	}
	
	/**
	 * Custom clean cache method
	 *
	 * @since	1.6
	 */
	protected function cleanCache($group = null, $client_id = 0) {
		//parent::cleanCache('com_modules');
		//parent::cleanCache('mod_menu');
	}
	
	function &getHotel()
	{
		$query = 	' SELECT
		h.*,
		c.country_name
		FROM #__hotelreservation_hotels				h
		LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
		'.
		' WHERE
		hotel_id = '.$this->getState('excursion.hotel_id');
	
		$this->_db->setQuery( $query );
		$h = $this->_db->loadObject();
		return  $h;
	}
	
	
		
	function getExcursionPictures(){
		//check temporary files
		$query = "
		SELECT
		*
		FROM #__hotelreservation_excursion_pictures
		WHERE excursion_id =".$this->getState('excursion.excursion_id') ."
		ORDER BY picture_id "
		;
		// dmp($query);
		//$this->_db->setQuery( $query );
		$files = $this->_getList( $query );
		$pictures			= array();
		if(count($files)>0){
			foreach( $files as $value )
			{
				$pictures[]	= array(
						'picture_info' 		=> $value->picture_info,
						'picture_path' 		=> $value->picture_path,
						'picture_enable'		=> $value->picture_enable,
				);
			}
		}
		return $pictures;
	}
	
	function changeState($excursionId)
	{
		$query = 	" UPDATE #__hotelreservation_excursions SET is_available = IF(is_available, 0, 1) WHERE id = ".$excursionId ;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}
	
	function changeFrontState($excursionId)
	{
		//$this->migrateExcursions();
		//$this->migrateOffers();
		$query = 	" UPDATE #__hotelreservation_excursions SET front_display = IF(front_display, 0, 1) WHERE id = ".$excursionId ;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true; 
	}

	
	function migrateExcursions(){
		$query = 	" select * from #__hotelreservation_excursions";
		$excursions = $this->_getList( $query );
			
		foreach($excursions as $excursion){
			
			switch( $excursion->type_price ){
				case 0:
					break;
				case 2:
					$excursion->excursion_price_1 = $excursion->excursion_price_midweek;
					$excursion->excursion_price_2 = $excursion->excursion_price_midweek;
					$excursion->excursion_price_3 = $excursion->excursion_price_midweek;
					$excursion->excursion_price_4 = $excursion->excursion_price_midweek;
					$excursion->excursion_price_5 = $excursion->excursion_price_weekend;
					$excursion->excursion_price_6 = $excursion->excursion_price_weekend;
					$excursion->excursion_price_7 = $excursion->excursion_price_midweek;
					break;
				case 1:
					$excursion->excursion_price_1 = $excursion->excursion_price;
					$excursion->excursion_price_2 = $excursion->excursion_price;
					$excursion->excursion_price_3 = $excursion->excursion_price;
					$excursion->excursion_price_4 = $excursion->excursion_price;
					$excursion->excursion_price_5 = $excursion->excursion_price;
					$excursion->excursion_price_6 = $excursion->excursion_price;
					$excursion->excursion_price_7 = $excursion->excursion_price;
					break;
			}
			
			$single_price  = $excursion->single_discount;
			if($excursion -> pers_price==1)
				$single_price  = $excursion->single_supplement;
			$query = " insert into #__hotelreservation_excursions_rates(excursion_id,name,can_cancel,child_price,extra_pers_price,price_type,price_1,price_2,price_3,price_4,price_5,price_6,price_7,availability,single_balancing, min_days, max_days, base_adults,base_children) 
			values($excursion->excursion_id,'Best Available Rate',0,0,0,$excursion->pers_price,$excursion->excursion_price_1,$excursion->excursion_price_2,$excursion->excursion_price_3,$excursion->excursion_price_4,$excursion->excursion_price_5,$excursion->excursion_price_6,$excursion->excursion_price_7, $excursion->number_of_excursions,$single_price,1,0,2,0) ";
			
			$this->_db->setQuery( $query );
			if (!$this->_db->query()){
				dmp($query);
			}
			
		}

		exit;
		
				
	}
	
	public function getModel($name = 'ExcursionRatePrices', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	function getTranslations(){

		$hoteltranslationsModel = new JHotelReservationLanguageTranslations();
		$translations = $hoteltranslationsModel->getAllTranslations(EXCURSION_TRANSLATION, $this->getState('excursion.excursion_id'));
		return $translations;
	}
	
	function saveExcursionDescriptions($data){
	
		try{
			$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
			$dirs = JFolder::folders( $path );
			sort($dirs);
			$modelHotelTranslations = new JHotelReservationLanguageTranslations();
			$modelHotelTranslations->deleteTranslationsForObject(EXCURSION_TRANSLATION,$data['id']);
			foreach( $dirs  as $_lng ){
				if(isset($data['excursion_main_description_'.$_lng]) && strlen($data['excursion_main_description_'.$_lng])>0){
					$excursionDescription = 		JRequest::getVar( 'excursion_main_description_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
					$modelHotelTranslations->saveTranslation(EXCURSION_TRANSLATION,$data['excursion_id'],$_lng,$excursionDescription);
				}
			}
		}
		catch(Exception $e){
			print_r($e);
			exit;
			JError::raiseWarning( 500,$e->getMessage());
		}
	
	}


    function publishList($cid, $is_available=true)
    {
        if (!is_array($cid)) $cid = array($cid);
        if (count($cid))
        {
            $sql = "UPDATE #__hotelreservation_excursions SET is_available = ".($is_available?"1":"0")
                ." WHERE id IN (".join(',', $cid).")";
            $this->_db->setQuery($sql);
            return $this->_db->query();
        }
        return false;
    }

    function unPublishList($cid, $is_available=false)
    {
        if (!is_array($cid)) $cid = array($cid);
        if (count($cid))
        {
            $sql = "UPDATE #__hotelreservation_excursions SET is_available = ".(!$is_available?"0":"1")
                ." WHERE id IN (".join(',', $cid).")";
            $this->_db->setQuery($sql);
            return $this->_db->query();
        }
        return false;
    }
}
