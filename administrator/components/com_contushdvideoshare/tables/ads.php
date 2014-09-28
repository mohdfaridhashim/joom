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

/**
 * Admin ads table class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Tableads extends JTable
{
	public $id = null;

	public $published = null;

	public $adsname = null;

	public $filepath = null;

	public $postvideopath = null;

	public $home = null;

	public $targeturl = null;

	public $clickurl = null;

	public $impressionurl = null;

	public $clickcounts = null;

	public $impressioncounts = null;

	public $adsdesc = null;

	public $typeofadd = null;

	public $imaaddet = null;

	/**
	 * Constructor function to save ads
	 * 
	 * @param   object  &$db  Database detail
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__hdflv_ads', 'id', $db);
	}
}
