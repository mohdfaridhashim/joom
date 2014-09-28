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

// User access levels
jimport('joomla.access.access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * Admin videos view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewadminvideos extends ContushdvideoshareView
{
	/**
	 * Function to set icons and values for admin grid view
	 * 
	 * @return  adminvideos
	 */
	public function adminvideos()
	{
		JHTML::stylesheet('styles.css', 'administrator/components/com_contushdvideoshare/css/');

		if (JRequest::getVar('user', '', 'get') && JRequest::getVar('user', '', 'get') == 'admin')
		{
			JToolBarHelper::title(JText::_('Admin Videos'), 'adminvideos');
		}
		else
		{
			JToolBarHelper::title(JText::_('Member Videos'), 'membervideos');
		}

		JToolBarHelper::save('savevideos', 'Save');
		JToolBarHelper::apply('applyvideos', 'Apply');
		JToolBarHelper::cancel('CANCEL7', 'Cancel');
		$model = $this->getModel();
		$videoslist = $model->addvideosmodel();
		$this->assignRef('editvideo', $videoslist);
		$player_values = $model->showplayersettings();
		$this->assignRef('player_values', $player_values);
		parent::display();
	}

	/**
	 * Function to set icons and values for admin edit view
	 * 
	 * @return  adminvideos
	 */
	public function editvideos()
	{
		JHTML::stylesheet('styles.css', 'administrator/components/com_contushdvideoshare/css/');

		if (JRequest::getVar('user', '', 'get') && JRequest::getVar('user', '', 'get') == 'admin')
		{
			JToolBarHelper::title(JText::_('Admin Videos'), 'adminvideos');
		}
		else
		{
			JToolBarHelper::title(JText::_('Member Videos'), 'membervideos');
		}

		JToolBarHelper::save('savevideos', 'Save');
		JToolBarHelper::apply('applyvideos', 'Apply');
		JToolBarHelper::cancel('CANCEL7', 'Cancel');
		$model = $this->getModel();
		$editvideoslist = $model->editvideosmodel();
		$this->assignRef('editvideo', $editvideoslist);
		$player_values = $model->showplayersettings();
		$this->assignRef('player_values', $player_values);
		parent::display();
	}
}
