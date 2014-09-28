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
//  No direct access to this file
defined('_JEXEC') or die;

// Import filesystem libraries.
jimport('joomla.filesystem.file');

/**
 * Admin UploadFileHelper class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class UploadFileHelper
{
	/**
	 * Function to upload file type videos
	 * 
	 * @param   array  $arrFormData  post content
	 * @param   int    $idval        video id
	 *
	 * @return	uploadFile
	 */
	public static function uploadFile($arrFormData, $idval)
	{
		//  Get video and set video name
		$strVideoName = $arrFormData['normalvideoform-value'];
		$arrVideoName = explode('uploads/', $strVideoName);

		if (isset($arrVideoName[1]))
		{
			$strVideoName = $arrVideoName[1];
		}

		//  Get hdvideo and set hdvideo name
		$strHdVideoName = $arrFormData['hdvideoform-value'];
		$arrHdVideoName = explode('uploads/', $strHdVideoName);

		if (isset($arrHdVideoName[1]))
		{
			$strHdVideoName = $arrHdVideoName[1];
		}

		//  Get thumb image name and set thumb image name
		$strThumbImg = $arrFormData['thumbimageform-value'];
		$arrThumbImg = explode('uploads/', $strThumbImg);

		if (isset($arrThumbImg[1]))
		{
			$strThumbImg = $arrThumbImg[1];
		}

		//  Get preview image name and set preview image name
		$strPreviewImg = $arrFormData['previewimageform-value'];
		$arrPreviewImg = explode('uploads/', $strPreviewImg);

		if (isset($arrPreviewImg[1]))
		{
			$strPreviewImg = $arrPreviewImg[1];
		}

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

		//  Function to upload video
		self::uploadVideoProcessing(
		$subtile_lang1, $subtile_lang2, $idval, $strVideoName, $strThumbImg,
		$strPreviewImg, $strSrtFile1, $strSrtFile2, $strHdVideoName, $arrFormData['newupload'], $arrFormData['fileoption']
		);

		/**
		 * DELETE TEMPORARY FILES
		 * delete temporary existing videos,hd videos,thumb image and preview image
		 * after files moved from temporary path to target path
		 * @ Temp Path : components/com_contushdvideoshare/images/uploads/ 
		 * @ Target Path : components/com_contushdvideoshare/videos		 		 
		 */
		if ($strVideoName != '')
		{
			self::unlinkUploadedTmpFiles($strVideoName);
		}

		if ($strHdVideoName != '')
		{
			self::unlinkUploadedTmpFiles($strHdVideoName);
		}

		if ($strThumbImg != '')
		{
			self::unlinkUploadedTmpFiles($strThumbImg);
		}

		if ($strPreviewImg != '')
		{
			self::unlinkUploadedTmpFiles($strPreviewImg);
		}

		if ($strSrtFile1 != '')
		{
			self::unlinkUploadedTmpFiles($strSrtFile1);
		}

		if ($strSrtFile2 != '')
		{
			self::unlinkUploadedTmpFiles($strSrtFile2);
		}
	}

	/**
	 * Function to upload file from temporary path
	 *
	 * @param   var  $subtile_lang1  subtile language1
	 * @param   var  $subtile_lang2  subtile language2
	 * @param   int  $idval          Video id
	 * @param   var  $file_video     videofile path
	 * @param   var  $file_timage    thumb image path
	 * @param   var  $file_pimage    preview image path
	 * @param   var  $file_str1      subtile1 path
	 * @param   var  $file_str2      subtile2 path
	 * @param   var  $file_hvideo    hd video path
	 * @param   var  $newupload      to check whether newly uploaded video or not
	 * @param   var  $filepath       upload method type
	 * 
	 * @return	uploadVideoProcessing
	 */
	public static function uploadVideoProcessing(
		$subtile_lang1, $subtile_lang2, $idval, $file_video, $file_timage,
		$file_pimage, $file_str1, $file_str2, $file_hvideo, $newupload, $filepath)
	{
		$strTargetPath = VPATH . "/";

		//  For videos
		$db = JFactory::getDBO();
		$s3bucket_video = $s3bucket_thumb = $s3bucket_pre = $s3bucket_srt1 = $s3bucket_srt2 = $s3bucket_hdurl = 0;
		$query = $db->getQuery(true);
		$query->clear()
				->select($db->quoteName(array('dispenable')))
				->from($db->quoteName('#__hdflv_site_settings'))
				->where($db->quoteName('id') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$setting_res = $db->loadResult();
		$dispenable = unserialize($setting_res);

		if ($file_video <> '')
		{
			$exts = self::getFileExtension($file_video);
			$strVidTempPath = FVPATH . "/images/uploads/" . $file_video;
			$strVidTargetPath = $strTargetPath . $idval . "_video" . "." . $exts;
			$fv = $idval . "_video" . "." . $exts;

			if ($dispenable['amazons3'] == 1)
			{
				$s3bucket_video = 1;
				require_once FVPATH . DS . 'helpers' . DS . 's3_config.php';
				$strVids3TargetPath = $dispenable['amazons3path'] . $fv;

				if ($s3->putObjectFile($strVidTempPath, $bucket, $strVids3TargetPath, S3::ACL_PUBLIC_READ))
				{
					$s3bucket_video = 1;
					self::amazons3update($strVidTempPath, $fv, $idval, 'videourl', $filepath);
				}
				else
				{
					$s3bucket_video = 0;
				}
			}
			elseif ($s3bucket_video == 0)
			{
				//  Function to copy from imasges/uploads to /components/com_hdvideoshare/videos/
				self::copytovideos($strVidTempPath, $strVidTargetPath, $fv, $idval, 'videourl', $newupload, $filepath);
			}
		}

		//  For thumb image
		if ($file_timage <> '')
		{
			$exts = self::getFileExtension($file_timage);
			$strThumbImgTempPath = FVPATH . "/images/uploads/" . $file_timage;
			$strThumbImgTargetPath = $strTargetPath . $idval . "_thumb" . "." . $exts;
			$ft = $idval . "_thumb" . "." . $exts;

			if ($dispenable['amazons3'] == 1)
			{
				$s3bucket_thumb = 1;
				require_once FVPATH . DS . 'helpers' . DS . 's3_config.php';
				$strthumbs3TargetPath = $dispenable['amazons3path'] . $ft;

				if ($s3->putObjectFile($strThumbImgTempPath, $bucket, $strthumbs3TargetPath, S3::ACL_PUBLIC_READ))
				{
					$s3bucket_thumb = 1;
					self::amazons3update($strThumbImgTempPath, $ft, $idval, 'thumburl', $filepath);
				}
				else
				{
					$s3bucket_thumb = 0;
				}
			}
			elseif ($s3bucket_thumb == 0)
			{
				//  Function to copy from imasges/uploads to /components/com_hdvideoshare/videos/
				self::copytovideos($strThumbImgTempPath, $strThumbImgTargetPath, $ft, $idval, 'thumburl', $newupload, $filepath);
			}
		}

		//  For preview image
		if ($file_pimage <> '')
		{
			$exts = self::getFileExtension($file_pimage);
			$strPreImgTempPath = FVPATH . "/images/uploads/" . $file_pimage;
			$strPreImgTargetPath = $strTargetPath . $idval . "_preview" . "." . $exts;
			$fp = $idval . "_preview" . "." . $exts;

			if ($dispenable['amazons3'] == 1)
			{
				$s3bucket_pre = 1;
				require_once FVPATH . DS . 'helpers' . DS . 's3_config.php';
				$strpreviews3TargetPath = $dispenable['amazons3path'] . $fp;

				if ($s3->putObjectFile($strPreImgTempPath, $bucket, $strpreviews3TargetPath, S3::ACL_PUBLIC_READ))
				{
					self::amazons3update($strPreImgTempPath, $fp, $idval, 'previewurl', $filepath);
					$s3bucket_pre = 1;
				}
				else
				{
					$s3bucket_pre = 0;
				}
			}
			elseif ($s3bucket_pre == 0)
			{
				//  Function to copy from imasges/uploads to /components/com_hdvideoshare/videos/
				self::copytovideos($strPreImgTempPath, $strPreImgTargetPath, $fp, $idval, 'previewurl', $newupload, $filepath);
			}
		}

		//  For Subtitle1
		if ($file_str1 <> '')
		{
			$exts = self::getFileExtension($file_str1);
			$strstr1TempPath = FVPATH . "/images/uploads/" . $file_str1;
			$strstr1TargetPath = $strTargetPath . $idval . "_" . $subtile_lang1 . "." . $exts;
			$fp = $idval . "_" . $subtile_lang1 . "." . $exts;

			if ($dispenable['amazons3'] == 1)
			{
				$s3bucket_srt1 = 1;
				require_once FVPATH . DS . 'helpers' . DS . 's3_config.php';
				$strstr1s3TargetPath = $dispenable['amazons3path'] . $fp;

				if ($s3->putObjectFile($strstr1TempPath, $bucket, $strstr1s3TargetPath, S3::ACL_PUBLIC_READ))
				{
					self::amazons3update($strstr1TempPath, $fp, $idval, 'previewurl', $filepath);
					$s3bucket_srt1 = 1;
				}
				else
				{
					$s3bucket_srt1 = 0;
				}
			}
			elseif ($s3bucket_srt1 == 0)
			{
				//  Function to copy from imasges/uploads to /components/com_hdvideoshare/videos/
				self::copytovideos($strstr1TempPath, $strstr1TargetPath, $fp, $idval, 'subtitle1', $newupload, $filepath);
			}
		}

		//  For Subtitle2
		if ($file_str2 <> '')
		{
			$exts = self::getFileExtension($file_str2);
			$strstr2TempPath = FVPATH . "/images/uploads/" . $file_str2;
			$strstr2TargetPath = $strTargetPath . $idval . "_" . $subtile_lang2 . "." . $exts;
			$fp = $idval . "_" . $subtile_lang2 . "." . $exts;

			if ($dispenable['amazons3'] == 1)
			{
				$s3bucket_srt2 = 1;
				require_once FVPATH . DS . 'helpers' . DS . 's3_config.php';
				$strstr2s3TargetPath = $dispenable['amazons3path'] . $fp;

				if ($s3->putObjectFile($strstr2TempPath, $bucket, $strstr2s3TargetPath, S3::ACL_PUBLIC_READ))
				{
					self::amazons3update($strstr2TempPath, $fp, $idval, 'subtitle2', $filepath);
					$s3bucket_srt2 = 1;
				}
				else
				{
					$s3bucket_srt2 = 0;
				}
			}
			elseif ($s3bucket_srt2 == 0)
			{
				//  Function to copy from imasges/uploads to /components/com_hdvideoshare/videos/
				self::copytovideos($strstr2TempPath, $strstr2TargetPath, $fp, $idval, 'subtitle2', $newupload, $filepath);
			}
		}

		//  For hdvideo
		if ($file_hvideo <> '')
		{
			$exts = self::getFileExtension($file_hvideo);
			$strHdVidTempPath = FVPATH . "/images/uploads/" . $file_hvideo;
			$strHdVidTargetPath = $strTargetPath . $idval . "_hd" . "." . $exts;
			$fh = $idval . "_hd" . "." . $exts;

			if ($dispenable['amazons3'] == 1)
			{
				$s3bucket_hdurl = 1;
				require_once FVPATH . DS . 'helpers' . DS . 's3_config.php';
				$strhds3TargetPath = $dispenable['amazons3path'] . $fh;

				if ($s3->putObjectFile($strHdVidTempPath, $bucket, $strhds3TargetPath, S3::ACL_PUBLIC_READ))
				{
					self::amazons3update($strHdVidTempPath, $fh, $idval, 'hdurl', $filepath);
					$s3bucket_hdurl = 1;
				}
				else
				{
					$s3bucket_hdurl = 0;
				}
			}
			elseif ($s3bucket_hdurl == 0)
			{
				//  Function to copy from imasges/uploads to /components/com_hdvideoshare/videos/
				self::copytovideos($strHdVidTempPath, $strHdVidTargetPath, $fh, $idval, 'hdurl', $newupload, $filepath);
			}
		}
	}

	/**
	 * Function to move files from temp path to target path
	 *
	 * @param   var  $strFileTempPath  file temp path
	 * @param   var  $vmfile           db values
	 * @param   int  $idval            Video id
	 * @param   var  $dbname           db field name
	 * @param   var  $filepath         upload method type
	 * 
	 * @return	amazons3update
	 */
	public static function amazons3update($strFileTempPath, $vmfile, $idval, $dbname, $filepath)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		//  Check thumb image is default thumb image
		if ($strFileTempPath <> 'default_thumb')
		{
		}
		else
		{
			$vmfile = 'default_thumb.jpg';
		}

		// Fields to update.
		$fields = array(
			$db->quoteName('streameroption') . '=\'None\'',
			$db->quoteName($dbname) . '=\'' . $vmfile . '\'',
			$db->quoteName('filepath') . '=\'' . $filepath . '\'',
			$db->quoteName('amazons3') . '=\'1\''
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('id') . '=' . $idval
		);

		//  Update streamer option,thumb url and file path
		$query->clear()
				->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Function to move files from temp path to target path
	 *
	 * @param   var  $strFileTempPath    file temp path
	 * @param   var  $strFileTargetPath  file target path
	 * @param   var  $vmfile             db values
	 * @param   int  $idval              Video id
	 * @param   var  $dbname             db field name
	 * @param   var  $newupload          to check whether newly uploaded video or not
	 * @param   var  $filepath           upload method type
	 * 
	 * @return	copytovideos
	 */
	public static function copytovideos($strFileTempPath, $strFileTargetPath, $vmfile, $idval, $dbname, $newupload, $filepath)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		//  Check thumb image is default thumb image
		if ($strFileTempPath <> 'default_thumb')
		{
			//  To make sure in edit mode video ,hd, thumb image or preview image file is exists
			//  if exists then remove the old one
			if ($newupload == 1)
			{
				if (JFile::exists($strFileTempPath) && JFile::exists($strFileTargetPath))
				{
					JFile::delete($strFileTargetPath);
				}
			}
			//  Function to files move from temp folder to target path
			if (JFile::exists($strFileTempPath))
			{
				rename($strFileTempPath, $strFileTargetPath);
			}
		}
		else
		{
			$vmfile = 'default_thumb.jpg';
		}

		// Fields to update.
		$fields = array(
			$db->quoteName('streameroption') . '=\'None\'',
			$db->quoteName($dbname) . '=\'' . $vmfile . '\'',
			$db->quoteName('filepath') . '=\'' . $filepath . '\''
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('id') . '=' . $idval
		);

		//  Update streamer option,thumb url and file path
		$query->clear()
				->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->query();

		$arrVideoName = explode('uploads/', $strFileTempPath);

		if (isset($arrVideoName[1]))
		{
			$arrVideoName[] = $arrVideoName[1];
		}
	}

	/**
	 * Function to delete files from temp path
	 *
	 * @param   var  $strFileName  temp file name
	 * 
	 * @return	unlinkUploadedTmpFiles
	 */
	public static function unlinkUploadedTmpFiles($strFileName)
	{
		$strFilePath = FVPATH . "/images/uploads/$strFileName";

		if (JFile::exists($strFilePath))
		{
			JFile::delete($strFilePath);
		}
	}

	/**
	 * Function to get file extensions
	 *
	 * @param   var  $strFileName  file name
	 * 
	 * @return	getFileExtension
	 */
	public static function getFileExtension($strFileName)
	{
		$strFileName = strtolower($strFileName);

		return JFile::getExt($strFileName);
	}
}
