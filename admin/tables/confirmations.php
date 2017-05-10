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

class TableConfirmations extends JTable
{
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableConfirmations(& $db) {

		parent::__construct('#__hotelreservation_confirmations', 'confirmation_id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getReservationData($confirmationId){
		$db =JFactory::getDBO();
		$query = 	" SELECT
						c.*,
						r.room_id as reserve_room_id,IF(c.hotel_id=0,ce.hotel_id,c.hotel_id) as hotel_id,r.room_name,
						GROUP_CONCAT( DISTINCT CONCAT(r.adults, '|', r.current) ORDER BY r.current) as total_adults,
						GROUP_CONCAT( DISTINCT CONCAT(r.children, '|', r.current) ORDER BY r.current) as children,
						GROUP_CONCAT( DISTINCT CONCAT(r.juniors, '|', r.current) ORDER BY r.current) as juniors,
						GROUP_CONCAT( DISTINCT CONCAT(r.babies, '|', r.current) ORDER BY r.current) as babies,
						GROUP_CONCAT( DISTINCT CONCAT(r.offer_id, '|', r.room_id, '|', r.current) ORDER BY r.current )	AS items_reserved,
						GROUP_CONCAT( DISTINCT CONCAT(r.offer_id, '|', r.room_id) ORDER BY r.current )	AS room_ids,
						GROUP_CONCAT( DISTINCT CONCAT(r.offer_id, '|', r.room_id, '|', r.current,'|',crp.date,'|',crp.price ) ORDER BY r.current ) as room_prices,
						GROUP_CONCAT( DISTINCT CONCAT(cg.first_name, '|', cg.last_name, '|', cg.identification_number) ORDER BY cg.id) AS guestDetails,
						GROUP_CONCAT( DISTINCT CONCAT(eo.offer_id, '|', eo.room_id, '|', eo.current, '|', eo.extra_option_id, '|', 1,'|', eo.extra_option_persons,'|', eo.extra_option_days,'|', eo.extra_option_dates,'|', ifnull(eo.extra_option_multiplier,'') )ORDER BY r.current  SEPARATOR ';') AS extraOptionIds,
						GROUP_CONCAT( DISTINCT CONCAT(at.airport_transfer_type_id, '|', at.room_id, '|', at.airline_id, '|', at.airline_name, '|', at.current, '|', at.airport_transfer_flight_nr, '|', at.airport_transfer_date,'|', at.airport_transfer_time_hour,'|', at.airport_transfer_time_min,'|', at.airport_transfer_guest , '|', at.included_offer) ORDER BY r.current )	AS airportTransfers,
						GROUP_CONCAT( DISTINCT CONCAT(ce.excursion_id,'_',ce.nr_booked, '_', cep.dateBooked, '_', cep.nrBooked) ORDER BY ce.confirmation_excursion_id)			AS excursions,
						cp.amount as totalPaid,		
						s.status_reservation_name				AS status_reservation_name,
						s.order									AS status_order,
						s.is_modif								AS status_is_modif,
						cp.payment_method					
						FROM #__hotelreservation_confirmations c
						LEFT JOIN #__hotelreservation_confirmations_rooms 						r	ON ( r.confirmation_id 			= c.confirmation_id )
						LEFT JOIN #__hotelreservation_confirmations_room_prices					crp	ON ( r.confirmation_room_id 	= crp.confirmation_room_id )
						LEFT JOIN #__hotelreservation_status_reservation 						s	ON ( c.reservation_status	 	= s.status_reservation_id )
						LEFT JOIN #__hotelreservation_confirmations_extra_options 				eo	ON ( eo.confirmation_id 		= c.confirmation_id )
						LEFT JOIN #__hotelreservation_confirmations_rooms_airport_transfer 		at	ON ( at.confirmation_id 		= c.confirmation_id )
						LEFT JOIN #__hotelreservation_confirmations_guests				 		cg	ON ( cg.confirmation_id 		= c.confirmation_id )
						LEFT JOIN #__hotelreservation_confirmations_payments			 		cp	ON ( cp.confirmation_id 		= c.confirmation_id  and cp.payment_status =".JHP_PAYMENT_STATUS_PAID.")
						LEFT JOIN #__hotelreservation_confirmations_excursions  				ce	ON ( ce.confirmation_id 		= c.confirmation_id )
						LEFT JOIN (SELECT confirmation_excursion_id,min(date) as dateBooked,count(*) as nrBooked from #__hotelreservation_confirmations_excursions_prices group by confirmation_excursion_id) cep	
						ON ( ce.confirmation_excursion_id 	= cep.confirmation_excursion_id )
						WHERE c.confirmation_id = $confirmationId
						GROUP BY c.confirmation_id
						";
		$db->setQuery( $query );
		$data 	=$db->loadObject();
		return $data;
	}
	

	function getHotelMonthlyReservations($hotelId,$startDate, $endDate){
		$db =JFactory::getDBO();
		$query = "select hc.confirmation_id,hc.first_name,hc.last_name,hc.start_date,hc.end_date,hc.voucher,hc.total,
		                 ho.offer_reservation_cost_val,ho.offer_id,ho.offer_commission, hceo.extra_commission, hceo.extra_option_price
				 from #__hotelreservation_confirmations hc
				 inner join #__hotelreservation_confirmations_rooms hcr  on hcr.confirmation_id = hc.confirmation_id
				 left join  #__hotelreservation_offers ho on ho.offer_id = hcr.offer_id
				 left join #__hotelreservation_confirmations_extra_options as hceo on hceo.confirmation_id =  hc.confirmation_id
				 where hc.hotel_id=$hotelId and hc.end_date>='$startDate' and hc.end_date<='$endDate' and hc.reservation_status <> ".CANCELED_ID."
				 group by hc.confirmation_id
		";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getByUserId($userId){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_confirmations where user_id=$userId";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getClientReservations($userId){
		$db =JFactory::getDBO();
		$query = "		SELECT 
							c.*,  
							s.status_reservation_name,
							s.is_modif,
							GROUP_CONCAT( DISTINCT r.room_id) AS room_ids,
							pp.name as payment_processor,
							p.payment_status, 
							p.confirmation_payment_id,
							h.hotel_name AS hotel_name
						FROM #__hotelreservation_confirmations c 
						INNER JOIN #__hotelreservation_status_reservation				s	on (c.reservation_status=s.status_reservation_id)
						INNER JOIN #__hotelreservation_confirmations_rooms				r	USING(confirmation_id)
				        INNER JOIN #__hotelreservation_hotels h	ON ( h.hotel_id = r.hotel_id )
						LEFT JOIN #__hotelreservation_confirmations_payments			p	USING(confirmation_id) 
						LEFT JOIN #__hotelreservation_payment_processors pp	ON ( pp.type = p.processor_type )
						WHERE c.user_id=$userId
						GROUP BY c.confirmation_id 
						ORDER BY c.confirmation_id DESC";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	function getReservationsIncome($reportType,$hotelId,$roomTypeId,$dateStart,$dateEnd){
		$whereCond = "";
		if(isset($roomTypeId) && $roomTypeId>0){
			$whereCond.=" and r.room_id = $roomTypeId";
		}
		$dateStart =  date_format(new DateTime($dateStart),'Y-m-d');
		$dateEnd =  date_format(new DateTime($dateEnd),'Y-m-d');
		
		$db =JFactory::getDBO();
		$query = "		SELECT 
							sum(p.amount) reservationTotal,
							(CASE '$reportType' WHEN 'DAY' then  p.payment_date
				                        WHEN 'WEEK' then  concat('W',Week(p.payment_date))
				                        WHEN 'MONTH' then concat(Month(p.payment_date),'-','01','-',Year(p.payment_date))
				                        WHEN 'YEAR' then  concat(Year(p.payment_date))
				            END) as groupUnit
						FROM #__hotelreservation_confirmations c 
						INNER JOIN #__hotelreservation_confirmations_rooms				r	USING(confirmation_id)
						LEFT JOIN 
						(
							SELECT 
								p.*
							from #__hotelreservation_confirmations_payments	p	
							where p.payment_status = ".JHP_PAYMENT_STATUS_PAID."
							and p.payment_date is not null
                            and p.payment_date between '$dateStart' and '$dateEnd'
						) 																p 	USING(confirmation_id)
						LEFT JOIN #__hotelreservation_hotels			 				h	ON ( h.hotel_id 					= c.hotel_id )	
						WHERE h.hotel_id= $hotelId
						$whereCond
						GROUP BY groupUnit
						ORDER BY groupUnit asc";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}

	function getReservationsOffers($hotelId,$roomTypeId,$dateStart,$dateEnd,$voucher){


		$whereCond = "";
		if(isset($roomTypeId) && $roomTypeId>0){
			$whereCond.=" and cr.room_id = $roomTypeId";
		}
		
		if(isset($voucher) && $voucher !=''){
			$whereCond.= " and c.voucher = ".$voucher;
		}

		$dateStart =  date_format(new DateTime($dateStart),'Y-m-d');
		$dateEnd =  date_format(new DateTime($dateEnd),'Y-m-d');

		$whereDateCond = '';
		if(!empty($dateStart) && !empty($dateEnd)){
			$whereDateCond.="  and p.payment_date between '$dateStart' and '$dateEnd' ";
		}else if(!empty($dateStart)){
			$whereDateCond.=" and p.payment_date >='$dateStart' ";
		}else if(!empty($dateEnd)){
			$whereDateCond.=" and p.payment_date <='$dateEnd' ";
		}
		if((!empty($dateStart) || !empty($dateEnd)) && !empty($hotelId))
		{
			$db = JFactory::getDBO();
			$query = "SELECT
					    cr.offer_id,
					    o.offer_name,
					    cr.room_name,
					    cr.hotel_id,
					    sum((cr.adults + cr.children)) as persons,	
					    count(c.total) as nrBookings,
					    p.currency,
					    hc.currency_symbol,
					    sum(c.total) AS reservation_amount,
					    sum(p.amount) AS amount_paid
					FROM
					    #__hotelreservation_confirmations c
					        LEFT JOIN
					    #__hotelreservation_confirmations_rooms cr ON cr.confirmation_id = c.confirmation_id and cr.offer_id > 0
					        INNER JOIN
					    #__hotelreservation_confirmations_payments p on p.confirmation_id = c.confirmation_id and p.payment_status = " . JHP_PAYMENT_STATUS_PAID . $whereDateCond . "
					        INNER JOIN
					    #__hotelreservation_offers o ON o.offer_id = cr.offer_id
					        LEFT JOIN 
					    #__hotelreservation_currencies as hc on hc.description = p.currency
					    WHERE cr.hotel_id = ". $hotelId ."
					    $whereCond
					GROUP BY cr.offer_id , cr.room_id 
					ORDER BY cr.offer_id ASC";

			$db->setQuery( $query );
			$result = $db->loadObjectList();

			return $result;
		}
	}
	
	function getReservationsCountries($reportType,$hotelId,$roomTypeId,$dateStart,$dateEnd){
		$whereCond = "";
		$dateStart =  date_format(new DateTime($dateStart),'Y-m-d');
		$dateEnd =  date_format(new DateTime($dateEnd),'Y-m-d');
						
		if(isset($roomTypeId) && $roomTypeId>0){
			$whereCond.=" and r.room_id = $roomTypeId";
		}
		
		$db =JFactory::getDBO();
		$query = "			SELECT
								c.country,
								count(*) as countryCount
							FROM #__hotelreservation_confirmations c 
							INNER JOIN #__hotelreservation_confirmations_rooms				r	USING(confirmation_id)
							LEFT JOIN #__hotelreservation_hotels			 				h	ON ( h.hotel_id 					= r.hotel_id )	
							WHERE c.hotel_id= '$hotelId'	
							and c.start_date between '$dateStart' and '$dateEnd'
							$whereCond
							GROUP BY c.country
							ORDER BY c.country asc";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}
	
	function getReservationsReport($reportType,$dayLag){
		$whereCond = "";
		$dayDiff = "-".$dayLag.' day';
		$dateStart =  date('Y-m-d',(strtotime ($dayDiff)));
		$dateEnd =  date_format(new DateTime('NOW'),'Y-m-d');
		
		$db =JFactory::getDBO();
		$query = "			SELECT
								count(c.confirmation_id) reservationTotal,
								(CASE '$reportType' WHEN 'DAY' then concat(Month(c.created),'-',Day(c.created),'-',Year(c.created))
				                        WHEN 'WEEK' then  concat('W',Week(c.created))
				                        WHEN 'MONTH' then concat(Month(c.created),'-','01','-',Year(c.created))
				                        WHEN 'YEAR' then  concat(Year(c.created))
				            	END) as groupUnit
								FROM #__hotelreservation_confirmations c
								left JOIN #__hotelreservation_confirmations_rooms				r	USING(confirmation_id)
								left JOIN #__hotelreservation_confirmations_excursions			e	USING(confirmation_id)
								LEFT JOIN #__hotelreservation_hotels			 				h	ON ( h.hotel_id 					= r.hotel_id )	
								where c.created between '$dateStart' and '$dateEnd'
								$whereCond
								GROUP BY groupUnit
								ORDER BY groupUnit asc";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}
	
	
	
	function getReviewsToSend(){
		$db =JFactory::getDBO();
		$query = "			 SELECT
								c.*,DATEDIFF( CURDATE(),date(c.start_date)) daysAfterCheckout
								FROM #__hotelreservation_confirmations c 
								INNER JOIN #__hotelreservation_status_reservation s	on c.reservation_status = s.status_reservation_id
								WHERE c.reservation_status !=2
			                	and (c.review_email_date IS NULL or c.review_email_date='')
			                	and DATEDIFF( CURDATE(),date(c.end_date))>3
							GROUP BY c.confirmation_id 
							ORDER BY c.confirmation_id DESC";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}
	
	function setStatus($reservationId, $status){
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__hotelreservation_confirmations SET reservation_status = $status  WHERE confirmation_id = ".$reservationId ;
		$db->setQuery($query);
		return $db->query();
	}
	
	function resetCubilisStatus($reservationId){
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__hotelreservation_confirmations SET cubilis_status = IF(cubilis_status=".CUBILIS_RESERVATION_SENT.",".CUBILIS_RESERVATION_MODIFIED.",".CUBILIS_RESERVATION_NEW.") WHERE confirmation_id = ".$reservationId ;
		$db->setQuery($query);
		return $db->query();
	}
	
	function updateCancelationComments($reservationId, $cancellationNotes){
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__hotelreservation_confirmations SET cancellation_notes = '$cancellationNotes'  WHERE confirmation_id = ".$reservationId ;
		$db->setQuery($query);
		return $db->query();
	}
	
	

	function getCubilisReservations($hotelId, $limit){
		$db =JFactory::getDBO();
		
		$query = "select c.*, GROUP_CONCAT(eo.extra_option_name,'||',eo.extra_option_price,'||',eo.extra_option_price_type,'||',eo.extra_option_is_per_day,'||',eo.extra_option_persons,'||',eo.extra_option_days ,'||',eo.extra_room_name separator '#') as extra_option_details
				 from  #__hotelreservation_confirmations c
				 left join (
				  			select eo.extra_option_name,eo.confirmation_id,eo.extra_option_price,eo.extra_option_price_type,eo.extra_option_is_per_day,eo.extra_option_persons, eo.extra_option_days,hr.room_name as extra_room_name
							from #__hotelreservation_confirmations_extra_options eo
							left join #__hotelreservation_rooms hr on hr.room_id = eo.room_id
				  ) eo ON eo.confirmation_id = c.confirmation_id
				 left join  #__hotelreservation_confirmations_payments cp on cp.confirmation_id = c.confirmation_id
				 where c.cubilis_status <> ".CUBILIS_RESERVATION_SENT." and c.hotel_id = $hotelId and (c.reservation_status <> ".CANCELED_ID." or (c.reservation_status = ".CANCELED_ID." and c.cubilis_status = ".CUBILIS_RESERVATION_MODIFIED."))  and (cp.payment_status = ".JHP_PAYMENT_STATUS_PAID." or cp.payment_status = ".JHP_PAYMENT_STATUS_WAITING.")
				 group by c.confirmation_id
			";
		$db->setQuery($query,0,$limit);
		$reservations =  $db->loadObjectList();
		
		if(!empty($reservations)){
			foreach($reservations as &$reservation){
				$query = "select distinct r.*, ho.offer_name,hor.price_type_day,hor.offer_rate_id,hrr.id as rate_id
						 from #__hotelreservation_confirmations_rooms r
						 left join #__hotelreservation_offers ho on ho.offer_id = r.offer_id
						 left join (
								select r.confirmation_room_id,hor.price_type_day,hor.id as offer_rate_id
						                 from #__hotelreservation_confirmations_rooms r 
						                 inner join #__hotelreservation_offers_rates hor  on r.room_id = hor.room_id
						                 inner join #__hotelreservation_offers_rates hort  on r.offer_id = hor.offer_id
										 where r.confirmation_id = ".$reservation->confirmation_id."
						) hor on r.confirmation_room_id = hor.confirmation_room_id
						 left join #__hotelreservation_rooms_rates hrr on r.room_id = hrr.room_id
						 where r.confirmation_id = ".$reservation->confirmation_id."
				         and r.confirmation_room_id is not null" ;
				$db->setQuery($query);
				$reservation->rooms = $db->loadObjectList();
				foreach($reservation->rooms as &$room){
					$query = "select * from #__hotelreservation_confirmations_room_prices where confirmation_room_id = '".$room->confirmation_room_id."'";
					$db->setQuery($query);
					$room->prices = $db->loadObjectList();
				}
			}
		}
		
		return $reservations;
	}
	
	function setReservationCubilisStatus($reservations){
		$db =JFactory::getDBO();
		$result = true;
		
		if(is_array($reservations) && count($reservations)){
			$reservationIds="(";
			foreach($reservations as $reservation){
				$reservationIds .= $reservation->confirmation_id.",";
			}
			$reservationIds =substr($reservationIds, 0, -1);
			$reservationIds.=")";
			
			$query = " UPDATE #__hotelreservation_confirmations SET cubilis_status = ".CUBILIS_RESERVATION_SENT."  WHERE confirmation_id in ".$reservationIds ;
			$db->setQuery($query);
			$result =  $db->query();
		}
		
		return $result;
	}
	
	function getBeds24Reservations($hotelId, $limit){
		$db =JFactory::getDBO();
	
		$query = "select c.*,cp.*, GROUP_CONCAT(eo.extra_option_name,'||',eo.extra_option_price,'||',eo.extra_option_price_type,'||',eo.extra_option_is_per_day,'||',eo.extra_option_persons,'||',eo.extra_option_days ,'||',eo.extra_room_name separator '#') as extra_option_details
				 from  #__hotelreservation_confirmations c
				 left join (
				  			select eo.extra_option_name,eo.confirmation_id,eo.extra_option_price,eo.extra_option_price_type,eo.extra_option_is_per_day,eo.extra_option_persons, eo.extra_option_days,hr.room_name as extra_room_name
							from #__hotelreservation_confirmations_extra_options eo
							left join #__hotelreservation_rooms hr on hr.room_id = eo.room_id
				  ) eo ON eo.confirmation_id = c.confirmation_id
				 left join  #__hotelreservation_confirmations_payments cp on cp.confirmation_id = c.confirmation_id
				 where c.beds24_status <> ".CUBILIS_RESERVATION_SENT." and c.hotel_id = $hotelId and (c.reservation_status <> ".CANCELED_ID." or (c.reservation_status = ".CANCELED_ID." and c.beds24_status = ".CUBILIS_RESERVATION_MODIFIED."))  and (cp.payment_status = ".JHP_PAYMENT_STATUS_PAID." or cp.payment_status = ".JHP_PAYMENT_STATUS_WAITING.")
				 and c.start_date >= NOW()		
				 group by c.confirmation_id
			";
		$db->setQuery($query,0,$limit);
		$reservations =  $db->loadObjectList();
		if(!empty($reservations)){
			foreach($reservations as &$reservation){
				$query = "select distinct r.*, ho.offer_name,hor.price_type_day,hor.offer_rate_id,hrr.id as rate_id,rr.beds24_room_id
						 from #__hotelreservation_confirmations_rooms r
						 inner join #__hotelreservation_rooms rr on r.room_id = rr.room_id
						 left join #__hotelreservation_offers ho on ho.offer_id = r.offer_id
						 left join (
								select r.confirmation_room_id,hor.price_type_day,hor.id as offer_rate_id
						                 from #__hotelreservation_confirmations_rooms r
						                 inner join #__hotelreservation_offers_rates hor  on r.room_id = hor.room_id
						                 inner join #__hotelreservation_offers_rates hort  on r.offer_id = hor.offer_id
										 where r.confirmation_id = ".$reservation->confirmation_id."
						) hor on r.confirmation_room_id = hor.confirmation_room_id
						 left join #__hotelreservation_rooms_rates hrr on r.room_id = hrr.room_id
						 where r.confirmation_id = ".$reservation->confirmation_id."
				         and r.confirmation_room_id is not null and rr.beds24_room_id is not null" ;
				$db->setQuery($query);
				$reservation->rooms = $db->loadObjectList();
				foreach($reservation->rooms as &$room){
					$query = "select * from #__hotelreservation_confirmations_room_prices where confirmation_room_id = '".$room->confirmation_room_id."'";
					$db->setQuery($query);
					$room->prices = $db->loadObjectList();
				}
			}
		}
	
		return $reservations;
	}
	
	function setReservationBeds24Status($reservations){
		$db =JFactory::getDBO();
		$result = true;
	
		if(is_array($reservations) && count($reservations)){
			$reservationIds="(";
			foreach($reservations as $reservation){
				$reservationIds .= $reservation->confirmation_id.",";
			}
			$reservationIds =substr($reservationIds, 0, -1);
			$reservationIds.=")";
				
			$query = " UPDATE #__hotelreservation_confirmations SET beds24_status = ".CUBILIS_RESERVATION_SENT."  WHERE confirmation_id in ".$reservationIds ;
			$db->setQuery($query);
			$result =  $db->query();
		}
	
		return $result;
	}

	
	function getReservationList($startDate, $endDate){
		$db =JFactory::getDBO();
		$query = "select c.*, h.hotel_id, h.hotel_name, h.email as hotel_email, count(cr.room_name) as number_rooms,  GROUP_CONCAT(of.offer_name) as offer_names , GROUP_CONCAT(cr.room_name) as room_names 
				from  #__hotelreservation_confirmations c
				left join #__hotelreservation_confirmations_rooms cr on cr.confirmation_id = c.confirmation_id
				left join #__hotelreservation_offers of on cr.offer_id = of.offer_id 
				left join #__hotelreservation_hotels h on h.hotel_id = c.hotel_id 
				where c.start_date between '$startDate' and '$endDate' and c.reservation_status <> ".CANCELED_ID."
				group by c.confirmation_id
				order by c.hotel_id ";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    function getMonthReservations($userOwnedHotelIds){
        $db =JFactory::getDBO();
        $query = "select count(c.confirmation_id) as cid from `#__hotelreservation_confirmations` c
                  LEFT JOIN `#__hotelreservation_hotels` h	ON ( h.hotel_id = c.hotel_id )
                  WHERE month(created) = month(now())
				  AND FIND_IN_SET( h.hotel_id,'".$userOwnedHotelIds."')";
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result->cid;
    }

    function getTotalReservations($userOwnedHotelIds){
        $db =JFactory::getDBO();
        $query = "SELECT count(c.confirmation_id) as r FROM `#__hotelreservation_confirmations` c
                  LEFT JOIN `#__hotelreservation_hotels` h	ON ( h.hotel_id = c.hotel_id )
				  WHERE FIND_IN_SET( h.hotel_id,'".$userOwnedHotelIds."')";
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result->r;
    }
    function getMonthlyIncomeReservations($currency,$userOwnedHotelIds){
        $db =JFactory::getDBO();
        $query = "		SELECT
							sum(p.amount) as reservationMonthly
						FROM #__hotelreservation_confirmations c
						INNER JOIN #__hotelreservation_confirmations_rooms r	USING(confirmation_id)
						LEFT JOIN
						(
							SELECT
								p.*
							from #__hotelreservation_confirmations_payments	p
							where p.payment_status = ".JHP_PAYMENT_STATUS_PAID."
							and p.payment_date is not null
                            and MONTH(p.payment_date) = MONTH(NOW())
                            and p.currency = '".$currency."'
							) 																p 	USING(confirmation_id)
						LEFT JOIN `#__hotelreservation_hotels`		h	ON ( h.hotel_id = c.hotel_id )
						WHERE FIND_IN_SET( h.hotel_id,'".$userOwnedHotelIds."')";
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result->reservationMonthly;
    }

    function getTotalIncomeReservations($currency,$userOwnedHotelIds){
        $db =JFactory::getDBO();
        $query = "		SELECT
							sum(p.amount) as amount
						FROM #__hotelreservation_confirmations c
						INNER JOIN #__hotelreservation_confirmations_rooms r	USING(confirmation_id)
						LEFT JOIN
						(
							SELECT
								p.*
							from #__hotelreservation_confirmations_payments	p
							where p.payment_status = ".JHP_PAYMENT_STATUS_PAID."
							AND p.currency = '".$currency."'
							and p.payment_date is not null
							)p	USING(confirmation_id)
						LEFT JOIN `#__hotelreservation_hotels` h	ON ( h.hotel_id = c.hotel_id )
						WHERE FIND_IN_SET( h.hotel_id,'".$userOwnedHotelIds."')";
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result->amount;
    }

    function getBookedOffers($userOwnedHotelIds,$monthly=false){
        $db =JFactory::getDBO();
        $monthlyFilter = "";
        if($monthly)
        	$monthlyFilter =  " and month(hc.created) = month(now()) ";
        $query= "select count(*) as offers from  #__hotelreservation_confirmations hc
                  inner join #__hotelreservation_confirmations_rooms hcr using(confirmation_id)
                  inner join  #__hotelreservation_offers ho on (ho.offer_id = hcr.offer_id)
                  left join #__hotelreservation_confirmations_payments hcp on (hc.confirmation_id = hcp.confirmation_id)
                  left join `#__hotelreservation_hotels` h	ON ( h.hotel_id = hc.hotel_id )
                  where 1  $monthlyFilter
                  and FIND_IN_SET( h.hotel_id,'".$userOwnedHotelIds."') and hcp.payment_status=".JHP_PAYMENT_STATUS_PAID;
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result->offers;
    }

    /**
     * @param $curency the default currency being used
     * @return mixed return only the currencies that exists and are not the default one
     */
    function getCurrencies($curr){
        $db=JFactory::getDBO();
        $query = "select p.currency as description from #__hotelreservation_confirmations_payments p where p.currency not like '".$curr."' group by p.currency";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    /**
     * @param $userId takes the logged in user Id and selectes it latest guest details data from its last reservation
     * @return mixed a object with the guest details data for an user id
     */
    function getLoginUserData($userId){
        $db=JFactory::getDBO();
        $query = "SELECT confirmation_id,user_id,guest_type, first_name,last_name,address,city,state_name,country,postal_code,phone, email,conf_email,postal_code, company_name,phone,remarks  FROM #__hotelreservation_confirmations where user_id = ".(int)$userId ." order by confirmation_id desc limit 1";
        $db->setQuery($query);
        $result = $db->loadObject();

        if(!empty($result->confirmation_id) && isset($result->confirmation_id)) {
            $query = "SELECT first_name,last_name,identification_number FROM #__hotelreservation_confirmations_guests where confirmation_id=" . $result->confirmation_id;

            $db->setQuery($query);
            $result->guestDetails = $db->loadObjectList();
        }
        return $result;
    }

	function getReservationExportData(){

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select all fields from the table.
		$query->select('c.confirmation_id,c.hotel_id,c.discount_code');
		$query->from($db->quoteName('#__hotelreservation_confirmations').' AS c');

		$query->select('h.hotel_name,h.commission,hi.city_tax,hi.city_tax_percent');
		$query->join('LEFT', '#__hotelreservation_hotels AS h ON c.hotel_id=h.hotel_id');
		$query->join('LEFT', '#__hotelreservation_hotel_informations as hi on h.hotel_id = hi.hotel_id');
		// reservation items
		//rooms  , offers -> to be changed with multilingual name with confirmation language tag
		$query->select('croom.room_name,croom.room_id,croom.offer_id,croom.hotel_id,off.offer_name');
		$query->join('LEFT', '#__hotelreservation_confirmations_rooms as croom on c.confirmation_id = croom.confirmation_id');
		$query->join('LEFT', '#__hotelreservation_offers as off on croom.offer_id = off.offer_id');

		//airport_transfer_type
		$query->select('crat.airport_transfer_type_id,arp.airport_transfer_type_name');
		$query->join('LEFT', '#__hotelreservation_confirmations_rooms_airport_transfer as crat on c.confirmation_id = crat.confirmation_id');
		$query->join('LEFT', '#__hotelreservation_airport_transfer_types as arp on crat.airport_transfer_type_id = arp.airport_transfer_type_id');

		// extra option to be changed with multilingual name
		$query->select('GROUP_CONCAT(distinct ceo.extra_option_id, "|" , ext.name) as extra_option_id');
		$query->join('LEFT', '#__hotelreservation_confirmations_extra_options as ceo on c.confirmation_id = ceo.confirmation_id');
		$query->join('LEFT', '#__hotelreservation_extra_options as ext on ceo.extra_option_id = ext.id');

		$query->group('c.confirmation_id');

		// Add the list ordering clause.
		$query->order('c.confirmation_id');

		$db->setQuery( $query );
		$result = $db->loadObjectList();
		return $result;
	}

	public function delete($pk = null, $children = false)
	{

		$db = JFactory::getDbo();

		$this->deleteReservationData($pk);

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_confirmations");
		$query->where('confirmation_id = ' . (int)$pk);
		$db->setQuery($query);
		$db->execute();



		return parent::delete($pk, $children);
	}


	protected function deleteReservationData($pk = null, $children = false){
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_confirmations_payments");
		$query->where('confirmation_id = ' . (int)$pk);
		$db->setQuery($query);
		$db->execute();


		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_confirmations_guests");
		$query->where('confirmation_id = ' . (int)$pk);
		$db->setQuery($query);
		$db->execute();


		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_confirmations_rooms_airport_transfer");
		$query->where('confirmation_id = ' . (int)$pk);
		$db->setQuery($query);
		$db->execute();


		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_confirmations_extra_options");
		$query->where('confirmation_id = ' . (int)$pk);
		$db->setQuery($query);
		$db->execute();


		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_confirmations_discounts");
		$query->where('reservation_id = ' . (int)$pk);
		$db->setQuery($query);
		$db->execute();



		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_confirmations_taxes");
		$query->where('confirmation_id = ' . (int)$pk);
		$db->setQuery($query);
		$db->execute();

		$query = $db->getQuery(true);
		$query->select('confirmation_room_id');
		$query->from("#__hotelreservation_confirmations_rooms");
		$query->where('confirmation_id = '. (int)$pk);

		//if to one reservation is one or more room booked
		$db->setQuery( $query );
		$result = $db->loadObjectList();
		foreach($result as $item){
			$query = $db->getQuery(true);
			$query->delete();
			$query->from("#__hotelreservation_confirmations_room_prices");
			$query->where('confirmation_room_id = ' . (int)$item->confirmation_room_id);
			$db->setQuery($query);
			$db->execute();
		}

		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_confirmations_rooms");
		$query->where('confirmation_id = ' . (int)$pk);
		$db->setQuery($query);
		$db->execute();


		$query = $db->getQuery(true);
		$query->select('confirmation_excursion_id');
		$query->from("#__hotelreservation_confirmations_excursions");
		$query->where('confirmation_id = '. (int)$pk);

		//if to one reservation is one or more room booked
		$db->setQuery( $query );
		$result = $db->loadObjectList();
		foreach($result as $item){
			$query = $db->getQuery(true);
			$query->delete();
			$query->from("#__hotelreservation_confirmations_excursions_prices");
			$query->where('confirmation_excursion_id = ' . (int)$item->confirmation_room_id);
			$db->setQuery($query);
			$db->execute();
		}

		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_confirmations_excursions");
		$query->where('confirmation_id = ' . (int)$pk);
		$db->setQuery($query);
		$db->execute();
	}
}
