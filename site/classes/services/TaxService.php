<?php 

class TaxService{
	
	public static function getTaxes($hotelId){
		$db = JFactory::getDBO();
		$query = " SELECT * FROM #__hotelreservation_taxes
					WHERE is_available = 1  AND hotel_id  = $hotelId";		
		$db->setQuery( $query );
		$taxes = $db->loadObjectList();
		
		return $taxes;
	}
	
	function setTaxDisplayPrice(&$taxes){
		foreach ($taxes as &$tax){
			if( $tax->tax_type =='Fixed'){
				$tax->tax_display_value = $this->convertToCurrency($tax->tax_value, $this->itemCurrency->description, $this->currency_selector);
			}
		}
	}
	static function getCityTaxInfo($informations,$currency){
		$taxText ='';
		$tax = JHotelUtil::fmt($informations->city_tax,2);
		if($tax>0){
			if($informations->city_tax_percent){
				$taxText =  JText::_('LNG_CITY_TAX_INFO_PERCENT',true);
			}else{
				$taxText =  JText::_('LNG_CITY_TAX_INFO',true);
				$tax = $currency.' '.$tax;
			}
		
			$taxText = str_replace("<<city-tax>>", $tax, $taxText);
		
			return $taxText;
		}
	}
}