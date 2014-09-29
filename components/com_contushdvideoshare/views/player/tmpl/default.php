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
echo $this->loadTemplate('player');
echo $this->loadTemplate('videos');
