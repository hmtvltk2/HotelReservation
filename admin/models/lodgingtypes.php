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

class JHotelReservationModelLodgingTypes extends JModelLegacy
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function updateTypes()
	{
		$ret = true;
		$e="";
		$m="";
		$hotelId = $_POST['hotelId'];
		if( $ret == true )
		{
			$db = JFactory::getDBO();

			$query = "START TRANSACTION";
			$db->setQuery($query);
			$db->queryBatch();
			if( $ret == true )
			{
				$opt_ids = isset( $_POST['typeIds'] ) ? $_POST['typeIds'] : null;
				$where   = isset( $opt_ids ) && ( ! empty( $opt_ids ) || $opt_ids != '' ) ? ' WHERE id NOT IN (' . implode( ",", $opt_ids ) . ')' : '';
				$db->setQuery (	" DELETE FROM #__hotelreservation_hotel_types $where");

				if (!$db->query() )
				{
					// dmp($db);
					$ret = false;
					$e = 'INSERT / UPDATE sql STATEMENT error !';
				}

				if(isset($_POST['typeNames']))
				{
					foreach ( $_POST['typeNames'] as $key => $value )
					{


						// dmp($key);
						$recordId   = isset( $_POST['typeIds'][$key] ) ? trim( $_POST['typeIds'][$key] ) : 0;
						$recordName = trim( $_POST['typeNames'][$key] );
						$recordName = $db->escape( $recordName );


						$db->setQuery( "
											INSERT INTO #__hotelreservation_hotel_types
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
				$m=JText::_('LNG_TYPES_SAVED',true);
			}
			else
			{
				$query = "ROLLBACK";
				$db->setQuery($query);
				$db->queryBatch();
			}


		}

		$buff 		= $ret ? $this->getHTMLContentHotelTypes($hotelId) : '';
			
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="'.($ret ? "0" : "1").'" errorMessage="'.$e.'" mesage="'.$m.'" content_records="'.$buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
		
	}
	function getHTMLContentHotelTypes($hotelId)
	{
		$db = JFactory::getDBO();
		$db->setQuery( "
									SELECT 
										*
									FROM #__hotelreservation_hotel_types
									ORDER BY name
									" );
		$types 	= $db->loadObjectList();
		$db->setQuery( "
												SELECT 
													*
												FROM #__hotelreservation_hotel_type_relation where hotelId=".$hotelId );
		$selectedTypes 	= $db->loadObjectList();
		$buff = $this->displayTypes($types, $selectedTypes);
		//var_dump($buff);
		return htmlspecialchars($buff);
	}
	
	
	function displayTypes($types, $selectedTypes){
		ob_start();
		?>
			<select id="types" name="types[]" class="types-select chosenAttribute">
				<option value=""><?php echo JText::_("LNG_SELECT_TYPE");?></option>
				<?php
				foreach( $types as $type )
				{
					$type->name = JHotelUtil::getTranslatedItemName($type->name);

					$selected = false;
					foreach($selectedTypes as $selectedType ){
						if($type->id == $selectedType->typeId)
						$selected =true;
					}
					?>
					<option <?php echo $selected? 'selected="selected"' : ''?> 	value='<?php echo $type->id?>'><?php echo $type->name ?></option>
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