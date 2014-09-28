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
// No direct acesss
defined('_JEXEC') or die('Restricted access');

// Import joomla model library
jimport('joomla.application.component.model');

/**
 * Admin ads model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareModeladdads extends ContushdvideoshareModel
{
	/**
	 * Function to add ads
	 * 
	 * @return  addvideos
	 */
	public function addadsmodel()
	{
		$rs_ads = JTable::getInstance('ads', 'Table');
		$add = array('rs_ads' => $rs_ads);

		return $add;
	}
}
