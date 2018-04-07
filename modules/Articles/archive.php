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

function select_month($in = 0)
{
	global $db, $nuke_configs, $module_name, $HijriCalendar;
	
	$contents = "";
	$contents .= "<div class=\"text-center\"><font class=\"content\">"._SELECTMONTH2VIEW."</font><br><br></div>";
	
	$result = $db->table(ARTICLES_TABLE)
					->where('post_type', 'article')
					->order_by(['time' => 'DESC'])
					->select(['time']);
						
	$contents .= "<ul>";
	$thismonth = "";//gregorian date
	$thisjmonth = "";//jalali date
	$thishmonth = "";//hijri date

	if(!empty($result))
	{
		foreach($result as $row)
		{
			$time = $row['time'];
			if($nuke_configs['datetype'] == 1)
			{
				$j_datetime = array(date("Y", $time), date("m", $time), date("d", $time));
				$jalalidate= gregorian_to_jalali($j_datetime[0],$j_datetime[1],$j_datetime[2]);
				if ($jalalidate[1] != $thisjmonth)
				{
					$month = $nuke_configs['j_month_name'][$jalalidate[1]];
					$month2 = str_replace(" ","-",$month);
					$contents .= "<li><a href=\"".LinkToGT("index.php?modname=Articles&file=archive&op=show_archive&year=$jalalidate[0]&month=$jalalidate[1]&month_l=$month2")."\">$month, $jalalidate[0]</a>";
					$thisjmonth = $jalalidate[1];
				}

			}
			elseif($nuke_configs['datetype'] == 2)
			{
				$dateTimes = $HijriCalendar->GregorianToHijri($time);
				$hgetdate = $dateTimes[0]-1;
				if ($dateTimes[0] != $thishmonth)
				{
					$month = $nuke_configs['A_month_name'][$hgetdate];
					$month2 = str_replace(" ","-",$month);
					$contents .= "<li><a href=\"".LinkToGT("index.php?modname=Articles&file=archive&op=show_archive&year=$dateTimes[2]&month=$dateTimes[0]&month_l=$month2")."\">$month, $dateTimes[2]</a>";
					$thishmonth = $dateTimes[0];
				}	
			}
			else
			{
				$dateTimes_year = date("Y",$time);
				$dateTimes_month = date("m",$time);
				$dateTimes_month = intval($dateTimes_month);
				if ($dateTimes_month != $thismonth)
				{
					$month = $nuke_configs['g_month_name'][$dateTimes_month];
					$month2 = str_replace(" ","-",$month);
					$contents .= "<li><a href=\"".LinkToGT("index.php?modname=Articles&file=archive&op=show_archive&&year=$dateTimes_year&month=$dateTimes_month&month_l=$month2")."\">$month, $dateTimes_year</a>";
					$thismonth = $dateTimes_month;
				}	
			}	
		}
	}
	$contents .= "</ul>";

	if(intval($in) == 0)
	{
		$meta_tags = array(
			"url" => LinkToGT("index.php?modname=$module_name&file=archive"),
			"title" => _STORIESARCHIVE,
			"description" => '',
			"extra_meta_tags" => array()
		);
		
		include("header.php");
		$output = '';
		$output .= title(_STORIESARCHIVE);
		$output .= OpenTable();
		$contents .= "<br><br><div class=\"text-center\">[ <a href=\"".LinkToGT("index.php?modname=$module_name&file=archive&op=show_archive")."\">"._SHOWALLSTORIES."</a> ]</div>";
		$output .= $contents;
		$output .= CloseTable();
		$html_output .= show_modules_boxes($module_name, array("bottom_full", "top_full","left","top_middle","bottom_middle","right"), $output);
		include("footer.php");
		
	}
	return $contents;
}

