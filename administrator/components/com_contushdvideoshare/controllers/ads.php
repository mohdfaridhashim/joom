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

// Import joomla controller library
jimport('joomla.application.component.controller');

/**
 * Admin ads controller class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareControllerads extends ContusvideoshareController
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareControllerads		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view = $this->getView('showads');

		if ($model = $this->getModel('showads'))
		{
			$view->setModel($model, true);
		}

		$view->setLayout('showadslayout');
		$view->showads();
	}

	/** 
	 * Function to add ads 
	 * 
	 * @return  addads
	 */
	public function addads()
	{
		$view = $this->getView('ads');

		// Get/Create the model

		if ($model = $this->getModel('addads'))
		{
		// Push the model into the view (as default)
		// Second parameter indicates that it is the default model for the view
			$view->setModel($model, true);
		}

		$view->setLayout('adslayout');
		$view->ads();
	}

	/** 
	 * Function to edit ads 
	 * 
	 * @return  editads
	 */
	public function editads()
	{
		$view = $this->getView('ads');

		// Get/Create the model

		if ($model = $this->getModel('editads'))
		{
			// Push the model into the view (as default)
			// Second parameter indicates that it is the default model for the view
			$view->setModel($model, true);
		}

		$view->setLayout('adslayout');
		$view->editads();
	}

	/** 
	 * Function to save ads 
	 * 
	 * @return  saveads
	 */
	public function saveads()
	{
		// Get/Create the model

		if ($model = $this->getModel('showads'))
		{
		// Push the model into the view (as default)
		// Second parameter indicates that it is the default model for the view
			$model->saveads(JRequest::getVar('task'));
		}
	}

	/** 
	 * Function to apply ads 
	 * 
	 * @return  applyads
	 */
	public function applyads()
	{
		// Get/Create the model
		if ($model = $this->getModel('showads'))
		{
			// Push the model into the view (as default)
			// Second parameter indicates that it is the default model for the view
			$model->saveads(JRequest::getVar('task'));
		}
	}

	/** 
	 * Function to remove ads 
	 * 
	 * @return  removeads
	 */
	public function removeads()
	{
		if ($model = $this->getModel('editads'))
		{
			// Push the model into the view (as default)
			// Second parameter indicates that it is the default model for the view
			$model->removeads();
		}
	}

	/** 
	 * Function to cancel ads 
	 * 
	 * @return  CANCEL6
	 */
	public function CANCEL6()
	{
		$view = $this->getView('showads');

		// Get/Create the model
		if ($model = $this->getModel('showads'))
		{
			$view->setModel($model, true);
		}

		$view->setLayout('showadslayout');
		$view->showads();
	}

	/** 
	 * Function to publish ads 
	 * 
	 * @return  publish
	 */
	public function publish()
	{
		$adsdetail = JRequest::get('POST');
		$model = $this->getModel('showads');
		$model->statusChange($adsdetail);
	}

	/** 
	 * Function to unpublish ads 
	 * 
	 * @return  unpublish
	 */
	public function unpublish()
	{
		$adsdetail = JRequest::get('POST');
		$model = $this->getModel('showads');
		$model->statusChange($adsdetail);
	}

	/** 
	 * Function to trash ads 
	 * 
	 * @return  trash
	 */
	public function trash()
	{
		$adsdetail = JRequest::get('POST');
		$model = $this->getModel('showads');
		$model->statusChange($adsdetail);
	}
}
