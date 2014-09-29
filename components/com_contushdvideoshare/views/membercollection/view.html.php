<?php
/**
 * View file to display member videos
 *
 * This file is to display member videos
 *
 * @category   Apptha
 * @package    Com_Contushdvideoshare
 * @version    3.6
 * @author     Apptha Team <developers@contus.in>
 * @copyright  Copyright (C) 2014 Apptha. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 */

// No direct acesss
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * Membercollection view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewmembercollection extends ContushdvideoshareView
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewmembercollection		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$model = $this->getModel();

		// Function call for fetching member videos
		$membercollection = $model->getmembercollection();
		$this->assignRef('membercollection', $membercollection);

		// Function call for fetching membercollection settings
		$memberpagerowcol = $model->getmemberpagerowcol();
		$this->assignRef('memberpagerowcol', $memberpagerowcol);

		// Function call for fetching Itemid
		$Itemid = $model->getmenuitemid_thumb();
		$this->assignRef('Itemid', $Itemid);
		parent::display();
	}
}
