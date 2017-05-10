<?php
/*------------------------------------------------------------------------
# JHotelReservation
# author CMSJunkie
# copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/hotel_reservation/?p=1
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?>
    <style>
        #content-wrapper{
            margin: 20px;
            padding: 0px;
        }
    </style>
    <div id="jhp-dashbord">
        <div class="row-fluid">
            <div class="span3">
                <div class="ibox">
                    <div class="ibox-title">
                        <span class="dir-label dir-label-info  pull-right"><?php echo JText::_("LNG_TOTAL");?></span>
                        <h5>Reservations</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><?php echo  $this->statistics->totalMonthlyReservation;?></h1>
                        <div class="stat-percent font-bold text-success">
                            <?php echo $this->statistics->totalReservations;?>
                        </div>
                        <small><?php echo JText::_("LNG_THIS_MONTH");?></small>
                    </div>
                </div>
            </div>
            <div class="span3">
                <div class="ibox">
                    <div class="ibox-title">
                        <span class="dir-label dir-label-success pull-right"><?php echo JText::_("LNG_TOTAL");?></span>
                        <h5>Offers</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><?php echo $this->statistics->monthlyBookedOffers;?></h1>
                        <div class="stat-percent font-bold text-success">
                            <?php echo $this->statistics->bookedOffers;?>
                        </div>
                        <small><?php echo JText::_("LNG_OFFERS_BOOKED_THIS_MONTH");?></small>
                    </div>
                </div>
            </div>
            <?php if (checkUserAccess(JFactory::getUser()->id,"income_reporting")){
            ?>
            <div class="span3">
                <div class="ibox">
                    <div class="ibox-title">
                        <span class="dir-label dir-label-primary  pull-right"><?php echo JText::_("LNG_TOTAL");?></span>
                        <h5>Income</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><?php echo $this->statistics->monthlyIncome.' '.$this->currency->currency_symbol;?></h1>
                        <div class="stat-percent font-bold text-success">
                            <?php echo $this->statistics->totalIncome.' '.$this->currency->currency_symbol; ?>
                        </div>
                        <small><?php echo JText::_("LNG_THIS_MONTH");?></small>
                    </div>
                </div>
            </div>
            <?php }?>

			<div class="span3">
				<div class="ibox">
					<div class="ibox-title">
					<div class="stat-percent font-bold text-success pull-right" id="extensionStatus"></div>
						<h5><?php echo JText::_("LNG_VERSION_STATUS");?></h5>
					</div>
					<div class="ibox-content">
						<div id="updatesStatus"><img src='<?php echo JURI::base()."/components/com_jhotelreservation/assets/img/loader.gif"; ?>'></div>
						<div style="clear:both">&nbsp;</div>
						<p class="span6"><?php echo JText::_("LNG_EXTENSION_VERSION");?> <span class="dir-label dir-label-warning" id="currentVersion"></span></p>
						<p class="versionNoMargin"><?php echo "&nbsp;&nbsp;&nbsp;".JText::_("LNG_UPDATE_VERSION");?> <span class="dir-label dir-label-primary" id="updateVersion"></span></p>
                    </div>
				</div>
			</div>
        </div>

        <div class="row-fluid">
            <div class="ibox">
                <div class="ibox-content chart">
                    <div class="row-fluid">
                        <div id="chartPanel">
                            <div id="chartdiv">
                                <div id="loader"><img src='<?php echo JURI::base()."/components/".getBookingExtName()."/assets/img/loader.gif";?>'>
                                </div>
                            </div>
                            <div id="buttonDiv">
                                <button class="chartButton" id="daysLag" value="7" onClick="generateChart(this.value);">7 <?php echo JText::_("LNG_DAYS");?></button>
                                <button class="chartButton" id="daysLag" value="30" onClick="generateChart(this.value);">1 <?php echo JText::_("LNG_MONTH");?></button>
                                <button class="chartButton" id="daysLag" value="90" onClick="generateChart(this.value);">3 <?php echo JText::_("LNG_MONTHS");?></button>
                                <button class="chartButton" id="daysLag" value="180" onClick="generateChart(this.value);">6 <?php echo JText::_("LNG_MONTHS");?></button>
                                <button class="chartButton" id="daysLag" value="365" onClick="generateChart(this.value);">1 <?php echo JText::_("LNG_YEAR");?></button>
                                <button class="chartButton" id="daysLag" value="730" onClick="generateChart(this.value);">2 <?php echo JText::_("LNG_YEARS");?></button>
                                <button class="chartButton" id="daysLag" value="1095" onClick="generateChart(this.value);">3 <?php echo JText::_("LNG_YEARS");?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Support & Documentation</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"> <i class="dir-icon-chevron-up"></i></a>
                            <a class="close-link"> <i class="dir-icon-times"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="feed-element feed-small">
                            <i class="pull-left dir-icon-life-saver dir-icon-custom rounded-x dir-icon-bg-sea "></i>
                            <div class="media-body">
                                <a href="http://www.cmsjunkie.com/forum/j-businessdirectory/?p=1">Comunity forum</a>
                                <p>Get in touch with our comunity to find the best solutions</p>
                            </div>
                        </div>
                        <div class="feed-element">
                            <i class="pull-left dir-icon-book dir-icon-custom rounded-x dir-icon-bg-green"></i>
                            <div class="media-body">
                                <a href="http://www.cmsjunkie.com/docs/jbusinessdirectory/businessdiradmin.html">Online documentation</a>
                                <p>Find details about the extension features & functionality</p>
                            </div>
                        </div>
                        <div class="feed-element">
                            <i class="pull-left dir-icon-ticket dir-icon-custom rounded-x dir-icon-bg-orange "></i>
                            <div class="media-body">
                                <a href="https://www.cmsjunkie.com/helpdesk/customer/index/">Support Ticket</a>
                                <p>could not found a solution to your issue? Post a ticket.</p>
                            </div>
                        </div>

                        <div class="feed-element">
                            <i class="pull-left dir-icon-bell dir-icon-custom rounded-x dir-icon-bg-dark-blue "></i>
                            <div class="media-body">
                                <a href="http://www.cmsjunkie.com/contacts/">Contact us</a>
                                <p>Post a sales question</p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Connect with us</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"> <i class="dir-icon-chevron-up"></i></a>
                            <a class="close-link"> <i class="dir-icon-times"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="block-content" id="informations">
                            <ul class="social">
                                <li><a target="social" href="http://twitter.com/cmsjunkie"
                                       class="twitter"><span>Twitter</span> </a></li>
                                <li><a target="social" href="http://facebook.com/cmsjunkie"
                                       class="facebook"><span>Facebook</span> </a></li>
                                <li><a href="mailto:info@cmsjunkie.com" class="email"><span>Email</span>
                                    </a></li>
                                <li><a target="social"
                                       href="https://plus.google.com/100376620356699373069/posts"
                                       class="google"><span>Google</span> </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="span4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>About CMS Junkie</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"> <i class="dir-icon-chevron-up"></i></a>
                            <a class="close-link"> <i class="dir-icon-times"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <p>
                            CMSJunkie offers <strong>top quality</strong> commercial CMS products: extensions,
                            templates, themes, modules for open sources content management
                            systems. All products are completely customizable and ready to be
                            used as a basis for a clean and high-quality website. We are now
                            working with following CMS systems: Magento, Wordperss, Joomla. <br />
                        </p>
                        <p>The CMSJunkie Store team can answer your questions about
                            purchasing, usage of our products, returns, and more. Our aim is to
                            <strong> keep every one of our customers happy</strong> and we are not just saying
                            that. We understand the importance of deadlines to our clients and
                            we deliver on time and keep everything on schedule.</p>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Custom Services</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"> <i class="dir-icon-chevron-up"></i></a>
                            <a class="close-link"> <i class="dir-icon-times"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <p>
                            We do offer <strong>custom development</strong>. If you are
                            interested to contract us to perform some customizations, please
                            feel free to <a href="http://www.cmsjunkie.com/contacts/"
                                            title="Contact CMS Junkie">contact us</a>!
                        </p>
                    </div>
                </div>
            </div>
            <div class="span4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Latest news</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"> <i class="dir-icon-chevron-up"></i></a>
                            <a class="close-link"> <i class="dir-icon-times"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="feed-activity-list">
                            <?php
                            if(!empty($this->news)){?>
                                <?php foreach($this->news as $news) { ?>
                                    <div class="feed-element">
                                        <div>
                                            <small class="pull-right text-navy"><?php echo  $news->publish_ago; ?></small>
                                            <?php
                                            if(isset($news->new) && $news->new) { ?>
                                                <span class="dir-label dir-label-warning pull-left"><?php echo JText::_("LNG_NEW")?></span>&nbsp;
                                            <?php } ?>
                                            <a target="_blank" href="<?php echo $news->link; ?>">
                                                <strong><?php echo $news->title; ?></strong>
                                            </a>
                                            <div><?php echo $news->description; ?></div>
                                            <small class="text-muted"><?php echo $news->publishDateS; ?></small>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php }else{ ?>
                                <p>
                                    <?php echo JText::_("LNG_RETRIEVING_REFRESH_PAGE");?>
                                </p>
                            <?php } ?>
                            <a href="<?php echo JRoute::_('http://www.cmsjunkie.com/blog/') ;?>" target="_blank" class="pull-right"><?php echo JText::_("LNG_VIEW_ALL_NEWS")?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    var siteRoot = '<?php echo JURI::root(); ?>';
    var bookingExtName = '<?php echo getBookingExtName()?>';
    var urlNews = siteRoot+'administrator/index.php?option='+bookingExtName+'&view=jhotelreservation&task=jhotelreservation.getLatestServerNewsAjax';
	var url = siteRoot+'administrator/index.php?option='+bookingExtName;

    //alert(urlNews);
    //retrieve the latest news
    jQuery.ajax({
        url: urlNews,
        type: 'GET'
    });


  //retrieve current version status; 
	var versionCheckTask = '&task=updates.getVersionStatus';
	jQuery.ajax({
		url: url+versionCheckTask,
		dataType: 'json',
		type: 'POST',
		success: function(data){
			  	 console.debug(data);
                 jQuery("#currentVersion").html(data.currentVersion);
                 jQuery("#updateVersion").html(data.updateVersion);
 			  	 jQuery("#updatesStatus").html(data.message);	
 			  	 jQuery("#extensionStatus").html(data.currentStatus);	
        }
	})
</script>