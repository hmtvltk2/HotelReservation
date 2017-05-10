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
defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableOffersExtraOptions extends JTable
{

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct($db){

        parent::__construct('#__hotelreservation_offers_extra_options', 'offers_extra_option_id', $db);
    }

    function setKey($k)
    {
        $this->_tbl_key = $k;
    }

    function deleteOfferExtraOptions($offerId){
        $db =JFactory::getDBO();
        $query = " delete from #__hotelreservation_offers_extra_options where offer_id = $offerId";
        $db->setQuery( $query );
        return $db->query();
    }

    function getOfferExtraOptions($offerId){
        if((int)$offerId>0 || isset($offerId)){
            $db = JFactory::getDBO();
            $query = "SELECT GROUP_CONCAT(extra_option_id) AS extraOptionIds FROM `#__hotelreservation_offers_extra_options` WHERE offer_id =" . $offerId . " GROUP BY offer_id";
            $db->setQuery($query);
            return $db->loadObject();
        }
    }


    function getOfferExtraOptionsIncluded($offerId,$startDate,$endDate){

        $query = "SELECT heo.id,heo.name FROM `#__hotelreservation_extra_options` as heo inner join
                    `#__hotelreservation_offers_extra_options` as oex on heo.id = oex.extra_option_id
                    where oex.offer_id = $offerId and heo.status = 1 and
                     					IF(
					heo.start_date <> '0000-00-00'
					AND
					heo.end_date <> '0000-00-00',
					('".$startDate."' BETWEEN heo.start_date  AND heo.end_date) and  ('".$endDate."' BETWEEN heo.start_date  AND heo.end_date),
					If(
						heo.start_date = '0000-00-00'
						AND
						heo.end_date <> '0000-00-00',
						'".$endDate."' < heo.end_date,
						if(
							heo.start_date <> '0000-00-00'
							AND
							heo.end_date = '0000-00-00',
							'".$startDate."' > heo.start_date ,
							1
							)
						)
					)
					group by heo.id";
        $this->_db->setQuery( $query );

        return $this->_db->loadObjectList();
    }

}