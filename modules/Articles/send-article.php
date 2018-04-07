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

function common(){
	return "<link href=\"includes/Ajax/jquery/jquery-checktree.css\" rel=\"stylesheet\" type=\"text/css\">
	<script src=\"includes/Ajax/jquery/jquery-checktree.js\"></script>";
}

function send_article($preview, $submit, $article_fileds, $security_code, $security_code_id)
{
	global $db, $userinfo, $nuke_configs, $module_name, $nuke_articles_categories_cacheData, $PnValidator, $visitor_ip;
	$contents = '';
	$meta_tags = array(
		"url" => LinkToGT("index.php?modname=$module_name&file=send-article"),
		"title" => _SEND_POST,
		"description" => _SEND_POST_DESCRIPTION,
		"keywords" => '',
		"extra_meta_tags" => array()
	);
	
	include ("header.php");
	$contents .= common();
	$contents .= OpenTable();
	$contents .= "<div align = 'center'><font class=\"title\"><b>"._SEND_POST."</b></div></font>";
	$contents .= CloseTable();
	$contents .= "<br>";
	$contents .= info_box("caution", _SUBMITADVICE);
	$contents .= "<br>";
	
	$username = (isset($userinfo['username'])) ? $userinfo['username']:$nuke_configs['anonymous'];
	$languageslist = get_dir_list('language', 'files');
	foreach($languageslist as $key => $val)
	{
		if($val == 'index.html' || $val == '.htaccess' || $val == 'alphabets.php')
		{
			unset($languageslist[$key]);
			continue;
		}
		$languageslist[$key] = str_replace(".php", "", $val);
	}
	
	if(isset($article_fileds) && is_array($article_fileds) && !empty($article_fileds))
	{
		$PnValidator->add_validator("in_languages", function($field, $input, $param = NULL) {
			$param = explode("-", $param);
			return in_array($input[$field], $param);
		}); 

		$PnValidator->validation_rules(array(
			'title'		=> 'required',
			'alanguage'	=> 'required|alpha|in_languages,'.implode("-",$languageslist).'',
			'hometext'	=> 'required'
		)); 
		// Get or set the filtering rules
		$PnValidator->filter_rules(array(
			'title'		=> 'sanitize_string',
			'alanguage'	=> 'sanitize_string',
			'hometext'	=> 'stripslashes|magic_quotes',
			'bodytext'	=> 'stripslashes|magic_quotes'
		)); 

		$article_fileds = $PnValidator->sanitize($article_fileds, array('title','alanguage'), true, true);
		$validated_data = $PnValidator->run($article_fileds);
		
		if($validated_data !== FALSE)
		{
			$article_fileds = $validated_data;
		}
		else
		{
			$contents .= OpenTable();
			$contents .= "<p align=\"center\">".$PnValidator->get_readable_errors(true,'gump-field','gump-error-message','<br />')."</p>";
			$contents .= CloseTable();
			$html_output .= show_modules_boxes($module_name, array("bottom_full", "top_full","left","top_middle","bottom_middle","right"), $contents);
			include("footer.php");
		}
		
		$title = filter($article_fileds['title'], "nohtml");
		$alanguage = filter($article_fileds['alanguage'], "nohtml");
		$hometext = $article_fileds['hometext'];
		$bodytext = $article_fileds['bodytext'];
		$cats = $article_fileds['cats'];
		
		if(isset($submit) && $submit == _OK)
		{
			$code_accepted = false;
			
			if(extension_loaded("gd") && in_array("send_post", $nuke_configs['mtsn_gfx_chk']))
				$code_accepted = code_check($security_code, $security_code_id);
			else
				$code_accepted = true;
				
			if($code_accepted)
			{
				$article_fileds['ip'] = $visitor_ip;
				$article_fileds['status'] = 'pending';
				$article_fileds['time'] = _NOWTIME;
				$article_fileds['cat'] = (isset($article_fileds['cats']) && !empty($article_fileds['cats'])) ? implode(",",$article_fileds['cats']):"";
				unset($article_fileds['cats']);
				$article_fileds['informant'] = (is_user()) ? $userinfo['username']:$nuke_configs['anonymous'];

				$db->table(ARTICLES_TABLE)
					->insert($article_fileds);
				
				$contents .= OpenTable();
				$contents .= "<div class=\"text-center\"><font class=\"title\">"._POST_SENT."</font><br><br>"
				."<font class=\"content\"><b>"._POST_SENT_THANKS."</b><br><br>"
				.""._POST_SENT_DESCRIPTION."</div>";
				$contents .= CloseTable();
				$html_output .= show_modules_boxes($module_name, array("bottom_full", "top_full","left","top_middle","bottom_middle","right"), $contents);
				include ("footer.php");
			}
			else
			{
				$contents .= OpenTable();
				$contents .= "<p align=\"center\">"._BADSECURITYCODE."<br />"._GOBACK."</p>";
				$contents .= CloseTable();
				$html_output .= show_modules_boxes($module_name, array("bottom_full", "top_full","left","top_middle","bottom_middle","right"), $contents);
				include("footer.php");
			}	
		}
	}
	else
	{
		$title = '';
		$alanguage = '';
		$hometext = '';
		$bodytext = '';
		$cats = 0;
	}
		

	$list_config = array(
		'checked_list'	=> ((isset($article_fileds['cats']) && is_array($article_fileds['cats']) && !empty($article_fileds['cats'])) ? $article_fileds['cats']:array()),
		'has_input'		=> true,
		'input_type'	=> 'checkbox',
		'input_name'	=> 'article_fileds[cats][]',
		'var_pid'		=> 'parent_id',
		'class_name'	=> '',
		'var_id'		=> 'catid-{ID}',
		'var_value'		=> 'catname'
	);
	
	$all_cats = (!empty($nuke_articles_categories_cacheData)) ? get_sub_lists($nuke_articles_categories_cacheData, $nuke_articles_categories_cacheData, 0, $list_config):"";
	
	$contents .= OpenTable();
	$contents .= "
	<form action=\"".LinkToGT("index.php?modname=$module_name&file=send-article")."\" method=\"post\" id=\"article_form\">
	<table widht=\"100%\">
		<tr>
			<th>"._NAME.":</th>
			<td>";
			if (is_user())
			{
				$contents .= "<a href=\"".LinkToGT("index.php?modname=Your_Account")."\">$username</a> <font class=\"content\">[ <a href=\"index.php?modname=Your_Account&amp;op=logout\">"._LOGOUT."</a> ]</font>";
			}
			else
			{
				$contents .= "".$nuke_configs['anonymous']." <font class=\"content\">[ <a href=\"".LinkToGT("index.php?modname=Your_Account&op=newuser")."\">"._NEWUSER."</a> ]</font>";
			}
			$contents .= "</td>
		</tr>
		<tr>
		<th>"._TITLE.":</th>
			<td>
				<input type=\"text\" name=\"article_fileds[title]\" value=\"$title\" maxlength=\"80\" required>
				<br>("._BEDESCRIPTIVE.")
			</td>
		</tr>
		<tr>
			<th>"._CATEGORY.":</th>
			<td>
				<div style=\"max-height:200px;width:95%;overflow:auto;line-height:32px;background:#efefef;border-radius:5px;padding:8px;\">
					<ul id=\"checkbox_tree\">$all_cats</ul>
				</div>
			</td>
		</tr>";
		if ($nuke_configs['multilingual'] == 1)
		{
			$contents .= "
			<tr>
				<th>"._LANGUAGE.":</th>
				<td>
					<select name=\"article_fileds[alanguage]\">";
					foreach($languageslist as $language_name)
					{
						$sel = ($alanguage != '' && $language_name == $alanguage) ? "selected":(($alanguage == '' && $language_name == $nuke_configs['language']) ? "selected":"");
						$contents .= "<option value=\"$language_name\" $sel>".ucfirst($language_name)."</option>\n";
					}
				$contents .= "</select>
				</td>
			</tr>";
		}
		else
		{
			$contents .= "<input type=\"hidden\" name=\"article_fileds[alanguage]\" value=\"$language\"></td></tr></table>";
		}
		$contents .= "
		<tr>
			<th>"._HOMETEXT.":</th>
			<td>";
				$contents .= wysiwyg_textarea('article_fileds[hometext]', stripslashes($hometext), 'PHPNukeUser', '50', '12');
			$contents .= "
			</td>
		</tr>
		<tr>
			<th>"._BODYTEXT.":</th>
			<td>";
				$contents .= wysiwyg_textarea('article_fileds[bodytext]', stripslashes($bodytext), 'PHPNukeUser', '50', '12');
				$contents .="<br /><font class=\"content\">("._AREYOUSURE.")
			</td>
		</tr>";
		if(isset($preview) && $preview == _PREVIEW)
		{
			if(extension_loaded("gd") && in_array("send_post", $nuke_configs['mtsn_gfx_chk']))
			{
				$security_code_input = makePass("_send_article");
				$contents .= "
				<tr>
					<th>"._SECCODE.":</th>
					<td>".$security_code_input['image']."<br /><br />".$security_code_input['input']."</td>
				</tr>";
			}
		}		
		$contents .= "
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type=\"submit\" name=\"preview\" value=\""._PREVIEW."\">
				&nbsp;&nbsp; 
				".((isset($preview) && $preview == _PREVIEW) ? "<input type=\"submit\" name=\"submit\" value=\""._OK."\">":"&nbsp;")." &nbsp; ("._SUBPREVIEW.")
			</td>
		</tr>
	</table>
	<input type=\"hidden\" name=\"op\" value=\"send_article\">
	</form>
	<script>
		$(document).ready(function(){
			$(\"#article_form\").validate();
		});
		$('#checkbox_tree').checktree();
	</script>";
	$contents .= CloseTable();
	$html_output .= show_modules_boxes($module_name, array("bottom_full", "top_full","left","top_middle","bottom_middle","right"), $contents);
	include ('footer.php');
}

$op					= isset($op) ? $op : "";
$article_fileds		= isset($article_fileds)? $article_fileds : array();
$preview			= isset($preview)? filter($preview, "nohtml") : "";
$submit				= isset($submit)? filter($submit, "nohtml") : "";
$security_code		= isset($security_code)? filter($security_code, "nohtml") : "";
$security_code_id	= isset($security_code_id)? filter($security_code_id, "nohtml") : "";

switch($op) {
	default:
		send_article($preview, $submit, $article_fileds, $security_code, $security_code_id);
	break;
}

?>