<?php // no direct access
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


defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.session.session' );

$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
//dmp($userData->roomGuests);

$userData =  UserDataService::getUserData();
$app = JFactory::getApplication();
$menu = $app->getMenu();
$voucher=$userData->voucher;
if ($menu->getActive() == $menu->getDefault()) {
	$voucher='';
}
$itemId = JHotelUtil::getItemIdS();
$minimizeMobile = $params->get('minimize-mobile');
$mobileClass = $minimizeMobile?"minimizeMobile":"";
?>
<script>
	var message = "<?php echo JText::_('LNG_ERROR_PERIOD',true)?>";
	var defaultEndDate = "<?php echo $params->get('end-date'); ?>";
	var defaultStartDate = "<?php echo $params->get('start-date'); ?>";

    var dateFormat = '<?php echo $appSettings->dateFormat; ?>';
    var language = '<?php echo JHotelUtil::getLanguageTag();?>';
    var formatToDisplay = calendarFormat(dateFormat);
	
</script>
<div id="hotel-map-container" class="popup map" style="display:none;">
    <div class="titleBar">
        <span class="popup-title"></span>
		<span  title="Cancel"  class="popup-close-button" onClick="jQuery.unblockUI();">
			<span title="Cancel" class="closeText">x</span>
		</span>
    </div>

        <div class="popup-content">
            <h3 class="title"> <?php echo JText::_('LNG_FIND_BEST_HOTEL_DEAL',true);?></h3>
            <div class="popup-content-body">
                <?php require JPATH_SITE.'/components/com_jhotelreservation/include/hotels_map.php';?>
                <div id="hotelMap-1" style="position: relative"></div>
            </div>
        </div>
</div>
<div class="row-fluid mobileFilter" id="mobileFilter">
	<i class="fa fa-filter"></i> <?php echo JText::_('LNG_FILTER_RESULT')?>
</div>

