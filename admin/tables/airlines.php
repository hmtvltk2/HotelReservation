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

class JTableAirlines extends JTable
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
    function __construct(&$db) {

        parent::__construct('#__hotelreservation_airlines', 'airline_id',$db);
    }
    function setKey($k)
    {
        $this->_tbl_key = $k;
    }

    /**
     * Method to get the datas from Hotels Table
     * @param $hotelId  Id of the selected hotel
     * @return mixed
     */
    function getAirlines() {

        $db= JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('hh.*');
        $query->from($db->quoteName('#__hotelreservation_airlines'). ' AS hh');
        $db->setQuery((string)$query);
        return $db->loadObjectList();
    }


    /**
     * @param $airlineid
     * @return bool
     */
    function state($airlineid)
    {
        $db= JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->update($db->quoteName('#__hotelreservation_airlines'));
        $query->set('is_available = IF(is_available, 0, 1)');
        if(is_numeric($airlineid)) {
            $query->where('airline_id=' . (int)$airlineid);
        }
        $db->setQuery((string)$query);

        if (!$db->query())
        {
            return false;
        }
        return true;
    }

}