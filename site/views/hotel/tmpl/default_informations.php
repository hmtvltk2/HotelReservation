<?php
/**
 * @copyright	Copyright (C) 2009-2012 CMSJunkie - All rights reserved.
 */
defined('_JEXEC') or die('Restricted access');
$free = JText::_('LNG_FREE',true);
?>

<div class="hotel-box hotel-item informations">
	<h2><?php echo $this->hotel->infoTranslation ?></h2>
	<div class="left">
		<h4>
			<i class="fa fa-arrow-circle-right" ></i>&nbsp;<?php echo JText::_('LNG_CHECK_IN_HOTEL'); ?>
		</h4>
		<p>
			<?php echo JText::_('LNG_AFTER',true); ?>&nbsp;<?php echo $this->hotel->informations->check_in  ?>&nbsp;<?php echo JText::_('LNG_HOURS',true); ?>
		</p>
		<h4>
            <i class="fa fa-arrow-circle-left" ></i>&nbsp;<?php echo JText::_('LNG_CHECK_OUT_HOTEL'); ?>
		</h4>
		<p>
			<?php echo JText::_('LNG_BEFORE',true); ?>&nbsp;<?php echo $this->hotel->informations->check_out ?>&nbsp;<?php echo JText::_('LNG_HOURS',true); ?>
		</p>
		<h4>
            <i class="fa fa-car" ></i>&nbsp;<?php echo JText::_('LNG_PARKING',true); ?>
		</h4>
		<p>
			<?php echo $this->hotel->informations->parking==0?JText::_('LNG_NO_PARKING',true):JText::_('LNG_PARKING_AVAILABLE',true); ?>
			
			<?php
				$price =  $this->hotel->informations->price_parking>0? $this->hotel->currency_symbol.' '.JHotelUtil::fmt($this->hotel->informations->price_parking,2):$free;
				echo ($this->hotel->informations->parking!=0 )?(empty($price)?"":"").' '.$price :'';
				if(isset($this->hotel->informations->parking_period) && $this->hotel->informations->parking!=0){
					echo  ' '.$this->hotel->informations->parking_period;
				}
			?>
		</p>
		<h4>
            <i class="fa fa-paw" ></i>&nbsp;<?php echo JText::_('LNG_PETS',true); ?>
		</h4>
		<p>
			<?php echo $this->hotel->informations->pets==0?JText::_('LNG_NO_PETS',true):JText::_('LNG_PETS_ALLOWED',true); ?>
			 
			<?php 
				$price =  $this->hotel->informations->price_pets>0? $this->hotel->currency_symbol.' '.JHotelUtil::fmt($this->hotel->informations->price_pets,2):$free;
				echo ( $this->hotel->informations->pets!=0)?(empty($price)?"":"").' '.$price.' '.(empty($this->hotel->informations->pet_info)?JText::_('LNG_PER_NIGHT',true):""):'';
				echo  $this->hotel->informations->pets != 0?' '.$this->hotel->informations->pet_info:'';
			?>
		</p>
		<h4>
            <i class="fa fa-money"></i>&nbsp;<?php echo JText::_('LNG_CITY_TAX',true); ?>
		</h4>
		<p>
			<?php if($this->hotel->informations->city_tax_percent == 1){
				echo$this->hotel->informations->city_tax.'%';
			}else{
				 echo $this->hotel->currency_symbol.' '.number_format($this->hotel->informations->city_tax,2).' p.p.p.n';
			}
			 ?>
		</p>
		<h4>
            <i class="fa fa-bed" ></i>&nbsp;<?php echo $this->hotel->nrRoomsTranslation; ?>
		</h4>
		<p>
			<?php echo $this->hotel->informations->number_of_rooms ?>
		</p>	
	</div>
	<div class="right">
		
		<h4>
            <i class="fa fa-exclamation-triangle" ></i>&nbsp;<?php echo JText::_('LNG_CANCELATION_CONDITIONS',true); ?>
		</h4>
		<p>
			<?php echo $this->hotel->informations->cancellation_conditions; ?>
		</p>
		
		<h4>
            <i class="fa fa-wifi" ></i>&nbsp;<?php echo JText::_('LNG_INTERNET_WIFI',true); ?>
		</h4>
		<p>
			<?php echo $this->hotel->informations->wifi==0?JText::_('LNG_NO_WIFI',true):JText::_('LNG_WIFI_AVAILABLE',true); ?>
			
			<?php
				$price =  $this->hotel->informations->price_wifi>0? $this->hotel->currency_symbol.' '.JHotelUtil::fmt($this->hotel->informations->price_wifi,2):$free;
				echo ($this->hotel->informations->wifi!=0 )?(empty($price)?"":"").' '.$price:'';
				if($this->hotel->informations->wifi!=0 && isset($this->hotel->informations->wifi_period)){
					echo  ' '.$this->hotel->informations->wifi_period;
				}
			?>
		</p>
		<h4>
            <i class="fa fa-wheelchair" ></i>&nbsp;<?php echo JText::_('LNG_SUITABLE_FOR_DISABLED',true); ?>
		</h4>
		<p>
			<?php echo  $this->hotel->informations->suitable_disabled==0?$this->hotel->suitableDisabledTranslation:$this->hotel->suitableDisabledAvailableTranslation; ?>
		</p>
		<h4>
            <i class="fa fa-subway" ></i>&nbsp;<?php echo JText::_('LNG_PUBLIC_TRANSPORTATION',true); ?>
		</h4>
		<p>
			<?php echo $this->hotel->informations->public_transport==0?JText::_('LNG_NO_PUBLIC_TRANPORTATION',true):JText::_('LNG_PUBLIC_TRANSPORTATION_AVAILABLE',true); ?>
		</p>
		<h4>
            <i class="fa fa-credit-card" ></i>&nbsp;<?php echo $this->hotel->paymentOptionsTranslation ?>
		</h4>
		<p>
			<?php 
				$paymentOptions ='';
            if(isset($this->hotel->paymentOptions)) {
                foreach ($this->hotel->paymentOptions as $idx=>$po) {
                    $translationValue = JText::_('LNG_'.strtoupper(str_replace(" ","_",$po->name)));
                    echo  $translationValue;
                    echo  $idx==count($this->hotel->paymentOptions)-1?"": ", ";
                }
            }
			?>
		</p>
		<h4>
            <i class="fa fa-child"></i>&nbsp;<?php echo JText::_('LNG_CHILDREN_AGE_CATEGORY',true); ?>
		</h4>
		<p>
            <?php
            echo $this->hotel->informations->children_category;
            ?>
		</p>
	</div>
	<div class="clear"></div>
</div>
