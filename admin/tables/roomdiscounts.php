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

class JTableRoomDiscounts extends JTable
{

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db) {

        parent::__construct('#__hotelreservation_discounts', 'discount_id', $db);
    }

    function setKey($k)
    {
        $this->_tbl_key = $k;
    }


    /**
     * Method to build an SQL query to change the state of one item based on its:
     * @param $discountId
     * @return bool True if the query is executed and the state is changed
     */
    function state($discountId)
    {
        $db= JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->update($db->quoteName('#__hotelreservation_discounts'));
        $query->set('is_available = IF(is_available, 0, 1)');
        if(is_numeric($discountId)) {
            $query->where('discount_id=' . (int)$discountId);
        }
        $db->setQuery((string)$query);

        if (!$db->query())
        {
            return false;
        }
        return true;
    }

    /**
     * Method to build a sql query to get the datas of one Item based on its Id:
     * @param $ItemId
     * @return mixed returns the Object data of  a Discount
     */
    function getData($ItemId)
    {
        $db= JFactory::getDBO();
        $query = $db->getQuery(true);
        //Get All fields from the table
        $query->select('d.*,group_concat(r.room_name order by r.room_name) as discount_rooms');
        $query->from($db->quoteName('#__hotelreservation_discounts'). ' AS d');

        $query->join('LEFT', $db->quoteName('#__hotelreservation_rooms'). ' AS r ON FIND_IN_SET(r.room_id,d.discount_room_ids)');
        if(is_numeric($ItemId)) {
            $query->where('discount_id =' . (int)$ItemId);
        }
        $query->group('discount_id');

        $db->setQuery((string)$query);
        $roomDiscount = $db->loadObject();

        return $roomDiscount;

    }

    /**
     * Method to build a sql query to get the Room Items that are included in one discount based on its:
     * @param $hotelid determine which hotel the room items belongs to
     * and
     * @param $discountid determine which discount the room items belongs to
     * @return string
     */
    function getitemRoomsDiscounts($hotelid,$discountid)
    {
        $query = " 	SELECT
						r.room_id,
						r.room_name	,
						IF( ISNULL(d.discount_id), 0, 1)		AS is_sel
					FROM #__hotelreservation_rooms r
					LEFT JOIN
					(
						SELECT * FROM #__hotelreservation_discounts WHERE discount_id = ".(int)$discountid. "
					) d ON FIND_IN_SET(r.room_id, d.discount_room_ids)
					WHERE r.is_available = 1 AND r.hotel_id  = ".(int)$hotelid."
					";
       return $query;
    }


    /**
     * Method to build an SQL query to get all offer Room datas for one discount
     * @param $discountid
     * @return string
     */

    function getOfferRoomDiscounts($hotel_id,$discountid)
    {
        $query = "
		 		select  o.offer_id, o.offer_name
		 		from #__hotelreservation_rooms r
				inner join #__hotelreservation_offers_rooms 			hor 	ON hor.room_id	 	= r.room_id
				inner join #__hotelreservation_offers		 			o 		ON hor.offer_id 	= o.offer_id
				where FIND_IN_SET(r.room_id,
				(
				 SELECT discount_room_ids FROM #__hotelreservation_discounts WHERE discount_id = ".(int)$discountid."
				)) and r.hotel_id =".$hotel_id."  and r.is_available =1 and o.is_available = 1 group by o.offer_id
			";

        return $query;

    }

    /**
     * Method to build an SQL query to get the excursions data for a discount and hotel based on their respective Ids:
     * @param $hotelid
     * and
     * @param $discountid
     * @return string
     */
    function getExcursions($hotelid,$discountid)
    {
            $query = " 	SELECT
						r.id,
						r.name as excursion_name	,
						IF( ISNULL(d.discount_id), 0, 1)		AS is_sel
					FROM #__hotelreservation_excursions r
					LEFT JOIN
					(
						SELECT * FROM #__hotelreservation_discounts WHERE discount_id = " . (int)$discountid . "
					) d ON FIND_IN_SET(r.id, d.excursion_ids)
					WHERE r.is_available = 1";
        return $query;
    }

    /**
     * Method to build an SQL query to get the html content of offers for rooms based on their param roomId:
     * @param $roomIds
     * @return string
     */
    function getHTMLContentOffersQuery($roomIds)
    {
            $db= JFactory::getDBO();
            $query = $db->getQuery(true);
            //Get All fields from the table
            $query->select('o.offer_id, o.offer_name');
            $query->from($db->quoteName('#__hotelreservation_rooms'). ' AS r');

            $query->join('INNER', $db->quoteName('#__hotelreservation_offers_rooms'). ' AS hor ON hor.room_id = r.room_id');
            $query->join('INNER', $db->quoteName('#__hotelreservation_offers'). ' AS o ON hor.offer_id 	= o.offer_id');
            if(is_numeric($roomIds)) {
                $query->where('r.room_id=' . (int)$roomIds);
            }
            $db->setQuery((string)$query);
            return $db->query();

    }

	/**]
	 * @param $hotel_id
	 * @param $roomId
	 * @param $offerId
	 * @param $startDate
	 * @param $endDate
	 * @param $allDiscounts
	 *
	 * @return mixed discount object to apply in the payment and confirmation screen
	 */
    function getReservationDiscountCoupons($hotel_id,$roomId,$offerId,$startDate,$endDate,$totalReservationPrice,$allDiscounts = false) {
    	$discounts = array();
    	
    	if ((!is_numeric( $roomId ) && !is_numeric( $hotel_id )) ||  (!is_numeric( $offerId ) && !is_numeric( $hotel_id )) ) {
    		return $discounts;
    	}
    	
    	$db= JFactory::getDBO();


	    $startDate = JHotelUtil::convertToMysqlFormat($startDate);
	    $endDate   = JHotelUtil::convertToMysqlFormat($endDate);

	    $number_days = (strtotime($endDate) - strtotime($startDate) ) / ( 60 * 60 * 24) ;
	    
	    $roomConditions = "";
	    // default condition to check for rooms and offers
	    if(!empty($offerId))
	    {
	    	$roomConditions .= " AND FIND_IN_SET( " . $offerId . ", offer_ids  ) ";
	    }
	    if(!empty($roomId))
	    {
	    	$roomConditions .= " AND	FIND_IN_SET( " .$roomId . ", discount_room_ids  )";
	    }
	    
	    if(!empty($roomId) && empty($offerId)){
	    	$roomConditions .= " and only_on_offers = 0 ";
	    }

	    $currentDayNr = 1;
	    $condition = "";
	    
	    for( $d = strtotime($startDate);$d < strtotime($endDate); ) {
	    	$query = $db->getQuery(true);
	    	
		    if($allDiscounts){
			    $onlyOffers = '';

		    // condition is used when function is called to get the coupons for hotel offers/rooms
			    if(isset($currentDayNr) && isset($totalReservationPrice))
			    {
				    $condition = " AND IF( minimum_number_days > 0, minimum_number_days <= IF(discount_type = 0, " . $currentDayNr . "," . $number_days . "), 1 ) ";
				    $condition .= " AND IF( maximum_number_days> 0, maximum_number_days >= IF(discount_type = 0," . $currentDayNr . "," . $number_days . "), 1 ) ";
				    $condition .= " AND IF( minimum_amount> 0, minimum_amount <= " . $totalReservationPrice . " , 1 ) ";
			    }

			    $currentDayNr++;
		    }

		    $query->select( 'discount_id,discount_name,discount_value,percent,reservation_cost_discount,check_full_code,price_type,code' );
		    $query->from( $db->quoteName( '#__hotelreservation_discounts' ) );
			$query->where( $db->quoteName( 'is_available' ) . " = 1
			AND '" . date( 'Y-m-d', $d ) . "' BETWEEN discount_datas AND discount_datae
            $roomConditions and " . $db->quoteName( 'hotel_id' ) . "=" . (int) $hotel_id.$condition );
			$db->setQuery( (string) $query );
			$discountsResult = $db->loadObjectList();
			
			//echo $query;
			
			foreach($discountsResult as $discountsResult){
				$discounts[$discountsResult->discount_id] = $discountsResult;
			}
			$d = strtotime(JHotelUtil::shiftDate(date( 'Y-m-d', $d ),1));
		}
		
		return $discounts;
    }

	/**
	 * @param      $adults
	 * @param      $offer | $room object depends where you call the function in this case in the $offer param will be passed the $room param from HotelService::getHotelRooms()
	 * @param      $d -> days generated based on the user search to check if the discount is available on each day
	 * @param      $number_days
	 * @param      $currentDayNr
	 * @param      $totalReservationPrice
	 * @param bool $allOffers  set to true when used inside HotelService::getAllOffers and params  $currentDayNr and $totalReservationPrice are set to null
	 * @param bool $rooms  set to true when used inside HotelService::getHotelRooms
	 *
	 * @return mixed object list of the discounts
	 */
	function getHotelDiscountCoupons($adults,$roomId,$offerId,$date,$number_days,$currentDayNr,$totalReservationPrice,$allOffers = false){
		$db= JFactory::getDBO();
		$query = $db->getQuery(true);

		$roomConditions = "";
		// default condition to check for rooms and offers
		if(!empty($offerId))
		{
			$roomConditions .= " AND FIND_IN_SET( " . $offerId . ", offer_ids  ) ";
		}
		if(!empty($roomId))
		{
			$roomConditions .= " AND	FIND_IN_SET( " .$roomId . ", discount_room_ids  )";
			$roomConditions .= " and only_on_offers = 0 ";		
		}
		
		// condition is used when function is called to get the coupons for hotel offers/rooms
		$condition = "";
		if(isset($currentDayNr))
		{
			$condition = " AND IF( minimum_number_days > 0, minimum_number_days <= IF(discount_type = 0, " . $currentDayNr . "," . $number_days . "), 1 ) ";
			$condition .= " AND IF( maximum_number_days> 0, maximum_number_days >= IF(discount_type = 0," . $currentDayNr . "," . $number_days . "), 1 ) ";
		}
		if(isset($totalReservationPrice))
		{
			$condition .= " AND IF( minimum_amount> 0, minimum_amount <= " . $totalReservationPrice . " , 1 ) ";
		}

		// condition true if functions is used to get coupons for all offers
		if($allOffers)
			$condition = " AND IF( minimum_number_days > 0, minimum_number_days <=". $number_days .", 1 )  ";

		$query->select( 'discount_id,
							hotel_id,
							discount_name,
							discount_datas,
							discount_datae,
							if(price_type = 1 , discount_value * '.$adults.' , discount_value) as discount_value,
							percent,
							discount_room_ids,
							minimum_number_days,
							minimum_number_persons,
							maximum_number_days,
							reservation_cost_discount,
							check_full_code,
							price_type,
							code' );

		    $query->from( $db->quoteName( '#__hotelreservation_discounts' ) );
			$query->where( $db->quoteName( 'is_available' ) . " = 1 $roomConditions
			 AND '" .$date . "' BETWEEN discount_datas AND discount_datae
             AND IF( minimum_number_persons > 0, minimum_number_persons <=". $adults. ", 1 )".$condition);
			$query->order('discount_datas');
			$db->setQuery( (string) $query );

			$discounts = $db->loadObjectList();
			return $discounts;
	}

	/**
	 * Separate Method to not interfere with method @getReservationExportData from Confirmations table and the csv generation
	 * @return array method to get the additional data about discounts for the reservation export to csv
	 */
	function getReservationDiscounts(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('c.confirmation_id,GROUP_CONCAT(d.discount_name,"(" ,d.discount_value ,")"  SEPARATOR "| " ) as discount,d.code,c.discount_code');
        $query->from($db->quoteName('#__hotelreservation_confirmations').' as c');
		$query->join('LEFT' , '#__hotelreservation_confirmations_rooms as croom on c.confirmation_id = croom.confirmation_id');
		$query->join('LEFT' ,'#__hotelreservation_discounts as d on croom.hotel_id = d.hotel_id');
        $query->where('d.is_available');
		$query->where('c.discount_code >"" ');
		$query->where('c.discount_code = d.code');


		$query->group('c.confirmation_id');

		// Add the list ordering clause.
		$query->order('c.confirmation_id');

		$db->setQuery( $query );
		$result = $db->loadObjectList();
		return $result;
	}

}