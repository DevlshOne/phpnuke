<?php

if (!defined('NUKE_FILE')) {
	die ("You can't access this file directly...");
}

global $nuke_configs;

$theme_setup = array(
	"default_css" => array(
		"<link rel=\"stylesheet\" type=\"text/css\" href=\"".$nuke_configs['nukecdnurl']."includes/Ajax/jquery/bootstrap/css/bootstrap.min.css\">",
		"<link rel=\"stylesheet\" type=\"text/css\" href=\"".$nuke_configs['nukecdnurl']."includes/fonts/font-awesome.min.css\">",
		"".((_DIRECTION == 'rtl') ? "<link rel=\"stylesheet\" href=\"".$nuke_configs['nukecdnurl']."includes/Ajax/jquery/bootstrap/css/bootstrap-rtl.css\">":"")."",
		"<link rel=\"stylesheet\" type=\"text/css\" href=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/style/style.css\" />",
		"<link rel=\"stylesheet\" type=\"text/css\" href=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/style/jRating.jquery.css\" media=\"screen\" />",
		"<link rel=\"stylesheet\" type=\"text/css\" href=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/style/bootstrap-social.css\" media=\"screen\" />",
		"".((_DIRECTION == 'ltr') ? "<link rel=\"stylesheet\" href=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/style/ltr.css\" type=\"text/css\" /><style>#TB_ajaxContent{text-align:left;}</style>":"")."",
		"<link href=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/style/green.css\" rel=\"stylesheet\" id=\"colour-scheme\">",
	),
	"default_js" => array(
		"<script type=\"text/javascript\" src=\"".$nuke_configs['nukecdnurl']."includes/Ajax/jquery/jquery.min.js\"></script>",
		"<script type=\"text/javascript\">var phpnuke_url = '".$nuke_configs['nukeurl']."', phpnuke_cdnurl = '".$nuke_configs['nukecdnurl']."', phpnuke_theme = '".$nuke_configs['ThemeSel']."', nuke_lang = '".(($nuke_configs['multilingual'] == 1) ? $nuke_configs['currentlang']:$nuke_configs['language'])."', nuke_date = ".$nuke_configs['datetype'].";var theme_languages = { success_voted : '"._SUCCESS_VOTED."', try_again : '"._ERROR_TRY_AGAIN."'}
			</script>",
	),
	"defer_js" => array(
		"<!--[if lt IE 9]> <script src=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/plugins/html5shiv/dist/html5shiv.js\"></script><script src=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/plugins/respond/respond.min.js\"></script> <![endif]-->",
		"<script src=\"".$nuke_configs['nukecdnurl']."includes/Ajax/jquery/bootstrap/js/bootstrap.min.js\"></script>",
		"<script type=\"text/javascript\" src=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/script/script.js\"></script>",
		"<script type=\"text/javascript\" src=\" ".$nuke_configs['nukecdnurl']."includes/jrating/jRating.jquery.js\"></script>",
	),
	"default_link_rel" => array(
		"<link rel=\"apple-touch-icon-precomposed\" sizes=\"114x114\" href=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/images/icons/114x114.png\">",
		"<link rel=\"apple-touch-icon-precomposed\" sizes=\"72x72\" href=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/images/icons/72x72.png\">",
		"<link rel=\"apple-touch-icon-precomposed\" href=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/images/icons/default.png\">",
		"<link rel=\"shortcut icon\" href=\"".$nuke_configs['nukecdnurl']."themes/".$nuke_configs['ThemeSel']."/images/icons/favicon.png\">",
	),
	"default_meta" => array(),
	"theme_nav_menus" => array(
		'primary' => _MAIN_MENU,
		'footer'  => _FOOTER_MENU,
	),
	"theme_widgets" => array(
		"right" => _RIGHT_BLOCKS,
		"left" => _LEFT_BLOCKS,
		"topcenter" => _TOPCENTER_BLOCKS,
		"bottomcenter" => _BOTTOMCENTER_BLOCKS,
	),
	"theme_boxes_templates" => array(
		"modules_boxes"	=> array(
			"extra_class" => "row",
		),
		"top_full_moldule_boxes" => array(
			"extra_class" => "",
		),
		"right_module_boxes" => array(
			"extra_class" => array(
				"_r" => "col-xs-12 col-sm-12 col-md-4 col-lg-4",
				"_l_r" => "col-xs-12 col-sm-12 col-md-3 col-lg-3",
			),
			"order" => array(
				"_r" => 1,
				"_l_r" => 1
			),
			"pull" => array(
				"_l" => "",
				"_r" => "col-md-pull-8",
				"_l_r" => "col-md-pull-6"
			),
			"push" => ""
		),
		"middle_module_boxes" => array(
			"extra_class" => array(
				"full" => "col-sm-12 text-right",
				"_l" => "col-xs-12 col-sm-12 col-md-8 col-lg-8",
				"_r" => "col-xs-12 col-sm-12 col-md-8 col-lg-8",
				"_l_r" => "col-xs-12 col-sm-12 col-md-6 col-lg-6"
			),
			"order" => array(
				"full" => 0,
				"_l" => 0,
				"_r" => 0,
				"_l_r" => 0
			),
			"push" => array(
				"full" => "",
				"_r" => "col-md-push-4",
				"_l" => "",
				"_l_r" => "col-md-push-3"
			)
		),
		"left_module_boxes" => array(
			"extra_class" => array(
				"_l" => "col-xs-12 col-sm-12 col-md-4 col-lg-4",
				"_l_r" => "col-xs-12 col-sm-12 col-md-3 col-lg-3",
			),
			"order" => array(
				"_l" => 2,
				"_l_r" => 2
			),
		),
		"top_middle_moldule_boxes" => array(
			"extra_class" => "",
		),
		"main_middle_moldule_boxes" => array(
			"extra_class" => "",
		),
		"bottom_middle_moldule_boxes" => array(
			"extra_class" => "",
		),
		"bottom_full_moldule_boxes" => array(
			"extra_class" => "",
		)
	),
	'caspian_configs' => isset($nuke_configs['caspian_configs']) ? phpnuke_unserialize($nuke_configs['caspian_configs']):array(
		'active_slider' => 0,
		'slider_image' => array('','','','','','','','','','',''),
		'slider_title' => array('','','','','','','','','','',''),
		'slider_link' => array('','','','','','','','','','',''),
		'slider_desc' => array('','','','','','','','','','',''),
		'about_us' => '',
		'address' => '',
		'phone' => '',
		'mobile' => '',
		'fax' => '',
		'twitter' => '',
		'instagram' => '',
		'facebook' => '',
		'telegram' => '',
		'contact_us' => '',
	)
);

