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

class JTableRatingClassifications extends JTable
{

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__hotelreservation_rating_classification', 'id', $db);
    }

    function setKey($k)
    {
        $this->_tbl_key = $k;
    }

	/**
	 * @param $id
	 *
	 * Method to be used in the front end 
	 * @return mixed|stdClass
	 */
    function getHotelRatingClassification($hotelRatingScore){
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id,name');
        $query->from($db->quoteName('#__hotelreservation_rating_classification'));
	    $query->where((float)$hotelRatingScore. ' between min_rate and max_rate ');
        $db->setQuery((string)$query);
        $ratingScores = $db->loadObjectList();
        return $ratingScores;
    }

}