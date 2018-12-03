<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* Part: blocks				                                            */
/* Part Name: block-ads		                                            */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2006 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
########################################################################
# PHP-Nuke Block: MashhadTeam Center Forum Block v.2 tabbed 		   #
# Made for PHP-Nuke 8.4                                                #
#                                                                      #
# Made by mahmood namvar [iman64]                                      #
# phpnukiha@yahoo.com                                				   #
########################################################################

if (!defined('BLOCK_FILE'))
{
    Header("Location: ../index.php");
    die();
}

global $db, $nuke_configs, $block_global_contents, $users_system, $custom_theme_setup;

$content = "";

$MTForumTabed = new MTForumTabed();
$latest_topics = $MTForumTabed->MTForumTabed();
$custom_theme_setup = array_merge_recursive($custom_theme_setup, array(
	"default_css" => array(
		"<link rel=\"stylesheet\" href=\"".$nuke_configs['nukecdnurl']."includes/Ajax/jquery/jquery-ui.min.css\" />",
		"<style>
			.MTFpager{font-family: 'tahoma';}
			.ui-tabs { direction:rtl; } 
			.ui-tabs .ui-tabs-nav { direction:rtl; }
			.ui-tabs .ui-tabs-nav li { float: right;}
			.ui-tabs .ui-tabs-nav li a { float: right;font-family:tahoma;}
			.ui-tabs .ui-tabs-panel {padding: 1px;}
		</style>"
	),
	"default_js" => array(
		
		"<script type=\"text/javascript\" language=\"javascript\" src=\"".$nuke_configs['nukecdnurl']."includes/Ajax/jquery/jquery-ui.min.js\"></script>",
	),
	"defer_js" => array(
		"<script type=\"text/javascript\" language=\"javascript\" src=\"".$nuke_configs['nukecdnurl']."includes/Ajax/jquery/MTForumTabed.js\"></script>",
		"<script>
			$(function() {
				var new_index;
				$(\"#forum_tabs\").tabs({
					show: { effect: \"blind\", duration: 100 },
					activate: function(event, ui) {
						new_index = ui.newTab.index()+1;
						ChangeTabedForumPage('First', new_index, '', '');
					}
				});
				$(\"#forum_tabs\").show(0);
			});
		</script>"
	)
));

$content .= "
<div id=\"forum_tabs\" style=\"display:none;\" class=\"MTForumBlock\">
	<ul>
		<li><a href=\"#forum_tabs-1\">همه</a></li>
		<li><a href=\"#forum_tabs-2\">پر بازديدترينها</a></li>
		<li><a href=\"#forum_tabs-3\">داغ ترینها</a></li>
		<li><a href=\"#forum_tabs-4\">مهم ها</a></li>
		<li><a href=\"#forum_tabs-5\">اطلاعیه ها</a></li>
		<li><a href=\"#forum_tabs-6\">جستجو</a></li>
	</ul>
	<div id=\"forum_tabs-1\"><div class=\"MTForumBlock\">$latest_topics</div></div>
	<div id=\"forum_tabs-2\"></div>
	<div id=\"forum_tabs-3\"></div>
	<div id=\"forum_tabs-4\"></div>
	<div id=\"forum_tabs-5\"></div>
	<div id=\"forum_tabs-6\"><input type=\"text\" style=\"width:300px;padding:5px;visibility:hidden;\" id=\"MTFSerach_input\" value=\"\" /><input type=\"button\" value=\"جستجو\" style=\"width:50px;padding:5px;visibility:hidden;\" id=\"MTFSerach_submit\" /><br /></div>

	<ul class=\"pager MTFpager\">
		<li class=\"previous\"><a id=\"MTFNext_button\" href=\"javascript:ChangeTabedForumPage('Prev', 1, '', '')\">"._PREV."</a></li>
		<li id=\"MTFloader\"></li>
		<li class=\"next\"><a id=\"MTFPrev_button\" href=\"javascript:ChangeTabedForumPage('Next', 1, '', '')\">"._NEXT."</a></li>
	</ul>
</div>";
?>