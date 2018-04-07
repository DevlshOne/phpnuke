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

if(!function_exists("article_more"))
{
	function article_more($article_info)
	{
		global $nuke_configs;
		$contents = '';
		$htmltags = '';
		$posted = _POSTEDON." ".$article_info['datetime']." "._BY." "; 
		$posted .= get_author($article_info['aid']);
		$posted .= "&nbsp;&nbsp;<a href=\"".$article_info['print_link']."\" target=\"_blank\"><img border=\"0\" src=\"".$nuke_configs['nukecdnurl']."images/print.gif\" width=\"16\" height=\"16\" alt=\""._PRINT."\" title=\""._PRINT."\"></a>";
		$tags = str_replace(" ","-",$article_info['tags']);
		$tags = explode(",",$tags);
		$tags = array_filter($tags);
		foreach($tags as $tag)
			$htmltags .= "<i><a href=\"".LinkToGT("index.php?modname=Articles&tags=$tag")."\">".str_replace("_"," ",$tag)."</a></i> ";

		$contents .= "
		<div class=\"Articles\">
			<div class=\"ArticlesTitle\">
				<a href=\"".$article_info['report_link']."\" data-toggle=\"modal\" data-target=\"#sitemodal\">"._POST_REPORT."</a>
				<a href=\"".$article_info['friend_link']."\" data-toggle=\"modal\" data-target=\"#sitemodal\">"._INTRODUCE_TO_FRIENDS."</a>
				<a href=\"".$article_info['pdf_link']."\">"._PDFFILE."</a>
				<a href=\"".$article_info['print_link']."\">"._PRINT."</a>
				<div class=\"ArticlesRating\">".$article_info['rating_box']."</div>
				<h1 class=\"ArticlesTitleText\"><a href=\"".$article_info['article_link']."\" rel=\"bookmark\" title=\"".$article_info['title']."\">".$article_info['title']."</a></h1>
			</div>
			<div class=\"ArticlesBody\">
				<div class=\"ArticlesBodyText\">
					".$article_info['hometext']."
					<br />
					<br />
					".$article_info['bodytext']."
					<br />
					<br />
					$htmltags
				</div>
			</div>
			<div class=\"ArticlesFoot\">
				<div class=\"ArticlesFootText\">
					$posted
				</div>
			</div>
		</div>
		<br />";
		return $contents;
	}
}

global $articles_votetype, $nuke_configs, $userinfo, $articles_ratecookie, $nuke_articles_categories_cacheData, $nuke_bookmarksite_cacheData, $article_url, $visitor_ip, $sid, $pn_Cookies, $nuke_authors_cacheData;

$show_rating_user	= 1;// show users that rated to this storie.0 is deactive & 1 is active
//fix by SHahab SHT

$user_id = (isset($userinfo['user_id']) && isset($userinfo['is_registered']) && $userinfo['is_registered'] == 1) ? intval($userinfo['user_id']):0;
$sid = intval($sid);
$votetype = ($articles_votetype) ? $articles_votetype:$nuke_configs['votetype'];
$vote_where = ($user_id != 0) ? "user_id = :user_id":"rating_ip = :visitor_ip";
$votes_query					= "(SELECT id FROM ".SCORES_TABLE." WHERE ($vote_where) AND post_id = s.sid AND db_table = 'articles' ORDER BY id DESC LIMIT 1) as rated_id";

if($votetype == 1)
{
	$votes_query	.= ", SUM(IF(sc.score > 0, sc.score, -sc.score)) AS score, COUNT(sc.id) AS ratings, 0 as likes, 0 as dislikes";
	$newvotetype = 1;
}
else
{
	$votes_query	.= ", SUM(IF(sc.score >= 1, 1, 0)) AS likes, SUM(IF(sc.score < 0, 1, 0)) AS dislikes, COUNT(sc.id) AS ratings, 0 as score";
		$newvotetype = "2,3";
}

$query_where = array();

if($nuke_configs['gtset'] == 1)
{
	$query_where[] = "s.article_url=:article_url";
	
	if($article_url == '')
		die_404();
}
else
	$query_where[] = "s.sid=:sid";
	
if(!is_admin())
	$query_where[] = "s.status = 'publish'";
	
$query_where = implode(" AND ", $query_where);

