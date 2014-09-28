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
 * View class for the hdvideoshare component (Ads tab)
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewads extends ContushdvideoshareView
{
	/**
	 * Function to add ads
	 * 
	 * @return  ads
	 */
	public function ads()
	{
		JHTML::stylesheet('styles.css', 'administrator/components/com_contushdvideoshare/css/');
		JToolBarHelper::title(JText::_('Video Ads'), 'ads');
		JToolBarHelper::save('saveads', 'Save & Close');
		JToolBarHelper::apply('applyads', 'Apply');
		JToolBarHelper::cancel('CANCEL6', 'Cancel');
		$model = $this->getModel();
		$adslist = $model->addadsmodel();
		$this->assignRef('adslist', $adslist);
		parent::display();
	}

	/**
	 * Function to edit ads
	 * 
	 * @return  editads
	 */
	public function editads()
	{
		JToolBarHelper::title(JText::_('Ads') . ': [<small>Edit</small>]');
		JToolBarHelper::save('saveads', 'Save & Close');
		JToolBarHelper::apply('applyads', 'Apply');
		JToolBarHelper::cancel('CANCEL6', 'Cancel');
		$model = $this->getModel();
		$editlist = $model->editadsmodel();
		$this->assignRef('adslist', $editlist);
		parent::display();
	}
}
