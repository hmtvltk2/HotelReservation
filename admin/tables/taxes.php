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

class JTableTaxes extends JTable
{

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db) {

        parent::__construct('#__hotelreservation_taxes', 'tax_id', $db);
    }

    function setKey($k)
    {
        $this->_tbl_key = $k;
    }


    /**
     * Method to build an SQL query to change the state of one item based on its:
     * @param $taxId
     * @return bool True if the query is executed and the state is changed
     */
    function state($taxId)
    {
        $db= JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->update($db->quoteName('#__hotelreservation_taxes'));
        $query->set('is_available = IF(is_available, 0, 1)');
        if(is_numeric($taxId)) {
            $query->where('tax_id=' . (int)$taxId);
        }
        $db->setQuery((string)$query);

        if (!$db->query())
        {
            return false;
        }
        return true;
    }
}