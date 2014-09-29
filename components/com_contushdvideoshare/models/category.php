<?php
/**
 * Category model for HD Video Share
 *
 * This file is to fetch category details from database for category view 
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
 * Category videos model class
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Modelcontushdvideosharecategory extends ContushdvideoshareModel
{
	/**
	 * Function to display the video results of related category
	 * 
	 * @return  array
	 */
	public function getcategory()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);
		$flatCatid = is_numeric(JRequest::getString('category'));

		if (JRequest::getString('category') && $flatCatid != 1)
		{
			$catvalue = str_replace(':', '-', JRequest::getString('category'));
			$query->select('id')
				->from('#__hdflv_category')
				->where($db->quoteName('seo_category') . ' = ' . $db->quote($catvalue));
			$db->setQuery($query);
			$catid = $db->loadResult();
		}
		elseif ($flatCatid == 1)
		{
			$catid = JRequest::getString('category');
		}
		elseif (JRequest::getInt('catid'))
		{
			$catid = JRequest::getInt('catid');
		}
		else
		{
			// This query is for category view pagination
			$query->select('id')
				->from('#__hdflv_category')
				->where($db->quoteName('published') . ' = ' . $db->quote('1'))
				->order($db->escape('category ASC'));
			$db->setQuery($query);
			$searchtotal1 = $db->loadObjectList();

			// Category id is stored in this catid variable
			$catid = $searchtotal1[0]->id;
		}

		if (!version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$catid = $db->getEscaped($catid);
		}

		// Query to calculate total number of videos in paricular category
		$query->clear()
				->select('a.id')
				->from('#__hdflv_upload AS a')
				->leftJoin('#__users AS d ON a.memberid=d.id')
				->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
				->leftJoin('#__hdflv_category AS b ON a.playlistid=b.id')
				->where(
						'('
						. $db->quoteName('e.catid') . ' = ' . $db->quote($catid)
						. ' OR ' . $db->quoteName('b.parent_id') . ' = ' . $db->quote($catid)
						. ' OR ' . $db->quoteName('a.playlistid') . ' = ' . $db->quote($catid)
						. ')'
						)
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
				->group($db->escape('e.vid'))
				->order($db->escape('b.ordering' . ' ' . 'ASC'));
		$db->setQuery($query);
		$searchtotal = $db->loadObjectList();
		$total = count($searchtotal);
		$pageno = 1;

		if (JRequest::getVar('video_pageid', '', 'post', 'int'))
		{
			$pageno = JRequest::getVar('video_pageid', '', 'post', 'int');
		}

		$limitrow = $this->getcategoryrowcol();
		$thumbview = unserialize($limitrow[0]->thumbview);
		$length = $thumbview['categoryrow'] * $thumbview['categorycol'];
		$pages = ceil($total / $length);

		if ($pageno == 1)
		{
			$start = 0;
		}
		else
		{
			$start = ( $pageno - 1) * $length;
		}

		// This query for displaying category's full view display
		$query->clear()
				->select(
				array(
					'a.id', 'a.filepath', 'a.thumburl', 'a.title', 'a.description', 'a.times_viewed',
					'a.ratecount', 'a.rate', 'a.streameroption', 'a.streamerpath', 'a.videourl', 'a.playlistid',
					'a.amazons3', 'a.seotitle', 'a.embedcode',
					'b.category', 'b.seo_category', 'b.parent_id', 'd.username', 'e.catid', 'e.vid'
					)
				)
				->from('#__hdflv_upload AS a')
				->leftJoin('#__users AS d ON a.memberid=d.id')
				->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
				->leftJoin('#__hdflv_category AS b ON a.playlistid=b.id')
				->where(
						'('
						. $db->quoteName('e.catid') . ' = ' . $db->quote($catid)
						. ' OR ' . $db->quoteName('b.parent_id') . ' = ' . $db->quote($catid)
						. ' OR ' . $db->quoteName('a.playlistid') . ' = ' . $db->quote($catid)
						. ')'
						)
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
				->group($db->escape('e.vid'))
				->order($db->escape('a.id' . ' ' . 'DESC'));

		$db->setQuery($query, $start, $length);
		$resultrows = $db->LoadObjectList();

		// This query is to get videos for player in category page
		$query->clear()
		->select(
				array(
						'a.id', 'a.filepath', 'a.thumburl', 'a.title', 'a.description', 'a.times_viewed',
					'a.ratecount', 'a.rate', 'a.streameroption', 'a.streamerpath', 'a.videourl', 'a.playlistid',
					'a.amazons3', 'a.seotitle', 'a.embedcode'
				)
		)
		->from('#__hdflv_upload AS a')
		->leftJoin('#__users AS d ON a.memberid=d.id')
		->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
		->leftJoin('#__hdflv_category AS b ON a.playlistid=b.id')
		->where(
				'(' . $db->quoteName('a.playlistid') . ' = ' . $db->quote($catid) . ')'
		)
		->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
		->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
		->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
		->group($db->escape('e.vid'))
		->order($db->escape('a.id' . ' ' . 'DESC'));
		
		$db->setQuery($query, $start, $length);
		$videoForPlayer = $db->LoadObjectList();

		if(empty($videoForPlayer))
		{
			$query->clear()
			->select( array( 'a.id', 'a.filepath', 'a.thumburl', 'a.title', 'a.description', 'a.times_viewed',
					'a.ratecount', 'a.rate', 'a.streameroption', 'a.streamerpath', 'a.videourl', 'a.playlistid',
					'a.amazons3', 'a.seotitle', 'a.embedcode' ))
			->from('#__hdflv_upload AS a')
			->leftJoin('#__users AS d ON a.memberid=d.id')
			->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
			->leftJoin('#__hdflv_category AS b ON a.playlistid=b.id')
			->where(
					'('
					. $db->quoteName('b.parent_id') . ' = ' . $db->quote($catid)
					. ')'
			)
			->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
			->group($db->escape('e.vid'))
			->order($db->escape('a.id' . ' ' . 'DESC'));
			$db->setQuery($query, $start, $length);
			$videoForPlayer = $db->LoadObjectList();
		}

		// This query for displaying category's full view display
		$query->clear()
				->select('category')
				->from('#__hdflv_category')
				->where($db->quoteName('id') . ' = ' . $db->quote($catid));
		$db->setQuery($query);
		$category = $db->LoadObjectList();

		// Below code is to merge the pagination values like pageno,pages,start value,length value
		if (count($resultrows) > 0)
		{
			$categoryname_array = array('categoryname' => $category);
			$merge_rows = array_merge($resultrows, $categoryname_array);
			$pageno_array = array('pageno' => $pageno);
			$merge_pageno = array_merge($merge_rows, $pageno_array);
			$pages_array = array('pages' => $pages);
			$merge_pages = array_merge($merge_pageno, $pages_array);
			$start_array = array('start' => $start);
			$merge_start = array_merge($merge_pages, $start_array);
			$length_array = array('length' => $length);
			$mergeLength = array_merge($merge_start, $length_array);
			$videoForPlayerArray = array('videoForPlayer' => $videoForPlayer);
			$rows = array_merge($mergeLength, $videoForPlayerArray);
		}
		else
		{
			// This query for displaying category's full view display
			$query->clear()
				->select('*')
				->from('#__hdflv_category')
				->where($db->quoteName('id') . ' = ' . $db->quote($catid));
			$db->setQuery($query);
			$rows = $db->LoadObjectList();
		}

		return $rows;
	}

	/**
	 * Function to get category view settings
	 * 
	 * @return  array
	 */
	public function getcategoryrowcol()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		// Query to get category view settings
		$query->select(array('thumbview','dispenable'))
				->from('#__hdflv_site_settings');
		$db->setQuery($query);
		$rows = $db->LoadObjectList();

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
	 * Function to get category view settings
	 * 
	 * @return  object
	 */
	public function getplayersettings()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select(array('player_values','player_icons'))
				->from('#__hdflv_player_settings');
		$db->setQuery($query);
		$settingsrows = $db->loadObject();

		return $settingsrows;
	}

	/**
	 * Function to get category ID
	 * 
	 * @return  mixed
	 */
	public function getcategoryid()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);
		$flatCatid = is_numeric(JRequest::getString('category'));

		if (JRequest::getString('category') && $flatCatid != 1)
		{
			$catvalue = str_replace(':', '-', JRequest::getString('category'));

			if (!version_compare(JVERSION, '3.0.0', 'ge'))
			{
				$catvalue = $db->getEscaped($catvalue);
			}

			$query->select('id')
					->from('#__hdflv_category')
					->where($db->quoteName('seo_category') . ' = ' . $db->quote($catvalue));
			$db->setQuery($query);
			$catid = $db->loadResult();
		}
		elseif ($flatCatid == 1)
		{
			$catid = JRequest::getString('category');
		}
		elseif (JRequest::getInt('catid'))
		{
			$catid = JRequest::getInt('catid');
		}
		else
		{
			// This query is for category view pagination
			$query->select('id')
					->from('#__hdflv_category')
					->where($db->quoteName('published') . ' = ' . $db->quote('1'))
					->order($db->escape('category ASC'));
			$db->setQuery($query);
			$searchtotal1 = $db->loadObjectList();
			$catid = $searchtotal1[0]->id;
		}

		return $catid;
	}

	/**
	 * Function to get category data
	 * 
	 * @return  array
	 */
	public function getcategoryList()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);
		$catid = $this->getcategoryid();

		if (!version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$catid = $db->getEscaped($catid);
		}

		// Get exact category details
		// Query is to select the popular videos row
		$query->select(
				array(
					'DISTINCT(a.id)', 'a.*', 'b.id AS vid'
					)
				)
				->from('#__hdflv_category AS a')
				->leftJoin('#__hdflv_upload AS b ON b.playlistid = a.id')
				->where($db->quoteName('a.id') . ' = ' . $db->quote($catid))
				->group($db->escape('a.id'));
		$db->setQuery($query);
		$catgoryrows = $db->LoadObjectList();

		// Get parent category details
		// Query is to select the popular videos row
		$query->clear()
				->select(
				array(
					'DISTINCT(a.id)', 'a.*', 'b.id AS vid'
					)
				)
				->from('#__hdflv_category AS a')
				->leftJoin('#__hdflv_upload AS b ON b.playlistid = a.id')
				->where($db->quoteName('a.parent_id') . ' = ' . $db->quote($catid))
				->group($db->escape('a.id'))
				->order($db->escape('ordering'));
		$db->setQuery($query);
		$parentrows = $db->LoadObjectList();
		$rows = array_merge($catgoryrows, $parentrows);

		return $rows;
	}

	/**
	 * Function to get html video access level
	 * 
	 * @return  string
	 */
	public function getHTMLVideoAccessLevel()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$uid = $user->get('id');

			if ($uid)
			{
				$query->select('g.id AS group_id')
						->from('#__usergroups AS g')
						->leftJoin('#__user_usergroup_map AS map ON map.group_id = g.id')
						->where('map.user_id = ' . (int) $uid);
				$db->setQuery($query);
				$message = $db->loadObjectList();

				foreach ($message as $mess)
				{
					$accessid[] = $mess->group_id;
				}
			}
			else
			{
				$accessid[] = 1;
			}
		}
		else
		{
			$accessid = $user->get('aid');
		}

		// CODE FOR SEO OPTION OR NOT - START
		$flatCatid = is_numeric(JRequest::getString('category'));

		if (JRequest::getString('category') && $flatCatid != 1)
		{
			$catvalue = str_replace(':', '-', JRequest::getString('category'));
			$query->clear()
					->select('id')
					->from('#__hdflv_category')
					->where('seo_category = ' . $db->quote($catvalue));
			$db->setQuery($query);
			$catid = $db->loadResult();
		}
		elseif ($flatCatid == 1)
		{
			$catid = JRequest::getString('category');
		}
		elseif (JRequest::getInt('catid'))
		{
			$catid = JRequest::getInt('catid');
		}
		else
		{
			// This query is for category view pagination
			$query->clear()
					->select('id')
					->from('#__hdflv_category')
					->where($db->quoteName('published') . ' = ' . $db->quote('1'))
					->order($db->escape('category ASC'));
			$db->setQuery($query);
			$searchtotal1 = $db->loadObjectList();

			// Category id is stored in this catid variable
			$catid = $searchtotal1[0]->id;
		}

		if (!version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$catid = $db->getEscaped($catid);
		}

		// Query to calculate total number of videos in paricular category
		$query->clear()
				->select(
				array(
					'a.*', 'b.id as cid', 'b.category', 'b.seo_category', 'b.parent_id', 'c.*'
					)
				)
				->from('#__hdflv_upload AS a')
				->leftJoin('#__users AS d ON a.memberid=d.id')
				->leftJoin('#__hdflv_video_category AS c ON c.vid=a.id')
				->leftJoin('#__hdflv_category AS b ON c.catid=b.id')
				->where(
						'('
						. $db->quoteName('c.catid') . ' = ' . $db->quote($catid)
						. ' OR ' . $db->quoteName('b.parent_id') . ' = ' . $db->quote($catid)
						. ' OR ' . $db->quoteName('a.playlistid') . ' = ' . $db->quote($catid)
						. ')'
						)
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1') . ' AND ' . $db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
				->order($db->escape('b.id ASC'));
		$db->setQuery($query);
		$rowsVal = $db->loadAssoc();

		if (count($rowsVal) > 0)
		{
			if (version_compare(JVERSION, '1.6.0', 'ge'))
			{
				$query = $db->getQuery(true);

				if ($rowsVal['useraccess'] == 0)
				{
					$rowsVal['useraccess'] = 1;
				}

				$query->clear()
						->select('rules as rule')
						->from('#__viewlevels AS view')
						->where('id = ' . (int) $rowsVal['useraccess']);
				$db->setQuery($query);
				$message = $db->loadResult();
				$accessLevel = json_decode($message);
			}

			$member = "true";

			if (version_compare(JVERSION, '1.6.0', 'ge'))
			{
				$member = "false";

				foreach ($accessLevel as $useracess)
				{
					if (in_array("$useracess", $accessid) || $useracess == 1)
					{
						$member = "true";
						break;
					}
				}
			}
			else
			{
				if ($rowsVal['useraccess'] != 0)
				{
					if ($accessid != $rowsVal['useraccess'] && $accessid != 2)
					{
						$member = "false";
					}
				}
			}

			return $member;
		}
	}
}
