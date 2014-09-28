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
 * Admin UploadUrlHelper class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class UploadUrlHelper
{
	/**
	 * Function to upload url type videos
	 * 
	 * @param   array  $arrFormData  post content
	 * @param   int    $idval        video id
	 *
	 * @return	uploadUrl
	 */
	public function uploadUrl($arrFormData, $idval)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$videourl = $hdurl = "";
		$baseUrl = str_replace("administrator/", "", JURI::base());
		$thumburl = $baseUrl . 'components/com_contushdvideoshare/videos/default_thumb.jpg';
		$previewurl = $baseUrl . 'components/com_contushdvideoshare/videos/default_preview.jpg';

		// Assign streameroption
		$streamer_option = $arrFormData['streameroption-value'];
		$fileoption = $arrFormData['fileoption'];

		// Assign video url
		if ($arrFormData['videourl-value'] != "")
		{
			$videourl = strrev($arrFormData['videourl-value']);
		}

		// Assign hd url
		if ($arrFormData['hdurl-value'] != "")
		{
			$hdurl = strrev($arrFormData['hdurl-value']);
		}

		// Assign thumb image url
		if ($arrFormData['thumburl-value'] != "")
		{
			$thumburl = strrev($arrFormData['thumburl-value']);
		}

		// Assign preview image url
		if ($arrFormData['previewurl-value'] != "")
		{
			$previewurl = strrev($arrFormData['previewurl-value']);
		}

		// Assign streamer path
		$streamer_path = ($arrFormData['streamerpath-value'] != '') ? $arrFormData['streamerpath-value'] : '';
		$isLive = $arrFormData['islive-value'];

		// Fields to update.
		$fields = array(
			$db->quoteName('streameroption') . '=\'' . $streamer_option . '\'',
			$db->quoteName('streamerpath') . '=\'' . $streamer_path . '\'',
			$db->quoteName('filepath') . '=\'' . $fileoption . '\'',
			$db->quoteName('videourl') . '=\'' . $videourl . '\'',
			$db->quoteName('thumburl') . '=\'' . $thumburl . '\'',
			$db->quoteName('previewurl') . '=\'' . $previewurl . '\'',
			$db->quoteName('hdurl') . '=\'' . $hdurl . '\'',
			$db->quoteName('islive') . '=\'' . $isLive . '\''
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
