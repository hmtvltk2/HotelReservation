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
if(isset($recentProperties) && count($recentProperties)>0) {
	?>
	<div id="hotel-list">
	<?php
    foreach ($recentProperties as $recentProperty)
    {
		    ?>
		    <div class="poi-container hotel-info">
			    <div class="section group property-section">
				    <div class="poi-image-container col column_2_of_12">
					    <?php
					    if ( ! empty( $recentProperty->hotel_picture_path ) )
					    { ?>
						    <div class='picture-container'>
								    <a target="_blank"
									       href="<?php echo JHotelUtil::getHotelLink($recentProperty); ?>">
										    <img
											    style="width: 100%;"
											    src="<?php echo JURI::root() . PATH_PICTURES.$recentProperty->hotel_picture_path ?>"
											    alt="<?php echo JHotelUtil::setAltAttribute( $recentProperty->hotel_picture_path ); ?>"/>
									</a>
						    </div>
					    <?php } ?>
				    </div>
					<div class="col column_10_of_12">
						    <div class="section group property-section">
							    <div class="col column_12_of_12">
								    <h3 class="property-title">
									    <a target="_blank" class="poi_desc" href="<?php echo JHotelUtil::getHotelLink($recentProperty); ?>"><?php  echo $recentProperty->hotel_name; ?></a>
								    </h3>
									    <span class="hotel-stars">
										    <?php for ($i=1;$i<=$recentProperty->hotel_stars;$i++){ ?>
											    <img class="property-image"  src='<?php echo JURI::base() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>'/>
										    <?php } ?>

									    </span>
								</div>

						    </div>
						<div class="section group property-section">
							<div class="col column_12_of_12">
								<div class="info-phone"><i class="fa fa-phone"> </i><?php echo $recentProperty->hotel_phone ?> </div>
									<div class="info-box-content">
										<div class="address"
										     itemtype="http://schema.org/PostalAddress"
										     itemscope=""
										     itemprop="address">
											<?php echo $recentProperty->hotel_address ?>
										</div>
									</div>
								</div>
						</div>
						<div class="section group property-section">
							<div class="col column_12_of_12">
								<div class="sp_offers poi-description">
									<div class="offerDescription">
										<div>
											<?php
											if ( ! empty( $recentProperty->hotel_description ))
											{ ?>
												<div class="poi_desc">
													<?php echo JHotelUtil::truncate( strip_tags( $recentProperty->hotel_description ), 200, false ); ?>
													<a class="linkmore" target="_blank"
													   href="<?php echo JHotelUtil::getHotelLink($recentProperty); ?>"> <?php echo JText::_( 'LNG_READ_MORE' ) ?></a>...
												</div>
											<?php } ?>
											<br/>
											<div class="clear"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
				    </div>
			    </div>
		    </div>
	    <?php }
    ?>
	<div class="section group property-section">
		<div class="col column_4_of_12" id="test">
			<?php if($user->id>0){?>
				<button	class="ui-hotel-button"
			           onClick	= "saveViewedProperties('userModuleForm','no-dates');"
			           type="button" name="saveProperties" value="saveProperties"><?php echo JText::_('LNG_SAVE',true)?>
				</button>
			<?php }else {
				?>
				<a href="<?php echo JURI::root().'/index.php?option='.getBookingExtName().'&view=viewedproperties&task=viewedproperties.loginUser';?>"><?php echo JText::_('LNG_CLICK_HERE')?></a>
			<?php
			} ?>
		</div>
		<div class="col column_4_of_12">
			<a class="linkmore" target="_blank"
			   href="<?php echo JRoute::_('index.php?option='. getBookingExtName().'&view=viewedproperties'); ?>"><?php echo JText::_( 'LNG_MY_LIST' ) ?></a>
		</div>
	</div>
	<script>
		<?php if($user->id>0){ ?>
		function saveViewedProperties() {
			var siteRoot = '<?php echo JURI::root(); ?>';
			var bookingExtName = '<?php echo getBookingExtName()?>';
			var url = siteRoot + '/index.php?option=' + bookingExtName + '&view=viewedproperties&task=viewedproperties.saveRecentViewedProperties';
			var properties = <?php echo json_encode($recentProperties)?>;
					jQuery.ajax
					({
						type: 'post',
						url: url,
						data: {
							properties: properties
						},
						success: function (response) {
							console.log(response);
							document.getElementById('test').innerHTML = response;
						},
						error: function (data) {
							console.log(data);
						}
					});
			return false;
		}
		<?php } ?>
	</script>
<?php }else{
	?>
	<div>
	    <?php echo JText::_('LNG_NO_PROPERTY_VIEWED',true); ?>
	</div>
<?php
} ?>
