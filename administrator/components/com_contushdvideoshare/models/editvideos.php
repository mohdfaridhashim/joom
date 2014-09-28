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

// Import joomla model library
jimport('joomla.application.component.model');
jimport( 'joomla.filesystem.file' );

/**
 * Admin edit videos model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareModeleditvideos extends ContushdvideoshareModel
{
	/**
	 * Function to edit videos
	 * 
	 * @return  editvideosmodel
	 */
	public function editvideosmodel()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Query to fetch category list
		$query->clear()
				->select($db->quoteName(array('id','category')))
					->from($db->quoteName('#__hdflv_category'))
					->where('published = 1')
					->order('id DESC');
		$db->setQuery($query);
		$rs_play = $db->loadObjectList();

		// Query to fetch pre/post roll ads
		$query->clear()
				->select($db->quoteName(array('id','adsname')))
					->from($db->quoteName('#__hdflv_ads'))
					->where('published = 1')
					->where('typeofadd <> \'mid\'')
					->where('typeofadd <> \'ima\'')
					->order('adsname ASC');
		$db->setQuery($query);
		$rs_ads = $db->loadObjectList();

		// Get adminvideos table object
		$rs_editupload = JTable::getInstance('adminvideos', 'Table');
		$cid = JRequest::getVar('cid', array(0), '', 'array');

		// To get the id no to be edited...
		$id = $cid[0];
		$rs_editupload->load($id);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', $rs_editupload->published);

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$strTable = '#__viewlevels';
			$strName = 'title';
		}
		else
		{
			$strTable = '#__groups';
			$strName = 'name';
		}

		// Query to fetch user groups
		$query->clear()
				->select(array('id AS id', $strName . ' AS title'))
					->from($db->quoteName($strTable))
					->order('id ASC');
		$db->setQuery($query);
		$usergroups = $db->loadObjectList();
		$editadd = array('rs_editupload' => $rs_editupload, 'rs_play' => $rs_play, 'rs_ads' => $rs_ads, 'user_groups' => $usergroups);

		return $editadd;
	}

	/**
	 * Function to get player settings
	 * 
	 * @return  showplayersettings
	 */
	public function showplayersettings()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Query to fetch player settings
		$query->clear()
				->select($db->quoteName('player_values'))
			->from($db->quoteName('#__hdflv_player_settings'));
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function to remove videos
	 * 
	 * @return  removevideos
	 */
	public function removevideos()
	{
		$option = 'com_contushdvideoshare';
		global $mainframe;
		$cid = JRequest::getVar('cid', array(), '', 'array');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$cids = implode(',', $cid);

		// Get count
		if (count($cid))
		{
			// Query to fetch video details for selected videos
			$query->clear()
					->select($db->quoteName(array('videourl','thumburl','previewurl','hdurl')))
					->from($db->quoteName('#__hdflv_upload'))
					->where('filepath = \'File\' OR filepath <> \'FFmpeg\' AND id IN ( ' . $cids . ' )');
			$db->setQuery($query);
			$arrVideoIdList = $db->loadObjectList();

			// VPATH - target path /components/com_contushdvideoshare/videos
			$strVideoPath = VPATH . "/";

			// Removed the video and image files for selected videos
			if (count($arrVideoIdList))
			{
				for ($i = 0; $i < count($arrVideoIdList); $i++)
				{
					if ($arrVideoIdList[$i]->videourl && JFile::exists($strVideoPath . $arrVideoIdList[$i]->videourl))
					{
						JFile::delete($strVideoPath . $arrVideoIdList[$i]->videourl);
					}

					if ($arrVideoIdList[$i]->thumburl != 'default_thumb.jpg' && JFile::exists($strVideoPath . $arrVideoIdList[$i]->thumburl))
					{
						JFile::delete($strVideoPath . $arrVideoIdList[$i]->thumburl);
					}

					if ($arrVideoIdList[$i]->previewurl != 'default_thumb.jpg' && JFile::exists($strVideoPath . $arrVideoIdList[$i]->previewurl))
					{
						JFile::delete($strVideoPath . $arrVideoIdList[$i]->previewurl);
					}

					if ($arrVideoIdList[$i]->hdurl && JFile::exists($strVideoPath . $arrVideoIdList[$i]->hdurl))
					{
						JFile::delete($strVideoPath . $arrVideoIdList[$i]->hdurl);
					}
				}
			}

			// Query to delete selected videos
			$conditions = array(
				$db->quoteName('id') . 'IN ( ' . $cids . ' )'
			);

			$query->clear()
					->delete($db->quoteName('#__hdflv_upload'))
					->where($conditions);
			$db->setQuery($query);

			if (!$db->query())
			{
				JError::raiseWarning($db->getErrorNum(), JText::_($db->getErrorMsg()));
			}
			else
			{
				// Query to delete video id in #__hdflv_video_category table
				$conditions = array(
				$db->quoteName('vid') . 'IN ( ' . $cids . ' )'
			);

			$query->clear()
					->delete($db->quoteName('#__hdflv_video_category'))
					->where($conditions);
			$db->setQuery($query);
			}
		}

		// Page redirect
		$mainframe = JFactory::getApplication();

		if (count($cid) > 1)
		{
			$msg = JText::_('Videos Deleted Successfully');
		}
		else
		{
			$msg = JText::_('Video Deleted Successfully');
		}

		// Set redirect to videos list page
		$mainframe->redirect('index.php?option=' . $option . '&layout=adminvideos&user=' . JRequest::getVar('user'), $msg, 'message');
	}
}
