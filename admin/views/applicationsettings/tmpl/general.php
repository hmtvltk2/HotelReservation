<?php 
/*------------------------------------------------------------------------
# JHotelReservation
# author CMSJunkie
# copyright Copyright (C) 2013 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/hotel_reservation/?p=1
# Technical Support:  Forum Multiple - http://www.cmsjunkie.com/forum/joomla-multiple-hotel-reservation/?p=1
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
$editor =JFactory::getEditor();
?>

 	 <fieldset>
			<legend><?php echo JText::_('LNG_COMPANY',true); ?></legend>
			
			<table class="admintable"  width=100%>
				<TR>
					<td width="10%" align="left" class="key"  >
							<?php echo JText::_('LNG_NAME',true); ?>:
					</td>
					<td align="left" >
						<input type='text' size=50 maxlength=255  id='company_name' name = 'company_name' value="<?php echo $this->item->company_name?>">
					</TD>
					<td align="right" rowspan="2">
						<div class="picture-preview" id="picture-preview">
							<?php
							if(isset($this->item->logo_path)){
								 ?><img src="<?php echo JURI::root().PATH_PICTURES.$this->item->logo_path;?>"/>
							<?php }
							?>
						</div>
					</td>
				</TR>
				<TR>
					<td width="10%" align="left" class="key"  >
							<?php echo JText::_('LNG_EMAIL',true); ?>:
					</td>
					<td align="left" >
						<input type='text' size=50 maxlength=255  id='company_email' name = 'company_email' value='<?php echo $this->item->company_email?>'>
						<input type="hidden" name="logo_path" id="imageLocation" value="<?php echo $this->item->logo_path?>">
					</TD> 
				</TR>
				
				<TR>
					<TD align=left class="key">
						<?php echo JText::_('LNG_COMPANY_LOGO',true); ?>:
					</TD>
					<TD>

                        <div class="dropzone dropzone-previews" id="file-upload">
                            <div id="actions" class="row">

                                <div class="col-lg-7">
                                    <!-- The fileinput-button span is used to style the file input field as button -->
                                 <span class="btn btn-success fileinput-button dz-clickable">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span><?php echo JText::_('LNG_ADD_FILES',true); ?></span>
                                 </span>
                                    <button  class="btn btn-primary start" id="submitAll">
                                        <i class="glyphicon glyphicon-upload"></i>
                                        <span><?php echo JText::_('LNG_UPLOAD_ALL',true);?></span>
                                    </button>
                                </div>

                            </div>
                        </div>
                            <?php
                            $baseUrl = JURI::base();
                            if(strpos ($baseUrl,'administrator') ===  false)
                                $baseUrl = $baseUrl.'administrator/';
                            ?>
                            <script>
                                jQuery(document).ready(function () {
                                    imageUploader("#file-upload",'<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/helpers/upload.php?t=<?php echo strtotime('now')?>&resizeImage=0&_root_app=<?php echo urlencode(JPATH_ROOT)?>&_target=<?php echo urlencode(PATH_PICTURES.LOGO_PICTURE_PATH)?>',".fileinput-button","<?php echo JText::_('LNG_DRAG_N_DROP',true); ?>","no image path needed for this view",1,"setUpLogo");
                                });
                            </script>
					</TD>
				</TR>
				
			</table>
		</fieldset>
        <fieldset>
			<legend><?php echo JText::_('LNG_APPLICATION_SETTINGS',true); ?></legend>

			<table class="admintable"  width=100% >
				<tr>
					<td width="10%" align="left" class="key" >
							<?php echo JText::_('LNG_ENABLE_RESERVATION',true); ?>:
					</td>
                    <td id="is_enable_reservation" class="radio btn-group btn-group-yesno">
                        <input
                            type		= "radio"
                            name		= "is_enable_reservation"
                            id			= "is_enable_reservation0"
                            value		= '1'
                            accesskey	= "Y"
                            <?php echo $this->item->is_enable_reservation==true?" checked ":""?>
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"
                            />
                       <label
                           class="labelYes_1"
                              id="label_is_enable_reservation0"
                              for="is_enable_reservation0"><?php echo JText::_( 'LNG_YES' ,true); ?> </label>
                        &nbsp;
                        <input
                            type		= "radio"
                            name		= "is_enable_reservation"
                            id			= "is_enable_reservation1"
                            value		= '0'
                            accesskey	= "N"
                            <?php echo $this->item->is_enable_reservation==false? " checked ":"" ?>
                            onmouseover	= "this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	= "this.style.cursor='default'"
                            />
                        <label
                            class="labelNo"
                               id="label_is_enable_reservation1"
                               for="is_enable_reservation1"><?php echo JText::_( 'LNG_NO' ,true); ?></label>
                    </td>
					<td align="left">
						<?php echo JText::_('LNG_ENABLE_DISABLE_RESERVATION',true); ?>
					</TD>
				</TR>
				
				<tr>
					<td width="10%" align="left" class="key" >
						<label class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CAPACITY_CALCULATION');?></strong><br/><?php echo JText::_('LNG_CAPACITY_CALCULATION_DETAILS');?>" title="">
							<?php echo JText::_('LNG_CAPACITY_CALCULATION')?> 
						</label>
					</td>
                    <td id="capacity_calculation" class="radio btn-group btn-group-yesno">
                        <input
                            type		= "radio"
                            name		= "capacity_calculation"
                            id			= "capacity_calculation0"
                            value		= '1'
                            accesskey	= "Y"
                            <?php echo $this->item->capacity_calculation==true?" checked ":""?>
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"
                            />
                       <label
                           class="labelYes_1"
                              id="label_capacity_calculation0"
                              for="capacity_calculation0"><?php echo JText::_( 'LNG_COMBINED' ,true); ?> </label>
                        &nbsp;
                        <input
                            type		= "radio"
                            name		= "capacity_calculation"
                            id			= "capacity_calculation1"
                            value		= '0'
                            accesskey	= "N"
                            <?php echo $this->item->capacity_calculation==false? " checked ":"" ?>
                            onmouseover	= "this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	= "this.style.cursor='default'"
                            />
                        <label
                            class="labelNo"
                               id="label_capacity_calculation1"
                               for="capacity_calculation1"><?php echo JText::_( 'LNG_SEPARATE' ,true); ?></label>
                    </td>
					<td align="left">
						&nbsp;
					</td>
				</TR>
				
				<tr>
					<td width="10%" align="left" class="key" ><?php echo JText::_('LNG_DATE_FORMAT',true)?> :</Td>
					<td>
						<select
							id		= 'date_format_id'
							name	= 'date_format_id'

						>
							<?php
							foreach ($this->item->dateFormats as $dateFormat)
							{
							?>
							<option value = '<?php echo $dateFormat->id?>' <?php echo $dateFormat->id==$this->item->date_format_id? "selected" : ""?>> <?php echo $dateFormat->name?></option>
							<?php
							}
							?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td width="10%" align="left" class="key"  >
							<label id="google_map_key-lbl" for="google_map_key" class="hasTooltip" title=""><?php echo JText::_("LNG_GOOGLE_MAP_KEY"); ?></label>
					</td>
					<td align="left" >
						<input type="text" id="google_map_key" name="google_map_key" value="<?php echo $this->item->google_map_key ?>">
					</td> 
				</tr>
				<TR>
					<td width="10%" align="left"  class="key">
							<?php echo JText::_( 'LNG_NOTIFY_EMAIL_CANCEL_PENDING' ,true); ?>
					</td>
					<td align="left"  id="is_email_notify_canceled_pending" class="radio btn-group btn-group-yesno">
						<input 
							type		= "radio"
							name		= "is_email_notify_canceled_pending"
							id			= "is_email_notify_canceled_pending0"
							value		= '1'
							<?php echo $this->item->is_email_notify_canceled_pending==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<label
                            class="labelYes"
                            id="label_is_email_notify_canceled_pending0"
                               for="is_email_notify_canceled_pending0"><?php echo JText::_( 'LNG_YES' ,true); ?></label>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_email_notify_canceled_pending"
							id			= "is_email_notify_canceled_pending1"
							value		= '0'
							<?php echo $this->item->is_email_notify_canceled_pending==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<label
                            class="labelNo"
                            id="label_is_email_notify_canceled_pending1"
                               for="is_email_notify_canceled_pending1"><?php echo JText::_( 'LNG_NO' ,true); ?></label>
					</td>
					<td align="left">
						<?php echo JText::_( 'LNG_INFO_NOTIFY_EMAIL_CANCEL_PENDING' ,true); ?>
					</TD>
				</TR>
				
				<TR>
					<td width="10%" align="left" class="key">
							<?php echo JText::_( 'LNG_HIDE_USER_EMAIL' ,true); ?>
					</td>
					<td align="left" id="hide_user_email"  class="radio btn-group btn-group-yesno">
						<input 
							type		= "radio"
							name		= "hide_user_email"
							id			= "hide_user_email0"
							value		= '1'
							<?php echo $this->item->hide_user_email==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<label
                            id="label_hide_user_email0"
                            class="labelYes"
                            for="hide_user_email0"><?php echo JText::_( 'LNG_YES' ,true); ?></label>
						&nbsp;
						<input 
							type		= "radio"
							name		= "hide_user_email"
							id			= "hide_user_email1"
							value		= '0'
							<?php echo $this->item->hide_user_email==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<label
                            class="labelNo"
                            id="label_hide_user_email1"
                            for="hide_user_email1"><?php echo JText::_( 'LNG_NO' ,true); ?></label>
					</td>
				</TR>
                <tr>
                    <td class="key">
                        <?php echo JText::_('LNG_SEND_CANCELLATION_EMAIL_ADMIN_ONLY',true);?>
                    </td>
                    <td align="left" id="send_cancellation_email_admin_only"  class="radio btn-group btn-group-yesno">
                        <input
                            type		= "radio"
                            name		= "send_cancellation_email_admin_only"
                            id			= "send_cancellation_email_admin_only0"
                            value		= '1'
                            <?php echo $this->item->send_cancellation_email_admin_only==true? " checked " :""?>
                            accesskey	= "Y"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"


                            />
                        <label
                            id="label_send_cancellation_email_admin_only0"
                            class="labelYes"
                            for="send_cancellation_email_admin_only0"><?php echo JText::_( 'LNG_YES' ,true); ?></label>
                        &nbsp;
                        <input
                            type		= "radio"
                            name		= "send_cancellation_email_admin_only"
                            id			= "send_cancellation_email_admin_only1"
                            value		= '0'
                            <?php echo $this->item->send_cancellation_email_admin_only==false? " checked " :""?>
                            accesskey	= "N"
                            onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
                            onmouseout	="this.style.cursor='default'"

                            />
                        <label
                            class="labelNo"
                            id="label_send_cancellation_email_admin_only1"
                            for="send_cancellation_email_admin_only1"><?php echo JText::_( 'LNG_NO' ,true); ?></label>
                    </td>
                </tr>
				<tr>
					<td class="key">
						<?php echo JText::_('LNG_DELIMITER',true);?>
					</td>
					<td align="left" id="delimiter"  class="radio btn-group btn-group-yesno">
						<select
							id		= 'delimiter'
							name	= 'delimiter'>
							<?php
							foreach ($this->delimiters as $key=>$delimiter)
							{
								?>
								<option value = '<?php echo $delimiter?>' <?php echo $delimiter==$this->item->delimiter? "selected" : ""?>> <?php echo $delimiter.' ('.$key.')'?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
                <TR>
                    <td width="10%" align="left" class="key">
                        <?php echo JText::_( 'LNG_TERMS_AND_CONDITIONS' ,true); ?>
                    </td>

                    <td colspan="2" >
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
                        $appSettings = JHotelUtil::getApplicationSettings();
                        $dirs = JHotelUtil::languageTabs();
                        $j=0;
                        foreach( $dirs  as $_lng ){
                            $langName= JHotelUtil::languageNameTabs($_lng);

                            echo JHtml::_('tabs.panel',  $langName , 'tab' . $j);
                            $langContent = isset($this->translations[$_lng])?$this->translations[$_lng]:"";
                            $editor =JFactory::getEditor();
                            echo $editor->display('terms_and_conditions_'.$_lng, $langContent, '600', '300', '70', '15', false);

                        }
                        echo JHtml::_('tabs.end');
                        ?>
                    </td>
                </TR>
			</table>
		</fieldset>
<script type="text/javascript">
	function setUpLogo(path){
		jQuery("#imageLocation").val("<?php echo LOGO_PICTURE_PATH ?>" + path);
		var img_new = document.createElement('img');
		img_new.setAttribute('src', "<?php echo JURI::root().PATH_PICTURES.LOGO_PICTURE_PATH ?>" + path );
		img_new.setAttribute('class', 'company-logo');
		jQuery("#picture-preview").children().remove();
		jQuery("#picture-preview").append(img_new);
	}
</script>
	
		
	
