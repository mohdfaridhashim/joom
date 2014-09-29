<?php
/**
 * Video Upload view file for front end users
 *
 * This file is to display add video page for front end users
 *
 * @category   Apptha
 * @package    Com_Contushdvideoshare
 * @version    3.6
 * @author     Apptha Team <developers@contus.in>
 * @copyright  Copyright (C) 2014 Apptha. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * Videoupload view file
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewvideoupload extends ContushdvideoshareView
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewvideoupload		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$model = $this->getModel();

		// Get category
		$category = $model->getupload();
		$this->assignRef('videocategory', $category[0]);
		$this->assignRef('upload', $category[1]);
		$this->assignRef('videodetails', $category[2]);
		$get_site_settings = $model->get_site_settings();
		$this->assignRef('dispenable', $get_site_settings);
		$getLicenseKey = $model->getLicenseKey();
		$this->assignRef('licenseKey', $getLicenseKey);
		parent::display();
	}
}
