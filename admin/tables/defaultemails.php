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





class JTableDefaultEmails extends JTable {
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db) {

        parent::__construct('#__hotelreservation_emails_default', 'email_default_id', $db);
    }

    function setKey($k)
    {
        $this->_tbl_key = $k;
    }

    /**
     * Method to build a sql query to get the list of default emails that belong to a hotel
     * based on its $hotelId
     * @param $hotelId
     * @return mixed
     */
    function getDefaultEmailsForHotel($hotelId){
        $query = "SELECT *
				  FROM #__hotelreservation_emails_default eml_def
				  WHERE eml_def.email_default_type NOT IN
				           ( SELECT em.email_type FROM #__hotelreservation_emails em
							 WHERE em.hotel_id = ".(int)$hotelId."
							)";
        $this->_db->setQuery( $query );
        return $this->_db->loadObjectList();
    }
}