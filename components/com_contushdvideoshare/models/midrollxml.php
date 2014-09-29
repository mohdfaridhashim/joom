<?php
/**
 * Midroll ad XML model file
 *
 * This file is to fetch Midroll ad details from database
 * and pass values to player 
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
 * Mid roll xml model class
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Modelcontushdvideosharemidrollxml extends ContushdvideoshareModel
{
	/**
	 * Function to midroll ads
	 * 
	 * @return  array
	 */
	public function getads()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*')
				->from('#__hdflv_ads')
				->where($db->quoteName('published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('typeofadd') . ' = ' . $db->quote('mid'));
		$db->setQuery($query);
		$rs_modulesettings = $db->loadObjectList();

		$query->clear()
				->select(array('player_icons', 'player_values'))
				->from('#__hdflv_player_settings');
		$db->setQuery($query);
		$rs_random = $db->loadObject();

		$player_icons = unserialize($rs_random->player_icons);
		$player_values = unserialize($rs_random->player_values);

		$random = $player_icons['midrandom'];
		$adrotate = $player_icons['midadrotate'];

		$interval = $player_values['midinterval'];
		$begin = $player_values['midbegin'];

		if ($random == 1)
		{
			$random = 'true';
		}
		else
		{
			$random = 'false';
		}

		if ($adrotate == 1)
		{
			$adrotate = 'true';
		}
		else
		{
			$adrotate = 'false';
		}

		if ($rs_modulesettings)
		{
			$this->showadsxml($rs_modulesettings, $random, $begin, $interval, $adrotate);
		}
	}

	/**
	 * Function to show midroll ads
	 * 
	 * @param   array    $midroll_video  Midroll ads detail in array format
	 * @param   boolean  $random         random display enabled or not
	 * @param   int      $begin          Mid roll ads starting time
	 * @param   int      $interval       Mid roll ad interval period to display the next ad
	 * @param   boolean  $adrotate       Rotation of displaying mid roll ad enabled or not
	 * 
	 * @return  string
	 */
	public function showadsxml($midroll_video, $random, $begin, $interval, $adrotate)
	{
		ob_clean();
		header("content-type: text/xml");
		echo '<?xml version="1.0" encoding="utf-8"?>';
		echo '<midrollad begin="' . $begin . '" adinterval="' . $interval . '"
			random="' . $random . '" adrotate="' . $adrotate . '">';

		if (count($midroll_video) > 0)
		{
			foreach ($midroll_video as $rows)
			{
			$defined_clickpath = JURI::base()
				. '?option=com_contushdvideoshare&view=impressionclicks&click=click&id=' . $rows->id;
			$defined_impressionpath = JURI::base()
				. '?option=com_contushdvideoshare&view=impressionclicks&click=impression&id=' . $rows->id;

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

				$targeturl = $rows->targeturl;

				if (!empty($rows->targeturl) && !preg_match("~^(?:f|ht)tps?://~i", $rows->targeturl))
				{
					$targeturl = "http://" . $rows->targeturl;
				}

				echo '<midroll targeturl="' . $targeturl . '" clickurl="' . $clickpath . '" impressionurl="' . $impressionpath . '" >';
				echo '<![CDATA[';
				echo '<span class="heading">' . $rows->adsname;
				echo '</span><br><span class="midroll">' . $rows->adsdesc;
				echo '</span><br><span class="webaddress">' . $targeturl;
				echo '</span>]]>';
				echo '</midroll>';
			}
		}

		echo '</midrollad>';
		exit();
	}
}
