<?php
/**
 * User video view file
 *
 * This file is to display logged in user videos
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
 * Myvideos view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewmyvideos extends ContushdvideoshareView
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewmyvideos		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$user = JFactory::getUser();

		if ($user->get('id') == '')
		{
			$url = JURI::base() . "index.php?option=com_contushdvideoshare&view=player";
			header("Location: $url");
		}
		else
		{
			$model = $this->getModel();

			// Function call for fetching member videos
			$deletevideos = $model->getmembervideo();
			$this->assignRef('deletevideos', $deletevideos['rows']);
			$this->assignRef('allowupload', $deletevideos['row1']);

			// Function call for fetching Itemid
			$Itemid = $model->getmenuitemid_thumb();
			$this->assignRef('Itemid', $Itemid);

			// Function call for fetching my videos settings
			$myvideorowcol = $model->getmyvideorowcol();
			$this->assignRef('myvideorowcol', $myvideorowcol);
			parent::display();
		}
	}
}
