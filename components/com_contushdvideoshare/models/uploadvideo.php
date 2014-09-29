<?php
/**
 * Upload videos model file
 *
 * This file is to upload videos for front end users
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

// Import joomla model library
jimport('joomla.application.component.model');

// Import filesystem libraries.
jimport('joomla.filesystem.file');

/**
 * Upload videos model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareModeluploadvideo extends ContushdvideoshareModel
{
	/**
	 * Constructor - global variable initialization
	 * 
	 */
	public function __construct()
	{
		global $option, $mainframe, $allowedExtensions;
		global $target_path, $error, $errorcode, $errormsg, $clientupload_val, $s3bucket_video;
		parent::__construct();

		// Get global configuration
		$mainframe = JFactory::getApplication();
		$option = JRequest::getVar('option');
		$target_path = $error = '';
		$errorcode = 12;
		$clientupload_val = "false";
		$errormsg[0] = " " . JText::_('HDVS_FILE_UPLOAD_SUCCESSFULLY');
		$errormsg[1] = " " . JText::_('HDVS_CANCELLED_BY_USER');
		$errormsg[2] = " " . JText::_('HDVS_INVALID_FILE_TYPE_SPECIFIED');
		$errormsg[3] = " " . JText::_('HDVS_YOUR_FILE_EXCEEDS_SERVER_LIMIT_SIZE');
		$errormsg[4] = " " . JText::_('HDVS_UNKNOWN_ERROR_OCCURED');
		$errormsg[5] = " " . JText::_('HDVS_UPLOAD_FILE_EXCEEDS_THE_UPLOAD_MAX_FILESIZE_DIRECTIVE');
		$errormsg[6] = " " . JText::_('HDVS_UPLOAD_FILE_EXCEEDS_THE_UPLOAD_MAX_FILESIZE_DIRECTIVE_THAT_WAS_SPECIFIED');
		$errormsg[7] = " " . JText::_('HDVS_THE_UPLOAD_FILE_WAS_ONLY_PARTIALLY_UPLOADED');
		$errormsg[8] = " " . JText::_('HDVS_NO_FILE_WAS_UPLOADED');
		$errormsg[9] = " " . JText::_('HDVS_MISSING_A_TEMORARY_FOLDER');
		$errormsg[10] = " " . JText::_('HDVS_FAILED_TO_WRITE_FILE_TO_DISK');
		$errormsg[11] = " " . JText::_('HDVS_FILE_UPLOAD_STOPPED_BY_EXTENSION');
		$errormsg[12] = " " . JText::_('HDVS_UNKNOWN_UPLOAD_ERROR');
		$errormsg[13] = " " . JText::_('HDVS_PLEASE_CHECK_PHPINI_SETTINGS');
	}

	/**
	 * Function to get uploaded file details from form
	 * 
	 * @return  void
	 */
	public function fileupload()
	{
		global $clientupload_val, $allowedExtensions, $errorcode, $error, $target_path, $s3bucket_video, $errormsg;

		if (JRequest::getVar('error') != '')
		{
			$error = JRequest::getVar('error');
		}

		if (JRequest::getVar('processing') != '')
		{
			$pro = JRequest::getVar('processing');
		}

		if (JRequest::getVar('clientupload') != '')
		{
			$clientupload_val = JRequest::getVar('clientupload');
		}

		$uploadFile = JRequest::getVar('myfile', null, 'files', 'array');

		if (JRequest::getVar('mode') != '')
		{
			$exttype = JRequest::getVar('mode');

			if ($exttype == 'video')
			{
				$allowedExtensions = array(
					"mp3", "MP3", "flv", "FLV", "mp4", "MP4", "m4v", "M4V", "M4A", "m4a", "MOV",
					"mov", "mp4v", "Mp4v", "F4V", "f4v"
					);
			}
			elseif ($exttype == 'image')
			{
				$allowedExtensions = array("jpg", "JPG", "png", "PNG");
			}
			elseif ($exttype == 'video_ffmpeg')
			{
				$allowedExtensions = array(
					"avi", "AVI", "dv", "DV", "3gp", "3GP", "3g2", "3G2", "mpeg", "MPEG", "wav", "WAV", "rm",
					"RM", "mp3", "MP3", "flv", "FLV", "mp4", "MP4", "m4v", "M4V", "M4A", "m4a", "MOV", "mov", "mp4v", "Mp4v",
					"F4V", "f4v");
			}
		}

		// Function to check error
		if (!$this->iserror())
		{
			// Check if stopped by post_max_size
			if (($pro == 1) && (empty($uploadFile)))
			{
				$errorcode = 13;
			}
			else
			{
				$file = $uploadFile;

				if ($this->no_file_upload_error($file))
				{
					if ($this->isAllowedExtension($file))
					{
						// Check file size
						if (!$this->filesizeexceeds($file))
						{
							$final_target_path = $this->doupload($file, $clientupload_val);
						}
					}
				}
			}
		}
		?>
		<script language="javascript" type="text/javascript">
			window.top.window.updateQueue(
				<?php echo $errorcode;?>,
			"<?php echo $errormsg[$errorcode]; ?>",
			"<?php echo $final_target_path; ?>",
			"<?php echo $s3bucket_video; ?>"
	);
		</script>
		<?php
	}

	/**
	 * Function to check error
	 * 
	 * @return  bool
	 */
	public function iserror()
	{
		global $error;
		global $errorcode;

		if ($error == "cancel")
		{
			$errorcode = 1;

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Function to set file upload error
	 * 
	 * @param   Object  $file  video file detail
	 * 
	 * @return  bool
	 */
	public function no_file_upload_error($file)
	{
		global $errorcode;
		$error_code = $file['error'];

		switch ($error_code)
		{
			case 1:
				$errorcode = 5;

				return false;

			case 2:
				$errorcode = 6;

				return false;

			case 3:
				$errorcode = 7;

				return false;

			case 4:
				$errorcode = 8;

				return false;

			case 6:
				$errorcode = 9;

				return false;

			case 7:
				$errorcode = 10;

				return false;

			case 8:
				$errorcode = 11;

				return false;

			case 0:
				return true;
			default:
				$errorcode = 12;

				return false;
		}
	}

	/**
	 * Function to check the extension of the file
	 * 
	 * @param   Object  $file  video file detail
	 * 
	 * @return  bool
	 */
	public function isAllowedExtension($file)
	{
		global $allowedExtensions;
		global $errorcode;
		$filename = $file['name'];
		$output = in_array(end(explode(".", $filename)), $allowedExtensions);

		if (!$output)
		{
			$errorcode = 2;

			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Function to check the file size
	 * 
	 * @param   Object  $file  video file detail
	 * 
	 * @return  bool
	 */
	public function filesizeexceeds($file)
	{
		global $errorcode;
		$POST_MAX_SIZE = ini_get('post_max_size');
		$filesize = $file['size'];
		$mul = substr($POST_MAX_SIZE, -1);
		$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));

		if ($_SERVER['CONTENT_LENGTH'] > $mul * (int) $POST_MAX_SIZE && $POST_MAX_SIZE)
		{
			return true;

			$errorcode = 3;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Function to upload video to temporary folder
	 * 
	 * @param   Object  $file              video file  detail
	 * @param   string  $clientupload_val  check for new upload
	 * 
	 * @return  string
	 */
	public function doupload($file, $clientupload_val)
	{
		global $errorcode, $s3bucket_video;
		global $target_path;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('dispenable')
				->from('#__hdflv_site_settings')
				->where($db->quoteName('id') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$setting_res = $db->loadResult();
		$dispenable = unserialize($setting_res);

		$destination_path = "components/com_contushdvideoshare/views/videoupload/tmpl";

		if ($clientupload_val == "true")
		{
			$destination = realpath(dirname(__FILE__) . '/../../../components/com_contushdvideoshare/videos/');
			$destination_path = str_replace('\\', '/', $destination) . "/";
		}

		$filename = JFile::makeSafe($file['name']);
		$target_path = $destination_path . rand() . "." . end(explode(".", $filename));

		// Clean up filename to get rid of strange characters like spaces etc
		$sourceImage = $file['tmp_name'];

		if ($dispenable['amazons3'] == 1)
		{
			$s3bucket_video = 1;
			require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 's3_config.php';
			$strVids3TargetPath = 'components/com_contushdvideoshare/videos/' . $filename;

			if ($s3->putObjectFile($sourceImage, $bucket, $strVids3TargetPath, S3::ACL_PUBLIC_READ))
			{
				$s3bucket_video = 1;
				$errorcode = 0;
			}
			else
			{
				$s3bucket_video = 0;
			}
		}

		if ($s3bucket_video == 0)
		{
			$strVids3TargetPath = $target_path;

			// To store images to a directory called components/com_contushdvideoshare/videos
			if (JFile::upload($sourceImage, $target_path))
			{
				$errorcode = 0;
			}
			else
			{
				$errorcode = 4;
			}
		}

		sleep(1);

		return $final_target_path = $strVids3TargetPath;
	}
}
