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

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class JHotelReservationModelPaymentOptions extends JModelLegacy
{
	
	function __construct()
	{
		parent::__construct();
	}
	function updatePaymentOptions()
	{
		$ret = true;
		$hotelId = $_POST['hotelId'];
		$e = '';
		$m = '';
		if( $ret == true )
		{
			$db = JFactory::getDBO();
	
			$query = "START TRANSACTION";
			$db->setQuery($query);
			$db->queryBatch();
			if( $ret == true )
			{
				$opt_ids = isset( $_POST['paymentOptionIds'] ) ? $_POST['paymentOptionIds'] : null;
				$where   = isset( $opt_ids ) && ( ! empty( $opt_ids ) || $opt_ids != '' ) ? ' WHERE id NOT IN (' . implode( ",", $opt_ids ) . ')' : '';
				$db->setQuery (	" DELETE FROM #__hotelreservation_hotel_payment_options $where");
	
				if (!$db->query() )
				{
					// dmp($db);
					$ret = false;
					$e = 'INSERT / UPDATE sql STATEMENT error !';
				}
				if ( isset( $_POST['paymentOptionNames'] ) )
				{
					foreach ( $_POST['paymentOptionNames'] as $key => $value )
					{


						//dmp($value);
						$recordId   = isset( $_POST['paymentOptionIds'][$key] ) ? trim( $_POST['paymentOptionIds'][$key] ) : 0;
						$recordName = trim( $_POST['paymentOptionNames'][$key] );
						$recordName = $db->escape( $recordName );


						$db->setQuery( "
												INSERT INTO #__hotelreservation_hotel_payment_options
												(
													id,
													name
												)
												VALUES
												(
													'$recordId',
													'$recordName'
													
												)
												ON DUPLICATE KEY UPDATE
													id 				= '$recordId',
													name			= '$recordName'
												" );
						//dmp($db);
						if ( ! $db->query() )
						{
							// dmp($db);
							$ret = false;
							$e   = 'INSERT / UPDATE sql STATEMENT error !';
						}

					}
				}
			}
	
			if( $ret == true )
			{
				$query = "COMMIT";
				$db->setQuery($query);
				$db->queryBatch();
				$m="Payment option saved successfully!";
			}
			else
			{
				$query = "ROLLBACK";
				$db->setQuery($query);
				$db->queryBatch();
			}
	
	
		}
	
		$buff 		= $ret ? $this->getHTMLContentHotelPaymentOptions($hotelId) : '';
			
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="'.($ret ? "0" : "1").'" errorMessage="'.$e.'" mesage="'.$m.'" content_records="'.$buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
	}
	function getHTMLContentHotelPaymentOptions($hotelId)
	{
		$db = JFactory::getDBO();
		$db->setQuery( "
								SELECT 
									*
								FROM #__hotelreservation_hotel_payment_options
								ORDER BY name
								" );
		$paymentOptions 	= $db->loadObjectList();
		$db->setQuery( "
										SELECT 
											*
										FROM #__hotelreservation_hotel_payment_option_relation where hotelId=".$hotelId );
		$selectedPaymentOptions 	= $db->loadObjectList();
		// dmp($paymentOptions);
		$buff = $this->displayPaymentOptions($paymentOptions, $selectedPaymentOptions);
		//var_dump($buff);
		return htmlspecialchars($buff);
	}
	function displayPaymentOptions($paymentOptions, $selectedPaymentOptions){
		ob_start();
		?>
			
			<select id="paymentOptions" multiple="multiple" class="chosenAttribute" name="paymentOptions[]">
				<?php
				foreach( $paymentOptions as $paymentOption )
				{

					$paymentOption->name = JHotelUtil::getTranslatedItemName($paymentOption->name);

					$selected = false;
					if(count($selectedPaymentOptions)>0)
					foreach( $selectedPaymentOptions as $selectedPaymentOption ){
						if($paymentOption->id == $selectedPaymentOption->paymentOptionId)
						$selected =true;
					}
					?>
					<option <?php echo $selected? 'selected="selected"' : ''?> 	value='<?php echo $paymentOption->id?>'><?php echo $paymentOption->name ?></option>
					<?php
					}
					?>
			</select>
			<?php 
			$buff = ob_get_contents();
			ob_end_clean();
			return $buff;
		}
	
}
?>