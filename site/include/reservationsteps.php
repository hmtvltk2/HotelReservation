<?php // no direct access

defined('_JEXEC') or die('Restricted access'); 

$arr_titles = array	(
						-2=>	JText::_('LNG_LIST_HOTELS'),
						-1=>	JText::_('LNG_HOTEL_DESCRIPTIONS'),
						0=>		JText::_('LNG_DATE_AND_PREFERENCES'),
						1=>		JText::_('LNG_DATE_AND_PREFERENCES'),
						2=>		JText::_('LNG_ROOMS_AND_RATES'),
						3=>		JText::_('LNG_EXTRA_OPTIONS'),
						4=>		JText::_('LNG_GUEST_INFORMATION'),
						5=>		JText::_('LNG_PARK_PAYMENT_OPTIONS'),
						6=>		JText::_('LNG_CONFIRMATION')
);

$availTabs = array	(
		1=>		'',
		2=>		'roomrates',
		3=>		'extraoptions',
		4=>		'guestDetails',
		5=>		'paymentoptions',
		6=>		'confirmation'
		);


?>
<div class="reservation-steps  hidden-phone" id="reservation-steps">
	<div class="tabs">
		<ul>
			<?php
			$index = 0;
			$class2 = "completed";
			for( $i=2; $i <=6; $i++ ){
				$index++;
				
				if(!$this->appSettings->is_enable_extra_options && $i==3){
					$index--;
					continue;
				}
				
				$class = "noClass";
					
				 if(JRequest::getVar( 'view')== $availTabs[$i]){
				 	$class = "selected";
				 	$class2 = "";
				 }
				
				
			?>
			<li class = '<?php echo $class.' '.$class2?>'>
				<a href="javascript:void(0)">
					<span class="number"><?php echo $index?>.</span>
					<span ><?php echo $arr_titles[$i]?></span>
				</a>
			</li>
				<?php
			}
			?>
		</ul>
	</div>
</div> 
