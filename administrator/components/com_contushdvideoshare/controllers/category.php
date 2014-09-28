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

// Import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * Admin category controller class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareControllercategory extends ContusvideoshareController
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareControllercategory		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$viewName = JRequest::getVar('view', 'category');
		$viewLayout = JRequest::getVar('layout', 'category');
		$view = $this->getView($viewName);

		if ($model = $this->getModel('category'))
		{
			$view->setModel($model, true);
		}

		$view->setLayout($viewLayout);
		$view->display();
	}

	/** 
	 * Function to save category 
	 * 
	 * @return  save
	 */
	public function save()
	{
		$detail = JRequest::get('POST');
		$model = $this->getModel('category');
		$model->savecategory($detail);
		$this->setRedirect('index.php?option=' . JRequest::getVar('option') . '&layout=category', 'Saved Successfully');
	}

	/** 
	 * Function to remove category 
	 * 
	 * @return  remove
	 */
	public function remove()
	{
		$arrayIDs = JRequest::getVar('cid', null, 'default', 'array');

		// Reads cid as an array
		if ($arrayIDs[0] === null)
		{
			// Make sure the cid parameter was in the request
			JError::raiseWarning(500, 'Category missing from the request');
		}

		$model = $this->getModel('category');
		$model->deletecategary($arrayIDs);
		$this->setRedirect('index.php?option=' . JRequest::getVar('option') . '&layout=category', 'Saved Successfully');
	}

	/** 
	 * Function to cancel category 
	 * 
	 * @return  cancel
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=' . JRequest::getVar('option') . '&layout=category');
	}

	/** 
	 * Function to publish category 
	 * 
	 * @return  publish
	 */
	public function publish()
	{
		$detail = JRequest::get('POST');
		$model = $this->getModel('category');
		$model->changeStatus($detail);
		$this->setRedirect('index.php?option=' . JRequest::getVar('option') . '&layout=category');
	}

	/** 
	 * Function to unpublish category 
	 * 
	 * @return  unpublish
	 */
	public function unpublish()
	{
		$detail = JRequest::get('POST');
		$model = $this->getModel('category');
		$model->changeStatus($detail);
		$this->setRedirect('index.php?option=' . JRequest::getVar('option') . '&layout=category');
	}

	/** 
	 * Function to save category 
	 * 
	 * @return  apply
	 */
	public function apply()
	{
		$detail = JRequest::get('POST');
		$model = $this->getModel('category');
		$model->savecategory($detail);
		$link = 'index.php?option=' . JRequest::getVar('option') . '&layout=category&task=edit&cid[]=' . $detail['id'];
		$this->setRedirect($link, 'Saved Successfully');
	}

	/** 
	 * Function to trash category 
	 * 
	 * @return  trash
	 */
	public function trash()
	{
		$detail = JRequest::get('POST');
		$model = $this->getModel('category');
		$model->changeStatus($detail);
		$this->setRedirect('index.php?option=' . JRequest::getVar('option') . '&layout=category');
	}
}
