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
class JHotelReservationModelOffer extends JModelAdmin{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_JHOTELRESERVATION_OFFER';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jhotelreservation.offer';

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
	public function getTable($type = 'Offers', $prefix = 'JTable', $config = array())
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

        if (!($hotelId = $app->getUserState('com_jhotelreservation.offers.filter.hotel_id'))) {
            $hotelId = JRequest::getInt('hotel_id', '0');
        }
        $this->setState('offer.hotel_id', $hotelId);
		// Load the User state.
		$pk = (int) JRequest::getInt('offer_id');
		if(!$pk)
			$pk = (int) JRequest::getInt('id');
		$this->setState('offer.offer_id', $pk);
		
		//dmp($hotelId);

        $app->setUserState('com_jhotelreservation.edit.offer.hotel_id',$hotelId);
        $app->setUserState('com_jhotelreservation.edit.offer.offer_id',$pk);
	
		//$this->setState('offer.hotel_id', $hotelId);
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
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('offer.offer_id');
		$false	= false;

		// Get a menu item row instance.
		$table = $this->getTable('Offers','JTable');
		
		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		if (!empty($table->offer_id)) {
			$this->setState('offer.hotel_id', $table->hotel_id);
		}
		
		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');
		
		
		$value->itemRooms = $this->getOfferRooms(); 
		$value->itemExcursions = $this->getOfferExcursions($this->getState('offer.offer_id'));
        $value->itemExtraOptions = $this->getOfferExtraOptions($this->getState('offer.offer_id'));
		$value->extras_ids = $this->getExtrasIds();

		$value->extras  =  $this->getDisplayExtras($value->extras_ids);
		
		
		
		$query = 	' 	SELECT * FROM #__hotelreservation_offers_themes ORDER BY name';
		$value->themes = $this->_getList( $query );

		$query = 	' 	SELECT * FROM #__hotelreservation_offers_themes_relation where offerId='.$this->getState('offer.offer_id');
		$value->selectedThemes = $this->_getList( $query );
		
		$query = " SELECT * FROM #__hotelreservation_offers_vouchers where offerId = ".$this->getState('offer.offer_id')." ORDER BY voucher";
		$value->vouchers = $this->_getList( $query );
		
		$value->offer_datas		= JHotelUtil::convertToFormat($value->offer_datas);
		$value->offer_datae		= JHotelUtil::convertToFormat($value->offer_datae);
		$value->offer_datasf		= JHotelUtil::convertToFormat($value->offer_datasf);
		$value->offer_dataef		= JHotelUtil::convertToFormat($value->offer_dataef);
		
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
		$form = $this->loadForm('com_jhotelreservation.offer', 'offer', array('control' => 'jform', 'load_data' => $loadData));
		
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
		$data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.offer.data', array());

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
        $id	= (!empty($data['offer_id'])) ? $data['offer_id'] : (int)$this->getState('offer.offer_id');
        $isNew	= true;
        $data['offer_datas']=JHotelUtil::convertToMysqlFormat($data['offer_datas']);
        $data['offer_datae']=JHotelUtil::convertToMysqlFormat($data['offer_datae']);
        $data['offer_datasf']=JHotelUtil::convertToMysqlFormat($data['offer_datasf']);
        $data['offer_dataef']=JHotelUtil::convertToMysqlFormat($data['offer_dataef']);
        $data['offer_name'] = $this->setDefaultName($data);

        // Get a row instance.
        $table = $this->getTable();
        $data['ordering'] =$table->getOfferOrder();

        $data['option_ids'] = '';

        // Load the row if saving an existing item.
        if ($id > 0) {
            $table->load($id);
            $data['ordering'] = $table->ordering;
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

        $this->setState('offer.offer_id', $table->offer_id);



        $this->storeVouchers($data);
        $this->storePictures($data);
        $this->storeRooms($data);
        $this->storeRate($data);
        $this->storeThemes($data);
	    $this->saveDisplayedExtras($table->hotel_id,$data);

        //store extra options
        //if(isset($data["extra_options_ids"])){
        $this->storeExtraOptions($this->getState('offer.offer_id'), $data["extra_options_ids"]);
        //}
        //if(isset($data["excursion_ids"])) {
        $this->storeOfferExcursions($this->getState('offer.offer_id'), $data["excursion_ids"]);

        // Clean the cache
        $this->cleanCache();

        return true;
    }

