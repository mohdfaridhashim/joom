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
 * Admin UploadEmbedHelper class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class UploadEmbedHelper
{
	/**
	 * Function to upload embed type videos
	 * 
	 * @param   array  $arrFormData  post content
	 * @param   int    $idval        video id
	 *
	 * @return	uploadEmbed
	 */
	public function uploadEmbed($arrFormData, $idval)
	{
		$embedcode = $arrFormData['embedcode'];
		$fileoption = $arrFormData['fileoption'];

		// Update fields
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Fields to update.
		$fields = array(
			$db->quoteName('embedcode') . '=\'' . $embedcode . '\''
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('id') . '=' . $idval
		);
		$query->clear()
				->update($db->quoteName('#__hdflv_upload'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->query();

		// Get thumb image name and set thumb image name
		$strThumbImg = $arrFormData['thumbimageform-value'];
		$arrThumbImg = explode('uploads/', $strThumbImg);

		if (isset($arrThumbImg[1]))
		{
			$strThumbImg = $arrThumbImg[1];
		}

		// Get upload helper file to upload thumb
		require_once FVPATH . DS . 'helpers' . DS . 'uploadfile.php';
		UploadFileHelper::uploadVideoProcessing('', '', $idval, '', $strThumbImg, '', '', '', '', $arrFormData['newupload'], $fileoption);

		// Delete temp file
		if ($strThumbImg != '')
		{
			UploadFileHelper::unlinkUploadedTmpFiles($strThumbImg);
		}
	}
}