function show_archive($year, $month, $month_l, $mode)
{
	global $userinfo, $db, $user, $page, $module_name, $nuke_configs;
	
	$contents = '';

	$page = isset($page) ? intval($page):0;
	$year = (isset($year) && strlen($year) == 4) ? intval($year):0;
	$month = (isset($month) && strlen($month) > 0 && strlen($month) < 3) ? intval($month):0;
	$month_l = isset($month_l) ? str_replace("-", " ", filter($month_l, "nohtml")):'';
	
	$month_names = ($nuke_configs['datetype'] == 1) ? "j_month_name":(($nuke_configs['datetype'] == 2) ? "h_month_name":"g_month_name");
	
	$month_l = str_replace(" ","-", $nuke_configs[$month_names][$month]);
	
	$link = "index.php?modname=Articles&file=archive&op=show_archive".
	(($year != 0) ? "&year=$year":"").
	(($month != 0) ? "&month=$month":"").
	(($month_l != '') ? "&month_l=$month_l":"").
	(($page != 0) ? "&page=$page":"");
	
	if(trim(LinkToGT($link), "/")."/" != trim(rawurldecode(LinkToGT($nuke_configs['REQUSERURL'])), "/")."/")
		die_404();
		
	$where_between = array();
	
	if($mode != 'all' && $year == 0 && $month == 0)
		$contents .= select_month(1);
	
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
		
		$query_set['time']		= "s.time BETWEEN '$currenttime' AND '$nexttime'";
		$where_between = array($currenttime, $nexttime);
	}
	
	$entries_per_page			= 20;
	$current_page				= (empty($page)) ? 1 : $page;
	$start_at					= ($current_page * $entries_per_page) - $entries_per_page;
	$link_to					= ($year != 0 && $month != 0 && $month_l != '') ? "index.php?modname=$module_name&file=archive&op=show_archive&year=$year&month=$month&month_l=$month_l":"index.php?modname=$module_name&file=archive&op=show_archive";
	
	$total_rows = $db->table(ARTICLES_TABLE)
					->whereBetween('time', $where_between)
					->select(['sid'])
					->count();
					
	if(!is_admin())
		$query_set['status']	= "s.status = 'publish'";
		
	$query_set['post_type'] = "(s.post_type = 'article' OR s.post_type = '')";
	$query_set['alanguage']		= "";
	
	if ($nuke_configs['multilingual'] == 1)
		$query_set['alanguage']	= "(s.alanguage='".$nuke_configs['currentlang']."' OR s.alanguage='')";
	
	$query_set					= implode(" AND ", array_filter($query_set));
	$query_set					= ($query_set != "") ? "WHERE $query_set":"";
	
    $result						= $db->query("
	SELECT s.sid, s.title, s.time, s.article_url, s.comments, s.counter, s.alanguage, s.cat_link, s.score, s.ratings, s.status, 
	(SELECT COUNT(s2.sid) FROM ".ARTICLES_TABLE." as s2 ".str_replace("s.","s2.", $query_set).") as total_rows 
	FROM ".ARTICLES_TABLE." AS s 
	$query_set 
	GROUP BY s.sid 
	ORDER BY s.time DESC, s.sid DESC LIMIT $start_at, $entries_per_page");
	
	$contents					.="
	<div class=\"table-responsive\">
	<table border=\"0\" width=\"100%\" class=\"table-striped table-hover table-condensed\">
		<tr>
			<th align=\"right\"><b>"._ARTICLES."</b></th>
			<th align=\"center\"><b>"._COMMENTS."</b></th>
			<th align=\"center\"><b>"._READS."</b></th>
			<th align=\"center\"><b>"._USCORE."</b></th>
			<th align=\"center\"><b>"._DATE."</b></th>
			<th align=\"center\"><b>"._OPERATION."</b></th>
		</tr>";
	if(!empty($result))
	{
		foreach ($result as $row)
		{
			$total_rows				= intval($row['total_rows']);
			$sid					= intval($row['sid']);
			$title					= filter($row['title'], "nohtml");
			$time					= $row['time'];
			$article_url			= filter($row['article_url'], "nohtml");
			$comments				= intval($row['comments']);
			$counter				= intval($row['counter']);
			$alanguage				= $row['alanguage'];
			$cat_link				= intval($row['cat_link']);
			$score					= intval($row['score']);
			$ratings				= intval($row['ratings']);
			$article_link			= LinkToGT(articleslink($sid, $title, $article_url, $time, $cat_link));
			$time					= nuketimes($time);

			$this_status			= filter($row['status'], "nohtml");	

			switch($this_status)
			{
				case"future":
					$this_post_status = " ("._PUBLISH_IN_FUTURE.")";
				break;
				case"draft":
					$this_post_status = " ("._DRAFT.")";
				break;
				case"pending":
					$this_post_status = " ("._PENDING_POST.")";
				break;
				default:
					$this_post_status = "";
				break;
			}
					
			if($nuke_configs['gtset'] == 1)
			{
				
				$print_link			= $article_link."print/";
				$pdf_link			= $article_link."pdf/";
				$friend_link		= $article_link."friend/";
			}
			else
			{
				$print_link			= "index.php?modname=$module_name&file=print&sid=$sid";
				$pdf_link			= "index.php?modname=$module_name&file=pdf&sid=$sid";
				$friend_link		= "index.php?modname=$module_name&file=friend&sid=$sid";
			}
			
			$actions				= "<a href=\"$print_link\"><img src=\"".$nuke_configs['nukecdnurl']."images/print.gif\" border=0 alt=\""._PRINT."\" title=\""._PRINT."\" width=\"16\" height=\"11\"></a>&nbsp;";
			$actions				.= "<a href=\"$pdf_link\"><img src=\"".$nuke_configs['nukecdnurl']."images/pdf.gif\" border=0 alt=\""._PDFFILE."\" title=\""._PDFFILE."\" width=\"16\" height=\"11\"></a>&nbsp;";
			if(is_user())
			{
				$actions			.= "<a href=\"$friend_link\" class=\"thickbox\"><img src=\"".$nuke_configs['nukecdnurl']."images/friend.gif\" border=0 alt=\""._SEND_POST_TO_FRIEND."\" title=\""._SEND_POST_TO_FRIEND."\" width=\"16\" height=\"11\"></a>";
			}
			if ($score != 0)
			{
				$rated				= substr($score / $ratings, 0, 4);
			}
			else
			{
				$rated = 0;
			}
			$title					= "<a href=\"$article_link\">$title</a>";
			if ($nuke_configs['multilingual'] == 1)
			{
				if (empty($alanguage))
				{
					$alanguage		= $nuke_configs['language'];
				}
				$alt_language		= ucfirst($alanguage);
				$lang_img			= "<img src=\"".$nuke_configs['nukecdnurl']."images/language/flag-$alanguage.png\" border=\"0\" hspace=\"2\" alt=\"$alt_language\" title=\"$alt_language\">";
			}
			else
			{
				$lang_img			= "<strong><big><b>&middot;</b></big></strong>";
			}
			$contents .="<tr>
				<td align=\"right\">$lang_img $title$this_post_status</td>
				<td align=\"center\">$comments</td>
				<td align=\"center\">$counter</td>
				<td align=\"center\">$rated</td>
				<td align=\"center\">$time</td>
				<td align=\"center\">$actions</td>
			</tr>";
		}
	}
	$contents .="
		<tr>
			<td valign=\"top\" align=\"center\" colspan=\"6\">
			<div id=\"pagination\" class=\"pagination\">";
			$contents			.= clean_pagination($total_rows, $entries_per_page, $current_page, $link_to);
			$contents			.="</div>
			</td>
		</tr>
	</table>
	</div>
	<br><br><hr size=\"1\" noshade>";
	$contents					.= select_month(1);
	$contents					.="<div align=\"center\">
	[ <a href=\"".LinkToGT("index.php?modname=$module_name&file=archive")."\">"._ARCHIVESINDEX."</a>".(($mode !== 'all') ? " | <a href=\"".LinkToGT("index.php?modname=$module_name&file=archive&op=show_archive")."\">"._SHOWALLSTORIES."</a>":"")." ]</div>";
	
	if (intval($page) != 0)
	{
		$meta_url				= $link_to."&page=".intval($page)."";
	}
	
	$next_link					= '';
	$prev_link					= '';
	
	$lastpage					= ceil($total_rows/$entries_per_page);
	
	if($page < $lastpage && $page != 0)
		$next_link				= LinkToGT($link_to."&page=".intval($page+1)."");
	
	if($page > 1 && $entries_per_page < $total_rows)
		$prev_link				= LinkToGT($link_to."&page=".intval($page-1)."");

	$meta_tags = array(
		"url" 					=> $link_to,
		"title" 				=> "آرشیو مطالب".(($month_l != '') ? " - $month_l $year":""),
		"description" 			=> '',
		"keywords" 				=> '',
		"prev" 					=> $prev_link,
		"next" 					=> $next_link,
		"extra_meta_tags" 		=> array(
			"<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Atom - "._STORIESARCHIVE." - ".(($month_l != '') ? " - $month_l $year":"")."\" href=\"".LinkToGT("index.php?modname=Feed&module_link=".$nuke_configs['REQUSERURL']."")."\" />\n"
		)
	);
	
	include("header.php");
	$output = '';
	$output .= title($nuke_configs['sitename']." : "._STORIESARCHIVE);
	
	if($month_l != '')
		$output .= title("$month_l $year");
		
	$output .= OpenTable();
	$output .= $contents;
	$output .= CloseTable();
	$html_output .= show_modules_boxes($module_name, array("bottom_full", "top_full","left","top_middle","bottom_middle","right"), $output);
	include("footer.php");
}

$op 							= isset($op) ? $op : "";
$in								= (isset($in) && intval($in) > 0) ? intval($in) : 0;
$year							= (isset($year) && intval($year) > 0) ? intval($year) : 0;
$month							= (isset($month) && intval($month) > 0) ? intval($month) : 0;
$month_l						= isset($month_l)? filter($month_l, "nohtml") : "";
$mode							= isset($mode)? filter($mode, "nohtml") : "";

if($year == 0 && $month == 0 && $mode != 'all')
	$op = "select_month";

switch($op)
{
	case "show_archive":
		show_archive($year, $month, $month_l, $mode);
	break;
	default:
		select_month(0);
	break;
}
?>