<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldHotelStars extends JFormFieldList {

    protected $type = 'hoteltypes';

    // getLabel() left out

    /**
     * Method to get the custom field options.
     * Use the query attribute to supply a query to generate the list.
     *
     * @return  array  The field option objects.
     *
     * @since   11.1
     */
    protected function getOptions()
    {
        $options = array();
        $options[] = JHtml::_('select.option', "", "Hotel Stars");

	    $stars = 7;

	    for($i=0;$i<=$stars;$i++)
	    {
		    $options[] = JHtml::_( 'select.option', $i, $i );
	    }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}