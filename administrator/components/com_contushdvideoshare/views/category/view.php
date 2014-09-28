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

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * Admin category view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewcategory extends ContushdvideoshareView
{
	protected $canDo;

	/**
	 * Function to view for manage categories
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  contushdvideoshareViewcategory		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		JHTML::stylesheet('styles.css', 'administrator/components/com_contushdvideoshare/css/');

		if (JRequest::getVar('task') == 'edit')
		{
			JToolBarHelper::title('Category' . ': [<small>Edit</small>]', 'category');
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel();
			$model = $this->getModel();
			$id = JRequest::getVar('cid');
			$category = $model->getcategorydetails($id[0]);
			$this->assignRef('category', $category[0]);
			$this->assignRef('categorylist', $category[1]);
			parent::display();
		}

		if (JRequest::getVar('task') == 'add')
		{
			JToolBarHelper::title('Category' . ': [<small>Add</small>]', 'category');
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			$model = $this->getModel();
			$category = $model->getNewcategory();
			$this->assignRef('category', $category[0]);
			$this->assignRef('categorylist', $category[1]);
			parent::display();
		}

		if (JRequest::getVar('task') == '')
		{
			$this->addToolbar();
			$model = $this->getModel('category');
			$category = $model->getcategory();
			$this->assignRef('category', $category);
			parent::display();
		}
	}

	/**
	 * Function for Setting the toolbar
	 * 
	 * @return  addToolBar
	 */
	protected function addToolBar()
	{
		if (version_compare(JVERSION, '2.5.0', 'ge') || version_compare(JVERSION, '1.6', 'ge')
			|| version_compare(JVERSION, '1.7', 'ge') || version_compare(JVERSION, '3.0', 'ge'))
				{
			require_once JPATH_COMPONENT . '/helpers/contushdvideoshare.php';

			// What Access Permissions does this user have? What can (s)he do?
			$this->canDo = ContushdvideoshareHelper::getActions();
			JToolBarHelper::title('Category', 'category');

			if ($this->canDo->get('core.create'))
			{
				JToolbarHelper::addNew();
			}

			if ($this->canDo->get('core.edit'))
			{
				JToolBarHelper::editList();
			}

			if ($this->canDo->get('core.delete'))
			{
				if (JRequest::getVar('category_status') == 3)
				{
					JToolBarHelper::deleteList('', 'remove', 'JTOOLBAR_EMPTY_TRASH');
				}
				else
				{
					JToolBarHelper::trash('trash');
				}
			}

			if ($this->canDo->get('core.edit.state'))
			{
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
			}

			if ($this->canDo->get('core.admin'))
			{
				JToolBarHelper::divider();
				JToolBarHelper::preferences('com_contushdvideoshare');
			}
		}
		else
		{
			JToolBarHelper::addNew();
			JToolBarHelper::editList();

			if (JRequest::getVar('category_status') == 3)
			{
				JToolBarHelper::deleteList('', 'remove', 'JTOOLBAR_EMPTY_TRASH');
			}
			else
			{
				JToolBarHelper::trash('trash');
			}

			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
		}
	}
}
