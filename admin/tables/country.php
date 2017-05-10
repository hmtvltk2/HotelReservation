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

class JTableCountry extends JTable
{
    /**
     * @var
     */
    private $db;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct($db)
    {
        parent::__construct('#__hotelreservation_countries', 'country_id', $db);
    }

    function getCountryCurrencies(){
        $countryTable = JTable::getInstance("Country", "JTable");
        $countries =  $countryTable->getData();
        return $countries;
    }

    /**
     * Method to build an sql query to get the List datas:
     * @return mixed returns the Object data List of all Countries
     */
    function getData() {

        $db= JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__hotelreservation_countries');
        $db->setQuery((string)$query);
        $countries = $db->loadObjectList();


        return $countries;
    }

    /**
     * @return mixed only country_id and country_name
     */

    function getCountries()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('country_id,country_name');
        $query->from($db->quoteName('#__hotelreservation_countries'));
        $db->setQuery((string)$query);
        $countries = $db->loadObjectList();
        return $countries;
    }

}