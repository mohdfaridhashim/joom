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

// Import view libraries.
jimport('joomla.application.component.view');

if (version_compare(JVERSION, '3.0', 'ge'))
{
	/**
	 * Admin ContushdvideoshareView class.
	 *
	 * @package     Joomla.Contus_HD_Video_Share
	 * @subpackage  Com_Contushdvideoshare
	 * @since       1.5
	 */
	class ContushdvideoshareView extends JViewLegacy
				{
	}
}
else
{
	/**
	 * Admin ContushdvideoshareView class.
	 *
	 * @package     Joomla.Contus_HD_Video_Share
	 * @subpackage  Com_Contushdvideoshare
	 * @since       1.5
	 */
	class ContushdvideoshareView extends JView
				{
	}
}
