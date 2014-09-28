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
// No direct access
defined('_JEXEC') or die('Restricted access');

// Import joomla view library
jimport('joomla.application.component.view');

/**
 * Sortorder view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewsortorder extends ContushdvideoshareView
{
	/**
	 * Function for category sorting
	 * 
	 * @return  categorysortorder
	 */
	public function categorysortorder()
	{
		$model = $this->getModel();
		$sortorder = $model->categorysortordermodel();
	}

	/**
	 * Function for video sorting
	 * 
	 * @return  videosortorder
	 */
	public function videosortorder()
	{
		$model = $this->getModel();
		$sortorder = $model->videosortordermodel();
	}
}
