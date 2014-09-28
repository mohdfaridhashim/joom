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

// Import Joomla model library
jimport('joomla.application.component.model');

// Import Joomla pagination
jimport('joomla.html.pagination');

/**
 * Admin show videos model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareModelshowvideos extends ContushdvideoshareModel
{
	/**
	 * Constructor function to declare global value
	 */
	public function __construct()
	{
		global $option, $mainframe, $db;
		parent::__construct();

		// Get global configuration
		$mainframe = JFactory::getApplication();
		$option = JRequest::getVar('option');
		$db = JFactory::getDBO();
	}

	/**
	 * Function to remove slashes from string
	 * 
	 * @param   string  $string  string to be remove slash
	 * @param   string  $type    type of action to be performed
	 * 
	 * @return  phpSlashes
	 */
	public function phpSlashes($string, $type = 'add')
	{
		if ($type == 'add')
		{
			if (get_magic_quotes_gpc())
			{
				return $string;
			}
			else
			{
				if (function_exists('addslashes'))
				{
					return addslashes($string);
				}
				else
				{
					return mysql_real_escape_string($string);
				}
			}
		}
		elseif ($type == 'strip')
		{
			return stripslashes($string);
		}
		else
		{
			die('error in PHP_slashes (mixed,add | strip)');
		}
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
	 * Function to get videos details for grid view
	 * 
	 * @return  showvideosmodel
	 */
	public function showvideosmodel()
	{
		global $option, $mainframe, $db;
		$query = $db->getQuery(true);

		//   To store and retrieve filter variables that are stored with the session
		$filter_order = $mainframe->getUserStateFromRequest($option . 'filter_order_adminvideos', 'filter_order', 'ordering', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option . 'filter_order_Dir_adminvideos', 'filter_order_Dir', 'asc', 'word');
		$search = $mainframe->getUserStateFromRequest($option . 'search', 'search', '', 'string');
		$search1 = $search;
		$state_filter = $mainframe->getUserStateFromRequest($option . 'filter_state', 'filter_state', '', 'int');
		$featured_filter = $mainframe->getUserStateFromRequest($option . 'filter_featured', 'filter_featured', '', 'string');
		$category_filter = $mainframe->getUserStateFromRequest($option . 'filter_category', 'filter_category', '', '');

		//  Default List Limit
		$limit = $mainframe->getUserStateFromRequest($option . '.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');

		// Set user = admin for admin videos
		$strAdmin = (JRequest::getVar('user', '', 'get')) ? JRequest::getVar('user', '', 'get') : '';

		// Get logged user
		$user = JFactory::getUser();
		$userid = $user->get('id');

		// Get user groups  from joomla version above 1.6.0
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			// Query items are returned as an associative array
			$query->clear()
					->select('g.id AS group_id')
					->from('#__usergroups AS g')
					->leftJoin('#__user_usergroup_map AS map ON map.group_id = g.id')
					->where('map.user_id = ' . (int) $userid);
			$db->setQuery($query);
			$arrUserGroup = $db->loadObject();
		}

		// Get user groups from joomla version below 1.6.0
		else
		{
			$query->clear()
					->select('gid')
					->from('#__users')
					->where('id = ' . (int) $userid);
			$db->setQuery($query);
			$arrUserGroup = $db->loadObject();
		}

		$query->clear()
				->select(array('id', 'member_id', 'category', 'seo_category', 'parent_id', 'ordering', 'published'))
				->from('#__hdflv_category')
				->where('published = 1');
		$db->setQuery($query);
		$rs_showplaylistname = $db->loadObjectList();

		// Select videos details
		if ($filter_order)
		{
			// For select videos details
			$query->clear()
					->select(
							array(
								'DISTINCT(d.videoid) AS cvid', 'a.id', 'a.memberid', 'a.published', 'a.title', 'a.seotitle',
								'a.featured', 'a.type', 'a.rate', 'a.ratecount', 'a.times_viewed', 'a.videos', 'a.filepath',
								'a.videourl', 'a.thumburl', 'a.previewurl', 'a.hdurl', 'a.home', 'a.playlistid', 'a.duration',
								'a.ordering', 'a.streamerpath', 'a.streameroption', 'a.postrollads', 'a.prerollads', 'a.midrollads', 'a.imaads', 'a.embedcode',
								'a.description', 'a.targeturl', 'a.download', 'a.prerollid', 'a.postrollid', 'a.created_date',
								'a.addedon', 'a.usergroupid', 'a.tags', 'a.useraccess', 'b.category', 'c.username', 'a.amazons3'
								)
							)
					->from('#__hdflv_upload a')
					->innerJoin('#__users c ON c.id = a.memberid')
					->leftJoin('#__hdflv_category b ON a.playlistid=b.id')
					->leftJoin('#__hdflv_comments d ON d.videoid=a.id');

			// For select user group id
			if (version_compare(JVERSION, '1.6.0', 'ge'))
			{
				/**
				 * User group
				 * 6 - Manager
				 * 7 - Administrator
				 * 8 - Super Users
				 */
				// For videos added by admin
				if ($strAdmin == 'admin')
				{
					if ($arrUserGroup->group_id == '8')
					{
						$query->where('a.usergroupid IN (6,7,8)');
					}
					else
					{
						$query->where(
								$db->quoteName('a.usergroupid') . ' = ' . $db->quote($arrUserGroup->group_id)
								. ' AND ' . $db->quoteName('a.memberid') . '=' . $db->quote($userid)
								);
					}
					// For videos added by member
				}
				else
				{
					$query->where('a.usergroupid NOT IN (6,7,8) AND ' . $db->quoteName('a.memberid') . '!=' . $db->quote($userid));
				}
			}
			else
			{
				// For videos added by admin
				if ($strAdmin == 'admin')
				{
					if ($arrUserGroup->gid == 25)
					{
						$query->where($db->quoteName('c.gid') . ' = ' . $db->quote('25'));
					}
					elseif ($arrUserGroup->gid == 24)
					{
						$query->where($db->quoteName('c.gid') . ' = ' . $db->quote('24'));
					}
				}
				// For videos added by member
				else
				{
					$query->where('c.gid NOT IN (24,25)');
				}
			}
		}

		// Assign filter variables
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		$search = $this->phpSlashes($search);

		// Filtering based on search keyword
		if ($search)
		{
			$dbescape_search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('a.title LIKE ' . $dbescape_search);
			$lists['search'] = $search1;
		}

		// Filtering based on status
		if ($state_filter)
		{
			if ($state_filter == 1)
			{
				$state_filterval = 1;
			}
			elseif ($state_filter == 2)
			{
				$state_filterval = 0;
			}
			else
			{
				$state_filterval = -2;
			}

			$query->where($db->quoteName('a.published') . ' = ' . $db->quote($state_filterval));
			$lists['state_filter'] = $state_filter;
		}
		else
		{
			$query->where($db->quoteName('a.published') . ' != ' . $db->quote('-2'));
		}

		// Filtering based on featured status
		if ($featured_filter)
		{
			$featured_filterval = ($featured_filter == '1') ? '1' : '0';
			$query->where($db->quoteName('a.featured') . ' = ' . $db->quote($featured_filterval));
			$lists['featured_filter'] = $featured_filter;
		}

		if ($category_filter)
		{
			$query->where($db->quoteName('a.playlistid') . ' = ' . $db->quote($category_filter));
			$lists['category_filter'] = $category_filter;
		}

		$query->order($db->escape($filter_order . ' ' . $filter_order_Dir));

		$db->setQuery($query);
		$getarrVideoList = $db->loadObjectList();
		$strTotalVideos = count($getarrVideoList);

		// Set pagination
		$pageNav = new JPagination($strTotalVideos, $limitstart, $limit);
		$db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$arrVideoList = $db->loadObjectList();

		// Display the last database error message in a standard format
		if ($db->getErrorNum())
		{
			JError::raiseWarning($db->getErrorNum(), $db->stderr());
		}

		$query->clear()
					->select('dispenable')
					->from('#__hdflv_site_settings')
					->where($db->quoteName('id') . ' = ' . $db->quote('1'));
			$db->setQuery($query);
			$setting_res = $db->loadResult();
			$dispenable = unserialize($setting_res);

			return array(
			'pageNav' => $pageNav, 'limit' => $limit, 'limitstart' => $limitstart,
			'lists' => $lists, 'rs_showupload' => $arrVideoList, 'rs_showplaylistname' => $rs_showplaylistname,
			'dispenable' => $dispenable
				);
	}

	/**
	 * Function to publish and unpublish videos
	 * 
	 * @param   array  $arrVideoId  video detail array
	 * 
	 * @return  showadsmodel
	 */
	public function changevideostatus($arrVideoId)
	{
		global $mainframe;

		if ($arrVideoId['task'] == "publish")
		{
			$msg = 'Published successfully';

			// Define joomla mailer
			$mailer = JFactory::getMailer();
			$config = JFactory::getConfig();
			$sender = $config->get('mailfrom');
			$site_name = $config->get('sitename');
			$mailer->setSender($sender);

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->clear()
					->select('dispenable')
					->from('#__hdflv_site_settings')
					->where($db->quoteName('id') . ' = ' . $db->quote('1'));
			$db->setQuery($query);
			$setting_res = $db->loadResult();
			$dispenable = unserialize($setting_res);

			foreach ($arrVideoId['cid'] as $videoid)
			{
				// Query is to display recent videos in home page
				$query->clear()
						->select(array('d.email', 'b.seo_category', 'a.seotitle', 'e.catid', 'a.id', 'd.username'))
						->from('#__hdflv_upload a')
						->leftJoin('#__users d ON a.memberid=d.id')
						->leftJoin('#__hdflv_video_category e ON e.vid=a.id')
						->leftJoin('#__hdflv_category b ON e.catid=b.id')
						->where($db->quoteName('a.id') . ' = ' . $db->quote($videoid))
						->group($db->escape('e.vid'));
				$db->setQuery($query);
				$user_details = $db->loadObject();

				// For SEO settings
				$seoOption = $dispenable['seo_option'];

				if ($seoOption == 1)
				{
					$featureCategoryVal = "category=" . $user_details->seo_category;
					$featureVideoVal = "video=" . $user_details->seotitle;
				}
				else
				{
					$featureCategoryVal = "catid=" . $user_details->catid;
					$featureVideoVal = "id=" . $user_details->id;
				}

				$mailer->addRecipient($user_details->email);
				$subject = JText::_('HDVS_VIDEO_APPROVED_BY_ADMIN');
				$baseurl = str_replace('administrator/', '', JURI::base());
				$video_url = $baseurl . 'index.php?option=com_contushdvideoshare&view=player&' . $featureCategoryVal . '&' . $featureVideoVal;
				$video_url = str_replace('administrator/', '', $video_url);
				$filepath = file_get_contents($baseurl . '/components/com_contushdvideoshare/emailtemplate/approveadmin.html');
				$baseurl_replace = str_replace("{baseurl}", $baseurl, $filepath);
				$site_name_replace = str_replace("{site_name}", $site_name, $baseurl_replace);
				$username_replace = str_replace("{username}", $user_details->username, $site_name_replace);
				$subject_replace = str_replace("{approved}", $subject, $username_replace);
				$message = str_replace("{video_url}", $video_url, $subject_replace);
				$mailer->isHTML(true);
				$mailer->setSubject($subject);
				$mailer->Encoding = 'base64';
				$mailer->setBody($message);
				$mailer->Send();
			}

			$publish = 1;
		}
		elseif ($arrVideoId['task'] == 'trash')
		{
			$publish = -2;
			$msg = 'Trashed Successfully';
		}
		else
		{
			$msg = 'Unpublished successfully';
			$publish = 0;
		}

		$objAdminVideosTable = &$this->getTable('adminvideos');
		$objAdminVideosTable->publish($arrVideoId['cid'], $publish);
		$strRedirectPage = 'index.php?layout=adminvideos&option=' . JRequest::getVar('option') . '&user=' . JRequest::getVar('user');
		$mainframe->redirect($strRedirectPage, $msg, 'message');
	}

	/**
	 * Function to save videos
	 * 
	 * @param   string  $task  action to be performed
	 * 
	 * @return  savevideos
	 */
	public function savevideos($task)
	{
		global $option, $mainframe;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$userTypeRedirect = (JRequest::getVar('user', '', 'get') == 'admin') ? "&user=" . JRequest::getVar('user', '', 'get') : "";

		//  To get an instance of the adminvideos table object
		$rs_saveupload = JTable::getInstance('adminvideos', 'Table');
		$arrCatId = JRequest::getVar('cid', array(0), '', 'array');
		$strCatId = $arrCatId[0];
		$rs_saveupload->load($strCatId);
		$createddate = date("Y-m-d h:m:s");

		// Variable initialization
		$arrFormData = JRequest::get('post');
		$embedcode = JRequest::getVar('embedcode', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$arrFormData['embedcode'] = $embedcode;
		$fileoption = $arrFormData['fileoption'];

		if (trim($arrFormData['seotitle']) == '')
		{
			$arrFormData['seotitle'] = $arrFormData['title'];
		}
		
		$arrFormData['seotitle'] = JApplication::stringURLSafe($arrFormData['seotitle']);
		
		if (trim(str_replace('-', '', $arrFormData['seotitle'])) == '')
		{
			$arrFormData['seotitle'] = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		$table = $this->getTable('adminvideos');

		while ($table->load(array('seotitle' => $arrFormData['seotitle'])) && empty($arrFormData['id']))
		{
			$arrFormData['seotitle'] = JString::increment($arrFormData['seotitle'], 'dash');
		}

		if (empty($arrFormData['id']) && empty($arrFormData['ordering']))
		{
			$query->clear()
				->select('count(ordering)')
				->from('#__hdflv_upload');
			$db->setQuery($query);
			$arrFormData['ordering'] = $db->loadResult();
		}

		$description = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$arrFormData['description'] = $description;

		// Object to bind to the instance
		if (!$rs_saveupload->bind($arrFormData))
		{
			JError::raiseWarning(500, $rs_saveupload->getError());
		}

		// Get and assign video url
		if (isset($rs_saveupload->videourl))
		{
			if ($rs_saveupload->videourl != "")
			{
				$rs_saveupload->videourl = trim($rs_saveupload->videourl);
			}
		}

		//  Inserts a new row if id is zero or updates an existing row in the hdflv_upload table
		if (!$rs_saveupload->store())
		{
			JError::raiseWarning(500, $rs_saveupload->getError());
		}

		// Check in the item
		$rs_saveupload->checkin();
		$idval = $rs_saveupload->id;

		// Uploading videos type : URL
		if ($fileoption == 'Url')
		{
			require_once FVPATH . DS . 'helpers' . DS . 'uploadurl.php';
			UploadUrlHelper::uploadUrl($arrFormData, $idval);
		}

		// Uploading videos type : YOUTUBE
		if ($fileoption == "Youtube")
		{
			require_once FVPATH . DS . 'helpers' . DS . 'uploadyoutube.php';
			UploadYouTubeHelper::uploadYouTube($arrFormData, $idval);
		}

		// Uploading videos type : Embed
		if ($fileoption == "Embed")
		{
			require_once FVPATH . DS . 'helpers' . DS . 'uploadembed.php';
			UploadEmbedHelper::uploadEmbed($arrFormData, $idval);
		}

		// Uploading videos type : FILE
		if ($arrFormData['fileoption'] == "File")
		{
			require_once FVPATH . DS . 'helpers' . DS . 'uploadfile.php';
			UploadFileHelper::uploadFile($arrFormData, $idval);
		}

		// Uploading videos type : FFMPEG
		if ($fileoption == "FFmpeg")
		{
			require_once FVPATH . DS . 'helpers' . DS . 'uploadffmpeg.php';
			UploadFfmpegHelper::uploadFfmpeg($arrFormData, $idval);
		}

		$catid = JRequest::getVar('playlistid');

		// Query to update created date
		$query->clear()
				->update($db->quoteName('#__hdflv_upload'))
				->set($db->quoteName('created_date') . ' = ' . $db->quote($createddate))
				->where($db->quoteName('id') . ' = ' . $db->quote($idval));
		$db->setQuery($query);
		$db->query();

		// Query to find the existing of category for video
		$query->clear()
				->select('count(vid)')
				->from('#__hdflv_video_category')
				->where($db->quoteName('vid') . ' = ' . $db->quote($idval));
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($total != 0)
		{
			$query->clear()
					->update($db->quoteName('#__hdflv_video_category'))
					->set($db->quoteName('catid') . ' = ' . $db->quote($catid))
					->where($db->quoteName('vid') . ' = ' . $db->quote($idval));
		}
		else
		{
			$values = array($db->quote($idval), $db->quote($catid));
			$query->clear()
					->insert($db->quoteName('#__hdflv_video_category'))
					->values(implode(',', $values));
		}

		$db->setQuery($query);
		$db->query();

		switch ($task)
		{
			case 'applyvideos':
				$link = 'index.php?option=' . $option . '&layout=adminvideos&task=editvideos' . $userTypeRedirect . '&cid[]=' . $rs_saveupload->id;
				break;
			case 'savevideoupload':
			default:
				$link = 'index.php?option=' . $option . '&layout=adminvideos' . $userTypeRedirect;
				break;
		}

		$msg = 'Saved Successfully';

		// Set to redirect
		$mainframe->redirect($link, $msg, 'message');
	}

	/**
	 * Function to make video as featured/unfeatured
	 * 
	 * @param   array  $arrVideoId  video detail array
	 * 
	 * @return  featuredvideo
	 */
	public function featuredvideo($arrVideoId)
	{
		global $mainframe;
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		if ($arrVideoId['task'] == "featured")
		{
			$publish = 1;
		}
		else
		{
			$publish = 0;
		}

		$msg = 'Updated Successfully';
		$strVideoIds = implode(',', $arrVideoId['cid']);
		$query->clear()
				->update($db->quoteName('#__hdflv_upload'))
				->set($db->quoteName('featured') . ' = ' . $db->quote($publish))
				->where($db->quoteName('id') . ' IN (' . $strVideoIds . ')');
		$db->setQuery($query);
		$db->query();
		$strRedirectPage = 'index.php?layout=adminvideos&option=' . JRequest::getVar('option') . '&user=' . JRequest::getVar('user');
		$mainframe->redirect($strRedirectPage, $msg, 'message');
	}

	/**
	 * Function to display comments in video grid view
	 * 
	 * @return  featuredvideo
	 */
	public function getcomment()
	{
		// Variable initialization
		global $option, $mainframe, $db;
		$query = $db->getQuery(true);
		$sub = $db->getQuery(true);
		$commentId = JRequest::getVar('cmtid', '', 'get', 'int');
		$id = JRequest::getVar('id', '', 'get', 'int');

		// For pagination
		$limit = $mainframe->getUserStateFromRequest($option . '.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option . 'limitstart', 'limitstart', 0, 'int');

		// Query for delete the comments
		if ($commentId)
		{
			$query->clear()
					->delete($db->quoteName('#__hdflv_comments'))
					->where($db->quoteName('id') . ' = ' . $db->quote($commentId) . ' OR ' . $db->quoteName('parentid') . ' = ' . $db->quote($commentId));
			$db->setQuery($query);
			$db->query();

			// Message for deleting comment
			$mainframe->enqueueMessage('Comment Successfully Deleted');
		}

		$id = JRequest::getVar('id', '', 'get', 'int');
		$query->clear()
				->select('COUNT(id)')
				->from('#__hdflv_comments')
				->where($db->quoteName('videoid') . ' = ' . $db->quote($id));
		$db->setQuery($query);
		$db->query();
		$commentcount = $db->getNumRows();

		if (!$commentcount)
		{
			$strRedirectPage = 'index.php?layout=adminvideos&option=' . JRequest::getVar('option') . '&user=' . JRequest::getVar('user');
			$mainframe->redirect($strRedirectPage);
		}

		// Query is to display the comments posted for particular video
	
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
		$db->query();
		$commentTotal = $db->getNumRows();

		$pageNav = new JPagination($commentTotal, $limitstart, $limit);
		$db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$comment = $db->loadObjectList();

		$query->clear()
				->select(array('title'))
				->from('#__hdflv_upload')
				->where($db->quoteName('id') . ' = ' . $db->quote($id));
		$db->setQuery($query);
		$videoTitle = $db->loadResult();

		// Display the last database error message in a standard format
		if ($db->getErrorNum())
		{
			JError::raiseWarning($db->getErrorNum(), $db->stderr());
		}

		$comment_result = array('pageNav' => $pageNav, 'limitstart' => $limitstart, 'comment' => $comment, 'videotitle' => $videoTitle);

		return $comment_result;
	}

	/**
	 * Function to get comments count
	 * 
	 * @param   int  $videoId  video id
	 * 
	 * @return  featuredvideo
	 */
	public function getCommentcount($videoId)
	{
		// Variable initialization
		global $db;
		$query = $db->getQuery(true);
		$query->clear()
				->select(array('count(id)'))
				->from('#__hdflv_comments')
				->where($db->quoteName('videoid') . ' = ' . $db->quote($videoId));
		$db->setQuery($query);
		$commentCount = $db->loadResult();

		return $commentCount;
	}
}
