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

// Import joomla pagination library
jimport('joomla.html.pagination');

/**
 * Admin category model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareModelcategory extends ContushdvideoshareModel
{
	/**
	 * Constructor function to declae values globally
	 */
	public function __construct()
	{
		global $mainframe, $db, $option;
		global $cateid;
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$option = JRequest::getCmd('option');
	}

	/**
	 * Function to remove slashes from string
	 * 
	 * @param   string  $string  string to be remove slash
	 * @param   string  $type    type of action to be performed
	 * 
	 * @return  phpSlashes
	 */
	public function phpSlashes($string, $type = 'add')
	{
		if ($type == 'add')
		{
			if (get_magic_quotes_gpc())
			{
				return $string;
			}
			else
			{
				if (function_exists('addslashes'))
				{
					return addslashes($string);
				}
				else
				{
					return mysql_real_escape_string($string);
				}
			}
		}
		elseif ($type == 'strip')
		{
			return stripslashes($string);
		}
		else
		{
			die('error in PHP_slashes (mixed,add | strip)');
		}
	}

	/**
	 * Function to fetch categories detail
	 * 
	 * @return  getcategory
	 */
	public function getcategory()
	{
		global $option, $mainframe, $db;
		$query = $db->getQuery(true);
		$filter_order = $mainframe->getUserStateFromRequest($option . 'filter_order_category', 'filter_order', 'a.ordering', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option . 'filter_order_Dir_category', 'filter_order_Dir', 'asc', 'word');
		$search = $mainframe->getUserStateFromRequest($option . 'category_search', 'category_search', '', 'string');
		$state_filter = $mainframe->getUserStateFromRequest($option . 'category_status', 'category_status', '', 'int');
		$search1 = $search;

		// Default List Limit
		$limit = $mainframe->getUserStateFromRequest($option . ' . limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option . ' . limitstart', 'limitstart', 0, 'int');

		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		$where = '';
		$search = $this->phpSlashes($search);

		// Query to fetch sub categories
		if ($search)
		{
			$where .= " a.category LIKE '%$search%'";
			$lists['category_search'] = $search1;
		}

		// Filtering based on status
		if ($state_filter)
		{
			if ($state_filter == 1)
			{
				$state_filterval = 1;
			}
			elseif ($state_filter == 2)
			{
				$state_filterval = 0;
			}
			else
			{
				$state_filterval = -2;
			}

			if ($search)
			{
				$where .= ' AND ';
			}

			$where .= " a.published = $state_filterval";
			$lists['category_status'] = $state_filter;
		}
		else
		{
			if ($search)
			{
				$where .= ' AND ';
			}

			$where .= " a.published != -2";
		}

		$fields = array(
			$db->quoteName('a.id') . ' AS value',
			$db->quoteName('a.category') . ' AS text',
			$db->quoteName('a.ordering'),
			$db->quoteName('a.published'),
			'COUNT(DISTINCT b.id) AS level'
			);
		$query->clear()
				->select($fields)
				->from($db->quoteName('#__hdflv_category') . ' AS a')
				->leftJoin('#__hdflv_category AS b ON a.lft > b.lft AND a.rgt < b.rgt')
				->where($where)
				->group($db->escape('a.id' . ' ,' . 'a.category' . ' , ' . 'a.lft' . ' , ' . 'a.rgt'))
				->order($filter_order . ' ' . $filter_order_Dir);
		$db->setQuery($query);
		$db->query();
		$categoryCount = $db->getNumRows();

		// Set pagination
		$pageNav = new JPagination($categoryCount, $limitstart, $limit);

		$db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$categorylist = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			JError::raiseWarning($db->getErrorNum(), $db->stderr());
		}

		return array('pageNav' => $pageNav, 'limitstart' => $limitstart, 'limit' => $limit, 'categoryFilter' => $lists, 'categorylist' => $categorylist);
	}

	/**
	 * Function to category details
	 * 
	 * @param   int  $id  category id
	 * 
	 * @return  getcategorydetails
	 */
	public function getcategorydetails($id)
	{
		$db = $this->getDBO();

		// Query to fetch details of selected category
		$query = $db->getQuery(true);
		$query->clear()
				->select($db->quoteName(array('id','member_id','category','seo_category','parent_id','ordering','published')))
				->from($db->quoteName('#__hdflv_category'))
				->where($db->quoteName('id') . '= ' . $id);
		$db->setQuery($query);
		$category = $db->loadObject();

		$fields = array(
			$db->quoteName('a.id') . ' AS value',
			$db->quoteName('a.category') . ' AS text',
			'COUNT(DISTINCT b.id) AS level'
			);
		$query->clear()
				->select($fields)
				->from($db->quoteName('#__hdflv_category') . ' AS a')
				->leftJoin('#__hdflv_category AS b ON a.lft > b.lft AND a.rgt < b.rgt')
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('a.id') . ' != ' . $db->quote($id))
				->group($db->escape('a.id' . ' ,' . 'a.category' . ' , ' . 'a.lft' . ' , ' . 'a.rgt'))
				->order('a.lft ASC');
		$db->setQuery($query);
		$categorylist = $db->loadObjectList();

		foreach ($categorylist as &$option)
		{
			$option->text = str_repeat('- ', $option->level) . $option->text;
		}

		if ($db->getErrorNum())
		{
			JError::raiseWarning($db->getErrorNum(), $db->stderr());
		}

		return array($category, $categorylist);
	}

	/**
	 * Function to fetch categories,ads and adding new video
	 * 
	 * @return  addvideosmodel
	 */
	public function getNewcategory()
	{
		global $db;
		$query = $db->getQuery(true);
		$fields = array(
			$db->quoteName('a.id') . ' AS value',
			$db->quoteName('a.category') . ' AS text',
			'COUNT(DISTINCT b.id) AS level'
			);
		$query->clear()
				->select($fields)
				->from($db->quoteName('#__hdflv_category') . ' AS a')
				->leftJoin('#__hdflv_category AS b ON a.lft > b.lft AND a.rgt < b.rgt')
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
				->group($db->escape('a.id' . ' ,' . 'a.category' . ' , ' . 'a.lft' . ' , ' . 'a.rgt'))
				->order('a.lft ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();

		foreach ($options as &$option)
		{
			$option->text = str_repeat('- ', $option->level) . $option->text;
		}

		$objCategoryTable = $this->getTable('category');
		$objCategoryTable->id = 0;
		$objCategoryTable->category = '';
		$objCategoryTable->published = '';

		/** get the most recent database error code
		 * display the last database error message in a standard format
		 *
		 */
		if ($db->getErrorNum())
		{
			JError::raiseWarning($db->getErrorNum(), $db->stderr());
		}

		return array($objCategoryTable, $options);
	}

	/**
	 * Function to fetch categories,ads and adding new video
	 * 
	 * @param   array  $arrFormData  category detail array
	 * 
	 * @return  addvideosmodel
	 */
	public function savecategory($arrFormData)
	{
		global $mainframe;
		$db = $this->getDBO();
		$query = $db->getQuery(true);
		$objCategoryTable = $this->getTable('category');

		// Code for seo category name
		$seo_category = $arrFormData['category'];
		$category_id = $arrFormData['id'];
		$query->clear()
				->select($db->quoteName(array('id','published')))
				->from($db->quoteName('#__hdflv_category'))
				->where($db->quoteName('category') . '=\'' . $seo_category . '\'');
		$db->setQuery($query);
		$category = $db->loadObjectList();

		if (isset($category[0]->id) && $category[0]->id != 0 && $category[0]->published == 1)
		{
			if ($category[0]->id == $category_id)
			{
				if (!$objCategoryTable->bind($arrFormData))
				{
					JError::raiseWarning(500, $objCategoryTable->getError());
				}

				if (!$objCategoryTable->check())
				{
					JError::raiseWarning(500, $objCategoryTable->getError());
				}

				if (!$objCategoryTable->store())
				{
					JError::raiseWarning(500, $objCategoryTable->getError());
				}

				$this->rebuild(0, 0);
			}
			else
			{
				$msg = 'Category already exist';
				$link = 'index.php?option=com_contushdvideoshare&layout=category';
				$mainframe->redirect($link, $msg, 'message');
			}
		}
		elseif ($category[0]->published == -2)
		{
			$msg = 'Category already exist. Please check in your trash . ';
			$link = 'index.php?option=com_contushdvideoshare&layout=category';
			$mainframe->redirect($link, $msg, 'message');
		}
		else
		{
			$parent_id = $arrFormData['parent_id'];
			$query->clear()
					->select($db->quoteName(array('ordering')))
				->from($db->quoteName('#__hdflv_category'))
				->where($db->quoteName('parent_id') . '=\'' . $parent_id . '\'');
			$db->setQuery($query);
			$ordering = $db->loadObjectList();
			$ordering_count = count($ordering);
			$arrFormData['ordering'] = $ordering_count + 1;
			$seo_category = stripslashes($seo_category);
			$seo_category = strtolower($seo_category);
			$seo_category = preg_replace('/[&:\s]+/i', '-', $seo_category);
			$arrFormData['seo_category'] = preg_replace('/[#!@$%^.,:;\/&*(){}\"\'\[\]<>|?]+/i', '', $seo_category);
			$arrFormData['seo_category'] = preg_replace('/---|--+/i', '-', $arrFormData['seo_category']);

			if (!$objCategoryTable->bind($arrFormData))
			{
				JError::raiseWarning(500, $objCategoryTable->getError());
			}

			if (!$objCategoryTable->check())
			{
				JError::raiseWarning(500, $objCategoryTable->getError());
			}

			if (!$objCategoryTable->store())
			{
				JError::raiseWarning(500, $objCategoryTable->getError());
			}

			$this->rebuild(0, 0);
		}
	}

	/**
	 * Function to fetch categories,ads and adding new video
	 * 
	 * @param   int  $parent_id  category parent id
	 * @param   int  $left       category order
	 * 
	 * @return  rebuild
	 */
	public function rebuild($parent_id = 0, $left = 0)
	{
		// Get the database object
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Get all children of this node
		$query->clear()
				->select($db->quoteName(array('id')))
				->from($db->quoteName('#__hdflv_category'))
				->where($db->quoteName('parent_id') . '=\'' . (int) $parent_id . '\'')
				->order('category ASC');
		$db->setQuery($query);
		$children = $db->loadObjectList();

		// The right value of this node is the left value + 1
		$right = $left + 1;

		// Execute this function recursively over all children
		for ($i = 0, $n = count($children); $i < $n; $i++)
		{
			// $right is the current right value, which is incremented on recursion return
			$right = $this->rebuild($children[$i]->id, $right);

			// If there is an update failure, return false to break out of the recursion
			if ($right === false)
			{
				return false;
			}
		}

		// Fields to update.
		$fields = array(
			$db->quoteName('lft') . '=\'' . (int) $left . '\'',
			$db->quoteName('rgt') . '=\'' . (int) $right . '\''
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('id') . '=' . (int) $parent_id
		);

		//  Update streamer option,thumb url and file path
		$query->clear()
				->update($db->quoteName('#__hdflv_category'))->set($fields)->where($conditions);
		$db->setQuery($query);

		// If there is an update failure, return false to break out of the recursion
		if (!$db->query())
		{
			return false;
		}

		// Return the right value of this node + 1
		return $right + 1;
	}

	/**
	 * Function to fetch categories,ads and adding new video
	 * 
	 * @param   array  $arrayIDs  category detail array
	 * 
	 * @return  addvideosmodel
	 */
	public function deletecategary($arrayIDs)
	{
		global $db;
		$query = $db->getQuery(true);

		if (count($arrayIDs))
		{
			$cids = implode(',', $arrayIDs);
			$query->clear()
						->delete($db->quoteName('#__hdflv_category'))
						->where($db->quoteName('id') . 'IN (' . $cids . ')');
				$db->setQuery($query);
				$db->query();
		}
	}

	/**
	 * Function to fetch category parent ids
	 * 
	 * @param   array  $id  category id
	 * 
	 * @return  array
	 */
	public function getcategoryids($id)
	{
		global $db,$cateid;
		$categoryid = '';
		$query = $db->getQuery(true);
		$query->clear()
				->select($db->quoteName(array('id')))
				->from($db->quoteName('#__hdflv_category'))
				->where($db->quoteName('parent_id') . 'IN (' . $id . ') AND published != -2');
		$db->setQuery($query);
		$categoryids = $db->loadColumn();
		$cateid .= $id;

		if (!empty($categoryids))
		{
			$cids = implode(',', $categoryids);
			$cateid .= ',';
			$categoryid = self::getcategoryids($cids);
		}

		return $cateid;
	}

	/**
	 * Function to fetch categories,ads and adding new video
	 * 
	 * @param   array  $arrayIDs  category detail array
	 * 
	 * @return  addvideosmodel
	 */
	public function changeStatus($arrayIDs)
	{
		global $mainframe, $db;
		$query = $db->getQuery(true);

		if ($arrayIDs['task'] == "publish")
		{
			$publish = 1;
			$msg = 'Published Successfully';
		}
		elseif ($arrayIDs['task'] == 'trash') {
			$publish = -2;
			$msg = 'Trashed Successfully';
		}
		else
		{
			$publish = 0;
			$msg = 'Unpublished Successfully';
		}

		$cids1 = $arrayIDs['cid'];
		$categoryTable = JTable::getInstance('category', 'Table');
		$cids = implode(',', $arrayIDs['cid']);
		$query->clear()
				->select($db->quoteName(array('parent_id')))
				->from($db->quoteName('#__hdflv_category'))
				->where($db->quoteName('id') . 'IN (' . $cids . ') AND published != -2');
		$db->setQuery($query);
		$options = $db->loadResult();

		// Recurse through children if they exist
		$categoryid = array();

		if (!empty($cids1))
		{
			foreach ($cids1 as $cids2)
			{
				$categoryid = self::getcategoryids($cids);
			}
		}

		if ($options != 0)
		{
			$query->clear()
				->select($db->quoteName(array('published')))
				->from($db->quoteName('#__hdflv_category'))
				->where($db->quoteName('id') . 'IN (' . $options . ') AND published != -2');
			$db->setQuery($query);
			$published = $db->loadResult();

			if ($published == 0)
			{
				$msg = 'Cannot change the published state when the parent category is of a lesser state . ';
				$link = 'index.php?option=com_contushdvideoshare&layout=category';
				$mainframe->redirect($link, $msg, 'message');
			}
		}

		$categoryTable->publish($cids1, $publish);

		// Fields to update.
		$fields = array(
			$db->quoteName('published') . '=' . $publish
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('parent_id') . ' IN (' . $categoryid . ' ) AND published != -2'
		);

		//  Update streamer option,thumb url and file path
		$query->clear()
			->update($db->quoteName('#__hdflv_category'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->query();
		$link = 'index.php?option=com_contushdvideoshare&layout=category';
		$mainframe->redirect($link, $msg, 'message');
	}
}
