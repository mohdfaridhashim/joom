<?php
/**
 * IMA ad model for HD Video Share
 *
 * This file is to fetch IMA Ad details from database for the player
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
 * IMA ad xml model class
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Modelcontushdvideoshareimaadxml extends ContushdvideoshareModel
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

		$query->select(array('id', 'published', 'adsname', 'imaaddet', 'typeofadd'))
				->from('#__hdflv_ads')
				->where($db->quoteName('published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('typeofadd') . ' = ' . $db->quote('ima'))
				->order($db->escape('id' . ' ' . 'DESC'));

		$db->setQuery($query);
		$rs_ads = $db->loadObject();
		$rows = unserialize($rs_ads->imaaddet);

		$query->clear()
				->select('player_values')
				->from('#__hdflv_player_settings');

		$db->setQuery($query);
		$settingResult = $db->loadResult();
		$settings = unserialize($settingResult);

		$this->showadsxml($rows, $settings);
	}

	/**
	 * Function to show ads
	 * 
	 * @param   array  $rows      IMA ad detail in array format
	 * @param   array  $settings  Player width and height
	 * 
	 * @return  string
	 */
	public function showadsxml($rows, $settings)
	{
		ob_clean();
		header("content-type: text/xml");
		echo '<?xml version="1.0" encoding="utf-8"?>';
		echo '<ima>';

		if (count($rows) > 0)
		{
			$imaadwidth = $settings['width'] - 30;
			$imaadheight = $settings['height'] - 60;
			$imaadpath = $rows['imaadpath'];
			$publisherId = $rows['publisherId'];
			$contentId = $rows['contentId'];
			$imaadType = $rows['imaadtype'];

			if ($imaadType == 'videoad')
			{
				$imaadType = '';
				$channels = '';
			}
			else
			{
				$imaadType = 'Text';
				$channels = $rows['channels'];
			}

			// Video ads
			echo '<adSlotWidth>' . $imaadwidth . '</adSlotWidth>
				<adSlotHeight>' . $imaadheight . '</adSlotHeight>
				<adTagUrl>' . $imaadpath . '</adTagUrl>';

			// Text ads size(468,60)
			echo '<publisherId>' . $publisherId . '</publisherId>
				<contentId>' . $contentId . '</contentId>';

			// Text or Overlay
			echo '<adType>' . $imaadType . '</adType>
				<channels>' . $channels . '</channels>';
		}
		else
		{
			// Video ads
			echo '<adSlotWidth>400</adSlotWidth>
				<adSlotHeight>250</adSlotHeight>
				<adTagUrl>
http://ad.doubleclick.net/pfadx/N270.126913.6102203221521/B3876671.22;dcadv=2215309;sz=0x0;ord=%5btimestamp%5d;dcmt=text/xml
				</adTagUrl>';

			// Text ads size(468,60)
			echo '<publisherId></publisherId>
				<contentId>1</contentId>';

			// Text or Overlay
			echo ' <adType>Text</adType>
				<channels>poker</channels>';
		}

		echo '</ima>';
		exit();
	}
}
