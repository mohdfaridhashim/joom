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

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * Admin control panel view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewcontrolpanel extends ContushdvideoshareView
{
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
		if (JRequest::getVar('task') == 'edit' || JRequest::getVar('task') == '')
		{
			JToolBarHelper::title('HD Video Share Control Panel', 'manege-pins.png');
			$model = $this->getModel();
			$controlpaneldetails = $model->controlpaneldetails();
			$this->assignRef('controlpaneldetails', $controlpaneldetails);
			parent::display();
		}
	}
}
