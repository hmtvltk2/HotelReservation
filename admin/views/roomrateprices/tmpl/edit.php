<?php defined('_JEXEC') or die('Restricted access'); ?>
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
jimport('joomla.html.pane');
$appSetings = JHotelUtil::getApplicationSettings();

JHTML::_("behavior.calendar");
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

$language = JFactory::getLanguage();
$language_tag = $language->getTag();

$language_tag = str_replace("-","_",$language->getTag());
setlocale(LC_TIME , $language_tag.'.UTF-8');
?>

<?php if(!$this->onlyAvailability){ ?>
<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=roomrateprices&layout=edit&rate_id='.$this->state->get("filter.rate_id")); ?>" method="post" name="quickFilterFrm" id="quickFilterFrm">

	<div class="section group">
		<div class="col column_6_of_12">
	<fieldset>
    <table class="rate-quick-filter">
		<thead>
			<tr>
				<th colspan="4"><?php echo JText::_("LNG_QUICK_SETUP")?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4" class="reservation-details">
                    <?php echo JText::_("LNG_FROM")?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input class="form-control"
                           id="start_date"
                           data-provide="datepicker"
                           name="start_date"
                           value ='<?php echo $this->state->get("filter.start_date")==$appSetings->defaultDateValue?'': $this->state->get("filter.start_date") ?>'
                           onchange="jQuery(this).css({display: 'inline !important'});"
                           type="text">

                    <button type="button" class="btn" id="datasf_img" style="margin-bottom: 8px;"><i class="icon-calendar"></i></button>

                    <?php echo ucfirst(JText::_("LNG_TO"))?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input class="form-control"
                           id="end_date"
                           data-provide="datepicker"
                           name="end_date"
                           value ='<?php echo $this->state->get("filter.end_date")==$appSetings->defaultDateValue?'': $this->state->get("filter.end_date"); ?>'

                           type="text">

                    <button type="button" class="btn" id="dataef_img" style="margin-bottom: 8px;"><i class="icon-calendar"></i></button>
				</td>
			</tr>
			<tr>
				<td colspan="4"> 
					<TABLE class="admintable">
					<tr>
					 
						<?php
							for($day=1;$day<=7;$day++)
							{
								?>
								<TD>
								<?php
								switch( $day )
								{
									case 1:
										echo JText::_('LNG_MON');
										break;
									case 2:
										echo JText::_('LNG_TUE');
										break;
									case 3:
										echo JText::_('LNG_WED');
										break;
									case 4:
										echo JText::_('LNG_THU');
										break;
									case 5:
										echo JText::_('LNG_FRI');
										break;
									case 6:
										echo JText::_('LNG_SAT');
										break;
									case 7:
										echo JText::_('LNG_SUN');
										break;
								}
								?>
								</TD>
								<?php
							}
							?>
							<td>
						       <strong> <?php echo JText::_('LNG_SELECT_ALL'); ?></strong>
						   </td>
						</TR>
						<TR>
							
							<?php
							for($day=1;$day<=7;$day++)
							{
								?>
								<TD>
									<input 
									type	= 'checkbox' 
									name	= 'week_day[]'
									id		= 'week_day_<?php echo $day?>'
									value	= "<?php echo $day?>"
									class="week-day"
									<?php echo  0 == 1 ? " checked " : " "?>
								>
								</TD>
							<?php
							}
							?>
							<td>
								<input type="checkbox" id="allweek" value="">
							</td>
						</TR>
					</TABLE>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<hr/>
				</td>
			</tr>	
			<tr>
				<td >
					<?php echo JText::_('LNG_PRICE');?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "price"
						id			= "price"
						value		= ""
						size		= 10
						maxlength	= 10
					/>
				</td>
				<td >
					<?php echo JText::_('LNG_AVAILABILITY');?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "availability"
						id			= "availability"
						value		= ""
						size		= 10
						maxlength	= 10
					/>
				</td>
			</tr>
			<tr>
				<td >
					<?php echo JText::_('LNG_SINGLE_USE_PRICE');?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "single_use_price"
						id			= "single_use_price"
						value		= ""
						size		= 10
						maxlength	= 10
					/>
				</td>
				<td >
					<?php echo JText::_('LNG_EXTRA_PERS_PRICE');?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "extra_pers_price"
						id			= "extra_pers_price"
						value		= ""
						size		= 10
						maxlength	= 10
					/>
				</td>
			</tr>
			<tr>
			
				<td >
					<?php echo JText::_('LNG_MIN_DAYS');?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "min_days"
						id			= "min_days"
						value		= ""
						size		= 10
						maxlength	= 10
					/>
				</td>
			
				<td >
					<?php echo JText::_('LNG_MAX_DAYS');?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "max_days"
						id			= "max_days"
						value		= ""
						size		= 10
						maxlength	= 10
					/>
				</td>
			</tr>

			<?php if($this->appSettings->show_children!=0){ ?>
			<tr>
				<td >
					<?php echo JText::_('LNG_CHILD_PRICE');?>
				</td>
				<td colspan="3">
					<input 
						type		= "text"
						name		= "child_price"
						id			= "child_price"
						value		= ""
						size		= 10
						maxlength	= 10
					/>
				</td>
			</tr>
				<?php foreach($this->childrenCategories as $childCategory){?>	 
					<tr>
						<td >
							<?php echo $childCategory->category_name;?>
						</td>
						<td colspan="3">
							<input 
								type		= "text"
								name		= "child_price_<?php echo $childCategory->id?>"
								id			= "child_price"
								value		= '<?php echo isset($this->childrenCategoryPrices[$childCategory->id])?$this->childrenCategoryPrices[$childCategory->id]->price:""; ?>'
								size		= 10
								maxlength	= 10
							/>
						</td>
					</tr>
				<?php }?>	 
			<?php } ?>
			
			<tr>
				<td >
					<?php echo JText::_('LNG_LOCK_FOR_ARRIVAL');?>
				</td>
				<td>
					<input 
						type		= "checkbox"
						name		= "lock_arrival"
						id			= "lock_arrival"
						value		= "1"
						size		= 10
						maxlength	= 10
					/>
				</td>
			
				<td >
					<?php echo JText::_('LNG_LOCK_FOR_DEPARTURE');?>
				</td>
				<td>
					<input 
						type		= "checkbox"
						name		= "lock_departure"
						id			= "lock_departure"
						value		= "1"
						size		= 10
						maxlength	= 10
					/>
				</td>
			</tr>
			
			<tr>
				<td colspan="4">
					<input type="submit" class="ui-hotel-button right" value="<?php echo JText::_("LNG_SAVE");?>" />
				</td>
			</tr>
		</tbody>
	</table>
    </fieldset>
	</div>
		<?php if($this->cubilis){ ?>
		<div class="col column_6_of_12">
			<div id="note">
				<div>
					<div class="red">
						<?php echo JText::_('LNG_USAGE_NOTE',true);?>
					</div>
					<p  class="red">
						<?php echo JText::_('LNG_CUBILIS_INFO',true);?>
					</p>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	
		<input type="hidden" name="option"	value="<?php echo getBookingExtName()?>" /> 
		<input type="hidden" name="task" value="roomrateprices.quickSetup" /> 
		<input type="hidden" name="rate_id" id="rate_id" value="<?php echo $this->state->get("filter.rate_id") ?>" /> 
		<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<?php } ?>
<?php 
$additionalParams="";
if(!empty($this->onlyAvailability)){
	$additionalParams .= "&onlyAvailability=true";
	$additionalParams .= "&room_id=".JRequest::getVar("room_id");
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=roomrateprices&layout=edit&rate_id='.$this->state->get("filter.rate_id").$additionalParams); ?>" method="post" name="adminForm" id="adminForm">

		<div id="rate-wrapper">
	
			<div class="rates-header">
				<div id="month-chooser"> 
					<?php echo JText::_('LNG_CHOOSE_MONTH'); ?>
					<select name="filter_month" onchange="this.form.submit()">
						<?php 
							
							for($i=-1;$i<11;$i++){
							?>
							<option value="<?php echo (date("n")+$i)%12+1 ?>" <?php echo $this->state->get("filter.month")==((date("n")+$i)%12+1)?"selected":"" ?> ><?php echo strftime("%B %Y",mktime(0, 0, 0, (date("n")+$i)+1, 1, date("Y"))); ?> </option>
							
						<?php }?>
					</select>
				</div>
				<?php 
					$year = $this->state->get("filter.month")<date("n")? date("Y")+1 : date("Y");
				?>
				<h3><?php echo JText::_('LNG_RATES_AND_AVAILABILITY') ." ".strftime('%B %Y',mktime(0, 0, 0, $this->state->get("filter.month"), 1, $year)); ?></h3>
				<span> <?php echo $this->editInfo?></span>
				<span> <?php echo JText::_('LNG_PRICE_TYPE').": ".($this->rate->price_type==1?JText::_('LNG_PER_PERSON'):JText::_('LNG_PER_ROOM')) ?></span><br/>
				<?php $newRates = $this->state->get("filter.newRates"); echo isset($newRates)?"<span class='red'>".JText::_("LNG_RATES_LOADED_DEFAULT_DETAIL")."</span>":"<span class='green'>".JText::_("LNG_RATES_LOADED_DATABASE_DETAIL")."</span>"; ?>
			</div>
			
			<div id="rate-container">
			<div class="rate-row">
			<?php 
				$weekDay = date("N", strtotime($this->items[0]->date));
				if($weekDay!=1){
					
				?>
					<div class="rate-header">
						<ul>
							<li>
								<?php echo JText::_('LNG_DATE')?>
							</li>
							<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
								<?php echo JText::_('LNG_PRICE')?>
							</li>
							<?php if($this->appSettings->show_children!=0){ ?>
								<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
									<?php echo JText::_('LNG_CHILD_PRICE')?>
								</li>
								<?php foreach($this->childrenCategories as $childCategory){?>	 
									<li>
										<?php echo $childCategory->category_name;?>
									</li>
								<?php }?>	 
							<?php } ?>
							<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
								<?php echo JText::_('LNG_SINGLE_USE_PRICE')?>
							</li>
							<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
								<?php echo JText::_('LNG_EXTRA_PERS_PRICE')?>
							</li>
							<li>
								<?php echo JText::_('LNG_AVAILABILITY')?>
							</li>
							<li >
								<?php echo JText::_('LNG_MINIMUM_STAY')?>
							</li>
							<li>
								<?php echo JText::_('LNG_MAXIMUM_STAY')?>
							</li>
							<li>
								<?php echo JText::_('LNG_LOCK_FOR_ARRIVAL')?>
							</li>
							<li>
								<?php echo JText::_('LNG_LOCK_FOR_DEPARTURE')?>
							</li>
							<li>
								<?php echo JText::_('LNG_BOOKED')?>
							</li>
							<li>
								<?php echo JText::_('LNG_AVAILABLE')?>
							</li>
						</ul>
					</div>
			<?php 
					
					for($j=1; $j<$weekDay; $j++){
						echo '<div class="rate-cell"></div>';	
					}
				}
			?>
			<?php foreach($this->items as $i => $item){
				if(!isset($newRates)){
					$item->childrenCategoryPrices = ChildrenCategoryService::getChildrenCategoryCustomPrices($this->state->get("filter.rate_id"),$item->date,$item->date);
				} 
				$monthDay = date("j", strtotime($item->date));
			?>
				
			 <?php $weekDay = date("N", strtotime($item->date));
			 	   if($weekDay==1){
			  ?>
				</div>
				<div class="rate-row">
					<div class="rate-header">
						<ul>
							<li>
								<?php echo JText::_('LNG_DATE')?>
							</li>
							<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
								<?php echo JText::_('LNG_PRICE')?>
							</li>
							<?php if($this->appSettings->show_children!=0){ ?>
							<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
								<?php echo JText::_('LNG_CHILD_PRICE')?>
							</li>
								<?php foreach($this->childrenCategories as $childCategory){?>	 
									<li>
										<?php echo $childCategory->category_name;?>
									</li>
								<?php }?>	 
							
							<?php } ?>
							<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
								<?php echo JText::_('LNG_SINGLE_USE_PRICE')?>
							</li>
							<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
								<?php echo JText::_('LNG_EXTRA_PERS_PRICE')?>
							</li>
							<li>
								<?php echo JText::_('LNG_AVAILABILITY')?>
							</li>
							<li>
								<?php echo JText::_('LNG_MINIMUM_STAY')?>
							</li>
							<li>
								<?php echo JText::_('LNG_MAXIMUM_STAY')?>
							</li>
							<li>
								<?php echo JText::_('LNG_LOCK_FOR_ARRIVAL')?>
							</li>
							<li>
								<?php echo JText::_('LNG_LOCK_FOR_DEPARTURE')?>
							</li>
							<li>
								<?php echo JText::_('LNG_BOOKED')?>
							</li>
							<li>
								<?php echo JText::_('LNG_AVAILABLE')?>
							</li>
						</ul>
					</div>
				<?php }?>
				<div class="rate-cell <?php echo ($item->available<=0 || !$item->isHotelAvailable)?"no-availability":"" ?>">
					<ul>
						<li class="date">
							<?php echo strftime("%a, %d-%m-%Y", strtotime($item->date)); ?>
						</li>
						<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
							<input type="text" <?php echo $this->onlyAvailability?"readonly='readonly'":"" ?> name="price[<?php echo $monthDay?>]" id="price[<?php echo $monthDay?>]" value="<?php echo $item->price ?>"   />
						</li>
						<?php if($this->appSettings->show_children!=0){ ?>
						<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
							<input type="text" <?php echo $this->onlyAvailability?"class='hide'":"" ?> <?php echo $this->onlyAvailability?"readonly='readonly'":"" ?> name="child_price[<?php echo $monthDay?>]" id="child_price[<?php echo $monthDay?>]" value="<?php echo $item->child_price ?>" />
						</li>
						<?php } ?>
						<?php 
							foreach($this->childrenCategories as $childCategory){

								$foundDate = false;
								if(isset($item->childrenCategoryPrices[$childCategory->id]) && ($item->childrenCategoryPrices[$childCategory->id]->date ==$item->date))
									$foundDate = true;
							?>
								<li>
										<input 
											type		= "text"
											name		= "child_price_<?php echo $childCategory->id?>[<?php echo $monthDay?>]"
											value		= '<?php echo isset($item->childrenCategoryPrices[$childCategory->id]) && $foundDate?$item->childrenCategoryPrices[$childCategory->id]->price:""; ?>'
											size		= 10
											maxlength	= 10
										/>
								</li>
						<?php }?>	 
						<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
							<input type="text" <?php echo $this->onlyAvailability?"class='hide'":"" ?> <?php echo $this->onlyAvailability?"readonly='readonly'":"" ?> name="single_use_price[<?php echo $monthDay?>]" id="single_use_price[<?php echo $monthDay?>]" value="<?php echo $item->single_use_price ?>" />
						</li>
						<li <?php echo $this->onlyAvailability?"class='hide'":"" ?>>
							<input type="text" <?php echo $this->onlyAvailability?"class='hide'":"" ?> <?php echo $this->onlyAvailability?"readonly='readonly'":"" ?> name="extra_pers_price[<?php echo $monthDay?>]" id="extra_pers_price[<?php echo $monthDay?>]" value="<?php echo $item->extra_pers_price ?>" />
						</li>
						<li>
							<input type="text" name="availability[<?php echo $monthDay?>]" id="availability[<?php echo $monthDay?>]" value="<?php echo $item->availability ?>" />
						</li>
						<li>
							<input type="text" name="min_days[<?php echo $monthDay?>]" id="min_days[<?php echo $monthDay?>]" value="<?php echo $item->min_days ?>" />
						</li>
						<li>
							<input type="text" name="max_days[<?php echo $monthDay?>]" id="max_days[<?php echo $monthDay?>]" value="<?php echo $item->max_days ?>" />
						</li>
						<li>
							<input type="checkbox" name="lock_arrival[<?php echo $monthDay?>]" id="lock_arrival[<?php echo $monthDay?>]" <?php echo $item->lock_arrival?"checked":"" ?>  />
						</li>
						<li>
							<input type="checkbox" name="lock_departure[<?php echo $monthDay?>]" id="lock_departure[<?php echo $monthDay?>]" <?php echo $item->lock_departure?"checked":"" ?> />
						</li>
						<li class="rate-info">
							<?php echo $item->booked ?>
						</li>
						<li  class="rate-info">
							<?php echo $item->available ?>
						</li>
					</ul>
				</div>
			<?php }?>
			</div>
			</div>
			<div class="clr"></div>
		</div>
		<input type="hidden" name="option"	value="<?php echo getBookingExtName()?>" /> 
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="onlyAvailability" value="<?php echo $this->onlyAvailability ?>" /> 
		<input type="hidden" name="rate_id" id="rate_id" value="<?php echo $this->state->get("filter.rate_id") ?>" /> 
		<?php echo JHTML::_( 'form.token' ); ?> 
	</form>

	


	<script language="javascript" type="text/javascript">

		Joomla.submitbutton = function(task, type)
		{
			//console.debug(task);
			
			jQuery("form").submit(function() {
				jQuery("input").removeAttr("disabled");
			});
			
			if (task == 'roomrateprices.cancel' || validateForm() ) {
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		}	
		
		function validateForm(){
			return true;
			//console.debug("validate");
			if( !validateField( form.elements['room_name'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_ROOM_NAME',true); ?>" ) ){
				varTabPane.showTab(1);
				return false;
			}
			return true;
		}

        var dateFormat = '<?php echo  $this->appSettings->dateFormat; ?>';
        var language = '<?php echo JHotelUtil::getJoomlaLanguage();?>';
        var formatToDisplay = calendarFormat(dateFormat);

        jQuery(document).ready(function(){
            jQuery.fn.datepicker.defaults.language = language;
            jQuery.fn.datepicker.defaults.format = formatToDisplay;
        });


        jQuery("#start_date #end_date").datepicker({
            autoclose: true,
            toggleActive: true,
            language: language,
            format: formatToDisplay
        });

        jQuery("#datasf_img").click(function () {
            jQuery('#start_date').focus();
        });
        jQuery("#dataef_img").click(function () {
            jQuery('#end_date').focus();
        });

        jQuery('#allweek').click(function() {
            jQuery('.week-day').prop('checked', jQuery(this).is(':checked'));
        });
        jQuery('.week-day').click(function() {
            var numselected = jQuery('.week-day:checked').length;
            jQuery('#allweek').prop('checked',(numselected == 7));
        });
	
	</script>


