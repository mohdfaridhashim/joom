<?php
/**
 * Ad xml file for HD Video Share
 *
 * This file is to display Ad XML for the player 
 *
 * @category   Apptha
 * @package    Com_Contushdvideoshare
 * @version    3.6
 * @author     Apptha Team <developers@contus.in>
 * @copyright  Copyright (C) 2014 Apptha. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 */

// No direct acesss
defined('_JEXEC') or die('Restricted access');

// Import Joomla model library
jimport('joomla.application.component.model');

/**
 * Adsxml model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Modelcontushdvideoshareadsxml extends ContushdvideoshareModel
{
	/**
	 * Function to get ads
	 * 
	 * @return  array
	 */
	public function getads()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select(
						array(
							'id', 'published', 'adsname', 'filepath', 'postvideopath', 'targeturl',
							'clickurl', 'impressionurl', 'adsdesc', 'typeofadd'
						)
				)
				->from('#__hdflv_ads')
				->where($db->quoteName('published') . ' = ' . $db->quote('1') . ' AND ' . $db->quoteName('typeofadd') . ' = ' . $db->quote('prepost'));
		$db->setQuery($query);
		$rs_ads = $db->loadObjectList();
		$this->showadsxml($rs_ads);
	}

	/**
	 * Function to show ads
	 * 
	 * @param   array  $rs_ads  ad detail in array format
	 * 
	 * @return  string
	 */
	public function showadsxml($rs_ads)
	{
		ob_clean();
		header("content-type: text/xml");
		echo '<?xml version="1.0" encoding="utf-8"?>';
		echo '<ads random="false">';
		$current_path = "components/com_contushdvideoshare/videos/";
		$defined_clickpath = JURI::base() . '?option=com_contushdvideoshare&view=impressionclicks&click=click';
		$defined_impressionpath = JURI::base() . '?option=com_contushdvideoshare&view=impressionclicks&click=impression';

		if (count($rs_ads) > 0)
		{
			foreach ($rs_ads as $rows)
			{
				if ($rows->filepath == "File")
				{
					$postvideo = JURI::base() . $current_path . $rows->postvideopath;
				}
				elseif ($rows->filepath == "Url")
				{
					$postvideo = $rows->postvideopath;
				}

				$targeturl = $rows->targeturl;

				if (!empty($rows->targeturl) && !preg_match("~^(?:f|ht)tps?://~i", $rows->targeturl))
				{
					$targeturl = "http://" . $rows->targeturl;
				}

				if(!empty($rows->clickurl))
				{
					$clickpath = $rows->clickurl;

					if (!preg_match("~^(?:f|ht)tps?://~i", $clickpath))
					{
						$clickpath = "http://" . $clickpath;
					}
				}
				else
				{
					$clickpath = $defined_clickpath;
				}

				if(!empty($rows->impressionurl))
				{
					$impressionpath = $rows->impressionurl;

					if (!preg_match("~^(?:f|ht)tps?://~i", $impressionpath))
					{
						$impressionpath = "http://" . $impressionpath;
					}

				}
				else
				{
					$impressionpath = $defined_impressionpath;
				}

				echo '<ad id="' . $rows->id . '" url="' . $postvideo
						. '" targeturl="' . $targeturl
						. '" clickurl="' . $clickpath . '" impressionurl="'
						. $impressionpath . '">';
				echo '<![CDATA[' . $rows->adsdesc . ']]>';
				echo '</ad>';
			}
		}

		echo '</ads>';
		exit();
	}
}
