<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldHotelCities extends JFormFieldList {

    protected $type = 'hotelcities';

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
        $options[] = JHtml::_('select.option', "", "All Cities");

        // Initialize some field attributes.
        $key = "id";
        $value = "name";
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
        $query = ' SELECT distinct hotel_city FROM #__hotelreservation_hotels where is_available = 1 and hotel_city!="" order by hotel_city asc';

        // Get the database object.
        $db = JFactory::getDBO();

        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadObjectlist();

        // Build the field options.
        if (!empty($items))
        {
            foreach ($items as $item)
            {
                if ($translate == true)
                {
                    $options[] = JHtml::_('select.option', $item->hotel_city, $item->hotel_city);
                }
                else
                {
                    $options[] = JHtml::_('select.option', $item->hotel_city, $item->hotel_city);
                }
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}