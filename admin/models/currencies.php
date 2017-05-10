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

jimport('joomla.application.component.modellist');

class JHotelReservationModelCurrencies extends JModelList
{
    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  string  An SQL query
     *
     * @since   1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //Get All fields from the table
        $query->select($this->getState('list.select', 'hc.*'));
        $query->from($db->quoteName('#__hotelreservation_currencies').'AS hc');

        //Join the currency table with the country table
        $query->select('cs.country_name as country,cs.country_id,cs.country_currency_short as country_short , cs.country_currency as country_currency');
        $query->join('LEFT' , $db->quoteName('#__hotelreservation_countries'). 'AS cs ON cs.country_id = hc.currency_id ');

        $currencyId = $this->getState('filter.currency_id');
        if (is_numeric($currencyId)) {
            $query->where('hc.currency_id ='.(int) $currencyId);
        }
        $query->group('hc.currency_id');


        return $query;
    }

}