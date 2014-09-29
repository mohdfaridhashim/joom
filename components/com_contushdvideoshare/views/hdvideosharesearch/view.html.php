<?php
/**
 * Search view for HD Video Share
 *
 * This file is to display videos based on search keyword 
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
 * HD Video Share Search view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewhdvideosharesearch extends ContushdvideoshareView
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewhdvideosharesearch		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$model = $this->getModel();

		// Function call for fetching search results
		$search = $model->getsearch();
		$this->assignRef('search', $search);

		// Function call for fetching my videos settings
		$searchrowcol = $model->getsearchrowcol();
		$this->assignRef('searchrowcol', $searchrowcol);

		// Function call for fetching Itemid
		$Itemid = $model->getmenuitemid_thumb();
		$this->assignRef('Itemid', $Itemid);
		parent::display();
	}
}
