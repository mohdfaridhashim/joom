<?php
/**
 * Player view file
 *
 * This file is to display the player and video thumb images on video home and detail page. 
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

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * Player view class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class ContushdvideoshareViewplayer extends ContushdvideoshareView
{
	/**
	 * Function to set layout and model for view page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return  ContushdvideoshareViewplayer		This object to support chaining.
	 * 
	 * @since   1.5
	 */
	public function  display($cachable = false, $urlparams = false)
	{
		$videoid = $categoryid = $videourl = '';
		$videodetails = array();
		$model = $this->getModel();

		// CODE FOR SEO OPTION OR NOT - START
		$video = JRequest::getVar('video');
		$categoryname = JRequest::getVar('category');
		$id = JRequest::getInt('id');
		$flagVideo = is_numeric($video);
		$flagcat      = is_numeric($categoryname);

		if (isset($video) && $video != "")
		{
			if ($flagVideo != 1 || $flagcat != 1)
			{
				// Joomla router replaced to : from - in query string
				$videoTitle = JRequest::getString('video');
				$video = str_replace(':', '-', $videoTitle);
				$category_name = JRequest::getString('category');
				$category = str_replace(':', '-', $category_name);

				if (!empty($category) && !empty($video))
				{
					$videodetails = $model->getVideoCatId($video, $category);
					$videoid = $videodetails->id;
					$categoryid = $videodetails->playlistid;
				}
				else
				{
					$videodetails = $model->getVideoId($video);
					$videoid = $videodetails->id;
					$categoryid = $videodetails->playlistid;
					$videodetails->videourl = $videodetails->videourl;
				}
			}
			else
			{
				$videoid = JRequest::getInt('video');
				$videodetails = $model->getVideodetail($videoid);
				$categoryid = JRequest::getInt('category');
				$videodetails->id = $videoid;
				$videodetails->playlistid = $categoryid;
				$videodetails->videourl = $videodetails->videourl;
			}

			$this->assignRef('videodetails', $videodetails);
		}
		elseif (isset($id) && $id != '')
		{
			$videoid = JRequest::getInt('id');
			$videodetails = $model->getVideodetail($videoid);
			$categoryid = JRequest::getInt('catid');
			$videodetails->id = $videoid;
			$videodetails->playlistid = $categoryid;
			$videodetails->videourl = $videodetails->videourl;
			$this->assignRef('videodetails', $videodetails);
		}
		// CODE FOR SEO OPTION OR NOT - END

		// Code for html5 player
		$htmlVideoDetails = $model->getHTMLVideoDetails($videoid);
		$this->assignRef('htmlVideoDetails', $htmlVideoDetails);

		$getfeatured = $model->getfeatured();
		$this->assignRef('getfeatured', $getfeatured);

		$detail = $model->showhdplayer($videoid);
		$this->assignRef('detail', $detail);

		$commentsview = $model->ratting($videoid);
		$this->assignRef('commentview', $commentsview);

		// Calling the function in models comment.php
		$comments = $model->displaycomments($videoid);

		//  Assigning the reference for the results
		$this->assignRef('commenttitle', $comments[0]);

		// Function call for fetching Itemid
		$Itemid = $model->getmenuitemid_thumb();
		$this->assignRef('Itemid', $Itemid);

		//  Assigning the reference for the results
		$this->assignRef('commenttitle1', $comments[1]);

		// Calling the function in models homepagebottom.php
		$homepagebottom = $model->gethomepagebottom();

		// Assigning the reference for the results
		$this->assignRef('rs_playlist1', $homepagebottom);

		// Calling the function in models homepagebottom.php
		$homepagebottomsettings = $model->gethomepagebottomsettings();

		// Assigning the reference for the results
		$this->assignRef('homepagebottomsettings', $homepagebottomsettings);

		$homeAccessLevel = $model->getHTMLVideoAccessLevel();
		$this->assignRef('homepageaccess', $homeAccessLevel);

		$homePageFirst = $model->initialPlayer();
		$this->assignRef('homePageFirst', $homePageFirst);
		parent::display();
	}
}
