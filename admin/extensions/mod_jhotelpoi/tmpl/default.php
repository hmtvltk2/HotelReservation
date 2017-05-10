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

defined('_JEXEC') or die('Restricted access');

//distance setting from the module settings
$poi_distance = $params->get('poi_distance');
$hotelId = JRequest::getVar("hotel_id");
$userData = $_SESSION['userData'];
?>

<?php
if(isset($pois) && count($pois)>0) {
	?>
	<div id="poi">
	<?php
    foreach ($pois as $single_poi) {
        if (isset($single_poi->id)) {

            if (isset($poi_distance) && !empty($poi_distance) && (float)$poi_distance >= (float)$single_poi->distance) {


	            $hotel_id = '';
            	if((isset($hotelId) && !empty($hotelId))){
            		$hotel_id = '&hotelId='.$hotelId;
		            //JHotelUtil::getHotelPoiLink($single_poi,$hotelId);
	            }
	            ?>
                <div id="poi-container" class="poi-container">
                    <div class="section group" id="poi">
                        <div class="poi-image-container col column_12_of_12">
                            <?php if (!empty($single_poi->poi_picture_path)) { ?>
                                <div class='picture-container'>
                                    <ul class="gallery list-unstyled">
                                        <li data-thumb="<?php echo JURI::root() . PATH_PICTURES . POINTS_OF_INTEREST_PICTURE_PATH . DS . $single_poi->poi_picture_path ?>">
                                            <a target="_blank"
                                               href="<?php echo
                                               JHotelUtil::getHotelPoiLink($single_poi,$hotelId) ?>">
                                                <img
	                                                style="width: 100%;"
                                                    src="<?php echo JURI::root() . PATH_PICTURES . POINTS_OF_INTEREST_PICTURE_PATH . DS . $single_poi->poi_picture_path ?>"
                                                    alt="<?php echo JHotelUtil::setAltAttribute($single_poi->poi_picture_path); ?>"/>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="section group" id="poi">
                        <div class="col column_12_of_12">
                            <div>
                                <h3>
                                    <a target="_blank" class="poi_desc" href="<?php echo
                                    JHotelUtil::getHotelPoiLink($single_poi,$hotelId)?>"><?php echo $single_poi->name; ?></a>
                                </h3>
	 							<div class="sp_offers poi-description">
	                                <div class="offerDescription">
	                                    <div>
	                                        <?php if (!empty($single_poi->description)) { ?>
	                                            <div class="poi_desc">
	                                                <?php echo JHotelUtil::truncate(strip_tags($single_poi->description), 200, false); ?>
	                                                <a class="linkmore" target="_blank"
	                                                   href="<?php echo JHotelUtil::getHotelPoiLink($single_poi,$hotelId);?>">
		                                                <?php echo JText::_('LNG_READ_MORE') ?></a>...
	                                            </div>
	                                        <?php } ?>
	                                        <br/>
	
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>
	                            </div>
                                <div class="poi_desc">
                                    <p>
                                        <i class="dir dir-icon-location-arrow"></i>
                                        <strong>   <?php echo $single_poi->formattedDistance . ' Km'; ?></strong>
                                        <?php echo strtolower(JText::_('LNG_FROM'));
                                        echo (isset( $userData->keyword ) && $userData->keyword != "" )?" ".$userData->keyword:" ".$single_poi->hotel_name;
                                        ?>                                        
                                    </p>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            <?php }
        }
    }
    ?>
	</div>
<?php } ?>