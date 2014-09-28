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
date_default_timezone_set('UTC');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_contushdvideoshare')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Initialize path for video upload
$componentPath = JPATH_COMPONENT;

JLoader::register('ContusvideoshareController', JPATH_COMPONENT . '/helpers/controller.php');
JLoader::register('ContushdvideoshareView', JPATH_COMPONENT . '/helpers/view.php');
JLoader::register('ContushdvideoshareModel', JPATH_COMPONENT . '/helpers/model.php');

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

$sitePath = str_replace(DS . 'administrator', '', $componentPath);
$videoPath = $sitePath . DS . 'videos';

// Get the video path
define('VPATH', $videoPath);

// Get current directory
define('FVPATH', $componentPath);

$controllerName = JRequest::getCmd('layout', 'controlpanel');

if ($controllerName == 'categorylist')
{
	$controllerName = 'category';
}

// Initialize to false and change to true according to current menu.
$category_active = $memberdetails_active = $adminvideos_active = $membervideos_active = false;
$sitesettings_active = $settings_active = $sortorder_active = $googlead_active = $ads_active = false;

switch ($controllerName)
{
	default:
		$controllerName = 'controlpanel';
	case "category":
		$category_active = true;
		break;
	case "memberdetails":
		$memberdetails_active = true;
		break;
	case "adminvideos":
		if (JRequest::getCmd('user', '', 'get') == 'admin')
		{
			$adminvideos_active = true;
		}
		else
		{
			$membervideos_active = true;
		}
		break;
	case "sitesettings":
		$sitesettings_active = true;
		break;
	case "settings":
		$settings_active = true;
		break;
	case "sortorder":
		$sortorder_active = true;
		break;
	case "googlead":
		$googlead_active = true;
		break;
	case "ads":
		$ads_active = true;
		break;
}

// Adding menus
JSubMenuHelper::addEntry(JText::_('Member Videos'), 'index.php?option=com_contushdvideoshare&layout=adminvideos', $membervideos_active);
JSubMenuHelper::addEntry(JText::_('Member Details'), 'index.php?option=com_contushdvideoshare&layout=memberdetails', $memberdetails_active);
JSubMenuHelper::addEntry(JText::_('Admin Videos'), 'index.php?option=com_contushdvideoshare&layout=adminvideos&user=admin', $adminvideos_active);
JSubMenuHelper::addEntry(JText::_('Category'), 'index.php?option=com_contushdvideoshare&layout=category', $category_active);
JSubMenuHelper::addEntry(JText::_('Player Settings'), 'index.php?option=com_contushdvideoshare&layout=settings', $settings_active);
JSubMenuHelper::addEntry(JText::_('Site Settings'), 'index.php?option=com_contushdvideoshare&layout=sitesettings', $sitesettings_active);
JSubMenuHelper::addEntry(JText::_('Google AdSense'), 'index.php?option=com_contushdvideoshare&layout=googlead', $googlead_active);
JSubMenuHelper::addEntry(JText::_('Video Ads '), 'index.php?option=com_contushdvideoshare&layout=ads', $ads_active);

require_once JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = 'ContushdvideoshareController' . $controllerName;

// Create the controller
$controller = new $controllerName;

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
