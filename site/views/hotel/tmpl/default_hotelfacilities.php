<div class="hotel-facilities hotel-item">
	
	<h2><?php echo JText::_('LNG_HOTEL_FACILITIES')?> <?php echo $this->hotel->hotel_name; ?></h2>
		<ul class="blue">
			<?php 
			foreach($this->hotel->facilities as $facility) {
                $languageFacilities = JText::_('LNG_'.strtoupper(str_replace(" ","_",$facility->name)));
                if ($facility->name == $languageFacilities) {
                    ?>
                    <li><?php echo JText::_('LNG_'.strtoupper(str_replace(" ","_",$facility->name))); ?></li>
                <?php
                } else {
                    ?>
                    <li><?php
                        //Values are not defined in the translation file
                        echo JText::_('LNG_'.strtoupper(str_replace(" ","_",$facility->name))); ?></li>
                <?php
                }
            } ?>
		</ul>
</div>