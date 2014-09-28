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
defined('_JEXEC') or die;
$db = JFactory::getDBO();
$query = $db->getQuery(true);

$query->select($db->quoteName('dispenable'))
		->from($db->quoteName('#__hdflv_site_settings'))
		->where($db->quoteName('id') . '= 1');
$db->setQuery($query);
$setting_res = $db->loadResult();
$dispenable = unserialize($setting_res);
$bucket = '';

if (isset($dispenable['amazons3']) && $dispenable['amazons3'] == 1)
{
	if (isset($dispenable['amazons3name']))
	{
		$bucket = $dispenable['amazons3name'];
	}

	if (!class_exists('S3'))
	{
		require_once 'S3.php';
	}

	## AWS access info
	if (!defined('awsAccessKey'))
	{
		if (isset($dispenable['amazons3accesskey']))
		{
			define('awsAccessKey', $dispenable['amazons3accesskey']);
		}
	}

	if (!defined('awsSecretKey'))
	{
		if (isset($dispenable['amazons3accesssecretkey_area']))
		{
			define('awsSecretKey', $dispenable['amazons3accesssecretkey_area']);
		}
	}

	// Instantiate the class
	$s3 = new S3(awsAccessKey, awsSecretKey);
	$s3->putBucket($bucket, S3::ACL_PUBLIC_READ);
}
