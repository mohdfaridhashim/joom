<?php
/**
 * RSS Feed view file
 *
 * This file is to display RSS Feed
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

// Import Joomla component library
jimport('joomla.application.component.view');

/**
 * RSS view file
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewrss extends ContushdvideoshareView
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewrss		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function  display($cachable = false, $urlparams = false)
	{
		$model = & $this->getModel();
		$detail = $model->playgetrecords();
	}
}
