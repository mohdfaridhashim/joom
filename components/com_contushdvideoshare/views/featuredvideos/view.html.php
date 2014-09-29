<?php
/**
 * Featured videos view for HD Video Share
 *
 * This file is to display featured videos 
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
 * Featuredvideos view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewfeaturedvideos extends ContushdvideoshareView
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewfeaturedvideos		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$model = $this->getModel();

		// Function call for fetching featured videos
		$featuredvideos = $model->getfeaturedvideos();
		$this->assignRef('featuredvideos', $featuredvideos);

		// Function call for fetching featured videos settings
		$featurevideosrowcol = $model->getfeaturevideorowcol();
		$this->assignRef('featurevideosrowcol', $featurevideosrowcol);

		// Function call for fetching Itemid
		$Itemid = $model->getmenuitemid_thumb();
		$this->assignRef('Itemid', $Itemid);
		parent::display();
	}
}
