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

// Import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * Admin videos controller class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareControlleradminvideos extends ContusvideoshareController
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareControlleradminvideos		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view = $this->getView('showvideos');

		if ($model = $this->getModel('showvideos'))
		{
			$view->setModel($model, true);
		}

		$view->setLayout('showvideoslayout');
		$view->showvideos();
	}

	/** 
	 * Function to set layout and model for add action 
	 * 
	 * @return  addvideos
	 */
	public function addvideos()
	{
		$view = $this->getView('adminvideos');

		if ($model = $this->getModel('addvideos'))
		{
			$view->setModel($model, true);
		}

		$view->setLayout('adminvideoslayout');
		$view->adminvideos();
	}

	/** 
	 * Function to set layout and model for edit action 
	 * 
	 * @return  editvideos
	 */
	public function editvideos()
	{
		$view = $this->getView('adminvideos');

		if ($model = $this->getModel('editvideos'))
		{
			$view->setModel($model, true);
		}

		$view->setLayout('adminvideoslayout');
		$view->editvideos();
	}

	/** 
	 * Function to set model for save action 
	 * 
	 * @return  savevideos
	 */
	public function savevideos()
	{
		if ($model = $this->getModel('showvideos'))
		{
			$model->savevideos(JRequest::getVar('task'));
		}
	}

	/** 
	 * Function to set model for apply action 
	 * 
	 * @return  applyvideos
	 */
	public function applyvideos()
	{
		if ($model = $this->getModel('showvideos'))
		{
			$model->savevideos(JRequest::getVar('task'));
		}
	}

	/** 
	 * Function to set model for remove action 
	 * 
	 * @return  removevideos
	 */
	public function removevideos()
	{
		if ($model = $this->getModel('editvideos'))
		{
			$model->removevideos();
		}
	}

	/** 
	 * Function to set layout for cancel action
	 * 
	 * @return  CANCEL7
	 */
	public function CANCEL7()
	{
		$view = $this->getView('showvideos');

		if ($model = $this->getModel('showvideos'))
		{
			$view->setModel($model, true);
		}

		$view->setLayout('showvideoslayout');
		$view->showvideos();
	}

	/** 
	 * Function to set redirect for comment page cancel action
	 * 
	 * @return  Commentcancel
	 */
	public function Commentcancel()
	{
		$option = JRequest::getCmd('option');
		$user = JRequest::getCmd('user');
		$userUrl = ($user == 'admin') ? "&user=$user" : "";
		$redirectUrl = 'index.php?option=' . $option . '&layout=adminvideos' . $userUrl;
		$this->setRedirect($redirectUrl);
	}

	/** 
	 * Function to make videos as featured
	 * 
	 * @return  featured
	 */
	public function featured()
	{
		$detail = JRequest::get('POST');
		$model = $this->getModel('showvideos');
		$model->featuredvideo($detail);
	}

	/** 
	 * Function to make videos as unfeatured
	 * 
	 * @return  unfeatured
	 */
	public function unfeatured()
	{
		$this->featured();
	}

	/** 
	 * Function to publish videos
	 * 
	 * @return  publish
	 */
	public function publish()
	{
		$detail = JRequest::get('POST');
		$model = $this->getModel('showvideos');
		$model->changevideostatus($detail);
	}

	/** 
	 * Function to unpublish videos
	 * 
	 * @return  unpublish
	 */
	public function unpublish()
	{
		$detail = JRequest::get('POST');
		$model = $this->getModel('showvideos');
		$model->changevideostatus($detail);
	}

	/** 
	 * Function to upload file processing
	 * 
	 * @return  uploadfile
	 */
	public function uploadfile()
	{
		$model = $this->getModel('uploadvideo');
		$model->fileupload();
	}

	/** 
	 * Function to trash videos
	 * 
	 * @return  trash
	 */
	public function trash()
	{
		$detail = JRequest::get('POST');
		$model = $this->getModel('showvideos');
		$model->changevideostatus($detail);
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
}
