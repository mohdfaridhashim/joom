<?php
/**
 * Category videos view file
 *
 * This file is to display Category videos
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
$ratearray 		= array("nopos1", "onepos1", "twopos1", "threepos1", "fourpos1", "fivepos1");
$user 			= JFactory::getUser();
$thumbview 		= unserialize($this->categoryrowcol[0]->thumbview);
$dispenable 	= unserialize($this->categoryrowcol[0]->dispenable);
$player_values 	= unserialize($this->player_values->player_values);
$player_icons 	= unserialize($this->player_values->player_icons);
$playerpath 	= JURI::base() . "components/com_contushdvideoshare/hdflvplayer/hdplayer.swf";
$base_url 		= str_replace(':', '%3A', JURI::base());
$url_base 		= substr_replace($base_url, "", -1);
$baseurl 		= str_replace('/', '%2F', $url_base);
$Itemid 		= $this->Itemid;
$document 		= JFactory::getDocument();
$style 			= '#video-grid-container .ulvideo_thumb .video-item{margin-right:' . $thumbview['categorywidth'] . 'px; }';
$document->addStyleDeclaration($style);

$seoOption = $dispenable['seo_option'];
$category  = JRequest::getString('category');
$catid = JRequest::getInt('catid');

if (isset($category) || isset($catid))
{
	if ($seoOption == 1)
	{
		$featuredCategoryVal = "category=" . $category;
	}
	else
	{
		$flatCatid = is_numeric($category);

		if ($flatCatid == 1)
		{
			$catid = $category;
		}

		$featuredCategoryVal = "catid=" . $catid;
	}

	$current_url = 'index.php?option=com_contushdvideoshare&view=category&' . $featuredCategoryVal;

	if (version_compare(JVERSION, '1.6.0', 'ge'))
	{
		$login_url = JURI::base() . "index.php?option=com_users&amp;view=login&return=" . base64_encode($current_url);
	}
	else
	{
		$login_url = JURI::base() . "index.php?option=com_user&amp;view=login&return=" . base64_encode($current_url);
	}
}
?>
<?php
$requestpage = JRequest::getVar('video_pageid', '', 'post', 'int');

if (USER_LOGIN == '1')
{
	if ($user->get('id') != '')
	{
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			?>
			<div class="toprightmenu">
				<a href="<?php
		echo JRoute::_("index.php?Itemid=" . $Itemid . "&amp;option=com_contushdvideoshare&view=myvideos"); ?>"><?php echo JText::_('HDVS_MY_VIDEOS'); ?></a> |
				<a href="<?php
				echo JRoute::_(
						'index.php?option=com_users&task=user.logout&'
						. JSession::getFormToken() . '=1&return=' . base64_encode(JUri::root())
						);
				?>">
							<?php echo JText::_('HDVS_LOGOUT'); ?></a>
			</div>
		<?php
		}
		else
		{
		?>
			<div class="toprightmenu">
				<a href="<?php
		echo JRoute::_("index.php?Itemid=" . $Itemid . "&amp;option=com_contushdvideoshare&view=myvideos"); ?>"><?php echo JText::_('HDVS_MY_VIDEOS'); ?></a> |
				<a href="<?php
		echo JRoute::_("index.php?option=com_user&task=logout"); ?>"><?php echo JText::_('HDVS_LOGOUT'); ?></a>
			</div>
		<?php
		}
	}
	else
	{
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			?><span class="toprightmenu">
				<a href="<?php
		echo JRoute::_("index.php?option=com_users&view=registration"); ?>"><?php echo JText::_('HDVS_REGISTER'); ?></a> |
				<a href="<?php
				echo $login_url;
				?>"> <?php
				echo JText::_('HDVS_LOGIN');
				?></a>
			</span>
		<?php
		}
		else
		{
		?>
			<span class="toprightmenu">
				<a href="<?php
		echo JRoute::_("index.php?option=com_user&view=register"); ?>"><?php echo JText::_('HDVS_REGISTER'); ?></a> |
				<a href="<?php
				echo $login_url;
				?>"> <?php
				echo JText::_('HDVS_LOGIN');
				?></a>
			</span>
			<?php
		}
	}
}
?>
<div class="player clearfix" id="clsdetail">

<?php
$totalrecords = $thumbview['categorycol'] * $thumbview['categoryrow'];

if (count($this->categoryview) - 6 < $totalrecords)
{
	$totalrecords = count($this->categoryview) - 6;
}

if ($totalrecords <= 0)
{
// If the count is 0 then this part will be executed
?>
		<?php if(isset($_GET['opt']) && $_GET['opt'] == 2) { ?>
		<p>
		<?php } ?>
		<h1 class="home-link hoverable"><?php echo $this->categoryview[0]->category; ?></h1>
		<?php
		echo '<div class="hd_norecords_found"> ' . JText::_('HDVS_NO_CATEGORY_VIDEOS_FOUND') . ' </div>';
}
	else
	{
		?>
		<div id="video-grid-container" class="clearfix">

	<?php
		if(isset($_GET['opt']) && $_GET['opt'] == 2)
		{
			$dispenable['categoryplayer'] = 1;
			echo $_GET['opt'];
			//print_r($dispenable);
		}
	if (isset($dispenable['categoryplayer']) && $dispenable['categoryplayer'] == 1)
	{
		?>

		<h1 id="viewtitle" class="floatleft" style=""><?php echo $this->categoryview['videoForPlayer'][0]->title; ?></h1>
		
		<?php
		/**
		 * Function to Detect mobile device for category videos
		 * 
		 * @return  Detect mobile device
		 */
				function Category_Detect_mobile()
				{
					$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';

					$mobile_browser = '0';

					$agent = strtolower($_SERVER['HTTP_USER_AGENT']);

					if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', $agent))
					{
						$mobile_browser++;
					}

					if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
					{
						$mobile_browser++;
					}

					if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
					{
						$mobile_browser++;
					}

					if (isset($_SERVER['HTTP_PROFILE']))
					{
						$mobile_browser++;
					}

					$mobile_ua = substr($agent, 0, 4);
					$mobile_agents = array(
						'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
						'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
						'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
						'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
						'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
						'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
						'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
						'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
						'wapr', 'webc', 'winw', 'xda', 'xda-'
					);

					if (in_array($mobile_ua, $mobile_agents))
					{
						$mobile_browser++;
					}

					if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
					{
						$mobile_browser++;
					}

					// Pre-final check to reset everything if the user is on Windows
					if (strpos($agent, 'windows') !== false)
					{
						$mobile_browser = 0;
					}

					// But WP7 is also Windows, with a slightly different characteristic
					if (strpos($agent, 'windows phone') !== false)
					{
						$mobile_browser++;
					}

					if ($mobile_browser > 0)
					{
						return true;
					}
					else
					{
						return false;
					}
				}

				$mobile = Category_Detect_mobile();

				if ($mobile !== true) {
?>
				<script type="text/javascript">
				function getvideoData(id,title,desc){
					document.getElementById('viewtitle').innerHTML = title;
				}
				</script>
				
<?php
				}

				if (($this->categoryview['videoForPlayer'][0]->filepath == 'Embed')
					|| (!empty($this->categoryview['videoForPlayer'][0])
					&& (preg_match('/vimeo/', $this->categoryview['videoForPlayer'][0]->videourl))
					&& ($this->categoryview['videoForPlayer'][0]->videourl != '')))
				{
					if ($this->homepageaccess == 'true')
					{
						if ($this->categoryview['videoForPlayer'][0]->filepath == 'Embed')
						{
							$playerembedcode = $this->categoryview['videoForPlayer'][0]->embedcode;
							$playeriframewidth = str_replace(
									'width=', 'width="' . $player_values['width'] . '"', $playerembedcode
									);
							contushdvideoshareController::videohitCount_function($this->categoryview['videoForPlayer'][0]->id);

							if ($mobile === true)
							{
								echo $playerembedcode;
							}
							else
							{
								?>
								<div id="flashplayer">
									<?php
									echo str_replace(
											'height=', 'height="' . $player_values['height'] . '"', $playeriframewidth
											); ?>
								</div>
								<?php
							}
							// For embed code videos
						}
						elseif (
								!empty($this->categoryview['videoForPlayer'][0])
								&& (preg_match('/vimeo/', $this->categoryview['videoForPlayer'][0]->videourl))
								&& ($this->categoryview['videoForPlayer'][0]->videourl != '')
								)
						{
							// For vimeo videos
							$split = explode("/", $this->categoryview['videoForPlayer'][0]->videourl);
							contushdvideoshareController::videohitCount_function($this->categoryview['videoForPlayer'][0]->id);

							if ($mobile === true)
							{
								$widthheight = '';
							}
							else
							{
								$widthheight = 'width="' . $player_values['width'] . '" height="'
										. $player_values['height'] . '"';
							}
							?>
							<div id="flashplayer">
								<iframe <?php echo $widthheight; ?> src="<?php
								echo 'http://player.vimeo.com/video/' . $split[3]
										. ''; ?>" class="iframe_frameborder" webkitallowfullscreen mozallowfullscreen allowfullscreen>
								</iframe>
							</div>
						<?php
						}
					}
					else
					{
						?>
						<style type="text/css">
							.login_msg{height:<?php echo $player_values['height']; ?>px;
									  color: #fff;width: 100%;
									  margin: <?php echo ceil($player_values['width'] / 3); ?>px 0 0;}
							.login_msg a{background: #999; color:#fff; padding: 5px;}
						</style>

						<div id="video" style="height:<?php echo $player_values['height']; ?>px;
							 background-color:#000000; position: relative;" >
							<div class="login_msg">
								<h3>Please login to watch this video</h3>
								<a href="<?php
								if (!empty($player_icons['login_page_url']))
								{
									echo $player_icons['login_page_url'];
								}
								else
								{
									echo "#";
								}
								?>"><?php echo JText::_('HDVS_LOGIN'); ?></a>
							</div>
						</div>
			<?php
					}
				}
		else
		{
			if ($mobile === true)
			{
				?>                                     
						<!-- HTML5 player starts here -->
						<div id="htmlplayer">
							<?php
							// Generate details for HTML5 player
							if ($this->homepageaccess == 'true')
							{
								ContushdvideoshareController::videohitCount_function($this->categoryview['videoForPlayer'][0]->id);

								if ($this->categoryview['videoForPlayer'][0]->filepath == "Youtube" || strpos($this->categoryview['videoForPlayer'][0]->videourl, 'youtube.com') > 0)
								{
									// For youtube videos
									if (strpos($this->categoryview['videoForPlayer'][0]->videourl, 'youtube.com') > 0)
									{
										$url = $this->categoryview['videoForPlayer'][0]->videourl;
										$query_string = array();
										parse_str(parse_url($url, PHP_URL_QUERY), $query_string);
										$id = $query_string["v"];
										$videoid = trim($id);
										$video = "http://www.youtube.com/embed/$videoid";
										?>
										<iframe width="<?php
										echo $player_values['width'];
										?>" height="<?php
										echo $player_values['height'];
										?>"
										src="<?php
										echo $video;
										?>" class="iframe_frameborder" ></iframe>
										<?php
									}
									elseif (strpos($this->categoryview['videoForPlayer'][0]->videourl, 'dailymotion') > 0)
									{
										// For dailymotion videos
										$video = $this->categoryview['videoForPlayer'][0]->videourl;
										?>
										<iframe width="<?php
										echo $player_values['width'];
										?>" height="<?php
										echo $player_values['height'];
										?>" src="<?php
										echo $video;
										?>" class="iframe_frameborder" ></iframe>
										<?php
									}
									elseif (strpos($this->categoryview['videoForPlayer'][0]->videourl, 'viddler') > 0)
									{
										// For viddler videos
										$imgstr = explode("/", $this->categoryview['videoForPlayer'][0]->videourl);
										?>
									<iframe id="viddler-<?php echo $imgstr; ?>" src="// www.viddler.com/embed/<?php
									echo $imgstr;
									?>/?f=1&autoplay=0&player=full&secret=26392356&loop=false&nologo=false&hd=false"
									width="<?php
									echo $player_values['width'];
									?>" height="<?php
									echo $player_values['height'];
									?>" frameborder="0"
									mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>
								<?php
									}
								}
								else if ($this->categoryview['videoForPlayer'][0]->filepath == "File"
									|| $this->categoryview['videoForPlayer'][0]->filepath == "FFmpeg"
									|| $this->categoryview['videoForPlayer'][0]->filepath == "Url")
								{
									$current_path = "components/com_contushdvideoshare/videos/";

									if ($this->categoryview['videoForPlayer'][0]->filepath == "Url")
									{
										// For URL Method videos
										if ($this->categoryview['videoForPlayer'][0]->streameroption == 'rtmp')
										{
											$rtmp = str_replace(
													'rtmp', 'http', $this->categoryview['videoForPlayer'][0]->streamerpath
													);
											$video = $rtmp . '_definst_/mp4:' . $this->categoryview['videoForPlayer'][0]->videourl
													. '/playlist.m3u8';

											// For RTMP videos
										}
										else
										{
											$video = $this->categoryview['videoForPlayer'][0]->videourl;
										}
									}
									else
									{
										if (isset($this->categoryview['videoForPlayer'][0]->amazons3)
											&& $this->categoryview['videoForPlayer'][0]->amazons3 == 1)
										{
											$video = $dispenable['amazons3link']
													. $this->categoryview['videoForPlayer'][0]->videourl;
										}
										else
										{
											$video = JURI::base() . $current_path
													. $this->categoryview['videoForPlayer'][0]->videourl;

											// For upload Method videos
										}
									}
									?>
									<video id="video" src="<?php
									echo $video;
									?>" width="<?php
									echo $player_values['width'];
									?>"
									height="<?php
									echo $player_values['height'];
									?>" autobuffer controls onerror="failed(event)">
									Html5 Not support This video Format.
									</video>
									<?php
								}
							}
				else
				{
				// Restricted video design part
					?>
			<style type="text/css">
				.login_msg{vertical-align: middle;height:<?php echo $player_values['height']; ?>px;
						  display: table-cell; color: #fff;}
				.login_msg a{background: #999; color:#fff; padding: 5px;}
			</style>

			<div id="video" style="height:<?php echo $player_values['height']; ?>px;
				 background-color:#000000; position: relative;" >
				<div class="login_msg">
					<h3>Please login to watch this video</h3>
					<a href="<?php
					if (!empty($player_icons['login_page_url']))
					{
						echo $player_icons['login_page_url'];
					}
					else
					{
						echo "#";
					}
					?>"><?php echo JText::_('HDVS_LOGIN'); ?></a>
				</div>
			</div>
						<?php
				}
					?>
						</div>

					<?php
			}
					else
					{
					?>                             

<!-- Flash player Start -->
<?php //print_r($this->categoryview); 
//echo $this->categoryview['title'];
//echo $this->categoryview[0]->title;
//print_r($this->categoryList);
//echo $this->categoryview[0]->title;
print_r($this->categoryview);
//echo $this->categoryview[0]->seo_category;
if(isset($_GET['opt']) && $_GET['opt'] == 2) {
//echo "<div class='clear'></div><h1 class='home-link hoverable'> ".$this->categoryview[0]->title ."</h1>";
	echo "<div class='clear'></div><h1 class='home-link hoverable'> ".$this->categoryview[0]->seo_category ."</h1>";
}
?>
<div id="flashplayer">
	<embed wmode="opaque" src="<?php echo $playerpath; ?>" type="application/x-shockwave-flash"
		   allowscriptaccess="always" allowfullscreen="true" flashvars="baserefJHDV=<?php echo $baseurl; ?>
<?php
echo '&mtype=playerModule&amp;id=' . $this->categoryview['videoForPlayer'][0]->id . '&amp;catid=' . $this->categoryview['videoForPlayer'][0]->playlistid;
?>"  style="width:<?php
echo $player_values['width'];
?>px; height:<?php
echo $player_values['height'];
?>px" />
</div>
					<?php
					}
		}
	}

			// Specifying the no of columns
			$no_of_columns = $thumbview['categorycol'];

			foreach ($this->categoryList as $val)
			{
				$current_column = 1;
				$l = 0;

				for ($i = 0; $i < $totalrecords; $i++)
				{
					if ($val->parent_id == $this->categoryview[$i]->parent_id
						&& $val->category == $this->categoryview[$i]->category)
						{
						$colcount = $current_column % $no_of_columns;

						if ($colcount == 1 && $l == 0)
						{
							echo "<div class='clear'></div><h1 class='home-link hoverable'> $val->category </h1>";
						}

						if ($colcount == 1 || $no_of_columns == 1)
						{
							echo "</ul><ul class='ulvideo_thumb clearfix'>";
							$l++;
						}

						// For SEO settings
						$seoOption = $dispenable['seo_option'];

						if ($seoOption == 1)
						{
							$categoryCategoryVal = "category=" . $this->categoryview[$i]->seo_category;
							$categoryVideoVal = "video=" . $this->categoryview[$i]->seotitle;
						}
						else
						{
							$categoryCategoryVal = "catid=" . $this->categoryview[$i]->catid;
							$categoryVideoVal = "id=" . $this->categoryview[$i]->id;
						}

						if ($this->categoryview[$i]->filepath == "File"
							|| $this->categoryview[$i]->filepath == "FFmpeg"
							|| $this->categoryview[$i]->filepath == "Embed")
						{
							if (isset($this->categoryview[$i]->amazons3) && $this->categoryview[$i]->amazons3 == 1)
							{
								$src_path = $dispenable['amazons3link']
										. $this->categoryview[$i]->thumburl;
							}
							else
							{
								$src_path = "components/com_contushdvideoshare/videos/" . $this->categoryview[$i]->thumburl;
							}
						}

						if ($this->categoryview[$i]->filepath == "Url"
							|| $this->categoryview[$i]->filepath == "Youtube")
						{
							$src_path = $this->categoryview[$i]->thumburl;
						}
						?>
				<?php
				if ($this->categoryview[$i]->id != '')
				{
					?>
							<li class="video-item">
								<div class="home-thumb">
									<div class="list_video_thumb">
										<a class="featured_vidimg" rel="htmltooltip" href="<?php
										echo JRoute::_(
												"index.php?Itemid=" . $Itemid . "&amp;option=com_contushdvideoshare&amp;view=player&amp;"
												. $categoryCategoryVal . "&amp;" . $categoryVideoVal
												);
										?>" >
											<img class="yt-uix-hovercard-target" src="<?php echo $src_path; ?>"
												 title="" alt="thumb_image" /></a>
									</div>
									<div class="show-title-container">
										<a href="<?php
										echo JRoute::_(
												"index.php?Itemid=" . $Itemid . "&amp;option=com_contushdvideoshare&amp;view=player&amp;"
												. $categoryCategoryVal . "&amp;" . $categoryVideoVal
												);
										?>"
										class="show-title-gray info_hover"><?php
									if (strlen($this->categoryview[$i]->title) > 50)
									{
										echo JHTML::_('string.truncate', ($this->categoryview[$i]->title), 50);
									}
									else
									{
										echo $this->categoryview[$i]->title;
									}
									?></a>
									</div>

									<?php
									if ($dispenable['ratingscontrol'] == 1)
									{
										if (isset($this->categoryview[$i]->ratecount) && $this->categoryview[$i]->ratecount != 0)
										{
											$ratestar = round($this->categoryview[$i]->rate / $this->categoryview[$i]->ratecount);
										}
										else
										{
											$ratestar = 0;
										}
										?>
										<div class="ratethis1 <?php echo $ratearray[$ratestar]; ?> "></div>

							<?php
									}

									if ($dispenable['viewedconrtol'] == 1)
							{
								?>

										<span class="floatright viewcolor">
											<?php echo $this->categoryview[$i]->times_viewed; ?> 
												<?php echo JText::_('HDVS_VIEWS'); ?></span>
							<?php
									}
							?>
								</div>
								<!--Tooltip Starts Here-->
<div class="htmltooltip">
		<?php
		if ($this->categoryview[$i]->description)
		{
		?>
<p class="tooltip_discrip">
	<?php echo JHTML::_('string.truncate', (strip_tags($this->categoryview[$i]->description)), 120); ?></p>
		<?php
		}
		?>
				<div class="tooltip_category_left">
					<span class="title_category"><?php echo JText::_('HDVS_CATEGORY'); ?>: </span>
					<span class="show_category"><?php echo $this->categoryview[$i]->category; ?></span>
				</div>
				<?php
				if ($dispenable['viewedconrtol'] == 1)
				{
					?>
					<div class="tooltip_views_right">
						<span class="view_txt"><?php echo JText::_('HDVS_VIEWS'); ?>: </span>
						<span class="view_count"><?php echo $this->categoryview[$i]->times_viewed; ?> </span>
					</div>
					<div id="htmltooltipwrapper<?php echo $i; ?>">
						<div class="chat-bubble-arrow-border"></div>
						<div class="chat-bubble-arrow"></div>
					</div>
				<?php
				}
				?>
			</div>
		<!--Tooltip ends and PAGINATION STARTS HERE -->
							</li>
						<?php
				}
							?>
						<!--First row-->
						<?php
						if ($colcount == 0)
						{
							echo '</ul>';
							$current_column = 0;
						}

						$current_column++;
					}
				}
			}
			?>

		</div>
		
		<ul class="hd_pagination">
			<?php
			if (isset($this->categoryview['pageno']))
			{
				$q = $this->categoryview['pageno'] - 1;

				if ($this->categoryview['pageno'] > 1)
				{
					echo("<li><a onclick='changepage($q);'>" . JText::_('HDVS_PREVIOUS') . "</a></li>");
				}

				if ($requestpage)
				{
					if ($requestpage > 3)
					{
						$page = $requestpage - 1;

						if ($requestpage > 3)
						{
							if ($requestpage >= 7)
							{
								$next_page_cal = $requestpage / 2;
								$next_page = ceil($next_page_cal);
								echo("<li><a onclick='changepage(1)'>1</a></li>");
								echo ("<li>...</li>");
								echo("<li><a onclick='changepage(" . $next_page . ")'>$next_page</a></li>");
								echo ("<li>...</li>");
							}
							else
							{
								echo("<li><a onclick='changepage(1)'>1</a></li>");
								echo ("<li>...</li>");
							}
						}
					}
					else
					{
						$page = 1;
					}
				}
				else
				{
					$page = 1;
				}

				if ($this->categoryview['pages'] > 1)
				{
					for ($i = $page, $j = 1; $i <= $this->categoryview['pages']; $i++, $j++)
					{
						if ($this->categoryview['pageno'] != $i)
						{
							echo("<li><a onclick='changepage(" . $i . ")'>" . $i . "</a></li>");
						}
						else
						{
							echo("<li><a onclick='changepage($i);' class='activepage'>$i</a></li>");
						}

						if ($j > 3)
						{
							break;
						}
					}

					if ($i < $this->categoryview['pages'])
					{
						if ($i + 1 != $this->categoryview['pages'])
						{
							echo ("<li>...</li>");
						}

						echo("<li><a onclick='changepage(" . $this->categoryview['pages'] . ")'>" . $this->categoryview['pages'] . "</a></li>");
					}

					$p = $this->categoryview['pageno'] + 1;

					if ($this->categoryview['pageno'] < $this->categoryview['pages'])
					{
						echo ("<li><a onclick='changepage($p);'>" . JText::_('HDVS_NEXT') . "</a></li>");
					}
				}
			}
			?>
		</ul>
		<!--  PAGINATION END HERE-->
<?php
	}
?>
</div>
<?php
if (JRequest::getVar('memberidvalue', '', 'post', 'int'))
{
	$memberidvalue = JRequest::getVar('memberidvalue', '', 'post', 'int');
}

$memberidvalue = isset($memberidvalue) ? $memberidvalue : '';
?>
<form name="memberidform" id="memberidform"
	  action="<?php echo JRoute::_('index.php?Itemid=' . $Itemid . '&amp;option=com_contushdvideoshare&view=membercollection'); ?>"
	  method="post">
	<input type="hidden" id="memberidvalue" name="memberidvalue" value="<?php echo $memberidvalue; ?>" />
</form>
<?php
$page = $_SERVER['REQUEST_URI'];
$hidden_page = '';
$searchtextbox = JRequest::getVar('searchtxtbox', '', 'post', 'string');
$hiddensearchbox = JRequest::getVar('hidsearchtxtbox', '', 'post', 'string');

if ($requestpage)
{
	$hidden_page = $requestpage;
}
else
{
	$hidden_page = '';
}

if ($searchtextbox)
{
	$hidden_searchbox = $searchtextbox;
}
else
{
	$hidden_searchbox = $hiddensearchbox;
}
?>
<form name="pagination" id="pagination" action="<?php echo $page; ?>" method="post">
	<input type="hidden" id="video_pageid" name="video_pageid" value="<?php echo $hidden_page ?>" />
	<input type="hidden" id="hidsearchtxtbox" name="hidsearchtxtbox" value="<?php echo $hidden_searchbox; ?>" />
</form>
<?php
$lang = JFactory::getLanguage();
$langDirection = (bool) $lang->isRTL();

if ($langDirection == 1)
{
	$rtlLang = 1;
}
else
{
	$rtlLang = 0;
}
?>
<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function($) {
		jQuery(".ulvideo_thumb").mouseover(function() {
			htmltooltipCallback("htmltooltip", "",<?php echo $rtlLang; ?>);
		});
	});
	jQuery(document).ready(function($) {
		htmltooltipCallback("htmltooltip", "",<?php echo $rtlLang; ?>);
	})
	jQuery(document).click(function() {
		htmltooltipCallback("htmltooltip", "",<?php echo $rtlLang; ?>);
	})
	function membervalue(memid)
	{
		document.getElementById('memberidvalue').value = memid;
		document.memberidform.submit();
	}

	function changepage(pageno)
	{
		document.getElementById("video_pageid").value = pageno;
		document.pagination.submit();
	}
</script>
