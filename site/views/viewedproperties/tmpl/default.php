<?php // no direct access
/**
* @copyright	Copyright (C) 2008-2016 CMSJunkie. All rights reserved.
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

jimport('joomla.html.pane');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select.chosenAttribute');

defined('_JEXEC') or die('Restricted access');
?>

<div>
	<form action="<?php echo JRoute::_('index.php?option='. getBookingExtName().'&view=viewedproperties'.JHotelUtil::getItemIdS()) ?>" method="post" name="propertiesForm" id="propertiesForm">

		<h2><?php echo JText::_('LNG_SAVED_HOTEL_LIST',true)?></h2>

		<?php
		if($this->user->id > 0)
		{
			if ( isset( $this->items ) && count( $this->items ) > 0 )
			{ ?>
				<?php foreach ( $this->items as $item )
			{
				if ( $this->user->id == $item->user_id || $this->user->get( 'isRoot' ) )
				{
					?>
					<div class="section group property-section" id="property_<?php echo $item->hotel_id ?>">
						<div class="poi-image-container col column_2_of_12">
							<?php
							if ( ! empty( $item->hotel_picture_path ) )
							{ ?>
								<div class='picture-container'>
									<a target="_blank"
									   href="<?php echo JHotelUtil::getHotelLink( $item ); ?>">
										<img
											style="width: 100%;"
											src="<?php echo JURI::root() . PATH_PICTURES . $item->hotel_picture_path ?>"
											alt="<?php echo JHotelUtil::setAltAttribute( $item->hotel_picture_path ); ?>"/>
									</a>
								</div>
							<?php } ?>
						</div>
						<div class="col column_10_of_12">
							<div class="section group property-section">
								<div class="col column_10_of_12">
									<h3 class="property-title">
										<a target="_blank" class="poi_desc"
										   href="<?php echo JHotelUtil::getHotelLink( $item ); ?>"><?php echo $item->hotel_name; ?></a>
									</h3>
									<span class="hotel-stars">
										    <?php for ( $i = 1; $i <= $item->hotel_stars; $i ++ )
										    { ?>
											    <img class="property-image"
											         src='<?php echo JURI::base() . "administrator/components/" . getBookingExtName() . "/assets/img/star.png" ?>'/>
										    <?php } ?>

									    </span>
								</div>
								<div class="col column_2_of_12">
									<?php
									if ( $this->user->id > 0 )
									{
										if ( JFactory::getUser()->id == $item->user_id || $this->user->get( 'isRoot' ) )
										{ ?>
											<span class="editButtons">
										<button
											class="cancel"
											onclick="deleteProperty(<?php echo $item->hotel_id ?>)">
											<i class="fa fa-times"></i>
										</button>
									</span>
										<?php }
									} ?>
								</div>
							</div>
							<div class="section group property-section">
								<div class="col column_12_of_12">
									<div class="info-phone"><i class="fa fa-phone"> </i><?php echo $item->hotel_phone ?>
									</div>
									<div class="info-box-content">
										<div class="address"
										     itemtype="http://schema.org/PostalAddress"
										     itemscope=""
										     itemprop="address">
											<?php echo $item->hotel_address ?>
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
												if ( ! empty( $item->hotel_description ) )
												{ ?>
													<div class="poi_desc">
														<?php echo JHotelUtil::truncate( strip_tags( $item->hotel_description ), 200, false ); ?>
														<a class="linkmore" target="_blank"
														   href="<?php echo JHotelUtil::getHotelLink( $item ); ?>"> <?php echo JText::_( 'LNG_READ_MORE' ) ?></a>...
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
				<?php }
			}
			}
			else
			{
				?>
				<div>
					No Properties saved in the list
				</div>
				<?php
			}
		}else{
			?>
			<a href="javascript:void(0)" class="link" onclick="checkUser();"><?php echo JText::_('LNG_GUEST_USER_LOGIN_HERE_TO_CHECK_SAVED_PROPERTIES')?></a>
		<?php
		}
		?>

		<input type="hidden" name="task"  id="task" value=""/>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>

<script type="text/javascript">
	function deleteProperty(propertyId){
		var siteRoot = '<?php echo JURI::root(); ?>';
		var bookingExtName = '<?php echo getBookingExtName()?>';
		var url = siteRoot + '/index.php?option=' + bookingExtName + '&view=viewedproperties&task=viewedproperties.deleteProperty';

		if (propertyId) {
			jQuery.ajax
			({
				type: 'post',
				url: url,
				cache: false,
				data: {
					propertyId: propertyId
				},
				success: function () {
					var commentBlockEl = document.getElementById("property_" + propertyId);
					commentBlockEl.parentNode.removeChild(commentBlockEl);
				},
				error: function (data) {
					console.log(data);
				}
			});
			return false;
		}
	}

	function checkUser(){
		var form 	= document.forms['propertiesForm'];
		form.task.value	="viewedproperties.checkUser";
		form.submit();
	}
</script>
