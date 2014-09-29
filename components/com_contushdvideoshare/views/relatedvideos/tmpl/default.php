<?php
/**
 * Related videos view file
 *
 * This file is to display Related videos detail
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

// Get current page number
$requestpage = JRequest::getVar('video_pageid', '', 'post', 'int');
$thumbview = unserialize($this->relatedvideosrowcol[0]->thumbview);
$dispenable = unserialize($this->relatedvideosrowcol[0]->dispenable);
$document = JFactory::getDocument();
$Itemid = $this->Itemid;
$style = '#video-grid-container .ulvideo_thumb .video-item{margin-right:' . $thumbview['relatedwidth'] . 'px; }';
$document->addStyleDeclaration($style);

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
				<a href="index.php?option=com_user&task=logout"><?php echo JText::_('HDVS_LOGOUT'); ?></a>
			</div>
		<?php
		}
	}
	else
	{
		if (JRequest::getVar('id'))
		{
			$video = 'id=' . JRequest::getVar('id');
		}
		else
		{
			$video = 'video=' . JRequest::getVar('video');
		}

		$current_url = 'index.php?option=com_contushdvideoshare&view=relatedvideos&' . $video;

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
				<a href="index.php?option=com_users&view=registration"><?php echo JText::_('HDVS_REGISTER'); ?></a> |
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
				<a href="index.php?option=com_user&view=register"><?php echo JText::_('HDVS_REGISTER'); ?></a> |
				<a  href="<?php
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

<div class="standard clearfix">
	<h1 class="home-link hoverable"><?php echo JText::_('HDVS_RELATED_VIDEOS'); ?></h1>
	<div id="video-grid-container" class="clearfix">
		<?php
		$totalrecords = $thumbview['relatedcol'] * $thumbview['relatedrow'];

		if (count($this->relatedvideos) - 4 < $totalrecords)
		{
			$totalrecords = count($this->relatedvideos) - 4;
		}

		$no_of_columns = $thumbview['relatedcol'];
		$current_column = 1;

		for ($i = 0; $i < $totalrecords; $i++)
		{
			$colcount = $current_column % $no_of_columns;

			if ($colcount == 1 || $no_of_columns == 1)
			{
				echo '<ul class="ulvideo_thumb clearfix">';
			}

			// For SEO settings
			$seoOption = $dispenable['seo_option'];

			if ($seoOption == 1)
			{
				$relatedCategoryVal = "category=" . $this->relatedvideos[$i]->seo_category;
				$relatedVideoVal = "video=" . $this->relatedvideos[$i]->seotitle;
			}
			else
			{
				$relatedCategoryVal = "catid=" . $this->relatedvideos[$i]->catid;
				$relatedVideoVal = "id=" . $this->relatedvideos[$i]->id;
			}

			if ($this->relatedvideos[$i]->filepath == "File"
				|| $this->relatedvideos[$i]->filepath == "FFmpeg"
				|| $this->relatedvideos[$i]->filepath == "Embed")
			{
				if (isset($this->relatedvideos[$i]->amazons3) && $this->relatedvideos[$i]->amazons3 == 1)
				{
					$src_path = $dispenable['amazons3link']
							. $this->relatedvideos[$i]->thumburl;
				}
				else
				{
					$src_path = "components/com_contushdvideoshare/videos/" . $this->relatedvideos[$i]->thumburl;
				}
			}

			if ($this->relatedvideos[$i]->filepath == "Url" || $this->relatedvideos[$i]->filepath == "Youtube")
			{
				$src_path = $this->relatedvideos[$i]->thumburl;
			}
			?>
			<li class="video-item">
				<div class="list_video_thumb">
					<a class="featured_vidimg" rel="htmltooltip"
					   href="<?php
					   echo JRoute::_('index.php?Itemid=' . $Itemid . '&amp;option=com_contushdvideoshare&view=player&' . $relatedCategoryVal . '&' . $relatedVideoVal);
					   ?>">
						<img class="yt-uix-hovercard-target" src="<?php echo $src_path; ?>" title=""
							 alt="thumb_image" /></a>
				</div>

				<div class="show-title-container">
					<a href="<?php
					   echo JRoute::_('index.php?Itemid=' . $Itemid . '&amp;option=com_contushdvideoshare&view=player&' . $relatedCategoryVal . '&' . $relatedVideoVal);
					   ?>" class="show-title-gray info_hover">
	<?php
	if (strlen($this->relatedvideos[$i]->title) > 50)
	{
		echo JHTML::_('string.truncate', ($this->relatedvideos[$i]->title), 50);
	}
	else
	{
		echo $this->relatedvideos[$i]->title;
	}
	?></a>
				</div>
<?php
if ($dispenable['ratingscontrol'] == 1)
{
?>


		<?php
		if (isset($this->relatedvideos[$i]->ratecount) && $this->relatedvideos[$i]->ratecount != 0)
		{
			$ratestar = round($this->relatedvideos[$i]->rate / $this->relatedvideos[$i]->ratecount);
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

					<span class="floatright viewcolor"><?php echo $this->relatedvideos[$i]->times_viewed; ?>
						<?php echo JText::_('HDVS_VIEWS'); ?></span>
				<?php
				}
				?>
			</li>
				<?php
				if ($colcount == 0)
				{
					echo '</ul>';
					$current_column = 0;
				}

				$current_column++;
		}
			?>
	</div>
</div>

<!--Tooltip Starts Here-->
		<?php
		for ($i = 0; $i < $totalrecords; $i++)
		{
			?>
	<div class="htmltooltip">
			<?php
			if ($this->relatedvideos[$i]->description)
			{
				?>
			<p class="tooltip_discrip"><?php echo JHTML::_('string.truncate', (strip_tags($this->relatedvideos[$i]->description)), 120); ?></p>
			<?php
			}
			?>
		<div class="tooltip_category_left">
			<span class="title_category"><?php echo JText::_('HDVS_CATEGORY'); ?>: </span>
			<span class="show_category"><?php echo $this->relatedvideos[$i]->category; ?></span>
		</div>
	<?php
	if ($dispenable['viewedconrtol'] == 1)
	{
		?>
			<div class="tooltip_views_right">
				<span class="view_txt"><?php echo JText::_('HDVS_VIEWS'); ?>: </span>
				<span class="view_count"><?php echo $this->relatedvideos[$i]->times_viewed; ?> </span>
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
$pages = $this->relatedvideos['pages'];
$q = $this->relatedvideos['pageno'];
$q1 = $this->relatedvideos['pageno'] - 1;

if ($this->relatedvideos['pageno'] > 1)
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
				$next_page = $requestpage / 2;
				$next_page = ceil($next_page);
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
			echo ("<li>....</li>");
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
<form name="memberidform" id="memberidform" action="<?php
echo JRoute::_('index.php?Itemid=' . $Itemid . '&amp;option=com_contushdvideoshare&view=membercollection'); ?>" method="post">
	<input type="hidden" id="memberidvalue" name="memberidvalue" value="<?php
	if (JRequest::getVar('memberidvalue', '', 'post', 'int'))
	{
		echo JRequest::getVar('memberidvalue', '', 'post', 'int');
	}
	?>" />
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
