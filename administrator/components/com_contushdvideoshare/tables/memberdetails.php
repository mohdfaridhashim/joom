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
defined('_JEXEC') or die('Restricted Access');

/**
 * Admin memberdetails table class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Tablememberdetails extends JTable
{
	public $id = null;

	public $name = null;

	public $username = null;

	public $email = null;

	public $password = null;

	public $created_date = null;

	public $published = null;

	/**
	 * Function to save memberdetails
	 * 
	 * @param   object  &$db  Database detail
	 * 
	 * @return  Tablememberdetails
	 */
	public function Tablememberdetails(&$db)
	{
		parent::__construct('#__hdflv_member_details', 'id', $db);
	}
}
