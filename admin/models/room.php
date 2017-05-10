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
class JHotelReservationModelRoom extends JModelAdmin{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_JHOTELRESERVATION_ROOM';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jhotelreservation.room';

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
	public function getTable($type = 'Room', $prefix = 'JTable', $config = array())
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
		$pk = (int) JRequest::getInt('room_id');
		if(!$pk)
			$pk = (int) JRequest::getInt('id');
		$this->setState('room.room_id', $pk);
	
		if (!($hotelId = $app->getUserState('com_jhotelreservation.rooms.filter.hotel_id'))) {
			$hotelId = JRequest::getInt('hotel_id', '0');
		}
        $this->setState('room.hotel_id', $hotelId);
		
		
		$app->setUserState('com_jhotelreservation.edit.room.hotel_id',$hotelId); 
		$app->setUserState('com_jhotelreservation.edit.room.room_id',$pk);
	
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
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('room.room_id');
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

		if (!empty($table->room_id)) {
			$this->setState('room.hotel_id', $table->hotel_id);	
		}
		
		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');
		$value->option_ids  = explode(',', $value->option_ids);

		$rateTable = $this->getTable("RoomRate","JTable");
		//dmp($rateTable);
		$keys = array();
		$keys["room_id"]=$table->room_id;
		
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


		if (!empty($table->room_id)) {
			$value->hasCubilis = UserService::isChannelManagerSet($table->hotel_id,CHANNEL_MANAGER_CUBILIS);
		}
		
		if (!empty($table->room_id)) {
			$value->hasBeds24 = UserService::isChannelManagerSet($table->hotel_id,CHANNEL_MANAGER_BEDS24);
		}


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
		$form = $this->loadForm('com_jhotelreservation.room', 'room', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) {
			return false;
		}
        // Determine correct permissions to check.
        if ($this->getState('room.room_id'))
        {
            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');
        }
        else
        {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }

        // Modify the form based on access controls.
        if (!$this->canEditState((object) $data))
        {
            // Disable fields for display.
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('ordering', 'filter', 'unset');
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
		$data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.room.data', array());

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
		$id	= (!empty($data['room_id'])) ? $data['room_id'] : (int)$this->getState('room.room_id');
		$isNew	= true;
        $data['room_name'] = $this->setDefaultName($data);
		// Get a row instance.
		$table = $this->getTable();
		$data['ordering'] =$table->getRoomOrder();

		$data['option_ids'] = '';
		
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

		$this->setState('room.room_id', $table->room_id);

		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	public function saveRate($data)
	{
		$id	= (!empty($data['rate_id'])) ? $data['rate_id'] : (int)$this->getState('room.rate_id');
		$isNew	= true;
		// Get a row instance.
		$table = $this->getTable("RoomRate","JTable");

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

		$this->setState('room.rate_id', $table->id);	
		
		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	public function savePictures($data){
		//prepare photos
		$path_old = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.PATH_ROOM_PICTURES.($data['room_id']+0)."/");
		$files = glob( $path_old."*.*" );
			
		$data['room_id'] = $this->getState('room.room_id');
		$path_new = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.PATH_ROOM_PICTURES.($data['room_id']+0)."/");
		
		$picture_ids 	= array();

		foreach( $data['pictures'] as $value )
		{				
			$row = $this->getTable('RoomPictures','JTable');
		
			$pic 						= new stdClass();
			$pic->room_picture_id		= 0;
			$pic->room_id 				= $data['room_id'];
			$pic->room_picture_info		= $value['room_picture_info'];
			$pic->room_picture_path		= $value['room_picture_path'];
			$pic->room_picture_enable	= $value['room_picture_enable'];
			//dmp($pic);
			$file_tmp = JHotelUtil::makePathFile( $path_old.basename($pic->room_picture_path) );
		
			if( !is_file($file_tmp) )
				continue;
		
			if( !is_dir($path_new) )
			{
				if( !@mkdir($path_new) )
				{
					throw( new Exception($this->_db->getErrorMsg()) );
				}
			}
		
			// dmp(($path_old.basename($pic->room_picture_path).",".$path_new.basename($pic->room_picture_path)));
			// exit;
			if( $path_old.basename($pic->room_picture_path) != $path_new.basename($pic->room_picture_path) )
			{
				if(@rename($path_old.basename($pic->room_picture_path),$path_new.basename($pic->room_picture_path)) )
				{
		
					$pic->room_picture_path	 = PATH_ROOM_PICTURES.($data['room_id']+0).'/'.basename($pic->room_picture_path);
					//@unlink($path_old.basename($pic->room_picture_path));
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
				//echo $pic."==".JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.$value['room_picture_path']);
				if( $pic == JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.$value['room_picture_path']) )
				{
					$is_find = true;
					break;
				}
			}
			/*if( $is_find == false )
				@unlink( JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.DS.$value['room_picture_path']) );*/
		}
		$query = " DELETE FROM #__hotelreservation_rooms_pictures
		WHERE room_id = '".$data['room_id']."'
		".( count($picture_ids)> 0 ? " AND room_picture_id NOT IN (".implode(',', $picture_ids).")" : "");
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		//~prepare photos
		return true;
		
	}
	
