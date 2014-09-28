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
// No direct access
defined('_JEXEC') or die('Restricted access');

// Import joomla view library
jimport('joomla.application.component.view');

// Import Joomla pagination
jimport('joomla.html.pagination');

/**
 * Show ads view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewshowads extends ContushdvideoshareView
{
	protected $canDo;

	/**
	 * Function to manage ads
	 * 
	 * @return  showads
	 */
	public function showads()
	{
		JHTML::stylesheet('styles.css', 'administrator/components/com_contushdvideoshare/css/');
		$this->addToolbar();
		$model = $this->getModel();
		$showads = $model->showadsmodel();
		$this->assignRef('showads', $showads);
		parent::display();
	}

	/**
	 * Function to set the toolbar
	 * 
	 * @return  addToolBar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('Video Ads'), 'ads');

		if (version_compare(JVERSION, '2.5.0', 'ge') || version_compare(JVERSION, '1.6', 'ge')
			|| version_compare(JVERSION, '1.7', 'ge') || version_compare(JVERSION, '3.0', 'ge'))
		{
			require_once JPATH_COMPONENT . '/helpers/contushdvideoshare.php';

			// What Access Permissions does this user have? What can (s)he do?
			$this->canDo = ContushdvideoshareHelper::getActions();

			if ($this->canDo->get('core.create'))
			{
				JToolBarHelper::addNew('addads', 'New Ad');
			}

			if ($this->canDo->get('core.edit'))
			{
				JToolBarHelper::editList('editads', 'Edit');
			}

			if ($this->canDo->get('core.delete'))
			{
				if (JRequest::getVar('ads_status') == 3)
				{
					JToolBarHelper::deleteList('', 'removeads', 'JTOOLBAR_EMPTY_TRASH');
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
			JToolBarHelper::addNew('addads', 'New Ad');
			JToolBarHelper::editList('editads', 'Edit');

			if (JRequest::getVar('ads_status') == 3)
			{
				JToolBarHelper::deleteList('', 'removeads', 'JTOOLBAR_EMPTY_TRASH');
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
