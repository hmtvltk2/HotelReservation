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

JHtml::_('formbehavior.chosen', 'select');

?>
<form action="<?php echo JRoute::_('index.php?option='.getBookingExtName().'&layout=edit&id=' . (int) $this->item->email_default_id)?>" method="post" name="adminForm" id="adminForm">
	<fieldset>
		<legend><?php echo JText::_('LNG_EMAIL_DETAILS',true); ?></legend>
<center>

    <TABLE class="admintable" align=center border=0>
        <TR>
            <TD width=10% ><?php echo JText::_('LNG_TYPE',true); ?> :</TD>
            <TD  colspan=2 align=left>

                <select
                    id 		= "email_default_type"
                    name	= "email_default_type"
                    style	= "width:145px"
                    >
                    <option <?php echo $this->item->email_default_type=='Reservation Email'? "selected" : ""?> value='Reservation Email'><?php echo JText::_('LNG_RESERVATION_EMAIL',true); ?></option>
                    <option <?php echo $this->item->email_default_type=='Cancelation Email'? "selected" : ""?> value='Cancelation Email'><?php echo JText::_('LNG_CANCELATION_EMAIL',true); ?></option>
                    <option <?php echo $this->item->email_default_type=='Review Email'? "selected" : ""?> value='Review Email'><?php echo JText::_('LNG_REVIEW_EMAIL',true); ?></option>
                    <option <?php echo $this->item->email_default_type=='Invoice Email'? "selected" : ""?> value='Invoice Email'><?php echo JText::_('LNG_INVOICE_EMAIL',true); ?></option>
                    <option <?php echo $this->item->email_default_type=='Bookings List'? "selected" : ""?> value='Bookings List'><?php echo JText::_('LNG_BOOKINGS_LIST',true); ?></option>
                    <option <?php echo $this->item->email_default_type=='Guest List Email'? "selected" : ""?> value='Guest List Email'><?php echo JText::_('LNG_GUEST_LIST',true); ?></option>
                    <option <?php echo $this->item->email_default_type=='Client Invoice Email'? "selected" : ""?> value='Client Invoice Email'><?php echo JText::_('LNG_CLIENT_INVOICE_EMAIL',true); ?></option>
                </select>
            </TD>
        </TR>
        <TR>
            <TD width=10% ><?php echo JText::_('LNG_NAME',true); ?> :</TD>
            <TD  width=1% align=left>
                <input
                    type		= "text"
                    name		= "email_default_name"
                    id			= "email_default_name"
                    value		= '<?php echo $this->item->email_default_name?>'
                    size		= 50
                    maxlength	= 128

                    />
            </TD>
            <TD>&nbsp;</TD>
        </TR>
        <TR>
            <TD width=10% ><?php echo JText::_('LNG_SUBJECT',true); ?> :</TD>
            <TD  width=1% align=left>
                <input
                    type		= "text"
                    name		= "email_default_subject"
                    id			= "email_default_subject"
                    value		= '<?php echo $this->item->email_default_subject?>'
                    size		= 50
                    maxlength	= 128

                    />
            </TD>
            <TD>&nbsp;</TD>
        </TR>
        <TR>
            <TD width=10%  class="key"> <?php echo JText::_('LNG_CONTENT',true); ?> :</TD>
            <TD  colspan=2 ALIGN=LEFT>
                <?php echo JText::_('LNG_USE_ONE_OF_EMAILS_TAG_IN_THE_EDITOR_TO_INSERT_CONTENT_WHEN_EMAIL_IS_SENT',true)?>
                <select style='text-align:center'
                        onchange = 	"
										if( this.value != '')
										{
											var selectedEditor = jQuery('dd.tabs:visible textarea').attr('id');
											tinyMCE.get(selectedEditor).execCommand('mceReplaceContent',false,this.value);
										}
									"
                    >
                    <option></option>


                    <option value="<?php echo htmlentities(EMAIL_RESERVATIONFIRSTNAME)?>"><?php echo htmlspecialchars(EMAIL_RESERVATIONFIRSTNAME)?></option>
                    <option value='<?php echo htmlentities(EMAIL_RESERVATIONLASTNAME)?>'><?php echo htmlentities(EMAIL_RESERVATIONLASTNAME)?></option>
                    <option value='<?php echo htmlentities(EMAIL_RESERVATIONDETAILS)?>'><?php echo htmlentities(EMAIL_RESERVATIONDETAILS)?></option>
                    <option value='<?php echo htmlentities(EMAIL_BILINGINFORMATIONS)?>'><?php echo htmlentities(EMAIL_BILINGINFORMATIONS)?></option>
                    <option value='<?php echo htmlentities(EMAIL_COMPANY_NAME)?>'><?php echo htmlentities(EMAIL_COMPANY_NAME)?></option>
                    <option value='<?php echo htmlentities(EMAIL_COMPANY_LOGO)?>'><?php echo htmlentities(EMAIL_COMPANY_LOGO)?></option>
                    <option value='<?php echo htmlentities(EMAIL_HOTEL_IMAGE)?>'><?php echo htmlentities(EMAIL_HOTEL_IMAGE)?></option>
                    <option value='<?php echo htmlentities(EMAIL_RATING_URL)?>'><?php echo htmlentities(EMAIL_RATING_URL)?></option>
                    <option value='<?php echo htmlentities(EMAIL_SOCIAL_SHARING)?>'><?php echo htmlentities(EMAIL_SOCIAL_SHARING)?></option>
                    <option value='<?php echo htmlentities(EMAIL_INVOICE_HOTEL_DETAILS)?>'><?php echo htmlentities(EMAIL_INVOICE_HOTEL_DETAILS)?></option>
                    <option value='<?php echo htmlentities(EMAIL_INVOICE_DATE)?>'><?php echo htmlentities(EMAIL_INVOICE_DATE)?></option>
                    <option value='<?php echo htmlentities(EMAIL_INVOICE_NUMBER)?>'><?php echo htmlentities(EMAIL_INVOICE_NUMBER)?></option>
                    <option value='<?php echo htmlentities(EMAIL_INVOICE_FIELDS)?>'><?php echo htmlentities(EMAIL_INVOICE_FIELDS)?></option>

                    <option value='<?php echo htmlentities(EMAIL_START_DATE)?>'><?php echo htmlentities(EMAIL_START_DATE)?></option>
                    <option value='<?php echo htmlentities(EMAIL_END_DATE)?>'><?php echo htmlentities(EMAIL_END_DATE)?></option>
                    <option value='<?php echo htmlentities(EMAIL_CHECKIN_TIME)?>'><?php echo htmlentities(EMAIL_CHECKIN_TIME)?></option>
                    <option value='<?php echo htmlentities(EMAIL_CHECKOUT_TIME)?>'><?php echo htmlentities(EMAIL_CHECKOUT_TIME)?></option>
	                <option value='<?php echo htmlentities(EMAIL_PARKING_TAX)?>'><?php echo htmlentities(EMAIL_PARKING_TAX)?></option>
                </select>
                &nbsp; <?php echo JText::_('LNG_EMAILS_TAG_EDITOR',true)?>
                <BR>
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
                $dirs = JHotelUtil::languageTabs();
                //dmp($dirs);
                $j=0;
                foreach( $dirs  as $_lng ){

                    $langName= JHotelUtil::languageNameTabs($_lng);

                    echo JHtml::_('tabs.panel',  $langName, 'tab'.$j);
                    $langContent = isset($this->translations[$_lng])?$this->translations[$_lng]:"";
                    $editor =JFactory::getEditor();
                    echo $editor->display('email_content_'.$_lng, $langContent, '800', '400', '70', '15', false);

                }
                echo JHtml::_('tabs.end');
                ?>
            </TD>
        </TR>
    </TABLE>
    </fieldset>
    <script language="javascript" type="text/javascript">
        Joomla.submitbutton = function(pressbutton)
        {
            var form = document.adminForm;
            if (pressbutton == 'save')
            {
                if( !validateField( form.elements['email_default_name'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_EMAIL_NAME',true); ?>" ) )
                    return false;
                submitform( pressbutton );
                return;
            } else {
                submitform( pressbutton );
            }
        }
    </script>

    <input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
    <input type="hidden" name="task" value="defaultemail.edit" />
    <input type="hidden" name="email_default_id" value="<?php echo $this->item->email_default_id ?>" />
    <input type="hidden" name="controller" value="defaultemail" />
    <?php echo JHTML::_( 'form.token' ); ?>
    </form>