<?php
/**
 * Player model file
 *
 * This file is to fetch all videos details from database for video home page view
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
 * Player model class
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Modelcontushdvideoshareplayer extends ContushdvideoshareModel
{
	/**
	 * Function to get video id
	 * 
	 * @param   string  $video  Video name
	 * 
	 * @return  object
	 */
	public function getVideoId($video)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		if (!version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$video = $db->getEscaped($video);
		}

		$query->select(array('id', 'playlistid', 'videourl'))
				->from('#__hdflv_upload')
				->where($db->quoteName('seotitle') . ' = ' . $db->quote($video));
		$db->setQuery($query);
		$videodetails = $db->loadObject();

		return $videodetails;
	}

	/**
	 * Function to get video details
	 * 
	 * @param   string  $video     Video name
	 * @param   string  $category  Category name
	 * 
	 * @return  object
	 */
	public function getVideoCatId($video, $category)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$adminview = JRequest::getString('adminview');

		if ($adminview == true)
		{
			$publish = '';
		}
		else
		{
			$publish = $db->quoteName('a.published') . ' = ' . $db->quote('1');
		}

		$query->select(array('a.id', 'a.playlistid', 'a.videourl'))
				->from('#__hdflv_upload AS a')
				->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
				->leftJoin('#__hdflv_category AS b ON e.catid=b.id');

		if ($publish != '')
		{
			$query->where($publish);
		}

		$query->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('a.seotitle') . ' = ' . $db->quote($video))
				->where($db->quoteName('b.seo_category') . ' = ' . $db->quote($category));
		$db->setQuery($query);
		$videodetails = $db->loadObject();

		return $videodetails;
	}

	/**
	 * Function to get featured videos
	 * 
	 * @return  object
	 */
	public function getfeatured()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$adminview = JRequest::getString('adminview');

		if ($adminview == true)
		{
			$publish = '';
		}
		else
		{
			$publish = $db->quoteName('published') . ' = ' . $db->quote('1');
		}

		$query->select('id')
				->from('#__hdflv_upload');

		if ($publish != '')
		{
			$query->where($publish);
		}

		$query->where($db->quoteName('featured') . ' = ' . $db->quote('1'))
				->where($db->quoteName('type') . ' = ' . $db->quote('0'))
				->order($db->escape('ordering' . ' ' . 'ASC'));
		$db->setQuery($query);
		$feavideo = $db->loadObject();

		return $feavideo;
	}

	/**
	 * Function to get video details for video id
	 * 
	 * @param   int  $video  video id
	 * 
	 * @return  object
	 */
	public function getVideodetail($video)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select(array('id', 'playlistid', 'videourl'))
				->from('#__hdflv_upload')
				->where($db->quoteName('id') . ' = ' . $db->quote($video));
		$db->setQuery($query);
		$videodetails = $db->loadObject();

		return $videodetails;
	}

	/**
	 * Function to get video details for home page
	 * 
	 * @param   int  $videoid  video id
	 * 
	 * @return  array
	 */
	public function showhdplayer($videoid)
	{
		$playid = $thumbid = $start = $total = 0;
		$db = JFactory::getDBO();
		$length = $pageno = 1;
		$hd_bol = "false";
		$query = $db->getQuery(true);

		$query->select(array('player_values', 'logopath', 'player_icons'))
				->from('#__hdflv_player_settings');
		$db->setQuery($query);
		$settingsrows = $db->loadObjectList();
		$player_values = unserialize($settingsrows[0]->player_values);

		if ($videoid)
		{
			$playid = $videoid;
		}

		$query->clear()
				->select('count(id)')
				->from('#__hdflv_upload')
				->where($db->quoteName('published') . ' = ' . $db->quote('1'))
				->order($db->escape('id' . ' ' . 'DESC'));
		$db->setQuery($query);
		$rs_count = $db->loadResult();

		if ($rs_count > 0)
		{
			$total = $rs_count;
		}

		if (JRequest::getVar('video_pageid', '', 'post', 'string'))
		{
			$pageno = JRequest::getVar('video_pageid', '', 'post', 'string');
			$_SESSION['commentappendpageno'] = $pageno;
		}

		if ($player_values['nrelated'] != "")
		{
			$length = $player_values['nrelated'];
		}
		else
		{
			$length = 4;
		}

		if ($length == 0)
		{
			$length = 1;
		}

		if ($pageno == 1)
		{
			$start = 0;
		}
		else
		{
			$start = ($pageno - 1) * $length;
		}

		$current_path = "components/com_contushdvideoshare/images/";

		$query->clear()
				->select('*')
				->from('#__hdflv_upload')
				->where($db->quoteName('published') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$hdvideo = false;

		if (isset($rows[0]->id))
		{
			$thumbid = $rows[0]->id;
		}

		if (count($rows) > 0)
		{
			if ($rows[0]->filepath == "File" || $rows[0]->filepath == "FFmpeg")
			{
				$video = JURI::base() . $current_path . $rows[0]->videourl;
				($rows[0]->hdurl != "") ? $hdvideo = JURI::base() . $current_path . $rows[0]->hdurl : $hdvideo = "";
				$previewimage = JURI::base() . $current_path . $rows[0]->previewurl;

				if ($rows[0]->hdurl)
				{
					$hd_bol = "true";
				}
				else
				{
					$hd_bol = "false";
				}
			}
			elseif ($rows[0]->filepath == "Url")
			{
				$video = $rows[0]->videourl;
				$previewimage = $rows[0]->previewurl;

				if ($rows[0]->hdurl)
				{
					$hd_bol = "true";
				}
				else
				{
					$hd_bol = "false";
				}

				$hdvideo = $rows[0]->hdurl;
			}
			elseif ($rows[0]->filepath == "Youtube")
			{
				$video = $rows[0]->videourl;
				$previewimage = $rows[0]->previewurl;

				if ($rows[0]->hdurl)
				{
					$hd_bol = "true";
				}
				else
				{
					$hd_bol = "false";
				}

				$hdvideo = $rows[0]->videourl;
			}

			$playid = $rows[0]->id;
		}

		$query->clear()
				->select('*')
				->from('#__hdflv_upload')
				->where($db->quoteName('published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('id') . ' NOT IN (' . $db->quote($playid) . ')')
				->order($db->escape('ordering' . ' ' . 'ASC'));
		$db->setQuery($query, $start, $length);
		$rs_playlist = $db->loadobjectList();

		$playerpath = JURI::base() . 'components/com_contushdvideoshare/hdflvplayer/hdplayer.swf';
		$base_url = str_replace(':', '%3A', JURI::base());
		$url_base = substr_replace($base_url, "", -1);
		$baseurl = str_replace('/', '%2F', $url_base);

		$query->clear()
				->select('*')
				->from('#__hdflv_googlead')
				->where($db->quoteName('publish') . ' = ' . $db->quote('1'))
				->where($db->quoteName('id') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$fields = $db->loadObjectList();

		if (isset($fields[0]->publish))
		{
			$insert_data_array = array(
				'playerpath' => $playerpath,
				'baseurl' => $baseurl,
				'thumbid' => $thumbid,
				'rs_playlist' => $rs_playlist,
				'length' => $length,
				'total' => $total,
				'closeadd' => $fields[0]->closeadd,
				'showoption' => $fields[0]->showoption,
				'reopenadd' => $fields[0]->reopenadd,
				'ropen' => $fields[0]->ropen,
				'publish' => $fields[0]->publish,
				'showaddc' => $fields[0]->showaddc
					);
		}
		else
		{
			$insert_data_array = array(
				'playerpath' => $playerpath,
				'baseurl' => $baseurl,
				'thumbid' => $thumbid,
				'rs_playlist' => $rs_playlist,
				'length' => $length,
				'total' => $total
					);
		}

		$merged_result = array_merge($settingsrows, $insert_data_array);

		return $merged_result;
	}

	/**
	 * Function to rating calculation
	 * 
	 * @param   int  $videoid  video id
	 * 
	 * @return  array
	 */
	public function ratting($videoid)
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		if ($videoid)
		{
			$id = $videoid;
		}
		else
		{
			$query->select(array('a.*', 'b.category', 'd.username', 'e.*'))
					->from('#__hdflv_upload AS a')
					->leftJoin('#__users AS d ON a.memberid=d.id')
					->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
					->leftJoin('#__hdflv_category AS b ON e.catid=b.id')
					->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
					->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
					->where($db->quoteName('a.featured') . ' = ' . $db->quote('1'))
					->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
					->group($db->escape('e.vid'))
					->order($db->escape('a.id' . ' ' . 'DESC'));

			// Query is to display recent videos in home page
			$db->setQuery($query);
			$rs_video = $db->loadObjectList();

			if (empty($rs_video))
			{
				$query->clear('where')
						->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
						->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
						->where($db->quoteName('a.type') . ' = ' . $db->quote('0'));
				$db->setQuery($query);
				$rs_video = $db->loadObjectList();
			}

			if (isset($rs_video[0]) && $rs_video[0] != '')
			{
				$id = $rs_video[0]->id;
			}
			else
			{
				$id = '';
			}
		}

		if (version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$get_rate = JRequest::getVar('rate');
		}
		else
		{
			$get_rate = JRequest::getVar('rate', '', 'get', 'int');
		}

		if ($get_rate)
		{
			$rated_user = '';
			$userid = $_SERVER['REMOTE_ADDR'];

			$query->clear()
			->select('rateduser')
			->from('#__hdflv_upload')
			->where($db->quoteName('id') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$rateduser    = $db->loadResult();
				
			$rate_user = explode(',', $rateduser);
			if(in_array($userid, $rate_user))
			{
				$rateuser = 1;
			}
			else
			{
				$rateuser = 0;
			}
			
			if($rateuser == 0) {
				if(empty($rateduser))
				{
					$rated_user = $userid;
				}
				else
				{
					$rated_user = $rateduser . ',' . $userid;
				}
				
			$fields = array(
				$db->quoteName('rate') . ' = ' . $get_rate . '+rate',
				$db->quoteName('ratecount') . '= 1+ratecount',
				$db->quoteName('rateduser') . '= "' . $rated_user . '"'
			);

			$query->clear()
					->update($db->quoteName('#__hdflv_upload'))
					->set($fields)
					->where($db->quoteName('id') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$db->query();

			$query->clear()
					->select('ratecount')
					->from('#__hdflv_upload')
					->where($db->quoteName('id') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$ratings = $db->loadResult();

			echo $ratings;
			} else {
				echo "You have already voted";
			}
			exit;
		}

		if ($id != '')
		{
			// Get Views counting
			$query->clear()
					->select(array('a.times_viewed', 'a.rate', 'a.rateduser', 'a.ratecount', 'a.memberid', 'b.username'))
					->from('#__hdflv_upload AS a')
					->leftJoin('#__users AS b ON a.memberid=b.id')
					->where($db->quoteName('a.id') . ' = ' . $db->quote($id));

			// This query is to display the title and times of views in the video page
			$db->setQuery($query);
			$commenttitle = $db->loadObjectList();

			return $commenttitle;
		}
	}

	/**
	 * Function to display comments
	 * 
	 * @param   int  $videoid  video id
	 * 
	 * @return  array
	 */
	public function displaycomments($videoid)
	{
		if ($videoid)
		{
			$db = $this->getDBO();
			$query = $db->getQuery(true);
			$sub = $db->getQuery(true);
			$id = $videoid;
			$pageno = 1;
			$length = 10;

			if (JRequest::getVar('name', '', 'get', 'string') && JRequest::getVar('message', '', 'get', 'string'))
			{
				// Getting the parent id value
				$parentid = JRequest::getVar('pid', '', 'get', 'int');

				// Getting the name who is posting the comments
				$name = JRequest::getVar('name', '', 'get', 'string');

				// Getting the message
				$message = JRequest::getVar('message', '', 'get', 'string');

				if (strlen($message) > 500)
				{
					$message = JHTML::_('string.truncate', ($message), 500);
				}

				if (!version_compare(JVERSION, '3.0.0', 'ge'))
				{
					$name = $db->getEscaped($name);
					$message = $db->getEscaped($message);
				}

				// Insert query to post a new comment for a particular video
				$values = array($parentid, $id, $db->quote($name), $db->quote($message), 1);
				$query->insert($db->quoteName('#__hdflv_comments'))
						->columns($db->quoteName(array('parentid', 'videoid', 'name', 'message', 'published')))
						->values(implode(',', $values));

				$db->setQuery($query);
				$db->query();
			}

			// Following code is to display the title and times of views for a particular video
			$query->clear()
					->select(array('a.title', 'a.description', 'a.times_viewed', 'a.memberid', 'b.username'))
					->from('#__hdflv_upload AS a')
					->leftJoin('#__users AS b ON a.memberid=b.id')
					->where($db->quoteName('a.id') . ' = ' . $db->quote($id));

			// This query is to display the title and times of views in the video page
			$db->setQuery($query);
			$commenttitle = $db->loadObjectList();

			// Query is to get the pagination value for comments display
			$query->clear()
					->select('count(id)')
					->from('#__hdflv_comments')
					->where($db->quoteName('published') . ' = ' . $db->quote('1'))
					->where($db->quoteName('videoid') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$total = $db->loadResult();

			if (JRequest::getVar('video_pageid', '', 'post', 'int'))
			{
				$pageno = JRequest::getVar('video_pageid', '', 'post', 'int');
			}

			$pages = ceil($total / $length);

			if ($pageno == 1)
			{
				$start = 0;
			}
			else
			{
				$start = ( $pageno - 1) * $length;
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

			// Query is to display the comments posted for particular video
			$db->setQuery($query);
			$rowscount = $db->loadObjectList();
			$totalcomment = count($rowscount);

			// Query is to display the comments posted for particular video
			$db->setQuery($query, $start, $length);
			$rows = $db->loadObjectList();

			// Below code is to merge the pagination values like pageno,pages,start value,length value
			$merge_page_no = array_merge($commenttitle, array('pageno' => $pageno));
			$mergepages = array_merge($merge_page_no, array('pages' => $pages));
			$merge_start_value = array_merge($mergepages, array('start' => $start));
			$merge_length = array_merge($merge_start_value, array('length' => $length));
			$merge_totalcomment = array_merge($merge_length, array('totalcomment' => $totalcomment));

			return array($merge_totalcomment, $rows);
		}
	}

	/**
	 * Function to get home page videos
	 * 
	 * @return  array
	 */
	public function gethomepagebottom()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		// Function to get homa page video settings
		$viewrow = $this->gethomepagebottomsettings();
		$thumbview = unserialize($viewrow[0]->homethumbview);
		$featurelimit = $thumbview['homefeaturedvideorow'] * $thumbview['homefeaturedvideocol'];

		// Query to get featured videos
		$query->select(
						array(
							'a.*', 'b.category', 'b.seo_category', 'd.username', 'c.*'
						)
				)
				->from('#__hdflv_upload AS a')
				->leftJoin('#__users AS d ON a.memberid=d.id')
				->leftJoin('#__hdflv_video_category AS c ON c.vid=a.id')
				->leftJoin('#__hdflv_category AS b ON c.catid=b.id')
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('a.featured') . ' = ' . $db->quote('1'))
				->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
				->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
				->group($db->escape('c.vid'))
				->order($db->escape('a.ordering' . ' ' . 'ASC'));

		// Query is to display featured videos in home page randomly
		$db->setQuery($query, 0, $featurelimit);

		// $featuredvideos contains the results
		$featuredvideos = $db->loadobjectList();
		$recentlimit = $thumbview['homerecentvideorow'] * $thumbview['homerecentvideocol'];

		// Query to get recent videos
		$query->clear()
				->select(
						array(
							'a.*', 'b.category', 'b.seo_category', 'd.username', 'c.*'
						)
				)
				->from('#__hdflv_upload AS a')
				->leftJoin('#__users AS d ON a.memberid=d.id')
				->leftJoin('#__hdflv_video_category AS c ON c.vid=a.id')
				->leftJoin('#__hdflv_category AS b ON c.catid=b.id')
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
				->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
				->group($db->escape('c.vid'))
				->order($db->escape('a.id' . ' ' . 'DESC'));

		// Query is to display recent videos in home page
		$db->setQuery($query, 0, $recentlimit);

		// $recentvideos contains the results
		$recentvideos = $db->loadobjectList();
		$popularlimit = $thumbview['homepopularvideorow'] * $thumbview['homepopularvideocol'];

		// Query to get popular videos
		$query->clear()
				->select(
						array(
							'a.*', 'b.category', 'b.seo_category', 'd.username', 'c.*'
						)
				)
				->from('#__hdflv_upload AS a')
				->leftJoin('#__users AS d ON a.memberid=d.id')
				->leftJoin('#__hdflv_video_category AS c ON c.vid=a.id')
				->leftJoin('#__hdflv_category AS b ON c.catid=b.id')
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
				->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
				->group($db->escape('c.vid'))
				->order($db->escape('a.times_viewed' . ' ' . 'DESC'));

		// Query is to display popular videos in home page
		$db->setQuery($query, 0, $popularlimit);

		// $popularvideos contains the results
		$popularvideos = $db->loadobjectList();

		// Merging the featured,recent,popular videos results

		return array($featuredvideos, $recentvideos, $popularvideos);
	}

	/**
	 * Function to get home page bottom settings 
	 * 
	 * @return  array
	 */
	public function gethomepagebottomsettings()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		// Query is to select the home page botom videos settings
		$query->select(array('homethumbview', 'dispenable'))
				->from('#__hdflv_site_settings');
		$db->setQuery($query);
		$rows = $db->LoadObjectList();

		return $rows;
	}

	/**
	 * Function to get video detail for HTML5 Player 
	 * 
	 * @param   int  $videoId  video id
	 * 
	 * @return  object
	 */
	public function getHTMLVideoDetails($videoId)
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);
		$adminview = JRequest::getString('adminview');

		if ($adminview == true)
		{
			$publish = '';
		}
		else
		{
			$publish = $db->quoteName('a.published') . ' = ' . $db->quote('1');
		}

		if ($publish != '')
		{
			$query->where($publish);
		}

		if (isset($videoId) && $videoId != '')
		{
			$query->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('a.id') . ' = ' . $db->quote($videoId))
				->order($db->escape('a.ordering' . ' ' . 'ASC'));
		}
		else
		{
			$query->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
				->where($db->quoteName('a.featured') . ' = ' . $db->quote('1'))
				->group($db->escape('e.vid'))
				->order($db->escape('a.ordering' . ' ' . 'ASC'));
		}

		$query->select(array('a.*', 'b.seo_category'))
				->from('#__hdflv_upload AS a')
				->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
				->leftJoin('#__hdflv_category AS b ON e.catid=b.id');

		// Query is to select the popular videos row
		$db->setQuery($query);
		$rows = $db->LoadObject();

		if (empty($videoId))
		{
			if (count($rows) == 0)
			{
				$query->clear()
						->select(array('a.*', 'b.seo_category', 'b.category', 'd.username', 'e.*'))
						->from('#__hdflv_upload AS a')
						->leftJoin('#__users d ON a.memberid=d.id')
						->leftJoin('#__hdflv_video_category e ON e.vid=a.id')
						->leftJoin('#__hdflv_category b ON e.catid=b.id');

				if ($publish != '')
				{
					$query->where($publish);
				}

				$query->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
						->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
						->group($db->escape('e.vid'))
						->order($db->escape('a.id' . ' ' . 'DESC'));

				// Query is to display recent videos in home page
				$db->setQuery($query, 0, 1);
				$rows = $db->LoadObject();
			}
		}

		return $rows;
	}

	/**
	 * Function to get video access level
	 * 
	 * @return  string
	 */
	public function getHTMLVideoAccessLevel()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);

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

		$videoid = 0;

		// CODE FOR SEO OPTION OR NOT - START
		$video = JRequest::getVar('video');
		$id = JRequest::getInt('id');
		$adminview = JRequest::getString('adminview');
		$flagVideo = is_numeric($video);

		if (isset($video) && $video != "")
		{
			if ($flagVideo != 1)
			{
				// Joomla router replaced to : from - in query string
				$videoTitle = JRequest::getString('video');
				$videoid = str_replace(':', '-', $videoTitle);

				if ($videoid != "")
				{
					$videoid = $videoid;
				}

				$query->clear()
						->select(
								array(
									'DISTINCT a.*', 'b.category'
								)
						)
						->from('#__hdflv_upload AS a')
						->leftJoin('#__hdflv_category AS b ON a.playlistid=b.id  OR a.playlistid=b.parent_id')
						->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
						->where($db->quoteName('a.seotitle') . ' = ' . $db->quote($videoid));
				$db->setQuery($query);
				$rowsVal = $db->loadAssoc();
			}
			else
			{
				$videoid = JRequest::getInt('video');
				$query->clear()
						->select(
								array(
									'DISTINCT a.*', 'b.category'
								)
						)
						->from('#__hdflv_upload AS a')
						->leftJoin('#__hdflv_category AS b ON a.playlistid=b.id  OR a.playlistid=b.parent_id')
						->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
						->where($db->quoteName('a.id') . ' = ' . $db->quote($videoid));
				$db->setQuery($query);
				$rowsVal = $db->loadAssoc();
			}
		}
		elseif (isset($id) && $id != '')
		{
			$videoid = JRequest::getInt('id');
			$query->clear()
					->select(
							array(
								'DISTINCT a.*', 'b.category'
							)
					)
					->from('#__hdflv_upload AS a')
					->leftJoin('#__hdflv_category AS b ON a.playlistid=b.id  OR a.playlistid=b.parent_id')
					->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
					->where($db->quoteName('a.id') . ' = ' . $db->quote($videoid));
			$db->setQuery($query);
			$rowsVal = $db->loadAssoc();
		}

		// CODE FOR SEO OPTION OR NOT - END
		else
		{
			$query->clear()
					->select(
							array(
								'a.*', 'b.category', 'd.username', 'e.*'
							)
					)
					->from('#__hdflv_upload AS a')
					->leftJoin('#__users AS d ON a.memberid=d.id')
					->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
					->leftJoin('#__hdflv_category AS b ON e.catid=b.id')
					->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
					->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
					->where($db->quoteName('a.featured') . ' = ' . $db->quote('1'))
					->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
					->where($db->quoteName('d.block') . ' = ' . $db->quote('0'))
					->group($db->escape('e.vid'))
					->order($db->escape('a.ordering' . ' ' . 'ASC'));

			// Query is to display recent videos in home page
			$db->setQuery($query);
			$rowsVal = $db->loadAssoc();

			if (count($rowsVal) == 0)
			{
				$query->clear('where')
						->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
						->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
						->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
						->where($db->quoteName('d.block') . ' = ' . $db->quote('0'));

				// Query is to display recent videos in home page
				$db->setQuery($query, 0, 1);
				$rowsVal = $db->loadAssoc();
			}
		}

		if (count($rowsVal) > 0)
		{
			if (version_compare(JVERSION, '1.6.0', 'ge'))
			{
				$query = $db->getQuery(true);

				if ($rowsVal['useraccess'] == 0)
				{
					$rowsVal['useraccess'] = 1;
				}

				$query->select('rules as rule')
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

			if (!empty($adminview))
			{
				$member = "true";
			}

			return $member;
		}
	}

	/**
	 * Function to get initial video details
	 * 
	 * @return  array
	 */
	public function initialPlayer()
	{
		$videoid = 0;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		if (JRequest::getvar('id', '', 'get', 'int') || JRequest::getVar('video', '', 'get', 'int'))
		{
			if (JRequest::getVar('video', '', 'get', 'int'))
			{
				$videoid = JRequest::getVar('video', '', 'get', 'int');
			}
			else
			{
				$videoid = JRequest::getvar('id', '', 'get', 'int');
			}

			if ($videoid != "")
			{
				$query->select(
								array(
									'DISTINCT a.*', 'b.category'
								)
						)
						->from('#__hdflv_upload AS a')
						->leftJoin('#__hdflv_category AS b ON a.playlistid=b.id  OR a.playlistid=b.parent_id')
						->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
						->where($db->quoteName('a.id') . ' = ' . $db->quote($videoid));
				$db->setQuery($query);
				$rowsVal = $db->loadAssoc();
			}
		}
		elseif (JRequest::getVar('video', '', 'get', 'string'))
		{
			$video_string = JRequest::getVar('video', '', 'get', 'string');
			$video = str_replace(':', '-', $video_string);
			$query->clear()
					->select(
							array(
								'DISTINCT a.*', 'b.category'
							)
					)
					->from('#__hdflv_upload AS a')
					->leftJoin('#__hdflv_category AS b ON a.playlistid=b.id  OR a.playlistid=b.parent_id')
					->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
					->where($db->quoteName('a.seotitle') . ' = ' . $db->quote($video));
			$db->setQuery($query);
			$rowsVal = $db->loadAssoc();
		}
		else
		{
			$query->clear()
					->select(
							array(
								'a.*', 'b.category', 'd.username', 'e.*'
							)
					)
					->from('#__hdflv_upload AS a')
					->leftJoin('#__users AS d ON a.memberid=d.id')
					->leftJoin('#__hdflv_video_category AS e ON e.vid=a.id')
					->leftJoin('#__hdflv_category AS b ON e.catid=b.id')
					->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
					->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
					->where($db->quoteName('a.featured') . ' = ' . $db->quote('1'))
					->where($db->quoteName('a.type') . ' = ' . $db->quote('0'))
					->group($db->escape('e.vid'))
					->order($db->escape('a.ordering' . ' ' . 'ASC'));

			// Query is to display recent videos in home page
			$db->setQuery($query);
			$rowsVal = $db->loadAssoc();

			if (count($rowsVal) == 0)
			{
				$query->clear('where')
						->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
						->where($db->quoteName('b.published') . ' = ' . $db->quote('1'))
						->where($db->quoteName('a.type') . ' = ' . $db->quote('0'));

				// Query is to display recent videos in home page
				$db->setQuery($query, 0, 1);
				$rowsVal = $db->loadAssoc();
			}
		}

		return $rowsVal;
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
}
