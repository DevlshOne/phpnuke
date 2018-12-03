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

/* Block to fit perfectly in the center of the site, remember that not all
   blocks looks good on Center, just try and see yourself what fits your needs */

if ( !defined('BLOCK_FILE') ) {
    Header("Location: ../index.php");
    die();
}

global $nuke_configs, $db, $block_global_contents, $users_system;

$params = array();
$nuke_comments_configs = ($nuke_configs['comments'] != '') ? phpnuke_unserialize(stripslashes($nuke_configs['comments'])):array();
	
	$where = array();
	$where_values = array();
	$select2 = "";
	$post_id = (isset($post_id)) ? intval($post_id):0;
	
	$where[] = "c.status =1";
	
	$where = array_filter($where);
	$where = implode(" AND ", $where);
	$where_values = array_filter($where_values, function($v){
		return $v !== false && !is_null($v) && ($v != '' || $v == '0');
	});
	
	$groups_query = '';
	if($nuke_configs['have_forum'] == 1)
	{
		$groups_query = "(SELECT g.group_colour FROM ".$users_system->users_table." as u LEFT JOIN ".$users_system->groups_table." AS g ON g.group_id = u.group_id WHERE u.username = c.username) as user_colour,	
		(SELECT g2.group_name FROM ".$users_system->users_table." as u2 LEFT JOIN ".$users_system->groups_table." AS g2 ON g2.group_id = u2.group_id WHERE u2.username = c.username) as group_name,";
	}
	$result = $db->query("
		SELECT c.*,
		(SELECT COUNT(c4.cid) FROM ".COMMENTS_TABLE." AS c4 WHERE ".str_replace("c.","c4.", $where)." AND c4.cid ".(($nuke_comments_configs['order_by'] == 1) ? ">":"<")."= IF(c.main_parent =0, c.cid,c.main_parent) AND c4.pid ='0') as position,
		$groups_query
		FROM ".COMMENTS_TABLE." AS c 
		WHERE $where 
		ORDER BY c.cid DESC LIMIT 0,5
	", $where_values);

	$content = '<ul class="list-group">';
	if($db->count() > 0)
	{
		foreach($result as $row)
		{
			$position = intval($row['position']);
			$cid = intval($row['cid']);
			$pid = intval($row['pid']);
			$user_colour = filter($row['user_colour'], "nohtml");
			$group_name = filter($row['group_name'], "nohtml");
			$module = filter($row['module'], "nohtml");
			$post_title = filter($row['post_title'], "nohtml");
			$post_id = intval($row['post_id']);
			$date = nuketimes($row['date'], true, true, true);
			$username = filter($row['username'], "nohtml");
			$name = filter($row['name'], "nohtml");
			$email = filter($row['email'], "nohtml");
			$url = filter($row['url'], "nohtml");
			$ip = filter($row['ip'], "nohtml");
			$shorted = (mb_strlen(strip_tags(stripslashes($row['comment']))) !== mb_strlen(mb_word_wrap(strip_tags(stripslashes($row['comment'])), 200, null, false))) ? true:false;
			$comment = smilies_parse(mb_word_wrap(strip_tags(stripslashes($row['comment'])), 200));
			$ip_info = "http://whatismyipaddress.com/ip/$ip";			
			$username_link = ($username == $ip) ? $ip_info:LinkToGT(sprintf($users_system->profile_url, '', $username));
			$post_link = (isset($nuke_configs['links_function'][$module]) && $nuke_configs['links_function'][$module] != '' && function_exists($nuke_configs['links_function'][$module])) ? $nuke_configs['links_function'][$module]($post_id):"";
			
			if(is_array($post_link))
				$post_link = $post_link[0];
				
			$current_comment_page = ($nuke_comments_configs['item_per_page'] > 0) ? ceil($position/$nuke_comments_configs['item_per_page']):0;
			$comment_link = $post_link;
			if($current_comment_page != 0 && $current_comment_page != 1)
			{
				$comment_link = trim($comment_link,"/");
				if($nuke_configs['gtset'] == 1)
					$comment_link .= "/comment-page-$current_comment_page/";
				else
					$comment_link .= "?page=$current_comment_page";
			}
			$url_arr = ($url != '') ? explode("/", $url):array();
			$clean_url = is_array($url_arr) && !empty($url_arr) ? $url_arr[2]:'';
			
			$user_level = ($group_name == 'ADMINISTRATORS') ? "<span style=\"font-weight:bold;color:#".$user_colour."\">مدير کل سايت : </span>":(($username != '') ? "کاربر سايت : ":"مهمان سايت : ");
			
			$content .= "
				<li class=\"list-group-item\">
				<span class=\"user\" style=\"margin-bottom:8px;\"><i class=\"glyphicon glyphicon-user\"></i> <a href=\"".$url."\" target=\"_blank\">$user_level<span style=\"font-weight:bold;color:#".$user_colour."\">".$name."</span></a></span>
				<p class=\"clear\" align=\"justify\"><i class=\"fa fa-comment\" aria-hidden=\"true\"></i> ".$comment." ";
				if($shorted)
				{
					$content .= "<a href=\"".LinkToGT($comment_link."#comment-$cid")."\" target=\"_blank\">"._MORE."</a>";
				}
				$content .= "</p>
				در : <a href=\"".LinkToGT($post_link)."\" target=\"_blank\">$post_title </a>
				</li>
				";
		}
	}
	$content .="</ul>";

?>