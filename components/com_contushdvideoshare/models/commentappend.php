<?php
/**
 * Appened default Comment for HD Video Share
 *
 * This file is to fetch comment details and 
 * appened default Comment info on the player detail page 
 *
 * @category   Apptha
 * @package    Com_Contushdvideoshare
 * @version    3.6
 * @author     Apptha Team <developers@contus.in>
 * @copyright  Copyright (C) 2014 Apptha. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla model library
jimport('joomla.application.component.model');

/**
 * Commentappend model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Modelcontushdvideosharecommentappend extends ContushdvideoshareModel
{
	/**
	 * Function to get comments
	 * 
	 * @return  array
	 */
	public function getcomment()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$sub = $db->getQuery(true);

		if (JRequest::getVar('id', '', 'get', 'int'))
		{
			if (JRequest::getVar('video_pageid', '', 'get', 'int'))
			{
				$pageno = JRequest::getVar('video_pageid', '', 'get', 'int');
			}
			else
			{
				$pageno = 1;
			}

			$id = JRequest::getVar('id', '', 'get', 'int');

			if (JRequest::getVar('name', '', 'get', 'string') && JRequest::getVar('message', '', 'get', 'string'))
			{
				// Getting the parent id value
				$parentid = JRequest::getVar('pid', '', 'get', 'int');

				// Getting the name who is posting the comments
				$name = JRequest::getVar('name', '', 'get', 'string');

				// Getting the message
				$message = JRequest::getVar('message', '', 'get', 'string');
				$values = array($parentid, $id, $db->quote($name), $db->quote($message), 1);

				// This insert query is to post a new comment for a particular video
				$query->clear()
						->insert($db->quoteName('#__hdflv_comments'))
						->columns($db->quoteName(array('parentid', 'videoid', 'name', 'message', 'published')))
						->values(implode(',', $values));
				$db->setQuery($query);
				$db->query();
			}

			// Following code is to display the title and times of views for a particular video
			$query->clear()
					->select(
							array(
								'a.title', 'a.times_viewed', 'a.playlistid', 'a.id', 'a.seotitle', 'c.seo_category', 'a.memberid', 'b.username'
							)
					)
					->from('#__hdflv_upload AS a')
					->leftJoin('#__hdflv_category AS c ON a.playlistid=c.id')
					->leftJoin('#__users AS b ON a.memberid=b.id')
					->where($db->quoteName('a.id') . ' = ' . $db->quote($id));

			// This query is to display the title and times of views in the video page
			$db->setQuery($query);
			$commenttitle = $db->loadObjectList();

			// Title query for video ends here
			$query->clear()
					->select('count(*)')
					->from('#__hdflv_comments')
					->where($db->quoteName('published') . ' = ' . $db->quote('1') . ' AND ' . $db->quoteName('videoid') . ' = ' . $db->quote($id));

			// Query is to get the pagination value for comments display
			$db->setQuery($query);
			$total = $db->loadResult();
			$length = 10;
			$pages = ceil($total / $length);

			if ($pageno == 1)
			{
				$start = 0;
			}
			else
			{
				$start = ($pageno - 1) * $length;
			}

			$sub->select(array('parentid as number', 'id', 'parentid', 'videoid', 'subject', 'name', 'created', 'message'))
			->from('#__hdflv_comments')
			->where($db->quoteName('parentid') . ' != ' . $db->quote('0'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('videoid') . ' = ' . $db->quote($id));
		
			$query->clear()
			->select(array('id as number', 'id', 'parentid', 'videoid', 'subject', 'name', 'created', 'message'))
			->from('#__hdflv_comments')
			->where($db->quoteName('parentid') . ' = ' . $db->quote('0'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('videoid') . ' = ' . $db->quote($id) .' UNION ' . $sub)
			->order($db->escape('number' . ' ' . 'DESC') . ',' . $db->escape('parentid'));

			$db->setQuery($query);
			$rowscount = $db->loadObjectList();
			$totalcomment = count($rowscount);

			// Query is to display the comments posted for particular video
			$db->setQuery($query, $start, $length);
			$rows = $db->loadObjectList();

			// Below code is to merge the pagination values like pageno,pages,start value,length value
			$commenttitle_and_pageno = array_merge($commenttitle, array('pageno' => $pageno));
			$mergepages = array_merge($commenttitle_and_pageno, array('pages' => $pages));
			$merge_start_value = array_merge($mergepages, array('start' => $start));
			$merge_length = array_merge($merge_start_value, array('length' => $length));
			$merge_totalcomment = array_merge($merge_length, array('totalcomment' => $totalcomment));

			// Merge code ends here
			$query->clear()
					->select('*')
					->from('#__hdflv_player_settings');
			$db->setQuery($query);
			$playersettingsresult = $db->loadObjectList();

			$query->clear()
			->select('dispenable')
			->from('#__hdflv_site_settings');
			$db->setQuery($query);
			$siteSettingsResult = $db->loadResult();
			$dispEnable = unserialize ( $siteSettingsResult );

			return array($merge_totalcomment, $rows, $playersettingsresult, $dispEnable);
		}
	}

	/**
	 * Function to get ratting
	 * 
	 * @return  mixed
	 */
	public function ratting()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		if (JRequest::getVar('id', '', 'get', 'int'))
		{
			$id = JRequest::getVar('id', '', 'get', 'int');
		}

		if (JRequest::getVar('rate', '', 'get', 'int'))
		{
			$fields = array(
				$db->quoteName('rate') . ' = ' . JRequest::getVar('rate', '', 'get', 'int') . '+rate',
				$db->quoteName('ratecount') . '= 1+ratecount'
			);

			$query->clear()
					->update($db->quoteName('#__hdflv_upload'))
					->set($fields)
					->where($db->quoteName('id') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$db->query();
			exit;
		}

		if (JRequest::getVar('id', '', 'get', 'int'))
		{
			// Get Views counting
			$query->clear()
					->select(
							array(
								'a.rate', 'a.ratecount', 'a.times_viewed', 'a.memberid', 'b.username'
							)
					)
					->from('#__hdflv_upload AS a')
					->leftJoin('#__users AS b ON a.memberid=b.id')
					->where($db->quoteName('a.id') . ' = ' . $db->quote($id));

			// This query is to display the title and times of views in the video page
			$db->setQuery($query);
			$commenttitle = $db->loadObjectList();

			return $commenttitle;
		}
	}
}
