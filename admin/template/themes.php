<?php

class Walker_admin_top_menus extends Walker
{

	public function start_lvl(&$output, $depth = 0, $args = array())
	{
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent\t<".$args->list_type."".(($depth == 0) ? " class=\"sub-menu\"":"").">\n";
	}
		
	public function end_lvl(&$output, $depth = 0, $args = array())
	{
		$indent = str_repeat("\t", $depth);
		$output .= "$indent\t</".$args->list_type.">\n";
	}
	
	public function start_el(&$output, $element, $depth = 0, $args = array(), $id = 0)
	{
		$indent = ($depth) ? str_repeat("\t", $depth) : '';

		$title			= (defined($element->title)) ? constant($element->title):$element->title;
		
		$output .= $indent . '<li>';

		$args_before = sprintf($args->before, $element->url, $element->icon);
		
		$item_output = $args_before . $title . $args->after;
		
		$output .= $item_output;
	}
	
	public function end_el(&$output, $element, $depth = 0, $args = array())
	{
		$output .= "</li>\n";
	}
}

function jquery_codes_load($plugins="", $reload = false)
{
	global $nuke_configs, $currentlang;
	$script = "	<script type=\"text/javascript\">
		$(document).ready(function() {
			custom_jquery({
				nuke_lang :  '".(($nuke_configs['multilingual'] == 1) ? $nuke_configs['currentlang']:$nuke_configs['language'])."',
				nuke_date :  '".$nuke_configs['datetype']."',
				reload :  '".$reload."'
			});
			$(document).pngFix( );
			$plugins
		});
	</script>\n";
	return $script;
}

function adminheader_popup($pagetitle)
{
	global $db, $this_place, $admin_file, $pagetitle, $nuke_configs;

	$contents = '';
	if(!isset($this_place))
	{
		$this_place = _ADMINISTRATION;
	}
		
	if(!defined("_DIRECTION"))
	{
		define("_DIRECTION", "rtl");
	}

$contents .= "<!DOCTYPE html>
<html xmlns=\"http://www.w3.org/1999/xhtml\" dir=\""._DIRECTION."\">
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
	<title>"._ADMINISTRATION." </title>";
	if (file_exists("themes/".$nuke_configs['ThemeSel']."/images/favicon.ico")) {
	$contents .= "<link rel=\"shortcut icon\" href=\"themes/".$nuke_configs['ThemeSel']."/images/favicon.ico\" type=\"image/x-icon\">\n";
	}
	$contents .="
	<link rel=\"stylesheet\" href=\"admin/template/css/screen.css\" type=\"text/css\" media=\"screen\" title=\"default\" />
	<script src=\"includes/Ajax/jquery/jquery.min.js\" type=\"text/javascript\"></script>
	<script type=\"text/javascript\" src=\"includes/Ajax/jquery/jquery-ui.min.js\"></script>
	<script type=\"text/javascript\" src=\"includes/Ajax/jquery/jquery.ui.touch-punch.min.js\"></script>
	<script src=\"admin/template/js/jquery/prettyCheckable.min.js\" type=\"text/javascript\"></script>
	<script type=\"text/javascript\" src=\"includes/Ajax/jquery/thickbox.js\"></script>
	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/thickbox.css\" type=\"text/css\" />
	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/jquery-ui.min.css\" type=\"text/css\" />
	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/datepicker/css/jquery-ui-1.8.14.css\" type=\"text/css\" />\n";
	if(_DIRECTION == "ltr")
	{
	$contents .= "
	<link rel=\"stylesheet\" href=\"admin/template/css/screen-ltr.css\" type=\"text/css\" media=\"screen\" title=\"default\" />
	<style>
		#TB_ajaxContent{
		text-align:left;
		}
	</style>\n";
	}
	else
	{
		$contents .="	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/jquery-ui.min.rtl.css\" type=\"text/css\" />";
	}
	$contents .="
	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/jquery-ui.theme.min.css\" type=\"text/css\" />
	<link rel=\"stylesheet prefetch\" href=\"includes/fonts/font-awesome.min.css\">
	<link rel=\"stylesheet prefetch\" href=\"includes/fonts/font-IRANSansWeb.css\">
