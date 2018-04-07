<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2002 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('NUKE_FILE'))
{
	die ("You can't access this file directly...");
}

##################################################
# Include for meta Tags generation               #
##################################################
global $db, $nuke_configs, $nuke_lang_cachedata, $nuke_rss_codes, $category;

$pagetitle = $nuke_configs['sitename'].((!defined("HOME_FILE")) ? " - ".((isset($meta_tags['title'])) ? strip_tags($meta_tags['title']):''):"");
$description = (isset($meta_tags['description'])) ? $meta_tags['description']:$nuke_configs['site_description'];
$keywords = (isset($meta_tags['keywords'])) ? $meta_tags['keywords']:$nuke_configs['site_keywords'];
$extra_meta_tags = (isset($meta_tags['extra_meta_tags'])) ? $meta_tags['extra_meta_tags']:array();
$description = str_replace(array("\n","\r"),"", $description);

$contents .= "
		<meta http-equiv=\"content-type\" content=\"text/html;charset=UTF-8\">
		<title>$pagetitle</title>
		<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
	if($description != '')
	$contents .= "	<meta name=\"description\" content=\"$description\">";

	if (file_exists("themes/".$nuke_configs['ThemeSel']."/images/favicon.ico"))
		$contents .= "	<link rel=\"shortcut icon\" href=\"".$nuke_configs['nukeurl']."/themes/".$nuke_configs['ThemeSel']."/images/favicon.ico\" type=\"image/x-icon\" />\n";
		
	if(isset($meta_tags['prev']) && $meta_tags['prev'] != '')
		$contents .= "	<link rel=\"prev\" href=\"".$meta_tags['prev']."\" />\n";
		
	if(isset($meta_tags['next']) && $meta_tags['next'] != '')
		$contents .= "	<link rel=\"next\" href=\"".$meta_tags['next']."\" />\n";
		
	if(isset($nuke_configs['site_meta_tags']) && $nuke_configs['site_meta_tags'] != '')
		$contents .= $nuke_configs['site_meta_tags']."\n";

	$contents .= "		<meta property=\"og:locale\" content=\"".$nuke_configs['locale']."\" />
		<meta property=\"og:type\" content=\"website\" />
		<meta property=\"og:title\" content=\"".$pagetitle."\" />
		<meta property=\"og:url\" content=\"".LinkToGT($meta_tags['url'])."\" />
		<meta property=\"og:site_name\" content=\"".$nuke_configs['sitename']."\" />";

	if ($nuke_configs['gverify'] != ""._YOUR_CODE."" && $nuke_configs['gverify'] != "")
		$contents .= "	<meta name=\"google-site-verification\" content=\"".$nuke_configs['gverify']."\" />\n";

	if ($nuke_configs['alexverify'] != ""._YOUR_CODE."" && $nuke_configs['alexverify'] != "")
		$contents .= "	<meta name=\"alexaVerifyID\" content=\"".$nuke_configs['alexverify']."\" />\n";

	if ($nuke_configs['yverify'] != ""._YOUR_CODE."" && $nuke_configs['yverify'] != "")
		$contents .= "	<meta name=\"y_key\" content=\"".$nuke_configs['yverify']."\">\n";

	// rss codes
	$rsslink = LinkToGT("index.php?modname=Feed");

	$contents .= "		<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Atom\" href=\"$rsslink\" />\n";

	if(isset($nuke_rss_codes) && is_array($nuke_rss_codes) && !empty($nuke_rss_codes))
		foreach($nuke_rss_codes as $nuke_rss_code)
			eval($nuke_rss_code);
	// rss codes
	
	//$extra_meta_tags
	if(!empty($extra_meta_tags))
		foreach($extra_meta_tags as $extra_meta_tag)
			$contents .= $extra_meta_tag;
###############################################
# DO NOT REMOVE THE FOLLOWING COPYRIGHT LINE! #
# YOU'RE NOT ALLOWED TO REMOVE NOR EDIT THIS. #
###############################################

// IF YOU REALLY NEED TO REMOVE IT AND HAVE MY WRITTEN AUTHORIZATION CHECK: http://phpnuke.org/index.php?modname=Commercial_License
// PLAY FAIR AND SUPPORT THE DEVELOPMENT, PLEASE!

//echo "<meta name=\"GENERATOR\" content=\"PHP-Nuke - Copyright by http://phpnuke.org\">\n";
?>