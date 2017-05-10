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
<div class="table-responsive clearfix">
<div id="content" class="table_info offers responsive-utilities">
	
		<div class="section group layoutnav smallfont">
				<div class="col column_6_of_12">
					<p><?php echo $this->hotel->specialOffersTranslation?></p>
				</div>
				<div class="col column_1_of_12 margin_left_none smallfont">
                    <p><?php echo JText::_('LNG_MIN_NIGHTS',true)?></p>
				</div>
				<div class="col column_1_of_12 margin_left_none smallfont center_text">
                    <p><?php echo $this->hotel->capacityTranslation?></p>
                </div>
				<div class="col column_1_of_12 smallfont">
                        <p><?php echo $this->appSettings->show_children!=0?JText::_('LNG_CHILDREN',true):"&nbsp;"?></p>
                </div>
				<div class="col column_1_of_12 smallfont text_right">
                       <p><?php
                            $priceType =  JRequest::getVar( 'show_price_per_person');
                            if($priceType==1){
                                echo JText::_('LNG_PRICE_PER_PERSON',true);
                            }else if($priceType==0){
                                echo JText::_('LNG_PRICE',true);
                            }else if($priceType==2){
                                echo JText::_('LNG_DISPLAY_WHOLE_PRICE',true);
                            }
                        ?></p>
			    </div>
			    <div class="col column_2_of_12 smallfont center_text">
        </div>
        </div>
		<?php
foreach( $this->offers as $offer )
{
	?>
	<div class="section group">
            <div class="col simpleLabelsSmall">
                <?php echo $this->hotel->specialOffersTranslation?>
            </div>
            <div class="col column_6_of_12 simpleItemsSmall margin_left_none">
                <div>
                    <div class="trigger open more">
                        <?php 		$itemName = $offer->offer_name."(".$offer->room_name.")";

                        ?>
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
                                                        jQuery('.poptext').fadeIn('slow');
                                                    "
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
                        &nbsp;|&nbsp;<a class="linkmore" href="#"><icon class="fa fa-chevron-right smallfont"></icon><?php echo JText::_('LNG_MORE_DETAILS',true)?></a>
                     </div>
                </div>
            </div>
            <div class="col simpleLabelsSmall">
                <?php echo JText::_('LNG_MIN_NIGHTS',true)?>
            </div>
            <div class="col column_1_of_12 simpleItemsSmall">
                <?php if($offer->offer_id > 0){
                     echo $offer->offer_min_nights;
                 } ?>
            </div>
            <div class="col simpleLabelsSmall nowrap">
                <?php echo $this->hotel->capacityTranslation?>
            </div>
            <div class="col column_1_of_12 simpleItemsSmall ">
                <?php echo $offer->max_adults;?>
            </div>
            <div class="col simpleLabelsSmall">
                <?php echo $this->appSettings->show_children!=0?JText::_('LNG_CHILDREN',true):"&nbsp;"?>
            </div>
            <div class="col column_1_of_12 simpleItemsSmall">
                <?php echo $this->appSettings->show_children!=0?$offer->base_children:"&nbsp;"?>
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
            <div class="col column_1_of_12 priceLayout simpleItemsSmall" style="margin:1% 0">
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
            <div class="col column_2_of_12 bookButton right textRight">
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

				<font style="color:red">
                <?php
                if($offer->capacityExceeded){
                    echo JText::_('LNG_CAPACITY_EXCEDEED',true);
                }else if(!$offer->capacityFullfilled){
                    echo str_replace("<<min_persons>>",$offer->offer_min_pers, JText::_('LNG_CAPACITY_NOT_MET'));
                }
                else if ($offer->overAvailability){
                	echo str_replace("<<max_nights>>",$offer->offer_max_nights, JText::_('LNG_MAXIMUM_DAYS'));
                	
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
                        class="ui-hotel-button layout bookButton right"
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
                        <button class="ui-hotel-button bookButton grey trigger open layout <?php echo $class?>"
                            class="reservation <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'not-bookable' : ''?>"
                            name="check-button"
                            value		= "<?php echo $buttonLabel ?>"
                            type		= 'button'
                            <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'disabled' : ''?>>
                            <span class="ui-button-text"><?php echo $buttonLabel?></span>
                        </button>
                <?php } ?>
				</font>
            </div>
	</div>
	<div class="tr_cnt">
		<div class="td_cnt">
			<div class="cnt">
				<div class="room-description row-fluid">

					<div class="room_main_description ">
						<?php
							echo $offer->offer_description."<br/>";
	 						echo $offer->offer_content."<br/>";
	 						echo $offer->offer_other_info;
						?>
					</div>
					<div id="calendar-holder-<?php echo ''.$offer->offer_id.''.$offer->room_id?>" class="room-calendar span6 right">
						<div class="room-loader right"></div>
					</div>
				</div>
								
				<div class='picture-container'>
					<div class='picture-container'>
						<div class="thumbs" data-gallery="one">
		  					<?php
							if(!empty($offer->pictures))
							{
								foreach( $offer->pictures as $picture )
								{
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