	function saveChildrenCategoryPrices($data){
		$childrenCategories =  ChildrenCategoryService::getChildrenCategories($this->getState('room.hotel_id'));
		ChildrenCategoryService::clearChildrenPrices($data["rate_id"]);
		foreach($childrenCategories as $childCategory){
			if(isset($data["child_price_".$childCategory->id])){
				$table = $this->getTable("ChildCategoryPrices");
				$table->rate_id = $data["rate_id"];
				$table->category_id = $childCategory->id;
				$table->price = $data["child_price_".$childCategory->id];
	
				$table->store();
			}
		}
	}
	
	public function setHotelMinRoomPrice(){
		$hotelId = $this->getState('room.hotel_id');
		
		$query="select rr.base_adults, rr.price_type, least(rr.price_1, rr.price_2, rr.price_3, rr.price_4, rr.price_5, rr.price_6, rr.price_7) as min_rate, 
      				   min(rrp.price) as min_rate_custom
				from #__hotelreservation_rooms r
				inner join #__hotelreservation_rooms_rates rr on r.room_id = rr.room_id
				left join #__hotelreservation_rooms_rate_prices rrp on rrp.rate_id = rr.id
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
		$query="update #__hotelreservation_hotels set min_room_price = $price where hotel_id = $hotelId ";
		
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

		$app		= JFactory::getApplication();
		$hotelId = $app->getUserStateFromRequest('filter.hotel_id', 'hotel_id', '1', 'cmd');

		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		JArrayHelper::toInteger($itemIds);

		$table = $this->getTable();

		//Rooms JTable Method to check if room/rooms being deleted are in relation with an active reservation
		$checked_records = $table->checkRoomRecords($hotelId,$itemIds);

		// if the counter over the object list is more than one , the room cannot be deleted
		if ( count($checked_records) > 0 )
		{
			JError::raiseWarning( 500, JText::_('LNG_SKIP_ROOM_REMOVE',true) );
			return false;
		}

		// Get a group row instance.


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
		hotel_id = '.$this->getState('room.hotel_id');;
	
		$this->_db->setQuery( $query );
		$h = $this->_db->loadObject();
		return  $h;
	}
	
	function getRoomPictures(){
		//check temporary files
		$query = "
		SELECT
		*
		FROM #__hotelreservation_rooms_pictures
		WHERE room_id =".$this->getState('room.room_id') ."
		ORDER BY room_picture_id "
		;
		// dmp($query);
		//$this->_db->setQuery( $query );
		$files = $this->_getList( $query );
		$pictures			= array();
		if(count($files)>0){
			foreach( $files as $value )
			{
				$pictures[]	= array(
						'room_picture_info' 		=> $value->room_picture_info,
						'room_picture_path' 		=> $value->room_picture_path,
						'room_picture_enable'		=> $value->room_picture_enable,
				);
			}
		}
		return $pictures;
	}
	
	function changeState($roomId)
	{
		$query = 	" UPDATE #__hotelreservation_rooms SET is_available = IF(is_available, 0, 1) WHERE room_id = ".$roomId ;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}
	
	function changeFrontState($roomId)
	{
		//$this->migrateRooms();
		//$this->migrateOffers();
		$query = 	" UPDATE #__hotelreservation_rooms SET front_display = IF(front_display, 0, 1) WHERE room_id = ".$roomId ;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true; 
	}

	
	function migrateRooms(){
		$query = 	" select * from #__hotelreservation_rooms";
		$rooms = $this->_getList( $query );
			
		foreach($rooms as $room){
			
			switch( $room->type_price ){
				case 0:
					break;
				case 2:
					$room->room_price_1 = $room->room_price_midweek;
					$room->room_price_2 = $room->room_price_midweek;
					$room->room_price_3 = $room->room_price_midweek;
					$room->room_price_4 = $room->room_price_midweek;
					$room->room_price_5 = $room->room_price_weekend;
					$room->room_price_6 = $room->room_price_weekend;
					$room->room_price_7 = $room->room_price_midweek;
					break;
				case 1:
					$room->room_price_1 = $room->room_price;
					$room->room_price_2 = $room->room_price;
					$room->room_price_3 = $room->room_price;
					$room->room_price_4 = $room->room_price;
					$room->room_price_5 = $room->room_price;
					$room->room_price_6 = $room->room_price;
					$room->room_price_7 = $room->room_price;
					break;
			}
			
			$single_price  = $room->single_discount;
			if($room -> pers_price==1)
				$single_price  = $room->single_supplement;
			$query = " insert into #__hotelreservation_rooms_rates(room_id,name,can_cancel,child_price,extra_pers_price,price_type,price_1,price_2,price_3,price_4,price_5,price_6,price_7,availability,single_balancing, min_days, max_days, base_adults,base_children) 
			values($room->room_id,'Best Available Rate',0,0,0,$room->pers_price,$room->room_price_1,$room->room_price_2,$room->room_price_3,$room->room_price_4,$room->room_price_5,$room->room_price_6,$room->room_price_7, $room->number_of_rooms,$single_price,1,0,2,0) ";
			
			$this->_db->setQuery( $query );
			if (!$this->_db->query()){
				dmp($query);
			}
			
		}

		exit;
		
				
	}
	
