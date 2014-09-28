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

// Import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * Admin sortorder controller class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareControllersortorder extends ContusvideoshareController
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareControllersortorder		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view = $this->getView('sortorder');

		// Get/Create the model
		if ($model = $this->getModel('sortorder'))
		{
			$view->setModel($model, true);
		}

		$view->setLayout('sortorderlayout');
		$task = JRequest::getVar('task', 'get', '', 'string');

		if ($task == 'videos')
		{
			$view->videosortorder();
		}
		else if ($task == 'category')
		{
			$view->categorysortorder();
		}
	}
}
