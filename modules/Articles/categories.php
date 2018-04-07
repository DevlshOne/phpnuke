<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2006 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (!defined('MODULE_FILE')) {
	die ("You can't access this file directly...");
}
require_once("mainfile.php");
$optionbox = "";
$module_name = basename(dirname(__FILE__));

define('INDEX_FILE', is_index_file($module_name));// to define INDEX_FILE status

function categories()
{
	global $userinfo, $db, $user, $page, $module_name, $nuke_articles_categories_cacheData, $nuke_configs;
		
	$contents 								= "";
	$contents 								.= OpenTable();
	
	$sub_cats_contents_num 					= 0;
	$array_contents 						= array();
	foreach($nuke_articles_categories_cacheData as $key => $val)
	{
		if($val['parent_id'] == 0)
		{
			$cat_link						= filter($val['catname_url'], "nohtml");
			$array_contents[$key] = array($val, $cat_link, LinkToGT("index.php?modname=Articles&category=$cat_link"));
			$sub_cats_contents_num++;
		}
	}
	if($sub_cats_contents_num > 0)
	{
		if(file_exists("themes/".$nuke_configs['ThemeSel']."/article_categories.php"))
			include("themes/".$nuke_configs['ThemeSel']."/article_categories.php");
		elseif(function_exists("article_categories"))
			$contents .= article_categories($article_info);
		else
		{
			$j 								= 1;
			$contents .= "<table width=\"100%\" border=\"0\">";
			foreach($array_contents as $key => $val)
			{
				if ($j==1) $contents	.= "<tr>";
				$contents				.= "<td width=\"100\"><a href=\"".$val[2]."\">".$val[0]['cattext']."</a></td>";
				if ($j==5)
				{
					$j=0;
					$contents			.= "</tr>";
				}
				$j++;
			}
			$contents .= "</table>";
		}
	}
	$contents								.= CloseTable();
		
	$meta_tags = array(
		"url" 								=> LinkToGT("index.php?modname=$module_name&file=categories"),
		"title" 							=> _ARTICLES_CATEGORIES,
		"description" 						=> '',
		"keywords" 							=> '',
		"prev" 								=> $prev_link,
		"next" 								=> $next_link,
		"extra_meta_tags" 					=> array()
	);
	
	include("header.php");
	$html_output .= show_modules_boxes($module_name, array("bottom_full", "top_full","left","top_middle","bottom_middle","right"), title($nuke_configs['sitename']." : "._ARTICLES_CATEGORIES."").$contents);
	include("footer.php");
}

$op 										= isset($op) ? $op : "";

switch($op)
{
	default:
		categories();
	break;
}
?>