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


//jimport('joomla.application.component.model');
jimport('joomla.application.component.modeladmin');


class JHotelReservationModelHotel extends JModelAdmin
{
	function __construct()
	{
		parent::__construct();
		$array = JRequest::getVar('cid',  0, '', 'array');
		//var_dump($array);
		if(isset($array[0])) $this->setId((int)$array[0]);
	}

	function setId($hotel_id)
	{
		// Set id and wipe data
		$this->_hotel_id	= $hotel_id;
		$this->_data		= null;
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
        $form = $this->loadForm('com_jhotelreservation.hotel','hotel',array('control'=> 'jform','load_data' => $loadData));
        if(empty($form))
        {
            return false;
        }
        return $form;
    }

	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */

	function getData()
	{
		// Load the data
		if (empty( $this->_data ))
		{
			$query = 	' SELECT * FROM #__hotelreservation_hotels'.
						' WHERE hotel_id = '.$this->_hotel_id;
				
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}

		if (!$this->_data)
		{
			$row = $this->getTable('hotels');
			$properties = $row->getProperties(1);
			$this->_data = JArrayHelper::toObject($properties, 'JObject');
			
			//~check temporary files
			$this->_data->pictures			= array();
			$this->_data->facilities		= array();
			$this->_data->selectedFacilities		= array();
			$this->_data->types				= array();
			$this->_data->selectedTypes				= array();
			$this->_data->accommodationTypes	= array();
			$this->_data->selectedAccommodationTypes	= array();
			$this->_data->environments		= array();
			$this->_data->selectedEnvironments		= array();
			$this->_data->regions			= array();
			$this->_data->selectedRegions			= array();
			$this->_data->selectedPaymentOptions = null;
		}else{

			//get pictures
			$query = "SELECT *
					FROM #__hotelreservation_hotel_pictures
					WHERE hotel_id =".$this->_data->hotel_id ."
					ORDER BY hotel_picture_id ";

			$files = $this->_getList( $query );
			$this->_data->pictures			= array();
			foreach( $files as $value )
			{
				$this->_data->pictures[]	= array(
													'hotel_picture_info' 		=> $value->hotel_picture_info,
													'hotel_picture_path' 		=> $value->hotel_picture_path,
													'hotel_picture_enable'		=> $value->hotel_picture_enable,
				);
			}
		}
		
		//convert date format
		$this->_data->start_date = JHotelUtil::convertToFormat($this->_data->start_date);
		$this->_data->end_date = JHotelUtil::convertToFormat($this->_data->end_date);
		
		$this->_data->ignored_dates = JHotelUtil::formatToDefaultDate($this->_data->ignored_dates);
		$query =  'SELECT * FROM #__hotelreservation_countries  ORDER BY country_name';
		$this->_data->countries = $this->_getList( $query );

		$query =  'SELECT * FROM #__hotelreservation_hotel_facilities ORDER BY name';
		$this->_data->facilities = $this->_getList( $query );
		$query =  'SELECT * FROM #__hotelreservation_hotel_facility_relation where hotelId = '.$this->_data->hotel_id;
		if(isset($this->_data->hotel_id))
			$this->_data->selectedFacilities = $this->_getList( $query );

		$query =  'SELECT * FROM #__hotelreservation_hotel_types ORDER BY name';
		$this->_data->types = $this->_getList( $query );
		$query =  'SELECT * FROM #__hotelreservation_hotel_type_relation where hotelId ='.$this->_data->hotel_id;
		if(isset($this->_data->hotel_id))
			$this->_data->selectedTypes = $this->_getList( $query );

		$query =  'SELECT * FROM #__hotelreservation_hotel_accommodation_types ORDER BY name';
		$this->_data->accommodationTypes = $this->_getList( $query );
		$query =  'SELECT * FROM #__hotelreservation_hotel_accommodation_type_relation where hotelId ='.$this->_data->hotel_id;
		if(isset($this->_data->hotel_id))
			$this->_data->selectedAccommodationTypes = $this->_getList( $query );

		$query =  'SELECT * FROM #__hotelreservation_hotel_environments ORDER BY name';
		$this->_data->environments = $this->_getList( $query );
		$query =  'SELECT * FROM #__hotelreservation_hotel_environment_relation where hotelId='.$this->_data->hotel_id;
		if(isset($this->_data->hotel_id))
			$this->_data->selectedEnvironments = $this->_getList( $query );

		$query =  'SELECT * FROM #__hotelreservation_hotel_regions ORDER BY name';
		$this->_data->regions = $this->_getList( $query );
		$query =  'SELECT * FROM #__hotelreservation_hotel_region_relation where hotelId='.$this->_data->hotel_id;
		if(isset($this->_data->hotel_id))
			$this->_data->selectedRegions = $this->_getList( $query );

		$query =  'SELECT * FROM #__hotelreservation_hotel_payment_options ORDER BY name';
		$this->_data->paymentOptions = $this->_getList( $query );
		$query =  'SELECT * FROM #__hotelreservation_hotel_payment_option_relation where hotelId='.$this->_data->hotel_id;
		if(isset($this->_data->hotel_id))
			$this->_data->selectedPaymentOptions = $this->_getList( $query );

		$query = 'SELECT * FROM #__hotelreservation_hotel_channel_manager where hotel_id = '.$this->_data->hotel_id;
		if(isset($this->_data->hotel_id))
			$channelManagers = $this->_getList( $query );
		$channelManagersArray = array();
		if(isset($channelManagers) && count($channelManagers)>0){
			foreach($channelManagers as $channelManager){
				$channelManagersArray[$channelManager->service]=$channelManager;
			}
		}
		
		$this->_data->channelManagers = $channelManagersArray;
		$query = ' SELECT currency_id, description FROM #__hotelreservation_currencies';
		$this->_data->currencies = $this->_getList( $query );
			
		$informationsTable = $this->getTable('HotelInformations','JTable');
		$properties = $informationsTable->getProperties(1);
		$this->_data->informations= JArrayHelper::toObject($properties, 'JObject');
		
		if(isset($this->_data->hotel_id))
			$this->_data->informations =  $informationsTable->getHotelInformations($this->_data->hotel_id);
				
		$contactTable = $this->getTable('HotelContact');
		$properties = $contactTable->getProperties(1);
		$this->_data->contact= JArrayHelper::toObject($properties, 'JObject');
		if(isset($this->_data->hotel_id))
			$this->_data->contact =  $contactTable->getHotelContacts($this->_data->hotel_id);
		
		return $this->_data;
	}

