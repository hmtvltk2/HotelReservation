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

?>

<div>
    <fieldset>
        <legend><?php echo JText::_('LNG_GUEST_DETAILS'); ?></legend>
        <div class='admintable' id="adminform">
            <?php
            foreach($this->item->guestdetails as $guestdetail){
                ?>
                <div class="section group guestAttributesRow">
                    <div class="col column_2_of_12">
                        <label class="guestLabel">
                            <?php echo JText::_("LNG_".strtoupper(str_replace(" ", "_",$guestdetail->name))) ?> :
                        </label>
                    </div>
                    <div class="col column_10_of_12">
                        <select id="guest-detail-attribute-<?php echo $guestdetail->id ?>" name="guest-detail-attribute-<?php echo $guestdetail->id ?>" class="inputbox input-medium">
                            <?php
                            //set only mandatory configuration attribute for first name, last name & email only
                            $attrConfiguraiton = $this->attributeConfiguration;
                            if($guestdetail->id==3 || $guestdetail->id==4 || $guestdetail->id == 12 ){
                                unset($attrConfiguraiton[0]);
                                unset($attrConfiguraiton[1]);
                            }
                            if($guestdetail->id == 6){
                                //unset($attrConfiguraiton[2]);
                            }
                            ?>
                            <?php echo JHtml::_('select.options', $attrConfiguraiton, 'value', 'text', $guestdetail->config_type);?>
                        </select>
                    </div>
                </div>
            <?php }?>
        </div>
    </fieldset>
</div>
