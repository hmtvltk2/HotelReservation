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

defined('_JEXEC') or die('Restricted access');
?>
<?php
if(isset($otherUsersProperties) && count($otherUsersProperties)>0) {
	?>
	<div id="hotel-list">
	<?php
    foreach ($otherUsersProperties as $otherUsersProperty)
    {
		    ?>
		    <div class="property-container">
			    <div class="section group property-section">
				    <div class="property-image-container col column_2_of_12">
					    <?php
					    if ( ! empty( $otherUsersProperty->hotel_picture_path ) )
					    { ?>
						    <div class='picture-container'>
								    <a target="_blank"
									       href="<?php echo JHotelUtil::getHotelLink($otherUsersProperty); ?>">
										    <img
											    style="width: 100%;"
											    src="<?php echo JURI::root() . PATH_PICTURES.$otherUsersProperty->hotel_picture_path ?>"
											    alt="<?php echo JHotelUtil::setAltAttribute( $otherUsersProperty->hotel_picture_path ); ?>"/>
									</a>
						    </div>
					    <?php } ?>
				    </div>
					<div class="col column_10_of_12">
						    <div class="section group property-section">
							    <div class="col column_8_of_12">
								    <h3 class="property-title">
									    <a target="_blank" class="property-desc" href="<?php echo JHotelUtil::getHotelLink($otherUsersProperty); ?>"><?php  echo $otherUsersProperty->hotel_name; ?></a>
								    </h3>
									    <span class="hotel-stars">
										    <?php for ($i=1;$i<=$otherUsersProperty->hotel_stars;$i++){ ?>
											    <img class="property-image"  src='<?php echo JURI::base() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>'/>
										    <?php } ?>

									    </span>
								    <div class="section group property-section">
									    <div class="col column_12_of_12">
										    <div class="info-phone"><i class="fa fa-phone"> </i><?php echo $otherUsersProperty->hotel_phone ?> </div>
										    <div class="info-box-content">
											    <div class="address"
											         itemtype="http://schema.org/PostalAddress"
											         itemscope=""
											         itemprop="address">
												    <?php echo $otherUsersProperty->hotel_address ?>
											    </div>
										    </div>
									    </div>
								    </div>
								</div>
							    <div class="col column_4_of_12">
								    <div class="section group property-section prop-rating">
									    <div class="col column_12_of_12">

								    <?php if($otherUsersProperty->noReviews >= MINIMUM_HOTEL_REVIEWS & $appSettings->enable_hotel_rating==1)
								    {
									    echo ReviewsService::getRatingSummaryHtml($otherUsersProperty,$otherUsersProperty->noReviews);
								    }
								    ?>
									    </diV>
								    </div>
								    <div class="section group property-section prop-rating">
									    <div class="col column_12_of_12">
									    <?php if($otherUsersProperty->recommended==1){?>

										    <div class="hotel-recommanded">
											    <span><?php  echo JText::_('LNG_RECOMMENDED',true);?></span>
										    </div>
									    <?php } ?>

									    <?php if(!empty($otherUsersProperty->roomsLeft)){ ?>
										    <div class="roomsLeftInfo">
											    <?php  echo $otherUsersProperty->roomsLeft > 1 ? $otherUsersProperty->roomsLeft." ".JText::_("LNG_ROOMS_LEFT") : $roomsLeft."".JText::_("LNG_ROOM_LEFT"); ?>
										    </div>
									    <?php } ?>
										    </div>
								    </div>
							    </div>
						    </div>
				    </div>
			    </div>
			    <div class="section group property-section">
				    <div class="col column_12_of_12">
					    <div class="section group property-section">
						    <div class="col column_12_of_12">
							    <div class="sp_offers">
								    <div class="offerDescription">
									    <div>
										    <?php
										    if ( ! empty( $otherUsersProperty->hotel_description ))
										    { ?>
											    <div class="property-desc">
												    <?php echo JHotelUtil::truncate( strip_tags( $otherUsersProperty->hotel_description ), 200, false ); ?>
												    <a class="linkmore" target="_blank"
												       href="<?php echo JHotelUtil::getHotelLink($otherUsersProperty); ?>"> <?php echo JText::_( 'LNG_READ_MORE' ) ?></a>...
											    </div>
										    <?php } ?>
										    <br/>
										    <div class="clear"></div>
									    </div>
								    </div>
							    </div>
						    </div>
					    </div>
					    <div class="section group property-section">
						    <div class="col column_2_of_12">
							    <a class="ui-hotel-button left" id="property-book" href="<?php echo JHotelUtil::getHotelLink($otherUsersProperty)?>"> <?php echo JText::_('LNG_BOOK_HOTEL',true)?></a>
						    </div>
						    <div class="col column_4_of_12 prop-book">
							    <?php if($otherUsersProperty->min_room_price > 0){ ?>
								    <div class="hotel-price">
									    <span class="prop-label"> <?php  echo JText::_('LNG_ROOMS',true);?> <?php  echo JText::_('LNG_FROM',true);?>:</span>
									    <span class="prop-price"><?php echo !empty($userData->currency->name)?$userData->currency->name:$otherUsersProperty->hotel_currency ?> <?php echo JHotelUtil::fmt($otherUsersProperty->min_room_price,2) ?></span>
								    </div>
							    <?php } ?>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
	    <?php }
    ?>
<?php }else{
	?>
	<div class="property-container">-
	    <?php echo JText::_('LNG_NO_SIMILIAR_PROPERTIES_FOUND',true); ?>
	</div>
<?php
} ?>
