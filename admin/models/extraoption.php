<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
/**
 * Company Model for Companies.
 *
 */
class JHotelReservationModelExtraOption extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JHotelReservation_COMPANY_TYPE';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jhotelreservation.extraoption';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record)
	{
        if (!empty($record->id))
        {
            if ($record->state != -2)
            {
                return;
            }

            $user = JFactory::getUser();

            if (!empty($record->catid))
            {
                return $user->authorise('core.delete', 'com_jhotelreservation.extraoption.' . (int) $record->catid);
            }
            else
            {
                return parent::canDelete($record);
            }
        }
//		return true;
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
        $user = JFactory::getUser();

        // Check against the category.
        if (!empty($record->catid))
        {
            return $user->authorise('core.edit.state', 'com_jhotelreservation.extraoption.' . (int) $record->catid);
        }
        // Default to component settings if category not known.
        else
        {
            return parent::canEditState($record);
        }
		//return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	*/
	public function getTable($type = 'ExtraOption', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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

		if (!($hotelId = $app->getUserState('com_jhotelreservation.extraoptions.filter.hotel_id'))) {
			$hotelId = JRequest::getInt('hotel_id', '0');
		}
		$this->setState('extraoption.hotel_id', $hotelId);
		
		// Load the User state.
		$sourceId = JRequest::getInt('dId');
		$this->setState('extraoption.dId', $sourceId);
        $app->setUserState('com_jhotelreservation.edit.extraoption.dId',$sourceId);

        // Load the User state.
		$id = JRequest::getInt('id');
		$this->setState('extraoption.id', $id);
        $app->setUserState('com_jhotelreservation.edit.extraoption.id',$id);
    }

	/**
	 * Method to get a menu item.
	 *
	 * @param   integer	The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null)
	{
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('extraoption.id');
		$intialId = $itemId;
		if($this->getState('extraoption.dId'))
			$itemId = $this->getState('extraoption.dId');

		$false	= false;

		// Get a menu item row instance.
		$table = $this->getTable();

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
		$value->id = $intialId;
        $value->commission = $value->commission >= 0?$value->commission:'';
		$value->extra_option_cost = $value->extra_option_cost !=0?$value->extra_option_cost:'';

		$value->start_date = JHotelUtil::convertToFormat($value->start_date);
		$value->end_date = JHotelUtil::convertToFormat($value->end_date);
		
		$value->rooms = $this->getRooms();


        return $value;
	}
	
	function getRooms(){
		$query = " SELECT
					r.room_id,
					r.room_name	,
					IF( ISNULL(eo.id), 0, 1) AS is_sel
					FROM #__hotelreservation_rooms r
					LEFT JOIN
					(
					SELECT * FROM #__hotelreservation_extra_options WHERE id = ".$this->getState('extraoption.id'). "
					) eo ON FIND_IN_SET(r.room_id, eo.room_ids)
					WHERE r.is_available = 1 AND r.hotel_id  = ".$this->getState('extraoption.hotel_id')."
		";
		// dmp($query);
		$itemRooms = $this->_getList( $query );
		return $itemRooms;
	}

	/**
	 * Method to get the menu item form.
	 *
	 * @param   array  $data		Data for the form.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		exit;
		// The folder and element vars are passed when saving the form.
		if (empty($data))
		{
			$item		= $this->getItem();
			// The type should already be set.
		}
		// Get the form.
		$form = $this->loadForm('com_jhotelreservation.extraoption', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
		if (empty($form))
		{
			return false;
		}
        // Determine correct permissions to check.
        if ($this->getState('extraoption.id'))
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
            //$form->setFieldAttribute('publish_up', 'disabled', 'true');
            //$form->setFieldAttribute('publish_down', 'disabled', 'true');
            //$form->setFieldAttribute('state', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            //$form->setFieldAttribute('publish_up', 'filter', 'unset');
            //$form->setFieldAttribute('publish_down', 'filter', 'unset');
            //$form->setFieldAttribute('state', 'filter', 'unset');
        }
		
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.extraoption.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}


	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 * @return  boolean  True on success.
	 */
	public function save($data)
	{
		$id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('extraoption.id');
		$isNew = true;
		$data["start_date"] = JHotelUtil::convertToMysqlFormat($data["start_date"]);
		$data["end_date"] = JHotelUtil::convertToMysqlFormat($data["end_date"]);
		$data['name'] = $this->setDefaultName($data);
		$data['extra_option_cost'] = 'NULL';
		// Get a row instance.
		$table = $this->getTable();

		// Load the row if saving an existing item.
		if ($id > 0)
		{
			$table->load($id);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}

		$this->setState('extraoption.id', $table->id);

		// Clean the cache
		$this->cleanCache();

		if($id>0){
			$this->storePicture($data["image_path"], $table->id, $id);
		}
		return true;
	}

	function storePicture($picturePath, $id, $oldId){
	
		//prepare photos
		$path_old = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.EXTRA_OPTIONS_PICTURE_PATH.$picturePath);
		$files = glob( $path_old."*.*" );
	
		$path_new = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.EXTRA_OPTIONS_PICTURE_PATH.($id)."/");
	
		
		$file_tmp = JHotelUtil::makePathFile( $path_old);
		if( !is_file($file_tmp) )
			return;
		//dmp("is file");
		if( !is_dir($path_new) )
		{
			if( !@mkdir($path_new) )
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
		}
	
		//dmp(($path_old.basename($picturePath).",".$path_new.basename($picturePath)));
		// exit;
		if( $path_old.basename($picturePath) != $path_new.basename($picturePath) )
		{
			//dmp($file_tmp);
			//dmp($picturePath);
			//dmp(strpos($file_tmp,"\\0\\"));
			if(strpos($file_tmp,"\\0\\")>0 && $oldId == 0 && @rename($path_old,$path_new.basename($picturePath)) ){
				//dmp(1);
				$picturePath = ($id).'/'.basename($picturePath);
			}else if(strpos($file_tmp,"\\0\\")== false && @copy($path_old,$path_new.basename($picturePath)) ){
				//dmp(2);
				$picturePath = ($id).'/'.basename($picturePath);
			}else
			{
				//throw( new Exception($this->_db->getErrorMsg()) );
			}
			//dmp($picturePath);
		}
		$data = array();
		$data["image_path"] = $picturePath;
		// Get a row instance.
		$table = $this->getTable();

		// Load the row if saving an existing item.
		if ($id > 0)
		{
			$table->load($id);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}
		
		return true;
	}
	
	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function delete(&$itemIds)
	{
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		JArrayHelper::toInteger($itemIds);
	
		// Get a group row instance.
		$table = $this->getTable();
	
		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId)
		{
	
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

	function getHTMLContentOffers($roomIds, $offerIds)
	{
		//dmp($offerIds);
	
		$roomIds = implode(",", $roomIds);
	
		$query = "
		select  o.offer_id, o.offer_name
		from #__hotelreservation_rooms r
		inner join #__hotelreservation_offers_rooms 			hor 	ON hor.room_id	 	= r.room_id
		inner join #__hotelreservation_offers		 			o 		ON hor.offer_id 	= o.offer_id
		where r.room_id in ($roomIds)
		";
	
		$offers = $this->_getList( $query );
	
		$offerIds = explode (',',$offerIds[0]);
	
		ob_start();
		?>
			<select id="offer_ids" multiple="multiple" name="offer_ids[]">
				<option value=""><?php echo JText::_('LNG_SELECT_OFFERS',true); ?></option>
				<?php
				foreach( $offers as $offer )
				{
					
					?>
					<option <?php echo in_array($offer->offer_id,$offerIds)? 'selected="selected"' : ''?> 	value='<?php echo $offer->offer_id?>'><?php echo $offer->offer_name ?></option>
					<?php
				}
				?>
			</select>
		<?php 
		$buff = ob_get_contents();
		ob_end_clean();
		
		return htmlspecialchars($buff);
	}

	function saveDescriptions($data){
		try{
			$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
			$dirs = JFolder::folders( $path );
			sort($dirs);
			$modelHotelTranslations = new JHotelReservationLanguageTranslations();
			$modelHotelTranslations->deleteTranslationsForObject(EXTRA_OPTIONS_TRANSLATION,$data['id']);
            $modelHotelTranslations->deleteTranslationsForObject(EXTRA_OPTION_NAME,$data['id']);
            foreach( $dirs  as $_lng ){
				if(isset($data['description_'.$_lng]) && strlen($data['description_'.$_lng])>0){
					$description = JRequest::getVar( 'description_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
					$modelHotelTranslations->saveTranslation(EXTRA_OPTIONS_TRANSLATION,$data['id'],$_lng,$description);
				}

                if(isset($data['name_'.$_lng]) && strlen($data['name_'.$_lng])>0){
                    $name = JRequest::getVar( 'name_'.$_lng, '', 'post', 'string');
                    $modelHotelTranslations->saveTranslation(EXTRA_OPTION_NAME,$data['id'],$_lng,$name);
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
     * @return mixed
     */
    function setDefaultName($post){
        $languageTag = JRequest::getVar('_lang');
        $dirs = JHotelUtil::languageTabs();

        if(!empty($post['name_'.$languageTag])) {
            $post['name'] = $post['name_'.$languageTag];
            return $post['name'];
        }else{
            foreach($dirs as $_lng){
                if(!empty($post['name_'.$_lng])){
                    $post['name'] = $post['name_'.$_lng];
                    return $post['name'];
                }
            }
        }
    }
}
