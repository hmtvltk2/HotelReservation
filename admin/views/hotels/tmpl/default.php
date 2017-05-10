<?php defined('_JEXEC') or die('Restricted access'); 
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task != 'hotels.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true,true);?>'))
		{
			Joomla.submitform(task);
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotels');?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left fltlft">
				<label class="filter-search-lbl element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL',true); ?></label>
				<input type="text" name="filter_search" id="filterSearch" value="<?php echo htmlspecialchars($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC',true); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT',true); ?>"><i class="icon-search"></i></button>
				<button class="btn hasTooltip" type="button" onclick="document.getElementById('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR',true); ?>"><i class="icon-remove"></i></button>
			</div>

			<div class="btn-group pull-right hidden-phone" style="margin-top: 1px;">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC',true); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>

			<div class="filter-select pull-right fltrt_1 btn-group">
				<select name="filter_accommodationtypeId" class="inputbox input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('LNG_JOPTION_SELECT_TYPE',true);?></option>
					<?php echo JHtml::_('select.options', $this->accomodationTypes, 'value', 'text', $this->state->get('filter.accommodationtypeId'));?>
				</select>
			
				<select name="filter_status_id" class="inputbox input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('LNG_JOPTION_SELECT_STATUS',true);?></option>
					<?php echo JHtml::_('select.options', $this->statuses, 'value', 'text', $this->state->get('filter.status_id'));?>
				</select>
			</div>
		</div>
	</div>
	<div class="clearfix"> </div>
    <div class="responsive_table-responsive-vertical">
	<table class="responsive_table responsive_table-hover responsive_table-mc-light-blue"  id="itemList">
		<thead>
			 <tr>
				<th width='1%' class="nowrap">#</th>
				<th width="1%" class="nowrap ">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL',true); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				 <?php
				 if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
					 ?>
					 <th width='1%' class="nowrap ">

						 <?php echo JHtml::_('grid.sort', 'LNG_AVAILABLE', 'h.is_available', $listDirn, $listOrder); ?>

					 </th>
				 <?php } ?>
				 <th width="5%">
					 &nbsp;
				 </th>
				<th width='20%' >
					<?php echo JHtml::_('grid.sort', 'LNG_NAME', 'h.hotel_name', $listDirn, $listOrder); ?>
				</th>
				<?php 
				if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
				?>
					<th width='1%' class="nowrap">
						<?php echo JHtml::_('grid.sort', 'LNG_FEATURED', 'h.featured', $listDirn, $listOrder); ?>
					</th>
				<?php 
					}
				?>
				<th width='15%' class="nowrap">
				<?php echo JHtml::_('grid.sort', 'LNG_COUNTRY', 'hc.country_name', $listDirn, $listOrder); ?>
				</th>
				<th width='8%'  class="nowrap ">
					<?php echo JHtml::_('grid.sort', 'LNG_CITY', 'h.hotel_city', $listDirn, $listOrder); ?>
				</th>
				<th width='8%' >
					<?php echo JHtml::_('grid.sort', 'LNG_PHONE', 'h.hotel_phone', $listDirn, $listOrder); ?>
				</th>
				<th width='8%' class="nowrap ">
					<?php echo JHtml::_('grid.sort', 'LNG_EMAIL', 'h.email', $listDirn, $listOrder); ?>
				</th>
				 <th width="8%" class="nowrap center "><?php  echo JHtml::_('grid.sort', 'LNG_ID', 'h.hotel_id', $listDirn, $listOrder);?></th>
				 <th width='40%' class="nowrap"></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>

		<tbody>
				<?php
				$nrcrt = 1;
				$i=-1;
				if(count($this->items))
				foreach($this->items as $hotel)
				{
					$i++;
					?>
					<TR class="row<?php echo $i%2 ?>"
						onmouseover="this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout="this.style.cursor='default'">
						<TD data-title="#"><?php echo $nrcrt++?></TD>
						<TD>
							<?php echo JHtml::_('grid.id', $i, $hotel->hotel_id); ?>
						</TD>
						<?php
						if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")) {
							?>


							<?php if($hotel->is_available == false){?>
								<td data-title="<?php echo JText::_('LNG_AVAILABLE',true)?>" class=" small">
									<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i?>','hotels.state')" title="Set Hotel Available"><i class="icon-unpublish"></i></a>
								</td>
							<?php }else{?>
								<td data-title="<?php echo JText::_('LNG_AVAILABLE',true)?>" class=" small">
									<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i?>','hotels.state')" title="Set Hotel Unavailable">
										<i class="icon-publish"></i>
									</a>
								</td>
							<?php } ?>
							<?php
						}
						?>
						<td  data-title="&nbsp;">
							<?php
							if(isset($hotel->hotel_picture_path)){
								echo "<img alt='".JHotelUtil::setAltAttribute($hotel->hotel_picture_path)."' class='round-image preview' src='".JURI::root().PATH_PICTURES.$hotel->hotel_picture_path."'/>";
							}else{
								echo "<img alt='default_" . $hotel->hotel_id . "' class='round-image preview' src='" . JURI::root() . PATH_PICTURES . "/no_image.jpg' />";
							}
							?>
						</td>
						<TD class="has-context" data-title="<?php echo JText::_('LNG_NAME',true)?>">
                            <a
							href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=hotel.edit&cid[]='. $hotel->hotel_id )?>'
							title="<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"> <B><?php echo stripslashes($hotel->hotel_name)?>
							
						</a>
 						<font size="1"> <?php echo "(".JText::_('LNG_ALIAS').":".$hotel->hotel_alias.")"; ?> </font>
						</TD>
						<?php 
							if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
						?>


                                <?php if($hotel->featured == false){?>
                                    <td data-title="<?php echo JText::_('LNG_FEATURED',true)?>" class=" has-context">
                                        <a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i?>','hotels.changeFeaturedState')" title="Set Hotel Featured"><i class="icon-unpublish"></i></a>
                                    </td>
                                <?php }else{?>
                                    <td data-title="<?php echo JText::_('LNG_FEATURED',true)?>" class="has-context">
                                        <a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i?>','hotels.changeFeaturedState')" title="Disable Featured">
                                            <i class="icon-publish"></i>
                                        </a>
                                    </td>
                                <?php }?>
						<?php 
							}
						?>
						<TD data-title="<?php echo JText::_('LNG_COUNTRY',true)?>" >
							<?php echo $hotel->country_name?>
						</TD>
						<TD data-title="<?php echo JText::_('LNG_CITY',true)?>" >
							<?php echo $hotel->hotel_city?>
						</TD>
						<TD data-title="<?php echo JText::_('LNG_PHONE',true)?>" >
							<?php echo $hotel->hotel_phone?>
						</TD>
						<TD data-title="<?php echo JText::_('LNG_EMAIL',true)?>" >
							<?php echo $hotel->email?>
						</TD>
						<td class="center" data-title="<?php echo JText::_('LNG_ID',true)?>" >
							<?php echo $hotel->hotel_id; ?>
						</td>
						<TD data-title=" " class="small" colspan="2">
						<?php
							if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
						?>
							<?php if (checkUserAccess(JFactory::getUser()->id,"availability_section")){ ?>
								<a	href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=availability&hotel_id='. $hotel->hotel_id )?>'
									title="<?php echo JText::_('LNG_AVAILABILITY')?>"
								>
									<b><?php echo JText::_('LNG_AVAILABILITY')?></b>
								</a>
								 |
							<?php } ?>
							<a	href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=rooms&hotel_id='. $hotel->hotel_id )?>'
								title="<?php echo JText::_('LNG_ROOMS',true)?>"
							>
								<b><?php echo JText::_('LNG_ROOMS',true)?></b>
							</a>
							<?php if(PROFESSIONAL_VERSION==1) {
									if($this->appSettings->is_enable_offers)
									{
										?>

										|
										<a href='<?php echo JRoute::_( 'index.php?option=' . getBookingExtName() . '&view=offers&hotel_id=' . $hotel->hotel_id ) ?>'
										   title="<?php echo JText::_( 'LNG_OFFERS', true ) ?>"
										>
											<b><?php echo JText::_( 'LNG_OFFERS', true ) ?></b>
										</a>
										<?php
									}

									if($this->appSettings->is_enable_extra_options)
									{ ?>
										|
										<a href='<?php echo JRoute::_( 'index.php?option=' . getBookingExtName() . '&view=extraoptions&hotel_id=' . $hotel->hotel_id ) ?>'
										   title="<?php echo JText::_( 'LNG_EXTRAS', true ) ?>"
										>
											<b><?php echo JText::_( 'LNG_EXTRAS', true ) ?></b>
										</a>
										<?php
									}
                                }?>
							|
							<a	href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=reservations&filter_hotel_id='. $hotel->hotel_id )?>'
								title="<?php echo JText::_('LNG_RESERVATIONS',true); ?>"
							>
								<b><?php echo JText::_('LNG_RESERVATIONS',true)?></b>
							</a>
							
								<?php
							if (checkUserAccess(JFactory::getUser()->id,"manage_invoices")){
							?>
							|
										<a href='<?php echo JRoute::_( 'index.php?option=' . getBookingExtName() . '&view=invoices&hotel_id=' . $hotel->hotel_id ) ?>'
										   title="<?php echo JText::_( 'LNG_INVOICES', true ) ?>"
										>
											<b><?php echo JText::_( 'LNG_INVOICES', true ) ?></b>
										</a>
							<?php } ?>
							
						<?php } ?>
						</td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
    </div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="hotel_id" value="" /> 
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>

