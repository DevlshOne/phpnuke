<?PHP

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2017 by MashhadTeam                                    */
/* http://www.phpnuke.ir                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (!defined('ADMIN_FILE'))
{
  die ("Access Denied");
}

global $db, $admin_file;

$filename =  basename(__FILE__);
$filename = substr($filename,0,-4);

if (check_admin_permission($filename))
{
	function hreferrer()
	{
		global $db, $admin_file, $nuke_configs, $pagetitle;
		$pagetitle = _REFERRERS_ADMIN;
		$contents = '';
		$contents .= GraphicAdmin();
		$contents .= OpenAdminTable();
		$contents .=  "<br /><br /><div class=\"text-center\"><b>" . _REFERRERS . "</b></div><br><br>";
		
		$total_rows = 0;
		$entries_per_page = 50;
		$current_page = (empty($_GET['page'])) ? 1 : $_GET['page'];
		$start_at  = ($current_page * $entries_per_page) - $entries_per_page;
		$link_to = "".$admin_file.".php?op=hreferer";
		
		$result = $db->query("SELECT *, (SELECT COUNT(rid) FROM ".REFERRER_TABLE.") AS total_rows from ".REFERRER_TABLE." ORDER BY rid DESC LIMIT ?, ?", array($start_at, $entries_per_page));
		
		$contents .=  "
		<form action=\"".$admin_file.".php\" method=\"post\">
		<table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" class=\"product-table\" id=\"http-referrers\">
		<tr>
		<th class=\"table-header-repeat line-left\" style=\"width:50px;\"><a>"._ROW."</a></th>
		<th class=\"table-header-repeat line-left\" style=\"width:200px;\"><a>"._DATE."</a></th>
		<th class=\"table-header-repeat line-left\" style=\"width:120px;\"><a>"._IP_INPUT."</a></th>
		<th class=\"table-header-repeat line-left\"><a>"._REFERRAL_LINK."</a></th>
		<th class=\"table-header-repeat\"><a>"._ENTRY_LINK."</a></th>
		<th class=\"table-header-repeat no-padding\" style=\"width:110px\"><input data-label=\""._OPERATION."\" type=\"checkbox\" class=\"styled select-all\" data-element=\"#http-referrers\"></th>";
		if($db->count() > 0)
		{
			foreach ($result as $row)
			{
				if($total_rows == 0)
					$total_rows = intval($row['total_rows']);
					
				$rid = intval($row['rid']);
				$path = filter($row['path'], 'nohtml');
				$url = filter($row['url'], 'nohtml');
				$ip = filter($row['ip'], 'nohtml');
				$time = nuketimes($row['time'])." ".date("H:i:s", $row['time']);
				$url_encoded = urlencode($url);
				//$url_arr = explode("/", $url);
				//$url_short = "http://$url_arr[2]";
				
				$url_short = rawurldecode(mb_word_wrap($url_encoded, 140, '...'));
				$path_short = rawurldecode(mb_word_wrap($path, 140, '...'));
				$contents .= "<tr>
					<td align=\"center\">$rid</td>
					<td align=\"center\">$time</td>
					<td align=\"center\" class=\"dleft\">$ip</td>
					<td align=\"center\" class=\"dleft\"><a href=\"index.php?url=$url_encoded\" target=\"_new\" title=\"$url\" class=\"info-tooltip\">$url_short</a></td>
					<td align=\"center\" class=\"dleft\"><a href=\"index.php?url=".$path."\" target=\"_new\" class=\"info-tooltip\">$path_short</a></td>
					<td align=\"center\"><a href=\"".$admin_file.".php?op=delreferrer&rid=$rid\" class=\"table-icon icon-2 info-tooltip\" title=\""._DELETE."\" onclick=\"return confirm('"._DELETE_THIS_SURE."');\"></a> <input type=\"checkbox\" class=\"styled\" name=\"rid[]\" value=\"$rid\" /></td>
				</tr>";
			}
		}
		$contents .= "</table>
		<input type=\"hidden\" name=\"op\" value=\"delreferrer\">
		<div class=\"text-center\"><input type=\"submit\" value='"._DELETE_SELECTED."' onclick=\"return confirm('"._DELETE_THIS_SURE."');\"></div><br /><p align=\"center\"><a href=\"".$admin_file.".php?op=delreferrer&all=1\" onclick=\"return confirm('"._DELETE_ALL_THIS_SURE."');\"><button type=\"button\">"._DELETE_ALL."</button></a></p><br />
		<div id=\"pagination\" class=\"pagination\">";
		$contents .= admin_pagination($total_rows, $entries_per_page, $current_page, $link_to);
		$contents .= "</div></form>";
		$contents .= CloseAdminTable();
		
		include("header.php");
		$html_output .= $contents;
		include("footer.php");
	}

	function delreferrer($rid, $all='')
	{
		global $db, $admin_file;

		if($all == 1)
		{
			$rids = null;
			$log_msg = _DELETE_ALL_REFERRERS;
		}
		else
		{
			if(!is_array($rid) && $rid != 0)
				$rid = array($rid);
			
			$rids = array_map("pn_array_map", $rid);			
			$log_msg = "حذف تعداد ".count($rids)." لینک ارجاع دهنده";
		}
		
		$db->table(REFERRER_TABLE)
			->in('rid', $rids)
			->delete();

		add_log($log_msg, 1);
		
		Header("Location: ".$admin_file.".php?op=hreferrer");
	}
	
	$rid = isset($rid) ? $rid:0;
	$op = isset($op) ? filter($op, "nohtml"):"";
	$all = isset($all) ? intval($all):0;

	switch($op) {

		case "hreferrer":
		hreferrer();
		break;

		case "delreferrer":
		delreferrer($rid, $all);
		break;

	}

}
else
{
	header("location: ".$admin_file.".php");
}
?>