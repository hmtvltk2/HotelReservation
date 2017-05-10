<?php
/**
 * @package JHotelReservation
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

//no direct accees
defined ('_JEXEC') or die ('Resticted aceess');
?>

<div id="jhotelreservation-wrap" class="clearfix">
	<div class="dir-box-icon-wrapper">
		<div class="dir-box-icon">
			<a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=applicationsettings'); ?>">
				<div class="fa-icon">
					<i class="fa fa-gear"></i>
				</div>
				<span><?php echo JText::_('LNG_APPLICATION_SETTINGS'); ?></span>
			</a>
		</div>
	</div>
    <div class="dir-box-icon-wrapper">
        <div class="dir-box-icon">
            <a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotels'); ?>">
                <div class="fa-icon">
                    <i class="fa fa-building"></i>
                </div>
                <span><?php echo JText::_('LNG_MANAGE_HOTELS'); ?></span>
            </a>
        </div>
    </div>
    
    <?php if (checkUserAccess(JFactory::getUser()->id,"manage_offers") && PROFESSIONAL_VERSION==1) {?>
    
	<div class="dir-box-icon-wrapper">
		<div class="dir-box-icon">
			<a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=offers'); ?>">
				<div class="fa-icon">
					<i class="fa fa-gift"></i>
				</div>
				<span><?php echo JText::_('LNG_MANAGE_OFFERS'); ?></span>
			</a>
		</div>
	</div>
	
	<?php } ?>
	
	<div class="dir-box-icon-wrapper">
		<div class="dir-box-icon">
			<a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=rooms'); ?>">
				<div class="fa-icon">
					<i class="fa fa-bed"></i>
				</div>
				<span><?php echo JText::_('LNG_MANAGE_ROOMS'); ?></span>
			</a>
		</div>
	</div>
	
	<?php if (checkUserAccess(JFactory::getUser()->id,"manage_extra_options") && PROFESSIONAL_VERSION==1) {?>
	
	<div class="dir-box-icon-wrapper">
		<div class="dir-box-icon">
			<a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=extraoptions'); ?>">
				<div class="fa-icon">
					<i class="fa fa-puzzle-piece"></i>
				</div>
				<span><?php echo JText::_('LNG_MANAGE_EXTRAS'); ?></span>
			</a>
		</div>
	</div>
	<?php } ?>
	
	<div class="dir-box-icon-wrapper">
		<div class="dir-box-icon">
			<a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reservations'); ?>">
				<div class="fa-icon">
					<i class="fa fa-calendar"></i>
				</div>
				<span><?php echo JText::_('LNG_MANAGE_RESERVATIONS'); ?></span>
			</a>
		</div>
	</div>
	
	<?php if (checkUserAccess(JFactory::getUser()->id,"hotel_ratings") && PROFESSIONAL_VERSION==1) {?>
	
	
	<div class="dir-box-icon-wrapper">
		<div class="dir-box-icon">
			<a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotelratings'); ?>">
				<div class="fa-icon">
					<i class="fa fa-comments"></i>
				</div>
				<span><?php echo JText::_('LNG_MANAGE_REVIEWS'); ?></span>
			</a>
		</div>
	</div>
	<?php } ?>
	
	<?php if (checkUserAccess(JFactory::getUser()->id,"reservations_reports") && PROFESSIONAL_VERSION==1) {?>
	
    <div class="dir-box-icon-wrapper">
        <div class="dir-box-icon">
            <a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reports'); ?>">
                <div class="fa-icon">
                    <i class="fa fa-bar-chart"></i>
                </div>
                <span><?php echo JText::_('LNG_MANAGE_REPORTS'); ?></span>
            </a>
        </div>
    </div>
    <?php } ?>
</div>