	//enforce single hotel restrictions 
	function checkHotelRestrictions($hotelId){
		$row = $this->getTable('hotels');
		$nrHotels = count($row->getAllHotels());
		$row->load($hotelId);
	
		if (($nrHotels>1 && ENABLE_SINGLE_HOTEL==1) || ($nrHotels>=1 && $row->hotel_id==0 && ENABLE_SINGLE_HOTEL==1)){
			$app = JFactory::getApplication();
			$app->enqueueMessage("Hotel does not exist", 'warning');
			$app->redirect('index.php?option='.getBookingExtName().'&task=hotels.viewHotels',"");
		}
	}
	
	function store($data)
	{
		try
		{
			$this->_db->BeginTrans();
			$row = $this->getTable('hotels');
			$this->checkHotelRestrictions($data['hotel_id']);
				
			$data['start_date']= JHotelUtil::convertToMysqlFormat($data['start_date']);
			$data['end_date']= JHotelUtil::convertToMysqlFormat($data['end_date']);

			$data['hotel_alias'] = !empty($data['hotel_alias'])?$data['hotel_alias']:'';
			$data['hotel_alias'] = JHotelUtil::getAlias( $data['hotel_name'], $data['hotel_alias'] );
			$hotelId = $data['hotel_id'];
			if($data['hotel_id']=='' || $data['hotel_id']==0 || $data['hotel_id'] ==null)
			{
				$data['hotel_id'] = $this->_db->insertid();
				$hotelId = $data['hotel_id'];
			}
			$data['hotel_alias'] = $this->checkAlias( $hotelId, $data['hotel_alias'] );

			// Bind the form fields to the table
			if (!$row->bind($data))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
				// return false;
			}
			// Make sure the record is valid
			if (!$row->check()) {
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
				// return false;
			}

			// Store the web link table to the database
			if (!$row->store()) {
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError( $this->_db->getErrorMsg() );
				// return false;
			}
			
			if($data['hotel_id']=='' || $data['hotel_id']==0 || $data['hotel_id'] ==null){
				$data['hotel_id'] = $this->_db->insertid();
				$this->_hotel_id =  $data['hotel_id'];
				$this->createHotelMapping($this->_hotel_id);
				self::copyEmailContent($this->_hotel_id);
			}
			//prepare photos
			
			$managePictures = JRequest::getVar("manage_pictures",null);
			if(!empty($managePictures)){
						
				$path_old = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_HOTEL_PICTURES.($data['hotel_id']+0)."/");
				$files = glob( $path_old."*.*" );
					
				$path_new = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_HOTEL_PICTURES.($data['hotel_id']+0)."/");
					
