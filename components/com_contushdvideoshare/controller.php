<?php
/**
 * Controller file for Contus HD Video Share
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

// Import Joomla controller library
jimport('joomla.application.component.controller');

// HD Video Share component main controller
if (version_compare(JVERSION, '1.6.0', 'ge'))
{
	$jlang = JFactory::getLanguage();
	$jlang->load('com_contushdvideoshare', JPATH_SITE, $jlang->get('tag'), true);
	$jlang->load('com_contushdvideoshare', JPATH_SITE, null, true);
}

/**
 * Featured Videos Module installer file
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareController extends ContusvideoshareController
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareController		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('dispenable')
				->from('#__hdflv_site_settings')
				->where($db->quoteName('published') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$rows = $db->loadResult();
		$dispenable = unserialize($rows);
		//print_r($dispenable);
		//echo $_GET['opt'];
		// if(isset($_GET['opt']) && $_GET['opt'] == 2)
		// {
		// 	$dispenable['categoryplayer'] = 1;
		// 	echo $_GET['opt'];
		// 	print_r($dispenable);
		// }
		define('USER_LOGIN', $dispenable['user_login']);
		$viewName = JRequest::getVar('view');

		if ($viewName != "languagexml" && $viewName != "configxml" && $viewName != "playxml" && $viewName != "googlead")
		{
			$document = JFactory::getDocument();
			$document->addScript(JURI::base() . 'components/com_contushdvideoshare/js/jquery.js');
			$document->addScript(JURI::base() . "components/com_contushdvideoshare/js/htmltooltip.js");
			$lang = JFactory::getLanguage();
			$langDirection = (bool) $lang->isRTL();

			if ($langDirection == 1)
			{
				$document->addStyleSheet(JURI::base() . 'components/com_contushdvideoshare/css/stylesheet_rtl.min.css');
			}
			else
			{
				$document->addStyleSheet(JURI::base() . 'components/com_contushdvideoshare/css/stylesheet.min.css');
			}
		}

		if ($viewName == "" || $viewName == "index")
		{
			$viewName = 'player';
		}

		$this->getdisplay($viewName);
	}

	/**
	 * Function to increase view count of a video
	 * 
	 * @param   int  $vid  Video id
	 * 
	 * @return  videohitCount_function
	 */
	public static function videohitCount_function($vid)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->clear()
				->update($db->quoteName('#__hdflv_upload'))
				->set($db->quoteName('times_viewed') . ' = 1+times_viewed')
				->where($db->quoteName('id') . ' = ' . $db->quote($vid));
		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Function to send report of a video
	 * 
	 * @return  sendreport
	 */
	public function sendreport()
	{
		$db = JFactory::getDBO();
		$repmsg = JRequest::getVar('reportmsg');
		$videoid = JRequest::getInt('videoid');
		$user = JFactory::getUser();
		$memberid = $user->get('id');

		// Alert admin regarding new video upload
		// Define joomla mailer
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();

		$query = $db->getQuery(true);

		//  Query is to display recent videos in home page
		$query->select(array('email','username'))
				->from('#__users')
				->where($db->quoteName('id') . ' = ' . $db->quote($memberid));
		$db->setQuery($query);
		$user_details = $db->loadObject();

		$sender = $config->get('mailfrom');
		$mailer->setSender($user_details->email);
		$featureVideoVal = "id=" . $videoid;
		$mailer->addRecipient($sender);
		$subject = JText::_('HDVS_USER_REPORTED');
		$baseurl = JURI::base();
		$video_url = $baseurl . 'index.php?option=com_contushdvideoshare&view=player&' . $featureVideoVal . '&adminview=true';
		$get_htmlmessage = file_get_contents($baseurl . '/components/com_contushdvideoshare/emailtemplate/reportvideo.html');
		$update_baseurl = str_replace("{baseurl}", $baseurl, $get_htmlmessage);
		$update_username = str_replace("{username}", $user_details->username, $update_baseurl);
		$update_rptmsg = str_replace("{reportmsg}", $repmsg, $update_username);
		$message = str_replace("{video_url}", $video_url, $update_rptmsg);
		$mailer->isHTML(true);
		$mailer->setSubject($subject);
		$mailer->Encoding = 'base64';
		$mailer->setBody($message);
		$send = $mailer->Send();

		if ($send !== true)
		{
			echo 'Error sending email: ' . $send->message;
		}
		else
		{
			echo JText::_('HDVS_REPORTED_SUCCESS');
		}
	}

	/**
	 * Function to assign model for the view
	 * 
	 * @param   string  $viewName  view name
	 * 
	 * @return  getdisplay
	 */
	public function getdisplay($viewName = "index")
	{
		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$view = $this->getmodView($viewName, $viewType);
		$model = $this->getModel($viewName, 'Modelcontushdvideoshare');

		if (!JError::isError($model))
		{
			$view->setModel($model, true);
		}

		$view->display($cachable = false, $urlparams = false);
	}

	/**
	 * Function to assign view if view not exist
	 * 
	 * @param   string  $name    view name
	 * @param   string  $type    view type
	 * @param   string  $prefix  view prefix
	 * @param   array   $config  config array
	 * 
	 * @return  &getmodView
	 */
	public function &getmodView($name = '', $type = '', $prefix = '', $config = array())
	{
		static $views;

		if (empty($prefix))
		{
			$prefix = $this->getName() . 'View';
		}

		if (empty($views[$name]))
		{
			if (version_compare(JVERSION, '1.6.0', 'ge'))
			{
				if ($view = $this->createView($name, $prefix, $type, $config))
				{
					$views[$name] = & $view;
				}
				else
				{
					header("Location:index.php?option=com_contushdvideoshare&view=player&itemid=0");
				}
			}
			else
			{
				if ($view = $this->_createView($name, $prefix, $type, $config))
				{
					$views[$name] = & $view;
				}
				else
				{
					header("Location:index.php?option=com_contushdvideoshare&view=player&itemid=0");
				}
			}
		}

		return $views[$name];
	}

	/**
	 * Function for adxml view
	 * 
	 * @return  adsxml
	 */
	public function adsxml()
	{
		$view = $this->getView('adsxml');

		if ($model = $this->getModel('adsxml'))
		{
			// Push the model into the view (as default)
			// Second parameter indicates that it is the default model for the view
			$view->setModel($model, true);
		}

		$view->display();
	}

	/**
	 * Function for imaadxml view
	 * 
	 * @return  imaadxml
	 */
	public function imaadxml()
	{
		$view = $this->getView('imaadxml');

		if ($model = $this->getModel('imaadxml'))
		{
			// Push the model into the view (as default)
			// Second parameter indicates that it is the default model for the view
			$view->setModel($model, true);
		}

		$view->display();
	}

	/**
	 * Function for impressionclicks view
	 * 
	 * @return  impressionclicks
	 */
	public function impressionclicks()
	{
		$view = $this->getView('impressionclicks');

		if ($model = $this->getModel('impressionclicks'))
		{
			$view->setModel($model, true);
		}

		$view->display();
	}

	/**
	 * Function for videourl view
	 * 
	 * @return  videourl
	 */
	public function videourl()
	{
		$view = $this->getView('videourl');

		if ($model = $this->getModel('videourl'))
		{
			$view->setModel($model, true);
		}

		$view->getvideourl();
	}

	/**
	 * Function for assigning model for upload method
	 * 
	 * @return  uploadfile
	 */
	public function uploadfile()
	{
		$model = $this->getModel('uploadvideo');
		$model->fileupload();
	}

	/**
	 * Function for download option in player
	 * 
	 * @return  downloadfile
	 */
	public function downloadfile()
	{
		$url = JRequest::getVar('f');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select(array('filepath', 'videourl'))
				->from('#__hdflv_upload')
				->where($db->quoteName('videourl') . ' = ' . $db->quote($url));
		$db->setQuery($query);
		$video_details = $db->loadObject();
		$filename = JPATH_COMPONENT . "/videos/" . $video_details->videourl;

		if (file_exists($filename))
		{
			header('Content-disposition: attachment; filename=' . basename($filename));
			readfile($filename);
		}
	}

	/**
	 * Function to get youtube videos detail
	 *
	 * @return  youtubeurl
	 */
	public function youtubeurl()
	{
		$application = JFactory::getApplication();
	
		if (version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$videourl = JRequest::getVar('videourl');
		}
		else
		{
			$videourl = JRequest::getVar('videourl', '', 'get', 'string');
		}
	
		$videourl = strrev($videourl);
		$act_filepath = addslashes(trim($videourl));
	
		if (!empty($act_filepath))
		{
			if (strpos($act_filepath, 'youtube') > 0 || strpos($act_filepath, 'youtu.be') > 0)
			{
				if (strpos($act_filepath, 'youtube') > 0)
				{
					$imgstr = explode("v=", $act_filepath);
					$imgval = explode("&", $imgstr[1]);
					$match = $imgval[0];
				}
				elseif (strpos($act_filepath, 'youtu.be') > 0)
				{
					$imgstr = explode("/", $act_filepath);
					$match = $imgstr[3];
					$act_filepath = "http://www.youtube.com/watch?v=" . $imgstr[3];
				}
	
				$youtube_data = $this->hd_GetSingleYoutubeVideo($match);
	
				if ($youtube_data)
				{
					$act['title'] = $youtube_data['title'];
	
					if (isset($youtube_data['thumbnail_url']))
					{
						$act['thumb'] = $youtube_data['thumbnail_url'];
					}
	
					$act['urlpath'] = $act_filepath;
	
					if (isset($youtube_data['description']))
					{
						$act['description'] = $youtube_data['description'];
					}
	
					if (isset($youtube_data['tags']))
					{
						$act['tags'] = $youtube_data['tags'];
					}
				}
				else
				{
					$application->enqueueMessage('Could not retrieve Youtube video information');
				}
			}
			else
			{
				$act['urlpath'] = $act_filepath;
				$application->enqueueMessage('URL entered is not a valid Youtube Url');
			}
	
			echo json_encode($act);
			exit;
	
			return $act;
		}
	}

	/**
	 * Function to get youtube videos detail
	 *
	 * @param   var  $youtube_media  Youtube video id
	 *
	 * @return  hd_GetSingleYoutubeVideo
	 */
	public function hd_GetSingleYoutubeVideo($youtube_media)
	{
		if ($youtube_media == '')
		{
			return;
		}
	
		$url = 'http://gdata.youtube.com/feeds/api/videos/' . $youtube_media;
		$ytb = $this->hd_ParseYoutubeDetails($this->hd_GetYoutubePage($url));
	
		return $ytb[0];
	}
	
	/**
	 * Function to get youtube videos detail
	 *
	 * @param   var  $ytVideoXML  URL of a youtube video
	 *
	 * @return  hd_ParseYoutubeDetails
	 */
	public function hd_ParseYoutubeDetails($ytVideoXML)
	{
		// Create parser, fill it with xml then delete it
		$yt_xml_parser = xml_parser_create();
		xml_parse_into_struct($yt_xml_parser, $ytVideoXML, $yt_vals);
		xml_parser_free($yt_xml_parser);
	
		// Init individual entry array and list array
		$yt_video = $yt_vidlist = array();
		$is_entry = true;
		$is_author = false;
	
		foreach ($yt_vals as $yt_elem)
		{
			// If no entry is being processed and tag is not start of entry, skip tag
	
			if (!$is_entry && $yt_elem['tag'] != 'ENTRY')
			{
				continue;
			}
	
			// Processed tag
			switch ($yt_elem['tag'])
			{
				case 'ENTRY' :
	
					if ($yt_elem['type'] == 'open')
					{
						$is_entry = true;
						$yt_video = array();
					}
					else
					{
						$yt_vidlist[] = $yt_video;
						$is_entry = false;
					}
	
					break;
				case 'ID' :
					$yt_video['id'] = substr($yt_elem['value'], -11);
					$yt_video['link'] = $yt_elem['value'];
					break;
				case 'PUBLISHED' :
					$yt_video['published'] = substr($yt_elem['value'], 0, 10) . ' ' . substr($yt_elem['value'], 11, 8);
					break;
				case 'UPDATED' :
					$yt_video['updated'] = substr($yt_elem['value'], 0, 10) . ' ' . substr($yt_elem['value'], 11, 8);
					break;
				case 'MEDIA:TITLE' :
					$yt_video['title'] = $yt_elem['value'];
					break;
				case 'MEDIA:KEYWORDS' :
	
					if (isset($yt_elem['value']))
					{
						$yt_video['tags'] = $yt_elem['value'];
					}
	
					break;
				case 'MEDIA:DESCRIPTION' :
	
					if (isset($yt_elem['value']))
					{
						$yt_video['description'] = $yt_elem['value'];
					}
	
					break;
				case 'MEDIA:CATEGORY' :
					$yt_video['category'] = $yt_elem['value'];
					break;
				case 'YT:DURATION' :
					$yt_video['duration'] = $yt_elem['attributes'];
					break;
				case 'MEDIA:THUMBNAIL' :
	
					if ($yt_elem['attributes']['HEIGHT'] == 240)
					{
						$yt_video['thumbnail'] = $yt_elem['attributes'];
						$yt_video['thumbnail_url'] = $yt_elem['attributes']['URL'];
					}
	
					break;
				case 'YT:STATISTICS' :
					$yt_video['viewed'] = $yt_elem['attributes']['VIEWCOUNT'];
					break;
				case 'GD:RATING' :
					$yt_video['rating'] = $yt_elem['attributes'];
					break;
				case 'AUTHOR' :
					$is_author = ($yt_elem['type'] == 'open');
					break;
				case 'NAME' :
					if ($is_author)
					{
						$yt_video['author_name'] = $yt_elem['value'];
					}
					break;
				case 'URI' :
					if ($is_author)
					{
						$yt_video['author_uri'] = $yt_elem['value'];
					}
					break;
				default :
			}
		}
	
		unset($yt_vals);
	
		return $yt_vidlist;
	}
	
	/**
	 * Function to get youtube videos detail
	 *
	 * @param   var  $url  Function to get youtube video detail
	 *
	 * @return  hd_GetYoutubePage
	 */
	public function hd_GetYoutubePage($url)
	{
		// Try to use curl first
	
		if (function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$xml = curl_exec($ch);
			curl_close($ch);
		}
		// If not found, try to use file_get_contents (requires php > 4.3.0 and allow_url_fopen)
		else
		{
			$xml = @file_get_contents($url);
		}
	
		return $xml;
	}

	/**
	 * Function for email option in player
	 * 
	 * @return  emailuser
	 */
	public function emailuser()
	{
		$to = JRequest::getVar('to');
		$from = JRequest::getVar('from');
		$video_url = JRequest::getVar('url');
		$body = JRequest::getVar('Note');
		$toEmailArray = explode ( '@', $to );
		$toUserName = (! empty ( $title )) ? $title : ucfirst ( $toEmailArray [0] );
		$fromEmailArray = explode ( '@', $from );
		$fromUserName = (! empty ( $title )) ? $title : ucfirst ( $fromEmailArray [0] );
		$subject = $fromUserName . ' ' . JText::_('HDVS_SENT_A_VIDEO') . '"' . JRequest::getVar('title') . '"';

		$mailer = JFactory::getMailer();
		$mailer->setSender($from);
		$mailer->addRecipient($to);
		$baseurl = JURI::base();
		$config = JFactory::getConfig();
		$site_name = $config->get('sitename');
		$get_htmlmessage = file_get_contents($baseurl . '/components/com_contushdvideoshare/emailtemplate/sharevideo.html');
		$update_baseurl = str_replace("{baseurl}", $baseurl, $get_htmlmessage);
		$site_name_replace = str_replace("{site_name}", $site_name, $update_baseurl);
		$updateToUserName = str_replace("{username}", $toUserName, $site_name_replace);
		$updateFromUserName = str_replace("{fromusername}", $fromUserName, $updateToUserName);
		$message = str_replace("{video_url}", $video_url, $updateFromUserName);
		$mailer->isHTML(true);
		$mailer->setSubject($subject);
		$mailer->Encoding = 'base64';
		$mailer->setBody($message);
		$send = $mailer->Send();

		if ($send !== true)
		{
			echo "success=error";
		}
		else
		{
			echo "success=sent";
		}
		exit;
	}
}
