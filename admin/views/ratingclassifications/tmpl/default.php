<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
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
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= true;
$saveOrder	= $listOrder == 'rc.ordering';

if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_jhotelreservation&task=ratingclassifications.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>


<form action="<?php echo JRoute::_('index.php?option='.getBookingExtName().'&view=ratingclassifications'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="editcell">
        <fieldset>
            <legend><?php echo JText::_('LNG_MANAGE_RATING_CLASSIFICATION',true); ?></legend>
                <div class="responsive_table-responsive-vertical">
                    <table class="responsive_table responsive_table-hover responsive_table-mc-light-blue" id="itemList">
                        <thead>
                          <tr>
                            <th width="1%" class="hidden-phone">
                                <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'rc.ordering', $listDirn, $listOrder);?>
                            </th>
                            <th width='1%'>#</th>
                            <th width="1%"  class="hidden-phone">
	                            <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL',true); ?>" onclick="Joomla.checkAll(this)" />
                            </th>
                            <th width='25%' class=" "><B><?php echo JHtml::_('grid.sort', 'LNG_NAME', 'rc.name', $listDirn, $listOrder);?></B></th>
                            <th width='25%' class=" "><B><?php echo JHtml::_('grid.sort', 'LNG_RATING_SCORE_MIN', 'rc.min_rate', $listDirn, $listOrder);?> </B></th>
                            <th width='25%' class=" "><B><?php echo JHtml::_('grid.sort', 'LNG_RATING_SCORE_MAX', 'rc.max_rate', $listDirn, $listOrder);?></B></th>
                            <th width='1%'><?php echo JHtml::_('grid.sort', 'LNG_ID', 'rc.id', $listDirn, $listOrder);?></th>
                          </tr>
                        </thead>
                        <tbody>

                        <?php
                        $nrcrt = 1;
                        foreach ($this->items as $i =>$item)
                        {
	                        $name      = $this->translationsModel->getObjectTranslation( RATE_CLASSIFICATION_TRANSLATION, $item->id, JRequest::getVar( '_lang' ) );
	                        $ordering  = ( $listOrder == 'rc.ordering' );
	                        $canCreate = true;
	                        $canEdit   = true;
	                        $canChange = true
	                        ?>
	                        <TR class="row<?php echo $i % 2 ?>"
	                            onmouseover="this.style.cursor='hand';this.style.cursor='pointer'"
	                            onmouseout="this.style.cursor='default'"
	                        >
		                        <td class="order hidden-phone">
			                        <?php
			                        $iconClass = '';
			                        if ( ! $canChange )
			                        {
				                        $iconClass = ' inactive';
			                        }
			                        elseif ( ! $saveOrder )
			                        {
				                        $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText( 'JORDERINGDISABLED' );
			                        }
			                        ?>
			                        <span class="sortable-handler <?php echo $iconClass ?>">
									<i class="icon-menu"></i>
								</span>
			                        <?php if ( $canChange && $saveOrder ) : ?>
				                        <input type="text" style="display:none" name="order[]" size="5"
				                               value="<?php echo $item->ordering; ?>"
				                               class="width-20 text-area-order "/>
			                        <?php endif; ?>
		                        </td>
		                        <TD data-title="#"><?php echo $nrcrt ++ ?></TD>
		                        <TD data-title="">
			                        <?php echo JHtml::_( 'grid.id', $i, $item->id ); ?>
		                        </TD>
		                        <td align=left data-title="<?php echo JText::_( 'LNG_NAME', true ) ?>"
		                            class="word-break-all">

			                        <a href='<?php echo JRoute::_( 'index.php?option=' . getBookingExtName() . '&view=ratingclassification&layout=edit&id=' . $item->id ) ?>'
			                           title="<?php echo JText::_( 'LNG_CLICK_TO_EDIT', true ); ?>"
			                        >
				                        <B><?php echo $item->name ?></B>
			                        </a>
		                        </td>
		                        <td data-title="<?php echo JText::_( 'LNG_RATING_SCORE_MIN', true ) ?>"
		                            class="word-break-all">
			                        <?php echo $item->min_rate; ?>
		                        </td>
		                        <td data-title="<?php echo JText::_( 'LNG_RATING_SCORE_MAX', true ) ?>"
		                            class="word-break-all">
			                        <?php echo $item->max_rate; ?>
		                        </td>
		                        <td data-title="<?php echo JText::_( 'LNG_ID', true ) ?>">
			                        <?php echo $item->id ?>
		                        </td>
	                        </TR>
	                        <?php
                        }
                        ?>
                        <tbody>
                    </table>
                </div>
        </fieldset>
    </div>
    <input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
    <input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
    <script language="javascript" type="text/javascript">

        Joomla.submitbutton = function(task)
        {
            if (task != 'ratingclassifications.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>'))
            {
                Joomla.submitform(task);
            }
        }
    </script>
</form>


