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
JHTML::_('script', 						'components/'.getBookingExtName().'/assets/js/jquery.selectlist.js');
JHTML::_('script', 							'components/'.getBookingExtName().'/assets/js/jquery.blockUI.js');
JHTML::_('script', 							'components/'.getBookingExtName().'/assets/js/offers.js');

JHTML::_('script', 						'components/'.getBookingExtName().'/assets/datepicker/js/datepicker.js');
JHTML::_('script', 								'components/'.getBookingExtName().'/assets/datepicker/js/eye.js');
JHTML::_('script', 							'components/'.getBookingExtName().'/assets/datepicker/js/utils.js');
JHTML::_('script', 							'components/'.getBookingExtName().'/assets/datepicker/js/layout.js');

jimport('joomla.application.component.controllerform');

class JHotelReservationControllerOffer extends JControllerForm
{
		/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	}


	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save($key= null,$urlVar=null)
	{
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $app      = JFactory::getApplication();
        $model = $this->getModel('Offer');
        $post = JRequest::get( 'post' );
        $context  = 'com_jhotelreservation.edit.offer';
        $task     = $this->getTask();

		$post['extras_ids']	= implode(',', $post['extras_ids']);
		
		$recordId = JRequest::getInt('offer_id');
        // Populate the row id from the session.
        $post['offer_id'] = $recordId;

        if(JRequest::getVar('task')=='save2new'){
        	$post["offer_id"]=0;
        	$post["offer_name"] = $post["offer_name"].'-Copy';
        }

		$post['offer_description'] 	= JRequest::getVar('offer_description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['offer_content'] 		= JRequest::getVar('offer_content', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['offer_other_info'] 	= JRequest::getVar('offer_other_info', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$pictures					= array();
		$rooms						= array();
		$rooms_discounts			= array();
		$rooms_packages				= array();
		$rooms_arrival_options		= array();
		
		foreach( $post as $key => $value )
		{
			if( 
				strpos( $key, 'offer_picture_info' ) !== false 
				||
				strpos( $key, 'offer_picture_path' ) !== false
				||
				strpos( $key, 'offer_picture_enable' ) !== false				
			)
			{
				foreach( $value as $k => $v )
				{
					if( !isset($pictures[$k]) )
						$pictures[$k] = array('offer_picture_info'=>'', 'offer_picture_path'=>'','offer_picture_enable'=>1);
						
						
					$pictures[$k][$key] = $v;
				}
			}
			else if( strpos($key, "room_ids") !== false )
			{
				foreach( $post['room_ids'] as $valueRoomID )
				{
					$rooms[] = array(
									'room_id'						=> $valueRoomID,
									'offer_id'						=> 0,
									'datas'							=> $post['offer_datas'],
									'datae'							=> $post['offer_datae'],
									'offer_price'						=> array()
								);
					
					$r_d['price_type'] 						= $post["price_type_".$valueRoomID];
					$r_d['price_type_day'] 					= $post["price_type_day_".$valueRoomID];
					$r_d['child_price']		 				= $post["child_price_".$valueRoomID];
					$r_d['single_balancing']		 		= $post["single_balancing_".$valueRoomID];
					$r_d['extra_night_price']		 		= $post["extra_night_price_".$valueRoomID];
					$r_d['extra_pers_price']				= $post["extra_pers_price_".$valueRoomID];
					$r_d['base_adults']						= $post["base_adults_".$valueRoomID];
					$r_d['base_children']					= $post["base_children_".$valueRoomID];
					
					if(JRequest::getVar('task')=='save2new')
						$r_d['offer_room_rate_id']				= 0;
					else 	
						$r_d['offer_room_rate_id']				= $post['offer_room_rate_id_'.$valueRoomID];
					
					$week_days 		= $post["week_day_".$valueRoomID];
					//dmp($week_days);
					if(isset($post["week_types_".$valueRoomID]))
						$r_d['week_type'] 		= $post["week_type_".$valueRoomID];
					
					for($i=1; $i<=7; $i++){
						$r_d["price_".$i]= $week_days[$i-1];
					}

					$extra_nights = $post["extra_night_".$valueRoomID];

					for($i=1; $i<=7; $i++){
						$r_d["extra_night_price_".$i]= $extra_nights[$i-1];
					}

					$extra_person = $post["extra_pers_price_".$valueRoomID];

					for($i=1; $i<=7; $i++){
						$r_d["extra_pers_price_".$i]= $extra_person[$i-1];
					}



					//dmp($r_d);
					//exit;
					$rooms_discounts[] = $r_d;
					$rooms[ count($rooms) -1 ]["offer_price"] =  $r_d;
						
					 //exit;
					if( isset( $post['offer_room_package_id_'.$valueRoomID] ) )
					{	
						foreach( $post['offer_room_package_id_'.$valueRoomID] as $packageID )
						{
							$r_p = array( 
											'offer_id'						=>	0,
											'room_id'						=> 	$valueRoomID,
											'package_id'					=>	$packageID
											
							);
							$rooms_packages[] = $r_p;
						}
					}
					
					if( isset( $post['offer_room_arrival_option_id_'.$valueRoomID] ) )
					{	
						foreach( $post['offer_room_arrival_option_id_'.$valueRoomID] as $arrivalOptionID )
						{
							$r_p = array( 
											'offer_id'						=>	0,
											'room_id'						=> 	$valueRoomID,
											'arrival_option_id'				=>	$arrivalOptionID
											
							);
							$rooms_arrival_options[] = $r_p;
						}
					}
				}
				// dmp($rooms);
				// exit;
			}
		}
		
		$post['pictures'] 				= $pictures;
		$post['rooms'] 					= $rooms;
		$post['rooms_packages']			= $rooms_packages;
		$post['rooms_arrival_options']	= $rooms_arrival_options;
		$post['ordering']			= $model->getLastOrder($post["offer_id"]);
		$offer_reservation_cost_val		= $post['offer_reservation_cost_val'];
		$offer_reservation_cost_proc	= $post['offer_reservation_cost_proc'];
		
		// week days when offer is available
		$nr_days		 = 0;
		for( $day=1;$day<=7;$day ++ )
		{
			if( !isset($post["offer_day_$day"]) )
				$post["offer_day_$day"] = 0;
				
			$nr_days += $post["offer_day_$day"];
		}
		
		if( !is_numeric($offer_reservation_cost_val) && $offer_reservation_cost_val !='' )
		{
			$msg = JText::_('LNG_ERROR_OFFER_COST_VALUE',true);
			JError::raiseWarning( 500, $msg );
		}
		else if( $offer_reservation_cost_proc !='' &&  !is_numeric($offer_reservation_cost_proc) )
		{
			$msg = JText::_('LNG_ERROR_OFFER_COST_PERCENT',true);
			JError::raiseWarning( 500, $msg );
		}
		else if( $offer_reservation_cost_proc !='' && ($offer_reservation_cost_proc < 0 || $offer_reservation_cost_proc > 100 ) )
		{
			$msg = JText::_('LNG_ERROR_OFFER_COST_PERCENT',true);
			JError::raiseWarning( 500, $msg );
		}
		else if( $nr_days == 0 )
		{
			$msg = JText::_('LNG_SELECTED_OFFER_DAYS_ERROR',true);
			JError::raiseWarning( 500, $msg );
		}
		else if( $this->checkNumberRooms($post) == false )
		{
			$msg = JText::_('LNG_SELECTED_ROOMS_ERROR',true);
			echo "<script> 
					if(document.getElementById('selected_TAB')) 
						document.getElementById('selected_TAB').value = '1';
				</script>";
			JError::raiseWarning( 500, $msg );
		}

        // Attempt to save the data.
        if (!$model->save($post))
        {
            // Save the data in the session.
            $app->setUserState('com_jhotelreservation.edit.offer.data', $post);
            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            JError::raiseWarning( 500, JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED',true));
            $this->setRedirect('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId));
        }


        $recordId = $model->getState($this->context . '.offer_id');
        $post["offer_id"]= $recordId;
        $model->saveOfferTranslations($post,$task);
        $this->setMessage(JText::_('LNG_OFFER_SAVED',true));
        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
           	case 'save2new':
            		 
                // Set the row data in the session.
                $recordId = $model->getState($this->context . '.offer_id');
                //dmp($recordId);
                $this->holdEditId($context, $recordId);
                $app->setUserState('com_jhotelreservation.edit.offer.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect('index.php?option=' . $this->option . '&view=' . $this->view_item .$this->getRedirectToItemAppend($recordId));
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState('com_jhotelreservation.edit.offer.data', null);


                // Redirect to the list screen.
                $this->setRedirect('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend());
                break;
        }
	}
	
	function checkNumberRooms($post)
	{
		if( !isset($post['room_ids']) )
			$post['room_ids'] = array();
		return count($post['room_ids']) > 0 ?  true : false;
	}




    /**
     * Method to handle the edit button
     * @param null $key
     * @param null $urlVar
     * @return bool
     */
    function edit($key = NULL, $urlVar = null)
    {
        $app = JFactory::getApplication();
        $context = 'com_jhotelreservation.edit.offer';
        $result = parent::edit();

        return true;
    }


    /**
     * Method to handle the new button
     * @return mixed
     */
    public function add()
    {
        // Initialise variables.
        $app		= JFactory::getApplication();
        $context	= 'com_jhotelreservation.edit.offer';

        $result = parent::add();
        if ($result) {
            //dmp("H: ".$hotelId);
            $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=offer'.$this->getRedirectToItemAppend(), false));
        }

        return $result;
    }
	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel($key = NULL)
	{
        $msg = JText::_( 'LNG_OPERATION_CANCELLED' ,true);
		$post 		= JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
			$post['hotel_id'] = 0; 
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=offers&view=offers', $msg );
	}
	
	
	
	function updateThemes()
	{
		$ret = true;
		$offerId = JRequest::getVar('offerId');
		$e="";
		if( $ret == true )
		{
			$db = JFactory::getDBO();
	
			$query = "START TRANSACTION";
			$db->setQuery($query);
			$db->queryBatch();
			if( $ret == true )
			{
				$opt_ids = isset($_POST['themeIds']) || !empty($_POST['themeIds']) || $_POST['themeIds'] != ''?$_POST['themeIds']:array();
				$opt_ids = implode(',', $opt_ids);
				$opt_ids = empty($opt_ids) || !isset($opt_ids)?0:$opt_ids;
				$db->setQuery (	" DELETE FROM #__hotelreservation_offers_themes
									WHERE id NOT IN (".$opt_ids.")");
	
				if (!$db->query() )
				{
					// dmp($db);
					$ret = false;
					$e = 'INSERT / UPDATE sql STATEMENT error !';
				}
	
				foreach($_POST['themeNames'] as $key => $value )
				{
	
	
					//dmp($value);
					$recordId 			= isset($_POST['themeIds'][$key]) ?trim($_POST['themeIds'][$key]) : 0;
					$recordName			= trim($_POST['themeNames'][ $key ]);
                    $recordName         = $db->escape($recordName);



                    $db->setQuery( "
							INSERT INTO #__hotelreservation_offers_themes
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
					if (!$db->query() )
					{
					// dmp($db);
					$ret = false;
					$e = 'INSERT / UPDATE sql STATEMENT error !';
					}
	
					}
				}
	
				if( $ret == true )
				{
				$query = "COMMIT";
				$db->setQuery($query);
				$db->queryBatch();
				$m= JText::_('LNG_THEME_SAVED_SUCCESSFULLY',true);
			}
			else
				{
				$query = "ROLLBACK";
					$db->setQuery($query);
					$db->queryBatch();
				}
	
	
		}
	
		$buff 		= $ret ? $this->getHTMLContentOffersThemes($offerId) : '';
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="'.($ret ? "0" : "1").'" errorMessage="'.$e.'" message="'.$m.'" content_records="'.$buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
	}
	
	function getHTMLContentOffersThemes($offerlId){
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('ot.*');
		$query->from($db->quoteName('#__hotelreservation_offers_themes').'as ot');
		$query->group('ot.id');
		$query->order('ot.name');
		$db->setQuery( $query );
		$themes 	= $db->loadObjectList();

		$db->setQuery( "SELECT * FROM #__hotelreservation_offers_themes_relation where offerId=".$offerlId );
		$selectedThemes = $db->loadObjectList();
		$model = $this->getModel('Offer');
		$buff = $model->displayThemes($themes, $selectedThemes);

		return htmlspecialchars($buff);
	}
}