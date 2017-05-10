<?php 
/*------------------------------------------------------------------------
# JBusinessDirectory
# author CMSJunkie
# copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/j-hotelreservation	/?p=1
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reservations');?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset  class="boxed">
		<div class="form-box">
			<h2> <?php echo JText::_('LNG_IMPORT_FROM_CSV');?></h2>
			<div>
				<?php echo JText::_('LNG_IMPORT_FROM_CSV_TEXT');?>									
			</div>			
			<div class="form-upload-elem">
				<div class="form-upload">
					<label class="optional" for="csvFile"><?php echo JText::_("LNG_SELECT_CSV_FILE") ?>.</label>
						<input type="file" id="csvFile" name="csvFile" size="50">		
					<div class="clear"></div>
				</div>					
				
			</div>
			
			<div class="detail_box">
					<div  class="form-detail req"></div>
					<label for="delimiter"><?php echo JText::_('LNG_DELIMITER')?> </label> 
					<select name="delimiter">
						<option value=","><?php echo JText::_('LNG_COMMA')?></option>
						<option value=";"><?php echo JText::_('LNG_SEMICOLON')?></option>
						</select>
					
					<div class="clear"></div>
					
				</div>
				
			<div class="clear"></div>
			<input type="submit" name="submit" value="<?php echo JText::_("LNG_IMPORT");?>">		
			<span class="error_msg" id="frmCompanyName_error_msg" style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD')?></span>
		</div>
		
	</fieldset>
	
	
	 <input type="hidden" name="option"	value="<?php echo getBookingExtName()?>" />
	 <input type="hidden" name="task" id="task" value="reservations.batchCancelFromCsv" /> 
	 <?php echo JHTML::_( 'form.token' ); ?> 
</form>