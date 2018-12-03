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

if (!defined('CONFIG_FUNCTIONS_FILE')) {
	die("You can't access this file directly...");
}

$articles_votetype = false;

function articleslink_all($all_vars = array())
{
	return articleslink($all_vars['sid'], $all_vars['title'], $all_vars['article_url'], $all_vars['time'], $all_vars['cat_link']);
}

function articleslink($sid, $title='', $article_url='', $time='', $cat_link='')
{
	global $nuke_configs, $db, $nuke_articles_categories_cacheData, $HijriCalendar;
	$sid = intval($sid);
	
	if($title == '' OR $article_url == '' OR $time == '' OR $cat_link == '' && empty($all_vars))
	{
		$row = $db->table(ARTICLES_TABLE)
						->where('sid', $sid)
						->first(['title', 'article_url', 'time', 'cat_link']);
		if(intval($db->count()) > 0)
		{
			$title = filter($row['title'], "nohtml");
			$article_url = filter($row['article_url'], "nohtml");
			$time = $row['time'];
			$cat_link = intval($row['cat_link']);
		}
		else
			return '';
	}
	
	$article_url = sanitize(str2url((($article_url != "") ? $article_url:$title)));
	$title = sanitize(str2url($title));

	$cat_link = intval($cat_link);
	$time = $time;
	$nutime = $time;
	$nudate = date("Y-m-d H:i:s", $time);
	
	$nudate1  = explode(" ", $nudate);
	$nudate1  = explode("-", $nudate1[0]);
	
	if($nuke_configs['datetype'] == 1)
	{
		$timelink = FormalDate2Hejri($nudate);
	}
	elseif($nuke_configs['datetype'] == 2)
	{
		$adateTimes = $HijriCalendar->GregorianToHijri($nutime);
		$timelink = $adateTimes[1].'-'.$adateTimes[0].'-'.$adateTimes[2];
	}
	else
	{
		$timelink = $nudate1[2]."-".$nudate1[1]."-".$nudate1[0];
	}
	
	$timelink = explode("-",$timelink);

	$article_url = str_replace(" ", "-", $article_url);

	$catname_link = sanitize(filter(implode("/", array_reverse(get_parent_names($cat_link, $nuke_articles_categories_cacheData, "parent_id", "catname_url"))), "nohtml"), array("/"));
	
	if($nuke_configs['gtset'] == "1")
	{
		$nuke_configs['pages_links'][$nuke_configs['userurl']] = (isset($nuke_configs['pages_links'][$nuke_configs['userurl']])) ? $nuke_configs['pages_links'][$nuke_configs['userurl']]:1;
		
		$article_link = "".str_replace(array('{ID}','{YEAR}','{MONTH}','{DAY}','{CATEGORY}','{PAGEURL}'), array($sid, $timelink[2], $timelink[1], $timelink[0], $catname_link, $article_url),$nuke_configs['pages_links'][$nuke_configs['userurl']]);
	}
	else
	{
		$article_link = "index.php?modname=Articles&file=article-seo&sid=$sid-$title";
	}
	return $article_link;
}

