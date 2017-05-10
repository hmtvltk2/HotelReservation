<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
/**
 * Company Model for POI.
 *
 */
class JHotelReservationModelInterestPoint extends JModelAdmin
{
    /**
     * @var		string	The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_JHotelReservation_POI';

    /**
     * Model context string.
     *
     * @var		string
     */
    protected $_context		= 'com_jhotelreservation.interestpoint';

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
                return $user->authorise('core.delete', 'com_jhotelreservation.interestpoint.' . (int) $record->catid);
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
            return $user->authorise('core.edit.state', 'com_jhotelreservation.interestpoint.' . (int) $record->catid);
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
    public function getTable($type = 'POI', $prefix = 'JTable', $config = array())
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

        if (!($hotelId = $app->getUserState('com_jhotelreservation.interestpoints.filter.hotel_id'))) {
            $hotelId = JRequest::getInt('hotel_id', '0');
        }
        $this->setState('interestpoint.hotel_id', $hotelId);

        // Load the User state.
        $id = JRequest::getInt('id');
        $this->setState('interestpoint.id', $id);
        $app->setUserState('com_jhotelreservation.edit.interestpoint.id',$id);
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
        $itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('interestpoint.id');

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

        $countryTable = $this->getTable('Country','JTable');
        $value->countries = $countryTable->getCountries();

        return $value;
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
        $form = $this->loadForm('com_jhotelreservation.interestpoint', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
        if (empty($form))
        {
            return false;
        }
        // Determine correct permissions to check.
        if ($this->getState('interestpoint.id'))
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

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('ordering', 'filter', 'unset');
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
        $data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.interestpoint.data', array());

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
        $id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('interestpoint.id');
        $isNew = true;

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

        $this->setState('interestpoint.id', $table->id);

        $this->savePictures($data);

        // Clean the cache
        $this->cleanCache();

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


    function saveDescriptions($data){
        try{
            $path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
            $dirs = JFolder::folders( $path );
            sort($dirs);
            $modelHotelTranslations = new JHotelReservationLanguageTranslations();
            $modelHotelTranslations->deleteTranslationsForObject(POI_TRANSLATION,$data['id']);
            foreach( $dirs  as $_lng ){
                if(isset($data['description_'.$_lng]) && strlen($data['description_'.$_lng])>0){
                    $description = JRequest::getVar( 'description_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
                    $modelHotelTranslations->saveTranslation(POI_TRANSLATION,$data['id'],$_lng,$description);
                }
            }
        }
        catch(Exception $e){
            print_r($e);
            exit;
            JError::raiseWarning( 500,$e->getMessage());
        }
    }

    function state($aid)
    {
        $roomDiscountsTable = $this->getTable();
        $state = $roomDiscountsTable->state($aid);
        return $state;
    }

    public function savePictures($data){
        //prepare photos
        $path_old = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.POINTS_OF_INTEREST_PICTURE_PATH.DS.($data['id']+0)."/");
        $files = glob( $path_old."*.*" );

        $data['id'] = $this->getState('interestpoint.id');
        $path_new = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.POINTS_OF_INTEREST_PICTURE_PATH.DS.($data['id']+0)."/");

        $picture_ids 	= array();

        foreach( $data['pictures'] as $value )
        {
            $row = $this->getTable('PoiPictures','JTable');

            $pic 						= new stdClass();
            $pic->id		            = 0;
            $pic->poid 				    = $data['id'];
//            $pic->poi_picture_info		= $value['poi_picture_info'];
            $pic->poi_picture_path		= $value['poi_picture_path'];
            $pic->poi_picture_enable	= $value['poi_picture_enable'];
            //dmp($pic);
            $file_tmp = JHotelUtil::makePathFile( $path_old.basename($pic->poi_picture_path) );

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
            if( $path_old.basename($pic->poi_picture_path) != $path_new.basename($pic->poi_picture_path) )
            {
                if(@rename($path_old.basename($pic->poi_picture_path),$path_new.basename($pic->poi_picture_path)) )
                {

                    $pic->poi_picture_path	 = POINTS_OF_INTEREST_PICTURE_PATH.DS.($data['poid']+0).'/'.basename($pic->poi_picture_path);
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
                if( $pic == JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.POINTS_OF_INTEREST_PICTURE_PATH.DS.$value['poi_picture_path']) )
                {
                    $is_find = true;
                    break;
                }
            }
            /*if( $is_find == false )
                @unlink( JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.DS.$value['room_picture_path']) );*/
        }
        $query = " DELETE FROM #__hotelreservation_points_of_interest_pictures WHERE poid = '".$data['id']."'
		".( count($picture_ids)> 0 ? " AND id NOT IN (".implode(',', $picture_ids).")" : "");

        $this->_db->setQuery( $query );
        if (!$this->_db->query())
        {
            throw( new Exception($this->_db->getErrorMsg()) );
        }
        //~prepare photos
        return true;

    }

    function getPoiPictures(){
        $table = $this->getTable('PoiPictures','JTable');
        $files = $table->getPoiPictures($this->getState('interestpoint.id'));
        $pictures	= array();
        if(count($files)>0){
            foreach( $files as $value )
            {
                $pictures[]	= array(
                    'poi_picture_path' 		=> $value->poi_picture_path,
                    'poi_picture_enable'		=> $value->poi_picture_enable,
                );
            }
        }
        return $pictures;
    }
}
