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
$module_name = basename(dirname(__FILE__));

if(!defined("INDEX_FILE"))
	define('INDEX_FILE', is_index_file($module_name));// to define INDEX_FILE status

if(!function_exists("article_index"))
{
	function article_index($article_info)
	{
		global $ShowTopic,$tipath,$nuke_configs;
		
		$article_info['comments'] = ($article_info['comments']==0) ? "0":$article_info['comments'];
		
		$contents = "
			<!--Articles-->

			<div class=\"Articles\">
			  <div class=\"ArticlesTitle\">
				<div class=\"ArticlesRating\">
					".$article_info['rating_box']."
				</div>
				<div>
				  <h2 class=\"ArticlesTitleText\"><a href=\"".$article_info['article_link']."\" rel=\"bookmark\" title=\"".$article_info['title']."\">
					".$article_info['title']."
					</a></h2>
				</div>
			  </div>
			  <div class=\"ArticlesBody\">
				<div class=\"ArticlesBodyText\">
				  <p>
					".$article_info['hometext']."
				  </p>
				</div>
			  </div>
			  <div class=\"ArticlesFoot\"><a class=\"MoreArticles\" href=\"".$article_info['article_link']."\" title=\"".$article_info['title']."\">
				"._MORE."
				</a>
				<div class=\"ArticlesFootText\">
				  ".$article_info['datetime']."
				  |
				  ".$article_info['comments']."
				  "._COMMENTS."
				  |
				  "._VISITS."
				  [
				  ".$article_info['counter']."
				  ]</div>
			  </div>
			</div>
			<br />";
		return $contents;
	}
}

