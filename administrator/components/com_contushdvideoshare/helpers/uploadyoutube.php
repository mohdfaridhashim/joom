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
defined('_JEXEC') or die;

/**
 * Admin UploadYouTubeHelper class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class UploadYouTubeHelper
{
	/**
	 * Function to upload youtube type videos
	 * 
	 * @param   array  $arrFormData  post content
	 * @param   int    $idval        video id
	 *
	 * @return	uploadYouTube
	 */
	public function uploadYouTube($arrFormData, $idval)
	{
		$videourl = strrev($arrFormData['videourl-value']);
		$str1 = explode('administrator', JURI::base());
		$videoshareurl = $str1[0] . "index.php?option=com_contushdvideoshare&view=videourl";
		$timeout = $header = $hdurl = $tags = "";

		// Check video url is youtube
		if (strpos($videourl, 'youtube') > 0)
		{
			$imgstr = explode("v=", $videourl);
			$imgval = explode("&", $imgstr[1]);
			$previewurl = "http://img.youtube.com/vi/" . $imgval[0] . "/maxresdefault.jpg";
			$img = "http://img.youtube.com/vi/" . $imgval[0] . "/mqdefault.jpg";
		}
		elseif (strpos($videourl, 'youtu.be') > 0)
		{
			$imgstr = explode("/", $videourl);
			$previewurl = "http://img.youtube.com/vi/" . $imgstr[3] . "/maxresdefault.jpg";
			$img = "http://img.youtube.com/vi/" . $imgstr[3] . "/mqdefault.jpg";
			$videourl = "http://www.youtube.com/watch?v=" . $imgstr[3];
		}

		// Check video url is youtube
		elseif (strpos($videourl, 'vimeo') > 0)
		{
			$split = explode("/", $videourl);

			if (ini_get('allow_url_fopen'))
			{
				$doc = new DOMDocument;
				$doc->load('http://vimeo.com/api/v2/video/' . $split[3] . '.xml');
				$videotags = $doc->getElementsByTagName('video');

				foreach ($videotags as $videotag)
				{
					$imgnode = $videotag->getElementsByTagName('thumbnail_medium');
					$img = $imgnode->item(0)->nodeValue;
					$vidtags = $videotag->getElementsByTagName('tags');
					$tags = $vidtags->item(0)->nodeValue;
				}
			}
			else
			{
				$url = "http://vimeo.com/api/v2/video/" . $split[3] . ".xml";
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				$result = curl_exec($curl);
				curl_close($curl);
				$xml = simplexml_load_string($result);
				$img = $xml->video->thumbnail_medium;
				$tags = $xml->video->tags;
			}
		}

		// Check video url is dailymotion
		elseif (strpos($videourl, 'dailymotion') > 0)
		{
			$split = explode("/", $videourl);
			$split_id = explode("_", $split[4]);
			$img = $previewurl = 'http://www.dailymotion.com/thumbnail/video/' . $split_id[0];
		}

		// Check video url is viddler
		elseif (strpos($videourl, 'viddler') > 0)
		{
			$imgstr = explode("/", $videourl);
			$img = $previewurl = "http://cdn-thumbs.viddler.com/thumbnail_2_" . $imgstr[4] . "_v1.jpg";
		}

		// Check video url is site url
		else
		{
			// Is cURL exit or not
			if (!function_exists('curl_init'))
			{
				echo "<script> alert('Sorry cURL is not installed!');window.history.go(-1); </script>\n";
				exit();
			}

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $videoshareurl . '&url=' . $videourl . '&imageurl=' . $videourl);
			curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($curl, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0", rand(4, 5)));
			curl_setopt($curl, CURLOPT_HEADER, (int) $header);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			$videoshareurl_location = curl_exec($curl);
			curl_close($curl);
			$location = explode('&', $videoshareurl_location);
			$location2 = explode('location2=', $location[2]);
			$imageurl = explode('imageurl=', $location[4]);
			$img = $imageurl[1];
			$hdurl = "";

			if ($location2[1] != "")
			{
				$hdurl = $videourl;
			}
		}

		// Assign streameroption
		$streamer_option = $arrFormData['streameroption-value'];
		$fileoption = $arrFormData['fileoption'];

		if(!empty($arrFormData['tags']))
		{
			$tags =  $arrFormData['tags'];
		}

		// Update fields
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Fields to update.
		$fields = array(
			$db->quoteName('streameroption') . '=\'' . $streamer_option . '\'',
			$db->quoteName('filepath') . '=\'' . $fileoption . '\'',
			$db->quoteName('videourl') . '=\'' . $videourl . '\'',
			$db->quoteName('thumburl') . '=\'' . $img . '\'',
			$db->quoteName('previewurl') . '=\'' . $previewurl . '\'',
			$db->quoteName('hdurl') . '=\'' . $hdurl . '\'',
			$db->quoteName('tags') . '=\'' . $tags . '\''
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('id') . '=' . $idval
		);

		//  Update streameroption,streamerpath,etc
		$query->clear()
				->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->query();

		//  Get and set subtitle1 of the video
		$strSrtFile1 = $arrFormData['subtitle_video_srt1form-value'];
		$arrSrtFile1 = explode('uploads/', $strSrtFile1);

		if (isset($arrSrtFile1[1]))
		{
			$strSrtFile1 = $arrSrtFile1[1];
		}

		//  Get and set subtitle2 of the video
		$strSrtFile2 = $arrFormData['subtitle_video_srt2form-value'];
		$arrSrtFile2 = explode('uploads/', $strSrtFile2);

		if (isset($arrSrtFile2[1]))
		{
			$strSrtFile2 = $arrSrtFile2[1];
		}

		$subtile_lang1 = $arrFormData['subtile_lang1'];
		$subtile_lang2 = $arrFormData['subtile_lang2'];

		// Get upload helper file to upload thumb
		require_once FVPATH . DS . 'helpers' . DS . 'uploadfile.php';
		UploadFileHelper::uploadVideoProcessing(
				$subtile_lang1, $subtile_lang2, $idval, '', '', '',
				$strSrtFile1, $strSrtFile2, '', $arrFormData['newupload'], $fileoption
				);

		// Delete temp file
		if ($strSrtFile1 != '')
		{
			UploadFileHelper::unlinkUploadedTmpFiles($strSrtFile1);
		}

		if ($strSrtFile2 != '')
		{
			UploadFileHelper::unlinkUploadedTmpFiles($strSrtFile2);
		}
	}
}