	function migrateOffers(){
		$query = 	" select * from #__hotelreservation_offers_rooms_price";
		$offers = $this->_getList( $query );
			
		foreach($offers as $offer){
				
			$single_price  = $offer->single_discount;
			if($offer -> offer_pers_price==1)
				$single_price  = $offer->single_supplement;
			$query = " insert into #__hotelreservation_offers_rates(offer_id,room_id,price_type,can_cancel,child_price,extra_pers_price,price_1,price_2,price_3,price_4,price_5,price_6,price_7,single_balancing, extra_night_price, base_adults,base_children)
			values($offer->offer_id,$offer->room_id,$offer->offer_pers_price,0,0,0,$offer->price_1,$offer->price_2,$offer->price_3,$offer->price_4,$offer->price_5,$offer->price_6,$offer->price_7, $single_price,$offer->offer_price_extranights,,2,0) ";

			//dmp($query);
																							
			$this->_db->setQuery( $query );
			if (!$this->_db->query()){
				dmp($query);
			}
				
		}
	
		exit;
	}
	
	public function getModel($name = 'RoomRatePrices', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	function getTranslations(){

		$hoteltranslationsModel = new JHotelReservationLanguageTranslations();
		$translations = $hoteltranslationsModel->getAllTranslations(ROOM_TRANSLATION, $this->getState('room.room_id'));
		return $translations;
	}
	
	function saveRoomTranslations($data){
	
		try{
			$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
			$dirs = JFolder::folders( $path );
			sort($dirs);
			$modelHotelTranslations = new JHotelReservationLanguageTranslations();
			$modelHotelTranslations->deleteTranslationsForObject(ROOM_TRANSLATION,$data['room_id']);
            $modelHotelTranslations->deleteTranslationsForObject(ROOM_NAME,$data['room_id']);

            foreach( $dirs  as $_lng ){
				if(isset($data['room_main_description_'.$_lng]) && strlen($data['room_main_description_'.$_lng])>0){
					$roomDescription = 		JRequest::getVar( 'room_main_description_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
					$modelHotelTranslations->saveTranslation(ROOM_TRANSLATION,$data['room_id'],$_lng,$roomDescription);
				}
                if(isset($data['room_name_'.$_lng]) && strlen($data['room_name_'.$_lng])>0){
                    $room_name_translations = 		JRequest::getVar( 'room_name_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
                    $modelHotelTranslations->saveTranslation(ROOM_NAME,$data['room_id'],$_lng,$room_name_translations);
                }
			}
		}
		catch(Exception $e){
			print_r($e);
			exit;
			JError::raiseWarning( 500,$e->getMessage());
		}
	
	}

    /**
     * @param $post
     */
    function setDefaultName($post){
        $languageTag = JRequest::getVar('_lang');
        $dirs = JHotelUtil::languageTabs();

        if(!empty($post['room_name_'.$languageTag])) {
            $post['room_name'] = $post['room_name_'.$languageTag];
            return $post['room_name'];
        }else{
            foreach($dirs as $_lng){
                if(!empty($post['room_name_'.$_lng])){
                    $post['room_name'] = $post['room_name_'.$_lng];
                    return $post['room_name'];
                }
            }
        }
    }



    function publishList($cid, $is_available=true)
    {
        if (!is_array($cid)) $cid = array($cid);
        if (count($cid))
        {
            $sql = "UPDATE #__hotelreservation_rooms SET is_available = ".($is_available?"1":"0")
                ." WHERE room_id IN (".join(',', $cid).")";
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
            $sql = "UPDATE #__hotelreservation_rooms SET is_available = ".(!$is_available?"0":"1")
                ." WHERE room_id IN (".join(',', $cid).")";
            $this->_db->setQuery($sql);
            return $this->_db->query();
        }
        return false;
    }
}
