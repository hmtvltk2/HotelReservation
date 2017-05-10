<?php 
/**
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */
?>

<style>
.container-fluid, .subhead-collapse{
	margin: 0 !important;
	padding: 0 !important;	
}

header.header{
	display: none;
}
</style>

<div id="jhp-wrapper">
	<nav class="navbar-default navbar-static-side not-printable" role="navigation" id="dir-navigation">
		<div class="sidebar-collapse not-printable">
			<ul class="nav metismenu" id="side-menu">
				<li class="nav-header">
					<div class="dropdown profile-element">
						<a href="#" class="navbar-minimalize minimalize-styl-2"><i class="fa fa-bars"></i> </a>
					</div>
				</li>
				<?php foreach($template->menus as $menu){?>
					<li class="<?php echo isset($menu["active"])?"active":""?>">
						<a href="<?php echo JRoute::_($menu["link"])?>">
							<i class="fa <?php echo $menu["icon"] ?>"></i>	<span class="nav-label"><?php echo $menu["title"] ?></span>
							<?php if(isset( $menu["new"])){?>
								<span class="label label-info pull-right"><?php echo JText::_("LNG_NEW")?></span>
								<?php } ?>
								
							 <?php if(isset($menu["submenu"])){?> 
								 <span class="fa fa-chevron-left"></span>
							 <?php } ?>
						</a>
						 <?php if(isset($menu["submenu"])){?> 
							<ul class="nav nav-second-level">
								<?php foreach($menu["submenu"] as $submenu){?>
									<li class="<?php echo isset($submenu["active"])?"active":""?>">
										<a href="<?php echo JRoute::_($submenu["link"])?>">
											<?php echo $submenu["title"] ?>
											<?php if(isset( $submenu["new"])){?>
												<span class="label label-info pull-right"><?php echo JText::_("LNG_NEW")?></span>
											<?php } ?>
										</a>
									</li>
								<?php } ?>
							</ul>
						<?php } ?>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="user-manual-section">			
			<h3> User Manual </h3>
			<p> Need help? Use our online documentation to get answers for most of your questions in setting up the J-HotelReservation functionality. </p>
			<a href="http://cmsjunkie.com/docs/jhotelreservation/index.html" target="_blank" > <button class="btn btn-small btn-success"> Read it now </button> </a>
		</div>		
	</nav>
	<div id="page-wrapper">
		<div class="normalheader transition animated fadeIn not-printable">
		    <div class="hpanel">
		        <div class="panel-body">
		            <div class="pull-right m-t-lg" id="hbreadcrumb">
		                <ol class="hbreadcrumb breadcrumb">
		                    <li><a href="<?php echo JRoute::_(JURI::base()."index.php?option=com_jhotelreservation&view=jhotelreservation");?>"><?php echo JText::_("LNG_DASHBOARD")?></a></li>
		                    <li class="active">
		                        <span><?php echo $this->section_name?></span>
		                    </li>
		                </ol>
		            </div>
		            <h2 class="font-light m-b-xs">
		                <?php echo $this->section_name?>
		            </h2>
		            <small><?php echo $this->section_description ?></small>
		        </div>
		    </div>
		</div>
		<div id="content-wrapper">
			<?php echo $template->content?>
			<div class="clear"></div>
		</div>
	</div>
</div>

<script>

jQuery(document).ready(function () {
    // MetisMenu
    jQuery("#side-menu").metisMenu();
	// Minimalize menu
	jQuery('.navbar-minimalize').click(function () {
	    jQuery("#jhp-wrapper").toggleClass("mini-navbar");
	    SmoothlyMenu();

	   	//show hide the user manual section
	    jQuery('.user-manual-section').fadeIn(1000);
	    if (jQuery('#jhp-wrapper').hasClass('mini-navbar')){
	    	jQuery('.user-manual-section').hide();
	    }
	
	});

	setupNav();
	// Collapse ibox function
    jQuery('.collapse-link').click(function () {
        var ibox = jQuery(this).closest('div.ibox');
        var button = jQuery(this).find('i');
        var content = ibox.find('div.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa fa-chevron-left').toggleClass('fa fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    // Close ibox function
    jQuery('.close-link').click(function () {
        var content = jQuery(this).closest('div.ibox');
        content.remove();
    });

    if(jQuery("#page-wrapper").height() < jQuery("#dir-navigation").height())
   		jQuery("#page-wrapper").css("height", jQuery("#dir-navigation").height()+'px');

    // Fullscreen ibox function
    jQuery('.fullscreen-link').click(function() {
        var ibox = jQuery(this).closest('div.ibox');
        var button = jQuery(this).find('i');
        jQuery('body').toggleClass('fullscreen-ibox-mode');
        button.toggleClass('fa-expand').toggleClass('fa-compress');
        ibox.toggleClass('fullscreen');
        setTimeout(function() {
            jQuery(window).trigger('resize');
        }, 100);
    });
});

function setupNav(){
	 if (jQuery(this).width() < 769) {
    	jQuery('#jhp-wrapper').addClass('mini-navbar')
    } else {
    	jQuery('#jhp-wrapper').removeClass('mini-navbar')
    }
}

function SmoothlyMenu() {
    if (!jQuery('#side-menu').hasClass('mini-navbar') || jQuery('body').hasClass('body-small')) {
        // Hide menu in order to smoothly turn on when maximize menu
        jQuery('#side-menu').hide();
        // For smoothly turn on menu
        setTimeout(
            function () {
                jQuery('#side-menu').fadeIn(500);
            }, 100);
    } else {
        // Remove all inline style from jquery fadeIn function to reset menu state
        jQuery('#side-menu').removeAttr('style');
    }
}

</script>