<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="page-characteristics">
	<br  style="font-size:1px;" />
	<fieldset>
	<legend><?php echo JText::_( 'HOTEL_CHARACTERISTICS' ,true); ?></legend>
		<table class="admintable" cellspacing="1" >
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_FACILITIES',true); ?> :</TD>
				<TD nowrap ALIGN=LEFT>
					<div id="facility-holder" class="option-holder">
						<?php
							echo $this->facilities->displayFacilities( $this->item->facilities, $this->item->selectedFacilities );
						?>
					</div>
				<?php 
					if (checkUserAccess(JFactory::getUser()->id,"manage_options")){
				?>
					<div class="manage-option-holder">
						<a href="javascript:" onclick="showManageFacilities()"><?php echo isset($this->item->hotel_id) ?  JText::_('LNG_MANAGE_FACILITIES',true):""; ?></a>
					</div>
				<?php 
					}
				?>	
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php  echo JText::_('LNG_TYPE',true)?> :</TD>
				<TD nowrap ALIGN=LEFT>
					<div id="types-holder" class="option-holder">
						<?php
							echo $this->lodgingtypes->displayTypes( $this->item->types, $this->item->selectedTypes );
						?>
					</div>
					
				<?php 
					if (checkUserAccess(JFactory::getUser()->id,"manage_options")){
				?>					
					<div class="manage-option-holder">
						<a href="javascript:" onclick="showManageTypes()"><?php echo isset($this->item->hotel_id) ? JText::_('LNG_MANAGE_Types',true):"";?></a>
					</div>
				<?php 
					}
				?>	
					
				</TD>

                <td id="note">	
                    <div>
                        <div class="red">
                            <?php echo JText::_('LNG_USAGE_NOTE',true);?>
                        </div>
                    	<p>
                            <?php echo JText::_('LNG_TRANSLATION_INFO',true);?>
                        </p>
                    </div>
                </td>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_ACCOMMODATION_TYPE',true); ?> :</TD>
				<TD nowrap ALIGN=LEFT>
					<div id="accommodationtypes-holder" class="option-holder">
						<?php
							echo $this->accomodationtypes->displayAccommodationTypes( $this->item->accommodationTypes, $this->item->selectedAccommodationTypes );
						?>
					</div>
				<?php 
					if (checkUserAccess(JFactory::getUser()->id,"manage_options")){
				?>					
					<div class="manage-option-holder">
						<a href="javascript:" onclick="showManageAccommodationTypes()"><?php  echo isset($this->item->hotel_id) ?  JText::_('LNG_MANAGE_ACCOMMODATION_TYPES',true):""; ?></a>
					</div>
				<?php 
					}
				?>						
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_ENVIRONMENT',true); ?> :</TD>
				<TD nowrap ALIGN=LEFT>
					<div id="environments-holder" class="option-holder">
						<?php
							echo $this->environmenttypes->displayEnvironments( $this->item->environments, $this->item->selectedEnvironments );
						?>
					</div>
				<?php 
					if (checkUserAccess(JFactory::getUser()->id,"manage_options")){
				?>					
					<div class="manage-option-holder">
						<a href="javascript:" onclick="showManageEnvironments()"><?php echo isset($this->item->hotel_id) ?  JText::_('LNG_MANAGE_ENVIRONMENTS',true):""; ?></a>
					</div>
				<?php 
					}
				?>						
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_REGION',true); ?> :</TD>
				<TD nowrap ALIGN=LEFT>
					<div id="regions-holder" class="option-holder">
						<?php
							echo $this->regiontypes->displayRegions( $this->item->regions, $this->item->selectedRegions );
						?>
					</div>
				<?php 
					if (checkUserAccess(JFactory::getUser()->id,"manage_options")){
				?>					
					<div class="manage-option-holder">
						<a href="javascript:" onclick="showManageRegions()"><?php echo isset($this->item->hotel_id) ?  JText::_('LNG_MANAGE_REGIONS',true):""; ?></a>
					</div>
				<?php 
					}
				?>						
				</TD>
			</TR>
		</table>
	</fieldset>
