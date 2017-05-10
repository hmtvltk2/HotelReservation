<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post"  name="searchForm" id="searchForm" >
	<div class='reservation-info' >
		<div class="hotel-info">
			<div class="hotel-image-holder">
				<a href="<?php echo JHotelUtil::getHotelLink($this->hotel) ?>">
					<img class="hotel-image" 
						src='<?php if(isset($this->hotel->pictures[0])) echo JURI::root() .PATH_PICTURES.$this->hotel->pictures[0]->hotel_picture_path?>' 
					/>
				</a>
			</div>

			<div class="hotel-content">								
				<div class="hotel-title">
					<h2 >
						<a href="<?php echo JHotelUtil::getHotelLink($this->hotel) ?>">
							<?php echo stripslashes($this->hotel->hotel_name) ?>
						</a> 
					</h2>
					<span class="hotel-stars">
						<?php
						for ($i=1;$i<=$this->hotel->hotel_stars;$i++){ ?>
							<img  src='<?php echo JURI::root() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>' />
						<?php } ?>
					</span>
				</div>
				
				<div class="hotel-address">
					<?php echo $this->hotel->hotel_address?>, <?php echo $this->hotel->hotel_city?>, <?php echo $this->hotel->hotel_county?>, <?php echo $this->hotel->country_name?>
				</div>
				
				<div class="clear"></div>
				
				<div class="reservation-description">
					<table>
						<tr>
							<td width="60">
								<strong><?php echo JText::_('LNG_ARIVAL',true) ?></strong>
							</td>
							<td>
							<?php
								$data_1 = strtotime( $this->userData->year_start.'-'.$this->userData->month_start.'-'.$this->userData->day_start );
								
								echo JText::_( substr(strtoupper(date( 'l', $data_1)),0,3)). ' ';
								echo date( ' d', $data_1 ) .' ';
								echo JText::_( strtoupper(date( 'F', $data_1))) . ', ';
								echo date( 'Y', $data_1 );
								//echo date( 'l, F d, Y', strtotime( $this->_models['variables']->year_start.'-'.$this->_models['variables']->month_start.'-'.$this->_models['variables']->day_start ) )
							?> 
							</td>
						</tr>
						<tr>
							<td>
								<strong><?php echo JText::_('LNG_DEPARTURE',true) ?></strong>
							</td>
							<td>
							<?php
								$data_2 = strtotime( $this->userData->year_end.'-'.$this->userData->month_end.'-'.$this->userData->day_end );
								echo JText::_( substr(strtoupper(date( 'l', $data_2)),0,3)). ' ';
								echo date( ' d', $data_2 ) .' ';
								echo JText::_( strtoupper(date( 'F', $data_2))) . ', ';
								echo date( 'Y', $data_2 );
								//echo date( 'l, F d, Y', strtotime( $this->_models['variables']->year_start.'-'.$this->_models['variables']->month_start.'-'.$this->_models['variables']->day_start ) )
							?> 
							</td>
						</tr>
						<tr>
							<td>
								<strong><?php echo JText::_('LNG_ADULT_S',true) ?></strong>
							</td>
							<td>
								<?php echo $this->userData->adults > 0? $this->userData->adults.' '.JText::_('LNG_ADULT_S',true) : ""?>
								<?php echo $this->userData->children > 0?  $this->userData->children.' '.JText::_('LNG_CHILD_S',true) : ""?>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?php echo JText::_('LNG_ROOMS',true) ?></strong>
							</td>
							<td>
								<?php echo $this->userData->rooms ?> 
							</td>
						</tr>
					</table>
				</div>
	
				
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<input type="hidden" name="task" 				id="task" 					value="" />
	<input type="hidden" name="tmp" 				id="tmp" 					value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type="hidden" name="hotel_id" 			id="hotel_id" 				value="" />
</form>

	
<script>
		
		function showHotel(hotelId, selectedTab){
			jQuery("#tabId").val(selectedTab);
			jQuery("#tip_oper").val('-1');
			jQuery("#controller").val('');
			jQuery("#task").val('checkAvalability');
			jQuery("#hotel_id").val(hotelId);
			jQuery("#searchForm").submit();
		}
		
</script>
	