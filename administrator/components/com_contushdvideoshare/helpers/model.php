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
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla model library
jimport('joomla.application.component.model');

if (version_compare(JVERSION, '3.0', 'ge'))
				{
	/**
	 * Admin model class.
	 *
	 * @package     Joomla.Contus_HD_Video_Share
	 * @subpackage  Com_Contushdvideoshare
	 * @since       1.5
	 */
	class ContushdvideoshareModel extends JModelLegacy
				{
	}
}
elseif (version_compare(JVERSION, '2.5', 'ge'))
				{
	/**
	 * Admin model class.
	 *
	 * @package     Joomla.Contus_HD_Video_Share
	 * @subpackage  Com_Contushdvideoshare
	 * @since       1.5
	 */
	class ContushdvideoshareModel extends JModel
				{
	}
}
elseif(version_compare(JVERSION, '1.6', 'ge') || version_compare(JVERSION, '1.7', 'ge'))
				{
	/**
	 * Admin model class.
	 *
	 * @package     Joomla.Contus_HD_Video_Share
	 * @subpackage  Com_Contushdvideoshare
	 * @since       1.5
	 */
	class ContushdvideoshareModel extends JModel
				{
	}
}
else
	{
	/**
	 * Admin model class.
	 *
	 * @package     Joomla.Contus_HD_Video_Share
	 * @subpackage  Com_Contushdvideoshare
	 * @since       1.5
	 */
	class ContushdvideoshareModel extends JModel
				{
	}
}
