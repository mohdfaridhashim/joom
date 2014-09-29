<?php
/**
 * Main Controller file for Contus HD Video Share
 *
 * @category   Apptha
 * @package    Com_Contushdvideoshare
 * @version    3.6
 * @author     Apptha Team <developers@contus.in>
 * @copyright  Copyright (C) 2014 Apptha. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JLoader::register('ContusvideoshareController', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/controller.php');
JLoader::register('ContushdvideoshareView', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/view.php');
JLoader::register('ContushdvideoshareModel', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/model.php');

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

require_once JPATH_COMPONENT . DS . 'controller.php';
$cache = JFactory::getCache('com_contusvideoshare');
$cache->clean();
date_default_timezone_set('UTC');

if (version_compare(JVERSION, '1.7.0', 'ge'))
{
	$version = '1.7';
}
elseif (version_compare(JVERSION, '1.6.0', 'ge'))
{
	$version = '1.6';
}
else
{
	$version = '1.5';
}

JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_contushdvideoshare' . DS . 'tables');
$controller = new contushdvideoshareController;
$controller->execute(JRequest::getVar('task'));
$controller->redirect();
