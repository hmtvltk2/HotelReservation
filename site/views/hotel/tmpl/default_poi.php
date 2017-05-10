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

if(isset($this->hotel->poi) && count($this->hotel->poi)>0){
?>
    <br/>
    <div id="content">
        <div>
            <?php
            foreach( $this->hotel->poi as $poi )
            {
                ?>
                <div class="section group">
                    <div class="col column_3_of_12">

                        <?php
                        if(!empty($poi->pictures) ) {
                            ?>
                            <div class="thumbs" data-gallery="one">
                                <div class="room-image listImageContainer">
                                    <a href="<?php echo JURI::base() . PATH_PICTURES . POINTS_OF_INTEREST_PICTURE_PATH . '/' . $poi->pictures[0]->poi_picture_path ?>"
                                       data-gallery="<?php echo $poi->id ?>"
                                       style="background-image:url('<?php echo JURI::base() . PATH_PICTURES . POINTS_OF_INTEREST_PICTURE_PATH . '/' . $poi->pictures[0]->poi_picture_path ?>')"
                                       class="listImage"
                                       title="<?php echo JHotelUtil::setAltAttribute($poi->pictures[0]->poi_picture_path); ?>">
                                        <span class="icon-overlay"></span>
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="col column_8_of_12 vertical-alignment">
                        <div class="itemTitle">
                            <h5><a href="<?php echo JHotelUtil::getHotelPoiLink($poi,$this->hotel->hotel_id)?>"><?php echo $poi->name;?></a></h5>
                        </div>
                        <div class="">
                            <?php echo substr(strip_tags($poi->description),0, 150); ?>
                             <a class="linkmore" target="_blank"
                                href="<?php echo JHotelUtil::getHotelPoiLink($poi,$this->hotel->hotel_id) ?>"><b><?php echo JText::_('LNG_READ_MORE')?></b></a>...
                            
                        </div>
                       
                        <div class="clear contact">
	                          <?php echo JText::_('LNG_DISTANCE_FROM_HOTEL')?>: <strong><?php echo $poi->distance.' km'; ?></strong>
                        </div>
                    </div>
               
                </div>
                <div class='poi-picture-container'>
                    <div class="thumbs" data-gallery="one">
                        <?php
                        if(!empty($poi->pictures))
                        {
                            foreach( $poi->pictures as $k => $picture ) {
                                if ($k > 0) {
                                    $picture->poi_picture_path = JURI::base() . PATH_PICTURES . POINTS_OF_INTEREST_PICTURE_PATH . '/' . $picture->poi_picture_path;

                                    ?>
                                    <a href="<?php echo $picture->poi_picture_path; ?>"
                                       data-gallery="<?php echo $poi->id ?>"
                                       style="background-image:url('<?php echo $picture->poi_picture_path; ?>')"
                                       title="<?php echo JHotelUtil::setAltAttribute($picture->poi_picture_path); ?>"></a>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
<?php } ?>

<script type="text/javascript">
    jQuery('.thumbs a').touchTouch();
</script>
