<?php // no direct access
/**
* @copyright	Copyright (C) 2008-2015 CMSJunkie. All rights reserved.
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

?>
	<div>
		<h4>
            <?php echo $hotel->specialOffersTranslation;
			echo "<strong>".JHotelUtil::getDateGeneralFormat($this->userData->start_date)."</strong> ";
			echo JText::_('LNG_TO',true);
			echo " <strong>".JHotelUtil::getDateGeneralFormat($this->userData->end_date)."</strong>";
		?>
		</h4>
	</div>

<div class="table-responsive clearfix">
	<div id="content" class="table_info offers detailedView">
    
		<?php
	foreach( $this->offers as $offer )
	{
	?>
	<div class="section group">
            <div class="col column_3_of_12">
                   

                    <?php if(!empty($offer->pictures) )
                        {
                        ?>

                        <div class="thumbs" data-gallery="one">
                            <div class="room-image listImageContainer">
                                <a class="listImage" href="<?php echo JURI::base().PATH_PICTURES.$offer->pictures[0]->offer_picture_path?>" data-gallery="<?php echo $offer->offer_id?>" style="background-image:url('<?php echo JURI::base().PATH_PICTURES.$offer->pictures[0]->offer_picture_path ?>')"
                                   title="<?php echo JHotelUtil::setAltAttribute($offer->pictures[0]->offer_picture_info);?>"> </a>
                                <span class="icon-overlay"></span>
                            </div>
                        </div>
                        <?php
                        }
                    ?>
            </div>
            <div class="col column_8_of_12">
           			<div class="itemTitle">
                        <h5><?php echo $offer->offer_name." (".$offer->room_name.")";?></h5>
                    </div>
                    <div class="room_short_description">
                        <?php echo substr(strip_tags($offer->offer_short_description), 0, 100); ?>...
                    </div>
                <div>
                    <div class="trigger open">
                        <a class="linkmore" href=""><icon class="fa fa-chevron-right"><?php echo JText::_('LNG_MORE',true)?></icon></a>
                    </div>
                </div>
            </div>
            <div class="col column_9_of_12 right">
	            <div class="col column_2_of_12 ">
		            <?php echo JText::_('LNG_MIN_NIGHTS').": ";
		             if($offer->offer_id > 0){
	                     echo "<b>".$offer->offer_min_nights."</b>";
	                 } ?>
		        </div>
		
		        <div class="col column_3_of_12">
		                <?php
		                     for($i=1;$i<=$offer->max_adults;$i++)
		                        echo "<i class='guest_adult'  title='".JText::_('LNG_ADULTS_MAX')." ".$offer->max_adults."'></i>";
		                     if($this->appSettings->show_children!=0){
		                            if($offer->max_children>0)
		                                echo " | ";
		                            for($i=1;$i<=$offer->max_children;$i++)
		                                echo "<i class='guest_child'  title='".JText::_('LNG_CHILDREN_MAX')." ".$offer->base_children."'></i>";
		                    }
	               		 ?>
		        </div>
		        
		        
		        <div class="col column_4_of_12">
		                <?php
		                $priceType =  JRequest::getVar( 'show_price_per_person');
		                if($priceType==1){
		                    echo JText::_('LNG_PRICE_PER_PERSON',true);
		                }else if($priceType==0){
		                    echo JText::_('LNG_PRICE',true);
		                }else if($priceType==2){
		                    echo JText::_('LNG_DISPLAY_WHOLE_PRICE',true);
		                }
		                ?>:
		            <div class="roomPrice priceStyle">
	                <?php
	                    if(!$offer->is_disabled && !$offer->capacityExceeded){
                      	  	echo $this->currency;
	                    		                    	
                    		if($priceType==1){
	                            echo $offer->pers_total_price;
	                        }else if($priceType==0){
	                            echo $offer->offer_average_display_price;
	                        }else if($priceType==2){
	                            echo $offer->total_price;
	
	                        }
	                    }
	                    else {?>
	                        &nbsp;
	                    <?php }
	                ?>
	                </div>
	            </div>
	            <div class="col column_3_of_12  right vertical-alignment">
	                <?php
	                    $cheie_offer_room 	= $offer->offer_id."_".$offer->room_id;
	                    $offer_offer_room 	= $offer->offer_id."|".$offer->room_id;
	
	                    ?>
	                    <input
	                        type	=	"hidden"
	                        name	=	"items_reserved_tmp_<?php echo $cheie_offer_room?>"
	                        id		=	"items_reserved_tmp_<?php echo $cheie_offer_room?>"
	                        value	=	"<?php echo $offer_offer_room?>"
	                    />
	
	                <?php
	
	                if($offer->capacityExceeded){
	                    echo "<font style='color:red'>".JText::_('LNG_CAPACITY_EXCEDEED',true)."</font>";
	                }else if(!$offer->capacityFullfilled){
	                    echo "<font style='color:red'>".str_replace("<<min_persons>>",$offer->offer_min_pers, JText::_('LNG_CAPACITY_NOT_MET'))."</font>";
	                }
	                else if(!$offer->is_disabled ){
	                ?>
	                
			                 <div align="center" class="clear">
                                <ul>
                                    <?php
                                    foreach ($this->userData->reservedItems as $i => $v) {
                                        $room_ex = explode('|', $v);
                                        if ($room_ex[0] >0 && $room_ex[0] == $offer->offer_id && $room_ex[1] == $offer->room_id) {
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
	                        <button
	                            class="ui-hotel-button layout bookButton"
	                            id			= 'itemRoomsCapacity_RADIO'
	                            name		= 'itemRoomsCapacity_RADIO[]'
	                            type		= 'button'
	                            <?php echo $offer->is_disabled? " disabled " : "" ?>
	                            <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'disabled' : ''?>
	                            onclick 	= 	'return bookItem(
	                                                <?php echo $offer->offer_id?>,
	                                                <?php echo $offer->room_id?>
	                                            );
	                                            '
	
	                        >
	                            <span class="ui-button-text"><?php echo JText::_('LNG_BOOK',true)?></span>
	                        </button>
	
	                <?php }else{
	                    $buttonLabel = JText::_('LNG_CHECK_DATES',true);
	
	                    $class="";
	                    if( $offer->lock_for_departure){
	                        $buttonLabel = JText::_('LNG_NO_DEPARTURE',true);
	                        $class = "red";
	                    }
	
	                    ?>
	                        <button class="ui-hotel-button grey trigger open layout bookButton <?php echo $class?>"
	                            class="reservation <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'not-bookable' : ''?>"
	                            name="check-button"
	                            value		= "<?php echo $buttonLabel ?>"
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
		<div class="td_cnt"  >
			<div class="cnt">
				<div class="room-description">
					<div id="calendar-holder-<?php echo ''.$offer->offer_id.''.$offer->room_id?>" class="room-calendar">
						<div class="room-loader right"></div>

					</div>
					<div class="room_main_description">
						<?php
							echo $offer->offer_description."<br/>";
	 						echo $offer->offer_content."<br/>";
	 						echo $offer->offer_other_info;
						?>
					</div>
				</div>
								
				<div class='picture-container'>
					<div class='picture-container'>
						<div class="thumbs" data-gallery="one">
							<?php
							if(!empty($offer->pictures))
							{
								foreach( $offer->pictures as  $idx =>$picture )
								{
									if($idx==0)
										continue;
									$offerPicturePath = JURI::base().PATH_PICTURES.$picture->offer_picture_path;
							?>
						        <a href="<?php echo $offerPicturePath;?>" data-gallery="<?php echo $offer->offer_id?>" style="background-image:url('<?php echo $offerPicturePath;?>')"
						           title="<?php  echo JHotelUtil::setAltAttribute($picture->offer_picture_info);?>"
						           alt="<?php  echo JHotelUtil::setAltAttribute($offerPicturePath);?>" 
						           ></a>
				        	<?php 
								}
							} 
							?>
		        		</div>
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