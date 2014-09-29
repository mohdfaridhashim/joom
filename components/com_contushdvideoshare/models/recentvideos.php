<?php
/**
 * Recent videos model file
 *
 * This file is to fetch Recent videos detail from database
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
 * Recent videos model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Modelcontushdvideosharerecentvideos extends ContushdvideoshareModel
{
	/**
	 * Function to display a Recent videos
	 * 
	 * @return  array
	 */
	public function getrecentvideos()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		// Query is to get the pagination for recent videos
		$query->clear()
				->select('a.id')
				->from('#__hdflv_upload AS a')
				->leftJoin('#__users AS d ON a.memberid=d.id')
				->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
				->leftJoin('#__hdflv_category AS b ON e.catid=b.id')
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
				->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
				->group($db->escape('e.vid'))
				->order($db->escape('e.vid' . ' ' . 'DESC'));
		$db->setQuery($query);
		$total_query = $db->LoadObjectList();
		$total = count($total_query);
		$pageno = 1;

		if (JRequest::getVar('video_pageid', '', 'post', 'int'))
		{
			$pageno = JRequest::getVar('video_pageid', '', 'post', 'int');
		}

		$limitrow = $this->getrecentvideosrowcol();
		$thumbview = unserialize($limitrow[0]->thumbview);
		$length = $thumbview['recentrow'] * $thumbview['recentcol'];
		$pages = ceil($total / $length);

		if ($pageno == 1)
		{
			$start = 0;
		}
		else
		{
			$start = ($pageno - 1) * $length;
		}

		// Query is to display the recent videos
		$query->clear()
				->select(
						array(
							'a.id', 'a.filepath', 'a.thumburl', 'a.title', 'a.description', 'a.times_viewed',
							'a.ratecount', 'a.rate',
							'a.amazons3', 'a.seotitle',
							'b.category', 'b.seo_category', 'd.username', 'e.catid', 'e.vid'
						)
				)
				->from('#__hdflv_upload AS a')
				->leftJoin('#__users AS d ON a.memberid=d.id')
				->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
				->leftJoin('#__hdflv_category AS b ON e.catid=b.id')
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
				->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
				->group($db->escape('e.vid'))
				->order($db->escape('e.vid' . ' ' . 'DESC'));
		$db->setQuery($query, $start, $length);
		$rows = $db->LoadObjectList();

		if (count($rows) > 0)
		{
			$rows['pageno'] = $pageno;
			$rows['pages'] = $pages;
			$rows['start'] = $start;
			$rows['length'] = $length;
		}

		return $rows;
	}

	/**
	 * Function to get itemid of the video share menu
	 * 
	 * @return  int
	 */
	public static function getmenuitemid_thumb()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Query is to get itemid of the video share menu
		$query->select('id')
				->from('#__menu')
				->where($db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_contushdvideoshare&view=player') . ' AND ' . $db->quoteName('published') . ' = ' . $db->quote('1'))
				->order('id DESC');
		$db->setQuery($query);
		$Itemid = $db->loadResult();

		return $Itemid;
	}

	/**
	 * Function to get thumb settings
	 * 
	 * @return  array
	 */
	public function getrecentvideosrowcol()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		// Query is to select the recent videos row
		$query->select(array('thumbview', 'dispenable'))
				->from('#__hdflv_site_settings');
		$db->setQuery($query);
		$rows = $db->LoadObjectList();

		return $rows;
	}
}
