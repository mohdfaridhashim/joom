<?php
/**
 * RSS Feed model file
 *
 * This file is to fetch videos detail from database and generate RSS Feed
 *
 * @category   Apptha
 * @package    Com_Contushdvideoshare
 * @version    3.6
 * @author     Apptha Team <developers@contus.in>
 * @copyright  Copyright (C) 2014 Apptha. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import joomla model library
jimport('joomla.application.component.model');

/**
 * RSS model class. 
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Modelcontushdvideosharerss extends ContushdvideoshareModel
{
	/**
	 * Function to get play records
	 * 
	 * @return  array
	 */
	public function playgetrecords()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$type = JRequest::getvar('type');
		$orderby = '';

		switch ($type)
		{
			case 'popular' :
				$orderby = " a.times_viewed";

				break;

			case 'recent' :
				$orderby = "a.id";

				break;

			case 'featured' :
				$where = "a.featured='1'";
				$orderby = "";

				break;

			case 'category' :
				$playid = JRequest::getvar('catid');
				$where = 'a.playlistid=' . $playid;
				$orderby = '';

				break;
			default;
		}

		$query->select(array('DISTINCT a.*', 'b.seo_category', 'b.category', 'd.username'))
				->from('#__hdflv_upload AS a')
				->leftJoin('#__users AS d ON a.memberid=d.id')
				->leftJoin('#__hdflv_category AS b ON a.playlistid=b.id')
				->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('b.published') . ' = ' . $db->quote('1'));

		if ($where != '')
		{
			$query->where($where);
		}

		if ($orderby != '')
		{
			$query->order($db->escape($orderby . ' ' . 'DESC'));
		}

		$db->setQuery($query);
		$rs_video = $db->loadObjectList();
		$query->clear()
				->select('dispenable')
				->from('#__hdflv_site_settings')
				->where($db->quoteName('id') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$setting_res = $db->loadResult();
		$dispenable = unserialize($setting_res);

		$this->showxml($rs_video, $dispenable);
	}

	/**
	 * Function to show RSS
	 * 
	 * @param   array  $rs_video    Video detail array
	 * @param   array  $dispenable  settings array
	 * 
	 * @return  string
	 */
	public function showxml($rs_video, $dispenable)
	{
		ob_clean();
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("content-type: text/xml");
		echo '<?xml version="1.0" encoding="utf-8"?>';
		echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"
				xmlns:media="http://search.yahoo.com/mrss/"
		        xmlns:gd="http://schemas.google.com/g/2005" 
		        xmlns:yt="http://gdata.youtube.com/schemas/2007" 
				xmlns:dc="http://purl.org/dc/elements/1.1/"
				xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
				xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
				xmlns:content="http://purl.org/rss/1.0/modules/content/">';
		$config = JFactory::getConfig();
		$mainframe = JFactory::getApplication();

		if (version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$siteName = $mainframe->getCfg('sitename');
			$language = $mainframe->getCfg('language');
		}
		else
		{
			$siteName = $config->getValue('config.sitename');
			$language = $config->getValue('config.language');
		}

		echo '<channel><title>' . $siteName . '</title>';
		echo '<description>' . $mainframe->getCfg('MetaDesc') . '</description>';
		echo '<image>
           <url></url>
            <title/>
            <link>' . JURI::base() . '</link>
        </image>';
		echo '<link>' . JURI::base() . '</link>';
		echo '<language>' . $language . '</language>';

		$current_path = "components/com_contushdvideoshare/videos/";
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('dispenable')
		->from('#__hdflv_site_settings');
		$db->setQuery($query);
		$resultSetting = $db->loadResult();
		$dispenable = unserialize($resultSetting);

		// Get player settings
		$query->clear()
		->select('player_icons')
		->from('#__hdflv_player_settings');
		$db->setQuery($query);
		$rs_settings = $db->loadResult();
		$player_icons = unserialize($rs_settings);
		$hddefault = $player_icons['hddefault'];
		
		if (count($rs_video) > 0)
		{
			foreach ($rs_video as $rows)
			{
				$timage = "";
				
				if ($rows->filepath == "File" || $rows->filepath == "FFmpeg")
				{
					if ($hddefault == 0 && $rows->hdurl != '')
					{
						$video = '';
					}
					else
					{
						if (isset($rows->amazons3) && $rows->amazons3 == 1)
						{
							$video = $dispenable['amazons3link'] . $rows->videourl;
						}
						else
						{
							$video = JURI::base() . $current_path . $rows->videourl;
						}
					}
				
					$video = JURI::base() . $current_path . $rows->videourl;
				
					if (!empty($rows->previewurl))
					{
						$preview_image = $rows->previewurl;
					}
					else
					{
						$preview_image = 'default_preview.jpg';
					}
				
					$previewimage = JURI::base() . $current_path . $preview_image;
					$timage = JURI::base() . $current_path . $rows->thumburl;
				}
				elseif ($rows->filepath == "Url")
				{
					$video = $rows->videourl;
				
					if (!empty($rows->previewurl))
					{
						$previewimage = $rows->previewurl;
					}
					else
					{
						$previewimage = JURI::base() . $current_path . 'default_preview.jpg';
					}
				
					$timage = $rows->thumburl;
				}
				elseif ($rows->filepath == "Youtube")
				{
					$video = $rows->videourl;
					$str2 = strstr($rows->previewurl, 'components');
				
					if ($str2 != "")
					{
						$previewimage = JURI::base() . $rows->previewurl;
						$timage = JURI::base() . $rows->thumburl;
					}
					else
					{
						$previewimage = $rows->previewurl;
						$timage = $rows->thumburl;
					}
				}
				elseif ($rows->filepath == "Embed")
				{
					$video = '';
					$timage = JURI::base(). $current_path . $rows->thumburl;
				}

				$query->clear()
						->select('seo_category')
						->from('#__hdflv_category')
						->where($db->quoteName('id') . ' = ' . $db->quote($rows->playlistid));
				$db->setQuery($query);
				$categorySeo = $db->loadObjectList();

				if ($dispenable['seo_option'] == 1)
				{
					$fbCategoryVal = "category=" . $categorySeo[0]->seo_category;
					$fbVideoVal = "video=" . $rows->seotitle;
				}
				else
				{
					$fbCategoryVal = "catid=" . $rows->playlistid;
					$fbVideoVal = "id=" . $rows->id;
				}

				$baseUrl = JURI::base();
				$baseUrl1 = parse_url($baseUrl);
				$baseUrl1 = $baseUrl1['scheme'] . '://' . $baseUrl1['host'];

				$query = $db->getQuery(true);
				
				// Query is to select the featured videos module settings
				$query->clear()
				->select('id')
				->from('#__menu')
				->where($db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_contushdvideoshare&view=player') . ' AND ' . $db->quoteName('published') . ' = ' . $db->quote('1'))
				->order('id DESC');
				$db->setQuery($query);
				$Itemid = $db->loadResult();

				$fbPath = $baseUrl1 . JRoute::_('index.php?Itemid=' . $Itemid . '&amp;option=com_contushdvideoshare&view=player&' . $fbCategoryVal . '&' . $fbVideoVal);
				$date = date("D, d M Y H:i:s T", strtotime($rows->created_date));

				echo '<item>';
				echo '<title>' . $rows->title . '</title>';
				echo '<link>' . $fbPath . '</link>';
				echo '<media:group>
                        <media:category/>
                        <media:content url="' . $video . '" type="application/x-shockwave-flash" medium="video" isDefault="true" expression="full" yt:format="5"/>
                        <media:description type="plain" />
                        <media:keywords/>
                        <media:thumbnail url="' . $timage . '"/>
                    </media:group>';
				echo '<guid>' . $fbPath . '</guid>';
				echo '<description>';
				echo '<![CDATA[<p><img src ="'.$timage.'"/>'.$rows->description.'</p>]]>';
				echo '</description>';
				echo '<pubDate>' . $date . '</pubDate>';
				echo '<author>' . $rows->username . '</author>';
				echo '<category>' . $rows->category . '</category>';
				echo '</item>';
			}
		}

		echo '</channel></rss>';
		exit();
	}
}