				$picture_ids 	= array();
				foreach( $data['pictures'] as $value )
				{
					$row = $this->getTable('HotelPictures','JTable');
	
					//dmp($key);
					$pic 						= new stdClass();
					$pic->hotel_picture_id		= 0;
					$pic->hotel_id 				= $data['hotel_id'];
					$pic->hotel_picture_info	= $value['hotel_picture_info'];
					$pic->hotel_picture_path	= $value['hotel_picture_path'];
					$pic->hotel_picture_enable	= $value['hotel_picture_enable'];
					$file_tmp = JHotelUtil::makePathFile( $path_old.basename($pic->hotel_picture_path) );
	
					if( !is_file($file_tmp) )
						continue;


					if( !is_dir($path_new) )
					{
                        $error = error_get_last();
                        echo $error['message'];

						if( !@mkdir($path_new) )
						{
							throw( new Exception($this->_db->getErrorMsg()) );
						}
					}

	
				    //exit;
					if( $path_old.basename($pic->hotel_picture_path) != $path_new.basename($pic->hotel_picture_path) )
					{
						if(@rename($path_old.basename($pic->hotel_picture_path),$path_new.basename($pic->hotel_picture_path)) )
						{
	
							$pic->hotel_picture_path	 = PATH_HOTEL_PICTURES.($data['hotel_id']+0).'/'.basename($pic->hotel_picture_path);
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
						
						$path = JHotelUtil::makePathFile(JPATH_ROOT.DS.$value['hotel_picture_path']);
						if( $pic == JHotelUtil::makePathFile(JPATH_ROOT.DS.$value['hotel_picture_path']) )
						{
							$is_find = true;
							break;
						}
					}

				}
					
				$query = " DELETE FROM #__hotelreservation_hotel_pictures
							WHERE hotel_id = '".$data['hotel_id']."'
							".( count($picture_ids)> 0 ? " AND hotel_picture_id NOT IN (".implode(',', $picture_ids).")" : "");

				$this->_db->setQuery( $query );
				if (!$this->_db->query())
				{
					throw( new Exception($this->_db->getErrorMsg()) );
				}
			}
			//~prepare photos
					
		
			$query = " DELETE FROM #__hotelreservation_hotel_facility_relation WHERE hotelId = ".$data['hotel_id'];

			$this->_db->setQuery( $query );
			if (!$this->_db->query())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
			if(isset( $data['facilities'])){
				//prepare facilities

                $facilityRelation = new stdClass();
					
				foreach( $data['facilities'] as $facility ){
						
					$row = $this->getTable('HotelFacilityRelation','JTable');
					$facilityRelation->hotelId= $data['hotel_id'];
					$facilityRelation->facilityId= $facility;
					if (!$row->bind($facilityRelation))
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
					if (!$row->store(true))
					{
						throw( new Exception($this->_db->getErrorMsg()) );
						$this->setError($this->_db->getErrorMsg());
					}
				}
			}
			
			
			$query = " DELETE FROM #__hotelreservation_hotel_type_relation
			WHERE hotelId = ".$data['hotel_id'];
			
			$this->_db->setQuery( $query );
			if (!$this->_db->query())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
			if(isset( $data['types'])){
				//prepare types

                $typeRelation = new stdClass();
				foreach( $data['types'] as $type ){

					$row = $this->getTable('HotelTypeRelation','JTable');
					$typeRelation->hotelId= $data['hotel_id'];
					$typeRelation->typeId= $type;

					if (!$row->bind($typeRelation))
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
					if (!$row->store(true))
					{
						throw( new Exception($this->_db->getErrorMsg()) );
						$this->setError($this->_db->getErrorMsg());
					}
						
				}
			}

			$query = " DELETE FROM #__hotelreservation_hotel_accommodation_type_relation
																WHERE hotelId = ".$data['hotel_id'];
					
			$this->_db->setQuery( $query );
			if (!$this->_db->query())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			if(isset( $data['accommodationtypes'])){
                $accommodationTypeRelation = new stdClass();
				foreach( $data['accommodationtypes'] as $type ){
					$row = $this->getTable('HotelAccommodationTypeRelation','JTable');
					$accommodationTypeRelation->hotelId= $data['hotel_id'];
					$accommodationTypeRelation->accommodationtypeId = $type;

					if (!$row->bind($accommodationTypeRelation))
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
					if (!$row->store(true))
					{
						throw( new Exception($this->_db->getErrorMsg()) );
						$this->setError($this->_db->getErrorMsg());
					}
						
				}
			}	
			$query = " DELETE FROM #__hotelreservation_hotel_environment_relation
						WHERE hotelId = ".$data['hotel_id'];
			

