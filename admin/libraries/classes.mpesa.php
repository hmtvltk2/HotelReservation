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

set_time_limit(60000);
class MPesaTransaction 
{
	var  $HTTP_RESPONSE_OK 		= array(200,301,302,303);
	const KEY_MAP_ARRAY 		= 'map';

	public $data;
	public $headers 			= array();
	public $gateway_retries 	= 3;
	public $gateway_retry_wait 	= 5; //seconds
	public $environment 		= 'test';

	public $vps_timeout 		= 45;
	public $curl_timeout 		= 90;

	public $gateway_url_live 	= '';
	public $gateway_url_devel 	= '';

	public $raw_response;
	//public $response;
	public $response_arr 		= array();

	public $txn_successful 		= null;
	public $raw_result;

	public $debug 				= false;

	public function __construct() 
	{
		$this->load_config();
		
	}

	public function load_config() 
	{
		if ( defined('MPESA_ORDER_ID') ) 
		{
			$this->data['ORDER_ID'] = constant('MPESA_ORDER_ID');
		}
		
		if ( defined('MPESA_INVOCE') ) 
		{
			$this->data['INVOCE'] = constant('MPESA_INVOCE');
		}
		
		if ( defined('MPESA_TOTAL') ) 
		{
			$this->data['TOTAL'] = constant('MPESA_TOTAL');
		}
		
		if ( defined('MPESA_PHONE_1') ) 
		{
			$this->data['PHONE_1'] = constant('MPESA_PHONE_1');
		}
		
		if ( defined('MPESA_PHONE_2') ) 
		{
			$this->data['PHONE_2'] = constant('MPESA_PHONE_2');
		}
		
		if ( defined('MPESA_EMAIL') ) 
		{
			$this->data['EMAIL'] = constant('MPESA_EMAIL');
		}
	
		if ( defined('MPESA_VENDOR_REF') ) 
		{
			$this->data['VENDOR_REF'] = constant('MPESA_VENDOR_REF');
		}
		
		if ( defined('MPESA_MPESA') ) 
		{
			$this->data['MPESA'] = constant('MPESA_MPESA');
		}
		

	}
	public function __set( $key, $val ) 
	{
		$this->data[$key] = $val;
	}

	public function __get( $key ) 
	{
		if ( isset($this->data[$key]) ) 
		{
			return $this->data[$key];
		}

		return null;
	}

	public function get_gateway_url() 
	{
		if ( strtolower($this->environment) == 'live' ) 
		{
			return $this->gateway_url_live.( substr($this->gateway_url_live,-1)!='/' ? '/' : '' );
		}
		else 
		{
			return $this->gateway_url_devel.( substr($this->gateway_url_devel,-1)!='/' ? '/' : '' );
		}
	}

	public function get_data_string() 
	{
		$query = array();
		
		foreach ( $this->data as $key => $value) 
		{
			if ( $this->debug ) 
			{
				echo "{$key} = {$value}";
			}

			//$query[] = strtoupper($key) . '[' .strlen($value).']='.$value;
			$query[] = strtoupper($key) . '='.$value;
		}

		return implode('&', $query);
	}

	public function before_send_transaction() 
	{

		$this->txn_successful 		= false;
		$this->raw_response 		= null; //reset raw result
		$this->response_arr 		= array();
	} 

	public function reset() 
	{

		$this->txn_successful 		= null;
		$this->raw_response 		= null; //reset raw result
		$this->response_arr 		= array();
		$this->data 				= array();
		$this->load_config();
	} 


