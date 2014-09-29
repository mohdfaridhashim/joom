<?php
/**
 * Video Upload model file for front end users
 *
 * This file is to store user videos in to database
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

// Import filesystem libraries.
jimport('joomla.filesystem.file');

/**
 * Videoupload model class.
 *
 * @package     Joomla.Contus_HD_Video_Share
 * @subpackage  Com_Contushdvideoshare
 * @since       1.5
 */
class Modelcontushdvideosharevideoupload extends ContushdvideoshareModel
{
	/**
	 * Function to display the category in the upload page
	 * 
	 * @return  array
	 */
	public function getupload()
	{
		$user = JFactory::getUser();
		$member_id = $user->get('id');
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		// Variable Declaration
		$value = $updateform = $streamerpath = $streameroption = $streamname = $url
						= $flv = $hd = $hq = $ftype = $success = $editvideo1 = '';
		$s3status = 0;
		$flagVideo = 1;
		$img = JURI::base() . 'components/com_contushdvideoshare/images/default_thumb.jpg';
		$previewurl = JURI::base() . 'components/com_contushdvideoshare/images/default_preview.jpg';

		$query->select('dispenable')
				->from('#__hdflv_site_settings')
				->where($db->quoteName('id') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$setting_res = $db->loadResult();
		$dispenable = unserialize($setting_res);

		if (!version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$task_edit = JRequest::getVar('type', '', 'get', 'string');
		}
		else
		{
			$task_edit = JRequest::getVar('type');
		}

		if ($task_edit == 'edit')
		{
			$video = JRequest::getVar('video');
			$id = JRequest::getInt('id');

			if (isset($video) && $video != '')
			{
				if ($flagVideo != 1)
				{
					// Joomla router replaced to : from - in query string
					$videoTitle = JRequest::getString('video');
					$videoid = str_replace(':', '-', $videoTitle);
				}
				else
				{
					$videoid = JRequest::getInt('video');
				}
			}
			elseif (isset($id) && $id != '')
			{
				$videoid = JRequest::getInt('id');
			}

			if ($flagVideo != 1)
			{
				$query->clear()
						->select(
								array(
									'a.id', 'a.memberid', 'a.published', 'a.title', 'a.seotitle', 'a.islive','a.embedcode',
									'a.featured', 'a.type', 'a.rate', 'a.ratecount', 'a.times_viewed', 'a.videos', 'a.filepath',
									'a.videourl', 'a.thumburl', 'a.previewurl', 'a.hdurl', 'a.home', 'a.playlistid', 'a.duration',
									'a.ordering', 'a.streamerpath', 'a.streameroption', 'a.postrollads', 'a.prerollads',
									'a.midrollads', 'a.description', 'a.targeturl', 'a.download', 'a.prerollid', 'a.postrollid',
									'a.created_date', 'a.addedon', 'a.usergroupid', 'a.tags', 'a.useraccess', 'b.vid', 'b.catid', 'c.id',
									'c.member_id', 'c.category', 'c.seo_category', 'c.parent_id', 'c.lft', 'c.rgt',
									'c.published'
								)
						)
						->from('#__hdflv_upload AS a')
						->leftJoin('#__hdflv_video_category AS b ON a.id=b.vid')
						->leftJoin('#__hdflv_category AS c ON c.id=b.catid')
						->where($db->quoteName('a.seotitle') . ' = ' . $db->quote($videoid));
			}
			else
			{
				$query->clear()
						->select(
								array(
									'a.id', 'a.memberid', 'a.published', 'a.title', 'a.seotitle', 'a.islive','a.embedcode',
									'a.featured', 'a.type', 'a.rate', 'a.ratecount', 'a.times_viewed', 'a.videos', 'a.filepath',
									'a.videourl', 'a.thumburl', 'a.previewurl', 'a.hdurl', 'a.home', 'a.playlistid', 'a.duration',
									'a.ordering', 'a.streamerpath', 'a.streameroption', 'a.postrollads', 'a.prerollads',
									'a.midrollads', 'a.description', 'a.targeturl', 'a.download', 'a.prerollid', 'a.postrollid',
									'a.created_date', 'a.addedon', 'a.usergroupid', 'a.tags', 'a.useraccess', 'b.vid', 'b.catid', 'c.id',
									'c.member_id', 'c.category', 'c.seo_category', 'c.parent_id', 'c.lft', 'c.rgt',
									'c.published'
								)
						)
						->from('#__hdflv_upload AS a')
						->leftJoin('#__hdflv_video_category AS b ON a.id=b.vid')
						->leftJoin('#__hdflv_category AS c ON c.id=b.catid')
						->where($db->quoteName('a.id') . ' = ' . $db->quote($videoid));
			}

			$db->setQuery($query);
			$editvideo1 = $db->loadObjectList();
		}

		$videourl = JRequest::getVar('videourl', '', 'post', 'string');
		$normalvideoformval = strrev(JRequest::getVar('normalvideoformval', '', 'post', 'string'));
		$normalvideoforms3status = JRequest::getVar('normalvideoforms3status', '', 'post', 'string');
		$hdvideoforms3status = JRequest::getVar('hdvideoforms3status', '', 'post', 'string');
		$thumbimageforms3status = JRequest::getVar('thumbimageforms3status', '', 'post', 'string');
		$previewimageforms3status = JRequest::getVar('previewimageforms3status', '', 'post', 'string');
		$seltype = JRequest::getVar('seltype');

		if ($ftype == '')
		{
			if (JRequest::getVar('video_filetype', '', 'post', 'string'))
			{
				$ftype = JRequest::getVar('video_filetype', '', 'post', 'string');
			}
		}

		if ($normalvideoforms3status == 1
			|| $hdvideoforms3status == 1
			|| $thumbimageforms3status == 1
			|| $previewimageforms3status == 1)
		{
			$s3status = 1;
		}

		if (JRequest::getVar('videotype') == 'edit')
		{
			if ($seltype == 0 || $seltype == 2 || $seltype == 3 || $seltype == 4)
			{
				$normalvideoformval = '';
			}
		}

		$query->clear()
				->select(
						array(
							'id', 'member_id', 'category', 'seo_category', 'parent_id', 'ordering', 'lft', 'rgt',
							'published'
						)
				)
				->from('#__hdflv_category')
				->where($db->quoteName('published') . ' = ' . $db->quote('1'))
				->where(
						'('
						. $db->quoteName('member_id') . ' = ' . $db->quote('0')
						. ' OR ' . $db->quoteName('member_id') . ' = ' . $db->quote($member_id)
						. ')'
				)
				->order($db->escape('category' . ' ' . 'ASC'));
		$db->setQuery($query);
		$category1 = $db->loadObjectList();

		if ($category1 === null)
		{
			JError::raiseError(500, 'Category was empty');
		}

		$img = 'components/com_contushdvideoshare/images/default_thumb.jpg';

		if (version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$input = JFactory::getApplication()->input;
			$uploadbutton = $input->get('uploadbtn');
		}
		else
		{
			$uploadbutton = JRequest::getCmd('uploadbtn');
		}

		if ($uploadbutton)
		{
			if ($user->get('id'))
			{
				// Setting the loginid into session
				$memberid = $user->get('id');
			}

			if ($videourl == '1' || $normalvideoformval)
			{
				// Checking for normal file type of videos
				if (strlen($normalvideoformval) > 0)
				{
					// Getting the normal video name
					$normalflv = substr($normalvideoformval, 16, strlen($normalvideoformval));
					$flv = substr($normalflv, strrpos($normalflv, '/') + 1, strlen($normalflv));
					$ftype = 'File';
					$url = $flv;
				}
				elseif (strlen(JRequest::getVar('ffmpeg', '', 'post', 'string')) > 0)
				{
					$VPATH1 = JPATH_COMPONENT . '/videos/';
					$EZFFMPEG_BIN_PATH = '/usr/bin/ffmpeg';

					// Getting the normal video name
					$path = substr(
							JRequest::getVar('ffmpeg', '', 'post', 'string'), 16,
							strlen(JRequest::getVar('ffmpeg', '', 'post', 'string'))
							);
					$filename = explode('.', $path);
					$vpath = $VPATH1;
					$destFile = $vpath . $path;
					$target_path2 = $VPATH1 . $filename[0] . '.flv';

					if ($filename[1] != 'flv')
					{
						exec($EZFFMPEG_BIN_PATH . ' ' . '-i' . ' ' . $destFile . ' ' . '-sameq' . ' ' . $target_path2 . '  2>&1');
					}

					$videofile = $destFile;
					ob_start();
					passthru('/usr/bin/ffmpeg -i "' . $videofile . '" 2>&1');
					ob_end_clean();
					$url = $filename[0] . '.flv';
					$flv = $filename[0] . '.flv';

					// Getting Hd path
					$hd = ' ';

					// Getting Hq path
					$hq = ' ';

					// Getting thumb path
					$img = $filename[0] . '.jpg';
					$ftype = 'FFmpeg';
				}

				// Getting the hd video name
				$hdvideo = substr(
						strrev(JRequest::getVar('hdvideoformval', '', 'post', 'string')), 16,
						strlen(strrev(JRequest::getVar('hdvideoformval', '', 'post', 'string')))
						);
				$hd = substr($hdvideo, strrpos($hdvideo, '/') + 1, strlen($hdvideo));

				// Getting the thumb image name
				$thumimg = substr(
						strrev(JRequest::getVar('thumbimageformval', '', 'post', 'string')), 16,
						strlen(strrev(JRequest::getVar('thumbimageformval', '', 'post', 'string')))
						);
				$img = substr($thumimg, strrpos($thumimg, '/') + 1, strlen($thumimg));

				// Getting the preview image name
				$previewimg = substr(
						strrev(JRequest::getVar('previewimageformval', '', 'post', 'string')), 16,
						strlen(strrev(JRequest::getVar('previewimageformval', '', 'post', 'string')))
						);
				$previewurl = substr($previewimg, strrpos($previewimg, '/') + 1, strlen($previewimg));
			}
			else
			{
				// Checking condition for urls
				$flv = JRequest::getVar('Youtubeurl', '', 'post', 'string');
				$embed_code = JRequest::getVar('embed_code', '', 'post', 'string', JREQUEST_ALLOWRAW);
				$url = $flv;

				if(!empty($embed_code) && $ftype == 'Embed')
				{
					$flv = '';
				}
				
				if(!empty($flv))
				{
					$ftype = 'Url';
				}

				$streamerpath = $streamname = $updatestreamer = '';
				$streamname = JRequest::getVar('streamname', '', 'post', 'string');
				$isLive = JRequest::getVar('islive-value', '', 'post', 'string');

				if (!empty($streamname) && $seltype == 3)
				{
					$streameroption = 'rtmp';
					$streamerpath = $streamname;
					$updatestreamer .= $db->quoteName('streamerpath') . ' = ' . $db->quote($streamname);
					$updatestreamer .= ', ' . $db->quoteName('streameroption') . ' = ' . $db->quote($streameroption);
					$updatestreamer .= ', ' . $db->quoteName('islive') . ' = ' . $db->quote($isLive);
				}

				// Getting Hd path
				$hd = JRequest::getVar('hdurl', '', 'post', 'string');

				// Getting Hq path
				$hq = JRequest::getVar('hq', '', 'post', 'string');

				// Getting Image path
				$img = JRequest::getVar('thumburl', '', 'post', 'string');
				$uploadFile = JRequest::getVar('thumburl', null, 'files', 'array');

				if ($uploadFile['name'] != '')
				{
					if($ftype == 'Embed')
					{
						$img = $uploadFile['name'];
						$previewurl = $uploadFile['name'];
					}
					else
					{
						$img = JURI::base() . 'components/com_contushdvideoshare/videos/' . $uploadFile['name'];
						$previewurl = JURI::base() . 'components/com_contushdvideoshare/videos/' . $uploadFile['name'];
					}

					if ($uploadFile['type'] == 'image/gif'
						|| ($uploadFile['type'] == 'image/jpeg')
						|| ($uploadFile['type'] == 'image/png'))
					{
						move_uploaded_file(
								$_FILES['thumburl']['tmp_name'],
								'components/com_contushdvideoshare/videos/' . $_FILES['thumburl']['name']
								);
					}
				}

				if ($img == '')
				{
					if (strpos($url, 'youtube') > 0)
					{
						$ftype = 'Youtube';
						$imgstr = explode('v=', $url);
						$imgval = explode('&', $imgstr[1]);
						$previewurl = 'http://img.youtube.com/vi/' . $imgval[0] . '/maxresdefault.jpg';
						$img = 'http://img.youtube.com/vi/' . $imgval[0] . '/mqdefault.jpg';
					}
					elseif (strpos($url, 'youtu.be') > 0)
					{
						$imgstr = explode('/', $url);
						$previewurl = 'http://img.youtube.com/vi/' . $imgstr[3] . '/maxresdefault.jpg';
						$img = 'http://img.youtube.com/vi/' . $imgstr[3] . '/mqdefault.jpg';
						$url = 'http://www.youtube.com/watch?v=' . $imgstr[3];
						$ftype = 'Youtube';
					}
					elseif (strpos($url, 'vimeo') > 0)
					{
						$ftype = 'Youtube';
						$split = explode('/', $url);

						if (ini_get('allow_url_fopen'))
						{
							$doc = new DOMDocument;
							$doc->load('http://vimeo.com/api/v2/video/' . $split[3] . '.xml');
							$videotags = $doc->getElementsByTagName('video');

							foreach ($videotags as $videotag)
							{
								$imgnode = $videotag->getElementsByTagName('thumbnail_medium');
								$img = $imgnode->item(0)->nodeValue;
								$vidtags = $videotag->getElementsByTagName('tags');
								$tags = $vidtags->item(0)->nodeValue;
							}
						}
						else
						{
							$url = 'http://vimeo.com/api/v2/video/' . $split[3] . '.xml';
							$curl = curl_init($url);
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
							$result = curl_exec($curl);
							curl_close($curl);
							$xml = simplexml_load_string($result);
							$img = $xml->video->thumbnail_medium;
							$tags = $xml->video->tags;
						}
					}
					elseif (strpos($url, 'dailymotion') > 0)
					{
						// Check video url is dailymotion
						$split = explode('/', $url);
						$ftype = 'Youtube';
						$split_id = explode('_', $split[4]);
						$img = $previewurl = 'http://www.dailymotion.com/thumbnail/video/' . $split_id[0];
					}
					elseif (strpos($url, 'viddler') > 0)
					{
						// Check video url is viddler
						$imgstr = explode('/', $url);
						$ftype = 'Youtube';
						$img = $previewurl = 'http://cdn-thumbs.viddler.com/thumbnail_2_' . $imgstr[4] . '_v1.jpg';
					}
					else
					{
						$img = $this->imgURL($url);
						$url1 = $this->catchURL($url);
						$url = $url1[0];
					}
				}
			}

			$gettitle = JRequest::getVar('title', '', 'post', 'string');
			$title = $db->quote($gettitle);
			$seoTitle = JRequest::getVar('seotitle', '', 'post', 'string');
			$getdescription = JRequest::getVar('description', '', 'post', 'string');
			$description = $getdescription;
			$gettagname = JRequest::getVar('tagname', '', 'post', 'string');
			$videoTags = JRequest::getVar('tags1', '', 'post', 'string');

			if(!empty($videoTags))
			{
				$tags = $videoTags;
			}

			$type = JRequest::getVar('type', '', 'post', 'string');
			$ordering = JRequest::getVar('ordering', '', 'post', 'string');

			if ($type == 1)
			{
				$useraccess = 2;
			}
			else
			{
				$useraccess = 0;
			}

			$download = JRequest::getVar('download', '', 'post');
			$db = JFactory::getDBO();
			$tagname1 = $gettagname;
			$split_tagname = explode(',', $tagname1);
			$tagname = implode(',', $split_tagname);

			$query->clear()
					->select('id')
					->from('#__hdflv_category')
					->where($db->quoteName('category') . ' IN (' . $db->quote($tagname) . ')');
			$db->setQuery($query);
			$result = $db->LoadObjectList();

			foreach ($result as $category)
			{
				$cid = $category->id;
			}

			$cdate = date('Y-m-d h:m:s');
			$value = '';
			$updateform = '';

			if (!version_compare(JVERSION, '3.0.0', 'ge'))
			{
				$videotype = JRequest::getVar('videotype', '', 'post', 'string');
			}
			else
			{
				$videotype = JRequest::getVar('videotype');
			}

			// Code for seo option
			if (trim($seoTitle) == '')
			{
				$seoTitle = $title;
			}
			
			$seoTitle = JApplication::stringURLSafe($seoTitle);
			
			if (trim(str_replace('-', '', $seoTitle)) == '')
			{
				$seoTitle = JFactory::getDate()->format('Y-m-d-H-i-s');
			}
			
			$table = $this->getTable('adminvideos');
			
			while ($table->load(array('seotitle' => $seoTitle)) && $videotype != 'edit')
			{
				$seoTitle = JString::increment($seoTitle, 'dash');
			}

			if ($videotype != 'edit')
			{
				$query->clear()
				->select('count(ordering)')
				->from('#__hdflv_upload');
				$db->setQuery($query);
				$ordering = $db->loadResult();
			}

			if ($videotype == 'edit')
			{
				$edit_video_id = JRequest::getVar('videoid', '', 'post', 'int');

				if ($previewurl != '')
				{
					$updateform .= $db->quoteName('previewurl') . ' = ' . $db->quote($previewurl);
				}
				else
				{
					$updateform .= $db->quoteName('previewurl') . ' = ""';
				}

				if ($hd != '')
				{
					$updateform .= ', ' . $db->quoteName('hdurl') . ' = ' . $db->quote($hd);
				}
				else
				{
					$updateform .= ', ' . $db->quoteName('hdurl') . ' = ""';
				}

				if ($url != '')
				{
					$updateform .= ', ' . $db->quoteName('videourl') . ' = ' . $db->quote($url);
				}
				else
				{
					$updateform .= ', ' . $db->quoteName('videourl') . ' = ""';
				}

				if ($img != '')
				{
					$updateform .= ', ' . $db->quoteName('thumburl') . ' = ' . $db->quote($img);
				}
				else
				{
					$updateform .= ', ' . $db->quoteName('thumburl') . ' = ""';
				}

				if ($seltype == 0 || $seltype == 2 || $seltype == 1 || $seltype == 4)
				{
					$updatestreamer .= $db->quoteName('streamerpath') . ' = ""';
					$updatestreamer .= ', ' . $db->quoteName('streameroption') . ' = ""';
				}

				$fields = array(
					$db->quoteName('filepath') . ' = ' . $db->quote($ftype),
					$db->quoteName('amazons3') . ' = ' . $db->quote($s3status),
					$db->quoteName('tags') . ' = ' . $db->quote($tags),
					$db->quoteName('title') . ' = ' . $title,
					$db->quoteName('seotitle') . ' = ' . $db->quote($seoTitle),
					$db->quoteName('embedcode') . ' = ' . $db->quote($embed_code),
					$db->quoteName('ordering') . ' = ' . $db->quote($ordering),
					$db->quoteName('useraccess') . ' = ' . $db->quote($useraccess),
					$db->quoteName('type') . ' = ' . $db->quote($type),
					$db->quoteName('download') . ' = ' . $db->quote($download),
					$db->quoteName('description') . ' = ' . $db->quote($description),
					$updateform,
					$updatestreamer
				);
				$query->clear()
						->update($db->quoteName('#__hdflv_upload'))
						->set($fields)
						->where($db->quoteName('id') . ' = ' . $db->quote($edit_video_id));

				$db->setQuery($query);
				$db->query();

				$query->clear()
						->delete($db->quoteName('#__hdflv_video_category'))
						->where($db->quoteName('vid') . ' = ' . $db->quote($edit_video_id));
				$db->setQuery($query);
				$db->query();
				$value = $edit_video_id;
			}
			else
			{
				if ($previewurl == '')
				{
					$previewurl = $img;
				}

				$user = JFactory::getUser();
				$userid = $user->get('id');

				if (version_compare(JVERSION, '1.6.0', 'ge'))
				{
					$query = $db->getQuery(true);
					$query->clear()
							->select('g.id AS group_id')
							->from('#__usergroups AS g')
							->leftJoin('#__user_usergroup_map AS map ON map.group_id = g.id')
							->where($db->quoteName('map.user_id') . ' = ' . (int) $userid);
					$db->setQuery($query);
					$ugp = $db->loadObject();
				}
				else
				{
					$query->clear()
							->select('gid AS group_id')
							->from('#__users')
							->where($db->quoteName('id') . ' = ' . (int) $userid);
					$db->setQuery($query);
					$ugp = $db->loadObject();
				}

				$usergroup = $ugp->group_id;

				if (isset($dispenable['adminapprove']) && $dispenable['adminapprove'] == 0)
				{
					$adminapprove = 0;
					$success = JText::_('HDVS_VIDEO_UPLOADED_APPROVE_MESSAGE');
				}
				else
				{
					$adminapprove = 1;
					$success = JText::_('HDVS_VIDEO_UPLOADED_SUCCESS');
				}

				$columns = array(
					'islive', 'streamerpath', 'amazons3', 'streameroption', 'title', 'seotitle', 'filepath',
					'videourl', 'thumburl', 'previewurl', 'published',
					'type', 'memberid', 'description', 'created_date', 'addedon', 'usergroupid',
					'playlistid', 'hdurl', 'tags', 'download', 'useraccess','ordering', 'embedcode'
				);
				$values = array(
					$db->quote($isLive), $db->quote($streamerpath), $db->quote($s3status), $db->quote($streameroption), $title,
					$db->quote($seoTitle), $db->quote($ftype), $db->quote($url), $db->quote($img), $db->quote($previewurl),
					$db->quote($adminapprove), $db->quote($type), $db->quote($memberid), $db->quote($description), $db->quote($cdate),
					$db->quote($cdate), $db->quote($usergroup), $db->quote($cid), $db->quote($hd), $db->quote($tags),
					$db->quote($download), $db->quote($useraccess),$db->quote($ordering),$db->quote($embed_code)
				);
				$query->clear()
						->insert($db->quoteName('#__hdflv_upload'))
						->columns($db->quoteName($columns))
						->values(implode(',', $values));

				$db->setQuery($query);
				$db->query();
				$db_insert_id = $db->insertid();
				$value = $db_insert_id;

				// Alert admin regarding new video upload
				$mailer = JFactory::getMailer();
				$config = JFactory::getConfig();

				$query->clear()
						->select(array('d.email', 'd.username'))
						->from('#__users AS d')
						->where($db->quoteName('d.id') . ' = ' . $db->quote($memberid));

				// Query is to display recent videos in home page
				$db->setQuery($query);
				$user_details = $db->loadObject();
				$sender = $config->get('mailfrom');
				$mailer->setSender($user_details->email);
				$featureVideoVal = 'id=' . $value;
				$mailer->addRecipient($sender);
				$subject = 'New video added by ' . $user_details->username . ' on your site.';
				$baseurl = JURI::base();
				$video_url = $baseurl . 'index.php?option=com_contushdvideoshare&view=player&' . $featureVideoVal . '&adminview=true';
				$get_html_message = file_get_contents($baseurl . '/components/com_contushdvideoshare/emailtemplate/membervideoupload.html');
				$update_baseurl = str_replace('{baseurl}', $baseurl, $get_html_message);
				$update_username = str_replace('{username}', $user_details->username, $update_baseurl);
				$message = str_replace('{video_url}', $video_url, $update_username);
				$mailer->isHTML(true);
				$mailer->setSubject($subject);
				$mailer->Encoding = 'base64';
				$mailer->setBody($message);
				$mailer->Send();
			}

			$cid = $category->id;
			$category_columns = array('vid', 'catid');
			$category_values = array($db->quote($value), $db->quote($cid));
			$query->clear()
					->insert($db->quoteName('#__hdflv_video_category'))
					->columns($db->quoteName($category_columns))
					->values(implode(',', $category_values));
			$db->setQuery($query);
			$db->query();

			if (count($result) > 0)
			{
				if ($videotype == 'edit')
				{
					$query->clear()
							->update($db->quoteName('#__hdflv_upload'))
							->set($db->quoteName('playlistid') . ' = ' . $db->quote($cid))
							->where($db->quoteName('id') . ' = ' . $db->quote($edit_video_id));

					$db->setQuery($query);
					$db->query();
					$success = JText::_('HDVS_UPDATED_SUCCESS');
				}
			}

			$url = JRoute::_($baseurl . 'index.php?option=com_contushdvideoshare&view=myvideos');
			JFactory::getApplication()->redirect($url, $success, 'message');
		}

		return array($category1, $success, $editvideo1);
	}

	/**
	 * Function to get ffmpeg video information
	 * 
	 * @param   string  $src_filepath  video file path
	 * 
	 * @return  array
	 */
	public function ezffmpeg_vdofile_infos($src_filepath)
	{
		$FLVTOOL_BIN_PATH = '/usr/bin/ffmpeg';
		$commandline = $FLVTOOL_BIN_PATH . ' -i ' . $src_filepath;
		$exec_return = $this->ezffmpeg_exec($commandline);
		$exec_return_content = explode('\n', $exec_return);
		$error_line_id = $this->ezffmpeg_array_search('error', $exec_return_content);

		if ($error_line_id)
		{
			$error_line = trim($exec_return_content[$error_line_id]);
			$return_array['status'] = -1;
			$return_array['error_msg'] = $error_line;
		}
		else
		{
			$return_array['status'] = 1;
			$infos_line_id = $this->ezffmpeg_array_search('Duration:', $exec_return_content);

			if ($infos_line_id)
			{
				$infos_line = trim($exec_return_content[$infos_line_id]);
				$infos_cleaning = explode(': ', $infos_line);

				$infos_datas = explode(',', $infos_cleaning[1]);
				$return_array['vdo_duration_format'] = trim($infos_datas[0]);
				$return_array['vdo_duration_seconds'] = $this->ezffmpeg_common_time_to_seconds($return_array['vdo_duration_format']);

				$return_array['vdo_bitrate'] = trim($infos_cleaning[3]);
			}

			$infos_line_video_id = $this->ezffmpeg_array_search('Video:', $exec_return_content);

			if ($infos_line_video_id)
			{
				$infos_line = trim($exec_return_content[$infos_line_video_id]);
				$infos_cleaning = explode(': ', $infos_line);
			}

			$infos_line_audio_id = $this->ezffmpeg_array_search('Audio:', $exec_return_content);

			if ($infos_line_audio_id)
			{
				$infos_line = trim($exec_return_content[$infos_line_audio_id]);
				$infos_cleaning = explode(': ', $infos_line);
				$infos_datas = explode(',', $infos_cleaning[2]);
			}
		}

		return $return_array;
	}

	/**
	 * Function to get seconds of video
	 * 
	 * @param   string  $timestamp  duration of video
	 * 
	 * @return mixed
	 */
	public function ezffmpeg_common_time_to_seconds($timestamp)
	{
		$timestamp_datas = explode(':', $timestamp);
		$nb_seconds = $timestamp_datas[2];
		$nb_minutes = $timestamp_datas[1];
		$nb_hours = $timestamp_datas[0];
		$return_val = ($nb_hours * 3600) + ($nb_minutes * 60) + $nb_seconds;

		return($return_val);
	}

	/**
	 * Function to execute commands
	 * 
	 * @param   string  $commandline  FFMPEG command
	 * 
	 * @return  mixed
	 */
	public function ezffmpeg_exec($commandline)
	{
		$read = '';
		$handle = popen($commandline . ' 2>&1', 'r');

		while (!feof($handle))
		{
			$read .= fread($handle, 2096);
		}

		pclose($handle);

		return($read);
	}

	/**
	 * Function to search ffmpeg value
	 * 
	 * @param   var    $needle       needle
	 * @param   array  $array_lines  array value
	 * 
	 * @return  bool
	 */
	public function ezffmpeg_array_search($needle, $array_lines)
	{
		$return_val = false;
		reset($array_lines);

		foreach ($array_lines as $num_line => $line_content)
		{
			if (strpos($line_content, $needle) !== false)
			{
				return $num_line;
			}
		}

		return $return_val;
	}

	/**
	 * Function to get image from url (youtube,metacafe,etc.)
	 * 
	 * @param   string  $location  video url
	 * 
	 * @return  string
	 */
	public function getVideoType($location)
	{
		if (preg_match('/http:\/\/www\.youtube\.com\/watch\?v=[^&]+/', $location, $vresult))
		{
			$type = 'youtube';
		}
		elseif (preg_match('/http:\/\/(.*?)blip\.tv\/file\/[0-9]+/', $location, $vresult))
		{
			$type = 'bliptv';
		}
		elseif (preg_match('/http:\/\/(.*?)break\.com\/(.*?)\/(.*?)\.html/', $location, $vresult))
		{
			$type = 'break';
		}
		elseif (preg_match('/http:\/\/www\.metacafe\.com\/watch\/(.*?)\/(.*?)\//', $location, $vresult))
		{
			$type = 'metacafe';
		}
		elseif (preg_match('/http:\/\/video\.google\.com\/videoplay\?docid=[^&]+/', $location, $vresult))
		{
			$type = 'google';
		}
		elseif (preg_match('/http:\/\/www\.dailymotion\.com\/video\/+/', $location, $vresult))
		{
			$type = 'dailymotion';
			$vresult[0] = $location;
		}

		return $type;
	}

	/**
	 * Function to get image from url (youtube,metacafe,etc.)
	 * 
	 * @param   string  $url  video url
	 * 
	 * @return  string
	 */
	public function imgURL($url)
	{
		$type = $this->getVideoType($url);

		switch ($type)
		{
			case 'youtube':
				$location_img_url = str_replace('http://www.youtube.com/watch?v=', '', $this->url);
				$img = 'http://img.youtube.com/vi/' . $location_img_url . '/1.jpg';

				break;

			case 'bliptv':
				$contents = trim($this->file_get_contents_curl($url));
				preg_match('/rel=\"image_src\" href=\"http:\/\/[^\"]+/', $contents, $result_img);
				preg_match('/http:\/\/[^\"]+/', $result_img[0], $result_img);
				$img = $result_img[0];

				break;

			case 'break':
				$contents = trim($this->file_get_contents_curl($url));
				preg_match('/meta name=\"embed_video_thumb_url\" content=\"http:\/\/[^\"]+/', $contents, $result_img);
				preg_match('/http:\/\/[^\"]+/', $result_img[0], $result_img);
				$img = $result_img[0];

				break;

			case 'dailymotion':
				$contents = trim($this->file_get_contents_curl($url));
				$img = str_replace('www.dailymotion.com', 'www.dailymotion.com/thumbnail', $this->url);

				break;

			default:
				$img = JURI::base() . 'components/com_contushdvideoshare/images/default_thumb.jpg';
		}

		return $img;
	}

	/**
	 * Function to get conents from url
	 * 
	 * @return array
	 */
	public function get_site_settings()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);
		$query->select('dispenable')
				->from('#__hdflv_site_settings')
				->where($db->quoteName('id') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$setting_res = $db->loadResult();

		return $dispenable = unserialize($setting_res);
	}

