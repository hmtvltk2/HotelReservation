<?php
/**
 * @version		$Id: remember.php 22249 2011-10-16 17:19:28Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
if ( file_exists(JPATH_SITE.'/administrator/components/com_jhotelreservation/helpers/utils.php'))
	require_once JPATH_SITE.'/administrator/components/com_jhotelreservation/helpers/utils.php';


/**
 * URL Translator
 *
 * @package		Joomla.Plugin
 * @subpackage	System.remember
 */
class plgSystemHotelUrlTranslator extends JPlugin
{
	var $excludingVouchers = array("viva","nusport","grazia","libelle","margriet","flair","panorama","autoweek","revu","story","topdeal","zonnebloem");
	var $regionItemsIds = array("drenthe"=>"185","groningen"=>"186","friesland"=>"187","overijssel"=>"188","gelderland"=>"189","noord holland"=>"191","utrecht"=>"192","limburg"=>"195","zuid holland"=>"196","zeeland"=>"197","noord brabant"=>"198","duitsland"=>"221");

	function onAfterRoute()
	{
		//check if component is installed. 
		if (!file_exists(JPATH_SITE.'/administrator/components/com_jhotelreservation/helpers/utils.php'))
			return;
		
		$app = JFactory::getApplication();
		
		// do nothing for admin
		if ($app->isAdmin()) {
			return;		
		}
		
		//do nothing when seo is disabled
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		if(!$appSettings->enable_seo){
			return;
		}
		
		// Get the full current URI.
		$uri = JURI::getInstance();
		$current = $uri->toString( array('path'));
		
		$pieces = explode("/", $current);
		$keyword = array_pop($pieces);
		$keyword = urldecode($keyword);
		$keywordCat = array_pop($pieces);
		
		if(!isset($keyword) || $keyword=='')
			return;
	
		$params = JRequest::get('GET');
		if(strpos($keyword,'hotels-')=== 0) {
			$params = $this->getHotelRegionParams($keyword, $params);
		}else if( strpos($keyword,'hotel-') === 0) {
			$params = $this->getHotelParams($keyword, $params);
		}else if(strpos($keyword,'hotelarrangement-')===0) {
			$params = $this->getHotelOfferParams($keyword, $params);
		}else if(strpos($keywordCat,'hotelarrangement')===0) {
			$params = $this->getHotelCityOffersParams($keyword, $params);
		}else if(strpos($keyword,'type-')===0) {
			$params = $this->getHotelTypeParams($keyword, $params);
		}else if(strpos($keyword,'theme-')===0) {
			$params = $this->getHotelThemeParams($keyword, $params);
		}else if(strpos($keyword,'poi-')===0) {
			$params = $this->getHotelPoiParams($keyword, $params);
		}else {
			$params = $this->getVoucherParams($keyword, $params);
			if(empty($params["voucher"])){
				$params = $this->getHotelCityParams($keyword, $params);
			}
		}
		JRequest::set($params,'get',true);
	}
	
	function getHotelParams($keyword, $params){
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$keyword =  preg_replace("/hotel-/", "", $keyword,1);
		if($appSettings->enable_seo){
			//$keyword =  preg_replace("/hotel-/", "", $keyword,1);
			$db = JFactory::getDBO();
			$query = "SELECT * from #__hotelreservation_hotels h WHERE h.hotel_alias = '".$db->escape($keyword)."' ";
			$db->setQuery($query, 0, 1);
			$hotel = $db->loadObject();
		}else{
			//by id not hotel alias

			$db = JFactory::getDBO();
			$query = "SELECT * from #__hotelreservation_hotels h WHERE h.hotel_id = '".$keyword."' ";
			$db->setQuery($query, 0, 1);
			$hotel = $db->loadObject();
		}
		if(isset($hotel)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "hotel.showHotel";
			$params["view"] = "hotel";
			$params["hotel_id"] = $hotel->hotel_id;
			$params["tip_oper"] = "-1";
			$params["init_hotel"] = "1";
			$params["Itemid"] = "";
		}
		return $params;
	}
	
	
	function getHotelCityParams($keyword, $params){
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__hotelreservation_hotels`  WHERE REPLACE(hotel_city,'-',' ') = '$keyword'";
		$db->setQuery($query);
		$hotels = $db->loadObjectList();
		
		if(!empty($hotels)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "hotels.searchHotels";
			$params["view"] = "hotels";
			$params["showAll"] = "1";
			$params["city"] = $keyword;
			$params["Itemid"] = "";
		}
		return $params;
	}
	
	
	function getHotelRegionParams($keyword, $params){	
		$keyword =  preg_replace("/hotels-/", "", $keyword,1);
		$keyword =  str_replace("-", " ", $keyword);
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__hotelreservation_hotel_regions`  WHERE name = '$keyword'";
		
		$db->setQuery($query, 0, 1);

		$region = $db->loadObject();
		if(!empty($region)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "hotels.searchHotels";
			$params["filterParams"] = "regionId=".$region->id;
			$params["tip_oper"] = "-2";
			$params["showAll"] = "1";
			$params["Itemid"] = $this->regionItemsIds[$keyword];
		}		
		return $params;
	}
	
