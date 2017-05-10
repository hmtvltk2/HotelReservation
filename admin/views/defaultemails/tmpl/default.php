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
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task != 'defaultemails.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>'))
        {
            Joomla.submitform(task);
        }
    }
</script>
<form action="<?php echo JRoute::_('index.php?option='.getBookingExtName()); ?>" method="post" name="adminForm" id="adminForm">
    <div id="editcell">
        <fieldset>
            <legend><?php echo JText::_('LNG_MANAGE_EMAIL_DEFAULT',true); ?></legend>
        </fieldset>
            <div class="clearfix"> </div>
            <div  class="responsive_table-responsive-vertical">
                <table class="responsive_table responsive_table-hover responsive_table-mc-light-blue"  id="itemList">
                    <thead>
                        <th width='1%' class="">&nbsp;</th>
                        <th width='1%'  class=" hidden-phone">&nbsp;</th>
                        <th class=""><B><?php echo JText::_('LNG_TYPE',true); ?></B></th>
                        <th class=""><B><?php echo JText::_('LNG_NAME',true); ?></B></th>
                        <th class=" hidden-phone"><B><?php echo JText::_('LNG_SUBJECT',true); ?></B></th>
                        <th class=" hidden-phone" ><B><?php echo JText::_('LNG_CONTENT',true); ?></B></th>
                    </thead>
                    <?php
                    $nrcrt = 1;
                    //if(0)
                    for($i = 0; $i <  count( $this->items ); $i++)
                    {
                        $email = $this->items[$i];
                        $emailContent = $this->hoteltranslationsModel->getObjectTranslation(EMAIL_TEMPLATE_TRANSLATION,$email->email_default_id,JRequest::getVar( '_lang'));

                        ?>
                        <TR
                            class="row<?php echo $i%2?>"
                            id="defaultEmails"
                            onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	=	"this.style.cursor='default'"
                            valign=top
                            >
                            <TD class=" hidden-phone" data-title="#"><?php echo $nrcrt++?></TD>
                            <TD class="">
                                <?php echo Jhtml::_('grid.id',$i , $email->email_default_id) ?>
                            </TD>
                            <TD class="has-context" data-title="<?php echo JText::_('LNG_TYPE',true)?>">
                                <a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=defaultemail&layout=edit&id='. $email->email_default_id)?>'
                                   title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"
                                    >
                                    <B><?php echo $email->email_default_type?></b>
                            </a>
                            </TD>
                            <TD align=left class="" data-title="<?php echo JText::_('LNG_NAME',true)?>">
                                <?php echo $email->email_default_name?>
                            </TD>
                            <TD class="small hidden-phone" data-title="<?php echo JText::_('LNG_SUBJECT',true)?>" ><?php echo $email->email_default_subject?></TD>
                            <TD  align=left class="small hidden-phone column" data-title="<?php echo JText::_('LNG_CONTENT',true)?>"><?php echo isset($emailContent)?$emailContent->content:"";?></TD>

                        </TR>
                    <?php
                    }
                    ?>
                </TABLE>
             </div>
    </div>
    <input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="email_default_id" value="" />
    <input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
    <script language="javascript" type="text/javascript">

        Joomla.submitbutton = function(pressbutton)
        {
            var form = document.adminForm;
            if (pressbutton == 'edit')
            {
                var isSel = false;
                if( form.elements['boxchecked'].length == null )
                {
                    if(form.elements['boxchecked'].checked)
                    {
                        isSel = true;
                    }
                }
                else
                {
                    for( i = 0; i < form.boxchecked.length; i ++ )
                    {
                        if(form.elements['boxchecked'][i].checked)
                        {
                            isSel = true;
                            break;
                        }
                    }
                }

                if( isSel == false )
                {
                    alert('<?php echo JText::_('LNG_YOU_MUST_SELECT_ONE_RECORD',true); ?>');
                    return false;
                }
                submitform( pressbutton );
                return;
            } else if (pressbutton == 'back') {
                form.view.value = 'applicationsettings';
                form.controller.value = 'applicationsettings';
                //form.submit();
                submitform( pressbutton );
            } else {
                submitform( pressbutton );
            }
        }
    </script>
</form>