function is_valid_Articles_link($parsed_vars)
{
	global $db, $nuke_configs, $nuke_categories_cacheData;
	
	extract($parsed_vars);
	
	if(isset($file))
	{
		switch($file)
		{
			case"article-seo":
				$query_where = array();

				if($nuke_configs['gtset'] == 1)
				{
					if($article_url == '')
						return false;
					$query_params[':article_url'] = $article_url;
					$query_where[] = "article_url=:article_url";
					
				}
				else
				{
					$query_params[':sid'] = intval($sid);
					$query_where[] = "sid=:sid";
				}
					
				if(!is_admin())
					$query_where[] = "status = 'publish'";
					
				$query_where = implode(" AND ", $query_where);

				$result = $db->query("SELECT sid FROM ".ARTICLES_TABLE." where $query_where", $query_params);

				if(intval($result->count()) > 0)
				{
					$row = $result->results()[0];
					if(intval($row['sid'] > 0))
					{
						if(trim(articleslink(intval($row['sid']), filter($row['title'], "nohtml"), filter($row['article_url'], "nohtml"), filter($row['time'], "nohtml"), intval($row['cat_link'])).$urlop, "/")."/" != trim(rawurldecode($nuke_configs['REQUSERURL']), "/")."/")
						{
							return false;
						}
					}
					return true;
				}
				return false;
			break;
			
			case"archive":
				$page = isset($page) ? intval($page):0;
				$year = (isset($year) && strlen($year) == 4) ? intval($year):0;
				$month = (isset($month) && strlen($month) > 0 && strlen($month) < 3) ? intval($month):0;
				$month_l = isset($month_l) ? filter($month_l, "nohtml"):'';
				
				$month_names = ($nuke_configs['datetype'] == 1) ? "j_month_name":(($nuke_configs['datetype'] == 2) ? "h_month_name":"g_month_name");
				
				$month_l = str_replace(" ","-", $nuke_configs[$month_names][$month]);
				
				$link = "index.php?modname=Articles&file=archive".
				(($year != 0) ? "&year=$year":"").
				(($month != 0) ? "&month=$month":"").
				(($month_l != '') ? "&month_l=$month_l":"").
				(($page != 0) ? "&page=$page":"");
				
				if(trim(LinkToGT($link), "/")."/" == trim(rawurldecode(LinkToGT($nuke_configs['REQUSERURL'])), "/")."/")
					return true;
				return false;
			break;
		}	
	}
	elseif(isset($category))
	{
		if(!isset($nuke_categories_cacheData))
			$nuke_categories_cacheData = get_cache_file_contents('nuke_categories');
			
		$catid = get_category_id('Articles', $category, $nuke_categories_cacheData);	
		$catid = intval($catid);
		if($catid > 0)
			return true;
		return false;
	}
	
	return true;
}

function articles_feed($module_args=array())
{
	global $db, $nuke_configs, $nuke_articles_categories_cacheData, $noPermaLink;
	
	$feed_data = array();
	
	$query_set = array();
		
	$query_set['status'] = "status = 'publish'";
	
	if ($nuke_configs['multilingual'] == 1)
		$query_set['alanguage']	= "(alanguage='".$nuke_configs['currentlang']."' OR alanguage='')";

	if(isset($module_args['file']))
	{
		$module_file = filter($module_args['file'], "nohtml");
		
		switch($module_file)
		{
			case"archive":
				$year = isset($module_args['year']) ? intval($module_args['year']):0;
				$month = isset($module_args['month']) ? intval($module_args['month']):0;
				
				if($year != 0 && $month != 0)
				{
					$jnmonth				= $month +1;
					$jnyear					= $year;
					if ($jnmonth == 13)
					{
						$jnyear++;
						$jnmonth			= "01";
					}
					
					$month					= correct_date_number($month);
					$jnmonth				= correct_date_number($jnmonth);
					
					$currenttime			= to_mktime("$year/$month/1");
					$nexttime				= to_mktime("$jnyear/$jnmonth/1");
					
					$query_set['time']		= 'time BETWEEN '.$currenttime.' AND '.$nexttime.'';
				}
			break;
		}
	}
	
	$all_sub_cats = array();
	
	if(isset($module_args['category']))
	{
		$category = filter($module_args['category'], "nohtml");
		
		$nuke_categories_cacheData = get_cache_file_contents('nuke_categories');
		$catid = get_category_id('Articles', $category, $nuke_categories_cacheData);	
		$catid = intval($catid);

		$all_sub_cats = array_unique(get_sub_categories_id('Articles', $catid, $nuke_categories_cacheData, array($catid)));
		
		foreach($all_sub_cats as $sub_cat)
			$query_set['cat'][] = "FIND_IN_SET(?, cat)";
			
		$query_set['cat']= "(".implode(" OR ", $query_set['cat']).")";
	}
		
	$query_set					= implode(" AND ", array_filter($query_set));
	$query_set					= ($query_set != "") ? "WHERE $query_set":"";
	
	$result = $db->query("SELECT aid, sid, informant, title, alanguage, article_url, time, hometext, cat, cat_link, article_image FROM ".ARTICLES_TABLE." $query_set ORDER BY time DESC LIMIT 0,50", $all_sub_cats);

	if($result->count() > 0)
	{
		$rows = $result->results();
		$row_count = 0;
		foreach ($rows as $row)
		{
			$sid = intval($row['sid']);
			$aid = filter($row['aid'], "nohtml");
			$cat_link = intval($row['cat_link']);
			$cats = ($row['cat'] != '') ? explode(",",$row['cat']):"";
			$informant = ($row['informant'] != '') ? correct_text(filter($row['informant'], "nohtml")):$row['aid'];
			$title = correct_text(filter($row['title'], "nohtml"));
			$alanguage = correct_text(filter($row['alanguage'], "nohtml"));
			$time = $row['time'];
			$article_url = correct_text(filter($row['article_url']));
			$hometext = correct_text(stripslashes($row['hometext']));
			$article_image = get_article_image($sid, $row['article_image'], $hometext);
			$date = date('Y-m-d\TH:i:s+00:00',$time);			

			$link = articleslink($sid, $row['title'], $row['article_url'], $time, $cat_link);
			
			$feed_data[$row_count]['aid'] = $aid;
			$feed_data[$row_count]['sid'] = $sid;
			$feed_data[$row_count]['title'] = $title;
			$feed_data[$row_count]['link'] = LinkToGT($link);
			$feed_data[$row_count]['comments'] = LinkToGT($link."#comments");
			$feed_data[$row_count]['pubDate'] = $date;
			$feed_data[$row_count]['dc:creator'] = _LASTPOSTBY." ".$informant;
			$feed_data[$row_count]['dc:date'] = $date;
			$feed_data[$row_count]['noPermaLink'] = parse_GT_link($link)[3];
			if($article_image)
				$feed_data[$row_count]['media'] = LinkToGT($article_image);
			$feed_data[$row_count]['description'] = strip_tags($hometext);
			$feed_data[$row_count]['content'] = $hometext;
			$feed_data[$row_count]['language'] = $alanguage;
			
			if(!empty($cats))
				foreach($cats as $cat)
				{
					if(isset($nuke_articles_categories_cacheData[$cat]))
						$feed_data[$row_count]['category'][] = filter(category_lang_text($nuke_articles_categories_cacheData[$cat]['cattext']), "nohtml");
				}
			
			$row_count++;
		}
	}
	
	return $feed_data;
}

