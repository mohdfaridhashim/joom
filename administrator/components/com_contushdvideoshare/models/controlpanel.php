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
 * Admin control panel model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareModelcontrolpanel extends ContushdvideoshareModel
{
	/**
	 * Function to show Top 5 popular videos,added videos etc.
	 * 
	 * @return  controlpaneldetails
	 */
	public function controlpaneldetails()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->clear()
				->select('COUNT(b.memberid) AS count, a.username AS username')
				->from('#__users a')
				->leftJoin('#__hdflv_upload b ON b.memberid = a.id')
				->group($db->escape('a.id'));
		$db->setQuery($query);
		$member_detail = $db->loadObjectList();

		// Query is to display the top 5 popular videos
		$query->clear()
				->select($db->quoteName(array('id','title','times_viewed')))
				->from($db->quoteName('#__hdflv_upload'))
				->where('published=1 and type=0')
				->order('times_viewed DESC');
		$db->setQuery($query, 5);
		$popularvideos = $db->LoadObjectList();

		// Query is to display the last 5 added videos
		$query->clear()
				->select($db->quoteName(array('id','title','created_date')))
				->from($db->quoteName('#__hdflv_upload'))
				->where('published=1 and type=0')
				->order('id DESC');
		$db->setQuery($query, 5);
		$latestvideos = $db->LoadObjectList();
		$count = array('membervideos' => $member_detail, 'popularvideos' => $popularvideos, 'latestvideos' => $latestvideos);

		return $count;
	}
}
