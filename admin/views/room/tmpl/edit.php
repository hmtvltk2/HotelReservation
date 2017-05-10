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
jimport('joomla.html.pane');
$appSetings = JHotelUtil::getApplicationSettings();
$dirs = JHotelUtil::languageTabs();
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_("behavior.calendar");
?>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&layout=edit&room_id='.(int) $this->item->room_id); ?>" method="post" name="adminForm" id="adminForm">

<?php echo JHTML::_( 'form.token' );?>
<?php 
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
		
		echo JHtml::_('tabs.start', 'tab_room_edit', $options);
		echo JHtml::_('tabs.panel', JText::_('LNG_GENERAL_INFORMATION',true), 'roomTab panel_1_id' );
			include(dirname(__FILE__).DS.'details.php');
		echo JHtml::_('tabs.panel', JText::_('LNG_RATE',true), 'roomTab panel_2_id' );
			include(dirname(__FILE__).DS.'rate.php');					
		echo JHtml::_('tabs.panel', JText::_('LNG_PICTURES'), 'roomTab panel_3_id');
			include(dirname(__FILE__).DS.'pictures.php');
		if($this->item->hasBeds24){
			echo JHtml::_('tabs.panel', JText::_('LNG_BEDS24'), 'roomTab panel_4_id');
			include(dirname(__FILE__).DS.'beds24.php');
		}
		echo JHtml::_('tabs.end');
?>		
		<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="room_id" value="<?php echo $this->state->get('room.room_id')?>" />
		<input type="hidden" name="hotel_id" value="<?php echo $this->state->get('room.hotel_id') ?>" />
		<?php echo JHTML::_( 'form.token' ); ?> 
	</form>

	
	
	

	<script language="javascript" type="text/javascript">


//	Joomla.submitbutton = function(task)
//	{
//		if (task == 'room.cancel') {
//			Joomla.submitform(task, document.getElementById('adminForm'));
//		}
//	};

	</script>
	
	<script language="javascript" type="text/javascript">
		var requiredTabIdx = <?php echo JHotelUtil::getTabIndex()?>;
	
	jQuery(document).ready(function()
	{

		updateStatus();
		
		jQuery(".span_up,.span_down").click(function(){
			
			var row = jQuery(this).parents("tr:first");

			if (jQuery(this).is(".span_up")) {
			   row.insertBefore(row.prev());
			} else {
			   row.insertAfter(row.next());
			}
		});
	});
	
	jQuery(function()
	{
		jQuery('#btn_removefile').click(function() {
            //function delPicture( obj, path, pos )
            //{
            pos = jQuery('#crt_pos').val();
            path = jQuery('#crt_path').val();
            jQuery(this).upload('<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/helpers/remove.php?_root_app=<?php echo urlencode(JPATH_ROOT.DS.PATH_PICTURES)?>&_filename=' + path + '&_pos=' + pos, function (responce) {
                    // alert(responce);
                    if (responce == '') {
                        alert("<?php echo JText::_('LNG_ERROR_REMOVING_FILE',true)?>");
                        jQuery(this).val('');
                    }
                    else {
                        var xml = responce;
                        //alert(responce);
                        jQuery(xml).find("picture").each(function () {
                            if (jQuery(this).attr("error") == 0) {
                                removePicture(jQuery(this).attr("pos"));
                            }
                            else if (jQuery(this).attr("error") == 2)
                                alert("<?php echo JText::_('LNG_ERROR_REMOVING_FILE',true)?>");
                            else if (jQuery(this).attr("error") == 3)
                                alert("<?php echo JText::_('LNG_FILE_DOESNT_EXIST',true)?>");
                        });

                        jQuery('#crt_pos').val('');
                        jQuery('#crt_path').val('');
                    }
                }, 'html'
            );

        });
		
		
	});
	

	function updateStatus(){
		if(jQuery("input[name='price_type']:checked").val()==1){
			jQuery("#single-supplement-container").show();
			jQuery("#single-discount-container").hide();
		}else{
			jQuery("#single-supplement-container").hide();
			jQuery("#single-discount-container").show();
		}
	}
	
	function clickBtnIgnoreDays(crtPos)
	{
		var pos = jQuery('#crt_interval_number').val();
		if( pos == crtPos)
		{
			jQuery('#dates_room_calendar').DatePickerHide();
			this.className = 'span_ignored_days';
			return false;
		}
		jQuery('#crt_interval_number').val(crtPos);
		jQuery('#div_interval_number_dates_'+crtPos).append( jQuery('#div_calendar') );
		jQuery('#dates_room_calendar').DatePickerShow();
		this.className = 'span_ignored_days_sel';
	}

	function clickBtnSeasonIgnoreDays(crtPos)
	{
		jQuery('#dates_room_season_calendar').DatePickerShow();
		jQuery('#div_interval_season_datai').append( jQuery('#div_season_calendar') );
		this.className = 'span_ignored_days_sel';
	}

	
	
	function removePicture(pos)
	{
		var tb = document.getElementById('table_room_pictures');
		//alert(tb);
		if( tb==null )
		{
			alert('Undefined table, contact administrator !');
		}
		
		if( pos >= tb.rows.length )
			pos = tb.rows.length-1;
		tb.deleteRow( pos );
	
	}

    jQuery(document).ready(function () {
        addingClasses("td.btn-group");
    });

    Joomla.submitbutton = function(pressbutton) {
        if (pressbutton == 'room.save' || pressbutton == 'room.apply' || pressbutton == 'room.save2new') {
        	 $$('dt.tabs.roomTab')[0].fireEvent('click');
		     $$('dt.tabs.roomName')[requiredTabIdx].fireEvent('click');
		        
	        jQuery('#adminForm').validationEngine('attach');
	        jQuery('#adminForm').validationEngine('validate');

            submitform( pressbutton );
        } else {
            jQuery('#adminForm').validationEngine('detach');
            submitform( pressbutton );
        }
    }

</script>


