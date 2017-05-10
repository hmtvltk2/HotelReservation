<?php 
class CurrencyService{
	
	/*
	 * Convert from source currency to dest currency using google converter
	*/
	public static function getAllCurrencies(){
		$db = JFactory::getDBO();
		$query = ' SELECT
					h.*
					FROM #__hotelreservation_currencies h
					ORDER BY description asc';
		$db->setQuery( $query );
		return $db->loadObjectList();
	}
	
	public static function getCurrency($id){
		if(empty($id))
			return null;
		$db = JFactory::getDBO();
		$query = " SELECT
					h.*
					FROM #__hotelreservation_currencies h
					where currency_id = $id
					";
		$db->setQuery( $query );
		return $db->loadObject();
	}
	
	public static function convertCurrency($amount, $fromCurrency, $toCurrency) {
        if((isset($fromCurrency) || !empty($fromCurrency)) && (isset($toCurrency) && !empty($toCurrency)) && !empty($amount) && $fromCurrency != $toCurrency ) {
        	
        	//check currency code 
        	if(!ctype_alnum($fromCurrency) || !ctype_alnum($toCurrency))
        		return $amount;
        	
        	$app = JFactory::getApplication();
        	$isAdmin = $app->isAdmin();
        	if($isAdmin) return;
        	
        	$currencyIndex = $fromCurrency.$toCurrency;
        	$currencyOffset = 0;
        	
        	if(!empty($_SESSION["userData"]->currency)){
        		$currency = $_SESSION["userData"]->currency;
        	}
        	else{
        		$currency =new stdClass();
        		$currency->$currencyIndex = "";
        	}
        	
        	//avoid calling the service url over and over
          	if(!empty($currency->$currencyIndex)){
        		$convertedAmount = JHotelUtil::fmt($amount*$currency->$currencyIndex,2);
        		return $convertedAmount;
        	}
        	else{        	
        		if (function_exists('curl_init')) {
		        	//fetch currency pair 
		            $url = "https://www.google.com/finance/converter?a=" . $amount . "&from=" . $fromCurrency . "&to=" . $toCurrency;
		            $ch = curl_init();
		            $timeout = 0;
		            curl_setopt($ch, CURLOPT_URL, $url);
		            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1");
		            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		            $rawdata = curl_exec($ch);
	
		            curl_close($ch);
		            $matches = array();
		            preg_match_all("|<span class=bld>(.*)</span>|U", $rawdata, $matches);
		            if (isset($matches[1][0])) {
		                $result = explode(" ", $matches[1][0]);
		                $currency->$currencyIndex = $result[0]/$amount;
                        $currency->$currencyIndex += $currencyOffset;
		                $_SESSION["userData"]->currency = $currency;
                        $newAmount =  JHotelUtil::fmt($amount*$currency->$currencyIndex,2);
		                return  $newAmount;
		            }
        		}
        		else{ //back up code in case curl don't work
        			$amount = urlencode($amount);
        			$fromCurrency = urlencode($fromCurrency);
        			$toCurrency = urlencode($toCurrency);
        			$get = file_get_contents("https://www.google.com/finance/converter?a=$amount&from=$fromCurrency&to=$toCurrency");
        			$get = explode("<span class=bld>", $get);
        			$get[1] = isset($get[1]) ? $get[1] : '';
        			$get = explode("</span>", $get[1]);
        			$converted_amount = preg_replace("/[^0-9\\.]/", null, $get[0]);
                    $currency->$currencyIndex = $converted_amount/$amount;
                    $currency->$currencyIndex += $currencyOffset;
		            $_SESSION["userData"]->currency = $currency;
                    $newAmount =  JHotelUtil::fmt($amount*$currency->$currencyIndex,2);

        		    return $newAmount;
        		}
        	}
        }else if((string)$fromCurrency == (string)$toCurrency){
           return $amount;
        }
        else 
        	return $amount;
    
	}
}

?>