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
jimport('joomla.access.access');

// Import joomla view library
jimport('joomla.application.component.view');

// Import Joomla pagination
jimport('joomla.html.pagination');

/**
 * Show videos in grid view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewshowvideos extends ContushdvideoshareView
{
	protected $canDo;

	/**
	 * Function to prepare view for showvideos
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewshowvideos		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function showvideos($cachable = false, $urlparams = false)
	{
		JHTML::stylesheet('styles.css', 'administrator/components/com_contushdvideoshare/css/');

		if (JRequest::getVar('page') != 'comment')
		{
			$model = $this->getModel();
			$showvideos = $model->showvideosmodel();
			$this->assignRef('videolist', $showvideos);
			$Itemid = $model->getmenuitemid_thumb();
			$this->assignRef('Itemid', $Itemid);
		}

		if (JRequest::getVar('page') == 'comment')
		{
			$model = $this->getModel('showvideos');
			$comment = $model->getcomment();
			$this->assignRef('comment', $comment);
			parent::display();
		}
		else
		{
			parent::display();
		}

		$this->addToolbar();
	}

	/**
	 * Function to set the toolbar
	 * 
	 * @return  showads
	 */
	protected function addToolBar()
	{
		if (JRequest::getVar('page') == 'comment')
		{
			JToolBarHelper::title('Comments');
		}
		elseif (JRequest::getVar('user', '', 'get'))
		{
			JToolBarHelper::title(JText::_('Admin Videos'), 'adminvideos');
		}
		else
		{
			JToolBarHelper::title(JText::_('Member Videos'), 'membervideos');
		}

		if (version_compare(JVERSION, '2.5.0', 'ge') || version_compare(JVERSION, '1.6', 'ge')
			|| version_compare(JVERSION, '1.7', 'ge') || version_compare(JVERSION, '3.0', 'ge'))
		{
			require_once JPATH_COMPONENT . '/helpers/contushdvideoshare.php';

			// What Access Permissions does this user have? What can (s)he do?
			$this->canDo = ContushdvideoshareHelper::getActions();

			if (JRequest::getVar('page') != 'comment')
			{
				if ($this->canDo->get('core.create'))
				{
					if (JRequest::getVar('user', '', 'get'))
					{
						JToolBarHelper::addNew('addvideos', 'New Video');
					}
				}

				if ($this->canDo->get('core.edit'))
				{
					JToolBarHelper::editList('editvideos', 'Edit');
				}

				if ($this->canDo->get('core.delete'))
				{
					if (JRequest::getVar('filter_state') == 3)
					{
						JToolBarHelper::deleteList('', 'Removevideos', 'JTOOLBAR_EMPTY_TRASH');
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
					JToolBarHelper::custom($task = 'featured', $icon = 'featured.png', $iconOver = 'featured.png', $alt = 'Enable Featured', $listSelect = true);
					JToolBarHelper::custom(
							$task = 'unfeatured', $icon = 'unfeatured.png', $iconOver = 'unfeatured.png',
							$alt = 'Disable Featured', $listSelect = true
							);
				}

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::divider();
					JToolBarHelper::preferences('com_contushdvideoshare');
				}
			}
			else
			{
				JToolBarHelper::cancel('Commentcancel', 'Cancel');
			}
		}
		else
		{
			if (JRequest::getVar('page') != 'comment')
			{
				if (JRequest::getVar('user', '', 'get'))
				{
					JToolBarHelper::addNew('addvideos', 'New Video');
				}

				JToolBarHelper::editList('editvideos', 'Edit');

				if (JRequest::getVar('filter_state') == 3)
				{
					JToolBarHelper::deleteList('', 'Removevideos', 'JTOOLBAR_EMPTY_TRASH');
				}
				else
				{
					JToolBarHelper::trash('trash');
				}

				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::custom($task = 'featured', $icon = 'featured.png', $iconOver = 'featured.png', $alt = 'Enable Featured', $listSelect = true);
				JToolBarHelper::custom(
						$task = 'unfeatured', $icon = 'unfeatured.png', $iconOver = 'unfeatured.png',
						$alt = 'Disable Featured', $listSelect = true
						);
			}
			else
			{
				JToolBarHelper::cancel('Commentcancel', 'Cancel');
			}
		}
	}
}
