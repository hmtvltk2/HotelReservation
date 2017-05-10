<?php
$index =0;
$offer_id = 0;
$roomOffers = array();
$roomOs = array();
$rooms = array();
$OffersFound = false;
$extraOptions = 0;
$airport = 0;
$excursion = '';
$roomNumber = 2;
$roomsFound = true;
$capacityExceededOffer = false;
$capacityFullfilledOffer = true;
foreach( $this->rooms as $room ) {
    $rooms[$room->room_id] = array();

    if (count($this->offers) > 0) {
        foreach ($this->offers as $offer) {
             if ($offer->room_id == $room->room_id) {
                $OffersFound = $this->appSettings->is_enable_offers && $offer->room_id == $room->room_id?true:false;
            }
        }
    }
}

$rooms = RoomService::getRoomOffers($rooms,$this->offers,$this->userData->start_date,$this->userData->end_date,$this->userData->voucher);

?>
    <div class="container-room clearfix">
            <div class="row clearfix">
                <div class="nav" id="headerRooms">
                    <div class="col-xs-3">
                        <p class="paragraph">
                            <?php echo JText::_('LNG_ROOM_TYPE')?>
                        </p>
                    </div>
                    <div  class="col-xs-1">
                        <p class="paragraph">
                            Max
                        </p>
                    </div>
                    <div class="col-xs-2">
                        <p class="paragraph" <?php echo  $OffersFound==false?'style="display:none"':''; ?>>
                            Options
                        </p>
                    </div>
                    <!-- check here if the room has an offer   -->
                    <div class="col-xs-3">
                        <p class="paragraph" <?php echo  $OffersFound==false?'style="display:none"':''; ?> >
                			<?php echo $this->hotel->specialOffersTranslation?>
                        </p>
                    </div>
                    <div class="col-xs-2 ">
                        <p class="labelRoomNav paragraph">
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
                            ?>
                        </p>
                    </div>
                </div>
                <?php
                $index = 0;

                foreach( $this->rooms as $room ) {
                    $daily = $room->daily;
                    $price_per_person = 0;
                    $offerCount = 0; 
                    $roomDisplay = "display:block";
                    $roomsLeftDisplay = "display:block";
                    if ($price_per_person == 0)
                        $price_per_person = $priceType;

	                if(isset($room->nrRoomsLeft))
	                {
		                if ($room->nrRoomsLeft == 0 )
		                {
			                $roomsLeftDisplay = "display:none";
		                }
	                }

                    if (!$room->front_display ) {
                         $roomDisplay = "display:none";
                    }

                    $capacityExceeded = false;
                    if ((isset($userData->roomGuests) && $userData->roomGuests[count($this->userData->reservedItems)] > $room->max_adults)) {
                        $capacityExceeded = true;
                    } else if (!isset($userData->roomGuests) && ($room->max_adults < $this->userData->adults)) {
                        $capacityExceeded = true;
                    } else if (($this->appSettings->show_children) && (!empty($userData->roomGuestsChildren) && isset($userData->roomGuestsChildren[count($this->userData->reservedItems)]) && $userData->roomGuestsChildren[count($this->userData->reservedItems)] > $room->base_children)) {
                        $capacityExceeded = true;
                    } else if (($this->appSettings->show_children) && !isset($userData->roomGuestsChildren) && ($room->base_children < $this->userData->children)) {
                        $capacityExceeded = true;
                    }
                    $grand_total = 0;

                    $singleRoom = empty($rooms[$room->room_id])||$this->appSettings->is_enable_offers==false? true : false;
                    foreach ($room->daily as $daily) {
                        $p = $daily['display_price_final'];
                        $grand_total += $p;
                    }
                    //dmp($rooms[$room->room_id]);
                    $roomHeaderDisplay = (!$room->front_display && $singleRoom)?"display:none":"";
                    $offerWithVoucher = 0;
                    if($this->userData->voucher != ''){
                        if(!empty($rooms[$room->room_id])){
                            $offerWithVoucher =  count($rooms[$room->room_id]);
                        }elseif (empty($rooms[$room->room_id])){
                                continue;
                        }
                    }

                    ?>
                    <div class="room_row clearfix" id="room_row" style="<?php echo $roomHeaderDisplay;?>">
                        <div class="col-xs-3" id="roomImage_Info" >
                            <div class="panel" id="panel">
                                <div id="panel_header" class="panel-heading room_panel">
                                    <h3 class="panel-title"><?php echo $room->room_name; ?></h3>
                                </div>
                                <div id="panel_body" class="panel-body">
                                    <?php
                                    if (!empty($room->pictures)) {
                                        ?>
                                    <div class="thumb" data-gallery="one">
                                        <a  href="<?php echo JURI::base().PATH_PICTURES.$room->pictures[0]->room_picture_path ?>"
                                           data-gallery="<?php echo $room->room_id ?>"
                                            title="<?php echo JHotelUtil::setAltAttribute($room->pictures[0]->room_picture_path); ?>">
                                            <img class="img-thumbnail"
                                                 alt="<?php echo JHotelUtil::setAltAttribute($room->pictures[0]->room_picture_path);?>"
                                                 src="<?php echo JURI::base().PATH_PICTURES.$room->pictures[0]->room_picture_path ?>" >
                                        </a>
                                    </div>

                                        <?php
                                    }
                                    ?>
                                    
                                </div>
                                <div class="trigger open panel-footer">
                                	<div class="description">
                                        <?php echo JHotelUtil::truncate(strip_tags($room->room_main_description), 70, true); ?>
                                        ...
                                    </div>
                                    	<a class="linkmore blue " id="linkmore" href="javascript:void(0)"><icon class="fa fa-chevron-right"><?php echo JText::_('LNG_MORE', true) ?></icon></a>
                                </div>
                            </div>
                        </div>

                        <?php
                        if ($this->appSettings->is_enable_offers) {
                            if(isset($rooms[$room->room_id]) && count($rooms[$room->room_id]) > 0) {
                                foreach ($rooms[$room->room_id] as $key => $roomOffer) {
                                    if ($roomOffer->room_id == $room->room_id) {
                                    	$capacityExceededOffer = false;
                                    	$capacityFullfilledOffer = true;
                                        $offer_price_per_person = 0;
                                        if (isset($daily[0]) && isset($daily[0]["discounts"]) && isset($daily[0]["discounts"][0])) {
                                            if (isset($daily[0]["discounts"][0]->offer_pers_price))
                                                $offer_price_per_person = $daily[0]["discounts"][0]->offer_pers_price;
                                        }

                                        if ($offer_price_per_person == 0)
                                            $offer_price_per_person = $roomOffer->price_type;


                                        if (isset($userData->roomGuests) & $this->userData->adults > $roomOffer->max_adults) {
                                            $capacityExceededOffer = true;
                                        } else if (!isset($userData->roomGuests) && $roomOffer->max_adults < $this->userData->adults) {
                                            $capacityExceededOffer = true;
                                        } else if ($roomOffer->offer_min_pers > ($this->userData->adults + $this->userData->children)) {
                                            $capacityFullfilledOffer = false;
                                        } else if (($this->appSettings->show_children) && (!empty($userData->roomGuestsChildren) && isset($userData->roomGuestsChildren[count($this->userData->reservedItems)]) && $userData->roomGuestsChildren[count($this->userData->reservedItems)] > $roomOffer->base_children)) {
                                            $capacityExceededOffer = true;
                                        } else if (($this->appSettings->show_children) && !isset($userData->roomGuestsChildren) && ($roomOffer->base_children < $this->userData->children)) {
                                            $capacityExceededOffer = true;
                                        }
                                        $offer_grand_total = 0;
                                        foreach ($roomOffer->daily as $daily) {
                                            $p = $daily['display_price_final'];
                                            $offer_grand_total += $p;
                                        }
                                        $offerCount++;
                                        ?>
                                        <div
                                            class="offerContainer col-xs-9 <?php echo !$room->front_display || !$OffersFound || $offerWithVoucher == 1 ? 'tableCellSingle' : '' ?>">
                                            <div class="col-xs-2 tableCell" id="persons">
                                                <div class="spaceBoxTop text-center">
                                                    <?php

                                                    for ($i = 1; $i <= $roomOffer->max_adults; $i++)
                                                        echo "<i class='guest_adult'  title='" . JText::_('LNG_ADULTS_MAX') . " " . $roomOffer->max_adults . "'></i>";
                                                    if ($this->appSettings->show_children != 0) {
                                                        if ($roomOffer->base_children > 0) {
                                                            echo "<i>|</i>";
                                                            for ($i = 1; $i <= $roomOffer->base_children; $i++)
                                                                echo "<i class='guest_child'  title='" . JText::_('LNG_CHILDREN_MAX') . " " . $roomOffer->base_children . "'></i>";
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-xs-3 tableCell" id="options">

                                                <?php if ($OffersFound) {
                                                    if (isset($roomOffer->airport) && !empty($roomOffer->airport)) {
                                                        ?>
                                                        <div class="options optionInfo">
                                                            <?php echo '<p>' . JText::_('LNG_AIRLINE', true) . ' : </p>&nbsp; ' . $roomOffer->airport; ?>
                                                        </div>
                                                    <?php }
                                                    if (isset($roomOffer->excursions) && !empty($roomOffer->excursions)) {
                                                        ?>
                                                        <div class="options">
                                                            <?php echo '<p>' . JText::_('LNG_EXCURSION_TYPE', true) . ' : </p>&nbsp;' . $roomOffer->excursions; ?>
                                                        </div>
                                                    <?php }
                                                    if (isset($roomOffer->extraOption) && !empty($roomOffer->extraOption)) {
                                                        ?>
                                                        <div class="options">
                                                            <?php echo '<p>' . JText::_('LNG_EXTRAS', true) . ' :</p>&nbsp;' . $roomOffer->extraOption; ?>
                                                        </div>
                                                    <?php }
                                                } ?>
                                            </div>
                                            <div class="col-xs-3 tableCell"
                                                 id="offers">
                                                <div
                                                    class="spaceBoxTop discount-Small text-center" <?php echo $OffersFound == false ? 'style="display: none"' : ''; ?>>
                                                    <i class="fa fa-tag red"></i>
                                                <span
                                                    class="special_offer_label"><?php echo $roomOffer->offer_name ?></span>


                                                    <p id="offerDeal" class="red text-center">
                                                    	<span class="percentDiscount">
	                                                        <?php
	                                                        if (!$roomOffer->is_disabled && !$capacityExceededOffer) {
	                                                        if ($priceType == 1) {
	                                                            echo JHotelUtil::discountPricePercentage($roomOffer->offer_average_price, $room->room_average_display_price);
	                                                        } elseif ($priceType == 0) {
	                                                            echo JHotelUtil::discountPricePercentage($roomOffer->pers_total_price, $room->pers_total_price);
	                                                        } elseif ($priceType == 2) {
	                                                            echo JHotelUtil::discountPricePercentage($roomOffer->total_price, $room->total_price);
	                                                        }
                                                        
                                                        echo "% </span>" . JText::_('LNG_DISCOUNT', true);
                                                        }


                                                        ?></p>
                                                    <?php if($roomOffer->last_minute){ ?>
														<div class="last-minute-offer">
															<?php echo JText::_('LNG_LAST_MINUTE_OFFER');?>
														</div>
													<?php } ?>
                                                </div>

                                                <div class="triggerOffer open moreDetails leftText">
                                                    <a class="linkmore offer_link_<?php echo $roomOffer->offer_id ?>_<?php echo $roomOffer->room_id ?>" id="linkmore"
                                                       onclick="showOfferDesc('.offer_<?php echo $roomOffer->offer_id ?>_<?php echo $roomOffer->room_id ?>',jQuery(this),'&nbsp;<?php echo JText::_('LNG_LESS_DETAILS', true) ?>','&nbsp;<?php echo JText::_('LNG_MORE_DETAILS', true) ?>','.offer_link_<?php echo $roomOffer->offer_id ?>_<?php echo $roomOffer->room_id ?>')"

                                                       href="javascript:void(0)"><icon class="fa fa-chevron-right"><?php echo JText::_('LNG_MORE_DETAILS', true) ?></icon></a>
                                                       
                                                       
                                                </div>
                                                
                                            </div>
                                            <div class="col-xs-3 tableCell" id="Price">
                                               <div class="spaceBoxTop">
                                                    <div id="price" class="red text-center priceStyle">
                                                        <?php
                                                        if (!$roomOffer->is_disabled && !$capacityExceededOffer) {
                                                            if ($priceType == 1) {
                                                                echo $roomOffer->pers_total_price;
                                                            } elseif ($priceType == 0) {
                                                                echo $roomOffer->offer_average_price;
                                                            } elseif ($priceType == 2) {
                                                                echo $roomOffer->total_price;
                                                            }
                                                            
                                                            if ($priceType == 1) {
                                                                $price = $room->pers_total_price;
                                                            } else if ($priceType == 0) {
                                                                $price = $room->room_average_display_price;
                                                            } else if ($priceType == 2) {
                                                                $price = $room->total_price;
                                                            }
                                                            echo "<p class='centerText line old-price'>" . $price.$this->currency. "</p>";
                                                        }

                                                        ?>
                                                    </div>

	                                               <?php if(isset($roomOffer->nrRoomsLeft)){ ?>
                                                    <div class="roomsLeftInfo" style="<?php echo $roomsLeftDisplay; ?>">
                                                        <?php $roomLeft = intVal($roomOffer->nrRoomsLeft);
                                                        echo $roomLeft > 1 ? $roomLeft." ".JText::_("LNG_ROOMS_LEFT") : $roomLeft."".JText::_("LNG_ROOM_LEFT"); ?>
                                                    </div>
		                                            <?php } ?>
                                                  </div>
                                            </div>
                                           
                                            <div class="col-xs-2 bookingDiv"
                                                 id="bookButtons">
                                                 
                                                   <div align="center" class="clear">
                            	
						                                <ul>
                                                <?php
						                                    foreach ($this->userData->reservedItems as $i => $v) {
						                                        $room_ex = explode('|', $v);
						                                        if ($room_ex[0] >0 && $room_ex[0] == $roomOffer->offer_id) {
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
                                                <?php
                                                if ($capacityExceededOffer) {
                                                    echo "<p class='room_requirement small-text'>".JText::_('LNG_CAPACITY_EXCEDEED', true). "</p>";
                                                } else if (!$capacityFullfilledOffer) {
                                                    echo "<p class='room_requirement small-text'>" . str_replace("<<min_persons>>", $roomOffer->offer_min_pers, JText::_('LNG_CAPACITY_NOT_MET')) . "</p>";
                                                } else if (!$roomOffer->is_disabled) {
                                                    ?>
                                                
                                                    <button
                                                        class="ui-hotel-button small <?php echo count($this->userData->reservedItems) == $this->userData->rooms ? 'not-bookable' : '' ?>"
                                                        id="bookRoom"
                                                        name='itemRoomsCapacity_RADIO[]'
                                                        type='button'
                                                        <?php echo $roomOffer->is_disabled ? " disabled " : "" ?>
                                                        <?php echo count($this->userData->reservedItems) == $this->userData->rooms ? 'disabled' : '' ?>
                                                        onclick='return bookItem(<?php echo $roomOffer->offer_id ?>,<?php echo $roomOffer->room_id; ?>);'>
                                                    <span
                                                        class="ui-button-text"><?php echo JText::_('LNG_BOOK', true) ?></span>
                                                    </button>

                                                <?php } else {
                                                	echo "<p class='room_requirement'>";
                                                	echo JText::_('LNG_NOT_AVAILABLE');
                                                    $buttonLabel = JText::_('LNG_CHECK', true);
                                                    $class = "grey";
                                                    if ($roomOffer->lock_for_departure) {
                                                        $buttonLabel = JText::_('LNG_NO_DEPARTURE', true);
                                                        $class = "red";
                                                    }
													echo "</p>";
                                                    ?>
                                                   
                                                    <button
                                                        id="bookRoom"
                                                        class="ui-hotel-button grey small small-text float_left reservation triggerOffer open <?php echo $class ?> <?php echo count($this->userData->reservedItems) == $this->userData->rooms ? 'not-bookable' : '' ?>"
                                                        name="check-button"
                                                        value="<?php echo $buttonLabel ?>"
                                                        type='button'
                                                        onclick="showOfferDesc('.offer_<?php echo $roomOffer->offer_id ?>_<?php echo $roomOffer->room_id ?>',jQuery(this),'&nbsp;<?php echo JText::_('LNG_LESS_DETAILS', true) ?>','&nbsp;<?php echo JText::_('LNG_MORE_DETAILS', true) ?>','.offer_link_<?php echo $roomOffer->offer_id ?>_<?php echo $roomOffer->room_id ?>');"
                                                        <?php echo count($this->userData->reservedItems) == $this->userData->rooms ? 'disabled' : '' ?>
                                                        >
                                                        <span class="ui-button-text"><?php echo $buttonLabel ?></span>
                                                    </button>
                                                    <?php
                                                }
                                                $cheie_offer_room = $roomOffer->offer_id . "_" . $roomOffer->room_id;
                                                $offer_offer_room = $roomOffer->offer_id . "|" . $roomOffer->room_id;

                                                ?>
                                                <input
                                                    type="hidden"
                                                    name="items_reserved_tmp_<?php echo $cheie_offer_room ?>"
                                                    id="items_reserved_tmp_<?php echo $cheie_offer_room ?>"
                                                    value="<?php echo $offer_offer_room ?>"
                                                    />
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                        }
                        ?>
                        <?php

                        if (empty($this->userData->voucher)) {
                        ?>
                        <div class="roomContainer col-xs-9 <?php echo $singleRoom || $offerCount==0 ? 'tableCellSingle' : '';?>" style="<?php echo $roomDisplay;?>">
                            <div id="persons" class="col-xs-2 tableCell">
                                <div class="spaceBoxTop text-center">
                                    <?php 
                                    for ($i = 1; $i <= intVal($room->max_adults); $i++) {
                                        echo "<i class='guest_adult'  title='" .JText::_('LNG_ADULTS_MAX')." ".$room->max_adults."'></i>";
                                    }
                                    if($this->appSettings->show_children != 0) {
                                    	if($room->base_children > 0) {
                                    		echo "<i>|</i>";
                                    		for ($idx = 1; $idx <= $room->base_children; $idx++) {
                                    			echo "<i class='guest_child'  title='".JText::_('LNG_CHILDREN_MAX')." ".$room->base_children."'></i>";
                                    		}
                                    	}
                                    }
                                    ?>
                                </div>
                            </div>
                            <div
                                class="col-xs-3  tableCell">
                                <div class="spaceBoxTop"><?php   //dmp($this->userData->rooms); ?>
                                </div>
                            </div>
                            <div
                                class="col-xs-3  tableCell offers ">
                                <div class="spaceBoxTop" style="display: none">
                                </div>
                            </div>
                            <div id="roomPrice"
                                class="col-xs-3  tableCell">
                                <div class="spaceBoxTop">
                                    <div id="priceRoom" class="text-center priceStyle">
                                        <?php
                                        if (!$room->is_disabled && !$capacityExceeded) {
                                            if ($priceType == 1) {
                                                echo $room->pers_total_price;
                                            } else if ($priceType == 0) {
                                                echo $room->room_average_display_price;
                                            } else if ($priceType == 2) {
                                                echo $room->total_price;
                                            }
                      						echo $this->currency;
                                            
                                        }
                                        ?>
                                    </div>
	                                <?php if(isset($room->nrRoomsLeft)){ ?>
	                                <div class="roomsLeftInfo" style="<?php echo $roomsLeftDisplay;?>">
                                            <?php $roomLeft = $room->nrRoomsLeft;
                                            echo $roomLeft > 1 ? $roomLeft." ".JText::_("LNG_ROOMS_LEFT"):$roomLeft.JText::_("LNG_ROOM_LEFT"); ?>
                                    </div>
	                                <?php } ?>
                                </div>
                            </div>
                            <div align="center" class="">
                            	
                                <ul style='margin-top:0px'>
                                    <?php
                                    $crt_room_sel = 1;

	               					$nrDays = JHotelUtil::getNumberOfDays( $this->userData->start_date,$this->userData->end_date);
                                    
                                    foreach ($this->userData->reservedItems as $i => $v) {
                                        $room_ex = explode('|', $v);
                                        if ($room_ex[0] == 0 && $room_ex[1] == $room->room_id) {
                                            ?>
                                            <li>
                                                <div style='text-align:center'>
                                                    <?php echo JText::_('LNG_BOOKED')."#: ".$room_ex[2] ?>
                                                </div>
                                            </li>
                                            <?php
                                        }

                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="col-xs-2 bookingDiv <?php echo $nrDays < $room->min_days ? 'daysRequired' : ''; ?>">
                                <?php
                                $is_checked = false;

                                if ($nrDays < $room->min_days) {
                                    $text = JText::_('LNG_MINIMUM_DAYS', true);
                                    $text = str_replace("<<min_days>>", $room->min_days, $text);
                                    echo "<p class='room_requirement'>".$text."</p>";
                                } else if ($nrDays > $room->max_days && $room->max_days != 0) {
                                    $text = JText::_('LNG_MAXIMUM_DAYS', true);
                                    $text = str_replace("<<max_nights>>", $room->max_days, $text);
                                    echo "<p class='room_requirement'>".$text."</p>";
                                } else if ($capacityExceeded) {
                                    echo  "<p class='small-text room_requirement'>".JText::_('LNG_CAPACITY_EXCEDEED', true)."</p>";
								
                                } else
                                    if (!$room->is_disabled) {
                                        ?>

                                        <button id="bookRoom"
                                                class="ui-hotel-button small <?php echo count($this->userData->reservedItems) == $this->userData->rooms ? 'not-bookable' : '' ?>"
                                                id='itemRoomsCapacity_RADIO'
                                                name='itemRoomsCapacity_RADIO[]'
                                                type='button'
                                            <?php echo $room->is_disabled ? " disabled " : "" ?>
                                            <?php echo count($this->userData->reservedItems) == $this->userData->rooms ? 'disabled' : '' ?>
                                                onclick='return bookItem(0,
                                                <?php echo $room->room_id ?>);'
                                            ><span class="ui-button-text"><?php echo JText::_('LNG_BOOK') ?></span>
                                        </button>

                                    <?php } else {
                                        $buttonLabel = JText::_('LNG_CHECK', true);
                                        $class = "";

                                        if ($room->lock_for_departure) {
                                            $buttonLabel = JText::_('LNG_NO_DEPARTURE', true);
                                            $class = "red";
                                        }

                                        ?>
                                        <button id="bookRoom"
                                                class="ui-hotel-button grey small small-text float_left reservation <?php echo $class ?> <?php echo count($this->userData->reservedItems) == $this->userData->rooms ? 'not-bookable' : '' ?>"
                                                name="check-button"
                                                value="<?php echo $buttonLabel ?>"
                                                onclick="showOfferDesc('.room_cnt_<?php echo $room->room_id?>',jQuery(this),'&nbsp;<?php echo JText::_('LNG_LESS_DETAILS', true) ?>','&nbsp;<?php echo JText::_('LNG_MORE_DETAILS', true) ?>');"

                                                type='button'
                                            <?php echo count($this->userData->reservedItems) == $this->userData->rooms ? 'disabled' : '' ?>
                                            >
                                            <span class="ui-button-text"><?php echo $buttonLabel ?></span>
                                        </button>

                                    <?php } ?>
                            </div>
                        </div>
                        <?php }?>
                    </div>
                    <div class="tr_cnt">
                        <div class="td_cnt">
                            <div id="cnt" class="cnt room_cnt room_cnt_<?php echo $room->room_id?> room_description" tabindex="1000">
                                <div class="room-description">
                                   <div id="calendar-holder-<?php echo $room->room_id ?>" class="room-calendar">
                                        <div class="room-loader right"></div>
                                        <?php
                                        if (isset($this->_models['variables']->availabilityCalendar)) {
                                            $calendar = $this->_models['variables']->availabilityCalendar;
                                            $id = $room->room_id;
                                            echo $calendar[$id];
                                        }

                                        if (isset($this->_models['variables']->defaultAvailabilityCalendar)) {
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
                                        if (!empty($room->pictures)) {
                                            foreach ($room->pictures as $idx => $picture) {
                                            	if($idx==0)
                                            		continue; 
                                                $picture->hotel_picture_path = JURI::base() . PATH_PICTURES . $picture->room_picture_path;

                                                ?>
                                                <a href="<?php echo $picture->hotel_picture_path; ?>"
                                                   data-gallery="<?php echo $room->room_id ?>"
                                                   style="background-image:url('<?php echo $picture->hotel_picture_path; ?>')"
                                                   title="<?php echo JHotelUtil::setAltAttribute($picture->room_picture_info); ?>"></a>
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
                    if ($this->appSettings->is_enable_offers) {
                        if (isset($rooms[$room->room_id]) && count($rooms[$room->room_id]) > 0) {
                            foreach ($rooms[$room->room_id] as $key => $roomOffer) {
                                if ($roomOffer->room_id == $room->room_id) {
                                    ?>
                                    <div class="offer_tr_cnt">
                                        <div class="offer_td_cnt">
                                            <div id="cnt"
                                                 class="offer_cnt offer_<?php echo $roomOffer->offer_id ?>_<?php echo $roomOffer->room_id ?>" tabindex="1000">
                                                <div class="room-description">
                                                    <?php if (!$capacityExceededOffer) { ?>
                                                        <div
                                                            id="calendar-holder-<?php echo '' . $roomOffer->offer_id . '' . $roomOffer->room_id ?>"
                                                            class="room-calendar">
                                                            <div class="room-loader right"></div>

                                                        </div>
                                                    <?php } ?>
                                                    <div class="room_main_description">
                                                        <?php
                                                        echo "<h3>" . JText::_("LNG_OFFER_DETAILS") . "</h3>";
                                                        echo $roomOffer->offer_description . "<br/>";
                                                        echo $roomOffer->offer_content . "<br/>";
                                                        echo $roomOffer->offer_other_info;
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class='picture-container'>
                                                    <div class='picture-container'>
                                                        <div class="thumbs" data-gallery="one">
                                                            <?php
                                                            if (!empty($roomOffer->pictures)) {
                                                                foreach ($roomOffer->pictures as $picture) {
                                                                    $picture->offer_picture_path = JURI::base() . PATH_PICTURES . $picture->offer_picture_path;                                                                    ?>
                                                                    <a href="<?php echo $picture->offer_picture_path; ?>"
                                                                       data-gallery="<?php echo $roomOffer->offer_id ?>"
                                                                       style="background-image:url('<?php echo $picture->offer_picture_path; ?>')"
                                                                       title="<?php echo JHotelUtil::setAltAttribute($picture->offer_picture_info); ?>"></a>
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
                            }
                        }
                    }
                }
                ?>
            </div>
    </div>

<script type="text/javascript">
    jQuery('.thumbs a, .thumb a').touchTouch();
 </script>