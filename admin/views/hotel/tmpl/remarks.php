<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

?>
<div id="remarks_admin">
	<br style="font-size: 1px;" />
	<fieldset>
		<legend><?php echo JText::_( 'LNG_REMARKS' ,true); ?></legend>
		<div class="admintable">
			<div class="section group">
				<div  class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_REMARKS',true); ?>:</div>
				<div  class="col column_8_of_12 hotel-editor" >
					<?php
					$remarks = !empty($this->item->remarks)?$this->item->remarks:'';
					$editor =JFactory::getEditor();
					
					echo $editor->display('remarks',$remarks , '800', '400', '70', '15', false);
						
					?>
				</div>
		</div>
	</fieldset>
</div>