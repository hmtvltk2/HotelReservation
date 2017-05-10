<?php
	class RoomService{
		
		static function getRoomHtmlContent($room, $startDate, $endDate,$showChildren){
			ob_start();
			?>
			
				<div class="roomrate" id="<?php echo $room->offer_id."-".$room->room_id."-".$room->current?>">
					<h2>
						<?php echo (isset( $room->offer_name)?$room->offer_name." - ":"") ?> <?php echo $room->room_name ?>  &nbsp; <span
							onclick="removeRoom('<?php echo $room->offer_id."-".$room->room_id."-".$room->current?>')" class="removeroom">[ <?php echo JText::_('LNG_DELETE',true)?> ]</span>
					</h2>
					<div>
						<input type="hidden" name="reservedItem[]" value="<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>" />
						<input type="hidden" name="current" value="<?php echo $room->current?>" />
						<dt><?php echo JText::_('LNG_ADULTS')?>:</dt> 
						<dd>
							<select name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][adults]" id="room[<?php echo $room->room_id?>][adults]">
								<?php for($i=1; $i<=$room->max_adults;$i++){?>
									<option	value="<?php echo $i?>" <?php echo $i==$room->adults ?'selected="selected"':''?>><?php echo $i?></option>
								<?php } ?>
							</select>
						</dd>
						<?php if($showChildren){?>
							<dt><?php echo JText::_('LNG_CHILDREN')?>:</dt> 
							<dd>
								 <select name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][children]">
								 	<?php for($i=0; $i<=$room->base_children;$i++){?>
										<option	value="<?php echo $i?>" <?php echo $i==$room->children ?'selected="selected"':''?>><?php echo $i?></option>
									<?php } ?>
								</select>
							</dd>	
							<?php } ?>
					</div>
					<div class="nights">
						<h4><?php echo JText::_('LNG_ROOM_RATE')?></h4>
						<ul class="listColumns">
							<?php 
							for( $d = strtotime($startDate);$d < strtotime($endDate); ){
								$dayString = date( 'Y-m-d', $d);
								$price = $room->daily[$dayString]["price_final"];
								if(isset($room->customPrices) && isset($room->customPrices[$dayString])){
									$isCustomPrice = true;
									$price = $room->customPrices[$dayString];
								}
							?>
							<li>
								<?php echo $dayString?>: <input type="text"	name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][price][<?php echo $dayString?>]" id="room_price_<?php echo $room->id?>_<?php echo $dayString?>" onBlur="setCustomPrice()" value="<?php echo $price?>" <?php if(!isSuperUser(JFactory::getUser()->id) && !isManager()) echo "disabled";?>>
								( <?php echo number_format($room->daily[$dayString]["price_final"],2) ?> )
							</li>
							<?php 
								$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
							} 
							?>
						</ul>
					</div>
				</div>
				<?php 
				
				$buff = ob_get_contents();
				ob_end_clean();
				
				return $buff;
			}

        static function getRoomOffers($rooms,$offers,$startDate,$endDate,$searchOfferVoucher){
            foreach($rooms as $key=>$value)
            {
                $roomOffers = HotelService::getRoomOffers($key,$offers,$startDate,$endDate,$searchOfferVoucher);
                $rooms[$key] = $roomOffers;
            }
            return $rooms;
        }
        
	   static function getTotalNrRoomsAvailable($startDate, $endDate){
			$db = JFactory::getDBO();
			$startDate = JHotelUtil::convertToMysqlFormat($startDate); 
			$endDate =  JHotelUtil::shiftDateDown($endDate,1);
			$endDate = JHotelUtil::convertToMysqlFormat($endDate); 
			$query = "	
						select a.hotel_id,sum(IF(a.roomsCustAvailable IS NULL,a.roomsAvailable,a.roomsCustAvailable)) as totalRoomsAvailable from 
						(
							select h.hotel_id,hr.room_id,sum(hrr.availability) as roomsAvailable, sum(hrp.availability) as roomsCustAvailable 
							from #__hotelreservation_hotels h
							inner join #__hotelreservation_rooms hr on h.hotel_id = hr.hotel_id
						    left join #__hotelreservation_rooms_rates hrr on hr.room_id= hrr.room_id
							left join ( select * from #__hotelreservation_rooms_rate_prices 
										where date between '$startDate' and '$endDate' )  hrp on hrr.id = hrp.rate_id
							group by h.hotel_id,hr.room_id
						) a
						group by a.hotel_id
				";
				$db->setQuery( $query );
				$reservationInfos =  $db->loadAssocList('hotel_id');
				if(empty($reservationInfos))
					return 0;
				
			return $reservationInfos;
		}
		
		static function getBeds24Room($roomId){
			$db = JFactory::getDBO();
			$query = "select hr.room_id from #__hotelreservation_rooms hr where hr.beds24_room_id = $roomId";
			$db->setQuery($query );
			$roomId =  $db->loadObject()->room_id;
			return $roomId;
		}
		
	}