function get_article_image($sid = 0, $article_image = '', $hometext = '')
{
	preg_match_all('#<img(.*)src=["|\'](.*)["|\']#isU', stripslashes($hometext), $images_match);
	if($article_image == '')
		if(file_exists("files/Articles/".$sid.".jpg"))
			$article_image = "files/Articles/".$sid.".jpg";
		else
			if(isset($images_match[2][0]) && $images_match[2][0] != '')
				$article_image = $images_match[2][0];
			else
				if(file_exists("images/no_image.jpg"))
					$article_image = "images/no_image.jpg";

	return $article_image;	
}

if(!function_exists("articles_search"))
{
	function articles_search($row)
	{
		$contents = "";
		$contents .= "
		<div class=\"panel panel-info\">
			<div class=\"panel-heading\"> <span class=\"glyphicon glyphicon-list-alt\"></span><b> <a href=\"".$row['link']."\">".$row['title']."</a></b></div>
			<div class=\"panel-body\">
				".stripslashes($row['hometext'])."
			</div>
		</div>";
		return $contents;	
	}
}

$cache_systems['nuke_articles_categories'] = array(
	'name'			=> "_ARTICLES_CATEGORIES",
	"main_id"		=> 'catid',
	'table'			=> CATEGORIES_TABLE,
	'where'			=> "module = 'Articles'",
	'order'			=> 'ASC',
	'fetch_type'	=> \PDO::FETCH_ASSOC,
	'first_code'	=> '',
	'loop_code'		=> '$this_data_array[$this_main_id][\'catname_url\'] = sanitize(str2url($row[\'catname\']));',
	'end_code'		=> '',
	'auto_load'		=> true
);

/*
$cache_systems[''.$pn_prefix.'_articles_configs'] = array(
	'name' => _ARTICLES_CONFIG,
	"main_id" => 'id',
	'table' => ''.$pn_prefix.'_articles_configs',
	'order' => 'ASC',
	'fetch_type' => MYSQL_ASSOC,
	'first_code' => '',
	'loop_code' => '',
	'end_code' => '',
	'auto_load' => true
);*/

