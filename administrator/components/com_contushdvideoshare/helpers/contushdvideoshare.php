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
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Admin ContushdvideoshareHelper class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareHelper
{
	/**
	 * Function to getActions 
	 * 
	 * @param   int  $messageId  message id
	 * 
	 * @return  getActions
	 */
	public static function getActions($messageId = 0)
	{
		jimport('joomla.access.access');
		$user = JFactory::getUser();
		$result = new JObject;

		if (empty($messageId))
			{
			$assetName = 'com_contushdvideoshare';
		}
		else
			{
			$assetName = 'com_contushdvideoshare.message.' . (int) $messageId;
		}

		$actions = JAccess::getActions('com_contushdvideoshare', 'component');

		foreach ($actions as $action)
			{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}
}
