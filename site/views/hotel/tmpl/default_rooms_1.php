	<div class="clear">
		<h4>
			<?php echo ucfirst(isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_AVAILABLE_PARKS',true) : JText::_('LNG_AVAILABLE_ROOMS',true)." ".strtolower(JText::_('LNG_FROM',true))) ?>
			<?php 	
				echo "<strong>".JHotelUtil::getDateGeneralFormat($this->userData->start_date)."</strong> ";
				echo JText::_('LNG_TO',true);
				echo " <strong>".JHotelUtil::getDateGeneralFormat($this->userData->end_date)."</strong>";
			?>
		</h4>
	</div>
<br/>
<div id="content">
    <div>
        <?php
	    foreach( $this->rooms as $room )
        {
            ?>
            <div class="section group">
                    <div class="col column_3_of_12">
                        
                        <?php
                        if(!empty($room->pictures) )
                        {
                        ?>

                        <div class="thumbs" data-gallery="one">
                            <div class="room-image listImageContainer">
                                <a href="<?php echo JURI::base() . PATH_PICTURES . $room->pictures[0]->room_picture_path ?>"
                                   data-gallery="<?php echo $room->room_id ?>"
                                   style="background-image:url('<?php echo JURI::base() . PATH_PICTURES . $room->pictures[0]->room_picture_path ?>')"
                                   class="listImage"
                                   title="<?php echo JHotelUtil::setAltAttribute($room->pictures[0]->room_picture_info); ?>">
                                    <span class="icon-overlay"></span>
                                </a>
                            </div>
                        </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="col column_8_of_12 vertical-alignment">
                  	    <div class="itemTitle">
                            <h5><?php echo $room->room_name;?></h5>
                        </div>
                        <div class="room_short_description">
                            <?php echo JHotelUtil::truncate(strip_tags($room->room_main_description), 400, true); ?>
                            ...
                        </div>
                        <div>
                            <div class="trigger open">
                                <a class="linkmore" href="#"><icon class="fa fa-chevron-right"><?php echo JText::_('LNG_MORE',true)?></icon></a>
                            </div>
                        </div>
                    </div>
                    
                     <div class="col column_9_of_12 vertical-alignment right"> 	
                     	<div class="col column_3_of_12 margin_left_none">
				            <?php
	
	                        for($i=1;$i<=$room->max_adults;$i++)
	                            echo "<i class='guest_adult'  title='".JText::_('LNG_ADULTS_MAX')." ".$room->max_adults."'></i>";
	                        if($this->appSettings->show_children!=0){
	                            if($room->base_children>0)
	                                echo " | ";
	                            for($i=1;$i<=$room->base_children;$i++)
	                                echo "<i class='guest_child'  title='".JText::_('LNG_CHILDREN_MAX')." ".$room->base_children."'></i>";
	                        }
	                        ?>
	                    </div>
	                    
				        <div class="col column_6_of_12 margin_left_none">
				        	<div class="roomPrice">
					            <?php
					            $priceType =  JRequest::getVar( 'show_price_per_person');
					            if($priceType==1){
					                echo JText::_('LNG_PRICE_PER_PERSON',true);
					            }else if($priceType==0){
					                echo JText::_('LNG_PRICE',true);
					            }else if($priceType==2){
					                echo JText::_('LNG_DISPLAY_WHOLE_PRICE',true);
					            }
					            else {?>
					                &nbsp;
					            <?php }
				            ?>:
				            </div>
				      
	                        <div class="roomPrice priceStyle">
	                            <?php
	                            if(!$room->is_disabled && !$room->capacityExceeded){
                   				     echo $this->currency;
	                            		                            	
	                                if($priceType==1){
	                                    echo $room->pers_total_price;
	                                }else if($priceType==0){
	                                    echo $room->room_average_display_price;
	                                }else if($priceType==2){
	                                    echo $room->total_price;
	                                }
	                            }
	                            ?>
	                        </div>
	                    </div>
	                    
	                    
	                    <div class="col column_3_of_12 vertical-alignment right">
	                        <?php
	                        $is_checked = false;
		                    $nrDays = JHotelUtil::getNumberOfDays( $this->userData->start_date,$this->userData->end_date);
	
	                        if ($nrDays < $room->min_days) {
	                            $text = JText::_('LNG_MINIMUM_DAYS',true);
	                            $text = str_replace("<<min_days>>",	$room->min_days, $text);
	                            echo "<font style='color:red'>".$text."</font>";
	                        } else if ($nrDays > $room->max_days && $room->max_days!=0) {
	                            $text = JText::_('LNG_MAXIMUM_DAYS',true);
	                            $text = str_replace("<<max_nights>>",	$room->max_days, $text);
	                            echo "<font style='color:red'>".$text."</font>";
	                        }else if($room->capacityExceeded){
	                            echo "<font style='color:red'>".JText::_('LNG_CAPACITY_EXCEDEED',true)."</font>";
	
	                        }else if(!$room->is_disabled){
	                            ?>
								<div style="align:center">
			                        <ul style='margin-top:0px'>
			                            <?php
			                            $crt_room_sel  = 1;
			                            foreach( $this->userData->reservedItems as $i=>$v )
			                            {
			                                $room_ex = explode('|' , $v );
			                                if( $room_ex[0] == 0 && $room_ex[1] == $room->room_id)
			                                {
			                                    ?>
			                                    <li>
			                                        <div style='text-align:center'>
			                       						<?php echo JText::_('LNG_BOOKED')."#:<strong> ".$room_ex[2]."</strong>" ?>
			                                    	</div>
			                                    </li>
			                                    <?php
			                                }
			                            }
			                            ?>
			                        </ul>
			                    </div>
	                            <button class="ui-hotel-button bookButton layout <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'not-bookable' : ''?>"
	                                    id			= 'itemRoomsCapacity_RADIO'
	                                    name		= 'itemRoomsCapacity_RADIO[]'
	                                    type		= 'button'
	                                <?php echo $room->is_disabled? " disabled " : "" ?>
	                                <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'disabled' : ''?>
	                                    onclick 	= 'return bookItem(0,
	                                    <?php echo $room->room_id?>);'
	                                ><span class="ui-button-text"><?php echo JText::_('LNG_BOOK')?></span></button>
	
	                        <?php }else{
	                            $buttonLabel = JText::_('LNG_CHECK_DATES',true);
	                            $class="";
	
	                            if( $room->lock_for_departure){
	                                $buttonLabel = JText::_('LNG_NO_DEPARTURE',true);
	                                $class = "red";
	                            }
	
	                            ?>
	                                <button class=" ui-hotel-button bookButton grey reservation trigger open layout <?php echo $class ?> <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'not-bookable' : ''?>"
	                                        name="check-button"
	                                        value		= "<?php echo $buttonLabel?>"
	                                        type		= 'button'
	                                    <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'disabled' : ''?>
	                                    >
	                                    <span class="ui-button-text"><?php echo $buttonLabel?></span>
	                                </button>
	                        <?php } ?>
	                    </div>
                    </div>
                    
                                        </div>
            <div class="tr_cnt">
                <div class="td_cnt">
                    <div class="cnt">
                        <div class="room-description">
                            <div id="calendar-holder-<?php echo $room->room_id?>" class="room-calendar">
                                <div class="room-loader right"></div>
                                <?php
                                if(isset($this->_models['variables']->availabilityCalendar)){
                                    $calendar =  $this->_models['variables']->availabilityCalendar;
                                    $id= $room->room_id;
                                    echo $calendar[$id];
                                }

                                if(isset($this->_models['variables']->defaultAvailabilityCalendar)){
                                    echo $this->_models['variables']->defaultAvailabilityCalendar;
                                }
                                ?>
                            </div>
                            <div class="room_main_description">

                                <?php
                                $description = $room->room_main_description;
                                echo $description;
                                ?>
                            </div>
                        </div>

                        <div class='picture-container'>
                            <div class="thumbs" data-gallery="one">
                                <?php
                                if(!empty($room->pictures))
                                {
                                    foreach( $room->pictures as $idx=>$picture )
                                    {	
                                    	if($idx==0)
                                    		continue;
                                        $hotelPicturePath = JURI::base() .PATH_PICTURES.$picture->room_picture_path;
                                        
                                        ?>
                                        <a href="<?php echo $hotelPicturePath;?>" data-gallery="<?php echo $room->room_id?>" style="background-image:url('<?php echo $hotelPicturePath;?>')" title="<?php  echo JHotelUtil::setAltAttribute($picture->room_picture_info);?>"></a>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<script type="text/javascript">
	jQuery('.thumbs a').touchTouch();
</script>
