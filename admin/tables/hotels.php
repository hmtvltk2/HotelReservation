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

class TableHotels extends JTable
{
	var $hotel_id				= null;
	var $hotel_name				= null;
    var $hotel_alias            = null;
	var $country_id				= null;
	var $hotel_county			= null;
	var $hotel_city				= null;
	var $hotel_website			= null;
	var $hotel_address			= null;
	var $currency_id			= null;
	var $is_available			= null;
	var $hotel_latitude			= null;
	var $hotel_longitude		= null;
	var $hotel_stars			= null;
	var $start_date				= null;
	var $end_date				= null;
	var $ignored_dates			= null;
	var $hotel_rating_score     = null;
	var $featured				= null;
	var $commission				= null;
	var $email					= null;
	var $recommended			= null;
	var $reservation_cost_val	= null;
	var $reservation_cost_proc	= null;
	var $hotel_phone			= null;
	var $hotel_number			= null;
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function Tablehotels(& $db) {

		parent::__construct('#__hotelreservation_hotels', 'hotel_id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}
	function getAllHotels(){
		$query = ' SELECT distinct h.hotel_id,h.hotel_name,h.hotel_alias,h.hotel_city,h.ignored_dates,c.country_name
						FROM #__hotelreservation_hotels h
						LEFT JOIN #__hotelreservation_countries	c USING ( country_id)
						ORDER BY hotel_name,country_name';
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

    function getAllHotelIds(){
        $query = ' SELECT
						h.hotel_id
						FROM #__hotelreservation_hotels h
						LEFT JOIN #__hotelreservation_countries	c USING ( country_id)
						ORDER BY hotel_id';
        $this->_db->setQuery( $query );
        return $this->_db->loadObjectList();
    }

	function getHotel($hotelId){
		$query = "SELECT * FROM #__hotelreservation_hotels h
 				  LEFT JOIN #__hotelreservation_countries c on h.country_id=c.country_id
				  where  h.hotel_id=".$hotelId;
// 		dmp($query);						
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}
	
	//retrieve hotels location(country and state, region)
	function getHotelsLocation(){
		
		$query = "select distinct c.country_name,hotel_county,hr.name as region_name 
				    from #__hotelreservation_hotels h 
					inner join #__hotelreservation_countries c on h.country_id=c.country_id
					inner join #__hotelreservation_hotel_region_relation hrr on h.hotel_id = hrr.hotelId
				    inner join #__hotelreservation_hotel_regions hr on hr.id = hrr.regionId
				    order by c.country_name,h.hotel_county,hr.name
				";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	function getAllHotelsWithoutMonthlyInvoice($startDate, $endDate){
		$query = "select hotel_id,hotel_name, commission, country_id, reservation_cost_val
						FROM #__hotelreservation_hotels h where hotel_id not in 
						(select hotelId from #__hotelreservation_invoices hi 
						where hi.date>='$startDate' and hi.date<='$endDate' )
						order by h.hotel_id";
		//dmp($query);
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * 
	 * @param unknown_type $searchParam
	 * @param  $searchType = 0 - search for all info including the prices based on search params
	 * @param  $searchType = 0 - search only for hotel data excluding the prices, reviews
	 * @return string
	 */
	function getHotelSearchQuery($searchParam, $searchType=0){
		$facilityFilter="";
		$typesFilter="";
		$accommodationTypeFilter="";
		$enviromentFilter="";
		$regionFilter="";
		$themesFilter="";
		$orderBy="";
		$voucherFilter = "";
        $starsFilter= "";
        $nightsBooked = UserDataService::getNrDays();

		if(!empty($searchParam['facilities'])){
			$options = explode(",",trim($searchParam['facilities']));
			foreach ($options as $option){
				$facilityFilter = $facilityFilter. " and FIND_IN_SET('".$option."',facilities) ";

			}
		}

        if(!empty($searchParam['stars'])){
            $starsFilter = " and h.hotel_stars in(".$searchParam['stars'].")";
        }
			
		if(!empty($searchParam['types'])){
			$options = explode(",",trim($searchParam['types']));
			foreach ($options as $option){
				$typesFilter = $typesFilter. " and FIND_IN_SET('".$option."',types) ";
			}
		}
		if(!empty($searchParam['accommodationTypes'])){
			$options = explode(",",trim($searchParam['accommodationTypes']));
			
			foreach ($options as $option){
				$accommodationTypeFilter = $accommodationTypeFilter. " and FIND_IN_SET('".$option."',accommodationTypes) ";
			}
		}
		if(!empty($searchParam['enviroments'])){
			$options = explode(",",trim($searchParam['enviroments']));
			foreach ($options as $option){
				$enviromentFilter = $enviromentFilter. " and FIND_IN_SET('".$option."',enviroments) ";
			}
		}
		if(!empty($searchParam['regions'])){
			$options = explode(",",trim($searchParam['regions']));
			foreach ($options as $option){
				$regionFilter = $regionFilter. " and FIND_IN_SET('".$option."',regions) ";
			}
		}

		
		if(!empty($searchParam['themes'])){
			$options = explode(",",trim($searchParam['themes']));
			$themesFilter = " ";
			foreach ($options as $option){
				$themesFilter = $themesFilter. " and FIND_IN_SET('".$option."',themes) ";
			}
		}

		$roomFilter='';
		if(!empty($searchParam['adults'][0])){
			$adults =  $searchParam['adults'];
			$roomFilter = $roomFilter." inner join ( select distinct r0.hotel_id  from ";
			foreach ($searchParam['adults'] as $idx=>$adultPerRoom){
				$roomFilter = $roomFilter."(select  hotel_id from #__hotelreservation_rooms where 1 and is_available = 1 and max_adults>= $adultPerRoom) r$idx";
				$prevIdx = $idx-1;
				$roomFilter =  ($idx!=0)?$roomFilter."  on r$idx.hotel_id = r$prevIdx.hotel_id ":$roomFilter;				
				$roomFilter =  ($idx!=count($searchParam['adults'])-1)?$roomFilter."  inner join ":$roomFilter;
		
			}
			$roomFilter = $roomFilter.")  as hr on hr.hotel_id = h.hotel_id";
		}
		
		$roomOfferFilter='';
		if(!empty($searchParam['adults'][0])){
			$adults =  $searchParam['adults'];
			$roomOfferFilter = $roomOfferFilter." inner join ( select distinct r0.room_id,r0.room_name from ";
			foreach ($searchParam['adults'] as $idx=>$adultPerRoom){
				$roomOfferFilter = $roomOfferFilter."(select room_id,room_name from #__hotelreservation_rooms where 1 and is_available = 1 and max_adults>= $adultPerRoom) r$idx";
				$prevIdx = $idx-1;
				$roomOfferFilter =  ($idx!=0)?$roomOfferFilter."  on r$idx.room_id = r$prevIdx.room_id ":$roomOfferFilter;
				$roomOfferFilter =  ($idx!=count($searchParam['adults'])-1)?$roomOfferFilter."  inner join ":$roomOfferFilter;
		
			}
			$roomOfferFilter = $roomOfferFilter.")  as rr on ofrr.room_id= rr.room_id";
		}
		
		$offerRestrictionFilter = "and ($nightsBooked between hof.offer_min_nights and hof.offer_max_nights or (hof.offer_max_nights<$nightsBooked and greatest(ofr.extra_night_price_1, ofr.extra_night_price_2, ofr.extra_night_price_3, ofr.extra_night_price_4, ofr.extra_night_price_5, ofr.extra_night_price_6, ofr.extra_night_price_7) >0 ))";

		$cityFilter ="";
		if(!empty($searchParam['city'])){
			$cityFilter = " and h.hotel_city = '".$searchParam['city']."'";
		}
		
		$activeHotelsFilter="";
		$startDate= !empty($searchParam['startDate'])?JHotelUtil::convertToMysqlFormat($searchParam['startDate']):null ;
		$endDate = !empty($searchParam['endDate'])?JHotelUtil::convertToMysqlFormat($searchParam['endDate']):null ; 
		$dayFilter = '';
		$offerCheckDate = JHotelUtil::convertToMysqlFormat(JHotelUtil::shiftDateDown($endDate,1));
		
		if(!empty($searchParam['startDate'])){
			
			$endDateTmp = date("Y-m-d", strtotime("-1 day", strtotime($endDate)));
			$days=$this->getDays($startDate, $endDateTmp);
			
			foreach($days as $day){
				$day = JHotelUtil::convertToFormat($day);
				$dayFilter = $dayFilter." and LOCATE('$day', ignored_dates)=0";
			}
			
			//dmp($dayFilter);
			
			$activeHotelsFilter = " and (h.start_date <= '$startDate' or h.start_date='0000-00-00')and (h.end_date >= '$endDate' or h.end_date='0000-00-00')";
		}
		
		$offerAvailabilityFilter = "and (offer_available=1 or room_available=1)";
		$availabilityFilter = "";
		
		$voucherFilter = " and  (hov.voucher is null or hov.voucher='' or hof.public=1)";
		if(!empty($searchParam['voucher'])){
			$voucher =  $searchParam['voucher'];
			$voucherFilter = " and LOWER(hov.voucher) =LOWER('$voucher')";
			$voucherFilter.= " and hof.offer_datas <= '$startDate' and hof.offer_datae >= '$endDate' ";
			$availabilityFilter = " and (min_offer_price is not null) ";
		}
		$roomAvabilityFilter = "and hh9.totalRoomsAvailable-IFNULL(hc.totalReserved,0)>0";
		
		$regionWhereFilter="";
		$accomodationTypeWhereFilter ="";
		$offerThemesWhereFilter ="";
		$whereClause="";
		if(!empty($searchParam['keyword'])){
			$keyword = $this->_db->escape($searchParam['keyword']);
			$whereClause = $whereClause. " and ((h.hotel_name like '%$keyword%') or (h.hotel_city like '%$keyword%') or (h.hotel_county like '%$keyword%')) ";
			
			if($searchParam['searchType']==JText::_("LNG_CITY")){
				$whereClause = "and h.hotel_city like '%$keyword%'";
			}	
					
			if(empty($searchParam['searchType'])){
				$searchParam['searchType'] = "";
			}

			if($searchParam['searchType']==JText::_("LNG_HOTELS")){
				$whereClause = "and h.hotel_city like '%$keyword%'";
			}
			
			if($searchParam['searchType']==JText::_("LNG_ACCOMMODATION_TYPES")){
				$whereClause = "";
				$accomodationTypeWhereFilter =" and hat.name like '%$keyword%'";
			}
			
			if($searchParam['searchType']==JText::_("LNG_THEMES")){
				$whereClause = "";
				$offerThemesWhereFilter = " and hr.name like '%$keyword%'";
			}
		}
		if(!empty($searchParam['searchId'])){
			if($searchParam['searchType']==JText::_("LNG_PROVINCE_AND_REGION")){
				$whereClause = "";
				$keywordReg = $searchParam['searchId'];
				$regionWhereFilter=" and hr.name like '%$keywordReg%'";
			}
		}
		
		
		if($searchParam["orderBy"]){
			$orderBy = " order by ".$searchParam["orderBy"];
		}
		
		$offerRateDateFilter="";
		$roomRateDateFilter="";
		
		if(!empty($startDate)){
			$roomRateDateFilter = " and hrrp.date between '$startDate' and '$endDate'";
			$offerRateDateFilter= " and orp.date between '$startDate' and '$endDate'";
		}
		
		if(empty($searchParam['apply_search_params'])){
			$roomFilter = "";
			$offerRestrictionFilter = "";
			$roomOfferFilter = "";
			$roomAvabilityFilter = "";
			$offerRestrictionFilter = "";
		}
		
		if(isset($searchParam['showAll']) && $searchParam['showAll'] == 1){
			$availabilityFilter ='';
			$activeHotelsFilter ='';
			$dayFilter='';
			$whereClause='';
			$offerRateDateFilter="";
			$roomRateDateFilter="";
			$offerAvailabilityFilter = "";
			$roomFilter = "";
			$roomOfferFilter = "";
			$offerRestrictionFilter = "";
			$roomAvabilityFilter = "";
		}
		
		if(isset($searchParam['no-dates']) && $searchParam['no-dates'] == 1){
			$availabilityFilter ='';
			$activeHotelsFilter ='';
			$dayFilter='';
			$offerRateDateFilter="";
			$roomRateDateFilter="";
			$offerAvailabilityFilter = "";
			$roomFilter = "";
			$roomOfferFilter = "";
			$offerRestrictionFilter = "";
			$roomAvabilityFilter = "";
		}
		
		$distanceSelect="";
		$havingDistance="";
	
		if(isset($searchParam['nearByHotels'])){
			$whereClause="";
			$regionWhereFilter="";
			$latitude = $searchParam['latitude'];
			$longitude = $searchParam['longitude'];
			$distance = $searchParam['distance'];
			$distanceSelect = "1.609344*3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -abs( h.hotel_latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs(h.hotel_latitude) *  pi()/180) * POWER(SIN(($longitude - h.hotel_longitude) *  pi()/180 / 2), 2) )) as distance ,";
			$havingDistance = " having distance < $distance ";
			
			
			$orderBy = "ORDER BY distance asc";
		}
		$excludeFilter = "";
		if(!empty($searchParam["excludedIds"])){
			$excludeFilter =" and h.hotel_id not in (".$searchParam["excludedIds"].") ";
		}
		
		$priceFilterSql = "";
		if(!empty($searchParam["priceFilter"])){
			$priceFilterSql .= " and (";
			foreach($searchParam["priceFilter"]  as $idx=> $priceFilter){
				$priceFilterSql .= "((IF(hh11.min_offer_price IS NULL OR hh10.room_min_rate IS NULL, COALESCE(hh11.min_offer_price, hh10.room_min_rate), LEAST(hh11.min_offer_price,hh10.room_min_rate))) between ".$priceFilter[0]." and ".$priceFilter[1].")";
				if($idx!=count($searchParam["priceFilter"])-1) 
					$priceFilterSql .= " or ";
			}
			$priceFilterSql .= " )";
		}
		
		$languageTag =  !empty($searchParam['languageTag'])?$searchParam['languageTag']:null;
		$query = "
					select
					h.hotel_id, h.hotel_name,h.hotel_alias,h.hotel_short_description as hotel_description, h.hotel_county, h.hotel_city, h.hotel_address, h.hotel_zipcode, h.hotel_website, h.hotel_stars, h.hotel_rating_score, h.featured, h.hotel_selling_points, h.recommended, h.hotel_phone, h.hotel_number,h.hotel_latitude,h.hotel_longitude,
					h.hotel_stars as hstars,hh10.room_min_rate,
					(hh9.totalRoomsAvailable-IFNULL(hc.totalReserved,0)) as roomsLeft,
					IF(hh11.min_offer_price IS NULL OR hh10.room_min_rate IS NULL, COALESCE(hh11.min_offer_price, hh10.room_min_rate), LEAST(hh11.min_offer_price,hh10.room_min_rate))  as lowest_hotel_price, 
					hh11.offer_available,
					$distanceSelect
			";
		
		if($searchType == 0){
			$query .= "	
					currency_symbol,
					hc.noBookings,
					offer_details,
					noReviews,
					c1.country_name,
					c2.description	AS hotel_currency,
					hotel_picture_path,hotel_picture_info,hotel_pictures_count,
					hlt.content as hotel_description,
					";
		}	
		
		$query .=" hh8.* from (
				 SELECT h.hotel_id, facilities, types,accommodationTypes, enviroments,regions,1 as room_available,themes,noReviews
                 from 
                     (select h.hotel_id from #__hotelreservation_hotels as h
                             where 1 $whereClause $starsFilter $dayFilter $activeHotelsFilter and h.is_available = 1) h 
                	 left join (select hotelId, GROUP_CONCAT(facilityId) as facilities
                	            from #__hotelreservation_hotel_facility_relation
                	            where 1 
                	            group by hotelId 
                	            ) as fr on h.hotel_id = fr.hotelId
               		 left join (select hotelId, GROUP_CONCAT(tr.typeId) as types
               		            from #__hotelreservation_hotel_type_relation as tr 
                 			    left join #__hotelreservation_hotel_types ht on ht.id=tr.typeId 
                 			    where 1 
                 			    group by hotelId 
				 			    ) tr on h.hotel_id = tr.hotelId
				     left join  (select  hotelId, GROUP_CONCAT(accommodationTypeId) as accommodationTypes  
				                 from #__hotelreservation_hotel_accommodation_type_relation 
				                 where 1 
				                 group by hotelId 
				                ) as atr on h.hotel_id = atr.hotelId
					left join (  select hotelId, GROUP_CONCAT(environmentId) as enviroments 
					             from #__hotelreservation_hotel_environment_relation 
					             where 1   
					             group by hotelId ) as er on h.hotel_id=er.hotelId 
				   ".(empty($regionWhereFilter)?' left ':' inner ')."join ( select  rl.hotelId, GROUP_CONCAT(rl.regionId) as regions 
				                 from #__hotelreservation_hotel_region_relation as rl 
							     left  join #__hotelreservation_hotel_regions as hr on hr.id = rl.regionId 
							     where 1  $regionWhereFilter 
							     group by hotelId
							   ) as rl  on h.hotel_id=rl.hotelId   
					$roomFilter 
					left join ( select hotel_id, GROUP_CONCAT(hotr.themeId) as themes
					            from #__hotelreservation_offers hof 
						 		left join #__hotelreservation_offers_themes_relation hotr on hotr.offerId=hof.offer_id 
								left join #__hotelreservation_offers_themes hot on hot.id = hotr.themeId
								".(!empty($searchParam['voucher'])?  "left join #__hotelreservation_offers_vouchers hov on hof.offer_id = hov.offerId where 1 $voucherFilter ":"")."
								group by hotel_id           
					    	  ) hof on h.hotel_id = hof.hotel_id 
				";
			if($searchType == 0){
				$query .=" left join
						(select hotel_id, count(hrt.review_id) as noReviews 
						     from #__hotelreservation_review_customers hrt
							 group by hotel_id
						) as hrc on h.hotel_id = hrc.hotel_id
					";
			}
			$query .= " where  1  $facilityFilter $typesFilter $accommodationTypeFilter $enviromentFilter $regionFilter) as hh8";
			$query .=" inner join #__hotelreservation_hotels h on hh8.hotel_id=h.hotel_id";
				

		
		if($searchType == 0){
			$query .=" left join #__hotelreservation_countries c1 USING (country_id) ";
			$query .=" left join #__hotelreservation_currencies c2 USING (currency_id) ";
			$query .=" left join
							( select hc.hotel_id,count(hcr.confirmation_room_id) as totalReserved,count(distinct hc.confirmation_id) as noBookings
							  from #__hotelreservation_confirmations hc
							  inner join #__hotelreservation_confirmations_rooms hcr on hc.confirmation_id = hcr.confirmation_id
							  where hc.start_date<'$endDate' and hc.end_date>='$startDate'
					
							  group by hc.hotel_id
					        )as hc on h.hotel_id=hc.hotel_id";

			$query .=" left join (
                             		select a.hotel_id,a.hotel_picture_path, a.hotel_picture_info,b.hotel_pictures_count
					       		    from #__hotelreservation_hotel_pictures a
					       		    inner join
					       				 (SELECT hp.hotel_id, min(hp.hotel_picture_id) as hotel_main_picture_id, count(hp.hotel_picture_id) as hotel_pictures_count
								  		 FROM #__hotelreservation_hotel_pictures AS hp 
								      	 group by hotel_id
					       			) b on a.hotel_picture_id = b.hotel_main_picture_id
						)as hh12 on hh12.hotel_id = h.hotel_id
					";
			
			$query .="left join (
							select * 
							from #__hotelreservation_language_translations
							where type = ".HOTEL_TRANSLATION."
							and language_tag = '$languageTag'
						)as hlt on hlt.object_id = h.hotel_id";
		}
		
		$query .="
				left join (
	                select hotel_id,
	                min(room_rate) as room_min_rate,
	                GROUP_CONCAT(room_name,'|',room_id,'|',room_rate) as room_details 
	                from (
	                	select  
	                	hr.room_name,
	                	hr.room_id,
	                	hr.hotel_id,
	                	if(min(hrrp.price), min(hrrp.price), min(least(hrr.price_1, hrr.price_2, hrr.price_3, hrr.price_4, hrr.price_5, hrr.price_6, hrr.price_7))) as room_rate
	               		from #__hotelreservation_rooms as hr 
                    	left join #__hotelreservation_rooms_rates as hrr on hr.room_id=hrr.room_id
                    	left join #__hotelreservation_rooms_rate_prices as hrrp on hrr.id=hrrp.rate_id $roomRateDateFilter
                    	where hr.front_display = 1 and hr.is_available=1
                    	group by hr.room_id
                    ) hh110 
                    group by hotel_id
	             ) as hh10 on h.hotel_id=hh10.hotel_id";
		
		$query .="
				left join (
	                select hotel_id,
	                sum(IF(hh19.roomsCustAvailable IS NULL,hh19.roomsAvailable,hh19.roomsCustAvailable)) as totalRoomsAvailable
	                from (
	                	select  
	                	hr.room_id,
	                	hr.hotel_id,
	                	sum(hrr.availability) as roomsAvailable, sum(hrrp.availability) as roomsCustAvailable
	               		from #__hotelreservation_rooms as hr 
                    	left join #__hotelreservation_rooms_rates as hrr on hr.room_id=hrr.room_id
                    	left join (select  rate_id, availability  from #__hotelreservation_rooms_rate_prices where availability is not null and date between '$startDate' and '$offerCheckDate') as hrrp on hrr.id=hrrp.rate_id 
                    	where hr.is_available=1
                    	group by hr.room_id
                    ) hh19
                    group by hotel_id
	             ) as hh9 on h.hotel_id=hh9.hotel_id";
		
		$query .="
			left join (
               select hotel_id,1 as offer_available,
               		  GROUP_CONCAT(offer_name,'||',offer_id,'||',offer_rate,'||',offer_min_nights,'||',base_adults,'||',price_type,'||',price_type_day,'||',offer_room_id,'||',offer_content,'||',last_minute,'||',ifnull(offer_initial_price,'') separator '##') as offer_details,
				      min(offer_rate) as min_offer_price
              from (
             		select
             		hof.offer_name,
             		hof.offer_content,
             		hof.offer_id,
             		hof.hotel_id as hotel_id,
             		hof.offer_min_nights as offer_min_nights,
             		hof.last_minute as last_minute,
             		hof.offer_initial_price as offer_initial_price,
					ofr.base_adults, ofr.price_type,  ofr.price_type_day, ofrr.room_id as offer_room_id,
					if(ofr.price_type_day=1 , 
						if ( min(orp.price),  min(orp.price), min(least(ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7))) ,
						if ( min(orp.price),  min(orp.price), min(least(ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7))) * hof.offer_min_nights )
						 as offer_rate
				    from #__hotelreservation_offers hof 
                    inner join #__hotelreservation_offers_rooms ofrr on ofrr.offer_id = hof.offer_id
                    $roomOfferFilter
					inner join #__hotelreservation_offers_rates ofr on ofr.offer_id = hof.offer_id and ofr.room_id = ofrr.room_id
					left join #__hotelreservation_offers_rate_prices orp on orp.rate_id = ofr.id $offerRateDateFilter and orp.date>='$startDate'
			        left join #__hotelreservation_offers_vouchers hov on hof.offer_id = hov.offerId  
  	                where hof.is_available = 1 and ('$offerCheckDate' between hof.offer_datasf and hof.offer_dataef) 
  	                	  and ('$offerCheckDate' between hof.offer_datas and hof.offer_datae)  $voucherFilter
					$offerRestrictionFilter
		            group by hof.offer_id
  	                order by hotel_id,offer_rate asc
  	              ) hh111 
                group by hotel_id
            )  as hh11 on h.hotel_id= hh11.hotel_id";
        	   
		$query .=" WHERE 1 $availabilityFilter $themesFilter $roomAvabilityFilter $offerAvailabilityFilter $cityFilter $excludeFilter $priceFilterSql $havingDistance";

		if($searchType == 0){
			$query .= " $orderBy ";
		}
		return $query;
	}

	function getHotels($searchParam, $limitstart=0, $limit=0){
		$db =JFactory::getDBO();
		
		$query = $this->getHotelSearchQuery($searchParam);
		
		$db->setQuery("SET OPTION SQL_BIG_SELECTS=1");
		//$db->query();
		
		$db->setQuery("SET SESSION group_concat_max_len = 1000000 ");
		$db->query();
		
		
		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();

		return $result;
	}

	function getTotalHotels($searchParam){
		$result = 0;
		$db =JFactory::getDBO();

		$query = $this->getHotelSearchQuery($searchParam,0);
		$db->setQuery($query);
		
		$result = $db->loadObjectList();
		

		//dmp($this->_db->getErrorMsg());
		return $result;
	}

	function getFilteredHotels($filterParams=array(), $limitstart=0, $limit=0){
		$whereCond=' where 1 ';

		if(isset($filterParams))
		foreach($filterParams as $key=>$value){
			$whereCond .= "and $key= $value ";
		}
		$query = "	SELECT
					h.*,
					c1.country_name,
					c2.description	AS hotel_currency
				FROM #__hotelreservation_hotels 			h
				LEFT JOIN #__hotelreservation_countries 	c1 USING (country_id)
				LEFT JOIN #__hotelreservation_currencies 	c2 USING (currency_id)
		$whereCond
				ORDER BY h.hotel_name
				";
		$db =JFactory::getDBO();
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	function getFilteredHotelsTotal($filterParams=array()){
		$whereCond=' where 1 ';
		foreach($filterParams as $key=>$value){
			$whereCond .= "and $key= $value ";
		}
		$query = "	SELECT
						h.*,
						c1.country_name,
						c2.description	AS hotel_currency
					FROM #__hotelreservation_hotels 			h
					LEFT JOIN #__hotelreservation_countries 	c1 USING (country_id)
					LEFT JOIN #__hotelreservation_currencies 	c2 USING (currency_id)
		$whereCond
					ORDER BY h.hotel_name
					";
		$db =JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		return $db->getNumRows();
	}

	function getDays($sStartDate, $sEndDate){
		// Firstly, format the provided dates.
		// This function works best with YYYY-MM-DD
		// but other date formats will work thanks
		// to strtotime().
		$sStartDate = date("Y-m-d", strtotime($sStartDate));
		$sEndDate = date("Y-m-d", strtotime($sEndDate));
	
		// Start the variable off with the start date
		$aDays[] = $sStartDate;
	
		// Set a 'temp' variable, sCurrentDate, with
		// the start date - before beginning the loop
		$sCurrentDate = $sStartDate;
	
		// While the current date is less than the end date
		while($sCurrentDate < $sEndDate){
			// Add a day to the current date
			$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
	
			// Add this new day to the aDays array
			$aDays[] = $sCurrentDate;
		}
	
		// Once the loop has finished, return the
		// array of days.
		return $aDays;
	}
	
	function getElapsedTime($eventTime)
	{
		$totaldelay = time() - $eventTime;
		if($totaldelay <= 0)
		{
			return ' < 1 sec';
		}
		else
		{
			if($days=floor($totaldelay/86400))
			{
				$totaldelay = $totaldelay % 86400;
				return $days.' days .';
			}
			if($hours=floor($totaldelay/3600))
			{
				$totaldelay = $totaldelay % 3600;
				return $hours.' hours .';
			}
			if($minutes=floor($totaldelay/60))
			{
				$totaldelay = $totaldelay % 60;
				return $minutes.' minutes .';
			}
			if($seconds=floor($totaldelay/1))
			{
				$totaldelay = $totaldelay % 1;
				return $seconds.' seconds .';
			}
		}
	}
	
	
	function getHotelCitiesSuggestions($keword,$limit){
		$db =JFactory::getDBO();
		$query = "select hotel_city as label, hotel_city as value, count(h.hotel_id) as nr_hotels, '".$db->escape(JText::_("LNG_CITY"))."' as category 
					FROM #__hotelreservation_hotels h 
					where hotel_city like '%$keword%' and h.is_available=1
					group by h.hotel_city
					order by nr_hotels desc, h.hotel_city";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getHotelProvinceSuggestions($keword,$limit){
		$db =JFactory::getDBO();
		$query = "select hotel_county as label, hotel_county as value, count(h.hotel_id) as nr_hotels, '".$db->escape(JText::_("LNG_PROVINCE_AND_REGION"))."' as category
		FROM #__hotelreservation_hotels h
		where hotel_county like '%$keword%' and h.is_available=1
		group by h.hotel_county
		order by nr_hotels desc, h.hotel_county";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getHotelRegionSuggestions($keword,$limit){
		$db =JFactory::getDBO();
		$query = "select hr.name as label, hr.name as value, count(h.hotel_id) as nr_hotels, '".$db->escape(JText::_("LNG_PROVINCE_AND_REGION"))."' as category
		FROM #__hotelreservation_hotels h
		inner join #__hotelreservation_hotel_region_relation as rl on h.hotel_id=rl.hotelId 
		inner join #__hotelreservation_hotel_regions as hr on hr.id = rl.regionId 
		where hr.name like '%$keword%' and h.is_available=1
		group by hr.id
		order by nr_hotels desc, hr.name";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getHotelsSuggestions($keword,$limit){
		$db =JFactory::getDBO();
		$query = "select hotel_name as label, hotel_id as value, count(h.hotel_id) as nr_hotels, '".$db->escape(JText::_("LNG_HOTELS"))."' as category
		FROM #__hotelreservation_hotels h
		#inner join  #__hotelreservation_offers hof on hof.hotel_id = h.hotel_id and hof.is_available =1   
		
		where hotel_name like '%$keword%' and h.is_available=1
		group by h.hotel_name
		order by nr_hotels desc, h.hotel_name";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getHotelAccomodationTypeSuggestions($keword,$limit){
		$db =JFactory::getDBO();
		$query = "select hat.name as label, hat.id as value, count(h.hotel_id) as nr_hotels, '".$db->escape(JText::_("LNG_ACCOMMODATION_TYPES"))."' as category
		FROM #__hotelreservation_hotels h
		
		left join #__hotelreservation_hotel_accommodation_type_relation as atr on h.hotel_id=atr.hotelId 
		left join #__hotelreservation_hotel_accommodation_types as hat on hat.id = atr.accommodationtypeId
		where hat.name like '%$keword%' and h.is_available=1
		group by hat.name
		order by nr_hotels desc, hat.name";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getHotelOfferThemesSuggestions($keword,$limit){
		$db =JFactory::getDBO();
		$query = "select hot.name as label, hot.id as value, count(h.hotel_id) as nr_hotels, '".$db->escape(JText::_("LNG_THEMES"))."' as category
		FROM #__hotelreservation_hotels h
		left join #__hotelreservation_rooms as r on h.hotel_id = r.hotel_id
		inner join #__hotelreservation_offers_rooms hor	ON hor.room_id 	= r.room_id
		inner join #__hotelreservation_offers	ho ON hor.offer_id 	= ho.offer_id
		left join #__hotelreservation_offers_themes_relation hotr on hotr.offerId=ho.offer_id 
		left join #__hotelreservation_offers_themes hot on hot.id = hotr.themeId
		where hot.name like '%$keword%' and h.is_available=1 and ho.is_available = 1 and r.is_available = 1
		group by hot.name
		order by nr_hotels desc, hot.name";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getNearByHotels($latitude, $longitude, $distance, $excludedIds, $limit){
		
		$excludedIdsFilter = "and h.hotel_id no in (". implode(",", $excludedIds).")";
		
		$query = " SELECT h.*,hotel_picture_path, min(hp.hotel_picture_id),
				1.609344*3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -abs( h.hotel_latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs(h.hotel_latitude) *  pi()/180) * POWER(SIN(($longitude - h.hotel_longitude) *  pi()/180 / 2), 2) )) as distance
				FROM #__hotelreservation_hotels h
				left join #__hotelreservation_hotel_pictures hp on h.hotel_id=hp.hotel_id
				where h.is_available =1 
				group by h.hotel_id
				having distance < $distance ORDER BY distance asc ";
		
		//echo $query;
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();
		
	}

    public static function getHotelRegion($hotelId){
        $db = JFactory::getDBO();
        $query = "  SELECT hrr.regionId, hr.name FROM #__hotelreservation_hotel_region_relation as hrr
                    LEFT JOIN #__hotelreservation_hotel_regions as hr on  hr.Id = hrr.regionId where hrr.hotelId=".$hotelId;
        $db->setQuery( $query );
        $result = $db->loadObject();
        return $result;
    }

    public static function getHotelType($hotelId){
        $db = JFactory::getDBO();
        $query = "  SELECT htr.typeId, ht.name FROM #__hotelreservation_hotel_type_relation as htr
                    LEFT JOIN #__hotelreservation_hotel_types as ht on  ht.Id = htr.typeId where htr.hotelId=".$hotelId;
        $db->setQuery( $query );
        $result = $db->loadObject();
        return $result;
    }

    public static function getRegionCount($regionId){
	    $region='';
	    if(isset($regionId))
		    $region = " and regionId=".$regionId;

        $db = JFactory::getDBO();
        $query = "  SELECT count(hrr.hotelId) as regionCount FROM #__hotelreservation_hotel_region_relation as hrr  LEFT JOIN
                    #__hotelreservation_hotels as h on h.hotel_id = hrr.hotelId where h.is_available=1".$region;
        $db->setQuery( $query );
        $result = $db->loadObject();
        return $result;
    }

    public static function getTypeCount($typeId){

	    $type = '';
	    if(isset($typeId))
		    $type = " and typeId=".$typeId;
        $db = JFactory::getDBO();
        $query = "  SELECT count(htr.hotelId) as typeCount
                    FROM #__hotelreservation_hotel_type_relation as htr LEFT JOIN
                    #__hotelreservation_hotels as h on h.hotel_id = htr.hotelId where h.is_available=1 ".$type;
        $db->setQuery( $query );
        $result = $db->loadObject();
        return $result;
    }

    public static function getHotelsNumberByCity($hotelCity){
        $db = JFactory::getDBO();
        $query = "  SELECT count(hotel_id) as cityCount FROM #__hotelreservation_hotels where hotel_city = '".$hotelCity."'";
        $db->setQuery( $query );
        $result = $db->loadObject();
        return $result->cityCount;
    }

	/**
	 * @param $hotelId
	 * @param $alias
	 *
	 * @return mixed
	 */
	function checkIfAliasExists($hotelId, $alias){
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as alias_number FROM #__hotelreservation_hotels  WHERE hotel_alias='$alias' and hotel_id<>$hotelId";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result->alias_number;
	}

	function getChannelManagers($hotelId){
		$db =JFactory::getDBO();
		$query = 'SELECT * FROM #__hotelreservation_hotel_channel_manager where hotel_id = '.(int)$hotelId;
		$db->setQuery($query);
		$channelManagers = $db->loadObjectList();
		$channelManagersArray = array();
		if(isset($channelManagers) && count($channelManagers)>0){
			foreach($channelManagers as $channelManager){
				$channelManagersArray[$channelManager->service]=$channelManager;
			}
		}
		return $channelManagersArray;
	}
	
	function getHotelsBeds24(){
		
		$db =JFactory::getDBO();
		$query = 'SELECT b.* 
				  FROM #__hotelreservation_hotels a 
				  inner join #__hotelreservation_hotel_channel_manager b 
				  where a.hotel_id = b.hotel_id
				  and b.service ="'.CHANNEL_MANAGER_BEDS24.'"';
		
		$db->setQuery($query);
		$hotels = $db->loadObjectList();
		
		return $hotels;
	}
				
}
