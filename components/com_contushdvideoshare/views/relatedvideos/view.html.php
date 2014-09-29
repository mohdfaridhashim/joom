<?php
/**
 * Related videos view file
 *
 * This file is to display Related videos detail
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

// Import Joomla component library
jimport('joomla.application.component.view');

/**
 * Relatedvideos view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewrelatedvideos extends ContushdvideoshareView
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewrelatedvideos		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function  display($cachable = false, $urlparams = false)
	{
		$model = $this->getModel();
		$relatedvideos = $model->getrelatedvideos();
		$this->assignRef('relatedvideos', $relatedvideos);
		$relatedvideosrowcol = $model->getrelatedvideosrowcol();
		$this->assignRef('relatedvideosrowcol', $relatedvideosrowcol);

		// Function call for fetching Itemid
		$Itemid = $model->getmenuitemid_thumb();
		$this->assignRef('Itemid', $Itemid);
		parent::display();
	}
}