function caspian_theme_config()
{
	global $nuke_configs, $db, $admin_file, $theme_setup;
	
	$contents = '';
	$caspian_configs = $theme_setup['caspian_configs'];
	
	$contents .="
	<tr><th colspan=\"2\" style=\"text-align:center\">".sprintf(_THEME_SETTINGS, "Mashhadteam-Caspian")."</th></tr>
	<tr><th style=\"width:200px;\">"._ACTIVATE_SLIDER."</th><td>";
	$checked1 = ($caspian_configs['active_slider'] == 1) ? "checked":"";
	$checked2 = ($caspian_configs['active_slider'] == 0) ? "checked":"";
	$contents .= "<input type='radio' class='styled' name='config_fields[caspian_configs][active_slider]' value='1' data-label=\"" . _YES . "\" $checked1> &nbsp; &nbsp;<input type='radio' class='styled' name='config_fields[caspian_configs][active_slider]' value='0' data-label=\"" . _NO . "\" $checked2>
	</td></tr>";
	
	for($i=0; $i<10; $i++)
	{
		$contents .="
		<tr>
			<th style=\"width:200px;\">"._SLIDER_IMAGES_DATA."</th>
			<td>
				<input type=\"text\" placeholder=\""._PICTURE_LINK."\" class=\"inp-form-ltr\" name=\"config_fields[caspian_configs][slider_image][$i]\" value=\"".$caspian_configs['slider_image'][$i]."\" /> &nbsp; &nbsp;
				<input type=\"text\" placeholder=\""._TITLE."\" class=\"inp-form-ltr\" name=\"config_fields[caspian_configs][slider_title][$i]\" value=\"".$caspian_configs['slider_title'][$i]."\" /> &nbsp; &nbsp;
				<input type=\"text\" placeholder=\""._REFERRAL_LINK."\" class=\"inp-form-ltr\" name=\"config_fields[caspian_configs][slider_link][$i]\" value=\"".$caspian_configs['slider_link'][$i]."\" /> &nbsp; &nbsp;
				<input type=\"text\" placeholder=\""._DESCRIPTIONS."\" class=\"inp-form-ltr\" name=\"config_fields[caspian_configs][slider_desc][$i]\" value=\"".$caspian_configs['slider_desc'][$i]."\" />
			</td>
		</tr>";
	}
	$contents .="
	<tr><th colspan=\"2\" style=\"text-align:center\">"._THEME_FOOTER_DATA."</th></tr>
	<tr><th>"._ABOUTUS_TEXT."</th><td>";
	$contents .= wysiwyg_textarea('config_fields[caspian_configs][about_us]', $caspian_configs['about_us'], 'PHPNukeAdmin', 50, 5);
	$contents .= "
	</td></tr>
	<tr><th>"._POSTAL_ADDRESS."</th><td>
		<input type=\"text\" name=\"config_fields[caspian_configs][address]\" size=\"40\" class=\"inp-form-ltr\" value=\"".$caspian_configs['address']."\">
	</td></tr>
	<tr><th>"._LANDLINE_PHONE."</th><td>
		<input type=\"text\" name=\"config_fields[caspian_configs][phone]\" size=\"40\" class=\"inp-form-ltr\" value=\"".$caspian_configs['phone']."\">
	</td></tr>
	<tr><th>"._MOBILE_PHONE."</th><td>
		<input type=\"text\" name=\"config_fields[caspian_configs][mobile]\" size=\"40\" class=\"inp-form-ltr\" value=\"".$caspian_configs['mobile']."\">
	</td></tr>
	<tr><th>"._FAX."</th><td>
		<input type=\"text\" name=\"config_fields[caspian_configs][fax]\" size=\"40\" class=\"inp-form-ltr\" value=\"".$caspian_configs['fax']."\">
	</td></tr>
	<tr><th>"._TWITTER_LINK."</th><td>
		<input type=\"text\" name=\"config_fields[caspian_configs][twitter]\" size=\"40\" class=\"inp-form-ltr\" value=\"".$caspian_configs['twitter']."\">
	</td></tr>
	<tr><th>"._INSTAGRAM_LINK."</th><td>
		<input type=\"text\" name=\"config_fields[caspian_configs][instagram]\" size=\"40\" class=\"inp-form-ltr\" value=\"".$caspian_configs['instagram']."\">
	</td></tr>
	<tr><th>"._FACEBOOK_LINK."</th><td>
		<input type=\"text\" name=\"config_fields[caspian_configs][facebook]\" size=\"40\" class=\"inp-form-ltr\" value=\"".$caspian_configs['facebook']."\">
	</td></tr>
	<tr><th>"._TELEGRAM_LINK."</th><td>
		<input type=\"text\" name=\"config_fields[caspian_configs][telegram]\" size=\"40\" class=\"inp-form-ltr\" value=\"".$caspian_configs['telegram']."\">
	</td></tr>
	<tr><th>"._FEEDBACK_LINK."</th><td>
		<input type=\"text\" name=\"config_fields[caspian_configs][contact_us]\" size=\"40\" class=\"inp-form-ltr\" value=\"".$caspian_configs['contact_us']."\">
	</td></tr>";
	 return $contents;
}

$other_admin_configs['themes']['Mashhadteam-Caspian'] = array("title" => _CASPIAN_THEME_SETTINGS, "function" => "caspian_theme_config", "God" => false);

?>