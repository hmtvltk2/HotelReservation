<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.modal','a.modal');
?>
<div>
<form action="index.php?option=<?php echo getBookingExtName() ?>&view=language&task=language.editLanguage&code=<?php echo $this->file->name?>" method="post" name="adminForm"  id="adminForm" autocomplete="off">
	<fieldset class="acyheaderarea">
		<div class="acyheader" style="float: left;"><?php echo JText::_('LNG_FILE',true).' : '.$this->file->name; ?></div>
		<div class="toolbar" id="toolbar" style="float: right;">
			<table><tr>
			<td><button class="btn btn-default" id="languageSaveButton" onclick="Joomla.submitbutton('language.apply');" title="<?php echo JText::_('LNG_SAVE',true); ?>" ><span class="icon-save"></span>
                    <?php echo JText::_('LNG_SAVE',true); ?></button></td>
                    <td>
                        <button class="btn btn-info" id="languageSaveButton" onclick="Joomla.submitbutton('language.send_email');" title="<?php echo JText::_('LNG_SEND_THE_LANGUAGE_FILES_TO_AUTHOR',true); ?>">
                        <span class="icon-envelope" style="color:#fff;"></span>
                        <?php echo JText::_('LNG_SEND_EMAIL',true); ?>
                    </button>
                    </td>
			</tr></table>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('LNG_FILE',true).' : '.$this->file->name;?>
		</legend>
		<textarea rows="30" name="content" id="translation"><?php echo $this->file->content;?></textarea>
	</fieldset>
    <fieldset>
        <legend><?php echo JText::_('LNG_FILE_CUSTOM',true);
            ?>
        </legend>
        <textarea rows="18" name="custom_content"  id="translation"><?php echo $this->file->custom_content;?></textarea>
    </fieldset>


	<div class="clr"></div>
	<input type="hidden" name="code" value="<?php echo $this->file->name; ?>" />
	<input type="hidden" name="option" value="<?php echo getBookingExtName(); ?>" />
	<input type="hidden" name="task" value="language.saveLanguage" />
    <input type="hidden" name="ctrl" value="file" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
<script language="javascript" type="text/javascript">
    jQuery(document).ready(function () {
        var iframeButton = jQuery("#sbox-window #sbox-content>iframe").find("#languageSaveButton");

        console.log(iframeButton);
        jQuery(iframeButton).mouseup(function () {
            jQuery('#sbox-overlay').remove();
            jQuery('#sbox-window').remove();
        });
    });
</script>

