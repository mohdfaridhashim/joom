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
 * Admin site settings table class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Tablesitesettings extends JTable
{
	public $id = null;

	public $published = null;

	public $thumbview = null;

	public $homethumbview = null;

	public $sidethumbview = null;

	public $dispenable = null;

	/**
	 * Function to save sitesettings
	 * 
	 * @param   object  &$db  Database detail
	 * 
	 * @return  Tablesitesettings
	 */
	public function Tablesitesettings(&$db)
	{
		parent::__construct('#__hdflv_site_settings', 'id', $db);
	}
}
