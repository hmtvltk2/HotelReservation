<?php defined('_JEXEC') or die('Restricted access');

/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
?>

<form action="<?php echo JRoute::_('index.php?option='.getBookingExtName()); ?>" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<table class="table  adminlist " id="itemList" >
            <thead>
                <th width='1%'>#</th>
                <th width='1%'  align=center><?php echo JHtml::_('grid.checkall'); ?></th>
                <Th width='20%' align=left><B><?php echo JText::_('LNG_NAME',true); ?></B></Th>
                <Th width='30%' align=left ><B><?php echo JText::_('LNG_SYMBOL',true); ?></B></Th>
            </thead>
            <tbody>
            <?php
            $nrcrt = 1;
            foreach($this->items as $i => $currency)
            {
                ?>
                <TR class="row<?php echo $i%2?>"
                    onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
                    onmouseout	=	"this.style.cursor='default'"
                    >
                    <TD align=center><?php echo $nrcrt++?></TD>

                    <TD align=center>
                        <?php echo JHtml::_('grid.id', $i, $currency->currency_id); ?>
                    </TD>
                    <TD align=left>

                        <a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=currency&layout=edit&id='. $currency->currency_id )?>'
                           title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"
                            >
                            <B><?php echo $currency->description?></B>
                        </a>

                    </TD align=left>
                    <td><?php echo $currency->currency_symbol?></td>

                </TR>
            <?php
            }
            ?>
            </tbody>
            <tfoot><tr>
                <td colspan="3"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
            </tfoot>
		</table>
	</div>
    <script type="text/javascript">
        Joomla.submitbutton = function(task)
        {
            if (task != 'currencies.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>'))
            {
                Joomla.submitform(task);
            }
        }
    </script>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>