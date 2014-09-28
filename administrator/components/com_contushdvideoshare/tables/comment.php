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
 * Admin comment table class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Tablecomment extends JTable
{
	public $id = null;

	public $parentid = null;

	public $videoid = null;

	public $name = null;

	public $email = null;

	public $subject = null;

	public $message = null;

	public $created = null;

	public $published = null;

	/**
	 * Function to save comment
	 * 
	 * @param   object  &$db  Database detail
	 * 
	 * @return  Tablecomment
	 */
	public function Tablecomment(&$db)
	{
		parent::__construct('#__hdflv_comments', 'id', $db);
	}
}
