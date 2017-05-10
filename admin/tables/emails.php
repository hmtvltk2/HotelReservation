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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableEmails extends JTable
{
    /**
     * @var
     */
    private $db;

    /**
     * @var
     */
    private $pk;


    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db)
    {

        parent::__construct('#__hotelreservation_emails', 'email_id', $db);

    }

    /**
     * Method to get the datas from Hotels Table
     * @param $hotelId  Id of the selected hotel
     * @return mixed
     */
    function getData($hotelId) {

        $db= JFactory::getDBO();
        $query = $db->getQuery(true);
        //Get All fields from the table
        $query->select('hh.*');
        $query->from($db->quoteName('#__hotelreservation_hotels'). ' AS hh');

        //Join the currency table with the country table
        if(is_numeric($hotelId)) {
            $query->select('hc.country_name');
            $query->join('LEFT', $db->quoteName('#__hotelreservation_countries'). ' AS hc on hc.country_id = hh.country_id');
            $query->where('hh.hotel_id=' . (int)$hotelId);
        }
        $db->setQuery((string)$query);
        $hotels = $db->loadObjectList();

        return $hotels;
    }

    /**
     * Method to delete a record
     * @param null $pk
     * @param bool $children
     * @return mixed
     */
    public function delete($pk = null, $children = false)
    {

        $query = $this->_db->getQuery(true);
        $query->delete();
        $query->from("#__hotelreservation_emails");
        $query->where('email_id = ' . (int)$pk);
        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');


        return parent::delete($pk, $children);
    }

    /**
     * Method to run an update query and check for a database error
     *
     * @param   string  $query         The query.
     * @param   string  $errorMessage  Unused.
     *
     * @return  boolean  False on exception
     *
     * @since   11.1
     */
    protected function _runQuery($query, $errorMessage)
    {
        $this->_db->setQuery($query);

        // Check for a database error.
        if (!$this->_db->execute())
        {
            $e = new JException($this->_db->getErrorMsg());
            $this->setError($e);
            $this->_unlock();
            return false;
        }
        if ($this->_debug)
        {
            $this->_logtable();
        }
    }

    /**
     * Method to build an SQL query to change the state of one item based on its:
     * @param $emailId
     * and
     * @param $hotelId
     * @return bool True if the query is executed and the state is changed
     */
    function state($emailId,$hotelId)
    {
        $db= JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from($db->quoteName('#__hotelreservation_emails'));
        $db->setQuery((string)$query);
        $item = $db->loadObject();

        $query->update($db->quoteName('#__hotelreservation_emails'));
        if(is_numeric($emailId) && is_numeric($hotelId) && is_string($item->email_type)) {
            $query->set('is_default = IF(email_id='.(int)$emailId.', 1, 0)');
            $query->where('hotel_id=' . (int)$hotelId.' AND email_type='.$db->quote($item->email_type));
        }
        $db->setQuery((string)$query);

        if (!$db->query())
        {
            return false;
        }
        return true;
    }
}