$alerts_messages['articles_comments'] = array(
	"prefix"	=> "cs",
	"by"		=> "cid",
	"table"		=> COMMENTS_TABLE,
	"where"		=> "module = 'Articles' AND status = '0'",
	"color"		=> "green",
	"text"		=> "_HAVE_N_NEW_COMMENTS",
);

$alerts_messages['articles_pending'] = array(
	"prefix"	=> "ps",
	"by"		=> "sid",
	"table"		=> ARTICLES_TABLE,
	"where"		=> "status = 'pending'",
	"color"		=> "green",
	"text"		=> "_HAVE_N_NEW_PENDING_ARTICLE",
);

$admin_top_menus['contents']['children'][] = array(
	"id" => 'articles', 
	"parent_id" => 'contents', 
	"title" => _ARTICLES, 
	"url" => "".$admin_file.".php?op=articles", 
	"icon" => "",
	"children" => array(
		array(
			"id" => 'articles_add', 
			"parent_id" => 'articles', 
			"title" => "_ADD_NEW_ARTICLE", 
			"url" => "".$admin_file.".php?op=article_admin", 
			"icon" => ""
		),
		array(
			"id" => 'articles_comments', 
			"parent_id" => 'articles', 
			"title" => "_ARTICLES_COMMENTS", 
			"url" => "".$admin_file.".php?op=comments&module=articles", 
			"icon" => ""
		),
		array(
			"id" => 'articles_categories', 
			"parent_id" => 'articles', 
			"title" => "_ARTICLES_CATEGORIES", 
			"url" => "".$admin_file.".php?op=categories&module=articles", 
			"icon" => ""
		)
	)
);
$admin_top_menus['categories']['children'][] = array(
	"id" => 'articles_cat', 
	"parent_id" => 'categories', 
	"title" => "_ARTICLES_CATEGORIES", 
	"url" => "".$admin_file.".php?op=categories&module_name=Articles", 
	"icon" => ""
);
$admin_top_menus['recives']['children'][] = array(
	"id" => 'articles_pending', 
	"parent_id" => 'recives', 
	"title" => "_PENDING_ARTICLES", 
	"url" => "".$admin_file.".php?op=articles&status=pending", 
	"icon" => ""
);

$nuke_configs_links_function['Articles'] = "articleslink";
$nuke_configs_comments_table['Articles'] = array('sid', ARTICLES_TABLE);
$nuke_configs_categories_link['Articles'] = "index.php?modname=Articles&category={CAT_NAME_URL}";
$nuke_configs_categories_delete['Articles']['data'] = array(
	"table"		=> ARTICLES_TABLE,
	"col_id"	=> "sid",
	"col_cats"	=> array("cat", "cat_link"),
	"where"		=> "",
	"recache"	=> "".$pn_prefix."_articles_categories"
);

$nuke_configs_search_data['Articles'] = array(
	"title"				=> "_ARTICLES",
	"table"				=> ARTICLES_TABLE,
	"have_comments"		=> true,
	"category_field"	=> "cat_link",
	"categories_field"	=> "cat",
	"time_field"		=> "time",
	"author_field"		=> "aid",
	"orderby"			=> "time",
	"where"				=> "status = 'publish'",
	"search_in_field"	=> array("title" => "_TITLE", "hometext" => "_HOMETEXT", "bodytext" => "_BODYTEXT", "tags" => "_KEYWORDS", "article_url" => "_ARTICLE_URL"),
	"fetch_fields"		=> array("sid", "title", "hometext", "cat_link", "cat", "time", "article_url"),
	"more_link"			=> "articleslink_all",
	"search_template"	=> "articles_search"
);

$nuke_configs_statistics_data['Articles'] = array(
	"total_articles" => array(
		"title"				=> "_ARTICLES",
		"table"				=> ARTICLES_TABLE,
		"count"				=> "sid",
		"as"				=> "total_articles",
		"where"				=> "status = 'publish'",
	),
	"pending_articles" => array(
		"title"				=> "_PENDING_ARTICLES",
		"table"				=> ARTICLES_TABLE,
		"count"				=> "sid",
		"as"				=> "pending_articles",
		"where"				=> "status = 'publish' AND aid != informant AND informant != ''",
	)
);


$nuke_rss_codes['Articles'] = '';

?>