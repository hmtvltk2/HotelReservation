<?php
/**
 * @copyright	Copyright (C) 2008-2016 CMSJunkie. All rights reserved.
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

class JTableChangeLog extends JTable
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

        parent::__construct('#__hotelreservation_changelog', 'id',$db);
    }
    function setKey($k)
    {
        $this->_tbl_key = $k;
    }

	function getChangeLogs($reservationId){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('ch.*,u.username, u.name');
		$query->from($db->quoteName('#__hotelreservation_changelog'). ' AS ch');
		$query->leftJoin('#__users u on u.id = ch.user_id');
		$query->where('reservation_id = '.$reservationId);
		$query->order('date desc');
		$db->setQuery((string)$query);
		$result = $db->loadObjectList();
		return $result;
	}

	function getCreatedByUser($reservationId) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('u.username as createdByUsername');
		$query->from($db->quoteName('#__hotelreservation_confirmations'). ' AS r');
		$query->leftJoin('#__users u on u.id = r.user_id');
		$query->where('r.confirmation_id = '.$reservationId);
		$db->setQuery((string)$query,0,1);
		$result = $db->loadObject();
		return $result;
	}

}