$result					= $db->query("SELECT s.*,
s1.sid as psid, s1.title as ptitle, s1.article_url as particle_url, s1.time as ptime, s1.cat_link as pcat_link,
s2.sid as nsid, s2.title as ntitle, s2.article_url as narticle_url, s2.time as ntime, s2.cat_link as ncat_link,
$votes_query 
FROM ".ARTICLES_TABLE." AS s 
LEFT JOIN ".SCORES_TABLE." AS sc ON sc.post_id = s.sid AND sc.votetype IN ($newvotetype) AND sc.db_table = 'articles' 
LEFT JOIN ".ARTICLES_TABLE." AS s1 ON s1.time = (
	SELECT time
	FROM ".ARTICLES_TABLE." 
	WHERE time < s.time AND post_type = s.post_type ".((!is_admin()) ? "AND status = 'publish'":"")."
	ORDER BY time DESC LIMIT 1
) 
LEFT JOIN ".ARTICLES_TABLE." AS s2 ON s2.time = (
	SELECT time
	FROM ".ARTICLES_TABLE." 
	WHERE time > s.time AND post_type = s.post_type ".((!is_admin()) ? "AND status = 'publish'":"")."
	ORDER BY time ASC LIMIT 1
) 
where $query_where", array(":user_id" => $user_id, ":visitor_ip" => $visitor_ip, ":article_url" => $article_url, ":sid" => $sid));

if(intval($result->count()) > 0)
{
	$row = $result->results()[0];

	$urlop = (($op != '') && in_array($op, array("pdf","print","friend","report"))) ? "$op/":"";
	if(intval($row['sid'] > 0))
	{
		if(trim(articleslink(intval($row['sid']), filter($row['title'], "nohtml"), filter($row['article_url'], "nohtml"), filter($row['time'], "nohtml"), intval($row['cat_link'])).$urlop, "/")."/" != trim(rawurldecode($REQUSERURL), "/")."/")
		{
			die(die_404());
		}
	}
	else
		die(die_404());
}
else
{
	die(die_404());
}

$article_info['sid']			= intval($row['sid']);
$article_info['aid']			= filter($row['aid'], "nohtml");
$article_info['post_type']		= filter($row['post_type'], "nohtml");
$article_info['aid_url']		= (isset($nuke_authors_cacheData[$row['aid']]['url'])) ? filter($nuke_authors_cacheData[$row['aid']]['url'], "nohtml"):$nuke_configs['nukeurl'];
$article_info['time']			= filter($row['time']);
$article_info['title']			= filter($row['title'], "nohtml");
$article_info['title_lead']		= filter($row['title_lead'], "nohtml");
$article_info['title_color']	= filter($row['title_color'], "nohtml");
$hometext						= text_rel2abs(stripslashes($row['hometext']));
$article_info['hometext']		= codereplace($hometext,$article_info['sid']);
$bodytext						= text_rel2abs(stripslashes($row['bodytext']));
$article_info['bodytext']		= codereplace($bodytext,$article_info['sid']);
$article_info['comments']		= intval($row['comments']);
$article_info['counter']		= intval($row['counter']);
$article_info['micro_meta']		= (isset($row['micro_meta']) && $row['micro_meta'] != "") ? unserialize(stripslashes($row['micro_meta'])):array();
$article_info['permissions']	= ($row['permissions'] != "") ? explode(",",$row['permissions']):array(0);
$article_info['cats']			= ($row['cat'] != "") ? explode(",",$row['cat']):array();
$article_info['cat_link']		= $row['cat_link'];
$article_info['informant']		= filter($row['informant'], "nohtml");
$article_info['article_url']	= filter($row['article_url']);
$article_info['tags']			= filter($row['tags']);
$tags2							= filter($row['tags']);
$article_info['allow_comment']	= intval($row['allow_comment']);
$article_info['score']			= intval($row['score']);
$article_info['ratings']		= intval($row['ratings']);
$article_info['article_pass']	= ($row['article_pass'] != '') ? md5($row['article_pass']):"";
$article_info['likes']			= intval($row['likes']);
$article_info['dislikes']		= intval($row['dislikes']);
$article_info['datetime']		= nuketimes($article_info['time']);
$article_info['article_image']	= get_article_image($article_info['sid'], $row['article_image'], $hometext);
$article_info['rated_id']	= intval($row['rated_id']);
if(!empty($article_info['cats']))
{
	foreach($article_info['cats'] as $cat)
	{
		if(!isset($nuke_articles_categories_cacheData[$cat])) continue;
		$article_info['cats_data'][$cat]		= array(
			"cattext"			=> filter($nuke_articles_categories_cacheData[$cat]['cattext'], "nohtml"),
			"catname"			=> filter($nuke_articles_categories_cacheData[$cat]['catname'], "nohtml"),
			"catimage"			=> filter($nuke_articles_categories_cacheData[$cat]['catimage'], "nohtml"),
			"catlink"			=> LinkToGT("index.php?modname=Articles&category=".filter($nuke_articles_categories_cacheData[$cat]['catname_url'], "nohtml")),
		);
	}			
}

$article_info['article_image_width'] = $article_info['article_image_height'] = 0;
if($article_info['article_image'] != '' && file_exists($article_info['article_image']))
	list($article_info['article_image_width'], $article_info['article_image_height']) = getimagesize($article_info['article_image']);
		
$disabled_rating = false;

if ($pn_Cookies->exists('Articles_ratecookie'))
{
	$rcookie				= base64_decode($pn_Cookies->get('Articles_ratecookie'));
	$rcookie				= addslashes($rcookie);
	$r_cookie				= explode(",", $rcookie);
	if(in_array($article_info['sid'], $r_cookie))
	{
		$disabled_rating	= true;
	}
}

if($article_info['rated_id'] > 0)
	$disabled_rating	= true;

$article_info['rating_box']		= rating_load($article_info['score'], $article_info['ratings'], $article_info['likes'], $article_info['dislikes'], 'Articles', "sid", $article_info['sid'], $disabled_rating, $votetype);


			
if(isset($nuke_articles_categories_cacheData[$article_info['cat_link']]))
{
	$article_info['cattext_link']		= filter($nuke_articles_categories_cacheData[$article_info['cat_link']]['cattext'], "nohtml");
	$article_info['catname_link']		= filter($nuke_articles_categories_cacheData[$article_info['cat_link']]['catname'], "nohtml");
	$article_info['catimage_link']		= filter($nuke_articles_categories_cacheData[$article_info['cat_link']]['catimage'], "nohtml");
}
else
{
	$article_info['cattext_link'] = "";
	$article_info['catname_link'] = "";
	$article_info['catimage_link'] = "";
}

$article_info['article_link']		= LinkToGT(articleslink($article_info['sid'], $article_info['title'], $article_info['article_url'], $article_info['time'], $article_info['cat_link']));
$article_info['next_article_link']	= (intval($row['nsid']) != 0) ? LinkToGT(articleslink(intval($row['nsid']), filter($row['ntitle'], "nohtml"), filter($row['narticle_url'], "nohtml"), $row['ntime'], intval($row['ncat_link']))):"";
$article_info['prev_article_link']	= (intval($row['psid']) != 0) ? LinkToGT(articleslink(intval($row['psid']), filter($row['ptitle'], "nohtml"), filter($row['particle_url'], "nohtml"), $row['ptime'], intval($row['pcat_link']))):"";

if (empty($article_info['aid']))
	Header("Location: ".LinkToGT("index.php?modname=$module_name")."");

$db->table(ARTICLES_TABLE)
	->where('sid', $article_info['sid'])
	->update([
		"counter" => true
	]);

$pagetitle				= $article_info['title'];

if(empty($article_info['informant']))
	$article_info['informant']	= _ANONYMOUS;

$allow_to_view					= false;
$disallow_message				= "";

$permission_result				= phpnuke_permissions_check($article_info['permissions']);

$allow_to_view					= $permission_result[0];
$disallow_message				= $permission_result[1];

if (!$allow_to_view)
{
	$article_info['bodytext']		= "<div class=\"text-center\">";
	$article_info['bodytext']		.="$disallow_message<br><br>";
	$article_info['bodytext']		.= _GOBACK;
	$article_info['bodytext']		.= "</div>";
}

$this_article_pass = $pn_Cookies->get("this_article_pass".$article_info['sid']);
$this_article_pass = intval($this_article_pass);

if($article_info['article_pass'] != "" && $this_article_pass != "1" && !is_admin())
{
	$your_pass = (isset($your_pass)) ? filter($your_pass, "nohtml"):'';

	if($your_pass == '')
	{
		$article_info['bodytext']= "<div class=\"text-center\">";
		$article_info['bodytext'].=""._REQUIRED_PASS."<br><br><form action=\"".LinkToGT($article_info['article_link'])."\" method=\"post\">";
		$article_info['bodytext'].=""._ENTER_PASSWORD." &nbsp;<input type=\"password\" name=\"your_pass\" /> &nbsp;<input type=\"submit\" value=\""._SEND."\" /></form>";
		$article_info['bodytext'].= "</div>";
		$ending = 1;
	}
	elseif($your_pass != '' && $article_info['article_pass'] != md5($your_pass))
	{
		$article_info['bodytext']= "<div class=\"text-center\">";
		$article_info['bodytext'].=""._WRONG_PASS."<br><br><form action=\"".LinkToGT($article_info['article_link'])."\" method=\"post\">";
		$article_info['bodytext'].=""._ENTER_PASSWORD." &nbsp;<input type=\"password\" name=\"your_pass\" /> &nbsp;<input type=\"submit\" value=\""._SEND."\" /></form>";
		$article_info['bodytext'].= "</div>";
		$ending = 1;
	}
	elseif($your_pass != '' && $article_info['article_pass'] == md5($your_pass))
	{
		$pn_Cookies->set("this_article_pass".$article_info['sid'],"1",3600);
		Header("Location: ".LinkToGT($article_info['article_link'])."");
	}
}

if($nuke_configs['gtset'] == 1)
{
	
	$article_info['print_link'] = $article_info['article_link']."print/";
	$article_info['pdf_link'] = $article_info['article_link']."pdf/";
	$article_info['friend_link'] = $article_info['article_link']."friend/";
	$article_info['report_link'] = $article_info['article_link']."report/";
}
else
{
	$article_info['print_link'] = "index.php?modname=$module_name&file=article-seo&op=print&sid=".$article_info['sid']."";
	$article_info['pdf_link'] = "index.php?modname=$module_name&file=article-seo&op=pdf&sid=".$article_info['sid']."";
	$article_info['friend_link'] = "index.php?modname=$module_name&file=article-seo&op=friend&sid=".$article_info['sid']."";
	$article_info['report_link'] = "index.php?modname=$module_name&file=article-seo&op=report&sid=".$article_info['sid']."";
}

$meta_tags = array(
	"url" => $article_info['article_link'],
	"title" => $article_info['title'],
	"description" => str_replace(array("\r","\n","\t"), "", strip_tags($article_info['hometext'])),
	"keywords" => $article_info['tags'],
	"prev" => $article_info['prev_article_link'],
	"next" => $article_info['next_article_link'],
	"extra_meta_tags" => array()
);

switch($op)
{
	default:
		include("header.php");
		unset($meta_tags);
		$contents = '';
		$GLOBALS['block_global_contents'] = $article_info;
		$GLOBALS['block_global_contents']['post_id'] = $article_info['sid'];
		$GLOBALS['block_global_contents']['post_title'] = $article_info['title'];
		$GLOBALS['block_global_contents']['module_name'] = $module_name;
		$GLOBALS['block_global_contents']['allow_comments'] = $article_info['allow_comment'];
		$GLOBALS['block_global_contents']['db_table'] = ARTICLES_TABLE;
		$GLOBALS['block_global_contents']['db_id'] = 'sid';
		
		if(file_exists("themes/".$nuke_configs['ThemeSel']."/article_more.php"))
			include("themes/".$nuke_configs['ThemeSel']."/article_more.php");
		elseif(function_exists("article_more"))
			$contents .= article_more($article_info);
		else 
			$contents .= "";
	
		$html_output .= show_modules_boxes($module_name, array("bottom_full", "top_full","left","top_middle","bottom_middle","right"), $contents);
		
		unset($GLOBALS['block_global_contents']);
		unset($article_info);
		include ("footer.php");
	break;
	
	case"friend":
	case"report":
		report_friend_form(false, $op, $article_info['sid'], $article_info['title'], $module_name, '', '', $article_info['article_link'], '', '');
		die();
	break;
	
	case"pdf":
		pdf_generate($article_info['aid'], $article_info['tags'], $article_info['title'], $article_info['title'], $article_info['datetime'], $article_info['hometext']."<br /><br />".$article_info['bodytext'], $article_info['article_link']);
		die();
	break;
	
	case"print":
		$css	= array('includes/Ajax/jquery/bootstrap/css/bootstrap.min.css','includes/Ajax/jquery/bootstrap/css/bootstrap-rtl.css');
		$js		= array('includes/Ajax/jquery/bootstrap/js/bootstrap.min.js');
		$html_content = "<style>.article-header{width:100%;float:right;}.article-header span {width: calc(100% - 270px);float: right;}.article-header span:nth-child(2), .article-header span:nth-child(4) {color: #a7a9ac;}.article-header span:nth-child(3) {font-size: 20px;font-weight: bold;line-height: 35px;color: #333;padding: 9px 0 17px;}.article-header img{float:right;width:250px;margin-left:20px;}.p-nt {margin: 17px 0;float:right;width:100%;padding-top: 17px;border-top:1px dotted #ccc;}.p-nt p {margin-bottom: 19px;}</style>
		<div class=\"article-header\">
            ".(($article_info['article_image'] != "" && $article_info['article_image_width'] != 0 && $article_info['article_image_height'] != 0) ? "<img src=\"".$article_info['article_image']."\" width=\"".$article_info['article_image_width']."\" height=\"".$article_info['article_image_height']."\" alt=\"".$article_info['title']."\" title=\"".$article_info['title']."\" />":"<span></span>")."
            ".(($article_info['title_lead'] != '') ? "<span style=\"color:#ccc;\">".$article_info['title_lead']."</span>":"")."
            <span>".$article_info['title']."</span>
            <span>".$article_info['hometext']."</span>
        </div>
        <div class=\"p-nt\"><p class=\"rtejustify\">".$article_info['hometext']."<br />".$article_info['bodytext']."</div>";
		
		print_theme($pagetitle, $article_info['title'], $article_info['datetime'], $article_info['cattext_link'], $html_content, $article_info['article_link'], $css, $js);
		die();
	break;
}

?>