			$this->_db->setQuery( $query );
			if (!$this->_db->query())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			if(isset( $data['environments'])){


                $environmentRelation = new stdClass();
				foreach( $data['environments'] as $environment ){
						
					$row = $this->getTable('HotelEnvironmentRelation','JTable');
					$environmentRelation->hotelId= $data['hotel_id'];
					$environmentRelation->environmentId= $environment;

					if (!$row->bind($environmentRelation))
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
					if (!$row->store(true))
					{
						throw( new Exception($this->_db->getErrorMsg()) );
						$this->setError($this->_db->getErrorMsg());
					}
						
				}
			}
				

			$query = " DELETE FROM #__hotelreservation_hotel_region_relation
						WHERE hotelId = ".$data['hotel_id'];
			

			$this->_db->setQuery( $query );
			if (!$this->_db->query())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			if(isset( $data['regions'])){


                $regionRelation = new stdClass();
				foreach( $data['regions'] as $region ){
						
					$row = $this->getTable('HotelRegionRelation');
					$regionRelation->hotelId= $data['hotel_id'];
					$regionRelation->regionId= $region;
					if (!$row->bind($regionRelation))
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
					if (!$row->store(true))
					{
						throw( new Exception($this->_db->getErrorMsg()) );
						$this->setError($this->_db->getErrorMsg());
					}
						
				}
			}

			$data["id"]= $data["informationId"];
				
			$row = $this->getTable('HotelInformations','JTable');
			if (!$row->bind($data))
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
			if (!$row->store(true))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
			if(empty($data["informationId"]))
				$data["informationId"] = $this->_db->insertid();
			
			$this->_informationId = $data["informationId"];
			
					
			$data["id"]= $data["contactId"];

			$row = $this->getTable('hotelcontact');
			if (!$row->bind($data))
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
			if (!$row->store(true))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
				
				
			//prepare payment options
			if(isset( $data['paymentOptions'])){
				$query = " DELETE FROM #__hotelreservation_hotel_payment_option_relation
													WHERE hotelId = ".$data['hotel_id'];
	
				$this->_db->setQuery( $query );
				if (!$this->_db->query())
				{
					throw( new Exception($this->_db->getErrorMsg()) );
				}
                $paymentRelation = new stdClass();
				foreach( $data['paymentOptions'] as $paymentOption ){
					$row = $this->getTable('HotelPaymentOptionRelation','JTable');
					$paymentRelation->hotelId= $data['hotel_id'];
					$paymentRelation->paymentOptionId= $paymentOption;
					if (!$row->bind($paymentRelation))
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
					if (!$row->store(true))
					{
						throw( new Exception($this->_db->getErrorMsg()) );
						$this->setError($this->_db->getErrorMsg());
					}
				}
			}
			
			$this->saveChanelManagerData($data);
			
