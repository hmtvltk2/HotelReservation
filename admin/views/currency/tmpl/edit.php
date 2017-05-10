<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');


?>
<form action="<?php echo JRoute::_('index.php?option='.getBookingExtName().'&layout=edit&id=' . (int) $this->item->currency_id); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset>
        <legend><?php echo JText::_('LNG_CURRENCY_DETAILS',true); ?></legend>
        <center>
            <table class="admintable" align=center border=0>
                <tr>
                    <td width=10% nowrap class="key"><?php echo JText::_('LNG_NAME',true); ?> :</td>
                    <td nowrap width=1% align=left>
                        <select
                            name = 'description'
                            id	 = 'description'
                            style= 'width:250px'

                            >
                            <?php
                            foreach( $this->item->countries as $country )
                            {
                                ?>
                                <option
                                    value='<?php echo $country->country_currency_short ?>'

                                    <?php echo addslashes($country->country_currency_short) == addslashes($this->item->description) ? ' selected ' : ''?>
                                    >
                                    <?php echo $country->country_name.' | '.$country->country_currency_short.' | '.$country->country_currency?>
                                </option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td width=10% nowrap class="key"><?php echo JText::_('LNG_SYMBOL',true); ?> :</td>
                    <td>
                        <input
                            type		= "text"
                            name		= "currency_symbol"
                            id			= "currency_symbol"
                            value		= '<?php echo $this->item->currency_symbol?>'
                            size		= 32
                            maxlength	= 128
                            />
                    </td>
                </tr>
            </table>
    </fieldset>

    <input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
    <input type="hidden" name="task" value="currency.edit" />
    <input type="hidden" name="currency_id" value="<?php echo $this->item->currency_id ?>" />
    <input type="hidden" name="controller" value="currency" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>