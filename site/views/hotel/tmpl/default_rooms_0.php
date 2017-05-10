<?php if(count($this->rooms)>0):?>
<br/>
<div id="content" class="table_info rooms">
	<div class="section group layoutnav">
		<div class="col column_5_of_12 smallfont">
			<?php echo JText::_('LNG_ROOMS',true)?>
		</div>
		<div class="col column_2_of_12  smallfont format-text center_text">
			<?php echo JText::_('LNG_CAPACITY_PERS',true)?>
		</div>
		<div class="col column_1_of_12 smallfont center_text">
           <?php echo $this->appSettings->show_children!=0?JText::_('LNG_CHILDREN',true):"&nbsp;"?>
        </div>
		<div class="col column_2_of_12 smallfont center_text ">
		<?php
			$priceType =  JRequest::getVar( 'show_price_per_person');
			if($priceType==1){ 
				echo JText::_('LNG_PRICE_PER_PERSON',true);
			}else if($priceType==0){
				echo JText::_('LNG_PRICE',true);
			}else if($priceType==2){
				echo JText::_('LNG_DISPLAY_WHOLE_PRICE',true);
			}
		?>
		</div>
	</div>
<?php
endif;
//dmp(empty($this->rooms(0)));
foreach( $this->rooms as $room )
{
	?>
	<div class="section group">
                <div class="col simpleLabelsSmall">
                    <?php echo JText::_('LNG_ROOM',true)?>
                </div>
                <div class="col column_5_of_12 simpleItemsSmall margin_left_none">
                    <div>
                    <div class="trigger open more">
                    
                        <?php $itemName =$room->room_name; ?>
                        <a href="#">
                        <?php
                        if( strlen($itemName) > MAX_LENGTH_ROOM_NAME )
                        {

                            ?>
                            <span
                                title			=	"<?php echo $itemName?>"
                                style			=	'cursor:hand;cursor:pointer'
                                onmouseover		= 	"
                                                        var text = jQuery(this).attr('title');
                                                        var posi = jQuery(this).position();
                                                        var top  = posi.top;
                                                        var left = posi.left+5;
                                                        var wid	 = jQuery(this).width();

                                                        jQuery(this).attr('title','');
                                                        jQuery(this).parent().append('<div class=\'poptext\'>'+text.replace('|','<br />')+'</div>');
                                                        jQuery('.poptext').attr('css','TOOLTIP_ROOM_NAME');
                                                        jQuery('.poptext').css(
                                                                            {
                                                                                'left':(left+wid)+'px',
                                                                                'top'			:(top-jQuery('.poptext').height())+'px',
                                                                                'display'		:'none',
                                                                                'position'		:'absolute',
                                                                                'z-index'		:'1000',
                                                                                'padding'		:'5px',
                                                                                'background-color': '#fff'
                                                                            });
                                                        jQuery('.poptext').fadeIn('slow');"
                                onmouseout		= 	"
                                                        var title = jQuery(this).parent().find('.poptext').html();
                                                        jQuery(this).attr('title',title.replace('<br />','|'));
                                                        jQuery(this).parent().find('.poptext').fadeOut('slow');
                                                        jQuery(this).parent().find('.poptext').remove();

                                                    "
                            ><?php echo substr($itemName, 0,MAX_LENGTH_ROOM_NAME); ?>...</span>
                            <?php
                        }
                        else
                            echo $itemName;
                        ?>
                        </a>
                        &nbsp;| 
                        <a class="linkmore" href="#"><icon class="fa fa-chevron-right smallfont"></icon><?php echo JText::_('LNG_MORE_DETAILS',true)?></a>
                        </div>
                    </div>
            </div>
            <div class="col simpleLabelsSmall">
                <?php echo JText::_('LNG_CAPACITY',true)?>
             </div>
            <div class="col column_2_of_12 simpleItemsSmall center_text">
                <?php echo $room->max_adults;?>
            </div>
            <div class="col simpleLabelsSmall">
                <?php echo $this->appSettings->show_children!=0?JText::_('LNG_CHILDREN',true):"&nbsp;"?>
            </div>
            <div class="col column_1_of_12 simpleItemsSmall center_text">
                <?php echo $this->appSettings->show_children!=0?$room->base_children:"&nbsp;"?>
            </div>
            <div class="col simpleLabelsSmall">
                <?php
                $priceType =  JRequest::getVar( 'show_price_per_person');
                if($priceType==1){
                    echo JText::_('LNG_PRICE_PER_PERSON',true);
                }else if($priceType==0){
                    echo JText::_('LNG_PRICE',true);
                }else if($priceType==2){
                    echo JText::_('LNG_DISPLAY_WHOLE_PRICE',true);
                }
                ?>
            </div>
            <div class="col column_2_of_12 priceLayout center_text simpleItemsSmall">
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
                    else {?>
                        &nbsp;
                <?php
                    }
                ?>
            </div>
            <div class="col column_2_of_12 bookButton textRight ">
              
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
	                    ?>
	            
	           <?php 
              	    }else if(!$room->is_disabled){
                ?>
                		
                		<div style="align:center">
	              	         <ul style='margin-top:0px'>
	                            <?php
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
                        <button class="ui-hotel-button bookButton layout right <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'grey' : ''?>"
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
                    <div>
                        <button class=" ui-hotel-button bookButton grey reservation trigger open layout right <?php echo $class ?> <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'not-bookable' : ''?>"
                            name="check-button"
                            value		= "<?php echo $buttonLabel?>"
                            type		= 'button'
                            <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'disabled' : ''?>
                        >
                            <span class="ui-button-text"><?php echo $buttonLabel?></span>
                        </button>
                    </div>
                <?php } ?>
 				
            </div>
	</div>
	<div class="tr_cnt">
		<div class="td_cnt" >
			<div class="cnt">
				<div class="room-description row-fluid">

					<div class="room_main_description span6">

						<?php
						$description = $room->room_main_description; 
						echo $description;
						?>
					</div>
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
				</div>
				
				<div class='picture-container'>
					<div class="thumbs" data-gallery="one">
	  					<?php
	  					if(!empty($room->pictures))
						{
							foreach( $room->pictures as $picture )
							{
								$hotelPicturePath = JURI::base() .PATH_PICTURES.$picture->room_picture_path;
						?>
					        <a href="<?php echo $hotelPicturePath?>" data-gallery="<?php echo $room->room_id?>" style="background-image:url('<?php echo $hotelPicturePath?>')"
							   alt="<?php echo JHotelUtil::setAltAttribute($hotelPicturePath);?>" 
					           title="<?php echo JHotelUtil::setAltAttribute($picture->room_picture_info);?>"></a>
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

<script type="text/javascript">
	jQuery('.thumbs a').touchTouch();
</script>	