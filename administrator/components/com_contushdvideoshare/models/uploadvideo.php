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

// Import joomla model library
jimport('joomla.application.component.model');

// Import filesystem libraries.
jimport('joomla.filesystem.file');

/**
 * Admin upload video model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareModeluploadvideo extends ContushdvideoshareModel
{
	/**
	 * Constructor function to declare global value
	 */
	public function __construct()
	{
		global $option, $mainframe, $allowedExtensions;
		global $target_path, $error, $errorcode, $errormsg, $clientupload_val;
		parent::__construct();

		// Get global configuration
		$mainframe = JFactory::getApplication();
		$option = JRequest::getVar('option');
		$target_path = $error = '';
		$errorcode = 12;
		$clientupload_val = "false";
		$errormsg[0] = " File Uploaded Successfully";
		$errormsg[1] = " Cancelled by user";
		$errormsg[2] = " Invalid File type specified";
		$errormsg[3] = " Your File Exceeds Server Limit size";
		$errormsg[4] = " Unknown Error Occured";
		$errormsg[5] = " The uploaded file exceeds the upload_max_filesize directive 
				in php.ini";
		$errormsg[6] = " The uploaded file exceeds the MAX_FILE_SIZE directive that 
				was specified in the HTML form";
		$errormsg[7] = " The uploaded file was only partially uploaded";
		$errormsg[8] = " No file was uploaded";
		$errormsg[9] = " Missing a temporary folder";
		$errormsg[10] = " Failed to write file to disk";
		$errormsg[11] = " File upload stopped by extension";
		$errormsg[12] = " Unknown upload error.";
		$errormsg[13] = " Please check post_max_size in php.ini settings";
	}

	/**
	 * Function to get uploaded file details from form
	 * 
	 * @return  fileupload
	 */
	public function fileupload()
	{
		global $clientupload_val, $allowedExtensions, $errorcode, $error, $target_path, $errormsg;

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
				$allowedExtensions = array("mp3", "MP3", "flv", "FLV", "mp4", "MP4", "m4v", "M4V", "M4A", "m4a", "MOV",
					"mov", "mp4v", "Mp4v", "F4V", "f4v");
			}
			elseif ($exttype == 'image')
			{
				$allowedExtensions = array("jpg", "JPG", "jpeg", "JPEG", "png", "PNG");
			}
			elseif ($exttype == 'srt')
			{
				$allowedExtensions = array("srt", "SRT");
			}
			elseif ($exttype == 'video_ffmpeg')
			{
				$allowedExtensions = array("avi", "AVI", "dv", "DV", "3gp", "3GP", "3g2", "3G2", "mpeg", "MPEG", "wav", "WAV", "rm",
					"RM", "mp3", "MP3", "flv", "FLV", "mp4", "MP4", "m4v", "M4V", "M4A", "m4a", "MOV", "mov", "mp4v", "Mp4v",
					"F4V", "f4v");
			}
		}

		// Check error
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
							$this->doupload($file, $clientupload_val);
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
				"<?php echo $target_path; ?>"
		);
		</script>
		<?php
	}

	/**
	 * Function to check error
	 * 
	 * @return  iserror
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
	 * @param   object  $file  uploaded file
	 * 
	 * @return  no_file_upload_error
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
	 * @param   object  $file  uploaded file
	 * 
	 * @return  isAllowedExtension
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
	 * @param   object  $file  uploaded file
	 * 
	 * @return  filesizeexceeds
	 */
	public function filesizeexceeds($file)
	{
		global $errorcode;
		$POST_MAX_SIZE = ini_get('post_max_size');
		$mul = substr($POST_MAX_SIZE, -1);
		$muxsize = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));

		if ($_SERVER['CONTENT_LENGTH'] > $muxsize * (int) $POST_MAX_SIZE && $POST_MAX_SIZE)
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
	 * @param   object   $file              uploaded file
	 * @param   boolean  $clientupload_val  uploaded type
	 * 
	 * @return  doupload
	 */
	public function doupload($file, $clientupload_val)
	{
		global $errorcode;
		global $target_path;
		$destination_path = "components/com_contushdvideoshare/images/uploads/";

		if ($clientupload_val == "true")
		{
			$destination = realpath(dirname(__FILE__) . '/../../../components/com_contushdvideoshare/videos/');
			$destination_path = str_replace('\\', '/', $destination) . "/";
		}

		$filename = JFile::makeSafe($file['name']);
		$target_path = $destination_path . rand() . "." . end(explode(".", $filename));

		// Clean up filename to get rid of strange characters like spaces etc
		$sourceImage = $file['tmp_name'];

		// To store images to a directory called components/com_contushdvideoshare/videos
		if (JFile::upload($sourceImage, $target_path))
		{
			$errorcode = 0;
		}
		else
		{
			$errorcode = 4;
		}

		sleep(1);
	}
}
