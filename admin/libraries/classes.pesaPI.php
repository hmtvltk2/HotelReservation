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

$path_1 			= $_SERVER['DOCUMENT_ROOT'];

$path_2 			= getcwd();//dirname( realpath( __FILE__ ) );
$path_3 			= dirname( realpath( __FILE__ ) );

$path_1				= str_replace( "/", "\\", $path_1);
$path_2				= str_replace( "/", "\\", $path_2);
$path_3				= str_replace( "/", "\\", $path_3);

$path_2				= str_replace( $path_1, "", $path_2);
$path_3				= str_replace( $path_1, "", $path_3);

$arr_path_2			= explode("\\", $path_2);
$arr_path_3			= explode("\\", $path_3);
//$a = glob( str_repeat("..".DS, count($arr_path) ) ."*.*");
//dmp($a);
$str_return_path_2 	= str_repeat("..".DS, count($arr_path_2) )."include";
$str_return_path_3 	= str_repeat("..".DS, count($arr_path_3) )."include";
set_include_path("$str_return_path_2:$str_return_path_3:".get_include_path());
//dmp(get_include_path());

if( !is_file( $str_return_path_2."PLUSPEOPLE".DS."mpesa_autoload.php" ) && !is_file( $str_return_path_3."PLUSPEOPLE".DS."mpesa_autoload.php" ) )
	return;
require_once("PLUSPEOPLE".DS."mpesa_autoload.php");
spl_autoload_preregister('mpesa_autoload');
//spl_autoload_register('mpesa_autoload');

class PesaPITransaction 
{
	public function __construct() 
	{
		//$this->load_config();
	}
	var $response_arr;
	function process($name, $phone, $amount, $receipt)
	{
		$this->response_arr = array('RESULT'=>0, 'RESPONCE'=>'', 'id'=>0);
		try 
		{
			$pesa 			= new PLUSPEOPLE\PesaPi\PesaPi();
			$transactions 	= $pesa->locateByReceipt($receipt);
			dmp($transactions[0]);
			if( count($transactions) ==0 )
			{
				$this->response_arr = array('RESULT'=>'1', 'RESPONCE'=>'Pending, Incoming Mobile Money Transaction Not found ');
				throw new Exception( 'Pending, Incoming Mobile Money Transaction Not found ');		
			}
			else
			{
				$transaction = $transactions[0];
				if( 
					$transaction->getPhonenumber() != $phone 
				)
				{
					$this->response_arr = array('RESULT'=>'8', 'RESPONCE'=>'Invalid phone');
					throw new Exception( 'Invalid phone');			
				}
				else if( 
					$transaction->getName() 		!= $name 
				)
				{
					$this->response_arr = array('RESULT'=>'7', 'RESPONCE'=>'Invalid name');
					throw new Exception( 'Invalid name');			
				}
				else if( 
					$transaction->getAmount() 		< $amount 
				)
				{
					$this->response_arr = array('RESULT'=>'3', 'RESPONCE'=>'Pending, Incoming Mobile Money Transaction Not found ');
					throw new Exception( 'Pending, Incoming Mobile Money Transaction Not found');				
				}
				else if( 
					$transaction->getAmount() 		> $amount 
				)
				{
					$this->response_arr = array('RESULT'=>'4', 'RESPONCE'=>'Pending, Incoming Mobile Money Transaction Not found ');
					throw new Exception( 'Pending, Incoming Mobile Money Transaction Not found');			
				}
				
				$this->response_arr['id'] = $transaction->getId();
			}
		}
		catch( Exception $e ) 
		{
			throw $e;
		}
		
		return $this->response_arr;
		
	}
}


?>