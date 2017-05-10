<?php
/**
 * @package    JHotelReservation
 * @subpackage  com_jhotelreservation
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
$appSetings = JHotelUtil::getApplicationSettings();
$dirs = JHotelUtil::languageTabs();
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=childcategory');?>" method="post" name="adminForm" id="adminForm">

	<fieldset>
		<legend><?php echo JText::_('LNG_CHILD_CATEGORY',true); ?></legend>
		
		<table class="admintable"  border=0>

			<tr>
				<td width=10% nowrap class="key"><?php echo JText::_('LNG_NAME',true); ?> </td>
				<td colspan="2">
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

						$j=0;
						foreach( $dirs  as $_lng ){
							$langName= JHotelUtil::languageNameTabs($_lng);
							echo JHtml::_('tabs.panel', $langName, 'tab'.$j );
							$this->item->name = empty($this->item->name)?"":$this->item->name;
							$langContent = isset($this->translations[$_lng])?$this->translations[$_lng]:$this->item->name;
                            $langContent = htmlspecialchars($langContent);
                            ?>

                            <input type="text" name="name_<?php echo $_lng?>" class="validate[required] text-input" value="<?php echo $langContent?>" size="100">
                    <?php
						}
						echo JHtml::_('tabs.end');
					  ?>
				</td>
			</tr>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_CHILDREN_AGE' ,true); ?> :</TD>
				<TD nowrap colspan=2 ALIGN=LEFT>
					<input 
						type		= "text"
						name		= "min_age"
						id			= "min_age"
						value		= "<?php echo $this->item->min_age >=0 ? $this->item->min_age:''?>"
						size		= 2
						maxlength	= 5
						placeholder="<?php echo JText::_( 'LNG_MIN' ,true); ?>"					
					/>
					&nbsp;:&nbsp;
					<input 
						type		= "text"
						name		= "max_age"
						id			= "max_age"
						value		= '<?php echo $this->item->max_age !=0 && $this->item->min_age < $this->item->max_age ? $this->item->max_age : ''?>'
						size		= 2
						maxlength	= 5
						placeholder="<?php echo JText::_( 'LNG_MAX' ,true); ?>"					
					/>
				</TD>
			</TR>
			<tr>
				<td width=10% nowrap class="key"><?php echo JText::_('LNG_STATUS',true); ?> </td>
				<td>
					<input 
						style		= 'float:none'
						type		= "radio"
						name		= "status"
						id			= "status"
						value		= '1'
						<?php echo $this->item->status==1? " checked " :""?>
					/>
					<?php echo JText::_('LNG_ENABLED',true); ?>
					&nbsp;
					<input 
						style		= 'float:none'
						type		= "radio"
						name		= "status"
						id			= "status"
						value		= '0'
						<?php echo $this->item->status==0? " checked " :""?>
					/>
					<?php echo JText::_('LNG_DISABLED',true); ?>
				</td>
			</tr>
		</table>
	</fieldset>
	

	<input type="hidden" name="hotel_id" value="<?php echo $this->state->get('childcategory.hotel_id') ?>" />
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>


<script>
	var offerSelectList = null;
	jQuery(document).ready(function(){

		<?php if($this->state->get('childcategory.hotel_id')>0){?>
		jQuery("select#room_ids").selectList({ 
			 sort: true,
			 classPrefix: 'room_ids',
			 onAdd: function (select, value, text) {
				addSelection(value);
			 },
			 onRemove: function (select, value, text) {
				 removeSelection(value);
				 jQuery('select#room_ids option[value='+value+']').removeAttr('selected');	
			 }
		});

		
		
		offerSelectList = jQuery("select#offer_ids").selectList({ 
			sort: true,
			classPrefix: 'offer_ids',
			instance: true,
			 onAdd: function (select, value, text) {
					
			},
			 onRemove: function (select, value, text) {
				 jQuery('select#offer_ids option[value='+value+']').removeAttr('selected');
			 }
		});
		
		<?php } ?>
	
    });

	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'childcategory.save' || pressbutton == 'childcategory.apply') {

			jQuery("form[name='adminForm']").validationEngine('attach');
			if (!jQuery("form[name='adminForm']").validationEngine('validate')) {
				return false;
			}

			submitform( pressbutton );
			return;
		} else {
			jQuery('#adminForm').validationEngine('detach');
			submitform( pressbutton );
		}
	};
        

    function checkAllOffers(){
        //console.debug("check");
        uncheckAllOffers();
    	jQuery(".offer_ids-select option").each(function(){ 
			jQuery(this).attr("selected","selected"); 
			if(jQuery(this).val()!=""){
				offerSelectList.add(jQuery(this));
			}
		});

    }  

    function uncheckAllOffers(){
        console.debug("uncheck");
    	jQuery("#offer_ids option").each(function(){ 
			jQuery(this).removeAttr("selected"); 
		});
    	
    	offerSelectList.remove();
    }  
</script>