    function saveDisplayedExtras($hotelId,$data) {

	    $selectedExtras = explode(",",$data['extras_ids']);

	    $oldSelectedExtras = $this->getOldSelectedExtras($hotelId,$this->getState('offer.offer_id'));

	    $extras = new stdClass();

	    $extrasForDeletion = array_diff($oldSelectedExtras->extras,$selectedExtras);


	    $this->deleteExtras($extrasForDeletion,$this->getState( 'offer.offer_id' ),$hotelId,$oldSelectedExtras->OffersInExtras);



	    foreach( $selectedExtras as $key=>$selectedExtra )
	    {

		    $row =JTable::getInstance('ExtraOption',"JTable");

		    if(isset($selectedExtra) && !empty($selectedExtra)) {

			    $extraOptionOfferIds = $row->getExtraOfferIds( $hotelId, $selectedExtra );
			    // check if current offer_Id is not in the string value of extras offer_ids
			    if ( false === strpos( $extraOptionOfferIds, (string) $this->getState( 'offer.offer_id' ) ) ) {
				    $row = $this->getTable( 'ExtraOption', 'JTable' );

				    $extraOptionOfferIds = isset($extraOptionOfferIds)?$extraOptionOfferIds:'';
				    $extraOptionOfferIds .= ','.(string) $this->getState( 'offer.offer_id' );
				    $extras->offer_ids = $extraOptionOfferIds;
				    $extras->id = $selectedExtra;
				    $extras->hotel_id = $hotelId;

				    if ( ! $row->bind( $extras ) ) {
					    throw( new Exception( $this->_db->getErrorMsg() ) );
					    $this->setError( $this->_db->getErrorMsg() );
				    }
				    // Make sure the record is valid
				    if ( ! $row->check() ) {
					    throw( new Exception( $this->_db->getErrorMsg() ) );
					    $this->setError( $this->_db->getErrorMsg() );
				    }

				    // Store the web link table to the database
				    if ( ! $row->store() ) {
					    throw( new Exception( $this->_db->getErrorMsg() ) );
					    $this->setError( $this->_db->getErrorMsg() );
				    }

			    }
		    }
	    }
    }

