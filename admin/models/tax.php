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
class JHotelReservationModelTax extends JModelAdmin
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @var        string    The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_JHOTELRESERVATION_TAX';

    /**
     * Model context string.
     *
     * @var        string
     */
    protected $_context = 'com_jhotelreservation.tax';


    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       2.5
     */
    public function getTable($type = 'Taxes', $prefix = 'JTable', $config = array())
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
        $form = $this->loadForm('com_jhotelreservation.tax', 'tax', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.tax.data', array());
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

        // Load the User state.
        $pk = (int)JRequest::getInt('id');
        if (!$pk)
            $pk = (int)JRequest::getInt('tax_id');
        $this->setState('tax.tax_id', $pk);

        $app->setUserState('com_jhotelreservation.edit.tax.tax_id', $pk);
    }



    /**
     * Function to get the hotel data based on hotel_id
     * @return mixed
     */
    function getHotel()
    {
        $hotel_id = JRequest::getVar('hotel_id',  0, '');
        $hotelTable = JTable::getInstance("Emails", "JTable");
        $hotel = $hotelTable->getData($hotel_id);
        return $hotel;
    }


    /**
     * Method to get a menu item.
     *
     * @param   integer	The id of the menu item to get.
     *
     * @return  mixed  Menu item data object on success, false on failure.
     */
    public function getItem($itemId = null)
    {
        $itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('tax.tax_id');

        $false	= false;

        // Get a menu item row instance.
        $table = $this->getTable("Taxes","JTable");

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


        return $value;
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
     * Method to get the Hotel Ids
     * @return mixed
     */
    function getHotelId()
    {
        $hotel_id = JRequest::getVar('hotel_id',  0, '');
        $this->setHotelId($hotel_id);
        return $hotel_id;
    }


    /**
     * @param $data
     * @return bool
     */
    function store($data)
    {
        $row = $this->getTable('Taxes');

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

    /**
     * @param $items
     * @return bool
     */
    function delete(&$items)
    {
        $items = (array) $items;
        JArrayHelper::toInteger($items);

        $row = $this->getTable("Taxes","JTable");

        if (count($items)) {
            foreach ($items as $item) {
                if (!$row->delete($item)) {
                    $this->setError($row->getErrorMsg());
                    $msg = JText::_('LNG_ERROR_DELETE_TAX', true);
                    return false;
                }
            }
        }
        $this->cleanCache();

        return true;

    }


    /**
     * Method to change the state of one record
     * @return mixed
     */
    function state()
    {
        $cids = JRequest::getVar('cid', array(0));
        $taxesTable = $this->getTable("Taxes", "JTable");
        if (count($cids)) {
            foreach ($cids as $tid) {
                $state = $taxesTable->state($tid);
                return $state;
            }
        }
    }

}