			$this->_db->CommitTrans();

		}
		catch( Exception $ex )
		{
			dmp($ex);
			exit();
			$this->_db->RollbackTrans();
			return false;
		}


		return true;
	}
	
	function saveChanelManagerData($data){
		//cubilis
		$user = $data["cubilis_user"];
		$password = $data["cubilis_password"];
		if(isset($user) && isset($password)){
			$channel = CHANNEL_MANAGER_CUBILIS;
			$hotel_id = $data['hotel_id'];
			$query = "INSERT INTO #__hotelreservation_hotel_channel_manager (hotel_id, service, user, password) VALUES ($hotel_id, '$channel', '$user','$password')
			ON DUPLICATE KEY UPDATE user = '$user', password = '$password' " ;
		
			$this->_db->setQuery( $query );
			if (!$this->_db->query() )
			{
				$ret = false;
				$e = 'INSERT / UPDATE sql STATEMENT error !';
			}
		}
		//beds24
		$user = $data["beds24_user"];
		$password = $data["beds24_password"];
		if(isset($user) && isset($password)){
			$channel = CHANNEL_MANAGER_BEDS24;
			$hotel_id = $data['hotel_id'];
			$query = "INSERT INTO #__hotelreservation_hotel_channel_manager (hotel_id, service, user, password) VALUES ($hotel_id, '$channel', '$user','$password')
			ON DUPLICATE KEY UPDATE user = '$user', password = '$password' " ;
		
			$this->_db->setQuery( $query );
			if (!$this->_db->query() )
			{
				$ret = false;
				$e = 'INSERT / UPDATE sql STATEMENT error !';
			}
		}
	}


	/**
	 * @param $hotelId
	 * @param $alias
	 * hotelId new or not that the check will be done in the checkIfAliasExists JTable method
	 * $alias the current alias that will be processed auto-generated or not
	 * if it is not unique or while the number of aliases is not 0 ,a dash and an incremented number(2 in this case) will be added to make it unique like so alias-name-2
	 * @return string the unique alias if the current alias auto-generated or not
	 * does exist in the hotels table , hotel_alias column  assigned to a hotel id
	 * @throws Exception
	 */
	function checkAlias($hotelId, $alias){

		$hotelsTable = $this->getTable('Hotels','Table');
		while($hotelsTable->checkIfAliasExists($hotelId, $alias)){
			$alias = JString::increment($alias, 'dash');
		}

		return $alias;
	}

	function copyEmailContent($hotelId){
		//copy emails default
		
		$row_email_default 	= $this->getTable('DefaultEmails','JTable');
		$defaultEmails= $row_email_default->getDefaultEmailsForHotel($hotelId);
		
		foreach($defaultEmails as $defaultEmail){
			$emailsTable	= $this->getTable('Emails','JTable');
			$emailsTable->hotel_id = $hotelId;
			$emailsTable->email_subject = $defaultEmail->email_default_subject;
			$emailsTable->email_name = $defaultEmail->email_default_name;
			$emailsTable->email_type = $defaultEmail->email_default_type;
			$emailsTable->is_default = 1;
			
			if(!$emailsTable->store()){
				throw( new Exception("Error saving email templates") );
			}
			$newEmailId = $emailsTable->email_id;

			$hoteltranslationsModel = new JHotelReservationLanguageTranslations();
			$translations= $hoteltranslationsModel->getAllTranslationObjects(EMAIL_TEMPLATE_TRANSLATION,$defaultEmail->email_default_id);
            $hoteltranslationsModel->saveCopiedContentTranslations($translations,$newEmailId);
		}
		
		return true;
	}

    function saveHotelDescriptions($post){
        try{
            $path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
            $dirs = JFolder::folders( $path );
            sort($dirs);
            $modelHotelTranslations = new JHotelReservationLanguageTranslations();
            $modelHotelTranslations->deleteTranslationsForObject(HOTEL_TRANSLATION,$post['hotel_id']);
            foreach( $dirs  as $_lng ){
                if(isset($post['hotel_description_'.$_lng]) && strlen($post['hotel_description_'.$_lng])>0){
                    $hotelDescription = JRequest::getVar( 'hotel_description_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
                    $modelHotelTranslations->saveTranslation(HOTEL_TRANSLATION,$post['hotel_id'],$_lng,$hotelDescription);
                }
            }
        }
        catch(Exception $e){
            JError::raiseWarning( 500,$e->getMessage());
        }

    }

    function saveMultiLangImportantInfo($post){
        try{
            $path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
            $dirs = JFolder::folders( $path );
            sort($dirs);
            $modelHotelTranslations = new JHotelReservationLanguageTranslations();
            $modelHotelTranslations->deleteTranslationsForObject(CANCELLATION_CONDITIONS,$post['informationId']);
            $modelHotelTranslations->deleteTranslationsForObject(CHILDREN_CATEGORY,$post['informationId']);
            foreach( $dirs  as $_lng ){
                if(isset($post['cancellation_conditions_'.$_lng]) && strlen($post['cancellation_conditions_'.$_lng])>0){
                    $cancellationConditions = JRequest::getVar( 'cancellation_conditions_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
                    $modelHotelTranslations->saveTranslation(CANCELLATION_CONDITIONS,$post['informationId'],$_lng,$cancellationConditions);

                }
                if(isset($post['children_category_'.$_lng]) && strlen($post['children_category_'.$_lng])>0){
                    $childrenCategory = JRequest::getVar('children_category_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML);
                    $modelHotelTranslations->saveTranslation(CHILDREN_CATEGORY,$post['informationId'],$_lng,$childrenCategory);

                }
            }
        }
        catch(Exception $e){
            JError::raiseWarning( 500,$e->getMessage());
        }
    }
    //function to assign created hotel to the creating hotel manager 
    function createHotelMapping($hotelId){
    	$hotelManagerId = JFactory::getUser()->id;
    	if(!isCorporateUser($hotelManagerId))
    		return false; 
   	
    	$row = $this->getTable('UserHotelMapping');
    	$row->createHotelMapping($hotelManagerId,$hotelId);
    	
    	return true; 
    }
}
?>