	/**
	 * Function to get conents from url
	 *
	 * @return array
	 */
	public function getLicenseKey()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);
		$query->clear()
				->select('player_values')
				->from('#__hdflv_player_settings');
		$db->setQuery($query);
		$settingResult = $db->loadResult();
	
		$playerValues = unserialize($settingResult);
		return $playerValues['licensekey'];
	}

	/**
	 * Function to get conents from url using curl
	 * 
	 * @param   string  $url  video url
	 * 
	 * @return  array
	 */
	public function file_get_contents_curl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}

	/**
	 * Function to get type and details from url
	 * 
	 * @param   string  $url  video url
	 * 
	 * @return  string
	 */
	public function catchURL($url)
	{
		$type = $this->getVideoType($url);
		$vid_location = array();
		$vid_location[0] = $url;

		switch ($type)
		{
			case 'bliptv':
				$newInfo = trim($this->file_get_contents_curl($url));
				preg_match('/http:\/\/(.*?)blip\.tv\/file\/get\/(.*?)\.flv/', $newInfo, $result);
				$vid_location[0] = urldecode($result[0]);

				break;

			case 'break':
				$newInfo = trim($this->file_get_contents_curl($url));
				preg_match('/sGlobalFileName=\'[^\']+/', $newInfo, $resulta);
				$resulta = str_replace('sGlobalFileName=\'', '', $resulta[0]);
				preg_match('/sGlobalContentFilePath=\'[^\']+/', $newInfo, $resultb);
				$resultb = str_replace('sGlobalContentFilePath=\'', '', $resultb[0]);
				$vid_location[0] = 'http://media1.break.com/dnet/media/' . $resultb . '/' . $resulta . '.flv';

				break;

			case 'metacafe':
				$newInfo = trim($this->file_get_contents_curl($url));
				preg_match('/mediaURL=http%3A%2F%2F(.*?)%2FItemFiles%2F%255BFrom%2520www.metacafe.com%255D%25(.*?)\.flv+/', $newInfo, $result);
				preg_match('/http%3A%2F%2F(.*?)%2FItemFiles%2F%255BFrom%2520www.metacafe.com%255D%25(.*?)\.flv+/', $result[0], $result);
				$vid_location[0] = urldecode(str_replace('&gdaKey', '?__gda__', $result[0]));

				break;

			case 'google':
				$newInfo = trim($this->file_get_contents_curl($url));
				preg_match('/http:\/\/(.*?)googlevideo.com\/videoplayback%3F[^\\\\]+/', $newInfo, $result);
				$vid_location[0] = urldecode($result[0]);

				break;
			case 'dailymotion':
				$newInfo = trim($this->file_get_contents_curl($url));
				preg_match('/"video", "(.*?)"/', $newInfo, $result);
				$flv = preg_split('/@@(.*?)\|\|/', urldecode($result[1]));
				$vid_location[0] = $flv[0];

				break;
		}

		return $vid_location;
	}
}
