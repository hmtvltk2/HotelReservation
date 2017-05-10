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

if( JRequest::getString( 'task') !='edit' && JRequest::getString( 'task') !='add' )
{
?>
<form action="index.php" method="post" name="adminForm">
	<div id="editcell">
				<div style='text-align:left'>
					<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
					<select name='hotel_id' id='hotel_id' style='width:300px'
						onchange ='
									var form 	= document.adminForm; 
									var obView	= document.createElement("input");
									obView.type = "hidden";
									obView.name	= "view";
									obView.value= "paymentsettings";
									form.appendChild(obView);
									form.submit();
									'
					>
						<option value=0 <?php echo $this->hotel_id ==0? 'selected' : ''?>><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
						<?php
						foreach($this->hotels as $hotel )
						{
						?>
						<option value='<?php echo $hotel->hotel_id?>' 
							<?php echo $this->hotel_id ==$hotel->hotel_id||(count($this->hotels)==1)? 'selected' : ''?>
						>
							<?php 
								echo stripslashes($hotel->hotel_name);
								echo (strlen($hotel->country_name)>0? ", ".$hotel->country_name : "");
								echo stripslashes(strlen($hotel->hotel_city)>0? ", ".$hotel->hotel_city : "");
							?>
						</option>
						<?php
						}
						?>
					</select>
					<hr>
				</div>
				<?php
				if( $this->hotel_id > 0  )
				{
				?>
					<TABLE class="adminlist" >
						<thead>
							<th width='1%'>#</th>
							<th width='1%'  align=center>&nbsp;</th>
							<th width='20%' align=center><B><?php echo JText::_('LNG_TYPE',true); ?></B></th>
							<th width='20%' align=center><B><?php echo JText::_('LNG_NAME',true); ?></B></th>
							<th width='20%' align=center><B><?php echo JText::_('LNG_DAYS',true); ?></B></th>
							<th width='20%' align=center><B><?php echo JText::_('LNG_PERCENT',true); ?></B></th>
							<th width='20%' align=center><B><?php echo JText::_('LNG_VALUE',true); ?></B></th>
							<th width='1%' align=center><B><?php echo JText::_('LNG_AVAILABLE',true); ?></B></th>
							<th width='5%' align=center><B><?php echo JText::_('LNG_ORDER',true); ?></B></th>
						</thead>
						<tbody>

						<?php
						$nrcrt = 1;
						//if(0)
						for($i = 0; $i <  count( $this->items ); $i++)
						{
							$payment = $this->items[$i]; 
							

						?>
					<TR class="row<?php echo $i%2?>"
							onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	=	"this.style.cursor='default'"
						>
							<TD align=center><?php echo $nrcrt++?></TD>
							<TD align=center>
								 <input type="radio" name="boxchecked"  id="boxchecked" value="<?php echo $payment->payment_id?>" 
									onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	=	"this.style.cursor='default'"
									onclick="
												adminForm.payment_id.value = '<?php echo $payment->payment_id?>'
											" 
								/>
							</TD>
							<TD align=left>
								<B><?php echo $payment->payment_type_name?></B>
							</TD>
							<TD align=left>
								
								<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=paymentsettings&view=paymentsettings&task=edit&payment_id[]='. $payment->payment_id.'&hotel_id='.$this->hotel_id )?>'
									title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"
								>
									<B><?php echo $payment->payment_name?></B>
								</a>	
								
							</TD>
							<TD align=center><?php echo $payment->payment_days!=0? ($payment->is_check_days==true? "" : "<s>").$payment->payment_days.($payment->is_check_days? "" : "</s>") : "&nbsp;"?></TD>
							<TD align=center><?php echo $payment->payment_percent!=0? $payment->payment_percent." %" : "&nbsp;"?></TD>
							<TD align=center><?php echo $payment->payment_value!=0? JHotelUtil::fmt($payment->payment_value,2) : "&nbsp;"?></TD>
							<TD align=center>
								<img border= 1 
									src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($payment->is_available==false? "unchecked.gif" : "checked.gif")?>" 
									onclick	=	"	
													document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=paymentsettings.state&payment_id[]='. $payment->payment_id.'&hotel_id='.$this->hotel_id )?> '
												"
								/>
								
							</TD>
							<TD align=center><?php echo $payment->payment_order?></TD>
						</TR>
						<?php
						}
						?>
						</tbody>
					</TABLE>
				<?php
				}
				?>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="payment_id" value="" />
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	<input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	<script language="javascript" type="text/javascript">
		<?php
		if( JHotelUtil::getCurrentJoomlaVersion() < 1.6 )
		{
		?>
		function submitbutton(pressbutton) 
		<?php
		}
		else
		{
		?>
		Joomla.submitbutton = function(pressbutton) 
		<?php
		}
		?>
		{
			var form = document.adminForm;
			if (pressbutton == 'edit' || pressbutton == 'Delete') 
			{
				var isSel = false;
				if( form.elements['boxchecked'].length == null )
				{
					if(form.elements['boxchecked'].checked)
					{
						isSel = true;
					}
				}
				else
				{
					for( i = 0; i < form.boxchecked.length; i ++ )
					{
						if(form.elements['boxchecked'][i].checked)
						{
							isSel = true;
							break;
						}
					}
				}
				
				if( isSel == false )
				{
					alert('<?php echo JText::_('LNG_YOU_MUST_SELECT_ONE_RECORD',true)?>');
					return false;
				}
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
		jQuery(document).ready(function()
				{
					var hotelId=jQuery('#hotel_id').val();
					var refreshScreen=jQuery('#refreshScreen').val();
					var nrHotels = jQuery('#hotel_id option').length;
					if(hotelId>0 && refreshScreen=="" && parseInt(nrHotels)==2){
						jQuery('#refreshScreen').val("true");
						jQuery("#hotel_id").trigger('change');	
					}
				});	
	</script>
</form>
<?php
}
else
{
?>
<form action="index.php" method="post" name="adminForm">
	<fieldset>
		<legend><?php echo JText::_('LNG_FEE_DETAILS',true); ?></legend>
		<center>
		<div style='text-align:left'>
			<strong>
				<?php echo JText::_('LNG_HOTEL',true)?> : 
				<?php 
					echo $this->hotel->hotel_name;
					echo (strlen($this->hotel->country_name)>0? ", ".$this->hotel->country_name : "");
					echo (strlen($this->hotel->hotel_city)>0? ", ".$this->hotel->hotel_city : "");
				?>
			</strong>
			<hr>
		</div>
		<TABLE class="admintable" align=center border=0 width=100%>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_TYPE',true); ?> :</TD>
				<TD nowrap width=90% align=left>
					<select
						name		= "payment_type_id"
						id			= "payment_type_id"
					>
						<option value='0'>
							 
						</option>
						<?php
						foreach( $this->item->payments as $payment )
						{
						?>
						<option <?php echo $this->item->payment_type_id == $payment->payment_type_id? " selected " : "" ?>
							value='<?php echo $payment->payment_type_id?>'
						>
							<?php echo $payment->payment_type_name?>
						</option>
						<?php
						}
						?>
					</select>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_NAME',true); ?> :</TD>
				<TD nowrap width=90% align=left>
					<input 
						type		= "text"
						name		= "payment_name"
						id			= "payment_name"
						value		= '<?php echo $this->item->payment_name?>'
						size		= 32
						maxlength	= 128
						AUTOCOMPLETE= OFF
						
					/>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_PERCENT',true); ?> :</TD>
				<TD nowrap align=left>
					<input 
						type		= "text"
						name		= "payment_percent"
						id			= "payment_percent"
						value		= '<?php echo $this->item->payment_percent?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:center'
					/>
					<B>%</B>
				</TD>
			</TR>
			<TR>
				<TD width=10%  class="key" nowrap><?php echo JText::_('LNG_VALUE',true); ?> :</TD>
				<TD nowrap align=left >
					<input 
						type		= "text"
						name		= "payment_value"
						id			= "payment_value"
						value		= '<?php echo $this->item->payment_value?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:right'
						
					/>
					
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_DAYS',true); ?> :</TD>
				<TD nowrap align=left>
					<input 
						type		= "text"
						name		= "payment_days"
						id			= "payment_days"
						value		= '<?php echo $this->item->payment_days?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:center'

					/>
					&nbsp;
					<input 
						type		= "checkbox"
						name		= "is_check_days"
						id			= "is_check_days"
						value		= '1'
						<?php echo $this->item->is_check_days? " checked " : "" ?>
						
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"

					/>
					<?php echo JText::_('LNG_IS_DAYS_CHECKED',true) ?>
				</TD>
			</TR>
			<TR>
				<TD width=20% nowrap class="key"><?php echo JText::_('LNG_ORDER',true); ?> :</TD>
				<TD nowrap align=left>
					<input 
						type		= "text"
						name		= "payment_order"
						id			= "payment_order"
						value		= '<?php echo $this->item->payment_order?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:center'
					/>
					
				</TD>
			</TR>
		</TABLE>
	</fieldset>
	<script language="javascript" type="text/javascript">
		<?php
		if( JHotelUtil::getCurrentJoomlaVersion() < 1.6 )
		{
		?>
		function submitbutton(pressbutton) 
		<?php
		}
		else
		{
		?>
		Joomla.submitbutton = function(pressbutton) 
		<?php
		}
		?>
		{
			var form = document.adminForm;
			if (pressbutton == 'save') 
			{
				if( !validateField( form.elements['payment_type_id'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_PAYMENT_NAME',true); ?>" ) )
					return false;
				if( !validateField( form.elements['payment_percent'], 'numeric', true, "<?php echo JText::_('LNG_PLEASE_INSERT_PAYMENT_PERCENT',true); ?>" ) )
					return false;
				if( !validateField( form.elements['payment_value'], 'numeric', true,  "<?php echo JText::_('LNG_PLEASE_INSERT_PAYMENT_VALUE',true); ?>" ) )
					return false;
				if( !validateField( form.elements['payment_days'], 'numeric',  form.elements['is_check_days'].checked==false ,  "<?php echo JText::_('LNG_PLEASE_INSERT_PAYMENT_DAYS',true); ?>"  ) )
					return false;
				if( !validateField( form.elements['payment_order'], 'numeric', true, "<?php echo JText::_('LNG_PLEASE_INSERT_PAYMENT_ORDER',true); ?>"  ) )
					return false;
			
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
	</script>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="hotel_id" value="<?php echo $this->hotel_id ?>" />
	<input type="hidden" name="payment_id" value="<?php echo $this->item->payment_id ?>" />
	<input type="hidden" name="controller" value="paymentsettings>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<?php
}
?>

