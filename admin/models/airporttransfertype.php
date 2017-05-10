
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

jimport('joomla.application.component.modeladmin');

/**
 * List Model.
 *
 * @package    JHotelReservation
 * @subpackage  com_jhotelreservation
 */
class JHotelReservationModelAirportTransferType extends JModelAdmin
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * @var        string    The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_JHOTELRESERVATION_AirportTransferType';

    /**
     * Model context string.
     *
     * @var        string
     */
    protected $_context = 'com_jhotelreservation.airporttransfertype';


    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       2.5
     */
    public function getTable($type = 'AirportTransferTypes', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param       array $data Data for the form.
     * @param       boolean $loadData True if the form is to load its own data (default case), false if not.
     * @return      mixed   A JForm object on success, false on failure
     * @since       2.5
     */
    public function getForm($data = array(), $loadData = true)
    {
        //Get the form
        $form = $this->loadForm('com_jhotelreservation.airporttransfertype', 'airporttransfertype', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return      mixed   The data for the form.
     * @since       2.5
     */
    protected function loadFormData()
    {
        //Check the session for previously entered form data
        $data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.airporttransfertype.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object    A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     */
    protected function canDelete($record)
    {
        return true;
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object    A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     */
    protected function canEditState($record)
    {
        return true;
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

        if (!($hotelId = $app->getUserState('com_jhotelreservation.airporttransfertypes.filter.hotel_id'))) {
            $hotelId = JRequest::getInt('hotel_id', '0');
        }
        $this->setState('airporttransfertype.hotel_id', $hotelId);

        // Load the User state.
        $pk = (int)JRequest::getInt('id', 0, '');
        if (!$pk)
            $pk = (int)JRequest::getInt('airport_transfer_type_id', 0, '');
        $this->setState('airporttransfertype.airport_transfer_type_id', $pk);

        $app->setUserState('com_jhotelreservation.edit.airporttransfertype.airport_transfer_type_id', $pk);

    }


    /**
     * Function to get the hotel data based on hotel_id
     * @return mixed
     */
    function getHotel()
    {
        $hotel_id = JRequest::getInt('hotel_id', 0, '');
        $hotelTable = JTable::getInstance("Emails", "JTable");
        $hotel = $hotelTable->getData($hotel_id);
        return $hotel;
    }


    /**
     * Method to get the Hotel Ids
     * @return mixed
     */
    function getHotelId()
    {
        $hotel_id = JRequest::getInt('hotel_id', 0, '');
        $this->setHotelId($hotel_id);
        return $hotel_id;
    }


    /**
     * Method to set the hotel Ids
     * @param $hotel_id
     */
    function setHotelId($hotel_id)
    {
        // Set id and wipe data
        $this->_hotel_id = $hotel_id;
    }

    /**
     * Method to get a menu item.
     *
     * @param   integer    The id of the menu item to get.
     *
     * @return  mixed  Menu item data object on success, false on failure.
     */
    public function getItem($itemId = null)
    {
        $itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('airporttransfertype.airport_transfer_type_id');
        $hotelId = (!empty($itemId)) ? $itemId : (int)$this->getState('hotel.hotel_id');

        $false = false;

        // Get a menu item row instance.
        $table = $this->getTable("Airporttransfertypes", "JTable");

        // Attempt to load the row.
        $return = $table->load($itemId);

        // Check for a table object error.
        if ($return === false && $table->getError()) {
            $this->setError($table->getError());
            return $false;
        }

        $properties = $table->getProperties(1);
        $value = JArrayHelper::toObject($properties, 'JObject');


        return $value;
    }

    /**
     * Method to store an item in the data table
     * @param $data
     * @return bool
     */
    function save($data)
    {
        $id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('airporttransfertype.airport_transfer_type_id');
        $isNew = true;
        $data['airport_transfer_type_name'] = $this->setDefaultName($data);


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

        $this->setState('airporttransfertype.airport_transfer_type_id', $table->airport_transfer_type_id);

        // Clean the cache
        $this->cleanCache();

        return true;
    }

    /**
     * Method to delete a record
     * @return bool
     */
    function remove()
    {
        $cids = JRequest::getVar('airport_transfer_type_id', array(0), 'post', 'array');

        $airlinesTable = $this->getTable("AirportTransferTypes", "JTable");
        $checkedrecords = $airlinesTable->checkedItemsForRemoval($cids);

        $checked_records = $this->_getList($checkedrecords);
        if (count($checked_records) > 0) {
            JError::raiseWarning(500, JText::_('LNG_SKIP_AIRLINE_REMOVE', true));
            return false;
        }

        $row = $this->getTable();

        if (count($cids)) {
            foreach ($cids as $cid) {
                if (!$row->delete($cid)) {
                    $this->setError($row->getErrorMsg());
                    $msg = JText::_('LNG_ERROR_DELETE_AIRPORT', true);
                    return false;
                }
            }
        }
        return true;

    }

    /**
     * Method to change the state of one record
     * @return mixed
     */
    function state($airport_transfer_type_id)
    {
        $airlinesTable = $this->getTable("AirportTransferTypes", "JTable");
        $state = $airlinesTable->state($airport_transfer_type_id);
        return $state;
    }

    function saveTranslations($data)
    {

        try {
            $path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
            $dirs = JFolder::folders($path);
            sort($dirs);
            $modelHotelTranslations = new JHotelReservationLanguageTranslations();
            $modelHotelTranslations->deleteTranslationsForObject(AIRPORT_TRANSFER_TRANSLATION_NAME, $data['airport_transfer_type_id']);
            $modelHotelTranslations->deleteTranslationsForObject(AIRPORT_TRANSFER_TRANSLATION, $data['airport_transfer_type_id']);

            foreach ($dirs as $_lng) {
                if (isset($data['airport_transfer_type_description_' . $_lng]) && strlen($data['airport_transfer_type_description_' . $_lng]) > 0) {
                    $transferDescription = JRequest::getVar('airport_transfer_type_description_' . $_lng, '', 'post', 'string', JREQUEST_ALLOWHTML);
                    $modelHotelTranslations->saveTranslation(AIRPORT_TRANSFER_TRANSLATION, $data['airport_transfer_type_id'], $_lng, $transferDescription);
                }
                if (isset($data['airport_transfer_type_name_' . $_lng]) && strlen($data['airport_transfer_type_name_' . $_lng]) > 0) {
                    $transferNametranslations = JRequest::getVar('airport_transfer_type_name_' . $_lng, '', 'post', 'string');

                    $modelHotelTranslations->saveTranslation(AIRPORT_TRANSFER_TRANSLATION_NAME, $data['airport_transfer_type_id'], $_lng, $transferNametranslations);
                }
            }
        } catch (Exception $e) {
            print_r($e);
            exit;
            JError::raiseWarning(500, $e->getMessage());
        }

    }

    /**
     * @param $post
     * @return mixed
     */
    function setDefaultName($post)
    {
        $languageTag = JRequest::getVar('_lang');
        $dirs = JHotelUtil::languageTabs();

        if (!empty($post['airport_transfer_type_name_' . $languageTag])) {
            $post['airport_transfer_type_name'] =$post['airport_transfer_type_name_' . $languageTag];

            return $post['airport_transfer_type_name'];
        } else {
            foreach ($dirs as $_lng) {
                if (!empty($post['airport_transfer_type_name_' . $_lng])) {
                    $post['airport_transfer_type_name'] = $post['airport_transfer_type_name_' . $_lng];
                    return $post['airport_transfer_type_name'];
                }
            }
        }
    }
}