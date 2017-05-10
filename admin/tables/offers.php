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

class JTableOffers extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function JTableOffers(& $db) {

		parent::__construct('#__hotelreservation_offers', 'offer_id', $db);
	}
	
	function getOffer($offerId){
		$language = JFactory::getLanguage();
		$languageTag = $language->getTag();
		$date = date("Y-m-d");
		$db =JFactory::getDBO();
		
		$query = "select *, hlt.content as offer_description,op.offer_picture_path,	op.offer_picture_info,
					if(ofr.price_type_day = 1,		
						if(min(orp.price),
							least(orp.price,ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7),
							least(ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7)),
							if(min(orp.price),
							least(orp.price,ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7)* of.offer_min_nights,
							least(ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7)* of.offer_min_nights)
							 )  as starting_price 
						from #__hotelreservation_offers of
						inner join #__hotelreservation_hotels h on h.hotel_id=of.hotel_id
						inner join #__hotelreservation_countries c on h.country_id=c.country_id
						inner join #__hotelreservation_offers_rooms ofrr on ofrr.offer_id = of.offer_id 
						inner join #__hotelreservation_offers_rates ofr on ofr.offer_id = of.offer_id and ofr.room_id = ofrr.room_id
						left join #__hotelreservation_offers_rate_prices orp on orp.rate_id = ofr.id and orp.date>'$date'
						left join #__hotelreservation_currencies hcr on h.currency_id = hcr.currency_id
						left join #__hotelreservation_offers_pictures op on op.offer_id=of.offer_id
						left join (
							select * from 
							 #__hotelreservation_language_translations 
							 where type = ".OFFER_TRANSLATION."
							 and language_tag = '$languageTag'
							)as hlt on hlt.object_id =  of.offer_id
							left join (
				                select lt.object_id,lt.content as hotel_description
				                from #__hotelreservation_language_translations lt
						        where type = ".HOTEL_TRANSLATION."
						        and language_tag = '$languageTag'
						    )as olt on olt.object_id = of.hotel_id
						where of.offer_id=$offerId";
		//dmp($query);exit; 
		$db->setQuery($query);
		return $db->loadObject();
	}
	 
	function getOffers($voucher, $hotelId, $city="", $orderBy, $limitstart, $limit){
		$db =JFactory::getDBO();
		$date = date("Y-m-d");
		
		
		$language = JFactory::getLanguage();
		$languageTag = $language->getTag();
		
		$hotelFilter="";
		if(!empty($hotelId)){
			$hotelFilter =" and h.hotel_id = $hotelId ";
		}
		
		$cityFilter ="";
		if(!empty($city)){
			$cityFilter = " and h.hotel_city = '".$city."'";
		}
		
		$voucherFilter = " (hov.voucher is null or hov.voucher='' or public = 1)";
		if($voucher!=''){
			$voucherFilter = " hov.voucher ='$voucher'";
		}
		
		if(empty($orderBy))
			$orderBy = " of.offer_name";
		
		$query = "select *, of.featured as featuredOffer,hlt.content as offer_short_description,
					if(ofr.price_type_day = 1,		
						if(min(orp.price),
							least(min(orp.price),ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7),
							least(ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7)),
							if(min(orp.price),
							least(min(orp.price),ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7)* of.offer_min_nights,
							least(ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7)* of.offer_min_nights)
							 )  as starting_price 
					from #__hotelreservation_offers of
					inner join #__hotelreservation_hotels h on h.hotel_id=of.hotel_id
					inner join #__hotelreservation_countries c on h.country_id=c.country_id
					inner join #__hotelreservation_offers_rooms ofrr on ofrr.offer_id = of.offer_id
					inner join #__hotelreservation_rooms r ON ofrr.room_id = r.room_id  
					inner join #__hotelreservation_offers_rates ofr on ofr.offer_id = of.offer_id and ofr.room_id = ofrr.room_id
					left join #__hotelreservation_offers_rate_prices orp on orp.rate_id = ofr.id  and orp.date>'$date'
					left  join #__hotelreservation_offers_vouchers hov on of.offer_id = hov.offerId  
					left  join #__hotelreservation_currencies hcr on h.currency_id = hcr.currency_id
					left join (
							select * from 
							 #__hotelreservation_language_translations 
							 where type = ".OFFER_SHORT_TRANSLATION."
							 and language_tag = '$languageTag'
							)as hlt on hlt.object_id = of.offer_id
					left join (
						select lt.object_id,lt.content as hotel_description from
						 #__hotelreservation_language_translations lt
						 where type = ".HOTEL_TRANSLATION."
						 and language_tag = '$languageTag'
						)as olt on olt.object_id = of.hotel_id

					where $voucherFilter $hotelFilter $cityFilter and now() between of.offer_datasf and of.offer_dataef and h.is_available = 1 and of.is_available = 1 and r.is_available = 1
					group by of.offer_id 
					order by of.featured desc, $orderBy
				";
		//echo($query);
		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();
		//dmp($db->getErrorMsg());
		
		return $result;
	}
	
	function getTotalOffers($voucher, $city){
		$db =JFactory::getDBO();
		$date = date("Y-m-d");
		
		$voucherFilter = " (hov.voucher is null or hov.voucher='' or public = 1 )";
		if($voucher!=''){
			$voucherFilter = " hov.voucher ='$voucher'";
		}
	
		$cityFilter ="";
		if(!empty($city)){
			$cityFilter = " and h.hotel_city = '".$city."'";
		}
		
		$query = "select *, min(least(price_1, price_2, price_3,price_4,price_5,price_6,price_7)) * of.offer_min_nights as starting_price
						from #__hotelreservation_offers of
						inner join #__hotelreservation_hotels h on h.hotel_id=of.hotel_id
						inner join #__hotelreservation_countries c on h.country_id=c.country_id
						inner join #__hotelreservation_offers_rates ofr on ofr.offer_id = of.offer_id
						left join #__hotelreservation_offers_rate_prices orp on orp.rate_id = ofr.id and orp.date>'$date'
						left  join #__hotelreservation_offers_vouchers hov on of.offer_id = hov.offerId  
						where $voucherFilter $cityFilter and now() between of.offer_datasf and of.offer_dataef and h.is_available = 1 and of.is_available = 1
						group by of.offer_id 
				";
		$db->setQuery($query);
		$db->query();
		return $db->getNumRows();
	}

	function getOffersPictures($offerId){
        if(isset($offerId) && (int)$offerId>0) {
            $db = JFactory::getDBO();

            $query = "select * from #__hotelreservation_offers_pictures where offer_id=$offerId";
            $db->setQuery($query);
            return $db->loadObjectList();
        }
	}
	
	function getOfferBookingSituation(){
		$db =JFactory::getDBO();
		
		$query = "select hc.confirmation_id,of.offer_name, h.hotel_id,h.hotel_name, of.offer_id, hc.voucher, hc.media_referer, count(hc.confirmation_id) as nrBookings
							from #__hotelreservation_offers of
							inner join #__hotelreservation_hotels h on h.hotel_id=of.hotel_id
							inner join #__hotelreservation_confirmations_rooms hcr on of.offer_id= hcr.offer_id
							inner join #__hotelreservation_confirmations hc on hc.confirmation_id= hcr.confirmation_id
              group by hc.voucher,hc.media_referer, hc.hotel_id
              order by hc.voucher, hc.media_referer asc
						";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getOfferViewSituation(){
		$db =JFactory::getDBO();
	
		$query = "select of.offer_name, h.hotel_id, h.hotel_name, ov.voucher,of.offer_id, ov.media_referer, ov.view_count
							from #__hotelreservation_offers of
							inner join #__hotelreservation_hotels h on h.hotel_id=of.hotel_id
							inner join #__hotelreservation_offers_views ov on of.offer_id= ov.offer_id
           					  order by ov.voucher, ov.media_referer, h.hotel_id asc
              
							";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getOfferOrder(){
		$query = " SELECT (max(ordering)+1) as offerOrder  FROM #__hotelreservation_offers";
		//dmp($query);
		$this->_db->setQuery( $query );
		return $this->_db->loadObject()->offerOrder;
	}

    function getOfferAirportTransferId($offerId){
        $db =JFactory::getDBO();
        $query = " SELECT o.airport_transfer_type_id  FROM #__hotelreservation_offers o INNER JOIN #__hotelreservation_airport_transfer_types arpt on o.airport_transfer_type_id = arpt.airport_transfer_type_id where o.offer_id=$offerId";
        //dmp($query);
        $db->setQuery($query);
        return $db->loadObject();
    }

    /**
     * @param $offer_Id
     * @return mixed
     */
    function getAirportNamesByOffer($offer_Id){
        $db = JFactory::getDBO();
        $query = "select a.airport_transfer_type_id , a.airport_transfer_type_name from #__hotelreservation_airport_transfer_types as a inner join
#__hotelreservation_offers as o where a.airport_transfer_type_id = o.airport_transfer_type_id and o.offer_id = $offer_Id group by o.offer_id order by a.airport_transfer_type_id";
            $db->setQuery($query);
        return $db->loadObject();
    }

    function getTotalNumberOffers(){
        $db = JFactory::getDBO();
        $query = "SELECT count(*) as n FROM #__hotelreservation_offers";
        $db->setQuery($query);
        $result = $db->loadObject();

        return $result->n;
    }

    function getTotalActiveOffers(){
        $db =JFactory::getDBO();
        $query = "SELECT count(*) as n FROM #__hotelreservation_offers where is_available = 1 and offer_datae>now()";
        $db->setQuery($query);
        $result = $db->loadObject();

        return $result->n;
    }
}