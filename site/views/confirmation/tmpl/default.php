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

defined('_JEXEC') or die('Restricted access'); 
$currency = JHotelUtil::getCurrencyDisplay($this->reservation->reservationData->userData->currency,null,null);

?>

<div class="hotel_reservation">
	<div class="hoteInnerContainer">

		<form action="<?php echo JRoute::_('index.php') ?>" method="post" name="userForm" >
			<div class="thank-you-holder">
				<?php
					$text=  JText::_('LNG_THANK_YOU_CONFIRMATION');
					$text = str_replace("<<hotelname>>", $this->reservation->reservationData->hotel->hotel_name, $text);
					$text = str_replace("<<e-mail adress>>", $this->reservation->reservationData->userData->email, $text);
					echo $text;
				?>
			</div>
			<?php echo $this->reservation->reservationInfo?>


			<table width="100%" cellspacing="0" class="left" >
				<tr class='rsv_dtls_subtotal' bgcolor="#FEFEFE">
					<td colspan=1 align="left">
						<?php echo TaxService::getCityTaxInfo($this->reservation->reservationData->hotel->informations,$currency); ?>
					</td>
				<?php 
				$parkingInfo = HotelService::getHotelParkingInfoStatus($this->reservation->reservationData->hotel,$currency);
				echo $parkingInfo;
				?>
			</table>
			<BR>
		</form>	
		<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post" name="userForm_new" >
			<div class="hotel_reservation">
				<button class="ui-hotel-button" type="submit">
					<span class="ui-button-text"><?php echo isset($this->reservation->reservationData->hotel->types) && $this->reservation->reservationData->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_BACK_TO_PARK'): JText::_('LNG_BACK')?></span>
				</button>
			</div>
			<input type="hidden" name="task" id="task" value="hotel.showHotel" />
			<input type="hidden" name="hotel_id" id="hotelId" value="<?php echo $this->reservation->reservationData->hotel->hotel_id ?>" />
			<input type="hidden" name="resetSearch" id="resetSearch" value="1" />
			<input type="hidden" name="init_hotel" id="init_hotel" value="1" />
		</form>
	</div>
</div>
<?php if($this->appSettings->enable_google_tag_manager){?>
<script>
<?php
$user = JFactory::getUser();
	if($user->get('isRoot')){  ?>
		dataLayer = [{'userTypeAnalytics' : 'internal'}];
<?php } elseif($user->id > 0 && !$user->get('isRoot')){ ?>
		dataLayer  = [{'userTypeAnalytics' : 'existing customer'}];
<?php } elseif($user->get('guest'))  { ?>
		dataLayer = [{'userTypeAnalytics' : 'new customer'}];
<?php }else { ?>
		dataLayer = [];
<?php }?>
</script>
<script>
	dataLayer.push({
		'ecommerce': {
			'purchase': {
				'actionField': {
					'id': '<?php echo $this->reservation->reservationData->userData->confirmation_id?>',
					'revenue': '<?php echo $this->reservation->total?>',
					'tax':'<?php echo $this->reservation->reservationData->hotel->informations->city_tax?>',
					'coupon': '<?php echo $this->reservation->reservationData->userData->voucher?>'
				},
				'products': [
					<?php
					foreach($this->reservation->rooms as $room):?>
						<?php if(isset($room->offer_name)):?>
						{
							'name': '<?php echo $room->offer_name."(".$room->room_name.")"; ?> | <?php echo $this->reservation->reservationData->hotel->hotel_name?>',
							'category': '<?php echo $this->reservation->reservationData->hotel->hotel_county?> / <?php echo $this->reservation->reservationData->hotel->hotel_city?>',
							'quantity': 1
						},
						<?php else: ?>
						{
							'name': '<?php echo $room->room_name ?> | <?php echo $this->reservation->reservationData->hotel->hotel_name?>',
							'category': '<?php echo $this->reservation->reservationData->hotel->hotel_county?> / <?php echo $this->reservation->reservationData->hotel->hotel_city?>',
							'quantity': 1
						},
						<?php endif;?>
					<?php endforeach;?>

					<?php foreach ($this->reservation->extraOptions as $extraOption):?>
						{
							'name': '<?php echo $extraOption->name; ?> | <?php echo $this->reservation->reservationData->hotel->hotel_name?>',
							'category': '<?php echo $this->reservation->reservationData->hotel->hotel_county?> / <?php echo $this->reservation->reservationData->hotel->hotel_city?>',
							'quantity': 1
						},
					<?php endforeach; ?>
				]
			}
		}
	});

</script>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?php echo $this->appSettings->google_tag_manager_id;?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $this->appSettings->google_tag_manager_id;?>');</script>
<!-- End Google Tag Manager -->

<?php } ?>

