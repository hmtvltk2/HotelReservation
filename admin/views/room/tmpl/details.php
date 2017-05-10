<?php
$appSettings = JHotelUtil::getApplicationSettings();
$dirs = JHotelUtil::languageTabs();
?>
<fieldset>
		<legend><?php echo JText::_('LNG_ROOM_DETAILS',true); ?></legend>

		<TABLE class="admintable" align=center border=0>
			<TR>
				<TD width=10% class="key"><?php echo JText::_('LNG_NAME',true); ?></TD>
				<TD style="width:100%" align=left>

                    <?php
                    $j=0;
                    echo JHtml::_('tabs.start', 'tab_room_name', $options);
                    
                    foreach( $dirs  as $_lng ){
                        $langName= JHotelUtil::languageNameTabs($_lng);
                        echo JHtml::_('tabs.panel',  $langName, 'roomName '.$j);
                        $langContent = isset( $this->room_name_translations[$_lng])? $this->room_name_translations[$_lng]:$this->item->room_name;
                        ?>
                            <input style='width:30%' tabindex=1 id='room_name_<?php echo $_lng;?>' class="validate[required] rooms text-input" name='room_name_<?php echo $_lng;?>' value="<?php echo htmlspecialchars($langContent);?>" />
                        <?php
                    }
                    ?>
				</TD>
				<TD>&nbsp;</TD>
			</TR>
			<TR>
				<TD width=10% class="key"><?php echo JText::_('LNG_AVAILABLE',true); ?></TD>
				<TD align=left id="is_available" class="radio btn-group btn-group-yesno">
					<input 
						type		= "radio"
						name		= "is_available"
						id			= "is_available0"
						value		= '1'
						<?php echo $this->item->is_available==true? " checked " :""?>
						accesskey	= "Y"
						
					/>
					<label
                        class="labelYes"
                        id="label_is_available0" for="is_available0"><?php echo JText::_('LNG_STR_YES',true); ?></label>
					&nbsp;
					<input 
						type		= "radio"
						name		= "is_available"
						id			= "is_available1"
						value		= '0'
						<?php echo $this->item->is_available==false? " checked " :""?>
						accesskey	= "N"
					/>
					<label
                        class="labelNo"
                        id="label_is_available1" for="is_available1"><?php echo JText::_('LNG_STR_NO',true); ?></label>
				</TD>

			</TR>
			<TR>
				<TD width=10% class="key"><?php echo JText::_('LNG_DISPLAY_ON_FRONT',true); ?></TD>
				<td align=left id="front_display" class="radio btn-group btn-group-yesno">
					<input 
						type		= "radio"
						name		= "front_display"
						id			= "front_display0"
						value		= '1'
						<?php echo $this->item->front_display==true? " checked " :""?>
						accesskey	= "Y"
						
					/>
					<label
                        class="labelYes"
                        id="label_front_display0"
                        for="front_display0"><?php echo JText::_('LNG_STR_YES',true); ?></label>
					&nbsp;
					<input 
						type		= "radio"
						name		= "front_display"
						id			= "front_display1"
						value		= '0'
						<?php echo $this->item->front_display==false? " checked " :""?>
						accesskey	= "N"
					/>
					<label
                        class="labelNo"
                        id="label_front_display1"
                        for="front_display1"><?php echo JText::_('LNG_STR_NO',true); ?></label>
				</td>
			</TR>
			<TR style="display:none">
				<TD width=10% class="key"><?php echo JText::_('LNG_SHORT_DESCRIPTION',true); ?>:</TD>
				<TD  colspan=2 ALIGN=LEFT>
					<textarea id='room_short_description' name='room_short_description' rows=2 cols=135><?php echo $this->item->room_short_description?></textarea>
				</TD>
			</TR>
			<TR>
				<TD width=10%  class="key"><?php echo JText::_('LNG_MAIN_DESCRIPTION',true); ?>:</TD>
				<TD  colspan=2 ALIGN=LEFT>
				<?php 
						$appSettings = JHotelUtil::getApplicationSettings();
						$options = array(
												    'onActive' => 'function(title, description){
												        description.setStyle("display", "block");
												        title.addClass("open").removeClass("closed");
												    }',
												    'onBackground' => 'function(title, description){
												        description.setStyle("display", "none");
												        title.addClass("closed").removeClass("open");
												    }',
												    'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
												    'useCookie' => true, // this must not be a string. Don't use quotes.
						);
						
						echo JHtml::_('tabs.start', 'tab_group_id', $options);
						//dmp($dirs);
						$j=0;
						foreach( $dirs  as $_lng ){

                            $langName= JHotelUtil::languageNameTabs($_lng);

                            echo JHtml::_('tabs.panel',  $langName , 'tab' . $j);

							$langContent = isset($this->translations[$_lng])?$this->translations[$_lng]:"";
							$editor =JFactory::getEditor();
							echo $editor->display('room_main_description_'.$_lng, $langContent, '800', '400', '70', '15', false);
							
						}
						echo JHtml::_('tabs.end');
					?>
				</TD>
			</TR>
			<!-- <TR >
				<TD width=10%  class="key"><?php echo JText::_('LNG_DETAILS',true); ?>:</TD>
				<TD  colspan=2 ALIGN=LEFT>
					<textarea id='room_details' name='room_details' rows=8 cols=121><?php echo $this->item->room_details?></textarea>
				</TD>
			</TR> -->
			
		</TABLE>
	</fieldset>
	
	<fieldset>
		<legend><?php echo JText::_('LNG_ROOM_CAPACITY',true); ?></legend>
		<?php
		
		$crt_limit	= 0;
		
		?>
		<input type='hidden' name='crt_interval_number' id='crt_interval_number' value='-1'>
		<div style='display:none'>
			<div id='div_calendar' class='div_calendar'>
				<p>
					<div class="dates_room_calendar" id="dates_room_calendar"></div>
				</p>
			</div>
		</div>
		<TABLE class="admintable" align=left id='table_room_numbers' name='table_room_numbers' >
			<TR>
				<TD class="key"><?php echo JText::_('LNG_MAX_ADULTS',true); ?> :</TD>
				<TD  align=left>
					<input 
						type		= "text"
						name		= "max_adults"
						id			= "max_adults"
						value		= '<?php echo $this->item->max_adults?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:center'
					/>
					<a 
						href	="javascript:;" 
						class	="tooltip" 
						title	="<?php echo JText::_('LNG_INFO_MAX_ADULTS',true)?>"
					>
						<img src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/help-icon-NLP.png"?>"
						/>
					</a>
				</TD>
				<TD align=left colspan="3">&nbsp;</TD>
			</TR>
			<?php if($this->appSettings->show_children!=0){ ?>
			<?php } ?>
		</TABLE>
	</fieldset>
