<fieldset>
		<legend><?php echo JText::_('LNG_ROOM_RATE')." (rate id:".$this->item->rate->id.")"; ?></legend>
		<input type="hidden" id="rate_id" name="rate_id" value = "<?php echo $this->item->rate->id ?>">
		 <table class="admintable rommprice">
			 <tr>
				<td class="key">
					<?php echo JText::_('LNG_RATE_NAME',true)?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "name"
						id			= "name"
						value		= '<?php echo $this->item->rate->name ?>'
						size		= 30
						maxlength	= 70
					/>
				</td>
			</tr>
		    <tr>
				<td class="key">
					<?php echo JText::_('LNG_RATE_DESCRIPTION',true)?>
				</td>
				<td>
					
					<?php 
						echo $editor->display('rate_description', $this->item->rate->rate_description, '800', '300', '70', '15', false);
					?>
				</td>
			</tr>
			
			 <tr style="display:none">
				<td class="key">
					<?php echo JText::_('LNG_RATE_CAN_CANCEL',true)?>
				</td>
				<td>
					<?php 
					$arr = array(
							JHTML::_('select.option',  '0', JText::_( "LNG_YES",true) ),
							JHTML::_('select.option',  '1', JText::_( "LNG_NO" ,true) )
					);
					?>
					<?php echo JHtml::_( 'select.radiolist', $arr, 'can_cancel', '', 'value', 'text', $this->item->rate->can_cancel,'can_cancel'); ?> 
				</td>
			</tr>
		 
		   <tr style="display:none">
				<td class="key">
					<?php echo JText::_('LNG_RATE_CANCEL_DAYS',true)?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "cancel_days"
						id			= "cancel_days"
						value		= '<?php echo $this->item->rate->cancel_days ?>'
						size		= 10
						maxlength	= 10
					/>
					<?php echo JText::_('LNG_RATE_CANCEL_TEXT',true)?>
				</td>
			</tr>
			
			 <tr style="display:none">
				<td class="key">
					<?php echo JText::_('LNG_RATE_CANCELATION_POLICY',true)?>
				</td>
				<td>
					<?php 
						echo $editor->display('cancel_text', $this->item->rate->cancel_text, '800', '400', '70', '15', false);
					?>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('LNG_ROOM_RATE_DEFAULT_SETTINGS',true); ?></legend>
		 <table class="admintable rommprice">
			<TR>
				<TD width=10%  class="key"><?php echo JText::_('LNG_PRICE_TYPE',true); ?></TD>
				<td  align=left id="price_type" class="radio btn-group btn-group-yesno">
					<input 
						style		= 'float:none'
						type		= "radio"
						name		= "price_type"
						id			= "price_type0"
						onclick 	= "updateStatus()"
						value		= '1'
						<?php echo $this->item->rate->price_type==1? " checked " :""?>
					/>
					<label
                        class="labelYes"
                        for="price_type0"
                        id="label_price_type0"><?php echo JText::_('LNG_PER_PERSON',true); ?></label>
					&nbsp;
					<input 
						style		= 'float:none'
						type		= "radio"
						name		= "price_type"
						id			= "price_type1"
						value		= '0'
						onclick 	= "updateStatus()"
						<?php echo $this->item->rate->price_type==0? " checked " :""?>
					/>
					<label
                        class="labelNo"
                        for="price_type1"
                        id="label_price_type1"><?php echo JText::_('LNG_PER_ROOM',true); ?></label>
				</td>
			</TR>
			<TR>
				<TD width=10%  class="key"><?php echo JText::_('LNG_PRICE',true); ?></TD>
				<td colspan=2 valign=top align=left>
					
					<div id='div_price_day_by_day' name='div_price_day_by_day'>
						<TABLE class='admintable' align=left border=0 width=100%>
							<TR>
								<td colspan=2 valign=top align=left>
									<TABLE cellpadding=0 cellspacing=0 align=left class="admintable" align=center border=0
										id='table_room_price_days' name='table_room_price_days' 
									>
										<TR id="RoomTablePriceDays">
											<?php
											for($day=1;$day<=7;$day++)
											{
											?>
											<TD id="TdPriceDays" align=center>
												<i>
												<?php		
												switch( $day )
												{
													case 1:
														echo JText::_('LNG_MON',true);
														break;
													case 2:
														echo JText::_('LNG_TUE',true);
														break;
													case 3:
														echo JText::_('LNG_WED',true);
														break;
													case 4:
														echo JText::_('LNG_THU',true);
														break;
													case 5:
														echo JText::_('LNG_FRI',true);
														break;
													case 6:
														echo JText::_('LNG_SAT',true);
														break;
													case 7:
														echo JText::_('LNG_SUN',true);
														break;
												}
												?>
												</i>
											</TD>
												<?php
											}
											?>
											<TD rowspan=2% width=40%>
												&nbsp;
											</TD>
										</TR>
										<TR id="RoomTablePrices" >
											<?php
											for($day=1;$day<=7;$day++)
											{
												switch( $day )
												{
													case 1:
														$p = $this->item->rate->price_1;
														break;
													case 2:
														$p = $this->item->rate->price_2;
														break;
													case 3:
														$p = $this->item->rate->price_3;
														break;
													case 4:
														$p = $this->item->rate->price_4;
														break;
													case 5:
														$p = $this->item->rate->price_5;
														break;
													case 6:
														$p = $this->item->rate->price_6;
														break;
													case 7:
														$p = $this->item->rate->price_7;
														break;
												}
											?>
											<TD id="Price" width=1% align=left valign=center>
												<input 
													type		= "text"
													name		= "price_<?php echo $day?>"
													id			= "price_<?php echo $day?>"
													value		= '<?php echo $p?>'
													size		= 10
													maxlength	= 10
													
													style		= 'text-align:right'
												/>
											</td>
											<?php
											}
											?>
										</TR>
									</TABLE>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('LNG_NUMBER_OF_ROOMS',true)?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "availability"
						id			= "availability"
						value		= '<?php echo $this->item->rate->availability ?>'
						size		= 10
						maxlength	= 10
					/>
				</td>
				<?php if($this->item->hasCubilis){?>
				<td id="note">
					<div>
						<div class="red">
							<?php echo JText::_('LNG_USAGE_NOTE',true);?>
						</div>
						<p  class="red">
							<?php echo JText::_('LNG_CUBILIS_INFO',true);?>
						</p>
					</div>
				</td>
				<?php } ?>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('LNG_BASE_ADULTS',true)?> (<?php echo JText::_('LNG_MAX',true).' '.$this->item->max_adults?>)
				</td>
				<td>
					<input 
						type		= "text"
						name		= "base_adults"
						id			= "base_adults"
						value		= '<?php echo $this->item->rate->base_adults ?>'
						size		= 10
						maxlength	= 10	
					
					/>
				</td>
				<td rowspan="3">
					 <div id="customRates"><?php echo JText::_('LNG_CUSTOM_RATES_NOTICE',true)?></div>
				</td> 
			</tr>
			
			<?php if($this->appSettings->show_children!=0){ ?>				
				<tr>
					<td class="key">
						<?php echo JText::_('LNG_BASE_CHILDREN',true)?>
					</td>
					<td>
						<input 
							type		= "text"
							name		= "base_children"
							id			= "base_children"
							value		= '<?php echo $this->item->rate->base_children ?>'
							size		= 10
							maxlength	= 10	
							
						/>
					</td>
				</tr>		
							
			<tr>
				<td class="key">
					<?php echo JText::_('LNG_CHILD_PRICE',true)?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "child_price"
						id			= "child_price"
						value		= '<?php echo $this->item->rate->child_price ?>'
						size		= 10
						maxlength	= 10
					/>
				</td>
			</tr>	 
				<?php foreach($this->childrenCategories as $childCategory){?>	 
					<tr>
						<td class="key">
							<?php echo $childCategory->category_name;?>
						</td>
						<td>
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
				
			<?php }?>
				
		
			<tr>
				<td class="key">
					<div id="single-supplement-container">
						<?php echo JText::_('LNG_SINGLE_SUPPLEMENT')?>
					</div>
					<div id="single-discount-container">
						<?php echo JText::_('LNG_SINGLE_DISCOUNT')?>
					</div>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "single_balancing"
						id			= "single_balancing"
						value		= '<?php echo $this->item->rate->single_balancing?>'
						size		= 10
						maxlength	= 10
					/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('LNG_EXTRA_PERS_PRICE',true)?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "extra_pers_price"
						id			= "extra_pers_price"
						value		= '<?php echo $this->item->rate->extra_pers_price?>'
						size		= 10
						maxlength	= 10
					/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('LNG_MIN_DAYS',true)?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "min_days"
						id			= "min_days"
						value		= '<?php echo $this->item->rate->min_days?>'
						size		= 10
						maxlength	= 10
					/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('LNG_MAX_DAYS',true)?>
				</td>
				<td>
					<input 
						type		= "text"
						name		= "max_days"
						id			= "max_days"
						value		= '<?php echo $this->item->rate->max_days?>'
						size		= 10
						maxlength	= 10
					/>
				</td>
			</tr>
		 </table>

        <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery(window).resize(function () {
                responsiveRatePrices("#RoomTablePriceDays","#RoomTablePrices","#Price input");
            });
        });
        </script>
	</fieldset>