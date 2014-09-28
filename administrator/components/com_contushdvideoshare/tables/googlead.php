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
 * Admin googlead table class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Tablegooglead extends JTable
{
	public $id = null;

	public $code = null;

	public $showoption = null;

	public $closeadd = null;

	public $reopenadd = null;

	public $publish = null;

	public $ropen = null;

	public $showaddc = null;

	public $showaddm = null;

	public $showaddp = null;

	/**
	 * Function to save googlead
	 * 
	 * @param   object  &$db  Database detail
	 * 
	 * @return  Tablegooglead
	 */
	public function Tablegooglead(&$db)
	{
		parent::__construct('#__hdflv_googlead', 'id', $db);
	}
}
