<?php
/**
 * @copyright	Copyright (C) 2009-2012 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
$imagePath = JURI::base() ."components/".getBookingExtName()."/assets/img/edit.png";
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task != 'language.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true,true);?>'))
        {
            Joomla.submitform(task);
        }
    }
</script>
<br  style="font-size:1px;" />
<fieldset>
    <div class="toolbar" id="toolbar" style="float:right;">
        <table>
            <tr>
                <td>
                    <button class="btn btn-danger btn-small" onclick="Joomla.submitbutton('language.delete');" title="<?php echo JText::_('LNG_DELETE_LANGUAGES',true); ?>">
                        <span class="icon-cancel"></span>
                        <?php echo JText::_('LNG_DELETE',true); ?>
                    </button>
                </td>
            </tr>
        </table>
    </div>
</fieldset>
 <fieldset>
		<legend><?php echo JText::_('LNG_HOTEL_LANGUAGES',true) ?></legend>
	
			<table class="table adminlist"  id="itemList">
				<thead>
				<tr>
					<th class="title titlenum">
						<?php echo JText::_('LNG_NUMBER',true); ?>
					</th>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
					<th class="title titletoggle">
						<?php echo JText::_('LNG_EDIT',true); ?>
					</th>
					<th class="title">
						<?php echo JText::_('LNG_NAME',true); ?>
					</th>
					<th class="hidden-phone title titletoggle">
						<?php echo JText::_('LNG_ID',true); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$k = 0;
					for($i = 0,$a = count($this->languages);$i<$a;$i++) {
                        $row = $this->languages[$i];

                        //dmp($row);
                        ?>
                        <tr class="<?php echo "row$k"; ?>">
                            <td align="center">
                                <?php echo $i + 1; ?>
                            </td>
                            <TD class="hidden-phone" align=center>
                                <?php echo JHtml::_('grid.id', $i, $row->language); ?>
                            </TD>
                            <td align="center">
                                <a class="modal"
                                   name="modal"
                                   rel="{handler: 'iframe', size:{x:800, y:650}}"
                                   href='<?php echo JRoute::_( 'index.php?option=com_jhotelreservation&tmpl=component&view=language&task=language.editLanguage&code='.$row->language )?>'
                                   title="<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>">
                                    <img class="icon16" src="<?php echo $imagePath;?>" alt="<?php JText::_('LNG_EDIT_LANGUAGE_FILE',true) ?>"/>
                                </a>

                            </td>
                            <td align="center">
                                <a class="modal"
                                   name="modal"
                                   rel="{handler: 'iframe', size:{x:800, y:650}}"
                                    href='<?php echo JRoute::_( 'index.php?option=com_jhotelreservation&tmpl=component&view=language&task=language.editLanguage&code='.$row->language )?>'
                                    title="<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>">
                                    <?php echo $row->name; ?>
                                </a>
                            </td>
                            <td class="hidden-phone" align="center">
                                <?php echo $row->language; ?>
                            </td>
                        </tr>
                        <?php
                        $k = 1 - $k;
                    }

				?>
			</tbody>
		</table>
	</fieldset>
<script>
        jQuery('a.modal').on('click',function(){
//            e.preventDefault();
            jQuery(document).scrollTop( 50 );
        });
</script>