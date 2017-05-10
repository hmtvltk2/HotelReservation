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

class JHotelReservationModelFacilities extends JModelLegacy
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function updateFacilities()
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
				$opt_ids = isset( $_POST['facilityIds'] ) ? $_POST['facilityIds'] : null;
				$where   = isset( $opt_ids ) && ( ! empty( $opt_ids ) || $opt_ids != '' ) ? ' WHERE id NOT IN (' . implode( ",", $opt_ids ) . ')' : '';
				$db->setQuery( " DELETE FROM #__hotelreservation_hotel_facilities $where " );
				if ( ! $db->query() )
				{
					// dmp($db);
					$ret = false;
					$e   = 'INSERT / UPDATE sql STATEMENT error !';
				}

				if ( isset( $_POST['facilityNames'] ) )
				{
					foreach ( $_POST['facilityNames'] as $key => $value )
					{


						//dmp($value);
						$recordId   = isset( $_POST['facilityIds'][$key] ) ? trim( $_POST['facilityIds'][$key] ) : 0;
						$recordName = trim( $_POST['facilityNames'][$key] );
						$recordName = $db->escape( $recordName );


						$db->setQuery( "
										INSERT INTO #__hotelreservation_hotel_facilities
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
				$m= JText::_('LNG_FACILITY_SAVED_SUCCESSFULLY',true);
			}
			else
			{
				$query = "ROLLBACK";
				$db->setQuery($query);
				$db->queryBatch();
			}


		}

		$buff 		= $ret ? $this->getHTMLContentHotelFacilities($hotelId) : '';
			
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="'.($ret ? "0" : "1").'" errorMessage="'.$e.'" mesage="'.$m.'" content_records="'.$buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
	}
	function getHTMLContentHotelFacilities($hotelId)
	{
		$db = JFactory::getDBO();
		$db->setQuery( "
									SELECT 
										*
									FROM #__hotelreservation_hotel_facilities
									ORDER BY name
									" );
		$facilities 	= $db->loadObjectList();
		$db->setQuery( "
											SELECT 
												*
											FROM #__hotelreservation_hotel_facility_relation where hotelId=".$hotelId );
		$selectedFacilities 	= $db->loadObjectList();
		// dmp($facilities);
		$buff = $this->displayFacilities($facilities, $selectedFacilities);
		//var_dump($buff);
		return htmlspecialchars($buff);
	}
	
	function displayFacilities($facilities, $selectedFacilities){
		ob_start();
		?>
			
			<select id="facilities" multiple="multiple" name="facilities[]" class="chosenAttribute">
				<?php
				foreach( $facilities as $facility )
				{
					$facility->name = JHotelUtil::getTranslatedItemName($facility->name);

					$selected = false;
					foreach( $selectedFacilities as $selectedFacility ){
						if($facility->id == $selectedFacility->facilityId)
						$selected =true;
					}
					?>
					<option <?php echo $selected? 'selected="selected"' : ''?> 	value='<?php echo $facility->id?>'><?php echo $facility->name ?></option>
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