	public function send_transaction() 
	{
		try { 

			$this->before_send_transaction();
			$data_string = $this->get_data_string();
			$headers[] = "Content-Type: text/namevalue"; //or text/xml if using XMLPay.
			$headers[] = "Content-Length: " . strlen ($data_string);  // Length of data to be passed 
			$headers[] = "X-VPS-Timeout: {$this->vps_timeout}";
			$headers[] = "X-VPS-Request-ID:" . uniqid(rand(), true);
			$headers[] = "X-VPS-VIT-Client-Type: PHP/cURL";          // What you are using

			$headers = array_merge( $headers, $this->headers );

			if ( $this->debug ) 
			{
				echo  __METHOD__ . ' Sending: ' . $data_string . '';
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->get_gateway_url() );
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($ch, CURLOPT_HEADER, 1);                // tells curl to include headers in response
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        // return into a variable
			curl_setopt($ch, CURLOPT_NOBODY, 1 );
			curl_setopt($ch, CURLOPT_TIMEOUT, 90);              // times out after 90 secs
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);        // this line makes it work under https
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); //adding POST data
			
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);       //verifies ssl certificate
			curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE);       //forces closure of connection when done
			curl_setopt($ch, CURLOPT_POST, 1);       			//data sent as POST

			$i = 0;

			while ($i++ <= $this->gateway_retries) 
			{
		  
				$result 	= curl_exec($ch);
				$headers 	= curl_getinfo($ch);
				
				if (array_key_exists('http_code', $headers) && !in_array($headers['http_code'],$this->HTTP_RESPONSE_OK ) ) 
				{
					sleep($this->gateway_retry_wait);  // Let's wait to see if its a temporary network issue.
				}
				else  
				{
					// we got a good response, drop out of loop.
					break;
				}
			}  

			if ( !array_key_exists('http_code', $headers) || !in_array($headers['http_code'],$this->HTTP_RESPONSE_OK )	 ) 
			{
				throw new InvalidResponseCodeException;
			}

			$this->raw_response = $result;

			$result = strstr($result, "Error::");
			
			$ret 	= array();
			if( strpos($result,'Error::',0)!==false)
			{
				$ret['responce'] = $result;
			}
			else
			{
				while( strlen($result) > 0 )
				{
					$keypos 	= strpos($result,'=');
					$keyval 	= substr($result,0,$keypos);

					// value
					$valuepos 	= strpos($result,'&') ? strpos($result,'&'): strlen($result);
					$valval 	= substr($result,$keypos+1,$valuepos-$keypos-1);

					// decoding the respose
					$ret[$keyval] = $valval;

					$result = substr($result, $valuepos+1, strlen($result) );
				}
			}
			// dmp($data_string);
			// exit;
			if( !isset( $ret['status'] ) )
				$ret['status'] = -1;
			switch( $ret['status'] )
			{
				case 'aei7p7yrx4ae34':	//ok
					$ret['RESULT'] = 0;
					break;
				case 'bdi6p2yy76etrs':	//pending
					$ret['RESULT'] = 1;
					break;
				case 'cr5i3pgy9867e1':	//already use
					$ret['RESULT'] = 2;
					break;
				case 'dtfi4p7yty45wq':	//less
					$ret['RESULT'] = 3;
					break;
				case 'eq3i7p5yt7645e':	//more
					$ret['RESULT'] = 4;
					break;
				case 'fe2707etr5s4wq':	//failed
					$ret['RESULT'] = 5;
					break;
				default:
					$ret['RESULT'] = 6;
					break;
				
			}
			// dmp($ret);
			// exit;
			
			return $ret;
		}
		catch( Exception $e ) 
		{
			// dmp($data_string);
			// exit;
			@curl_close($ch);
			throw $e;
		}
		
	}

	public function response_handler( $response_arr ) 
	{
		try 
		{ 
			$result_code = isset($response_arr['RESULT'])? $response_arr['RESULT'] : ''; // get the result code to validate.

			if ( $this->debug ) 
			{
				echo __METHOD__ . ' response=' . print_r( $response_arr, true) . '';
				echo __METHOD__ . ' RESULT=' . $result_code . '';
			}

			// foreach( $response_arr as $k => $v);
			//	dmp($result_code);
			// exit;
			if ( $result_code == 1 ) //success
			{

				//
				// Even on zero, still check AVS
				//
				
				//
				// Return code was 0 and no AVS exceptions raised
				//
				$this->txn_successful = true;

				parse_str($this->raw_response, $this->response_arr);
				return $this->response_arr;
			}
			else if ($result_code == 1)	//pending 
			{
				throw new TransactionDataException( "Pending, Incoming Mobile Money Transaction Not found" );
			}
			else if ($result_code == 2) //already use
			{
				// Hard decline from bank.
				throw new TransactionDataException( "The code you are attempting to pass has been used already." );
			}
			else if ($result_code == 3 ) //less
			{
				// Voice authorization required.
				throw new TransactionDataException ("The amount that you have sent via mobile money is LESS than what was required to validate this transaction");
			}
			else if ($result_code ==4) //more
			{
				// Issue with credit card number or expiration date.
				$msg = 'The amount that you have sent via mobile money is MORE than what was required to validate this transaction. (Up to the merchant to decide what to do with this transaction; whether to pass itor not)';
				throw new TransactionDataException ($msg);
			}
			else if ($result_code == 5) //failed
			{
				// Issue with credit card number or expiration date.
				$msg = 'Not all parameters have been fulfilled. A notification of this transaction has been sent to the website owner';
				throw new TransactionDataException ($msg);
			}
			else if ($result_code == 6) //
			{
				// Issue with credit card number or expiration date.
				// dmp($response_arr);
				$msg = $response_arr['responce'];
				throw new TransactionDataException ($msg);
				// return $response_arr;
			}

			// Using the Fraud Protection Service.
			// This portion of code would be is you are using the Fraud Protection Service, this is for US merchants only.
			//
			// Throw generic response
			//
			throw new Exception( $response_arr['status'] );
		}
		catch( Exception $e ) 
		{
			throw $e;
		}
	} 

	public function process() 
	{
		try 
		{ 
			return $this->response_handler($this->send_transaction());
		}
		catch( Exception $e ) 
		{
			throw $e;
		}

	}

	public function apply_associative_array( $arr, $options = array() ) 
	{
		try 
		{ 
			$map_array = array();
	 
			if ( isset($options[self::KEY_MAP_ARRAY]) ) 
			{
				$map_array = $options[self::KEY_MAP_ARRAY];
			}

			foreach( $arr as $cur_key => $val ) 
			{
				if( isset($map_array[$cur_key]) ) 
				{
					$cur_key = $map_array[$cur_key];
				}
				else 
				{
					if ( isset($options['require_map']) && $options['require_map'] ) 
					{
						continue;
					}
				}

				$this->data[strtoupper($cur_key)] = $val;

			}
		}
		catch( Exception $e ) 
		{
			throw $e;
		}

	}


}


?>