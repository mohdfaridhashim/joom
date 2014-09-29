<?php
/**
 * Playerbase model file
 *
 * This file is to display player home page
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

// Import Joomla model library
jimport('joomla.application.component.model');

/**
 * Playerbase model class
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Modelcontushdvideoshareplayerbase extends ContushdvideoshareModel
{
	/**
	 * Function to get player skin
	 * 
	 * @return  void
	 */
	public function playerskin()
	{
		$playerpath = JURI::base() . 'components/com_contushdvideoshare/hdflvplayer/hdplayer.swf';
		$this->showplayer($playerpath);
	}

	/**
	 * Function to show player
	 * 
	 * @param   string  $playerpath  player skin path
	 * 
	 * @return  void
	 */
	public function showplayer($playerpath)
	{
		ob_clean();
		header("content-type:application/x-shockwave-flash");
		readfile($playerpath);
		exit();
	}
}
