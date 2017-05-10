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

class JHotelReservationModelAccomodationTypes extends JModelLegacy
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function updateAccommodationTypes()
	{
		$ret = true;
		$hotelId = $_POST['hotelId'];
		if( $ret == true )
		{
			$db = JFactory::getDBO();

			$query = "START TRANSACTION";
			$db->setQuery($query);
			$db->queryBatch();
			if( $ret == true )
			{
				$opt_ids = isset( $_POST['accommodationtypeIds'] ) ? $_POST['accommodationtypeIds'] : null;
				$where   = isset( $opt_ids ) && ( ! empty( $opt_ids ) || $opt_ids != '' ) ? ' WHERE id NOT IN (' . implode( ",", $opt_ids ) . ')' : '';
				$db->setQuery (	" DELETE FROM #__hotelreservation_hotel_accommodation_types $where");

				if (!$db->query() )
				{
					// dmp($db);
					$ret = false;
					$e = 'INSERT / UPDATE sql STATEMENT error !';
				}else{
					if(isset($_POST['accommodationtypeNames']))
					{
						foreach ( $_POST['accommodationtypeNames'] as $key => $value )
						{


							//dmp($key);
							$recordId   = isset( $_POST['accommodationtypeIds'][$key] ) ? trim( $_POST['accommodationtypeIds'][$key] ) : 0;
							$recordName = trim( $_POST['accommodationtypeNames'][$key] );
							$recordName = $db->escape( $recordName );


							$db->setQuery( "
													INSERT INTO #__hotelreservation_hotel_accommodation_types
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
								$e   = 'INSERT / UPDATE sql STATEMENT error1 !';
							}

						}
					}
				}
			}

			if( $ret == true )
			{
				$query = "COMMIT";
				$db->setQuery($query);
				$db->queryBatch();
				$m=JText::_('LNG_ACCOMMODATION_TYPES_SAVED',true);
			}
			else
			{
				$query = "ROLLBACK";
				$db->setQuery($query);
				$db->queryBatch();
			}


		}

		$buff 		= $ret ? $this->getHTMLContentHotelAccommodationTypes($hotelId) : '';
			
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="'.($ret ? "0" : "1").'" errorMessage="'.$e.'" mesage="'.$m.'" content_records="'.$buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
	}
	function getHTMLContentHotelAccommodationTypes($hotelId)
	{
		$db = JFactory::getDBO();
		$db->setQuery( "
										SELECT 
											*
										FROM #__hotelreservation_hotel_accommodation_types
										ORDER BY name
										" );
		$accommodationtypes = $db->loadObjectList();
		$db->setQuery( "
														SELECT 
															*
														FROM #__hotelreservation_hotel_accommodation_type_relation where hotelId=".$hotelId );
		$selectedAccommodationTypes 	= $db->loadObjectList();
		$buff = $this->displayAccommodationTypes($accommodationtypes, $selectedAccommodationTypes);
		//var_dump($buff);
		return htmlspecialchars($buff);
	}
	
	function displayAccommodationTypes($accommodationTypes, $selectedAccommodationTypes){
		ob_start();
		?>
					<select id="accommodationtypes" class="chosenAttribute" multiple="multiple" name="accommodationtypes[]">
						<?php
						foreach( $accommodationTypes as $accommodationtype )
						{

							$accommodationtype->name = JHotelUtil::getTranslatedItemName($accommodationtype->name);

							$selected = false;
							foreach($selectedAccommodationTypes as $selectedAccommodationType ){
								if($accommodationtype->id == $selectedAccommodationType->accommodationtypeId)
								$selected =true;
							}
							?>
							<option <?php echo $selected? 'selected="selected"' : ''?> 	value='<?php echo $accommodationtype->id?>'><?php echo $accommodationtype->name ?></option>
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