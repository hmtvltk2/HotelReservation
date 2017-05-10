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

class JTablePoiPictures extends JTable
{

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db) {

        parent::__construct('#__hotelreservation_points_of_interest_pictures', 'id', $db);
    }

    function setKey($k)
    {
        $this->_tbl_key = $k;
    }

    function getPoiPictures($poid,$enable = 0){
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__hotelreservation_points_of_interest_pictures'));
	    if($enable == 1) {
		    $query->where( 'poi_picture_enable = ' . $enable );
	    }
	    $query->where('poid='.(int)$poid);
	    $query->order('id asc');
        $db->setQuery((string)$query);
        $files = $db->loadObjectList();

        return $files;
    }
}