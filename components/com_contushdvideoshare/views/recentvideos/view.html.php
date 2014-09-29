<?php
/**
 * Recent videos view file
 *
 * This file is to display Recent videos detail
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
 * Recent Videos Module View file
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewrecentvideos extends ContushdvideoshareView
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewrecentvideos		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function  display($cachable = false, $urlparams = false)
	{
		$model = $this->getModel();

		// Function call for fetching recent videos
		$recentvideos = $model->getrecentvideos();
		$this->assignRef('recentvideos', $recentvideos);

		// Function call for fetching Itemid
		$Itemid = $model->getmenuitemid_thumb();
		$this->assignRef('Itemid', $Itemid);

		// Function call for fetching recent videos settings
		$recentvideosrowcol = $model->getrecentvideosrowcol();
		$this->assignRef('recentvideosrowcol', $recentvideosrowcol);
		parent::display();
	}
}
