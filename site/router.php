<?php
/**
# JBusinessDirectory
# author CMSJunkie
# copyright Copyright (C) 2014 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/j-businessdirectory/?p=1
*/

defined('_JEXEC') or die;
/**
 * Routing class from com_jhotelreservation
 *
 * @since  3.3
 */
class JHotelReservationRouter extends JComponentRouterBase {

	public function build( &$query ) {
		$segments = array();
		$params = JComponentHelper::getParams('com_jhotelreservation');

		if (isset($query['view'])) {
			$segments[] = $query['view'];
			unset($query['view']);
		}
		else
		{
			// We need to have a view in the query or it is an invalid URL
			return $segments;
		}
		if (isset($query['offerId'])) {
			$segments[] = $query['offerId'];
			unset($query['offerId']);
		}

		if (isset($query['hotel_id'])) {
			$segments[] = $query['hotel_id'];
			unset($query['hotel_id']);
		}
		//excursion
		if (isset($query['id'])) {
			$segments[] = $query['id'];
			unset($query['id']);
		}
		return $segments;
	}

	public function parse( &$segments ) {
		$vars = array();
		$count = count($segments);
		$vars['view'] = $segments[0];

		switch($vars['view']){
			
			case "hotel":
				$vars['hotel_id'] = $segments[$count - 1];
				break;
			case "offer":
				$vars['offerId'] = $segments[$count - 1];
				break;
		}

		return $vars;
	}

}

/**
 * JHotelReservation router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @param   array  &$query  An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function JHotelReservationBuildRoute(&$query)
{
	$router = new JHotelReservationRouter();

	return $router->build($query);
}

/**
 * Parse the segments of a URL.
 *
 * This function is a proxy for the new router interface
 * for old SEF extensions.
 *
 * @param   array  $segments  The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @since   3.3
 * @deprecated  4.0  Use Class based routers instead
 */
function JHotelReservationParseRoute($segments)
{
	$router = new JHotelReservationRouter;

	return $router->parse($segments);
}
