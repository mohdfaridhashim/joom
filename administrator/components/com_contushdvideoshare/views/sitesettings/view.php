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
 * Site settings view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewsitesettings extends ContushdvideoshareView
{
	protected $canDo;

	/**
	 * Function to prepare view for Site settings
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewsitesettings		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		if (JRequest::getVar('task') == 'edit' || JRequest::getVar('task') == '')
		{
			JHTML::stylesheet('styles.css', 'administrator/components/com_contushdvideoshare/css/');
			$this->addToolbar();

			$model = $this->getModel();
			$setting = $model->getsitesetting();

			// Assign data to the view
			$this->assignRef('sitesettings', $setting[0]);
			$this->assignRef('jomcomment', $setting[1]);
			$this->assignRef('jcomment', $setting[2]);

			// Display the view
			parent::display();
		}
	}

	/**
	 * Function to set the toolbar
	 * 
	 * @return  showads
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('Site Settings'), 'sitesettings');

		if (version_compare(JVERSION, '2.5.0', 'ge') || version_compare(JVERSION, '1.6', 'ge')
			|| version_compare(JVERSION, '1.7', 'ge') || version_compare(JVERSION, '3.0', 'ge'))
		{
			require_once JPATH_COMPONENT . '/helpers/contushdvideoshare.php';

			// What Access Permissions does this user have? What can (s)he do?
			$this->canDo = ContushdvideoshareHelper::getActions();

			if ($this->canDo->get('core.admin'))
			{
				JToolBarHelper::apply();
				JToolBarHelper::divider();
				JToolBarHelper::preferences('com_contushdvideoshare');
			}
		}
		else
		{
			JToolBarHelper::apply();
		}
	}
}