</head>
<body> 

	<div id=\"content-outer\">

		<div id=\"content\">
			
			<table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" id=\"content-table\">
				<tr>
					<th rowspan=\"3\" class=\"sized\"><img src=\"admin/template/images/shared/side_shadowright.jpg\" width=\"20\" height=\"300\" alt=\"\" /></th>
					<th class=\"topleft\"></th>
					<td id=\"tbl-border-top\">&nbsp;</td>
					<th class=\"topright\"></th>
					<th rowspan=\"3\" class=\"sized\"><img src=\"admin/template/images/shared/side_shadowleft.jpg\" width=\"20\" height=\"300\" alt=\"\" /></th>
				</tr>
				<tr>
					<td id=\"tbl-border-right\"></td>
					<td>
						<div id=\"content-table-inner\" style=\"padding-top:0;\"><div>";
						
	return $contents;

}

function adminheader($pagetitle, $meta_tags, $has_micrometa)
{
global $db, $this_place, $admin_file, $pagetitle, $is_popup, $nuke_authors_cacheData, $aid, $nuke_configs, $admin_top_menus;
$contents = '';
if(defined('IS_POPUP'))
{
	
	$contents .= adminheader_popup($pagetitle);
}
else
{
if(!isset($this_place))
{
	$this_place = _ADMINISTRATION;
}

$this_place .= ($pagetitle != '') ? " - $pagetitle":'';

$admin_realname = ($nuke_authors_cacheData[$aid]['realname'] != "") ? $nuke_authors_cacheData[$aid]['realname']:$aid;
$upload_allowed_info = phpnuke_unserialize(stripslashes($nuke_configs['upload_allowed_info']));
$default_folder = is_God() ? 'files':(($upload_allowed_info[$aid]['path'] != '') ? $upload_allowed_info[$aid]['path']:"files/uploads/$aid");
	
if(!defined("_DIRECTION"))
{
	define("_DIRECTION", "rtl");
}

$top_admin_menus_output = '';
$args = (object) array(
	'list_type'			=> "ul",
	'before'			=> '<a href="%1$s"><i class="fa fa-%2$s"></i> ',
	'after'				=> '</a>', 
);

$admin_top_menus = array_flatten($admin_top_menus, 0, 'id', 'parent_id', 'children', array('title','url','icon'), array());

$admin_top_menus = arrayToObject($admin_top_menus);

$walker = new Walker_admin_top_menus;
$args = array($admin_top_menus, 'id', 'parent_id', 0, $args);
$top_admin_menus_output .= call_user_func_array(array($walker, "walk"), $args);

if(defined("ADMIN_LOGIN"))
{
$contents .="<!DOCTYPE html>
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
	<title>"._ADMINLOGIN." - ".strip_tags($pagetitle)."</title>
	<script src=\"admin/template/js/jquery/jquery.pngFix.pack.js\" type=\"text/javascript\"></script>
	<script type=\"text/javascript\">
		$(document).ready(function(){
			$(document).pngFix( );
		});
	</script>";		
	if (file_exists("themes/".$nuke_configs['ThemeSel']."/images/favicon.ico"))
	{
	$contents .= "<link rel=\"shortcut icon\" href=\"themes/".$nuke_configs['ThemeSel']."/images/favicon.ico\" type=\"image/x-icon\">\n";
	}
	$contents .="<link rel=\"stylesheet\" href=\"admin/template/css/login.css\" type=\"text/css\" media=\"screen\" title=\"default\" />
	<link rel=\"stylesheet prefetch\" href=\"includes/fonts/font-awesome.min.css\">
	<link rel=\"stylesheet prefetch\" href=\"includes/fonts/font-IRANSansWeb.css\">
	<script src=\"includes/Ajax/jquery/jquery.min.js\" type=\"text/javascript\"></script>";
	if(_DIRECTION == "ltr")
	{
		$contents .= "<link rel=\"stylesheet\" href=\"admin/template/css/login-ltr.css\" type=\"text/css\" media=\"screen\" title=\"default\" />\n";
	}
	if($has_micrometa)
	{
	$contents .= "<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/jquery/jquery.rating.css\" type=\"text/css\" media=\"screen\" title=\"default\" />
	<script src=\"includes/Ajax/jquery/jquery/jquery.rating.pack.js\" type=\"text/javascript\"></script>\n";
	}
	$contents .="
</head>
<body>";
}
else
{
$counter_result = $db->query("SELECT COUNT(cid) AS comments_counts, (SELECT COUNT(fid) FROM ".FEEDBACKS_TABLE." WHERE replys = '' OR replys IS NULL) AS feedbacks_count FROM ".COMMENTS_TABLE." WHERE status = '0'");
$counter_row = $counter_result->results()[0];
$comments_counts = intval($counter_row['comments_counts']);
$feedbacks_count = intval($counter_row['feedbacks_count']);

$contents .="<!DOCTYPE html>
<html xmlns=\"http://www.w3.org/1999/xhtml\" dir=\""._DIRECTION."\">
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
	<title>".strip_tags($this_place)." </title>";
	if (file_exists("themes/".$nuke_configs['ThemeSel']."/images/favicon.ico"))
	{
	$contents .= "<link rel=\"shortcut icon\" href=\"themes/".$nuke_configs['ThemeSel']."/images/favicon.ico\" type=\"image/x-icon\">\n";
	}
	$contents .="
    <!--
	this option not available now
	<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1\" />
	-->
	<link rel=\"stylesheet prefetch\" href=\"includes/fonts/font-awesome.min.css\">
	<link rel=\"stylesheet prefetch\" href=\"includes/fonts/font-IRANSansWeb.css\">
	<link rel=\"stylesheet\" href=\"admin/template/css/screen.css\" type=\"text/css\" media=\"screen\" title=\"default\" />
	<script src=\"includes/Ajax/jquery/jquery.min.js\" type=\"text/javascript\"></script>
	<script src=\"includes/Ajax/jquery/jquery-migrate-1.4.1.min.js\"></script>
	<script type=\"text/javascript\" src=\"includes/Ajax/jquery/jquery-ui.min.js\"></script>
	<script type=\"text/javascript\" src=\"includes/Ajax/jquery/jquery.ui.touch-punch.min.js\"></script>
	<script type=\"text/javascript\" src=\"includes/Ajax/jquery/jquery.cookie.js\"></script>
	<script src=\"admin/template/js/jquery/prettyCheckable.min.js\" type=\"text/javascript\"></script>
	<script type=\"text/javascript\" src=\"includes/Ajax/jquery/thickbox.js\"></script>
	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/thickbox.css\" type=\"text/css\" />
	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/jquery-ui.min.css\" type=\"text/css\" />
	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/datepicker/css/jquery-ui-1.8.14.css\" type=\"text/css\" />\n";
	if(_DIRECTION == "ltr")
	{
	$contents .= "
	<link rel=\"stylesheet\" href=\"admin/template/css/screen-ltr.css\" type=\"text/css\" media=\"screen\" />
	<style>
		#TB_ajaxContent{
		text-align:left;
		}
	</style>\n";
	}
	else
	{
		$contents .="	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/jquery-ui.min.rtl.css\" type=\"text/css\" />";
	}
	if($has_micrometa)
	{
	$contents .= "<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/jquery/jquery.rating.css\" type=\"text/css\" media=\"screen\" title=\"default\" />
	<script src=\"includes/Ajax/jquery/jquery/jquery.rating.pack.js\" type=\"text/javascript\"></script>\n";
	}
	$contents .="
	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/select2.css\" type=\"text/css\" media=\"screen\" />
	<link rel=\"stylesheet\" href=\"includes/Ajax/jquery/jquery-ui.theme.min.css\" type=\"text/css\" />
</head>
<body> 

	<header>
		<div class=\"topnav\">
			<nav>
				<ul>
					<li><a target=\"_balnk\" href=\"".$nuke_configs['nukeurl']."\"><i class=\"fa fa-desktop\"></i> "._SHOW_FRONTPAGE."</a></li>
				</ul>
			</nav>
			<span><i class=\"fa fa-calendar\"></i> "._TODAY." : ".nuketimes(_NOWTIME, false, false, false, 3)."</span>
		</div>
		
		<div class=\"nukelogo\"></div>
		<div class=\"top-counters\">
			<a href=\"".$admin_file.".php?op=comments&status=0\"><div class=\"nukecomment\"><span>"._UNAPPROVED_COMMENTS."</span><strong>$comments_counts</strong></div></a>
			<a href=\"".$admin_file.".php?op=feedbacks\"><div class=\"nukefeedback\"><span>"._FEEDBACKS_NUMBBER."</span><strong>$feedbacks_count</strong></div></a>
		</div>
		<div class=\"clear\"></div>
		<div id=\"main-menu\">
			<div class=\"rightnav\" id=\"myTopnav\">
			<ul class=\"botnav\">
				$top_admin_menus_output
				<li class=\"admin_link\"><a href=\"".$admin_file.".php\"><i class=\"fa fa-tachometer\"></i> "._ADMIN_PAGE."</a></li>
				<li class=\"logout_link\"><a href=\"".$admin_file.".php?op=logout\"><i class=\"fa fa-sign-out\"></i> "._ADMIN_LOGOUT."</a></li>
			</ul>
			</div>
			<div class=\"clear\"></div>
		</div>
	</header>
	
	<div class=\"clear\"></div>

	<div id=\"content-outer\">

		<div id=\"content\">
			
			<div id=\"page-heading\">
				<h1>".$this_place."</h1>
			</div>

			<div id=\"content-table\">
				<div id=\"content-table-inner\">
					<div id=\"table-content\">";
						if(defined("ADMIN_MAIN")){
							$contents .= get_latest_info();
						}
	}
}
return $contents;
}

