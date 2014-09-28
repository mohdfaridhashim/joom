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

/**
 * Admin edit ads model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareModeleditads extends ContushdvideoshareModel
{
	/**
	 * Function to edit ads
	 * 
	 * @return  editadsmodel
	 */
	public function editadsmodel()
	{
		$db = JFactory::getDBO();
		$objAdsTable = JTable::getInstance('ads', 'Table');
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$id = $cid[0];
		$objAdsTable->load($id);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', $objAdsTable->published);
		$add = array('rs_ads' => $objAdsTable);

		return $add;
	}

	/**
	 * Function to remove ad
	 * 
	 * @return  removeads
	 */
	public function removeads()
	{
		$mainframe = JFactory::getApplication();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$cids = implode(',', $cid);

		if (count($cid))
		{
			// Query to fetch ad details for selected ads
			$query->clear()
					->select($db->quoteName(array('postvideopath')))
					->from($db->quoteName('#__hdflv_ads'))
					->where('id  IN ( ' . $cids . ' )');
			$db->setQuery($query);
			$arrAdsIdList = $db->loadResultArray();

			// VPATH - target path /components/com_contushdvideoshare/videos
			$strVideoPath = VPATH . "/";

			// Removed the video and image files for selected videos
			if (count($arrAdsIdList))
			{
				for ($i = 0; $i < count($arrAdsIdList); $i++)
				{
					if ($arrAdsIdList[$i] && JFile::exists($strVideoPath . $arrAdsIdList[$i]))
					{
						JFile::delete($strVideoPath . $arrAdsIdList[$i]);
					}
				}
			}

			$cids = implode(',', $cid);
			$conditions = array(
				$db->quoteName('id') . 'IN ( ' . $cids . ' )'
			);

			$query->clear()
					->delete($db->quoteName('#__hdflv_ads'))
					->where($conditions);
			$db->setQuery($query);

			if (!$db->query())
			{
				JError::raiseWarning($db->getErrorNum(), JText::_($db->getErrorMsg()));
			}
		}

		if (count($cid) > 1)
		{
			$msg = 'Ads Deleted Successfully';
		}
		else
		{
			$msg = 'Ad Deleted Successfully';
		}

		// Set to redirect
		$mainframe->redirect('index.php?option=com_contushdvideoshare&layout=ads', $msg, 'message');
	}
}
