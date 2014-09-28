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

// Import joomla model library
jimport('joomla.application.component.model');

/**
 * Admin google adsense model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareModelgooglead extends ContushdvideoshareModel
{
	/**
	 * Function to get google adsense
	 * 
	 * @return  getgooglead
	 */
	public function getgooglead()
	{
		$db = $this->getDBO();
		$rs_googlead = JTable::getInstance('googlead', 'Table');

		// To get the id no to be edited...
		$id = 1;
		$rs_googlead->load($id);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', $rs_googlead->publish);

		return $rs_googlead;
	}

	/**
	 * Function to save google adsense
	 * 
	 * @return  savegooglead
	 */
	public function savegooglead()
	{
		$option = JRequest::getCmd('option');
		$arrFormData = JRequest::get('POST');
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$objGoogleAdTable = & JTable::getInstance('googlead', 'Table');
		$id = 1;

		if (JRequest::getVar('reopenadd') == '')
		{
			$arrFormData['reopenadd'] = '1';
			$arrFormData['ropen'] = '';
		}

		$code = JRequest::getVar('code', '', 'post', 'string', JREQUEST_ALLOWRAW);

		// Convert all applicable characters to HTML entities
		$arrFormData['code'] = htmlentities(stripslashes($code));

		// Get the node from the table.
		$objGoogleAdTable->load($id);

		// Bind data to the table object.
		if (!$objGoogleAdTable->bind($arrFormData))
		{
			JError::raiseWarning(500, $objGoogleAdTable->getError());
		}

		// Store the node in the database table.
		if (!$objGoogleAdTable->store())
		{
			JError::raiseWarning(500, $objGoogleAdTable->getError());
		}

		// Page redirect
		$link = 'index.php?option=' . $option . '&layout=googlead';
		$mainframe->redirect($link, 'Saved Successfully', 'message');
	}
}