function adminfooter()
{
	if(defined("ADMIN_LOGIN"))
	{
		return "
</body>
</html>";
		die();
	}
	
	$contents = '';
					$contents .="</div>
				</div>
			</div>
		</div>
		<div class=\"clear\">&nbsp;</div>
	</div>
	<div class=\"clear\">&nbsp;</div>

	<div class=\"clear\">&nbsp;</div>
	<script src=\"includes/Ajax/jquery/select2.min.js\" type=\"text/javascript\"></script>
	<script src=\"admin/template/js/jquery/jquery.filestyle.js\" type=\"text/javascript\"></script>
	<script src=\"admin/template/js/jquery/jquery.tooltip.js\" type=\"text/javascript\"></script>
	<script src=\"admin/template/js/jquery/jquery.dimensions.js\" type=\"text/javascript\"></script>
	<script src=\"admin/template/js/jquery/jquery.pngFix.pack.js\" type=\"text/javascript\"></script>
	<script src=\"admin/template/js/jquery/custom_jquery.js\" type=\"text/javascript\"></script>\n";
	$contents .= jquery_codes_load();
$contents .="</body>
</html>";
	return $contents;
}

function admin_pagination($total_rows, $entries_per_page, $current_page, $link_to, $pageid="", $page_name="")
{
	$total_page = ceil($total_rows / $entries_per_page);
	$str_page  = (strpos($link_to, "?") === false) ? "?page$pageid" : "&amp;page$pageid";
	$page_name = (isset($page_name) && $page_name != "") ? "#$page_name":"";
	$page = $current_page-3;
	$upper =$current_page+3;
	if ($page <=0) {
		$page=1;
	}
	if ($upper >$total_page) {
		$upper =$total_page;
	}

  	if ($upper-$page <6){

		//We know that one of the page has maxed out
		//check which one it is
		//echo "$upper >=$maxPage<br>";
		if ($upper >=$total_page){
			//the upper end has maxed, put more on the front end
			//echo "to begining<br>";
			$dif =$total_page-$page;
			//echo "$dif<br>";
				if ($dif==3){
					$page=$page-3;
				}elseif ($dif==4){
					$page=$page-2;
				}elseif ($dif==5){
					$page=$page-1;
				}
		}elseif ($page <=1) {
			//its the low end, add to upper end
			//echo "to upper<br>";
			$dif =$upper-1;

			if ($dif==3){
				$upper=$upper+3;
			}elseif ($dif==4){
				$upper=$upper+2;
			}elseif ($dif==5){
				$upper=$upper+1;
			}
		}
	}	
	if ($page <=0) {
		$page=1;
	}
	$nav = $first = $prev = $next = $last = $options = '';
 	for($page; $page <=  $upper; $page++) {
		if ($page == $current_page){
			$nav .= "<div class=\"page-info\">Page <strong>$page</strong> / $total_page</div>";
		}else{
			$nav .= "<a href=\"$link_to$str_page=$page$page_name\"><div class=\"page-other\">$page</div></a>";
		}
	}	  
	if ($current_page > 1){
		$ppage  = $current_page - 1;
		$prev  = "<a class=\"page-left\" href=\"$link_to$str_page=$ppage$page_name\"></a> ";
		$first = "<a class=\"page-far-left\" href=\"$link_to\"></a> ";
	}else{
		$prev  = "<span class=\"page-left\"></span> ";
		$first = "<span class=\"page-far-left\"></span> ";
	}	
	if ($current_page < $total_page AND $upper <= $total_page)
	{
		$page = $current_page + 1;
		$next = " <a class=\"page-right\" href=\"$link_to$str_page=$page$page_name\"></a>";
		$last = " <a class=\"page-far-right\" href=\"$link_to$str_page=$total_page$page_name\"></a>";
	} else {
		$next = " <span class=\"page-right\"></span>";
		$last = " <span class=\"page-far-right\"></span>";
	}	
	for($i=1;$i<=$total_page;$i++)
	{
		if($i == $current_page)
		{
			$options .="<a class=\"action-pagenum\"> &nbsp;$i</a>";
		}
		else
		{
			$options .="<a href=\"$link_to$str_page=$i$page_name\" class=\"action-pagenum\"> &nbsp;$i</a>";
		}
	}
	$paginate_links = "<div align=\"center\"><table align=\"center\" border=\"0\"><tr><td style=\"border:0\" align=\"center\">".$first . $prev . $nav . $next . $last."</td><td style=\"border:0\">&nbsp;&nbsp;
			<div class=\"actions-box\">
				<a href=\"#\" class=\"action-slider action-pagenumber\" rel=\"$pageid\"></a>
				<div class=\"actions-box-slider\" id=\"actions-box-slider$pageid\">
					$options
				</div>
				<div class=\"clear\"></div>
			</div>
			</td></tr></table></div>";
	

  return $paginate_links;
  
}

