<?php
/**
 * @name       Joomla HD Video Share
 * @SVN        3.5.1
 * @package    Com_Contushdvideoshare
 * @author     Apptha <assist@apptha.com>
 * @copyright  Copyright (C) 2014 Powered by Apptha
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @since      Joomla 1.5
 * @Creation Date   March 2010
 * @Modified Date   March 2014
 * */
// No direct access.
defined('_JEXEC') or die;

/**
 * Admin HdvideoshareFilterHelper class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class HdvideoshareFilterHelper
{
	/**
	 * Function to get list of option for status 
	 * 
	 * @return  getStatusOptions
	 */
	public function getStatusOptions()
	{
		$options = array('1' => 'Enable',
			'2' => 'Disable'
		);

		return $options;
	}

	/**
	 * Function to get list of option for Featured 
	 * 
	 * @return  getFeaturedOptions
	 */
	public function getFeaturedOptions()
	{
		$options = array('1' => 'Featured',
			'2' => 'Unfeatured'
		);

		return $options;
	}

	/**
	 * Get a list of filter options for ad types
	 *
	 * @return	getAdTypes
	 */
	public function getAdTypes()
	{
		$types = array('mid' => 'Mid Roll Ad',
			'prepost' => 'Pre/Post Roll Ad'
		);

		return $types;
	}
}