function articles_home($category='', $tags='', $orderby = '')
{
	global $db, $userinfo, $page, $module_name, $visitor_ip, $nuke_articles_categories_cacheData, $nuke_configs, $articles_votetype, $nuke_modules_cacheData, $nuke_authors_cacheData;
	$link_to = "index.php?modname=$module_name";

	$nuke_modules_cacheData_by_title = phpnuke_array_change_key($nuke_modules_cacheData, "mid", "title");
	
	$nuke_categories_cacheData = get_cache_file_contents('nuke_categories');
	$catid = get_category_id($module_name, $category, $nuke_categories_cacheData);	
	
	$catid = intval($catid);
	if($catid < 0)
		header("location: ".LinkToGT("index.php")."");

	if ($nuke_configs['multilingual'] == 1)
	{
		$module_titles = $lang_titles = ($nuke_modules_cacheData_by_title[$module_name]['lang_titles'] != "") ? phpnuke_unserialize(stripslashes($nuke_modules_cacheData_by_title[$module_name]['lang_titles'])):"";
		
		$module_title = $module_titles[$nuke_configs['currentlang']];
	}
	else
		$module_title = $nuke_modules_cacheData_by_title[$module_name]['title'];
	
	$db->table(ARTICLES_TABLE)
		->where('status', 'future')
		->where('time', '<',  _NOWTIME)
		->update([
			'status' => 'publish'
		]);
		
	
	$votetype = ($articles_votetype) ? $articles_votetype:$nuke_configs['votetype'];
	
	switch($orderby)
	{
		case"most-visit":
			$orderby = 'counter';
		break;
		case"most-rate":
			$orderby = 'score';
		break;
		case"most-comment":
			$orderby = 'comments';
		break;
		default:
			$orderby = 'time';
		break;
	}
	
	$query_set = array();
	$query_params = array();
	
	$query_params[':visitor_ip'] = $visitor_ip;
	
	if(!is_admin())
		$query_set['status'] = "s.status = 'publish'";
		
	$query_set['alanguage'] = "";
	$query_set['ihome'] = "";
	$query_set['cat'] = "";
	$query_set['post_type'] = "(s.post_type = 'article' OR s.post_type = '')";
	
	if ($nuke_configs['multilingual'] == 1)
	{
		$query_set['alanguage'] = "(s.alanguage='".$nuke_configs['currentlang']."' OR s.alanguage='')";
		
		$query_set['ihome'] = "s.ihome = '1'";
	}
	
	$all_sub_cats = array();

	if ($catid > 0)
	{
		unset($query_set['ihome']);

		$all_sub_cats = array_unique(get_sub_categories_id($module_name, $catid, $nuke_categories_cacheData, array($catid)));

		$c=1;
		foreach($all_sub_cats as $sub_cat)
		{
			$query_set['cat'][] = "FIND_IN_SET(:cat_$c, cat)";
			$query_params[":cat_$c"] = $sub_cat;
			$c++;
		}
		$query_set['cat']= "(".implode(" OR ", $query_set['cat']).")";
	}
			
    if (isset($userinfo['artcle_num']) AND (isset($nuke_configs['user_pagination']) && $nuke_configs['user_pagination'] == 1))
		$artcle_num = intval($userinfo['artcle_num']);
	else
		$artcle_num = intval($nuke_configs['home_pagination']);
	
	$contents = "";
	
	$tags2 = $tags3 = "";
	if($tags != "")
	{
		$tags	= str_replace(array("_","-")," ",$tags);
		$tags	= check_html($tags);
		$tags_arr	= adv_filter($tags, array('sanitize_string'),array('required'));
		if($tags_arr[0] != 'error')
		{
			$tags = $tags_arr[1];
			$tags	= htmlentities(trim($tags), ENT_QUOTES,"utf-8");
			$tags2	= str_replace(_FAANDAR1,_FAANDAR11,$tags);
			$tags2	= str_replace(_FAANDAR2,_FAANDAR22,$tags2);
			$tags3	= str_replace(_FAANDAR11, _FAANDAR1,$tags);
			$tags3	= str_replace(_FAANDAR22, _FAANDAR2,$tags3);
			
			$tagresult = $db->table(TAGS_TABLE)
				->Where('tag', $tags)
				->orWhere('tag', $tags2)
				->orWhere('tag', $tags3)
				->first(['tag_id']);

			if($tagresult->count() > 0)
			{
				$tag_id = intval($tagresult['tag_id']);
				$db->table(TAGS_TABLE)
					->where('tag_id', $tag_id)
					->update([
						'visits' => true
					]);
				
			}
			$query_set['tags'] = "(tags LIKE :tags OR tags LIKE :tags2 OR tags LIKE :tags3)";
			
			$query_params[":tags"] = "%$tags%";
			$query_params[":tags2"] = "%$tags2%";
			$query_params[":tags3"] = "%$tags3%";
			
			$link_to .= "&tags=$tags";
			
			$contents .= OpenTable();
			$contents .= "<div align=\"center\"><h1>".$tags."</h1></div>";
			$contents .= CloseTable();
		}
	}
	
	if ($catid > 0)
	{
		$numrows_a = (isset($nuke_articles_categories_cacheData[$catid]) && !empty($nuke_articles_categories_cacheData[$catid])) ? 1:0;
		$parent_id = intval($nuke_articles_categories_cacheData[$catid]['parent_id']);
		
		if ($numrows_a == 0)
		{
			$contents .= OpenTable();
			$contents .= "<div class=\"text-center\"><font class=\"title\">".$nuke_configs['sitename']."</font><br><br>"._NOINFO4TOPIC."<br><br>[ <a href=\"".LinkToGT("index.php?modname=$module_name")."\">"._GOTONEWSINDEX."</a> | <a href=\"".LinkToGT("index.php?modname=Articles&file=categories")."\">"._SELECTNEWTOPIC."</a> ]</div>";
			$contents .= CloseTable();
		}
		else
		{
			$contents .= OpenTable();
			$cat_title = sanitize(filter(implode("/", array_reverse(get_parent_names($catid, $nuke_articles_categories_cacheData, "parent_id", "catname_url"))), "nohtml"), array("/"));
			
			$attrs = array(
				"title" => "{CAT_TEXT}",
				"id" => "category-{CATID}"
			);
			$cats_link_deep = implode("/", category_link($module_name, $cat_title, $attrs));
			
			$contents .= "<div class=\"text-center\"><font class=\"title\">".$nuke_configs['sitename'].": $cats_link_deep</font><br><br>
			<form action=\"index.php?modname=Search\" method=\"post\">
			<input type=\"hidden\" name=\"cat\" value=\"$catid\">
			"._SEARCHONTOPIC.": <input type=\"name\" name=\"query\" size=\"30\">&nbsp;&nbsp;
			<input type=\"submit\" value=\""._SEARCH."\">
			</form>
			[ <a href=\"".LinkToGT("index.php")."\">"._GOTOHOME."</a> | <a href=\"".LinkToGT("index.php?modname=Articles&file=categories")."\">"._SELECTNEWTOPIC."</a> ]</div><br />";
			$sub_cats_contents_num=0;
			$j=1;
			$sub_cats_contents = "<table width=\"100%\" border=\"0\"><tr><td align=\"center\"><b>"._SUB_CATS."</b><br /><br /></td></tr>";
			foreach($nuke_articles_categories_cacheData as $key => $val)
			{
				if($val['parent_id'] == $catid)
				{
					$cat_link = sanitize(filter(implode("/", array_reverse(get_parent_names($key, $nuke_articles_categories_cacheData, "parent_id", "catname_url"))), "nohtml"), array("/"));
					
					if ($j==1) $sub_cats_contents .= "<tr>";
					$sub_cats_contents .= "<td width=\"100\"><a href=\"".LinkToGT("index.php?modname=Articles&category=$cat_link")."\">".$val['cattext']."</a></td>";
					if ($j==5)
					{
						$j=0;
						$sub_cats_contents .= "</tr>";
					}
					$j++;
					$sub_cats_contents_num++;
				}
			}
			$sub_cats_contents .= "</table>";
			if($sub_cats_contents_num > 0)
				$contents .= $sub_cats_contents; 
			$contents .= CloseTable();
		}
		$link_to .= "&category=$cat_title";
	}
	
	$total_rows	= 0;
	
	// AND (sc.rating_ip = '$ip' OR sc.username = '".$userinfo['username']."') was removed for all
	$user_id = (isset($userinfo['user_id']) && isset($userinfo['is_registered']) && $userinfo['is_registered'] == 1) ? intval($userinfo['user_id']):0;
	
	$query_params[':user_id'] = $user_id;
	
	$entries_per_page						= intval($artcle_num);
	$current_page							= (empty($page)) ? 1 : $page;
	$start_at								= intval(($current_page * $entries_per_page) - $entries_per_page);
	$query_set								= implode(" AND ", array_filter($query_set));
	$query_set								= ($query_set != "") ? "WHERE $query_set":"";
	
	$vote_where = ($user_id != 0) ? "user_id = :user_id":"rating_ip = :visitor_ip";
	$votes_query							= "(SELECT id FROM ".SCORES_TABLE." WHERE ($vote_where) AND post_id = s.sid AND db_table = 'articles' ORDER BY id DESC LIMIT 1) as rated_id";
	
	if($votetype == 1)
	{
		$votes_query						.= ", SUM(IF(sc.score > 0, sc.score, -sc.score)) AS score, COUNT(sc.id) AS ratings, 0 as likes, 0 as dislikes";
		$newvotetype = 1;
	}
	else
	{
		$votes_query						.= ", SUM(IF(sc.score >= 1, 1, 0)) AS likes, SUM(IF(sc.score < 0, 1, 0)) AS dislikes, COUNT(sc.id) AS ratings, 0 as score";
		$newvotetype = "2,3";
	}
		
    $results								= $db->query("
	SELECT s.*, 
	(SELECT COUNT(sid) FROM ".ARTICLES_TABLE." ".str_replace("s.","", $query_set).") as total_rows, 
	$votes_query 
	FROM ".ARTICLES_TABLE." AS s 
	LEFT JOIN ".SCORES_TABLE." AS sc ON sc.post_id = s.sid AND sc.votetype IN ($newvotetype) AND sc.db_table = 'articles' 
	$query_set 
	GROUP BY s.sid 
	ORDER BY s.$orderby DESC, s.sid DESC LIMIT $start_at, $entries_per_page", $query_params);

	if(!empty($results))
	{
		$rows = $results->results();
		foreach ($rows as $row)
		{
			$total_rows							= intval($row['total_rows']);
			$article_info['sid']				= intval($row['sid']);
			$article_info['aid']				= filter($row['aid'], "nohtml");
			$article_info['aid_url']			= (isset($nuke_authors_cacheData[$row['aid']]['url'])) ? filter($nuke_authors_cacheData[$row['aid']]['url'], "nohtml"):$nuke_configs['nukeurl'];
			$article_info['title']				= filter($row['title'], "nohtml");
			$article_info['time']				= $row['time'];
			$article_info['hometext']			= text_rel2abs(stripslashes($row['hometext']));
			//$hometext							= codereplace($hometext,$sid);
			$bodytext							= stripslashes($row['bodytext']);
			$article_info['bodytext']			= codereplace(text_rel2abs($bodytext),$article_info['sid']);
			$article_info['comments']			= intval($row['comments']);
			$article_info['counter']			= intval($row['counter']);
			$article_info['cats']				= $row['cat'];
			$article_info['cat_link']			= $row['cat_link'];
			$article_info['article_url']		= filter($row['article_url']);
			$article_info['informant']			= filter($row['informant'], "nohtml");
			$article_info['tags']				= filter($row['tags']);
			$article_info['allow_comment']		= intval($row['allow_comment']);
			$article_info['score']				= intval($row['score']);
			$article_info['ratings']			= intval($row['ratings']);
			$article_info['datetime']			= nuketimes($article_info['time'], false, false, false);
			$article_info['likes']				= intval($row['likes']);
			$article_info['dislikes']			= intval($row['dislikes']);
			$article_info['rated_id']			= intval($row['rated_id']);
			$article_info['article_image']		= $row['article_image'];
			$article_info['title_lead']			= filter($row['title_lead'], "nohtml");
			$article_info['title_color']		= filter($row['title_color'], "nohtml");
			$article_info['micro_data']			= ($row['micro_data'] != "") ? phpnuke_unserialize(stripslashes($row['micro_data'])):array();
			
			$disabled_rating = false;
			
			if (isset($_COOKIE['articles_ratecookie']))
			{
				$rcookie						= base64_decode($_COOKIE['articles_ratecookie']);
				$rcookie						= addslashes($rcookie);
				$r_cookie						= explode(",", $rcookie);
				if(in_array($article_info['sid'], $r_cookie))
				{
					$disabled_rating			= true;
				}
			}
			
			if($article_info['rated_id'] > 0)
				$disabled_rating				= true;
			
			$article_info['rating_box']			= rating_load($article_info['score'], $article_info['ratings'], $article_info['likes'], $article_info['dislikes'], 'articles', "sid", $article_info['sid'], $disabled_rating, $votetype);
			$r_options							= "";
			$cats = ($article_info['cats'] != "") ? explode(",", $article_info['cats']):array();
			if(!empty($cats))
			{
				foreach($cats as $cat)
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
			
			if(file_exists("themes/".$nuke_configs['ThemeSel']."/article_index.php"))
				include("themes/".$nuke_configs['ThemeSel']."/article_index.php");
			elseif(function_exists("article_index"))
				$contents .= article_index($article_info);
			else
				$contents .= "";
			
			unset($article_info);
		}
	}

	if (intval($page) != 0)
	{
		$meta_url = $link_to."&page=".intval($page)."";
	}
	
	$article_info['next_link'] = '';
	$article_info['prev_link'] = '';
		
	if($entries_per_page < $total_rows)
	{
		$contents .= "<div id=\"pagination\" class=\"pagination\">";
		$contents .= clean_pagination($total_rows, $entries_per_page, $current_page, $link_to);
		$contents .= "</div>";
	}
	
	$lastpage = ceil($total_rows/$entries_per_page);
	
	if($page < $lastpage && intval($page) != 0)
		$article_info['next_link'] = LinkToGT($link_to."&page=".intval($page+1)."");
	
	if($page > 1 && $entries_per_page < $total_rows)
		$article_info['prev_link'] = LinkToGT($link_to."&page=".intval($page-1)."");
	
	$meta_tags = array(
		"url"				=> $link_to,
		"title"				=> $module_title,
		"description"		=> '',
		"keywords"			=> '',
		"prev"				=> $article_info['prev_link'],
		"next"				=> $article_info['next_link'],
		"extra_meta_tags"	=> ($catid > 0) ? array(
			"<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Atom - $cat_title\" href=\"".LinkToGT("index.php?modname=Feed&module_link=".$nuke_configs['REQUSERURL']."")."\" />\n"
		):"",
	);
	
	$boxes_contents = show_modules_boxes($module_name, array("bottom_full", "top_full","left","top_middle","bottom_middle","right"), $contents);

	include("header.php");
	$html_output .= $boxes_contents;
	include("footer.php");
}

if (!(isset($category))) { $category = 0; }
if (!(isset($op))) { $op = ""; }
if (!(isset($tags))) { $tags = ""; }
if (!(isset($orderby))) { $orderby = "DESC"; }

switch ($op)
{
	default:
	articles_home($category, $tags, $orderby);
	break;
}

?>