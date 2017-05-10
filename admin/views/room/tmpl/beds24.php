<?php
$appSettings = JHotelUtil::getApplicationSettings();
$dirs = JHotelUtil::languageTabs();
?>
<fieldset>
		<legend><?php echo JText::_( 'LNG_BEDS24'); ?></legend>
		<div class="admintable">
			<div class="section group">
				<div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_BEDS24_ROOMID'); ?></div>
				<div class="col column_10_of_12">
					<input 
						type		= "text"
						name		= "beds24_room_id"
						id			= "beds24_room_id"
						value		= "<?php echo htmlspecialchars($this->item->beds24_room_id)?>"
						size		= 64
						maxlength	= 128
					/>
				</div>
			</div>
		</div>
</fieldset>
