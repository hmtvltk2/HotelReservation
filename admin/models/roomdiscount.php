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


class JHotelReservationModelRoomDiscount extends JModelAdmin
{

    function __construct()
    {
        parent::__construct();
    }


    /**
     * @var        string    The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_JHOTELRESERVATION_ROOMDISCOUNT';

    /**
     * Model context string.
     *
     * @var        string
     */
    protected $_context = 'com_jhotelreservation.roomdiscount';


    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       2.5
     */
    public function getTable($type = 'RoomDiscounts', $prefix = 'JTable', $config = array())
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
        $form = $this->loadForm('com_jhotelreservation.roomdiscount', 'roomdiscount', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.roomdiscount.data', array());
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
        if (!($hotelId = $app->getUserState('com_jhotelreservation.roomdiscounts.filter.hotel_id'))) {
            $hotelId = JRequest::getInt('hotel_id', '0');
        }
        $this->setState('roomdiscount.hotel_id', $hotelId);
        // Load the User state.
        $pk = (int)JRequest::getInt('id', 0 ,'');
        if (!$pk)
            $pk = (int)JRequest::getInt('discount_id', 0, '');
        $this->setState('roomdiscount.discount_id', $pk);

        $app->setUserState('com_jhotelreservation.edit.offer.hotel_id',$hotelId);
        $app->setUserState('com_jhotelreservation.edit.roomdiscount.discount_id', $pk);
    }

    /**
     * Method to store an item.
     *
     * @param   $data   The data to be saved of the menu item.
     *
     * @return  boolean  true if the data is stored successfully.
     */
    function save($data)
    {
        $row = $this->getTable();

        $id	= (!empty($data['discount_id'])) ? $data['discount_id'] : (int) $this->getState('roomdiscount.discount_id');

        $isNew = true;

        $data['discount_datas']= JHotelUtil::convertToMysqlFormat($data['discount_datas']);
        $data['discount_datae']= JHotelUtil::convertToMysqlFormat($data['discount_datae']);
        $data['percent']= empty($data['percent'])?0:1;

        if(empty($data['discount_room_ids'])){
            $data['discount_room_ids'] = '';
            $data['offer_ids'] = '';
        }
        if(empty($data['offer_ids'])){
            $data['offer_ids'] = '';
        }
        if(empty($data['excursion_ids'])){
        $data['excursion_ids'] = '';
        }

        // Load the row if saving an existing item.
        if ($id > 0)
        {
           $row->load($id);
           $isNew = false;
        }

        // Bind the form fields to the table
        if (!$row->bind($data))
        {
            $this->setError($row->getErrorMsg());
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($row->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError( $row->getErrorMsg() );
            return false;
        }

        $this->setState('roomdiscount.discount_id', $row->discount_id);


        // Clean the cache
        $this->cleanCache();


        return true;
    }


    /**
     * Method to delete a record by Its Id
     *
     * @param $items
     * @return bool
     */
    public function delete(&$items)
    {
        //sanitaze the ids.
        $items = (array) $items;
        JArrayHelper::toInteger($items);

        //get e group row instance
        $table= $this->getTable('RoomDiscounts','JTable');

        if(count($items)) {
            foreach ($items as $item) {
                if (!$table->delete($item)) {
                    $this->setError($table->getError());
                    $msg = JText::_('LNG_ERROR_DELETE_DISCOUNT',true);
                    return false;
                }
            }
        }

        $this->cleanCache();

        return true;
    }


    /**
     * Function to get the hotel data based on hotel_id
     * @return mixed
     */
    function getHotel()
    {
        $hotel_id = $this->getState('roomdiscount.hotel_id');
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
        $itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('roomdiscount.discount_id');

        $hId = $this->getState('roomdiscount.hotel_id');

        $false	= false;

        // Get a menu item row instance.
        $table = $this->getTable("RoomDiscounts");

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


        if($itemId == 0) {
            $value->discount_datas = gmdate('Y-m-d');
            $value->discount_datae = gmdate('Y-m-d');
        }


        $typesTable = $this->getTable('RoomDiscounts');
        $itemRoom =  $typesTable->getitemRoomsDiscounts($hId,$itemId);
        $value->itemRooms = $this->_getList($itemRoom);


        $offer =$typesTable->getOfferRoomDiscounts($hId,$itemId);
        $value->offers = $this->_getList($offer);




        $selectedOffers = $value->offer_ids;
        //dmp($selectedOffers);
        if(isset($selectedOffers)){
            $selectedOffers = explode(",",$selectedOffers);
            foreach($value->offers as $offer){
                $offer->is_sel = 0;
                if(in_array($offer->offer_id, $selectedOffers)){
                    $offer->is_sel = 1;
                }
            }
        }

        $excursion = $typesTable->getExcursions($hId,$itemId);
        $value->excursions = $this->_getList($excursion);


        $value->discount_datas = JHotelUtil::convertToFormat($value->discount_datas);
        $value->discount_datae = JHotelUtil::convertToFormat($value->discount_datae);



        return $value;
    }

    /**
     * Method to change the state of one record
     * @return mixed
     */
    function state($aid)
    {
        $roomDiscountsTable = $this->getTable("RoomDiscounts","JTable");
        $state = $roomDiscountsTable->state($aid);
        return $state;
    }

    /**
     * @param $roomIds
     * @param $offerIds
     * @return string
     */
    function getHTMLContentOffers($roomIds, $offerIds)
    {
        //dmp($offerIds);

        $roomIds = implode(",", $roomIds);

        //gets the Offers of one room based on room Id
        $offersRoomTable = $this->getTable("RoomDiscounts","JTable");
        $offer = $offersRoomTable->getHTMLContentOffersQuery($roomIds);
        $offers = $this->_getList($offer);



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

}