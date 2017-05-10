<?php 
$index =0;
foreach( $this->excursions as $excursion ){
	if(!$excursion->front_display){
		continue;
	}
	
	$index++;
}
?>

<?php if($index>0){ ?>
<br>
<TABLE width="100%" valign="top" border="0" class="table_info excursions" cellspacing="0" >
<tr class="tr_header">
		<TH width=2%>&nbsp;</TH>
		<TH align="left"><?php echo JText::_('LNG_EXCURSIONS',true)?> </TH>
		<TH width=10% class="hidden-phone"><?php echo JText::_('LNG_CAPACITY_PERS',true)?></TH>
		<?php if($this->appSettings->show_children!=0 && 1==0){ ?>
			<TH width=10% class="hidden-phone"><?php echo JText::_('LNG_CHILDREN',true)?></TH>
		<?php } ?>
		<TH width=10% align=right>
		<?php 
		if(JRequest::getVar( 'show_price_per_person')==1){ 
			echo JText::_('LNG_PRICE_PER_PERSON',true);
		}else{ 
			echo JText::_('LNG_PRICE',true);
		}
		?>
	    </TH>
		<TH align=right width="15%" class="hidden-phone" ></TH> 
		<TH align=right width="15%" class="hidden-phone" >&nbsp;</TH> 
	</tr>
<?php
$index = 0;
$showRoomHeader = true;
$noOfferFound = true;
foreach( $this->excursions as $excursion ){
	
	//dmp($excursion);
	$daily = $excursion->daily;
	$price_per_person = 0;

	if($price_per_person == 0)
		$price_per_person = $excursion->price_type;

	//dmp($excursion->public);
	//when searching with voucher only offer with searched voucher should be displayed


	if(!$excursion->front_display){
		continue;
	}

	 
	$grand_total = 0;
	foreach( $daily as $day )
	{
		$p = $day['display_price_final'];
		$grand_total += $p;
	}


		
	?>
	
	
	<tr>
		<td align=center>
			<input 
				type	= 'checkbox' 
				name	= 'excursion_ids[]'
				id		= 'excursion_ids[]'
				value	= '<?php echo '0|'.$excursion->id?>'
				onmouseover			=	"this.style.cursor='hand';this.style.cursor='pointer'"
				onmouseout			=	"this.style.cursor='default'"
				style				= 	"display:none;"
			>
		</td>
		<td id="<?php echo "excursion_".$excursion->id;?>" align=left nowrap >
			<div class="trigger open">
				<div class="excursion_expand"></div>
				<?php 
					$itemName =$excursion->excursion_name;
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
				&nbsp;|&nbsp;
				<a class="link_more" href="#">&nbsp;<?php echo JText::_('LNG_MORE',true)?> »</a>
			</div>
		</td>
		
		<td align=center class="hidden-phone">
			<?php echo $excursion->capacity;?>
		</td>
		
		<td align=right >
			<?php 
				if(!$excursion->is_disabled){
					if(JRequest::getVar( 'show_price_per_person')==1){
						echo $excursion->pers_total_price;
					}else{ 
						echo $excursion->excursion_average_display_price;
					}
				}
			?>
		</td>
		<td align=center class="hidden-phone" >
			<ul style='margin-top:0px'>
			<?php
			$crt_excursion_sel  = 1;
			
			$datas = ( $this->userData->year_start.'-'.$this->userData->month_start.'-'.$this->userData->day_start );
			$datae = ( $this->userData->year_end.'-'.$this->userData->month_end.'-'.$this->userData->day_end );
			
			$interval = date_diff(new DateTime($datas), new DateTime($datae));
			$nrDays = $interval->format('%a');
			
			?>
			</ul>
		</td>
		<td colspan=1 align=right style="padding-left:15px;" >
			<?php
			$is_checked = false;
			
			if ($nrDays < $excursion->min_days) {
				//dmp($nrDays);
				$text = JText::_('LNG_MINIMUM_DAYS',true);
				$text = str_replace("<<min_days>>",	$excursion->min_days, $text);
				echo $text;
			} else if ($nrDays > $excursion->max_days && $excursion->max_days!=0) {
				//dmp($nrDays);
				$text = JText::_('LNG_MAXIMUM_DAYS',true);
				$text = str_replace("<<max_days>>",	$excursion->max_days, $text);
				echo $text;
			}else if(!$excursion->is_disabled){
			?>
			<select id	= 'excursionId<?php echo $excursion->excursion_id;?>' name= 'excursions[]' 
					<?php echo $excursion->is_disabled? " disabled " : "" ?> 					
					<?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'disabled' : ''?>
					style="width: 85px;"
			>
				<option value="0"> --Select-- </option>
				<?php for($i=1;$i<=$excursion->capacity;$i++){
					echo "<option value='".$excursion->excursion_id."_"."$i'>".$i."</option>";
				}
				?>
			</select>
			
		
			<?php }else{ 
				$buttonLabel = JText::_('LNG_CHECK_DATES',true);
				$class="";

				?>
				<span class="button button-white trigger open <?php echo $class?>">
					<button
						class="reservation <?php echo count($this->userData->reservedItems)  == $this->userData->excursions ? 'not-bookable' : ''?>"
						name="check-button"
						value		= "<?php echo $buttonLabel?>"
						type		= 'button'
						<?php echo count($this->userData->reservedItems)  == $this->userData->excursions ? 'disabled' : ''?>
					><?php echo $buttonLabel ?>
					</button>
				</span>
			<?php } ?>
			
		</td>
	</tr>
	<tr class="tr_cnt">
		<td class="td_cnt" colspan="9" >
			<div class="cnt">
				<div class="excursion-description">
					<div class="tabs-container" style="display:none">
							<ul style="display:block">
								<li><a href="#tabs-1_<?php echo $excursion->excursion_id; ?>"><?php echo JText::_('LNG_ROOM_DETAILS',true)?></a></li>
								<li><a href="#tabs-2_<?php echo $excursion->excursion_id; ?>"><?php echo JText::_('LNG_RATE',true)?></a></li>
								<li><a href="#tabs-3_<?php echo $excursion->excursion_id; ?>"><?php echo JText::_('LNG_RATE_RULES',true)?></a></li>
							</ul>
							<div id="tabs-1_<?php echo $excursion->excursion_id; ?>">
								<?php echo $excursion->excursion_details?>
							</div>
							<div id="tabs-2_<?php echo $excursion->excursion_id; ?>">
								<?php echo JText::_('LNG_PRICE_BREAKDOWN_BY_NIGHT',true)?>
								<div class="price_breakdown">
									<table >
									<?php
									$grand_total = 0;
									foreach( $excursion->daily as $daily )
									{
										$p 		= $daily['display_price_final'];
										$day	= $daily['date'];
										echo '<tr><td>'.date('D d M', strtotime($day)).'</td><td>'.JHotelUtil::fmt($p,2).' '.$this->userData->currency->symbol.'</td></tr>';
										$grand_total += JHotelUtil::fmt($p,2);	
									}
									?>
										<tr class="price_breakdown_grad_total">
											<td>
												<strong> = <?php echo JText::_('LNG_GRAND_TOTAL',true) ?></strong>
											</td>
											<td>
												<?php echo JHotelUtil::fmt($grand_total,2); ?> <?php //echo $this->_models['variables']->currency_selector?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div id="tabs-3_<?php echo $excursion->excursion_id; ?>">
								<?php echo JText::_('LNG_RATE_RULES_DESCRIPTION',true)?>
							</div>
						</div>
			
				
					<div id="calendar-holder-<?php echo $excursion->excursion_id+1000?>" class="excursion-calendar">	
						<div class="excursion-loader right"></div>
						<?php  
							if(isset($this->_models['variables']->availabilityCalendar)){
								$calendar =  $this->_models['variables']->availabilityCalendar;
								$id= $excursion->excursion_id;
								echo $calendar[$id];
							}
							
							if(isset($this->_models['variables']->defaultAvailabilityCalendar)){
								echo $this->_models['variables']->defaultAvailabilityCalendar;
							}
						?>
					</div>
					<div class="excursion_main_description">
					
						<?php
						$description = $excursion->excursion_main_description; 
						echo $description;
						?>
					</div>
				</div>
				
				<div class='picture-container'>
					<?php 
					if( isset($excursion->pictures) && count($excursion->pictures) >0 )
					{
						foreach( $excursion->pictures as $picture )
						{
					?>
						<a class="preview" onclick="return false;" title="<?php echo isset($picture->picture_info)?$picture->picture_info:'' ?>" alt="<?php echo isset($picture->picture_info)?$picture->picture_info:'' ?>" href="<?php echo JURI::base() .PATH_PICTURES.$picture->picture_path?>">
							<img 
									class="img_picture"
									style="height: 50px"
									src='<?php echo JURI::base() .PATH_PICTURES.$picture->picture_path?>' 
									alt="<?php echo isset($picture->picture_info)?$picture->picture_info:'' ?>"
									title="<?php echo isset($picture->picture_info)?$picture->picture_info:'' ?>"
							/>
						</a>
					<?php
					}
					}
					?>
				</div>
			</div>
		</td>
	</tr>
	<?php
	}
	?>
		
		
</TABLE>
<?php } ?>