function OpenAdminTable()
{
	$contents = '';
	$contents .= "<div class=\"Table\">\n";
    $contents .= "<div class=\"Contents\">\n";
	return $contents;
}

function CloseAdminTable()
{
	$contents = '';
	$contents .= "</div>\n";
	$contents .= "</div>\n";
	return $contents;
}

function OpenAdminTable2()
{
	$contents = '';
	$contents .= "<div class=\"Table\">\n";
    $contents .= "<div class=\"Contents\">\n";
	return $contents;
}

function CloseAdminTable2()
{
	$contents = '';
	$contents .= "</div>\n";
	$contents .= "</div>\n";
	return $contents;
}

function OpenTable()
{
	$contents = '';
	$contents .= "<div class=\"Table\">\n";
    $contents .= "<div class=\"Contents\">\n";
	return $contents;
}

function CloseTable()
{
	$contents = '';
	$contents .= "</div>\n";
	$contents .= "</div>\n";
	return $contents;
}

function themesidebox($title, $content, $themeview=1, $themetype)
{
	$contents = '';
	$contents .="
		<table width=\"200\" border=\"0\">
			<tr>
				<td style=\"border:1px solid #cccccc;height:25px;padding:3px;\">$title</td>
			</tr>
			<tr>
				<td style=\"border:1px solid #cccccc;height:150px;padding:3px;\">$content</td>
			</tr>		
		</table>
	";
	return $contents;
}

