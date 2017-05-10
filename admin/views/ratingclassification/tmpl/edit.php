<?php
/**
 * @package    JHotelReservation
 * @subpackage  com_jhotelreservation
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
$appSetings = JHotelUtil::getApplicationSettings();
$dirs = JHotelUtil::languageTabs();
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=ratingclassification');?>" method="post" name="adminForm" id="adminForm">

	<fieldset>
		<legend><?php echo JText::_('LNG_CLASSIFICATION',true); ?></legend>
		
		<div class="admintable">
			<div class="section group">
				<div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_NAME',true); ?> </div>
				<div class="col column_10_of_12">
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
							$langName = JHotelUtil::languageNameTabs($_lng);
							echo JHtml::_('tabs.panel',$langName, 'tab'.$j );
							$langContent = isset($this->translations[$_lng])?$this->translations[$_lng]:$this->item->name;
                            $langContent = htmlspecialchars($langContent);
                            ?>

                            <input type="text" name="name_<?php echo $_lng?>" value="<?php echo $langContent?>" size="100">
                    <?php
						};
						echo JHtml::_('tabs.end');
					  ?>
				</div>
			</div>
			<div class="section group">
				<div class="key labelFields col column_2_of_12"><?php echo JText::_( 'LNG_RATE_SCORE' ,true); ?> :</div>
				<div class="col column_10_of_12">
					<input 
						type		= "text"
						name		= "min_rate"
						id			= "min_rate"
						value		= "<?php echo $this->item->min_rate >=0 ? $this->item->min_rate:''?>"
						size		= 2
						maxlength	= 5
						placeholder="<?php echo JText::_( 'LNG_MIN' ,true); ?>"					
					/>
					&nbsp;:&nbsp;
					<input 
						type		= "text"
						name		= "max_rate"
						id			= "max_rate"
						value		= '<?php echo $this->item->max_rate !=0 && $this->item->min_rate < $this->item->max_rate ? $this->item->max_rate : ''?>'
						size		= 2
						maxlength	= 5
						placeholder="<?php echo JText::_( 'LNG_MAX' ,true); ?>"					
					/>
				</div>
			</div>
		</div>
	</fieldset>
	

	<input type="hidden" name="hotel_id" value="<?php echo $this->state->get('ratingclassification.hotel_id') ?>" />
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>