	function getHotelThemeParams($keyword, $params){
		$keyword =  preg_replace("/theme-/", "", $keyword,1);
		$keyword =  str_replace("-", " ", $keyword);
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__hotelreservation_offers_themes`  WHERE REPLACE(name,'-',' ') = '$keyword'";
	
	
		$db->setQuery($query, 0, 1);
	
		$theme = $db->loadObject();
		if(isset($theme)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "search.searchHotels";
			$params["filterParams"] = "themeId=".$theme->id;
			$params["showAll"] = "1";
			$params["view"] = "hotels";
			$params["Itemid"] = ""; 
		}
		return $params;
	}
	
	function getHotelTypeParams($keyword, $params){
		$keyword =  preg_replace("/type-/", "", $keyword,1);
		$keyword =  str_replace("-", " ", $keyword);
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__hotelreservation_hotel_types`  WHERE REPLACE(name,'-',' ') = '$keyword'";
	
	
		$db->setQuery($query, 0, 1);
	
		$type = $db->loadObject();
		if(isset($type)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "search.searchHotels";
			$params["filterParams"] = "typeId=".$type->id;
			$params["tip_oper"] = "-2";
			$params["showAll"] = "1";
			$params["view"] = "hotels";
			$params["Itemid"] = "";
		}
		return $params;
	}
	
	function getVoucherParams($keyword, $params){
		
		foreach($this->excludingVouchers as $voucher){
			if(strcasecmp($voucher, $keyword) == 0)
				return;
		}
		
		$keyword =  str_replace("-", " ", $keyword);
		$db = JFactory::getDBO();
		$query = "
				select * from #__hotelreservation_offers of
					inner join #__hotelreservation_offers_vouchers hov on of.offer_id = hov.offerId  
					where REPLACE(hov.voucher,'-',' ') = '$keyword' 
					group by of.offer_id";
	
		$db->setQuery($query, 0, 1);
		$voucher = $db->loadObject();
		if(isset($voucher)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "offers.searchOffers";
			$params["voucher"] = $voucher->voucher;
			$params["Itemid"] = "";
		}
		return $params;
	}
	
	function getHotelOfferParams2($keyword, $params){

		$keyword =  preg_replace("/hotelarrangement-/", "", $keyword,1);
		$keyword =  str_replace("-", " ", $keyword);

		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__hotelreservation_offers of  
						inner join ( select *, REPLACE(offer_name,'-',' ') as offerName FROM #__hotelreservation_offers) of1 on of.offer_id = of1.offer_id
						WHERE of1.offerName = '".$keyword."' ";
		$db->setQuery($query, 0, 1);
		
		$offer = $db->loadObject();
		if(isset($offer)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "offer.displayOffer";
			$params["offerId"] = $offer->offer_id;
			$params["Itemid"] = "";
		}
		return $params;
	}
	
	function getHotelOfferParams($keyword, $params){

		$keywords =  explode("-",$keyword);
		$keyword =  end($keywords);

		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__hotelreservation_offers where offer_id = $keyword";
	
		$db->setQuery($query, 0, 1);
		
		$offer = $db->loadObject();
		
		if(isset($offer)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "offer.displayOffer";
			$params["view"] = "offer";
			$params["offerId"] = $offer->offer_id;
			$params["Itemid"] = "";
		}
		return $params;
	}
	
	function getHotelCityOffersParams($keyword, $params){
	
		$keyword =  str_replace("-", " ", $keyword);
		
		$db = JFactory::getDBO();
		$query = "SELECT * 
					FROM #__hotelreservation_offers of
					left join #__hotelreservation_hotels h on h.hotel_id = of.hotel_id
					WHERE REPLACE(h.hotel_city,'-',' ') = '$keyword'";
		$db->setQuery($query);
		$offers = $db->loadObjectList();
		
		if(!empty($offers)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "offers.searchOffers";
			$params["city"] = "$keyword";
			$params["view"] = "listOffers";
			$params["Itemid"] = "";
		}
		return $params;
	}

	function getHotelPoiParams($keyword, $params){

		$keywords =  explode("-",$keyword);

		$poiName =  end($keywords);
		$hotelId = prev($keywords);


		$leftJoin = '';
		if($hotelId !== false ) {
			$leftJoin = 'left join #__hotelreservation_hotels h on h.hotel_id = poi.hotel_id';
		}

		$db = JFactory::getDBO();
		$query = "SELECT * 
					FROM #__hotelreservation_points_of_interest poi
					$leftJoin
					WHERE LOWER(REPLACE(poi.name,' ','')) = '".$db->escape($poiName)."'";
		$db->setQuery($query, 0, 1);
		$poi = $db->loadObject();

		if(!empty($poi)){
			$params["option"] = "com_jhotelreservation";
			$params["view"] = "poi";

			if($hotelId !== false ) {
				$params["hotelId"] = $hotelId;
			}
			$params["poid"] = $poi->id;
			$params["Itemid"] = "";
		}
		return $params;
	}
}