<?php ?>
<div class="<?php echo $mobileClass;?>">
		<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotels'.$itemId,false,-1) ?>" method="post" name="userModuleForm" id="userModuleForm" >
			<input type='hidden' name='task' value='hotels.searchHotels'/>
			<input type='hidden' name='year_start' value=''/>
			<input type='hidden' name='month_start' value=''/>
			<input type='hidden' name='day_start' value=''/>
			<input type='hidden' name='year_end' value=''/>
			<input type='hidden' name='month_end' value=''/>
			<input type='hidden' name='hotel_id' value=''/>
			<input type='hidden' name='day_end' value=''/>
			<input type='hidden' name='rooms' value='' />
			<input type='hidden' name='guest_adult' value=''/>
			<input type='hidden' name='guest_child' value=''/>
			<input type='hidden' name='filterParams' id="filterParams" value='<?php echo isset($userData->filterParams) ? $userData->filterParams :''?>' />
			<input type="hidden" name="resetSearch" id="resetSearch" value="true"/>
			<input type="hidden" name="searchType" id="searchType" value="<?php echo JRequest::getVar("searchType")?>"/>
			<input type="hidden" name="searchId" id="searchId" value="<?php echo JRequest::getVar("searchId")?>"/>
			<input type="hidden" name="priceLow" id="priceLow" value="">
			<input type="hidden" name="priceHigh" id="priceHigh" value="">
			<?php 
				if(isset($userData->roomGuests)){
					foreach($userData->roomGuests as $guestPerRoom){?>
					<input class="room-search" type="hidden" name='room-guests[]' value='<?php echo $guestPerRoom?>'/>
					<?php }
				}
			?>
			<?php 
				if(isset($userData->roomGuestsChildren)){
					foreach($userData->roomGuestsChildren as $guestPerRoom){?>
					<input class="room-search" type="hidden" name='room-guests-children[]' value='<?php echo $guestPerRoom?>'/>
					<?php }
				}
			?>
			<div class="mod_hotel_reservation<?php echo $moduleclass_sfx;?>" id="mod_hotel_reservation">
				<div class="reservation-container ">
					<h3><?php echo JText::_('LNG_FIND_BEST_HOTEL_DEAL')?></h3>
					
					<?php if ($params->get('show-search')==1){?>
						<div class="destination divider">
							<div class="search-nav">
								<label>
									<?php echo JText::_('LNG_FIND_HOTEL');?>
								</label>
								
								<input autocomplete="off" class="keyObserver inner-shadow" type="text" value="<?php echo isset($userData->keyword) && $activeComponent=="com_jhotelreservation" && $activeView=="hotels"?$userData->keyword:''?>" name="keyword" id="keyword" placeholder="<?php echo JText::_("LNG_TYPE_INSTRUCTIONS")?>"/>
								<i class="fa fa-expand expandLocationSearch" style="display:<?php echo $params->get('location-dialogue')?"":"none";?>"></i>
								<a href="javascript:void(0)" onclick="displayModalMap()" id="show_hotels_map"><?php echo JText::_('LNG_SHOW_HOTELS_MAP',true)?></a>
							</div>
						</div>
					<?php }?>
					<div class="dates divider">
                        <div class="row-fluid ">
                            <div class="date span6">
                                <div class="input-append">
                                    <label for="jhotelreservation_datas"><?php echo JText::_('LNG_ARIVAL')?></label>
                                    <input name="jhotelreservation_datas"
                                           data-provide="datepicker"
                                           class="form-control date_hotelreservation"
                                           id="jhotelreservation_datas"
                                           type="text" 
                                           readonly style = "cursor:pointer"
                                           value="<?php echo $jhotelreservation_datas; ?>"
                                           onchange="if(!checkStartDate(this.value, defaultStartDate,defaultEndDate))return false;setDepartureDate('jhotelreservation_datae',this.value,dateFormat)"/>
                                    <button type="button" class="btn" id="jhotelreservation_datas_img"><i class="icon-calendar"></i></button>
                                </div>
                            </div>

                            <div class="date span6">
                                <div class="input-append">
                                    <label for="jhotelreservation_datae"><?php echo JText::_('LNG_DEPARTURE')?></label>
                                    <input name="jhotelreservation_datae"
                                           data-provide="datepicker"
                                           id="jhotelreservation_datae"
                                           class="form-control date_hotelreservation"
                                           type="text" 
                                           readonly style = "cursor:pointer"
                                           value="<?php echo $jhotelreservation_datae; ?>"
                                           onchange="checkEndDate(this.value,defaultStartDate,defaultEndDate)"/>
                                    <button type="button" class="btn" id="jhotelreservation_datae_img"><i class="icon-calendar"></i></button>
                                </div>
                            </div>
                        </div>
						<div class="no-dates">
							<input type="checkbox" name="no-dates" id="no-dates" value="1" <?php echo isset($userData->noDates) && $userData->noDates!=0?"checked='checked'":"" ?>/>
							 <label for="no-dates"><?php echo JText::_('LNG_NO_DATES')?></label>
						</div>
						<div class="clear"></div>
					</div>
					<div class="rooms divider row-fluid">
						<div class="span4">
								<label for=""><a id="" href="javascript:void(0);" onclick="showExpandedSearch()"><?php echo JText::_('LNG_ROOMS')?></a></label>
								<div class="styled-select">
								<select id='jhotelreservation_rooms' name='jhotelreservation_rooms'
										class		= 'select_hotelreservation keyObserver inner-shadow'
									>
										<?php
									$i_min = 1;
									$i_max = $params->get("max-rooms");
									if(!isset($i_max))
										$i_max= 10;
									
									for($i=$i_min; $i<=$i_max; $i++)
									{
									?>
									<option 
										value='<?php echo $i?>'
										<?php echo $jhotelreservation_rooms==$i ? " selected " : ""?>
									>
										<?php echo $i?>
									</option>
									<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="span4">
							<label><?php echo JText::_('LNG_GUEST',true)?></label>
							<div class="styled-select">
								<select name='jhotelreservation_guest_adult' id='jhotelreservation_guest_adult'
									class		= 'select_hotelreservation keyObserver inner-shadow'
								>
									<?php
									$i_min = 1;
									$i_max = $params->get("max-room-guests");
									if($jhotelreservation_guest_adult>$i_max)
										$i_max = $jhotelreservation_guest_adult;
									
									for($i=$i_min; $i<=$i_max; $i++)
									{
									?>
									<option value='<?php echo $i?>'  <?php echo $jhotelreservation_guest_adult==$i ? " selected " : ""?>><?php echo $i?></option>
									<?php
									}
									?>
								</select>
							</div>
						</div>
				
						<?php if($appSettings->show_children!=0){?>
							<div class="span4 col-md-4" style="<?php echo $appSettings->show_children!=0 ? "":"display:none" ?>">
								<label><?php echo JText::_('LNG_CHILDREN',true)?></label>
								<div class="styled-select">
									<select name='jhotelreservation_guest_child' id='jhotelreservation_guest_child'
									class		= 'select_hotelreservation'
									>
										<?php
										$i_min = 0;
										$i_max = 10;
										
										for($i=$i_min; $i<=$i_max; $i++)
										{
										?>
											<option <?php echo $jhotelreservation_guest_child==$i ? " selected " : ""?> value='<?php echo $i?>'  ><?php echo $i?></option>
											<?php
											}
											?>
										</select>
								</div>
							</div>
							<?php if($appSettings->enable_children_categories!=0){?>
								<div class="row-fluid">
								<div id="introText" style="display:<?php echo !empty($userData->jhotelreservation_child_ages)?'block':'none';?>;" class="childrenAges">
									<label><?php echo JText::_('LNG_CHILDREN_AGE_SELECT',true)?></label>
								</div>
								<div id="childrenAges" class="row-fluid" >
									<?php if(!empty($userData->jhotelreservation_child_ages)){				
										$childrenAgesArray = $userData->jhotelreservation_child_ages;
										foreach($childrenAgesArray as $childAge){
									?>
										 <div class="styled-select span3 col-md-3 spacer fixedWidth">
											 <select id="jhotelreservation_child_ages[]"  name="jhotelreservation_child_ages[]">
											 <?php for ($j = 0; $j <= HOTEL_MAX_CHILDREN_AGE; $j++){?>
										        <option value="<?php echo $j?>"  <?php echo intVal($childAge)==$j ? "selected" : ""?>><?php echo $j?></option>
										     <?php }?>   
											</select>
										 </div>
									<?php } 
									
									}
									?>	
									
									</div>
								</div>
							<?php }?>
						<?php }?>
						
					</div>
					<?php if ($params->get('show-voucher')==1){?>
						<div class="voucher divider">
							<label><?php echo JText::_('LNG_VOUCHER',true)?></label>
							<input type="text" class="keyObserver inner-shadow" value="<?php echo $voucher ?>" name="voucher" id="voucher" />
						</div>
					<?php } ?>
					<div>
						<button	class="ui-hotel-button"  onClick	= "jQuery('#resetSearch').val(1);searchDates('userModuleForm','no-dates');"
							type="button" name="checkRates" value="checkRates"><?php echo JText::_('LNG_SEARCH',true)?>
						</button>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			
			<?php 
			if($params->get("show-filter") && !empty($userData->searchFilter)){
				$filter = $userData->searchFilter;
				$filterCategories= $filter["filterCategories"];
				$showFilter = JRequest::getVar('showFilter');
				$filterDisplayType = 1;

                if($params->get('show-stars') == 0) {
                    $filterCategories['stars'] = array();
                }
				if(count($filterCategories)>0 && isset($showFilter)){
                    ?>
					<div id="search-filter" class="seach-filter moduletable module-menu" >
					<div>
						<div class="searchInnerContainer">
                            <div style="clear: both;">
						        <h3 style="float: left"><?php echo JText::_('LNG_SEARCH_FILTER',true)?></h3>
                                <a href="javascript:void(0);" class="resetLink" onClick="resetFilter()"><?php echo JText::_('LNG_SEARCH_RESET_FILTER',true)?></a>
                            </div>
						<?php 
						switch ($filterDisplayType){
							case 1:
								foreach ($filterCategories as $filterCategory){
                                    if(isset($filterCategory['name']) && isset($filterCategory['items'])) {
                                    	//dmp($filterCategory['items']);
	                                   
	                                    
	                                    $displayFilterCat = false; 
	                                    foreach ($filterCategory['items'] as $filterCategoryItem) {
	                                    	if (!empty($filterCategoryItem->count)) {
	                                    		$displayFilterCat = true; 
	                                    		break;
	                                    	}
	                                    }
	                                    if ($displayFilterCat){
	                                    	   echo '<div class="search-category-box">';
		                                       echo '<h4>' . $filterCategory['name'] . '</h4>';
		                                       echo '<ul>';
                                    	}
                                        foreach ($filterCategory['items'] as $filterCategoryItem) {
                                            if (!empty($filterCategoryItem->count)) {
                                                ?>
                                                <li class="filterListItem">
                                                    <div class="styledCheckbox">
                                                        <input type="checkbox"
                                                               id="filterCategoryItem<?php echo $filterCategoryItem->identifier . $filterCategoryItem->id ?>" <?php if (isset($filterCategoryItem->selected)) echo "checked" ?>
                                                               onclick="<?php if (isset($filterCategoryItem->selected)) echo "removeFilterRule('$filterCategoryItem->identifier=$filterCategoryItem->id')"; else echo "addFilterRule('$filterCategoryItem->identifier=$filterCategoryItem->id')"; ?>">
                                                        <label
                                                            for="filterCategoryItem<?php echo $filterCategoryItem->identifier . $filterCategoryItem->id ?>">
                                                        </label>

                                                        <label class="outsideLabel"
                                                               for="filterCategoryItem<?php echo $filterCategoryItem->identifier . $filterCategoryItem->id ?>">
                                                            <?php 
                                                            if (isset($filterCategory['type']))
                                                           		 $translationValue =  $filterCategoryItem->name;
                                                            else
                                                            	$translationValue = JText::_('LNG_' . strtoupper(str_replace(" ", "_", $filterCategoryItem->name)));
                                                            	 
                                                          
                                                            if ($filterCategoryItem->name == $translationValue) {
                                                                echo $translationValue;
                                                            } else {
                                                                echo $translationValue;
                                                            }
                                                            ?><?php echo '(' . $filterCategoryItem->count . ')' ?>
                                                        </label>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                        }
                                        if ($displayFilterCat){
                                    	    echo '</ul>';
                                        	echo '</div>';
                                        }
                                    }
								}
								break;
						}
						?>
						
						</div>
						</div>
					</div>
			<?php
				 }
			}
			if($params->get("show-small-map")){
				$markers = modJHotelReservationHelper::getStaticMarkers($hotels);
			
			?>
				<a onclick="displayModalMap()">
					<div id="" class="">
			            <div class="popup-content-body">
			                <?php echo  "<img alt='company map' src='https://maps.googleapis.com/maps/api/staticmap?size=400x400&maptype=roadmap&key=$appSettings->google_map_key$markers'>"?>
			            </div>
					</div>
				</a>
			<?php
				}
			 ?>
			 
			 
		</form>
</div>
<script>
<?php if($params->get("show-filter")){ ?>
	jQuery('.filterListItem input[type="checkbox"]').on('click', function() {
		showLoadingAnimation();
	});
<?php }?>
	var maxChildAge= <?php echo HOTEL_MAX_CHILDREN_AGE?>;
    jQuery("#jhotelreservation_guest_child").change(
    	 function(){
			jQuery("#childrenAges").empty();
			var limit = jQuery(this).val();
			if(limit==0)
				jQuery("#introText").hide();
			for (i = 0; i < limit; i++) {
				 var container = jQuery("<div></div>").attr("class","styled-select span3 spacer fixedWidth");
				 var combo = jQuery("<select></select>").attr("id", "jhotelreservation_child_ages[]").attr("name", "jhotelreservation_child_ages[]");
				 for (j = 0; j <= maxChildAge; j++)
			        combo.append("<option value='"+j+"'>" + j + "</option>");
	
				 container.append(combo);
				 jQuery("#childrenAges").append(container);
				 jQuery("#introText").show(); 
			}
	});


    jQuery(document).ready(function() {
    	  jQuery.fn.datepicker.defaults.language = language;
          jQuery.fn.datepicker.defaults.format = formatToDisplay;
          loadMapScript(3,mod_hotels);
	      jQuery("#jhotelreservation_datas #jhotelreservation_datae").datepicker({
	         autoclose: true,
	         format: formatToDisplay,
	         language: language
	      });
	      jQuery("#mobileFilter").click(function(){
	    	  jQuery(".minimizeMobile").toggle();
	      });

	      jQuery("#jhotelreservation_datas_img").click(function(){
	         jQuery("#jhotelreservation_datas").focus();
	      });
	
	      jQuery("#jhotelreservation_datae_img").click(function(){
	         jQuery("#jhotelreservation_datae").focus();
	      });
          jQuery(".keyObserver").keypress( function(e){
       		 if(e.which == 13) {
               	//checkRoomRates('userModuleForm');
		         searchDates('userModuleForm','no-dates');
		         //if(typeof checkRoomRates('userModuleForm') == 'undefined'){showLoadingAnimation();}
              }
          });
    });

    /**
     * Data for the markers consisting of a name, a LatLng and a zIndex for
     * the order in which these markers should display on top of each
     * other.
     */
    var mod_hotels = [
        <?php
     	  	echo (JHotelUtil::generateMapInfoContent($hotels));
         ?>
    ];
    function displayModalMap(){
        jQuery.blockUI({
            message: jQuery('#hotel-map-container'), css: {
                top:  100 + 'px',
                left: '5%',
                width: '80%',
                backgroundColor: '#fff'
            }
        });
        jQuery('.blockUI.blockMsg').center();
        loadMapScript(1,mod_hotels);
    }
    function resetFilter(){
        jQuery('#resetSearch').val(1);
	    var inputDefaultStartDate = document.getElementById('jhotelreservation_datas');
	    var inputDefaultEndDate = document.getElementById('jhotelreservation_datae');
	    inputDefaultStartDate.value = '<?php echo $date;?>';
	    inputDefaultEndDate.value = '<?php echo $next_date?>';
        showLoadingAnimation();
        jQuery('#userModuleForm').submit();
    }



function searchDates(formName,checkboxName) {
	var checkBox = document.getElementById(checkboxName);
	if (checkBox.checked) {
		var inputDefaultStartDate = document.getElementById('jhotelreservation_datas');
		var inputDefaultEndDate = document.getElementById('jhotelreservation_datae');
		inputDefaultStartDate.value = '<?php echo $date;?>';
		inputDefaultEndDate.value = '<?php echo $next_date?>';
		if (typeof checkRoomRates(formName) == 'undefined') {
			showLoadingAnimation();
		}
	} else {
		if (typeof checkRoomRates(formName) == 'undefined') {
			showLoadingAnimation();
		}
	}
}

</script>
		<?php require_once JPATH_SITE.'/components/com_jhotelreservation/include/multipleroomselection.php'; ?> 
		<?php require_once 'autocomplete.php'; ?>
		<?php require_once 'loading-info.php'; ?>  
		<?php require_once 'dialog.php'; ?>  
		