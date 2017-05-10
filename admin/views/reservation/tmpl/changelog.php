<?php defined('_JEXEC') or die('Restricted access'); 
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
JHTML::_('stylesheet', 	JURI::root().'components/com_jhotelreservation/assets/style/responsiveMaterialTable.css');
?>

<div class="responsive_table-responsive-vertical">
	<?php if(!empty($this->item->reservationData->userData->user_id)):?>
	<div class="right padding10"><?php echo JText::_("LNG_CREATED_BY").":".JFactory::getUser($this->item->reservationData->userData->user_id)->name; ?> </div>
	<?php endif;?>
	<table class="responsive_table responsive_table-hover responsive_table-mc-light-blue"  id="itemList">

<?php 		if(count($this->changeLogs)> 0 ) { ?>
		<thead>
		<tr>
			<th width="1%">
				<?php echo JText::_('LNG_RESERVATION',true); ?>
			</th>
			<th>
				<?php echo JText::_('LNG_GUEST_NAME',true) ?>
			</th>
			<th>
				<?php echo JText::_('LNG_USER_ID',true) ?>
			</th>
			<th>
				<?php echo JText::_('LNG_USERNAME',true) ?>
			</th>
			<th>
				<?php echo JText::_('LNG_NAME',true) ?>
			</th>
			<th>
				<?php echo JText::_('LNG_CHANGES_TIME',true) ?>
			</th>
			<th>
				<?php echo JText::_('LNG_CREATED_BY',true) ?>
			</th>
			<th>
				<?php echo JText::_('LNG_CHANGES',true) ?>
			</th>
		</tr>
		</thead>

		<tbody>
		<?php
			foreach ( $this->changeLogs as $changeLog ):?>
				<tr>
					<td
						data-title="<?php echo JText::_( 'LNG_RESERVATION', true ) ?>"> <?php echo $changeLog->reservation_id ?></td>
					<td
						data-title="<?php echo JText::_( 'LNG_GUEST_NAME', true ) ?>">
						<div class="text-clearing">
							<?php echo $this->item->reservationData->userData->first_name . ' ' . $this->item->reservationData->userData->last_name ?>
						</div>
					</td>
					<td
						data-title="<?php echo JText::_( 'LNG_USER_ID', true ) ?>"> <?php echo $changeLog->user_id ?></td>
					<td
						data-title="<?php echo JText::_( 'LNG_USERNAME', true ) ?>"> <?php echo $changeLog->username ?></td>
					<td
						data-title="<?php echo JText::_( 'LNG_NAME', true ) ?>"> <?php echo $changeLog->name ?></td>
					<td
						data-title="<?php echo JText::_( 'LNG_CHNAGES_TIME', true ) ?>"> <?php echo $changeLog->date ?></td>
					<td
						data-title="<?php echo JText::_( 'LNG_CREATED_BY', true ) ?>"> <?php echo $changeLog->createdByUsername ?></td>
					<td
						data-title="<?php echo JText::_( 'LNG_CHANGES', true ) ?>">
						<div class="text-clearing">
							<ul>
							<?php foreach ( $changeLog->description as $item ) {
								if ( strlen( $item ) > 0 ) {
									echo '<li>';
									if ( isset( $item ) ) {
										if (strpos($item,JText::_( 'LNG_FROM',true )) !== false) {

											$label = explode( JText::_( 'LNG_FROM',true ), $item );
											$value = explode( JText::_( 'LNG_TO',true ), $label[1] );
											$value[0] = str_replace('  ',"",$value[0]);
											$value[1] = str_replace('  ',"",$value[1]);
											$value[0] = !empty($value[0])?$value[0]:JText::_("LNG_EMPTY",true);
											$value[1] = $value[1] == " "?JText::_("LNG_EMPTY",true):$value[1];

											echo $label[0] . " " . JText::_( 'LNG_FROM',true ) . " <b class='log-red'>" . $value[0] . "</b> " . JText::_( 'LNG_TO',true ) . " <b class='log-green' >" . $value[1]. "</b>";

										}else if(strpos($item,JText::_( 'LNG_REMOVED',true )) !== false) {

											$value = explode(JText::_( 'LNG_REMOVED',true ), $item);

											echo $value[0]."<b class='log-red'> ". JText::_( 'LNG_REMOVED',true ).$value[1]."</b>";

										}else if(strpos($item,JText::_( 'LNG_ADDED',true )) !== false) {

											$value = explode(JText::_( 'LNG_ADDED',true ), $item);

											echo $value[0]."<b class='log-green'>".JText::_( 'LNG_ADDED',true ).$value[1]."</b>";
										}
										else
											echo $item;
									}
									echo '</li>';
								}
							} ?>
							</ul>
						</div>
					</td>
				</tr>
			<?php endforeach;
		;?>
		</tbody>
		<?php }else {
	?>
	<thead>
	<tr>
		<th width="1%">
			<?php echo JText::_( 'LNG_NO_CHANGE_LOGS_FOUND', true ); ?>
		</th>
	</tr>
	</thead>
	<?php
			}?>
	</table>
</div>
 