function bubble_show($content)
{
	$contents = '';
	$contents .= "<span class=\"bubble\"><div class=\"bubble-right\"></div>
		<div class=\"bubble-inner\">$content</div>
		<div class=\"bubble-left\"></div><span>";
	return $contents;
}

function themecenterbox($title, $content, $themeview=1, $themetype)
{
	$contents = '';
	$contents .="
		<table width=\"600\" border=\"0\">
			<tr>
				<td style=\"border:1px solid #cccccc;height:25px;padding:3px;\">$title</td>
			</tr>
			<tr>
				<td style=\"border:1px solid #cccccc;height:150px;padding:3px;\">$content</td>
			</tr>		
		</table>
	
	";
	return $contents;
}

function get_latest_info()
{
	global $db, $admin_file, $alerts_messages;
	if(is_array($alerts_messages) && !empty($alerts_messages))
	{
		$counter_prefix = 1;
		foreach($alerts_messages as $alerts_message_key => $alerts_messages_value)
		{
			if(!isset($alerts_messages_value['table'])) continue;
			
			$count_prefix = isset($alerts_messages_value['prefix']) ? $alerts_messages_value['prefix']:"c$counter_prefix";
			$count_by = isset($alerts_messages_value['by']) ? $alerts_messages_value['by']:"id";
			$count_table = $alerts_messages_value['table'];
			$count_where = (isset($alerts_messages_value['where']) && $alerts_messages_value['where'] != '') ? "WHERE ".$alerts_messages_value['where']:"";
			$select_queries[] = "(SELECT COUNT($count_prefix.$count_by) FROM $count_table AS $count_prefix $count_where) as $alerts_message_key";
			$counter_prefix++;
		}
		$select_queries = implode(", ", $select_queries);
	}
	
	$contents = '';
	$result = $db->query("SELECT DISTINCT $select_queries");
	$rows = $result->results()[0];
	foreach($rows as $row_key => $row_value)
	{
		if($row_value != 0)
		{
			$color = $alerts_messages[$row_key]['color'];
			$text = sprintf((defined($alerts_messages[$row_key]['text']) ? constant($alerts_messages[$row_key]['text']):$alerts_messages[$row_key]['text']), $row_value);
			$contents .="
			<div class=\"message-alert message-$color\">
				<span class=\"message-close close-$color\">&#215;</span>
				<span class=\"message-text\">$text</sapn>
			</div>
			";
		}
	}
	
	return $contents;
}

?>