    function deleteExtras($extrasForDeletion,$offerId,$hotelId,$offersInExtras) {
	    $extra = new stdClass();

    	foreach ($extrasForDeletion as $key =>$item){


    		$items = explode(',',$offersInExtras[$item]);

		    foreach($items as $k =>$value){
		    	if($offerId == $value){
				    $items[$k] = '';
			    }
		    }



		    $result = implode(',',$items);
		    $row = $this->getTable( 'ExtraOption', 'JTable' );

		    $extra->offer_ids = $result;
		    $extra->id = $item;
		    $extra->hotel_id = $hotelId;

		    if ( ! $row->bind( $extra ) ) {
			    throw( new Exception( $this->_db->getErrorMsg() ) );
			    $this->setError( $this->_db->getErrorMsg() );
		    }
		    // Make sure the record is valid
		    if ( ! $row->check() ) {
			    throw( new Exception( $this->_db->getErrorMsg() ) );
			    $this->setError( $this->_db->getErrorMsg() );
		    }

		    // Store the web link table to the database
		    if ( ! $row->store() ) {
			    throw( new Exception( $this->_db->getErrorMsg() ) );
			    $this->setError( $this->_db->getErrorMsg() );
		    }
	    }
    }

	
	function storeRate($data){

		//exit;
		//room discounts
		$discount_ids 	= array();
		
		foreach( $data['rooms'] as $valueRoom )
		{
		//dmp( $valueRoom);
			if( count( $valueRoom['offer_price'] ) > 0 )
			{
		
				$offer_price = $valueRoom['offer_price'];
				$offer_price['offer_id'] = $this->getState('offer.offer_id');
				$offer_price['room_id'] = $valueRoom['room_id'];
				$offer_price['id'] =$offer_price['offer_room_rate_id'];
		
				dmp($offer_price);
				//exit;
				$row = $this->getTable('OfferRate','Table');
		
				//dmp($offer_price);
				//exit;
				if (!$row->bind($offer_price))
				{
					dmp($this->_db->getErrorMsg());
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
				// Make sure the record is valid
				if (!$row->check())
				{
					dmp($this->_db->getErrorMsg());
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
		
				// Store the web link table to the database
				if (!$row->store())
				{
					dmp($this->_db->getErrorMsg());
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
		
				$discount_ids[] = $this->_db->insertid();
			}
				
		}
		
	}
	
	function storeThemes($data){
		//prepare themes
		//dmp($data['themes']);
		$query = " DELETE FROM #__hotelreservation_offers_themes_relation
												WHERE offerId = ".$this->getState('offer.offer_id');
		
		// dmp($query);
		// exit;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		//print_r($data['themes']);
        //$themes[] = explode(',',$data['themes']);
        $themeRelation = new stdClass();
        if(isset($data['themes'])) {
            foreach ($data['themes'] as $theme) {

                $row = $this->getTable('OffersThemesRelation', 'Table');
                $themeRelation->offerId = $this->getState('offer.offer_id');
                $themeRelation->themeId = $theme;
                //dmp($facilityRelation);
                if (!$row->bind($themeRelation)) {
                    throw(new Exception($this->_db->getErrorMsg()));
                    $this->setError($this->_db->getErrorMsg());

                }
                // Make sure the record is valid
                if (!$row->check()) {
                    throw(new Exception($this->_db->getErrorMsg()));
                    $this->setError($this->_db->getErrorMsg());
                }

                // Store the web link table to the database
                if (!$row->store(true)) {
                    throw(new Exception($this->_db->getErrorMsg()));
                    $this->setError($this->_db->getErrorMsg());
                }
            }
        }
	}
	
	function storeRooms($data){
		//room
		$offer_room_ids 	= array();
		foreach( $data['rooms'] as $value )
		{
			$row = $this->getTable('OfferRooms','JTable');
		
			// dmp($key);
			$offer_room										= new stdClass();
			$offer_room->offer_id 							= $this->getState('offer.offer_id');
			$offer_room->room_id 							= $value['room_id'];
		
			if (!$row->bind($offer_room))
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
		
			$offer_room_ids[] = $this->_db->insertid();
		}


		$query = " DELETE FROM #__hotelreservation_offers_rooms
						WHERE offer_id = '".$this->getState('offer.offer_id')."'
						".( count($offer_room_ids)> 0 ? " AND offer_room_id NOT IN (".implode(',', $offer_room_ids).")" : "");
		
		// dmp($query);
		// exit;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
	}
	function storeVouchers($data){
		//update vouchers

        $offer_id = $this->getState('offer.offer_id');

		if($data["processVouchers"]==1){
			$this->_db->setQuery (	" DELETE FROM #__hotelreservation_offers_vouchers
					WHERE offerId = $offer_id");
			if (!$this->_db->query() )
			{
				// dmp($db);
				$ret = false;
				$e = 'INSERT / UPDATE sql STATEMENT error !';
			}
			//dmp($data['vouchers']);
			if(isset($data['vouchers']) && count($data['vouchers'])>0){
				foreach($data['vouchers'] as $key => $value )
				{
					$recordName			= trim($data['vouchers'][ $key ]);
                    $recordName         = $this->_db->escape($recordName);

                    $this->_db->setQuery( "
							INSERT INTO #__hotelreservation_offers_vouchers
							(
							offerId,
							voucher
					)
							VALUES
							(
							'$offer_id',
							'$recordName'
								
					)
							" );
							dmp($recordName);
							if (!$this->_db->query() )
							{
								// dmp($db);
									$ret = false;
									$e = 'INSERT / UPDATE sql STATEMENT error !';
								}
					
								}
								}
							}
							//end update vouchers
							//exit;
	}
	
	function storePictures($data){
		//prepare photos
		
		$path_old = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.PATH_OFFER_PICTURES.($data['offer_id']+0)."/");
		$files = glob( $path_old."*.*" );
		$path_new = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.PATH_OFFER_PICTURES.($data['offer_id']+0)."/");
		
		
		$picture_ids 	= array();
		foreach( $data['pictures'] as $value )
		{
			$row = $this->getTable('OfferPictures','JTable');
		
			// dmp($key);
			$pic 						= new stdClass();
			$pic->offer_picture_id		= 0;
			$pic->offer_id 				= $this->getState('offer.offer_id');
			$pic->offer_picture_info	= $value['offer_picture_info'];
			$pic->offer_picture_path	= $value['offer_picture_path'];
			$pic->offer_picture_enable	= $value['offer_picture_enable'];
			//dmp($pic);
			$file_tmp = JHotelUtil::makePathFile( $path_old.basename($pic->offer_picture_path) );
				
			if( !is_file($file_tmp) )
				continue;
		
			if( !is_dir($path_new) )
			{
				if( !@mkdir($path_new) )
				{
					throw( new Exception($this->_db->getErrorMsg()) );
				}
			}
				
			if( $path_old.basename($pic->offer_picture_path) != $path_new.basename($pic->offer_picture_path) )
			{
				if(@rename($path_old.basename($pic->offer_picture_path),$path_new.basename($pic->offer_picture_path)) )
				{
		
					$pic->offer_picture_path	 = PATH_OFFER_PICTURES.($data['room_id']+0).'/'.basename($pic->offer_picture_path);
					//@unlink($path_old.basename($pic->offer_picture_path));
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
		
			$picture_ids[] = $this->_db->insertid();
		
		
		}
		
		$files = glob( $path_new."*.*" );
		
		foreach( $files as $pic )
		{
			$is_find = false;
			foreach( $data['pictures'] as $value )
			{
				if( $pic == JHotelUtil::makePathFile(JPATH_ROOT.$value['offer_picture_path']) )
				{
					$is_find = true;
					break;
				}
			}
			//if( $is_find == false )
			//	@unlink( JHotelUtil::makePathFile(JPATH_COMPONENT.$value['offer_picture_path']) );
		}
		
		$query = " DELETE FROM #__hotelreservation_offers_pictures
						WHERE offer_id = '".$data['offer_id']."'
						".( count($picture_ids)> 0 ? " AND offer_picture_id NOT IN (".implode(',', $picture_ids).")" : "");
		
		// dmp($query);
		// exit;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		//~prepare photos
	}
	
	
	function storeExtraOptions($offerId,$extraOptionsArray){

        $row =JTable::getInstance('OffersExtraOptions',"JTable");
        $row->deleteOfferExtraOptions($offerId);
        //$excursionsArray = explode(',',$excursionsArray);
        if(isset($extraOptionsArray)) {
            foreach ($extraOptionsArray as $extra_option_id) {
                $row = JTable::getInstance('OffersExtraOptions',"JTable");
                $row->offer_id = $offerId;
                $row->extra_option_id = $extra_option_id;

                // Make sure the record is valid
                if (!$row->check()) {
                    throw(new Exception($this->_db->getErrorMsg()));
                    $this->setError($this->_db->getErrorMsg());
                }

                // Store the web link table to the database
                if (!$row->store(true)) {
                    throw(new Exception($this->_db->getErrorMsg()));
                    $this->setError($this->_db->getErrorMsg());
                }
            }
        }
	}
	
	function storeOfferExcursions($offerId,$excursionsArray){
		
		$row =JTable::getInstance('OffersExcursions',"Table");
		$row->deleteOfferExcursions($offerId);
        //$excursionsArray = explode(',',$excursionsArray);
        if(isset($excursionsArray)) {
            foreach ($excursionsArray as $excursion_id) {
                $row = JTable::getInstance('OffersExcursions', "Table");

                $row->offer_id = $offerId;
                $row->excursion_id = $excursion_id;

                // Make sure the record is valid
                if (!$row->check()) {
                    throw(new Exception($this->_db->getErrorMsg()));
                    $this->setError($this->_db->getErrorMsg());
                }

                // Store the web link table to the database
                if (!$row->store(true)) {
                    throw(new Exception($this->_db->getErrorMsg()));
                    $this->setError($this->_db->getErrorMsg());
                }
            }
        }
	}
	
	
	
	function getOfferPictures(){

		$pictures  	= array();
		$table = $this->getTable('Offers','JTable');
		$files = $table->getOffersPictures($this->getState('offer.offer_id'));
		
		if( isset( $files) )
		{			
			foreach( $files as $value )
			{
				$pictures[]	= array( 
													'offer_picture_info' 		=> $value->offer_picture_info,
													'offer_picture_path' 		=> $value->offer_picture_path,
													'offer_picture_enable'		=> $value->offer_picture_enable,
												);
			}
		}
		return $pictures;
	}
	
	
	
	public function getModel($name = 'RoomRatePrices', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	function getTranslations(){

		$hoteltranslationsModel = new JHotelReservationLanguageTranslations();
		$translations = $hoteltranslationsModel->getAllTranslations(OFFER_TRANSLATION, $this->getState('offer.offer_id'));
		return $translations;
	}

    /**
     * @param $data
     */
    function saveOfferTranslations($data,$task){
        try{
            $path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
            $dirs = JFolder::folders( $path );
            sort($dirs);
            $modelHotelTranslations = new JHotelReservationLanguageTranslations();
            $modelHotelTranslations->deleteTranslationsForObject(OFFER_TRANSLATION,$data['offer_id']);
            $modelHotelTranslations->deleteTranslationsForObject(OFFER_NAME,$data['offer_id']);
            $modelHotelTranslations->deleteTranslationsForObject(OFFER_CONTENT_TRANSLATION,$data['offer_id']);
            $modelHotelTranslations->deleteTranslationsForObject(OFFER_SHORT_TRANSLATION,$data['offer_id']);
            $modelHotelTranslations->deleteTranslationsForObject(OFFER_INFO_TRANSLATION,$data['offer_id']);

            foreach( $dirs  as $_lng ){
                if(isset($data['offer_description_'.$_lng]) && strlen($data['offer_description_'.$_lng])>0){
                    $offerDescription = 		JRequest::getVar( 'offer_description_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
                    $modelHotelTranslations->saveTranslation(OFFER_TRANSLATION,$data['offer_id'],$_lng,$offerDescription);
                }
                //Offer name saving
                if(isset($data['offer_name_'.$_lng]) && strlen($data['offer_name_'.$_lng])>0){
                    $offerName  = JRequest::getVar( 'offer_name_'.$_lng, '', 'post', 'string');
                    if ($task == 'save2new') {
                    	$offerName = $offerName.'-Copy';
                    }
                    $modelHotelTranslations->saveTranslation(OFFER_NAME,$data['offer_id'],$_lng,$offerName);
                }
              
                if(isset($data['offer_content_'.$_lng]) && strlen($data['offer_content_'.$_lng])>0){
                    $offerDescription = 		JRequest::getVar( 'offer_content_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
                    $modelHotelTranslations->saveTranslation(OFFER_CONTENT_TRANSLATION,$data['offer_id'],$_lng,$offerDescription);
                }

                if(isset($data['offer_short_description_'.$_lng]) && strlen($data['offer_short_description_'.$_lng])>0){
                    $offerDescription = 		JRequest::getVar( 'offer_short_description_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
                    $modelHotelTranslations->saveTranslation(OFFER_SHORT_TRANSLATION,$data['offer_id'],$_lng,$offerDescription);
                }

                if(isset($data['offer_other_info_'.$_lng]) && strlen($data['offer_other_info_'.$_lng])>0){
                    $offerDescription = JRequest::getVar( 'offer_other_info_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
                    $modelHotelTranslations->saveTranslation(OFFER_INFO_TRANSLATION,$data['offer_id'],$_lng,$offerDescription);
                }
            }
        }
        catch(Exception $e){
            //print_r($e);
            //exit;
            JError::raiseWarning( 500,$e->getMessage());
        }
    }
	
	
	function displayThemes($themes, $selectedThemes){
		ob_start();
		?>
	
				<select id="themes" multiple="multiple" name="themes[]">
                    <!--<option value="">
				
					<?php echo JText::_('LNG_SELECT_THEME',true)?></option>-->
					
					<?php
					if( isset($themes) && is_array($themes))
					foreach( $themes as $theme )
					{
						$selected = false;
						foreach( $selectedThemes as $selectedTheme ){
							if($theme->id == $selectedTheme->themeId && $selectedTheme->offerId>0)
							$selected =true;
						}
						?>
						<option <?php echo $selected? 'selected="selected"' : ''?> 	value='<?php echo $theme->id?>'><?php echo $theme->name ?></option>
						<?php
						}
						?>
				</select>
	
				<?php
				$buff = ob_get_contents();
				ob_end_clean();
				return $buff;
		}
		
		function getOfferRooms(){

			$offerId = $this->getState('offer.offer_id');
			$hotelId = $this->getState('offer.hotel_id');

			$query = " 	SELECT
							r.room_id,
							r.room_name,
							r.max_adults,
							r.max_children,
							".
									(
											$offerId > 0 ?
											"IF( ISNULL(ho.room_id), 0, 1 )"
											:
											"0"
									)
									."						AS is_sel
						FROM #__hotelreservation_rooms r
						".($offerId > 0 ?
										"LEFT JOIN (
										select * from  #__hotelreservation_offers_rooms o_r  where  o_r.offer_id = $offerId
								) ho  on r.room_id = ho.room_id
										":"")."
						WHERE
							r.hotel_id = ".$hotelId."
							";
			
										
									//dmp($query);
			$itemRooms = $this->_getList( $query );
			if( isset( $itemRooms) )
			{
					
				foreach($itemRooms as $k => $r )
				{
			
					//dmp($r);
					$query = "SELECT *	FROM  #__hotelreservation_offers_rates d where d.offer_id=$offerId and d.room_id=$r->room_id";
					//dmp($query);
					$res = $this->_getList( $query );
					if(isset( $res) && count($res)>0 )
					{
						foreach( $res as $d )
						{
							//$d->week_types 	= explode(',', $d->week_types);
							// dmp($d);
							$itemRooms[$k]->discounts  = $d;
						}
					}else{
						$discounts = new stdClass();
						$discounts->id =0;
						$discounts->offer_room_price_id = null;
						$discounts->offer_id = $offerId;
						$discounts->room_id = $r->room_id;
						$discounts->price_1 = null;
						$discounts->price_2 = null;
						$discounts->price_3 = null;
						$discounts->price_4 = null;
						$discounts->price_5 = null;
						$discounts->price_6 = null;
						$discounts->price_7 = null;
						$discounts->single_balancing = null;
						$discounts->child_price = null;
						$discounts->price_type = 1;
						$discounts->extra_night_price = null;
						$discounts->extra_pers_price = null;
						$discounts->base_adults = null;
						$discounts->base_children = null;
						$itemRooms[$k]->discounts = $discounts;
					}
				}
			}
		return 	$itemRooms;
	}
	
	function getExtraOptions(){
        //$hotelId = JRequest::getInt('hotel_id');
		$hotelId = $this->getState('offer.hotel_id');
		$query = "select * from  #__hotelreservation_extra_options WHERE  hotel_id = ". $hotelId;
		$this->_db->setQuery( $query );
		$extraOptions = $this->_db->loadObjectList();
		return $extraOptions;
	}
	
	public function setHotelMinOfferPrice(){
		$hotelId = $this->_hotel_id;
	
		$query="select rr.base_adults, rr.price_type, least(rr.price_1, rr.price_2, rr.price_3, rr.price_4, rr.price_5, rr.price_6, rr.price_7) as min_rate,min(rrp.price) as min_rate_custom
				from #__hotelreservation_offers r
				inner join #__hotelreservation_offers_rates rr on r.offer_id = rr.offer_id
				left join #__hotelreservation_offers_rate_prices rrp on rrp.rate_id = rr.id
				where r.is_available = 1 and r.hotel_id= $hotelId
				group by r.hotel_id";
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObject();
	
	
		$price = $result->min_rate;
		if(isset($result->min_rate_custom) && $price>$result->min_rate_custom){
		$price = $result->min_rate_custom;
		}
	
		if($result->price_type == 0){
		$price = $price / $result->base_adults;
		}
	
		$query="update #__hotelreservation_hotels set min_offer_price = $price where hotel_id = $hotelId ";
	
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
		dmp($query);
		dmp("error");
		}
		exit;
	}
	
	function getLastOrderNumber($hotelId){
		$query = "select max(ordering) as ordering from  #__hotelreservation_offers WHERE  hotel_id = ".$hotelId;
		$this->_db->setQuery( $query );
		$offer = $this->_db->loadObject();
		return $offer->ordering;
	}
	
	function getLastOrder($offerId)
	{
		$offer_id = 0;
		if( isset($offerId) )
			$offer_id = $offerId;
		$increment = 0;
		if( $offer_id > 0 ){
			$query = 	" SELECT * FROM #__hotelreservation_offers  WHERE offer_id = ".$offer_id;
		} else {
			$query = 	" SELECT * FROM #__hotelreservation_offers  ORDER BY ordering DESC LIMIT 1 ";
			$increment++;
		}
	
		$db 	= JFactory::getDBO();
		$this->_db->setQuery( $query );
		$row = $this->_db->loadObject();
	
		if(!isset($row ))
			return 1;
	
		return ($row->ordering+$increment);
	}
	
	function getExcursions(){

		$hotelId = $this->getState('offer.hotel_id');
		$query = " 	
	 				SELECT r.id, r.name as excursion_name
					FROM #__hotelreservation_excursions r
					WHERE r.is_available = 1 ";
		$excursions = $this->_getList( $query );
		return $excursions;
	}

    function getAirportTransferTypes(){
        $hotel_id = $this->getState('offer.hotel_id');
        $row =JTable::getInstance('AirportTransferTypes',"JTable");
        $airport_transfer_types = $row->getAirportTransferTypes($hotel_id);
        if(count($airport_transfer_types) > 0) {
            return $airport_transfer_types;
        }
    }
	
	function getOfferExcursions($offerId){
		$row =JTable::getInstance('OffersExcursions',"Table");
		$excursions = $row->getOfferExcursions($offerId);
		if(count($excursions))
			return $excursions->excursionIds;
		else 
			return null;
	}

    function getOfferExtraOptions($offerId){
        $row =JTable::getInstance('OffersExtraOptions',"JTable");
        $extraOptions = $row->getOfferExtraOptions($offerId);
        if(count($extraOptions)> 0)
            return $extraOptions->extraOptionIds;
        else
            return null;
    }

	function getDisplayExtras($selectedExtras){

		$query = "select  o.id, o.name
					from #__hotelreservation_extra_options o
					where o.status = 1 AND o.hotel_id  = ".$this->getState('offer.hotel_id').";
				";

		$extras = $this->_getList( $query );
		if(isset($selectedExtras)){
			$selectedExtras = explode(",",$selectedExtras);
			foreach($extras as &$extra){
				$extra->is_sel = 0;
				if(in_array($extra->id, $selectedExtras)){
					$extra->is_sel = 1;
				}
			}
		}

		return $extras;
	}

	function getOldSelectedExtras($hotelId,$offerId) {


		$row =JTable::getInstance('ExtraOption',"JTable");
		$extraOptions = $row->getOldDisplayExtras($hotelId,$offerId);

		$result = new stdClass();
		$result->OffersInExtras = array();
		$result->extras = array();
		foreach ($extraOptions as $extraOption) {
			$result->OffersInExtras[$extraOption->id] = $extraOption->offer_ids;
			$result->extras[] = $extraOption->id;
		}

		return $result;
	}

	function getExtrasIds() {

		$row =JTable::getInstance('ExtraOption',"JTable");
		$extraOptions = $row->getDisplayExtras($this->getState('offer.hotel_id'),$this->getState('offer.offer_id'));

		return $extraOptions;
	}

    /**
     * @param $post
     * @return mixed
     */
    function setDefaultName($post){
        $languageTag = JRequest::getVar('_lang');
        $dirs = JHotelUtil::languageTabs();
        if(!empty($post['offer_name_'.$languageTag])) {
            $post['offer_name'] = $post['offer_name_'.$languageTag];
            return $post['offer_name'];
        }else{
            foreach($dirs as $_lng) {
                if (!empty($post['offer_name_'.$_lng]) ) {
                    $post['offer_name'] = $post['offer_name_'.$_lng];
                    return $post['offer_name'];
                }
            }
        }
    }

    function publishList($cid, $is_available=true)
    {
        if (!is_array($cid)) $cid = array($cid);
        if (count($cid))
        {
            $sql = "UPDATE #__hotelreservation_offers SET is_available = ".($is_available?"1":"0")
                ." WHERE offer_id IN (".join(',', $cid).")";
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
            $sql = "UPDATE #__hotelreservation_offers SET is_available = ".(!$is_available?"0":"1")
                ." WHERE offer_id IN (".join(',', $cid).")";
            $this->_db->setQuery($sql);
            return $this->_db->query();
        }
        return false;
    }
    
    //get offer rate plans for Cubilis
	public function getRoomOfferRatePlans($roomId){
		$table = $this->getTable('OfferRate','Table');
		$result = $table->getRoomOfferRatePlans($roomId);
		return $result;	
	}

}
