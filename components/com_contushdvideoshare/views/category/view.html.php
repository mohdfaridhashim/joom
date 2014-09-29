<?php
/**
 * Category videos view file
 *
 * This file is to display Category videos
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
 * Category view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewcategory extends ContushdvideoshareView
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewcategory		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$model = $this->getModel();

		// Calling the function in models category.php
		$getcategoryview = $model->getcategory();
		$this->assignRef('categoryview', $getcategoryview);

		// Assigning reference for the category row/col settings
		$categorrowcol = $model->getcategoryrowcol();
		$this->assignRef('categoryrowcol', $categorrowcol);

		// Assigning reference for the category settings
		$getcategoryListVal = $model->getcategoryList();
		$this->assignRef('categoryList', $getcategoryListVal);

		// Assigning reference for the category settings
		$getplayersettings = $model->getplayersettings();
		$this->assignRef('player_values', $getplayersettings);

		// Assigning reference for the category video for the player
		$getcategoryid = $model->getcategoryid();
		$this->assignRef('getcategoryid', $getcategoryid);

		// Assigning reference for the category videos access level result
		$homeAccessLevel = $model->getHTMLVideoAccessLevel();
		$this->assignRef('homepageaccess', $homeAccessLevel);

		// Assigning reference for the Item id
		$Itemid = $model->getmenuitemid_thumb();
		$this->assignRef('Itemid', $Itemid);

		parent::display();
	}
}
