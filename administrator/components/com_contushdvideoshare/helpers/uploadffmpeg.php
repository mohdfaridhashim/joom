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

// Import Joomla filesystem library
jimport('joomla.filesystem.file');

/**
 * Admin UploadFfmpegHelper class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class UploadFfmpegHelper
{
	/**
	 * Function to upload FFMPEG type videos
	 *
	 * @param   array  $arrFormData  post content
	 * @param   int    $idval        video id
	 * 
	 * @return	uploadFfmpeg
	 */
	public function uploadFfmpeg($arrFormData, $idval)
	{
		$db = JFactory::getDBO();

		// Query for get HEIGTH,WIDTH player size and FFMPEG path from player settings
		$query = $db->getQuery(true);
		$query->clear()
				->select($db->quoteName(array('player_values')))
				->from($db->quoteName('#__hdflv_player_settings'));
		$db->setQuery($query);
		$arrPlayerSettings = $db->loadResult();
		$player_values = unserialize($arrPlayerSettings);

		$query->clear()
				->select($db->quoteName(array('dispenable')))
				->from($db->quoteName('#__hdflv_site_settings'))
				->where($db->quoteName('id') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$setting_res = $db->loadResult();
		$dispenable = unserialize($setting_res);

		require_once FVPATH . DS . 'helpers' . DS . 'uploadfile.php';

		// Check valid record
		if (count($arrPlayerSettings))
			{
			if (( $player_values['height'] % 2) == 0)
			{
				$previewheight = $player_values['height'];
			}
			else
			{
				$previewheight = $player_values['height'] + 1;
			}

			if (( $player_values['width'] % 2) == 0)
			{
				$previewwidth = $player_values['width'];
			}
			else
			{
				$previewwidth = $player_values['width'] + 1;
			}

			// To get ffmpeg path
			if ($player_values['ffmpegpath'])
			{
				$strFfmpegPath = $player_values['ffmpegpath'];
			}
		}

		$fileoption = $arrFormData['fileoption'];
		$ffmpeg_video = $arrFormData['ffmpegform-value'];
		$video_name = explode('uploads/', $ffmpeg_video);

		if (!empty($video_name[1]))
		{
			$strTmpVidName = $video_name[1];

			// FVPATH - temporary path /components/com_contushdvideoshare/images/uploads
			$strTmpPath = FVPATH . DS . "images" . DS . "uploads" . DS . $strTmpVidName;

			// VPATH - target path /components/com_contushdvideoshare/videos
			$strTargetPath = VPATH . DS;
			$exts = uploadFfmpegHelper::getFileExtension($strTmpVidName);
			$strVidTargetPath = $strTargetPath . $idval . "_video" . "." . $exts;

			// Function to move video from temp path to target path
			if (JFile::exists($strTmpPath))
			{
				rename($strTmpPath, $strVidTargetPath);
			}

			$strTmpPath = $strTargetPath . $idval . "_video" . "." . $exts;

			// Function to get FFMPEG video information
			$arrData = uploadFfmpegHelper::getFfmpegVidInfo($strTmpPath, $strFfmpegPath);

			// Get file format
			$hdfile = $arrData['vdo_format'];
			$s3bucket_videurl = $s3bucket_hdurl = $s3bucket_thumburl = $s3bucket_previewurl = 0;

			// To check for HD or Flv or other movies
			if ($hdfile == "h264")
			{
				$exts = uploadFfmpegHelper::getFileExtension($strTmpVidName);
				$video_name = $idval . '_hd' . ".flv";
				$flvpath = $strTargetPath . $idval . '_hd' . ".flv";
				exec($strFfmpegPath . ' ' . "-i" . ' ' . $strTmpPath . ' ' . "-sameq" . ' ' . $strTargetPath . $idval . "_hd.flv");
				exec(
						$strFfmpegPath . " -i " . $strTmpPath . ' ' . "-an -ss 00:00:03 -an -r 1 -s 120x68 -f image2"
						. ' ' . $strTargetPath . $idval . "_thumb.jpeg"
						);
				exec(
						$strFfmpegPath . " -i " . $strTmpPath . ' ' . "-an -ss 00:00:03 -an -r 1 -vframes 1 -y"
						. ' ' . $strTargetPath . $idval . '_preview' . ".jpeg"
						);
				$hd_name = '';

				if ($dispenable['amazons3'] == 1)
				{
					$s3bucket_videurl = $s3bucket_hdurl = $s3bucket_thumburl = $s3bucket_previewurl = 1;
					require_once FVPATH . DS . 'helpers' . DS . 's3_config.php';
					$strVids3TargetPath = $dispenable['amazons3path'] . $video_name;
					$strhdVids3TargetPath = $dispenable['amazons3path'] . $hd_name;
					$strthumbs3TargetPath = $dispenable['amazons3path'] . $idval . '_thumb' . ".jpeg";
					$strpreviews3TargetPath = $dispenable['amazons3path'] . $idval . '_preview' . ".jpeg";

					if ($s3->putObjectFile($flvpath, $bucket, $strVids3TargetPath, S3::ACL_PUBLIC_READ))
					{
						UploadFileHelper::amazons3update($flvpath, $video_name, $idval, 'videourl', $fileoption);
					}
					else
					{
						$s3bucket_videurl = 0;
					}

					if ($s3->putObjectFile($strTargetPath . $idval . '_thumb' . ".jpeg", $bucket, $strthumbs3TargetPath, S3::ACL_PUBLIC_READ))
					{
						UploadFileHelper::amazons3update($strTargetPath . $idval . '_thumb' . ".jpeg", $idval . '_thumb' . ".jpeg", $idval, 'thumburl', $fileoption);
					}
					else
					{
						$s3bucket_thumburl = 0;
					}

					if ($s3->putObjectFile($strTargetPath . $idval . '_preview' . ".jpeg", $bucket, $strpreviews3TargetPath, S3::ACL_PUBLIC_READ))
					{
						UploadFileHelper::amazons3update(
								$strTargetPath . $idval . '_preview' . ".jpeg",
								$idval . '_preview' . ".jpeg", $idval, 'previewurl', $fileoption
								);
					}
					else
					{
						$s3bucket_previewurl = 0;
					}
				}

				if ($s3bucket_videurl == 0)
				{
					// Fields to update.
					$fields = array(
						$db->quoteName('videourl') . '=\'' . $video_name . '\''
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('id') . '=' . $idval
					);
					$query->clear()
							->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
					$db->setQuery($query);
					$db->query();
				}

				if ($s3bucket_hdurl == 0)
				{
					// Fields to update.
					$fields = array(
						$db->quoteName('hdurl') . '=\'' . $hd_name . '\''
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('id') . '=' . $idval
					);
					$query->clear()
							->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
					$db->setQuery($query);
					$db->query();
				}

				if ($s3bucket_thumburl == 0)
				{
					$thumb_name = $idval . '_thumb' . ".jpeg";

					// Fields to update.
					$fields = array(
						$db->quoteName('thumburl') . '=\'' . $thumb_name . '\''
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('id') . '=' . $idval
					);
					$query->clear()
							->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
					$db->setQuery($query);
					$db->query();
				}

				if ($s3bucket_previewurl == 0)
				{
					$preview_name = $idval . '_preview' . ".jpeg";

					// Fields to update.
					$fields = array(
						$db->quoteName('previewurl') . '=\'' . $preview_name . '\''
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('id') . '=' . $idval
					);
					$query->clear()
							->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
					$db->setQuery($query);
					$db->query();
				}
			}
			else
				{
				exec($strFfmpegPath . ' ' . "-i" . ' ' . $strTmpPath . ' ' . "-sameq" . ' ' . $strTargetPath . $idval . "_video.flv");
				exec(
				$strFfmpegPath . " -i " . $strTmpPath . ' ' . "-an -ss 00:00:03 -an -r 1 -s 120x68 -f image2"
				. ' ' . $strTargetPath . $idval . "_thumb.jpeg"
				);
				exec(
				$strFfmpegPath . " -i " . $strTmpPath . ' ' . "-an -ss 00:00:03 -an -r 1 -vframes 1 -y"
				. ' ' . $strTargetPath . $idval . '_preview' . ".jpeg"
				);

				// To get Thumb image & Preview image from the original video file
				$video_name = $idval . '_video' . ".flv";
				$hd_name = "";

				if ($dispenable['amazons3'] == 1)
				{
					$s3bucket_videurl = $s3bucket_hdurl = $s3bucket_thumburl = $s3bucket_previewurl = 1;
					require_once FVPATH . DS . 'helpers' . DS . 's3_config.php';
					$strVids3TargetPath = $dispenable['amazons3path'] . $video_name;
					$strhdVids3TargetPath = $dispenable['amazons3path'] . $hd_name;
					$strthumbs3TargetPath = $dispenable['amazons3path'] . $idval . '_thumb' . ".jpeg";
					$strpreviews3TargetPath = $dispenable['amazons3path'] . $idval . '_preview' . ".jpeg";

					if ($s3->putObjectFile($strTargetPath . $video_name, $bucket, $strVids3TargetPath, S3::ACL_PUBLIC_READ))
				{
						UploadFileHelper::amazons3update($strTargetPath . $video_name, $video_name, $idval, 'videourl', $fileoption);
					}
					else
						{
						$s3bucket_videurl = 0;
					}

					if ($s3->putObjectFile($strTargetPath . $idval . '_thumb' . ".jpeg", $bucket, $strthumbs3TargetPath, S3::ACL_PUBLIC_READ))
				{
						UploadFileHelper::amazons3update(
						$strTargetPath . $idval . '_thumb' . ".jpeg",
						$idval . '_thumb' . ".jpeg", $idval, 'thumburl', $fileoption
						);
					}
					else
						{
						$s3bucket_thumburl = 0;
					}

					if ($s3->putObjectFile($strTargetPath . $idval . '_preview' . ".jpeg", $bucket, $strpreviews3TargetPath, S3::ACL_PUBLIC_READ))
				{
						UploadFileHelper::amazons3update(
								$strTargetPath . $idval . '_preview' . ".jpeg",
								$idval . '_preview' . ".jpeg", $idval, 'previewurl', $fileoption
								);
					}
					else
						{
						$s3bucket_previewurl = 0;
					}
				}

				if ($s3bucket_videurl == 0)
				{
					// Fields to update.
					$fields = array(
						$db->quoteName('videourl') . '=\'' . $video_name . '\''
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('id') . '=' . $idval
					);
					$query->clear()
							->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
					$db->setQuery($query);
					$db->query();
				}

				if ($s3bucket_thumburl == 0)
				{
					$thumb_name = $idval . '_thumb' . ".jpeg";

					// Fields to update.
					$fields = array(
						$db->quoteName('thumburl') . '=\'' . $thumb_name . '\''
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('id') . '=' . $idval
					);
					$query->clear()
							->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
					$db->setQuery($query);
					$db->query();
				}

				if ($s3bucket_previewurl == 0)
				{
					$preview_name = $idval . '_preview' . ".jpeg";

					// Fields to update.
					$fields = array(
						$db->quoteName('previewurl') . '=\'' . $preview_name . '\''
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('id') . '=' . $idval
					);
					$query->clear()
							->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
					$db->setQuery($query);
					$db->query();
				}
			}
		}

		$thumb_name = $idval . '_thumb' . ".jpeg";
		$preview_name = $idval . '_preview' . ".jpeg";

		// Assign streameroption
		$streamer_option = $arrFormData['streameroption-value'];

		// Fields to update.
		$fields = array(
			$db->quoteName('streameroption') . '=\'' . $streamer_option . '\'',
			$db->quoteName('filepath') . '=\'' . $fileoption . '\''
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('id') . '=' . $idval
		);

		// To update the video file name in database table
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

	/**
	 * Function to get FFMPEG video information
	 *
	 * @param   var  $strVidPath     temp path
	 * @param   var  $strFfmpegPath  target path
	 * 
	 * @return	getFfmpegVidInfo
	 */
	public function getFfmpegVidInfo($strVidPath, $strFfmpegPath)
	{
		$commandline = $strFfmpegPath . " -i " . $strVidPath;
		$exec_return = uploadFfmpegHelper::ffmpeg_exec($commandline);
		$exec_return_content = explode("\n", $exec_return);
		$infos_line_id = uploadFfmpegHelper::ffmpeg_search('Video:', $exec_return_content);

		if ($infos_line_id)
		{
			$infos_line = trim($exec_return_content[$infos_line_id]);
			$infos_cleaning = explode(': ', $infos_line);
			$infos_datas = explode(',', $infos_cleaning[2]);
			$return_array['vdo_format'] = trim($infos_datas[0]);
		}

		return($return_array);
	}

	/**
	 * Function to get execute FFMPEG video using POPEN
	 * The popen() function opens a process by creating a pipe, forking, and invoking the shell
	 *
	 * @param   var  $commandline  ffmpeg command
	 * 
	 * @return	ffmpeg_exec
	 */
	public function ffmpeg_exec($commandline)
	{
		$read = '';
		$handle = popen($commandline . ' 2>&1', 'r');

		while (!feof($handle))
		{
			$read .= fread($handle, 2096);
		}

		pclose($handle);

		return($read);
	}

	/**
	 * Function to reset array value and search
	 *
	 * @param   var    $needle       needle
	 * @param   array  $array_lines  array value
	 * 
	 * @return	ffmpeg_search
	 */
	public function ffmpeg_search($needle, $array_lines)
	{
		$return_val = false;
		reset($array_lines);

		foreach ($array_lines as $num_line => $line_content)
		{
			if (strpos($line_content, $needle) !== false)
			{
				return($num_line);
			}
		}

		return($return_val);
	}

	/**
	 * Function to get file extensions	
	 *
	 * @param   var  $strFileName  filename
	 * 
	 * @return	getFileExtension
	 */
	public function getFileExtension($strFileName)
	{
		$strFileName = strtolower($strFileName);

		return JFile::getExt($strFileName);
	}
}
