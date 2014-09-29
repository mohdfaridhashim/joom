<?php
/**
 * View file to display member videos
 *
 * This file is to display member videos
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

// Rating array
$ratearray = array("nopos1", "onepos1", "twopos1", "threepos1", "fourpos1", "fivepos1");
$user = JFactory::getUser();
$requestpage = JRequest::getVar('video_pageid', '', 'post', 'int');
$thumbview = unserialize($this->memberpagerowcol[0]->thumbview);
$dispenable = unserialize($this->memberpagerowcol[0]->dispenable);
$Itemid = $this->Itemid;
?>
<style type="text/css">
	#video-grid-container .ulvideo_thumb .video-item{margin-right:<?php echo $thumbview['memberpagewidth'] . 'px'; ?>}
</style>

<?php
if (USER_LOGIN == '1')
{
	if ($user->get('id') != '')
	{
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			?>
			<span class="toprightmenu">
				<a href="<?php
		echo JRoute::_("index.php?Itemid=" . $Itemid . "&amp;option=com_contushdvideoshare&view=myvideos"); ?>"><?php echo JText::_('HDVS_MY_VIDEOS'); ?></a> |
				<a href="<?php
					echo JRoute::_(
							'index.php?option=com_users&task=user.logout&'
							. JSession::getFormToken() . '=1&return=' . base64_encode(JUri::root())
							);
					?>">
							<?php echo JText::_('HDVS_LOGOUT'); ?></a>
			</span>
		<?php
		}
		else
		{
		?>
			<span class="toprightmenu">
				<a href="<?php
		echo JRoute::_("index.php?Itemid=" . $Itemid . "&amp;option=com_contushdvideoshare&view=myvideos"); ?>"><?php echo JText::_('HDVS_MY_VIDEOS'); ?></a> |
				<a href="<?php
		echo JRoute::_("index.php?option=com_user&task=logout"); ?>"><?php echo JText::_('HDVS_LOGOUT'); ?></a>
			</span>
		<?php
		}
	}
	else
	{
		$current_url = 'index.php?option=com_contushdvideoshare&view=membercollection';

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$login_url = JURI::base() . "index.php?option=com_users&amp;view=login&return=" . base64_encode($current_url);
		}
		else
		{
			$login_url = JURI::base() . "index.php?option=com_user&amp;view=login&return=" . base64_encode($current_url);
		}

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			?><span class="toprightmenu">
				<a href="<?php
		echo JRoute::_("index.php?option=com_users&view=registration"); ?>"><?php echo JText::_('HDVS_REGISTER'); ?></a> |
				<a href="<?php
				echo $login_url;
				?>"  alt="<?php
				echo JText::_('HDVS_LOGIN');
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
				?>" alt="<?php
				echo JText::_('HDVS_LOGIN');
				?>"> <?php
				echo JText::_('HDVS_LOGIN');
				?></a></span>
			<?php
		}
	}
}
?>
<div class="clearfix" >
<?php
foreach ($this->membercollection as $rows)
{
	?>
		<h1><?php echo JText::_('HDVS_VIDEO_ADDED_BY'); ?>
		<?php
		if ($rows->username == '')
		{
			echo "Administrator";
		}
		else
		{
			echo ucwords($rows->username);
		}
		?></h1>
			<?php break;
}

		$totalrecords = count($this->membercollection);
		$totalrecords = $thumbview['memberpagecol'] * $thumbview['memberpagerow'];

		if (count($this->membercollection) - 4 < $totalrecords)
		{
			$totalrecords = count($this->membercollection) - 4;
		}
		?>
	<div id="video-grid-container" class="clearfix">
	<?php
	$no_of_columns = $thumbview['memberpagecol'];
	$current_column = 1;

	for ($i = 0; $i < $totalrecords; $i++)
	{
		$colcount = $current_column % $no_of_columns;

		if ($colcount == 1 || $no_of_columns == 1)
		{
			echo '<ul class="ulvideo_thumb clearfix">';
		}

		$seoOption = $dispenable['seo_option'];

		if ($seoOption == 1)
		{
			$memberCategoryVal = "category=" . $this->membercollection[$i]->seo_category;
			$memberVideoVal = "video=" . $this->membercollection[$i]->seotitle;
		}
		else
		{
			$memberCategoryVal = "catid=" . $this->membercollection[$i]->catid;
			$memberVideoVal = "id=" . $this->membercollection[$i]->id;
		}

		if ($this->membercollection[$i]->filepath == "File"
			|| $this->membercollection[$i]->filepath == "FFmpeg"
			|| $this->membercollection[$i]->filepath == "Embed")
		{
			if (isset($this->membercollection[$i]->amazons3) && $this->membercollection[$i]->amazons3 == 1)
			{
				$src_path = $dispenable['amazons3link']
						. $this->membercollection[$i]->thumburl;
			}
			else
			{
				$src_path = "components/com_contushdvideoshare/videos/" . $this->membercollection[$i]->thumburl;
			}
		}

		if ($this->membercollection[$i]->filepath == "Url" || $this->membercollection[$i]->filepath == "Youtube")
		{
			$src_path = $this->membercollection[$i]->thumburl;
		}

		if ($this->membercollection[$i]->id != '')
		{
				?>
				<li class="video-item">
					<div class="home-thumb" id="member_thread">
						<div class="list_video_thumb">
							<a class="featured_vidimg" rel="htmltooltip" href="<?php echo JRoute::_(
										"index.php?Itemid=" . $Itemid . "&amp;option=com_contushdvideoshare&amp;view=player&amp;"
										. $memberCategoryVal . "&amp;" . $memberVideoVal
										);
								?>" >
								<img class="yt-uix-hovercard-target" src="<?php echo $src_path; ?>"  border="0"
									 title="" alt="thumb_image" /></a>

						</div>
						<div class="video_thread">
							<div class="show-title-container">
								<a href="<?php
								echo JRoute::_(
										"index.php?Itemid=" . $Itemid . "&amp;option=com_contushdvideoshare&amp;view=player&amp;"
										. $memberCategoryVal . "&amp;" . $memberVideoVal
										);
								?>" class="show-title-gray info_hover">
		<?php
		if (strlen($this->membercollection[$i]->title) > 50)
		{
			echo JHTML::_('string.truncate', ($this->membercollection[$i]->title), 50);
		}
		else
		{
			echo $this->membercollection[$i]->title;
		}
		?></a>
							</div>
									<?php
									if ($dispenable['ratingscontrol'] == 1)
									{
										?><?php
										if (isset($this->membercollection[$i]->ratecount) && $this->membercollection[$i]->ratecount != 0)
										{
											$ratestar = round($this->membercollection[$i]->rate / $this->membercollection[$i]->ratecount);
										}
										else
										{
											$ratestar = 0;
										}
										?>
								<div class="ratethis1 <?php echo $ratearray[$ratestar]; ?> "></div>
		<?php
									}
										?>

							<?php
							if ($dispenable['viewedconrtol'] == 1)
							{
								?>
<span class="floatright viewcolor"><?php echo $this->membercollection[$i]->times_viewed; ?>
	<?php echo JText::_('HDVS_VIEWS'); ?></span></div>
							<?php
							}
							?>
					</div></li>
						<?php
		}

						if ($colcount == 0)
						{
							echo '</ul>';
							$current_column = 0;
						}

						$current_column++;
	}
					?>
	</div>
	<!--Tooltip Starts Here-->
		<?php
		for ($i = 0; $i < $totalrecords; $i++)
		{
			?>
		<div class="htmltooltip">
			<?php
			if ($this->membercollection[$i]->description)
			{
				?>
				<p class="tooltip_discrip">
					<?php echo JHTML::_('string.truncate', (strip_tags($this->membercollection[$i]->description)), 120); ?>
				</p>
			<?php
			}
			?>
			<div class="tooltip_category_left">
				<span class="title_category"><?php echo JText::_('HDVS_CATEGORY'); ?>: </span>
				<span class="show_category"><?php echo $this->membercollection[$i]->category; ?></span>
			</div>
			<?php
			if ($dispenable['viewedconrtol'] == 1)
			{
			?>
				<div class="tooltip_views_right">
					<span class="view_txt"><?php echo JText::_('HDVS_VIEWS'); ?>: </span>
					<span class="view_count"><?php echo $this->membercollection[$i]->times_viewed; ?> </span>
				</div>
				<div id="htmltooltipwrapper<?php echo $i; ?>">
					<div class="chat-bubble-arrow-border"></div>
					<div class="chat-bubble-arrow"></div>
				</div>
			<?php
			}
			?>
		</div>
<?php
		}
			?>
	<!--Tooltip end Here-->

	<ul class="hd_pagination">
		<?php
		$this->membercollection['pages'] = isset($this->membercollection['pages']) ? $this->membercollection['pages'] : '';
		$this->membercollection['pageno'] = isset($this->membercollection['pageno']) ? $this->membercollection['pageno'] : '';
		$pages = $this->membercollection['pages'];
		$q = $this->membercollection['pageno'];
		$q1 = $this->membercollection['pageno'] - 1;

		if ($this->membercollection['pageno'] > 1)
		{
			echo("<li><a onclick='changepage($q1);'>" . JText::_('HDVS_PREVIOUS') . "</a></li>");
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
						$next_page_val = $requestpage / 2;
						$next_page = ceil($next_page_val);
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

		if ($pages > 1)
		{
			for ($i = $page, $j = 1; $i <= $pages; $i++, $j++)
			{
				if ($q != $i)
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

			if ($i < $pages)
			{
				if ($i + 1 != $pages)
				{
					echo ("<li>...</li>");
				}

				echo("<li><a onclick='changepage(" . $pages . ")'>" . $pages . "</a></li>");
			}

			$p = $q + 1;

			if ($q < $pages)
			{
				echo ("<li><a onclick='changepage($p);'>" . JText::_('HDVS_NEXT') . "</a></li>");
			}
		}
		?>
	</ul>
</div>
		<?php
		if (JRequest::getVar('memberidvalue', '', 'post', 'int'))
		{
			$memberidvalue = JRequest::getVar('memberidvalue', '', 'post', 'int');
		}

		$memberidvalue = isset($memberidvalue) ? $memberidvalue : '';
		?>
<form name="memberidform" id="memberidform" action="
	<?php echo JRoute::_('index.php?Itemid=' . $Itemid . '&amp;option=com_contushdvideoshare&view=membercollection'); ?>" method="post">
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
