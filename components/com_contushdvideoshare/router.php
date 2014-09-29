<?php
/**
 * Router file for Contus HD Video Share
 *
 * This file will be called when the admine enables URL rewrite option
 *
 * @category   Apptha
 * @package    Com_Contushdvideoshare
 * @version    3.6
 * @author     Apptha Team <developers@contus.in>
 * @copyright  Copyright (C) 2014 Apptha. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Function to assign router values
 * 
 * @param   object  &$query  query string
 * 
 * @return  contushdvideoshareBuildRoute
 */
function contushdvideoshareBuildRoute(&$query)
{
	$segments = array();

	// Code for get itemid if itemid is empty. It's used to add alias name in URL link
	if (isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}

	if (isset($query['catid']))
	{
		$segments[] = $query['catid'];
		unset($query['catid']);
	}
	elseif (isset($query['category']))
	{
		$segments[] = $query['category'];
		unset($query['category']);
	}

	if (isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}
	elseif (isset($query['video']))
	{
		$segments[] = $query['video'];
		unset($query['video']);
	}

	if (isset($query['type']))
	{
		$segments[] = $query['type'];
		unset($query['type']);
	}

	return $segments;
}

/**
 * Function to assign view for the corresponding router value
 * 
 * @param   array  $segments  segments
 * 
 * @return  contushdvideoshareParseRoute
 */
function contushdvideoshareParseRoute($segments)
{
	$vars = array();

	// View is always the first element of the array
	$count = count($segments);

	if ($count)
	{
		switch ($segments[0])
		{
			case 'category':
				$vars['view'] = 'category';
				$vars['category'] = $segments[1];
				break;

			case 'player':
				$vars['view'] = 'player';

				if (isset($segments[2]))
				{
					$vars['category'] = $segments[1];
					$vars['video'] = $segments[2];
				}

				break;

			case 'rss':
				$vars['view'] = 'rss';

				if (isset($segments[2]))
				{
					$vars['type'] = $segments[2];
					$vars['catid'] = $segments[1];
				}
				else
				{
					$vars['type'] = $segments[1];
				}
			
				break;

			case 'configxml':
				$vars['view'] = 'configxml';
				$vars['id'] = $segments[1];

				if (isset($segments[2]))
				{
					$vars['catid'] = $segments[2];
				}

				break;

			case 'playxml':
				$vars['view'] = 'playxml';
				$vars['id'] = $segments[1];

				if (isset($segments[2]))
				{
					$vars['catid'] = $segments[2];
				}

				break;

			case 'adsxml':
				$vars['view'] = 'adsxml';

				break;

			case 'midrollxml':
				$vars['view'] = 'midrollxml';

				break;

			case 'languagexml':
				$vars['view'] = 'languagexml';

				break;

			case 'playerbase':
				$vars['view'] = 'playerbase';

				break;

			case 'featuredvideos':
				$vars['view'] = 'featuredvideos';

				break;

			case 'myvideos':
				$vars['view'] = 'myvideos';

				break;

			case 'recentvideos':
				$vars['view'] = 'recentvideos';

				break;

			case 'myvideos':
				$vars['view'] = 'myvideos';

				break;

			case 'hdvideosharesearch':
				$vars['view'] = 'hdvideosharesearch';

				break;

			case 'membercollection':
				$vars['view'] = 'membercollection';

				break;

			case 'popularvideos':
				$vars['view'] = 'popularvideos';

				break;

			case 'relatedvideos':
				$vars['view'] = 'relatedvideos';

				if (isset($segments[1]))
				{
					$vars['video'] = $segments[1];
				}

				break;

			case 'recentvideos':
				$vars['view'] = 'recentvideos';

				break;

			case 'featuredvideos':
				$vars['view'] = 'featuredvideos';

				break;

			case 'videoupload':
				$vars['view'] = 'videoupload';

				if (isset($segments[1]))
				{
					$vars['id'] = $segments[1];
				}

				if (isset($segments[2]))
				{
					$vars['type'] = $segments[2];
				}

				break;
			break;
		}
	}

	return $vars;
}