</div>

	<div id="showFacilitiesNewFrm" style="display:none;">
  		<div id="popup_container">
    <!--Content area starts-->

    		<div class="head">
      		    <div class="head_inner">
               <h2> <?php echo JText::_('LNG_MANAGE_FACILITIES',true); ?></h2>
               <a href="#" class="cancel_btn" onclick="closePopup();"><span class="cancel_icon">&nbsp;</span><?php echo JText::_('LNG_CANCEL',true); ?></a></div>
            </div>
            <div class="content">
                    <div class="descriptions" >

                       <div id="content_section_tab_data1">
                       	<span id="frm_error_msg_facility" class="text_error" style="display: none;"></span> 
						<div class="row" id="facility-container">
						</div>
						 
					 	<div class="option-row">
							<a href="javascript:" onclick="addNewFacility(0,'')"><?php echo JText::_('LNG_ADD_NEW_FACILITY',true); ?></a>
						</div>
						<div class="proceed_row">
                           <!--button sec starts-->
                              <div class="buttom_sec" id="frmFacilitiesFormSubmitWait" style="display: none;"> <span class="error_msg" style="background-image: none; color: rgb(0, 0, 0) ! important;"><?php echo JText::_('LNG_LOADING',true); ?></span> </div>
                              <button name="btnSave" id="btnSave" onclick="saveFacilities(this.form);" type="submit" class="submit">    
                                     <span><span><?php echo JText::_('LNG_SAVE',true)?></span></span>
                              </button>
                              <input value="<?php echo JText::_('LNG_CANCEL',true); ?>" class="cancel" name="btnCancel" id="btnCancel" onclick="closePopup();" type="button">
                          </div>
                          <!--button sec ends-->
                        </div>
            </div>
          </div>
          </div>
     </div>        
	
	<div id="showTypesNewFrm" style="display:none;">
  		<div id="popup_container">
    <!--Content area starts-->

    		<div class="head">
      		    <div class="head_inner">
               <h2> <?php echo JText::_('LNG_MANAGE_TYPES',true); ?></h2>
               <a href="#" class="cancel_btn" onclick="closePopup();"><span class="cancel_icon">&nbsp;</span><?php echo JText::_('LNG_CANCEL',true); ?></a></div>
            </div>
            <div class="content">
                    <div class="descriptions" >

                       <div id="content_section_tab_data1">
                       	<span id="frm_error_msg_types" class="text_error" style="display: none;"></span>
						<div class="row" id="types-container">
						</div>
						 
					 	<div class="option-row">
							<a href="javascript:" onclick="addNewType(0,'')"><?php echo JText::_('LNG_ADD_NEW_TYPE',true); ?></a>
						</div>
						<div class="proceed_row">
                           <!--button sec starts-->
                              <button name="btnSave" id="btnSave" onclick="saveTypes(this.form);" type="submit" class="submit">    
                                     <span><span><?php echo  JText::_('LNG_SAVE',true)?></span></span>
                              </button>
                              <input value="<?php echo JText::_('LNG_CANCEL',true); ?>" class="cancel" name="btnCancel" id="btnCancel" onclick="closePopup();" type="button">
                          </div>
                          <!--button sec ends-->
                        </div>
                        <div class="buttom_sec" id="frmTypesFormSubmitWait" style="display: none;"> <span class="error_msg" style="background-image: none; color: rgb(0, 0, 0) ! important;"><?php echo JText::_('LNG_LOADING',true); ?></span> </div>
            </div>
          </div>
          </div>
     </div>
	
	<div id="showAccommodationTypesNewFrm" style="display:none;">
  		<div id="popup_container">
    <!--Content area starts-->

    		<div class="head">
      		    <div class="head_inner">
               <h2> <?php echo JText::_('LNG_MANAGE_ACCOMMODATION_TYPES',true); ?></h2>
               <a href="#" class="cancel_btn" onclick="closePopup();"><span class="cancel_icon">&nbsp;</span><?php echo JText::_('LNG_CANCEL',true); ?></a></div>
            </div>
            <div class="content">
                    <div class="descriptions" >

                       <div id="content_section_tab_data1">
                       	<span id="frm_error_msg_accommodationtypes" class="text_error" style="display: none;"></span>
						<div class="row" id="accommodationtypes-container">
						</div>
						 
					 	<div class="option-row">
							<a href="javascript:" onclick="addNewAccommodationType(0,'')"><?php echo JText::_('LNG_ADD_NEW_ACCOMMODATION_TYPE',true); ?></a>
						</div>
						<div class="proceed_row">
                           <!--button sec starts-->
                              <button name="btnSave" id="btnSave" onclick="saveAccommodationTypes(this.form);" type="submit" class="submit">    
                                     <span><span><?php echo JText::_('LNG_SAVE',true)?></span></span>
                              </button>
                              <input value="<?php echo JText::_('LNG_CANCEL',true); ?>" class="cancel" name="btnCancel" id="btnCancel" onclick="closePopup();" type="button">
                          </div>
                          <!--button sec ends-->
                        </div>
                        <div class="buttom_sec" id="frmAccommodationTypesFormSubmitWait" style="display: none;"> <span class="error_msg" style="background-image: none; color: rgb(0, 0, 0) ! important;"><?php echo JText::_('LNG_LOADING',true); ?></span> </div>
            </div>
          </div>
          </div>
     </div>
     
     <div id="showEnvironmentsNewFrm" style="display:none;">
  		<div id="popup_container">
    <!--Content area starts-->

    		<div class="head">
      		    <div class="head_inner">
               <h2> <?php echo JText::_('LNG_MANAGE_ENVIRONMENT',true); ?></h2>
               <a href="#" class="cancel_btn" onclick="closePopup();"><span class="cancel_icon">&nbsp;</span><?php echo JText::_('LNG_CANCEL',true); ?></a></div>
            </div>
            <div class="content">
                    <div class="descriptions" >

                       <div id="content_section_tab_data1">
                       		<span id="frm_error_msg_environments" class="text_error" style="display: none;"></span>
						<div class="row" id="environments-container">
						</div>
						 
					 	<div class="option-row">
							<a href="javascript:" onclick="addNewEnvironment(0,'')"><?php echo JText::_('LNG_ADD_NEW_ENVIRONMENT',true); ?></a>
						</div>
						<div class="proceed_row">
                           <!--button sec starts-->
                              <button name="btnSave" id="btnSave" onclick="saveEnvironments(this.form);" type="submit" class="submit">    
                                     <span><span><?php echo JText::_('LNG_SAVE',true); ?></span></span>
                              </button>
                              <input value="<?php echo JText::_('LNG_CANCEL',true); ?>" class="cancel" name="btnCancel" id="btnCancel" onclick="closePopup();" type="button">
                          </div>
                          <!--button sec ends-->
                        </div>
                        <div class="buttom_sec" id="frmEnvironmentsFormSubmitWait" style="display: none;"> <span class="error_msg" style="background-image: none; color: rgb(0, 0, 0) ! important;"><?php echo JText::_('LNG_LOADING',true); ?></span> </div>
            </div>
          </div>
          </div>
     </div>
     
     <div id="showRegionsNewFrm" style="display:none;">
  		<div id="popup_container">
    <!--Content area starts-->

    		<div class="head">
      		    <div class="head_inner">
               <h2> <?php echo JText::_('LNG_MANAGE_REGIONS',true); ?></h2>
               <a href="#" class="cancel_btn" onclick="closePopup(); "><span class="cancel_icon">&nbsp;</span><?php echo JText::_('LNG_CANCEL',true); ?></a></div>
            </div>
            <div class="content">
                    <div class="descriptions" >

                       <div id="content_section_tab_data1">
                       		<span id="frm_error_msg_regions" class="text_error" style="display: none;"></span>
						<div class="row" id="regions-container">
						</div>
						 
					 	<div class="option-row">
							<a href="javascript:" onclick="addNewRegion(0,'')"><?php echo JText::_('LNG_ADD_NEW_REGION',true); ?></a>
						</div>
						<div class="proceed_row">
                           <!--button sec starts-->
                              <button name="btnSave" id="btnSave" onclick="saveRegions(this.form);" type="submit" class="submit">    
                                     <span><span><?php echo JText::_('LNG_SAVE',true)?></span></span>
                              </button>
                              <input value="<?php echo JText::_('LNG_CANCEL',true); ?>" class="cancel" name="btnCancel" id="btnCancel" onclick="closePopup();" type="button">
                          </div>
                          <!--button sec ends-->
                        </div>
                        <div class="buttom_sec" id="frmRegionsFormSubmitWait" style="display: none;"> <span class="error_msg" style="background-image: none; color: rgb(0, 0, 0) ! important;"><?php echo JText::_('LNG_LOADING',true); ?></span> </div>
            </div>
          </div>
          </div>
     </div>