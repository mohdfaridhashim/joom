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

/**
 * Admin sortorder model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareModelsortorder extends ContushdvideoshareModel
{
	/**
	 * Function to save sort order
	 * 
	 * @return  videosortordermodel
	 */
	public function videosortordermodel()
	{
		$db = JFactory::getDBO();
		$sql = '';
		$query = $db->getQuery(true);
		$listitem = JRequest::getvar('listItem');
		$pagenum = JRequest::getvar('pagenum');
		$ids = implode(',', $listitem);

		if (isset($pagenum))
		{
			$page = (20 * ($pagenum - 1));
		}

		foreach ($listitem as $key => $value)
		{
			$listitems[$key + $page] = $value;
		}

		$query->clear()
				->update($db->quoteName('#__hdflv_upload'));

		foreach ($listitems as $position => $item)
		{
			$sql .= sprintf("WHEN %d THEN %d ", $item, $position);
		}

		$query->set($db->quoteName('ordering') . ' = CASE id ' . $sql . ' END')
				->where($db->quoteName('id') . ' IN (' . $ids . ')');
		$db->setQuery($query);
		$db->query();

		exit();
	}

	/**
	 * Function to save sort order
	 *
	 * @return  categorysortordermodel
	 */
	public function categorysortordermodel()
	{
		$db = JFactory::getDBO();
		$sql = '';
		$query = $db->getQuery(true);
		$listitem = JRequest::getvar('listItem');
		$pagenum = JRequest::getvar('pagenum');
		$ids = implode(',', $listitem);
	
		if (isset($pagenum))
		{
			$page = (20 * ($pagenum - 1));
		}
	
		foreach ($listitem as $key => $value)
		{
			$listitems[$key + $page] = $value;
		}
	
		$query->clear()
		->update($db->quoteName('#__hdflv_category'));
	
		foreach ($listitems as $position => $item)
		{
			$sql .= sprintf("WHEN %d THEN %d ", $item, $position);
		}
	
		$query->set($db->quoteName('ordering') . ' = CASE id ' . $sql . ' END')
		->where($db->quoteName('id') . ' IN (' . $ids . ')');
		$db->setQuery($query);
		$db->query();
	
		exit();
	}
}
