<?php

if(version_compare(PHP_VERSION, '5.4.0', "<"))
{
	die("<p align=\"center\">sorry. PHPNuke 8.4 MT Edition needs php 5.4.0 or above. your Server PHP Version is : ".PHP_VERSION."</p>");
}

define("IN_INSTALL", true);
// Try to override some limits - maybe it helps some...
@set_time_limit(0);
$mem_limit = @ini_get('memory_limit');
if (!empty($mem_limit))
{
	$unit = strtolower(substr($mem_limit, -1, 1));
	$mem_limit = (int) $mem_limit;

	if ($unit == 'k')
	{
		$mem_limit = floor($mem_limit / 1024);
	}
	else if ($unit == 'g')
	{
		$mem_limit *= 1024;
	}
	else if (is_numeric($unit))
	{
		$mem_limit = floor((int) ($mem_limit . $unit) / 1048576);
	}
	$mem_limit = max(128, $mem_limit) . 'M';
}
else
{
	$mem_limit = '128M';
}
@ini_set('memory_limit', $mem_limit);

error_reporting(E_ALL);
@ini_set('display_errors', 1);

if(isset($_GET['op']))
{
	require_once("mainfile.php");
}
else
{
	// Get php version
	$phpver = phpversion();
	//// Set IRAN Time
	if ($phpver >= '5.1.0')
	{
		date_default_timezone_set('Asia/Tehran');
	}
	define("NUKE_FILE", true);
	define("_NOWTIME", time());
	if($phpver >= '5.4.0')
	{
		$methods = array("_GET","_POST","_REQUEST","_FILES");
		foreach($methods as $method)
		{
			unset($var_requests);
			eval('$var_requests = $'.$method.';');
			if(isset($var_requests) && !empty($var_requests))
			{
				foreach($var_requests as $method_key => $method_val)
				{
					$$method_key = $method_val;
				}
			}
		}
	}
	else
	{
		if (!ini_get('register_globals'))
		{
			@import_request_variables("GPC", "");
		}
	}
	require_once("config.php");
	require_once("includes/functions.php");
	require_once("includes/class.sessions.php");
	require_once("includes/class.cache.php");

	$pn_salt = "";
	// setup 'default' cache
	$cache = new Cache();
	require_once("includes/constants.php");
	include_once('db/Database.php');
	// Request URL Redirect To Nuke Url
	$Req_Protocol 	= strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
	$Req_Host     	= $_SERVER['HTTP_HOST'];
	$Req_Uri		= $_SERVER['REQUEST_URI'];
	$Req_Path		= $_SERVER['SCRIPT_NAME'];
	$Req_URL		= $Req_Protocol . '://' . $Req_Host . $Req_Path;
	$Req_URI		= $Req_Protocol . '://' . $Req_Host . $Req_Uri;
	$Req_Filename 	= substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	$filenamepos 	= strpos($Req_URL,$Req_Filename);
	$Req_URL 		= substr($Req_URL,0,$filenamepos);
	$Redirect_Url 	= substr($Req_URI,strlen($Req_URL),strlen($Req_URL));
}

global $db, $nuke_configs;

if($cache->isCached('install_options'))
{
	$install_options = $cache->retrieve('install_options');
	$install_options = phpnuke_unserialize($install_options);

	if(isset($install_options['admininfo']) && isset($install_options['db_info']))
	{
		if($install_options['mode'] == 'upgrade')
		{
			define("OLD_DB", $install_options['admininfo']['old_dbname']);
			define("OLD_DB_PREFIX", $install_options['admininfo']['old_dbprefix']);
		}
		define("NEW_DB", $install_options['db_info']['db_name']);
	}
}

function upgrade_header($step = 1, $progress = 0)
{
	global $db, $nuke_configs, $cache;
	
	if($cache->isCached('install_options'))
	{
		$install_options = $cache->retrieve('install_options');
		$install_options = phpnuke_unserialize($install_options);
	}
	else
		$install_options['mode'] = "install";
	
	$active_1 = ($step == 1) ? "active":"";
	$active_2 = ($step == 2) ? "active":"";
	$active_3 = ($step == 3) ? "active":"";
	$active_4 = ($step == 4) ? "active":"";
	$active_5 = ($step == 5) ? "active":"";
	$active_6 = ($step == 6) ? "active":"";
	$active_7 = ($step == 7) ? "active":"";

	$step_title = (isset($install_options['mode']) && $install_options['mode'] == 'install') ? "اطلاعات مدیر جدید":"اطلاعات نسخه قدیم";
	
	echo"<!DOCTYPE html>
<html lang=\"en\">
<head>
	<title>نصب نيوک فارسي 8.4</title>
	<meta charset=\"UTF-8\">
	<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
	<link href=\"includes/Ajax/jquery/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\">
	<link href=\"includes/Ajax/jquery/bootstrap/css/bootstrap-rtl.css\" rel=\"stylesheet\">
	<link href=\"install/bootstrap/bootstrap-wizard.css\" rel=\"stylesheet\">
	<link href=\"install/chosen/chosen.css\" rel=\"stylesheet\">
	<script src=\"install/js/pwdwidget.js\" type=\"text/javascript\"></script>
	<style type=\"text/css\">
		.have_forum{display:none;}
		.wizard-modal p{margin:0 0 10px;padding:0}#wizard-ns-detail-servers,.wizard-additional-servers{font-size:12px;margin-top:10px;margin-left:15px}#wizard-ns-detail-servers > li, .wizard-additional-servers li{line-height:20px;list-style-type:none}#wizard-ns-detail-servers>li>img{padding-right:5px}.wizard-modal .chzn-container .chzn-results{max-height:150px}.wizard-addl-subsection{margin-bottom:40px}.create-server-agent-key{margin-left:15px;width:90%}
	</style>
	<!--[if lt IE 9]> <script src=\"install/js/html5shiv-3.7.0.js\"></script> <script src=\"install/js/respond-1.3.0.min.js\"></script> <![endif]-->
	<link rel=\"stylesheet prefetch\" href=\"includes/fonts/font-awesome.min.css\">
	<link rel=\"stylesheet\" href=\"install/css/style.css\">
	<script src=\"includes/Ajax/jquery/jquery.min.js\" type=\"text/javascript\"></script>
</head>

<body class=\"modal-open\">
	<div class=\"modal fade wizard in\" style=\"display: block;\" aria-hidden=\"false\">
		<div class=\"modal-dialog wizard-dialog\" style=\"width: 720px; padding-top: 0px;\">
			<div class=\"modal-content wizard-content\" style=\"height: 455px;\">
				<div class=\"modal-header wizard-header\">
					<h3 class=\"modal-title wizard-title\">نصب نيوک</h3>  <span class=\"wizard-subtitle\"></span>
				</div>
				<div class=\"modal-body wizard-body\" style=\"height: 400px;\">
					<div class=\"pull-right wizard-steps\" style=\"height: 400px;\">
						<div class=\"wizard-nav-container\" style=\"height: 310px;\">
							<ul class=\"nav wizard-nav-list\">
								<li class=\"wizard-nav-item $active_1\"><a class=\"wizard-nav-link\"><span class=\"glyphicon glyphicon-chevron-left\"></span> خوش آمديد</a>
								</li>
								<li class=\"wizard-nav-item $active_2\"><a class=\"wizard-nav-link\"><span class=\"glyphicon glyphicon-chevron-left\"></span> اتصال به ديتابيس</a>
								</li>
								<li class=\"wizard-nav-item $active_3\"><a class=\"wizard-nav-link\"><span class=\"glyphicon glyphicon-chevron-left\"></span> بررسي سرور</a>
								</li>
								<li class=\"wizard-nav-item $active_4\"><a class=\"wizard-nav-link\"><span class=\"glyphicon glyphicon-chevron-left\"></span> اطلاعات سايت</a>
								</li>
								<li class=\"wizard-nav-item $active_5\"><a class=\"wizard-nav-link\"><span class=\"glyphicon glyphicon-chevron-left\"></span> $step_title</a>
								</li>
								<li class=\"wizard-nav-item $active_6\"><a class=\"wizard-nav-link\"><span class=\"glyphicon glyphicon-chevron-left\"></span> نصب سيستم</a>
								</li>";
								if(isset($install_options['mode']) && $install_options['mode'] == 'upgrade')
								{
									echo"<li class=\"wizard-nav-item $active_7\"><a class=\"wizard-nav-link\"><span class=\"glyphicon glyphicon-chevron-left\"></span>بروزرسانی</a>
									</li>";
								}
							echo"</ul>
						</div>
						<div class=\"wizard-progress-container\">
							<div class=\"progress progress-striped\">
								<div class=\"progress-bar\" style=\"width: $progress%;\"></div>
							</div>
						</div>
					</div>
					<div class=\"wizard-cards\" style=\"height: 400px;\">";
}

function upgrade_footer()
{
	echo"
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class=\"modal-backdrop fade in\"></div>
</body>
<script src=\"install/chosen/chosen.jquery.js\"></script>
<script src=\"includes/Ajax/jquery/bootstrap/js/bootstrap.min.js\" type=\"text/javascript\"></script>
<script src=\"install/js/prettify.js\" type=\"text/javascript\"></script>

</html>";
}

function upgrade_start()
{
	global $cache;
	
	$cache->flush_caches();
	
	upgrade_header(0);
	echo"
		<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>به صفحه نصب phpnuke فارسی خوش آمدید</h3>
				<div class=\"wizard-input-section\">
					<b>ضمن تشکر از حسن انتخاب شما دوست عزيز ؛ ذکر چند نکته خالي از لطف نيست:</b>
					<br>1 - رعايت کپي رايت ؛ احترام به ناشر و قدرداني از زحمات دوستاني است که ما را در تهيه اين محصول ياري نموده اند.
					<br>2 - مسؤليت هر گونه استفاده از سيستم بر خلاف قوانين مدني کشور به عهده خود شخص بوده و سايت مرجع پذيراي هيچ گونه مسؤليتي نميباشد.<br />
					<span><a href=\"http://www.phpnuke.ir/Forum/viewtopic.php?f=1&t=20\" target=\"_blank\">مطالعه ساير قوانين </a></span>&nbsp;&nbsp;&nbsp;<span style=\"cursor:pointer\" onclick=\"showHelp()\">مطالعه راهنماي نصب</span><br /><br />
					<div class=\"text-center\"><a href=\"install.php?step=db&mode=install\" class=\"btn btn-primary\">نصب نيوک</a> &nbsp; <a href=\"install.php?step=db&mode=upgrade\" class=\"btn btn-primary\">بروزرساني نيوک</a></div>
				</div>
			</div>
		</div>
		<div class=\"wizard-footer\">
			<div class=\"wizard-buttons-container\">
				<div class=\"btn-group-single pull-left\">
					<button class=\"btn wizard-back disabled\" type=\"button\">قبلی</button>
					<button class=\"btn wizard-next btn-primary\" type=\"button\">بعدی</button>
				</div>
			</div>
		</div>";
	upgrade_footer();
}

function step_db()
{
	global $db, $nuke_configs, $mode, $cache;
	
	$install_options = array();
	
	$mode = (isset($mode) && in_array($mode, array("install","upgrade"))) ? $mode:"install";
	
	$install_options['mode'] = $mode;
	
	$cache->store("install_options", phpnuke_serialize($install_options));

	upgrade_header(2, 20);
	echo"
	<form role=\"form\" class=\"form-horizontal\" id=\"nukeform\" action=\"install.php?step=server_check\" method=\"post\">
		<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\" style=\"height: 300px;\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"pn_server_name\">نام سرور :</label>
						<div class=\"col-sm-8\"> 
							<input type=\"text\" class=\"form-control\" id=\"pn_server_name\" name=\"db_fields[db_server_name]\" value=\"localhost\" placeholder=\"آدرس سرور را وارد نمایید\" minlength=\"3\" required data-msg-required=\"آدرس سرور را به درستی وارد نمایید\" />
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"pn_db_name\">نام دیتابیس جدید نیوک:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"pn_db_name\" name=\"db_fields[db_name]\" value=\"\" placeholder=\"نام دیتابیس را وارد نمایید\" required data-msg-required=\"نام دیتابیس را به درستی وارد نمایید\" />
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"pn_db_forumname\">تالار گفتمان:</label>
						<div class=\"col-sm-8\" id=\"have_forum\">
							<input type=\"radio\" name=\"db_fields[db_have_forum]\" value=\"1\" /> فعال
							<input type=\"radio\" name=\"db_fields[db_have_forum]\" value=\"0\" checked /> غیر فعال
						</div>
					</div>
					<div class=\"form-group have_forum\">
						<label class=\"control-label col-sm-4\" for=\"pn_db_forumname\">نام دیتابیس جدید تالار:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"pn_db_forumname\" name=\"db_fields[db_forumname]\" value=\"\" placeholder=\"نام دیتابیس را وارد نمایید\" />
						</div>
					</div>
					<div class=\"form-group have_forum\">
						<label class=\"control-label col-sm-4\" for=\"pn_db_forumpath\">مسیر نصب تالار:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"pn_db_forumpath\" name=\"db_fields[db_forumpath]\" value=\"Forum\" placeholder=\"نام دیتابیس را وارد نمایید\" />
						</div>
					</div>
					<div class=\"form-group have_forum\">
						<label class=\"control-label col-sm-4\" for=\"pn_db_forumcms\">سیستم دیتابیس جدید تالار:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"pn_db_forumcms\" name=\"db_fields[db_forumcms]\" value=\"phpbb\" placeholder=\"نام دیتابیس را وارد نمایید\" />
						</div>
					</div>
					<div class=\"form-group have_forum\">
						<label class=\"control-label col-sm-4\" for=\"pn_db_forumunicode\">collation دیتابیس جدید تالار:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"pn_db_forumunicode\" name=\"db_fields[db_forumunicode]\" value=\"latin1\" placeholder=\"نام دیتابیس را وارد نمایید\" />
						</div>
					</div>
					<div class=\"form-group have_forum\">
						<label class=\"control-label col-sm-4\" for=\"pn_db_forumprefix\">پیشوند جداول دیتابیس جدید تالار بدون _:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"pn_db_forumprefix\" name=\"db_fields[db_forumprefix]\" value=\"phpbb\" placeholder=\"پیشوند جداول تالار را وارد نمایید\" />
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"pn_db_username\">نام کاربری دیتابیس:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"pn_db_username\" name=\"db_fields[db_username]\" value=\"\" placeholder=\"نام کاربری دیتابیس را وارد نمایید\" required data-msg-required=\"نام کاربری را به درستی وارد نمایید\" />
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"pn_server_password\">کلمه عبور دیتابیس:</label>
						<div class=\"col-sm-8\">
							<input type=\"password\" class=\"form-control\" id=\"pn_server_password\" name=\"db_fields[db_password]\" />
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"pn_server_prefix\">پیشوند جداول:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"pn_server_prefix\" name=\"db_fields[db_prefix]\" value=\"\" placeholder=\"پیشفرض nuke\" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class=\"wizard-footer\">
			<div class=\"wizard-buttons-container\">
				<div class=\"btn-group-single pull-left\">
					<a href=\"javascript:history.go(-1)\"><button class=\"btn wizard-back\" type=\"button\">قبلی</button></a>
					<input class=\"btn wizard-next btn-primary\" type=\"submit\" value=\"بعدی\" />
				</div>
			</div>
		</div>
	</form>
	<script>
	$(document).ready(function(){
		$(\"#have_forum\").find('input').click(function(){
			if($(this).val() == 1)
				$(\".have_forum\").show();
			else
				$(\".have_forum\").hide();
		});
	});
	</script>
	";
	upgrade_footer();
}

function step_server_check()
{
	global $db, $nuke_configs, $db_fields, $cache, $pn_dbtype, $pn_dbfetch, $pn_dbcharset;

	if(!$cache->isCached('install_options'))
	{
		upgrade_header(3, 40);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	$import_db = Database::connect($db_fields['db_server_name'], $db_fields['db_username'] , $db_fields['db_password'], $db_fields['db_name'], $pn_dbtype, $pn_dbfetch, $pn_dbcharset, true);

	if(!$import_db)
		$errors[] = "در حال حاضر مشکلي در ارتباط با تنظيمات بانک اطلاعاتي وجود دارد";
	
	$install_options = $cache->retrieve('install_options');
	
	$install_options = phpnuke_unserialize($install_options);
	
	$install_options['db_info'] = $db_fields;
	$install_options['db_info']['db_prefix'] = ($install_options['db_info']['db_prefix'] == '') ? "nuke":$install_options['db_info']['db_prefix'];
	
	$cache->store("install_options", phpnuke_serialize($install_options));
	
	upgrade_header(3, 40);
	
	$errors = array();
	$showerror = 0;
	$configstatus = '';
	
	if(!file_exists("config.php"))
	{
		if(!@rename("config.default.php", "config.php"))
		{
			$errors[] = "فایل config.php وجود ندارد";
			$configstatus = "<span class=\"fail\"><strong>عدم دسترسی</strong></span>";
			$showerror = 1;
		}
	}

	// Check PHP Version
	if(version_compare(PHP_VERSION, '5.4.0', "<"))
	{
		$errors[] = "برای نصب نسخه 8.4 نیوک باید نسخه PHP سرور شما 5.4.0 به بالا باشد. نسخه فعلی : ".PHP_VERSION."";
		$phpversion = "<span class=\"fail\"><strong>".PHP_VERSION."</strong></span>";
		$showerror = 1;
	}
	else
	{
		$phpversion = '<span class="pass">'.PHP_VERSION.'</span>';;
	}
	
	if(function_exists('mb_detect_encoding'))
	{
		$mboptions[] = "Multi-Byte";
	}
	
	if(function_exists('iconv'))
	{
		$mboptions[] = 'iconv';
	}
	
	// Check Multibyte extensions
	if(count($mboptions) < 1)
	{
		$mbstatus = "<span class=\"fail\"><strong>None</strong></span>";
	}
	else
	{
		$mbstatus = "<span class=\"pass\">".implode(', ', $mboptions)."</span>";
	}

	// Check database engines
	if(class_exists('PDO'))
		$supported_dbs = PDO::getAvailableDrivers();
		
	if(count($supported_dbs) < 1)
	{
		$errors[] = 'نیوک برای نصب نیاز به حداقل یکی از انواع دیتابیس ها دارد. سرور شما هیچ دیتابیسی را پشتیبانی نمی کند';
		$dbsupportlist = "<span class=\"fail\"><strong>None</strong></span>";
		$showerror = 1;
	}
	else
	{
		$dbsupportlist = "<span class=\"pass\">".implode(', ', $supported_dbs)."</span>";
	}
	
	// Check config file is writable
	if(!is_writable('config.php'))
	{
		$errors[] = "فایل تنظیمات (config.php) قابل بازنشانی نیست. لطفاً حق دسترسی به این فایل را 777 نمایید";
		$configstatus = "<span class=\"fail\"><strong>عدم دسترسی</strong></span>";
		$showerror = 1;
	}
	else
	{
		$configstatus = "<span class=\"pass\"><strong>دسترسی مجاز</strong></span>";
	}
	@fclose($configwritable);

	// Check cache directory is writable
	if(!is_writable(dirname('cache/')))
	{
		$errors[] = "دسترسی ایجاد فایل در پوشه (cache/) وجود ندارد. لطفاً حق دسترسی به این پوشه را 777 نمایید.";
		$cachestatus = "<span class=\"fail\"><strong>عدم دسترسی</strong></span>";
		$showerror = 1;
	}
	else
	{
		$cachestatus = "<span class=\"pass\"><strong>دسترسی مجاز</strong></span>";
	}

	// Check upload directory is writable
	if(!is_writable(dirname('files/uploads/')))
	{
		$errors[] = "دسترسی ایجاد فایل در پوشه (files/uploads/) وجود ندارد. لطفاً حق دسترسی به این پوشه را 777 نمایید.";
		$uploadsstatus = "<span class=\"fail\"><strong>عدم دسترسی</strong></span>";
		$showerror = 1;
	}
	else
	{
		$uploadsstatus = "<span class=\"pass\"><strong>دسترسی مجاز</strong></span>";
	}

	// Check articles directory is writable
	if(!is_writable(dirname('files/Articles/')))
	{
		$errors[] = "دسترسی ایجاد فایل در پوشه (files/Articles/) وجود ندارد. لطفاً حق دسترسی به این پوشه را 777 نمایید.";
		$Articlesstatus = "<span class=\"fail\"><strong>عدم دسترسی</strong></span>";
		$showerror = 1;
	}
	else
	{
		$Articlesstatus = "<span class=\"pass\"><strong>دسترسی مجاز</strong></span>";
	}

	if($showerror == 1)
	{
		$error_list = implode("<br />", $errors);
		$message = "<div class=\"error\">
		<h3>خطا</h3>
		<p>برای نصب نیوک 8.4 نیاز به تأمین پیش نیاز های زیر دارید. لطفاً ابتدا این اصلاحات را انجام داده سپس اقدام به نصب نیوک کنید</p>
		$error_list
		</div>";
	}
	else
	{
		$message = "سرور شما همه پیش نیازهای نصب نیوک 8.4 را داراست. میتوانید به مرحله بعد بروید";
	}
	
	
	echo"
		<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\" style=\"height: 305px;\">
				<h3>بررسی پیش نیاز های سرور</h3>
				<div class=\"wizard-input-section\">
					<div class=\"border_wrapper\">
						<table class=\"general\" cellspacing=\"0\">
							<tbody>
								<tr class=\"first\">
									<td class=\"first\">نسخه PHP:</td>
									<td class=\"last alt_col\">$phpversion</td>
								</tr>
								<tr class=\"alt_row\">
									<td class=\"first\">دیتابیسهای پشتیبانی شده:</td>
									<td class=\"last alt_col\">$dbsupportlist</td>
								</tr>
								<tr class=\"alt_row\">
									<td class=\"first\">افزونه های پشتیبانی شده ترجمه</td>
									<td class=\"last alt_col\">$mbstatus</td>
								</tr>
								<tr class=\"alt_row\">
									<td class=\"first\">دسترسی فایل config.php:</td>
									<td class=\"last alt_col\">$configstatus</td>
								</tr>
								<tr>
									<td class=\"first\">دسترسی پوشه cache:</td>
									<td class=\"last alt_col\">$cachestatus</td>
								</tr>
								<tr class=\"alt_row\">
									<td class=\"first\">دسترسی پوشه files/uploads/:</td>
									<td class=\"last alt_col\">$uploadsstatus</td>
								</tr>
								<tr class=\"last\">
									<td class=\"first\">دسترسی پوشه files/Articles/:</td>
									<td class=\"last alt_col\">$Articlesstatus</td>
								</tr>
							</tbody>
						</table>
						<br />$message
					</div>
				</div>
			</div>
		</div>
		<div class=\"wizard-footer\">
			<div class=\"wizard-buttons-container\">
				<div class=\"btn-group-single pull-left\">
					<a href=\"javascript:history.go(-1)\"><button class=\"btn wizard-back\" type=\"button\">قبلی</button></a>";
					if($showerror == 1)
						echo"<button class=\"btn wizard-next btn-primary disabled\" type=\"button\">بعدی</button>";
					else
						echo"<a href=\"install.php?step=siteinfo\"><button class=\"btn wizard-next btn-primary\" type=\"button\">بعدی</button></a>";
				echo"</div>
			</div>
		</div>";
	upgrade_footer();
}

function step_siteinfo()
{
	global $db, $nuke_configs, $mode, $cache, $Req_URL;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(4, 60);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	$install_options = $cache->retrieve('install_options');
	$install_options = phpnuke_unserialize($install_options);
	
	upgrade_header(4, 60);
	echo"
	<form role=\"form\" class=\"form-horizontal\" id=\"nukeform\" action=\"install.php?step=admin_info\" method=\"post\">
		<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات سایت</h3>
				<div class=\"wizard-input-section\">
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"nukeurl\">آدرس سایت :</label>
						<div class=\"col-sm-8\"> 
							<input type=\"text\" class=\"form-control\" id=\"nukeurl\" name=\"install_fields[nukeurl]\" value=\"$Req_URL\" placeholder=\"آدرس سایت را وارد نمایید\" required data-msg-required=\"آدرس وارد شده معتبر نمیباشد\" />
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"sitename\">نام سایت :</label>
						<div class=\"col-sm-8\"> 
							<input type=\"text\" class=\"form-control\" id=\"sitename\" name=\"install_fields[sitename]\" value=\"\" placeholder=\"نام سایت را وارد نمایید\" required data-msg-required=\"نام سایت را به درستی وارد نمایید\" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class=\"wizard-footer\">
			<div class=\"wizard-buttons-container\">
				<div class=\"btn-group-single pull-left\">
					<a href=\"javascript:history.go(-1)\"><button class=\"btn wizard-back\" type=\"button\">قبلی</button></a>
					<input class=\"btn wizard-next btn-primary\" type=\"submit\" value=\"بعدی\" />
				</div>
			</div>
		</div>
	</form>";
	upgrade_footer();
}

function step_admin_info()
{
	global $db, $nuke_configs, $install_fields, $cache;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(5, 80);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	$install_options = $cache->retrieve('install_options');
	$install_options = phpnuke_unserialize($install_options);
	$install_options['siteinfo'] = $install_fields;
	
	$cache->store("install_options", phpnuke_serialize($install_options));
	$step_title = ($install_options['mode'] == 'install') ? "اطلاعات مدیر جدید":"اطلاعات نسخه قدیم";
	upgrade_header(5, 80);
	echo"
	<form role=\"form\" class=\"form-horizontal\" id=\"nukeform\" action=\"install.php?step=install\" method=\"post\">
		<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>$step_title</h3>
				<div class=\"wizard-input-section\">";
					if($install_options['mode'] == 'install')
					{
					echo"<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"admin_id\">نام کاربری مدیر :</label>
						<div class=\"col-sm-8\"> 
							<input type=\"text\" class=\"form-control\" id=\"admin_id\" name=\"install_fields[aid]\" value=\"admin\" placeholder=\"نام کاربری مدیر را وارد نمایید\" maxlength=\"25\" required data-msg-required=\"نام کاربری مدیر را به درستی وارد نمایید\" />
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"admin_realname\">نام نمایشی مدیر :</label>
						<div class=\"col-sm-8\"> 
							<input type=\"text\" class=\"form-control\" id=\"admin_realname\" name=\"install_fields[realname]\" placeholder=\"نام نمایشی مدیر را وارد نمایید\" />
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"admin_password\">کلمه عبور:</label>
						<div class=\"col-sm-8\">
							<div class='pwdwidgetdiv' id='thepwddiv'></div>
							<script  type=\"text/javascript\" >
								var pwdwidget = new PasswordWidget('thepwddiv','install_fields[pwd]');
								pwdwidget.MakePWDWidget();
							</script>
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"admin_email\">ایمیل :</label>
						<div class=\"col-sm-8\"> 
							<input type=\"email\" class=\"form-control\" id=\"admin_email\" name=\"install_fields[email]\" placeholder=\"ایمیل مدیر را وارد نمایید\" />
						</div>
					</div>";
					}
					else
					{
					echo"
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"admin_password\">کلمه عبور مدیر قبلی:</label>
						<div class=\"col-sm-8\">
							<input type=\"password\" class=\"form-control\" id=\"admin_password\" name=\"install_fields[pwd]\" />
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"old_dbname\">نام دیتابیس قدیم:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"old_dbname\" name=\"install_fields[old_dbname]\" value=\"\" placeholder=\"نام دیتابیس قدیم را وارد نمایید\" required data-msg-required=\"وارد کردن نام دیتابیس الزامی است\"  />
						</div>
					</div>
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"old_dbprefix\">پیشوند جداول دیتابیس قدیم:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"old_dbprefix\" name=\"install_fields[old_dbprefix]\" value=\"nuke\"  placeholder=\"پیشوند جداول دیتابیس قدیم را وارد نمایید\" required data-msg-required=\"وارد کردن پیشوند جداول دیتابیس الزامی است\" />
						</div>
					</div>";
					}
				echo"
					<div class=\"form-group\">
						<label class=\"control-label col-sm-4\" for=\"admin_filename\">نام فایل مدیریت:</label>
						<div class=\"col-sm-8\">
							<input type=\"text\" class=\"form-control\" id=\"admin_filename\" name=\"install_fields[admin_filename]\" value=\"admin\"  placeholder=\"پیشوند جداول دیتابیس قدیم را وارد نمایید\" required data-msg-required=\"وارد کردن پیشوند جداول دیتابیس الزامی است\" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class=\"wizard-footer\">
			<div class=\"wizard-buttons-container\">
				<div class=\"btn-group-single pull-left\">
					<a href=\"javascript:history.go(-1)\"><button class=\"btn wizard-back\" type=\"button\">قبلی</button></a>
					<input class=\"btn wizard-next btn-primary\" type=\"submit\" value=\"بعدی\" />
				</div>
			</div>
		</div>
	</form>";
	upgrade_footer();
}

function step_install()
{
	global $db, $nuke_configs, $install_fields, $cache, $pn_dbtype, $pn_dbfetch, $pn_dbcharset;

	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	$install_options = $cache->retrieve('install_options');
	$install_options = phpnuke_unserialize($install_options);
	$install_options['admininfo'] = $install_fields;
	
	if($install_options['admininfo']['admin_filename'] != '' && $install_options['admininfo']['admin_filename'] != 'admin' && file_exists("admin.php") && !file_exists($install_options['admininfo']['admin_filename'].".php"))
	{
		if(!rename("admin.php", $install_options['admininfo']['admin_filename'].".php"))
		{
			upgrade_header(7, 95);
			echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
				<div class=\"wizard-card\" data-cardname=\"group\">
					<h3>اطلاعات پایگاه داده</h3>
					<div class=\"wizard-input-section\">
						امکان تغییر نام فایل مدیریت وجود ندارد
					</div>
				</div>
			</div>";
			upgrade_footer();
			die();
		}
	}
	
	global $pn_salt;
	$pn_salt = change_config_content($install_options);

	$cache = new Cache();
	$cache->flush_caches();
	$cache->store("install_options", phpnuke_serialize($install_options));
	
	$errors = array();
	
	require_once 'admin/modules/modules/mysql_backup.php';

	$DB_obj = new BackupMySQL();
	$DB_obj->database = $install_options['db_info']['db_name'];

	if($install_options['db_info']['db_server_name'] == '' || $install_options['db_info']['db_username'] == '' || $install_options['db_info']['db_name'] == '')
		$errors[] = "اطلاعات ارسال شده درست نمیباشد";
	
	$import_db = Database::connect($install_options['db_info']['db_server_name'], $install_options['db_info']['db_username'] , $install_options['db_info']['db_password'], $install_options['db_info']['db_name'], $pn_dbtype, $pn_dbfetch, $pn_dbcharset, true);

	if(!$import_db)
		$errors[] = "در حال حاضر مشکلي در ارتباط با تنظيمات بانک اطلاعاتي وجود دارد";
	
	$filename = 'install/nuke.sql';
	
	$DB_obj->db = $import_db;
	
	$default_tables = array("`nuke_admins_menu`", "`nuke_articles`", "`nuke_authors`", "`nuke_banned_ip`", "`nuke_blocks`", "`nuke_blocks_boxes`", "`nuke_blocks_themes`", "`nuke_bookmarksite`", "`nuke_categories`", "`nuke_comments`", "`nuke_config`", "`nuke_feedbacks`", "`nuke_headlines`", "`nuke_languages`", "`nuke_log`", "`nuke_modules`", "`nuke_mtsn`", "`nuke_mtsn_ipban`", "`nuke_nav_menus`", "`nuke_nav_menus_data`", "`nuke_points_groups`", "`nuke_referrer`", "`nuke_reports`", "`nuke_scores`", "`nuke_sessions`", "`nuke_statistics`", "`nuke_statistics_counter`", "`nuke_surveys`", "`nuke_surveys_check`", "`nuke_tags`", "`nuke_users`");

	$import_db->query("DROP TABLE IF EXISTS ".implode(", ", $default_tables).";");
	
	if($install_options['db_info']['db_prefix'] != 'nuke')
	{
		$rename_query = array();
		foreach($default_tables as $key => $default_table)
		{
			$default_table_name = $default_table;
			$new_table_name = str_replace("nuke_", $install_options['db_info']['db_prefix']."_", $default_table);
			$rename_query[] = "$default_table_name TO $new_table_name";
			$default_tables[$key] = $new_table_name;
		}
		$import_db->query("DROP TABLE IF EXISTS ".implode(", ", $default_tables).";");
	}
		

	$DB_obj->db_connection_charset = $pn_dbcharset;
	
	$result = $DB_obj->read_sql_url($filename, 1, 0, ";", 0);
	//$result = objectToArray(json_decode($result));
	
	// Get a listing of all tables
	//$tables_result = $import_db->query("SELECT TABLE_NAME FROM `information_schema`.`TABLES` WHERE TABLE_SCHEMA =  '".$install_options['db_info']['db_name']."'");
	//$tables_result = $import_db->query("SHOW TABLES");
	
	/*if(!empty($tables_result))
	{
		$rename_query = array();
		// Loop through all tables
		foreach ($tables_result as $row)
		{		
			//$old_table = $row['Tables_in_'.$install_options['db_info']['db_name']];
			$old_table = $row['TABLE_NAME'];
			echo $old_table;continue;
			// Preliminary check: Is the old table prefix correct?
			if(!preg_match('/^nuke_/', $old_table))
				continue;
				
			// Preliminary check: Is the old table prefix the same as the new one?
			if(preg_match('/^'.$install_options['db_info']['db_prefix']."_".'/', $old_table))
				continue;
			// Construct the new table prefix
			$new_table = preg_replace('/^nuke_/', $install_options['db_info']['db_prefix']."_", $old_table);
			$rename_query[] = "`$old_table` TO `$new_table`";
		}
	}*/
	
	if($install_options['db_info']['db_prefix'] != 'nuke')
	{
		//$rename_query = implode(", ", $rename_query);
		// Rename the actual table
		foreach($rename_query as $rename_q)
			$import_db->query("RENAME TABLE ".$rename_q);
	}
	
	upgrade_header(6, (($install_options['mode'] == 'install') ? 100:90));

	echo"
	<form role=\"form\" class=\"form-horizontal\" id=\"nukeform\" action=\"install.php?step=install\" method=\"post\">
		<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>نصب سیستم</h3>
				<div class=\"wizard-input-section\">
					<div class=\"alert alert-success\">
						<span class=\"create-server-name\"></span>جداول نیوک شما <strong>با موفقیت نصب شد.</strong>
					</div>";
					/*<table width=\"100%\">
						<tr>
							<th class=\"text-center\" style=\"width:40%\">عنوان</th>
							<th class=\"text-center\" style=\"width:15%\">در حال کار</th>
							<th class=\"text-center\" style=\"width:15%\">انجام شده</th>
							<th class=\"text-center\" style=\"width:15%\">باقیمانده</th>
							<th class=\"text-center\" style=\"width:15%\">کل</th>
						</tr>
						<tr>
							<td>تعداد ردیف</td>
							<td id=\"line-session\" align=\"center\">".$result['lines_this']."</td>
							<td id=\"line-done\" align=\"center\">".$result['lines_done']."</td>
							<td id=\"line-togo\" align=\"center\">".$result['lines_togo']."</td>
							<td id=\"line-total\" align=\"center\">".$result['lines_tota']."</td>
						</tr>
						<tr>
							<td>تعداد کوئری</td>
							<td id=\"query-session\" align=\"center\">".$result['queries_this']."</td>
							<td id=\"query-done\" align=\"center\">".$result['queries_done']."</td>
							<td id=\"query-togo\" align=\"center\">".$result['queries_togo']."</td>
							<td id=\"query-total\" align=\"center\">".$result['queries_tota']."</td>
						</tr>
						<tr>
							<td>حجم اطلاعات Byte</td>
							<td id=\"byte-session\" align=\"center\" dir=\"ltr\">".$result['bytes_this']."</td>
							<td id=\"byte-done\" align=\"center\" dir=\"ltr\">".$result['bytes_done']."</td>
							<td id=\"byte-togo\" align=\"center\" dir=\"ltr\">".$result['bytes_togo']."</td>
							<td id=\"byte-total\" align=\"center\" dir=\"ltr\">".$result['bytes_tota']."</td>
						</tr>
					</table><br /><br />*/
					echo"<p align=\"center\">";
					if($install_options['mode'] == 'install')
					{
					echo"
					<meta http-equiv=\"refresh\" content=\"5;URL='install.php?op=final'\" />";
					}
					else
					{
					echo"
					<a class=\"btn btn-default\" href=\"install.php?op=first\">ادامه بروزرسانی</a>";
					}
				echo"</p>
				</div>
			</div>
		</div>
	</form>";
	upgrade_footer();
}

function change_config_content($install_options)
{

$pn_salt = random_str(15, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$~%^&*()_-+|');

$file_contents = '<?php

######################################################################
# PHP-NUKE: Advanced Content Management System
# ============================================
#
# Copyright (c) 2006 by Francisco Burzi
# http://phpnuke.org
#
# This program is free software. You can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License.
######################################################################

if (stristr(htmlentities($_SERVER["PHP_SELF"]), "config.php")) {
    Header("Location: index.php");
    die();
}

######################################################################
# Database & System Config
#
# dbhost:       SQL Database Hostname
# dbuname:      SQL Username
# dbpass:       SQL Password
# dbname:       SQL Database Name
# $prefix:      Your Database table\'s prefix
# $user_prefix: Your Users\' Database table\'s prefix (To share it)
# $dbtype:      Your Database Server type. Supported servers are:
#               MySQL, mysql4, sqlite, postgres, mssql, oracle,
#               msaccess, db2 and mssql-odbc
#               Be sure to write it exactly as above, case SeNsItIvE!
# $sitekey: Security Key. CHANGE it to whatever you want, as long
#               as you want. Just don\'t use quotes.
# $gfx_chk: Set the graphic security code on every login screen,
#		You need to have GD extension installed:
#		0: No check
#		1: Administrators login only
#		2: Users login only
#		3: New users registration only
#		4: Both, users login and new users registration only
#		5: Administrators and users login only
#		6: Administrators and new users registration only
#		7: Everywhere on all login options (Admins and Users)
#		NOTE: If you aren\'t sure set this value to 0
# $subscription_url : If you manage subscriptions on your site, you
#                     must write here the url of the subscription
#                     information/renewal page. This will send by
#                     email if set.
# $admin_file: Administration panel filename. "admin" by default for
#		   "admin.php". To improve security please rename the file
#              "admin.php" and change the $admin_file value to the
#              new filename (without the extension .php)
# $tipath:      Path to where the topic images are stored.
# $nuke_editorr: Turn On/Off the WYSIWYG text editor
#                   0: Off, will use the default simple text editor
#                   1: On, will use the full featured text editor
# $nuke_editorr: Debug control to see PHP generated errors.
#                   false: Do not show errors
#                   True See all errors ( No notices )t editor
######################################################################

$pn_dbhost = "'.$install_options['db_info']['db_server_name'].'";
$pn_dbuname = "'.$install_options['db_info']['db_username'].'";
$pn_dbpass = \''.$install_options['db_info']['db_password'].'\';
$pn_dbname = "'.$install_options['db_info']['db_name'].'";
$pn_prefix = "'.$install_options['db_info']['db_prefix'].'";
$pn_dbtype = "mysql";
$pn_dbfetch = PDO::FETCH_ASSOC;
$pn_dbcharset = "utf8mb4";

$pn_sitekey = "'.random_str(40).'";
$pn_subscription_url = "";
$pn_tipath = "images/topics/";
$pn_cache_type = "MySQL";
$admin_file = "'.$install_options['admininfo']['admin_filename'].'";
$pn_salt = \''.$pn_salt.'\';

/*********************************************************************/
/* You finished to configure the Database. Now you can change all    */
/* you want in the Administration Section.   To enter just launch    */
/* you web browser pointing to http://yourdomain.com/admin.php       */
/* (Change xxxxxx.xxx to your domain name, for example: phpnuke.org) */
/*                                                                   */
/* Remeber to go to Settings section where you can configure your    */
/* new site. In that menu you can change all you need to change.     */
/*                                                                   */
/* Congratulations! now you have an automated news portal!           */
/* Thanks for choose PHP-Nuke: The Future of the Web                 */
/*********************************************************************/

// DO NOT TOUCH ANYTHING BELOW THIS LINE UNTIL YOU KNOW WHAT YOU\'RE DOING

$reasons = array("As Is","Offtopic","Flamebait","Troll","Redundant","Insighful","Interesting","Informative","Funny","Overrated","Underrated");
$badreasons = 4;
$AllowableHTML = array("img"=>2,"tr"=>1,"td"=>2,"table"=>2,"div"=>2,"p"=>2,"hr"=>1,"b"=>1,"i"=>1,"strike"=>1,"u"=>1,"font"=>2,"a"=>2,"em"=>1,"br"=>1,"strong"=>1,"blockquote"=>1,"tt"=>1,"li"=>1,"ol"=>1,"ul"=>1,"center"=>1);
$CensorList = array("fuck","cunt","fucker","fucking","pussy","cock","c0ck","cum","twat","clit","bitch","fuk","fuking","motherfucker");

//***************************************************************
// IF YOU WANT TO LEGALY REMOVE ANY COPYRIGHT NOTICES PLAY FAIR AND CHECK: http://phpnuke.org/modules.php?name=Commercial_License
// COPYRIGHT NOTICES ARE GPL SECTION 2(c) COMPLIANT AND CAN\'T BE REMOVED WITHOUT PHP-NUKE\'S AUTHOR WRITTEN AUTHORIZATION
// THE USE OF COMMERCIAL LICENSE MODE FOR PHP-NUKE HAS BEEN APPROVED BY THE FSF (FREE SOFTWARE FOUNDATION)
// YOU CAN REQUEST INFORMATION ABOUT THIS TO GNU.ORG REPRESENTATIVE. THE EMAIL THREAD REFERENCE IS #213080
// YOU\'RE NOT AUTHORIZED TO CHANGE THE FOLLOWING VARIABLE\'S VALUE UNTIL YOU ACQUIRE A COMMERCIAL LICENSE
// (http://phpnuke.org/modules.php?name=Commercial_License)
//***************************************************************
$commercial_license = 0;
?>';

    $fp = fopen('config.php', 'w');
    fputs($fp, $file_contents);
    fclose($fp);
	return $pn_salt;
}

function upgrade_progress_output($pagetitle = '', $total_rows = 0, $fetched_rows = 0, $start = 0, $finish_page = '', $in_progress_page = '', $total_progress = 0, $total_proccess = 500)
{
	global $nuke_configs, $cache, $install_options;

	$percent = ($fetched_rows == 0 || ($fetched_rows > 0 && $fetched_rows < $total_proccess && $start != 0) || ($fetched_rows > 0 && $fetched_rows == $total_rows)) ? 100:(int)((($fetched_rows+$start)/$total_rows)*100);
	
	upgrade_header(7, 95);
	echo"<div class=\"wizard-card\">
		<h3>بروزرسانی سیستم</h3>";
		
		if($finish_page != '')
		{
		echo"<div class=\"wizard-input-section\">
			<p>سیستم در حال نصب می باشد شکیبا باشید<br /><br /></p>";
			
			if($fetched_rows == 0 || ($fetched_rows > 0 && $fetched_rows < $total_proccess && $start != 0) || ($fetched_rows > 0 && $fetched_rows == $total_rows))
			{
				if($finish_page != '')
					echo"<meta http-equiv=\"refresh\" content=\"5;URL='$finish_page'\" />";
			}
			else
			{
				if($in_progress_page != '')
					echo"<meta http-equiv=\"refresh\" content=\"5;URL='$in_progress_page'\" />";
			}
			echo"$pagetitle
			<div class=\"progress\">
				<div class=\"progress-bar progress-bar-info progress-bar-striped active\" role=\"progressbar\" aria-valuenow=\"$percent\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:$percent%\"></div>
			</div>
			کل عملیات بروزرسانی
			<div class=\"progress\">
				<div class=\"progress-bar progress-bar-primary progress-bar-striped active\" role=\"progressbar\" aria-valuenow=\"$total_progress\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:$total_progress%\"></div>
			</div>
		</div>";
		}
		else
		{
			echo"<div class=\"wizard-success\">
				<div class=\"alert alert-success\">
					<span class=\"create-server-name\"></span>نیوک شما <strong>با موفقیت ".(($install_options['mode'] == 'install') ? "نصب":"بروزرسانی")." شد.</strong>
				</div> 
				<a class=\"btn btn-default\" href=\"".$install_options['admininfo']['admin_filename'].".php\">ورود به مدیریت</a>  
				<a class=\"btn btn-default\" href=\"".$install_options['siteinfo']['nukeurl']."\">نمایش سایت</a>
				<a class=\"btn btn-default\" target=\"_blank\" href=\"".$install_options['siteinfo']['nukeurl'].$install_options['db_info']['db_forumpath']."/install/\">".(($install_options['mode'] == 'install') ? "نصب":"بروزرسانی")." تالار گفتمان</a>
			</div>";
		}
	echo"</div>";
	upgrade_footer();

}

function upgrade_first()
{
	global $db, $nuke_configs, $cache, $install_options, $install_fields, $pn_dbcharset;
	
	$languages_list = array();
	$all_languages = get_dir_list('language', "files", true);
	foreach($all_languages as $language)
	{
		if($language == 'index.html' || $language == '.htaccess' || $language == 'alphabets.php') continue;
		$language = str_replace(".php", "", $language);
		$languages_list[] = $language;
	}
	
	$default_admin = "admin";
	$default_admin_pwds = array();
	// update nuke_authors
	$insert_query = array();
	$result = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_authors`");
	
	if(isset($install_fields) && isset($install_fields['pwd']) && !empty($install_fields['pwd']))
	{
		if(!$cache->isCached('install_options'))
		{
			upgrade_header(7, 95);
			echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
				<div class=\"wizard-card\" data-cardname=\"group\">
					<h3>اطلاعات پایگاه داده</h3>
					<div class=\"wizard-input-section\">
						اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
					</div>
				</div>
			</div>";
			upgrade_footer();
			die();
		}
		
		$install_options = $cache->retrieve('install_options');
		$install_options = phpnuke_unserialize($install_options);
		$install_options['admininfo']['pwd'] = $install_fields['pwd'];
		$cache->store("install_options", phpnuke_serialize($install_options));
	}	
	
	if(intval($result->count()) > 0)
	{
		$rows = $result->results();
		
		foreach($rows as $row)
		{
			if($row['name'] == "God")
			{
				$default_admin_pwds[] = $row['pwd'];
				$default_admin = $row['aid'];
				$cache->store('default_admin', $default_admin);
			}
			$insert_query[] = array($row['aid'],$row['name'],$row['url'],$row['email'],$row['pwd'],$row['counter'],$row['radminsuper'],$row['admlanguage'],$row['aadminsuper']);
		}
		
		if($install_options['mode'] == 'upgrade' && !empty($default_admin_pwds) && !in_array(md5($install_options['admininfo']['pwd']), $default_admin_pwds))
		{
			upgrade_header(7, 95);
			echo"
			<form role=\"form\" class=\"form-horizontal\" id=\"nukeform\" action=\"install.php?op=first\" method=\"post\">
				<div class=\"wizard-card-container\" style=\"height: 326px;\">
					<div class=\"wizard-card\" data-cardname=\"group\">
						<h3>بروزرسانی سیستم</h3>
						<div class=\"wizard-error\">
							<div class=\"alert alert-danger\">	<strong>خطا :</strong> کلمه عبور قدیم مدیریت صحیح نمی باشد .</div>
						</div>
						<div class=\"wizard-input-section\">
							<div class=\"form-group\">
								<label class=\"control-label col-sm-4\" for=\"admin_password\">کلمه عبور مدیر قبلی:</label>
								<div class=\"col-sm-8\">
									<input type=\"password\" class=\"form-control\" id=\"admin_password\" name=\"install_fields[pwd]\" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class=\"wizard-footer\">
					<div class=\"wizard-buttons-container\">
						<div class=\"btn-group-single pull-left\">
							<a href=\"javascript:history.go(-1)\"><button class=\"btn wizard-back\" type=\"button\">قبلی</button></a>
							<input class=\"btn wizard-next btn-primary\" type=\"submit\" value=\"بعدی\" />
						</div>
					</div>
				</div>
			</form>";
			upgrade_footer();
			die();
		}
	
		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query) && !empty($insert_query))
			$db->table(AUTHORS_TABLE)->multiinsert(array("aid","name","url","email","pwd","counter","radminsuper","admlanguage","aadminsuper"),$insert_query);
	}
	
	// update nuke_admins_menu
	/*$db->query("set names 'latin1'");
	$insert_query = array();
	$result = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_admins_menu`");
	if(intval($db->count()) > 0)
	{
		$rows = $result->results();
		foreach($rows as $row)
		{
			$insert_query[] = array($row['amid'],$row['atitle'],$row['aadmins']);
		}
		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query) && !empty($insert_query))
			$db->table(ADMINS_MENU_TABLE)->multiinsert(array("amid","atitle","admins"),$insert_query);
	}*/

	
	// update nuke_blocks AND nuke_messages
	$db->query("set names 'latin1'");
	$insert_query1 = array();
	$box_blocks_data = array();
	$top_center_weight = 1;
	$last_bid = 0;
	$result = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_blocks` ORDER BY bposition ASC, weight ASC");
	if(intval($result->count()) > 0)
	{
		$rows = $result->results();
		foreach($rows as $row)
		{
			if($row['blockfile'] != '' && !file_exists("blocks/".$row['blockfile'].""))
				continue;
			
			$last_bid = max($last_bid, $row['bid']);
			
			$lang_titles = array();
			$result2 = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_blocks_titles` WHERE bid = '".$row['bid']."'");
			if(intval($result2->count()) > 0)
			{
				$rows2 = $result2->results();
				foreach($rows2 as $row2)
				{
					if(isset($row2['btitle']))
						$lang_titles[$row2['lang']] = $row2['btitle'];
				}
			}
			
			$box_id = "right";
			
			switch($row['bposition'])
			{
				case"r":
					$box_id = "left";
				break;
				case"c":
					$box_id = "topcenter";
				break;
				case"d":
					$box_id = "bottomcenter";
				break;
				default:
					$box_id = "right";
				break;
			}
			
			$top_center_weight = max($top_center_weight, $row['weight']);
			
			$box_blocks_data[$box_id][$row['bid']] = array(
				"title" => $row['title'],
				"lang_titles" => ((!empty($lang_titles)) ? phpnuke_serialize($lang_titles):""),
				"blanguage" => $row['blanguage'],
				"weight" => $row['weight'],
				"active" => $row['active'],
				"time" => $row['time'],
				"permissions" => $row['view'],
				"publish" => 0,
				"expire" => 0,
				"action" => "d",
				"theme_block" => '',
			);
			
			$insert_query1[] = array($row['bid'],$row['title'],$row['content'],$row['url'],$row['refresh'],$row['blockfile']);
		}
		
		$result = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_message`");
		if(intval($result->count()) > 0)
		{	
			$rows = $result->results();
			foreach($rows as $row)
			{
				$lang_titles = array();
				foreach($languages_list as $languages_name)
				{
					$lang_titles[$languages_name] = $row['title'];
				}
				
				$last_bid++;
				$box_id = "topcenter";
				$top_center_weight++;
				$box_blocks_data[$box_id][$last_bid] = array(
					"title" => $row['title'],
					"lang_titles" => ((!empty($lang_titles)) ? phpnuke_serialize($lang_titles):""),
					"blanguage" => $row['mlanguage'],
					"weight" => $top_center_weight,
					"active" => $row['active'],
					"time" => $row['date'],
					"permissions" => $row['view'],
					"publish" => 0,
					"expire" => $row['expire'],
					"action" => "d",
					"theme_block" => '',
				);
				
				$insert_query1[] = array($last_bid, $row['title'],$row['content'], '', '', '');
			}
		}
		
		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query1) && !empty($insert_query1))
			$blocks_insert = $db->table(BLOCKS_TABLE)->multiinsert(array("bid","title","content","url","refresh","blockfile"),$insert_query1);

		if($blocks_insert && isset($box_blocks_data) && !empty($box_blocks_data))
		{
			foreach($box_blocks_data as $box_id => $box_block_data)
			{
				$db->table(BLOCKS_BOXES_TABLE)
					->where('box_id', $box_id)
					->update(array(
						'box_blocks' => implode(",", array_keys($box_block_data)),
						'box_blocks_data' => phpnuke_serialize($box_block_data),
					));
			}
		}
	}
	
	// update nuke_categories
	$db->query("set names 'latin1'");
	$insert_query = array();
	$result = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_topics`");
	if(intval($result->count()) > 0)
	{
		$rows = $result->results();
		foreach($rows as $row)
		{
			$insert_query[] = array($row['topicid'],0,'Articles',$row['topicname'],$row['topicimage'],$row['topictext'],$row['parent_id']);
		}
		$insert_query[] = array('Null', 1, 'Articles', 'uncategorized', '', 'uncategorized', 0);

		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query) && !empty($insert_query))
			$db->table(CATEGORIES_TABLE)->multiinsert(array("catid","type","module","catname","catimage","cattext","parent_id"),$insert_query);
	}

	// update nuke_config
	$when_query = array();
	$insert_query = array();
	$feedback_configs = '{"custom_fields":{"1":{"name":"age","title":"\u0633\u0646","desc":"\u0633\u0646 \u062e\u0648\u062f \u0631\u0627 \u0628\u0647 \u0645\u0627 \u0628\u06af\u0648\u06cc\u06cc\u062f","required":"0","data-rule":"number","data-msg":"\u0633\u0646 \u0634\u0645\u0627 \u06a9\u0645\u062a\u0631 \u0627\u0632 \u062d\u062f \u0645\u062c\u0627\u0632 \u0627\u0633\u062a"},"2":{"name":"phone","title":"\u0645\u0648\u0628\u0627\u06cc\u0644","desc":"\u0634\u0645\u0627\u0631\u0647 \u0645\u0648\u0628\u0627\u06cc\u0644 \u062e\u0648\u062f \u0631\u0627 \u0648\u0627\u0631\u062f \u0646\u0645\u0627\u06cc\u06cc\u062f","required":"1","data-rule":"number","data-msg":"\u0634\u0645\u0627\u0631\u0647 \u0648\u0627\u0631\u062f \u0634\u062f\u0647 \u0627\u0634\u062a\u0628\u0627\u0647 \u0627\u0633\u062a"}},"letreceive":1,"delay":600,"notify":{"sms":1},"description":"<p>\u0628\u0647 \u0633\u06cc\u0633\u062a\u0645 \u0645\u062f\u06cc\u0631\u06cc\u062a \u0645\u062d\u062a\u0648\u0627\u06cc \u0646\u06cc\u0648\u06a9 \u062e\u0648\u0634 \u0622\u0645\u062f\u06cc\u062f............<\/p>","phone":"05123456789","mobile":"09123456789","fax":"05123456789","address":"\u0627\u06cc\u0631\u0627\u0646 - \u062e\u0631\u0627\u0633\u0627\u0646 \u0631\u0636\u0648\u06cc - \u06a9\u0634\u0648\u0631 \u0645\u0634\u0647\u062f \u0645\u0642\u062f\u0633 ","meta_description":"\u0628\u062e\u0634 \u0627\u0631\u062a\u0628\u0627\u0637 \u0628\u0627 \u0645\u0627 \u0633\u06cc\u0633\u062a\u0645 \u0645\u062f\u06cc\u0631\u06cc\u062a \u0645\u062d\u062a\u0648\u0627\u06cc \u0646\u06cc\u0648\u06a9 \u0641\u0627\u0631\u0633\u06cc \u062c\u062f\u06cc\u062f ","meta_keywords":["\u0627\u0631\u062a\u0628\u0627\u0637 \u0628\u0627 \u0645\u0627","\u062a\u0645\u0627\u0633 \u0628\u0627 \u0645\u0627","\u0641\u06cc\u062f \u0628\u06a9"],"map_active":"1","google_api":"","map_position":"36.28795445718431,59.61575198173523"}';
	$feedback_configs = objectToArray(json_decode(($feedback_configs)));
	$feedback_configs['depts'] = array();

	$db->query("set names 'latin1'");
	$result = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_config`");
	if(intval($result->count()) > 0)
	{
		$rows = $result->results();
		$feedback_depts = array();
		$result2 = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_feedback_depts`");
		if(intval($result2->count()) > 0)
		{
			$rows2 = $result2->results();

			if(!empty($rows2))
			{
				foreach($rows2 as $row2)
					$feedback_configs['depts'][$row2['did']] = array($row2['dname'], $row2['demail'], $row2['dresponsibility']);
			}
			$feedback_configs = phpnuke_serialize($feedback_configs);
			$when_query['feedbacks'] = "WHEN config_name = 'feedbacks' THEN :".$feedback_configs."";
			$when_query_val[":feedbacks"] = $feedback_configs;
			$params_index[] = "?";
			$query_IN[] = 'feedbacks';
		}
		
		if(isset($rows[0]) && !empty($rows[0]))
		{
			foreach($rows[0] as $config_key => $config_val)
			{
				$when_query[$config_key] = "WHEN config_name = '".$config_key."' THEN :".$config_key."";
				$when_query_val[":".$config_key.""] = $config_val;
				$params_index[] = "?";
				$query_IN[] = $config_key;
			}
		
			if(!empty($when_query))
			{
				$when_query = implode("\n", $when_query);
				$params_index = implode(" , ", $params_index);
				$params = array_merge($when_query_val, $query_IN);
				
				$db->query("set names '$pn_dbcharset'");
				$db->query("UPDATE ".CONFIG_TABLE." SET config_value = CASE 
					$when_query
				END
				WHERE config_name IN ($params_index)", $params);
			}
			$nuke_configs = get_cache_file_contents('nuke_configs');	
			
			foreach($rows[0] as $config_key => $config_val)
			{
				if(!array_key_exists($config_key, $nuke_configs))
				{
					$insert_query[] = array($config_key, $config_val);
				}
			}
			
			$db->query("set names '$pn_dbcharset'");
			if(isset($insert_query) && !empty($insert_query))
				$db->table(CONFIG_TABLE)->multiinsert(array("config_name","config_value"),$insert_query);
		}
	}

	// update nuke_headlines
	$db->query("set names 'latin1'");
	$insert_query = array();
	$result = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_headlines`");
	if(intval($result->count()) > 0)
	{
		$rows = $result->results();
		$hid = 0;
		foreach($rows as $row)
		{
			$hid = $row['hid'];
			$insert_query[] = array($row['hid'],$row['sitename'],$row['headlinesurl']);
		}
		$insert_query[] = array(($hid+1), 'phpnuke', 'http://www.phpnuke.ir/feed/');
		
		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query) && !empty($insert_query))
			$db->table(HEADLINES_TABLE)->multiinsert(array("hid","sitename","headlinesurl"),$insert_query);
	}

	// update nuke_modules
	$insert_query = array();
	$main_module_selected = false;
	$nuke_modules_cacheData = get_cache_file_contents('nuke_modules');
	$nuke_modules_cacheData_by_title = phpnuke_array_change_key($nuke_modules_cacheData, "mid", "title");
	$result = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_modules`");
	if(intval($result->count()) > 0)
	{
		$rows = $result->results();
		foreach($rows as $row)
		{
			$row['main_module'] = 0;
			if(!$main_module_selected)
			{
				$result2 = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_main`");
				if(intval($result2->count()) > 0)
				{
					$row2 = $result2->results();
					if(isset($row2[0]) && $row2[0] != '' && $row2[0] == $row['title'])
					{
						$row['main_module'] = 1;
						$main_module_selected = true;
					}
				}
			}
			
			$row['module_boxes'] = array("index" => 'right|'.(($row['leftblock'] == 1) ? "left":"").'||||');
			if(!array_key_exists($row['title'], $nuke_modules_cacheData_by_title))
				$insert_query[] = array($row['mid'],$row['title'],$row['active'],$row['view'],$row['admins'],$row['leftblock'],$row['main_module'],$row['inmenu'],phpnuke_serialize($row['module_boxes']));
		}
		
		if(isset($insert_query) && !empty($insert_query))
			$db->table(MODULES_TABLE)->multiinsert(array("mid","title","active","mod_permissions","admins","all_blocks","main_module","in_menu","module_boxes"),$insert_query);
	}

	// update nuke_groups_info
	$db->query("set names '$pn_dbcharset'");
	$when_query = array();
	$result = $db->query("SELECT g.*, p.points FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_groups_info` as g LEFT JOIN `".OLD_DB."`.`".OLD_DB_PREFIX."_groups_points` as p ON g.id = p.id ORDER BY g.id ASC");
	if(intval($result->count()) > 0)
	{
		$rows = $result->results();
		
		foreach($rows as $row)
			$when_query[$row['id']] = "WHEN id = '".$row['id']."' THEN ".$row['points']."";
			
		$db->query("set names '$pn_dbcharset'");
		if(!empty($when_query))
		{
			$ids = array_keys($when_query);
			$ids = implode(", ", $ids);
			$when_query = implode("\n", $when_query);
			$db->query("UPDATE ".POINTS_GROUPS_TABLE." SET points = CASE 
				$when_query
			END
			WHERE id IN($ids)");
		}
	}

	
	$result1 = $db->query("SELECT SUM(g_hits) as total_hits FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_stats_hour` WHERE g_year != '0'");
	if(intval($result1->count()) > 0)
	{
		$results = $result1->results();
		$total_hits = intval($results[0]['total_hits']);
	}
	
	$result2 = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_counter");
	if(intval($result2->count()) > 0)
	{
		$rows2 = $result2->results();
		if(!empty($rows2))
		{
			
			$total = $WebTV = $Lynx = $MSIE = $Opera = $Konqueror = $Netscape = $FireFox = $Bot = $Other = $Windows = $Linux = $Mac = $FreeBSD = $SunOS = $IRIX = $BeOS = $OS2 = $AIX = $Other = 0;
			
			foreach($rows2 as $row2)
			{
				if($row2['type'] == 'total')
					$total = intval($row2['count']);
					
				if($row2['type'] == 'browser' && $row2['var'] == 'WebTV')
					$WebTV = intval($row2['count']);
					
				if($row2['type'] == 'browser' && $row2['var'] == 'Lynx')
					$Lynx = intval($row2['count']);
					
				if($row2['type'] == 'browser' && $row2['var'] == 'MSIE')
					$MSIE = intval($row2['count']);
					
				if($row2['type'] == 'browser' && $row2['var'] == 'Opera')
					$Opera = intval($row2['count']);
					
				if($row2['type'] == 'browser' && $row2['var'] == 'Konqueror')
					$Konqueror = intval($row2['count']);
					
				if($row2['type'] == 'browser' && $row2['var'] == 'Netscape')
					$Netscape = intval($row2['count']);
					
				if($row2['type'] == 'browser' && $row2['var'] == 'FireFox')
					$FireFox = intval($row2['count']);
					
				if($row2['type'] == 'browser' && $row2['var'] == 'Bot')
					$Bot = intval($row2['count']);
					
				if($row2['type'] == 'browser' && $row2['var'] == 'Other')
					$Other = intval($row2['count']);
					
				if($row2['type'] == 'os' && $row2['var'] == 'Windows')
					$Windows = intval($row2['count']);
					
				if($row2['type'] == 'os' && $row2['var'] == 'Linux')
					$Linux = intval($row2['count']);
					
				if($row2['type'] == 'os' && $row2['var'] == 'Mac')
					$Mac = intval($row2['count']);
					
				if($row2['type'] == 'os' && $row2['var'] == 'FreeBSD')
					$FreeBSD = intval($row2['count']);
					
				if($row2['type'] == 'os' && $row2['var'] == 'SunOS')
					$SunOS = intval($row2['count']);
					
				if($row2['type'] == 'os' && $row2['var'] == 'IRIX')
					$IRIX = intval($row2['count']);
					
				if($row2['type'] == 'os' && $row2['var'] == 'BeOS')
					$BeOS = intval($row2['count']);
					
				if($row2['type'] == 'os' && $row2['var'] == 'OS/2')
					$OS2 = intval($row2['count']);
					
				if($row2['type'] == 'os' && $row2['var'] == 'AIX')
					$AIX = intval($row2['count']);
					
				if($row2['type'] == 'os' && $row2['var'] == 'Other')
					$Other = intval($row2['count']);
			}
			
			$browsers_sum = $WebTV + $Lynx + $MSIE + $Opera + $Konqueror + $Netscape + $FireFox + $Bot + $Other;
			$oses_sum = $Windows + $Linux + $Mac + $FreeBSD + $SunOS + $IRIX + $BeOS + $OS2 + $AIX + $Other;
			
			$p_MSIE = ($MSIE/$browsers_sum)*100;
			$p_FireFox = ($FireFox/$browsers_sum)*100;
			$p_Opera = ($Opera/$browsers_sum)*100;
			$p_br_others = (($p_MSIE+$p_FireFox+$p_Opera) < 100) ? (100-($p_MSIE+$p_FireFox+$p_Opera)):0;
			
			$p_Windows = ($Windows/$oses_sum)*100;
			$p_Linux = ($Linux/$oses_sum)*100;
			$p_Mac = ($Mac/$oses_sum)*100;
			$p_os_others = (($p_Windows+$p_Linux+$p_Mac) < 100) ? (100-($p_Windows+$p_Linux+$p_Mac)):0;	

			$Msie_hits = round(($total_hits*$p_MSIE)/100);
			$Firefox_hits = round(($total_hits*$p_FireFox)/100);
			$Opera_hits = round(($total_hits*$p_Opera)/100);
			$Others_br_hits = round(($total_hits*$p_br_others)/100);

			$br_diff = ($total_hits - ($Msie_hits + $Firefox_hits + $Opera_hits + $Others_br_hits));
			if($br_diff != 0)
				$Others_br_hits = $Others_br_hits+$br_diff;
			
			$win7_hits = round(($total_hits*$p_Windows)/100);
			$Linux_hits = round(($total_hits*$p_Linux)/100);
			$MacOSX_hits = round(($total_hits*$p_Mac)/100);
			$Others_os_hits = round(($total_hits*$p_os_others)/100);
			$os_diff = ($total_hits - ($win7_hits + $Linux_hits + $MacOSX_hits + $Others_os_hits));
			if($os_diff != 0)
				$Others_os_hits = $Others_os_hits+$os_diff;
		}
	}
		
	$when_query = array();
	$result = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_mostonline");
	if(intval($result->count()) > 0)
	{
		$rows = $result->results();
		$row = $rows[0];		
		
		$when_query[] = "WHEN `type` = 'browser' AND var = 'Msie' THEN ".$Msie_hits."";
		$when_query[] = "WHEN `type` = 'browser' AND var = 'Firefox' THEN ".$Firefox_hits."";
		$when_query[] = "WHEN `type` = 'browser' AND var = 'Opera' THEN ".$Opera_hits."";
		$when_query[] = "WHEN `type` = 'browser' AND var = 'Others' THEN ".$Others_br_hits."";
		$when_query[] = "WHEN `type` = 'os' AND var = 'win 7' THEN ".$win7_hits."";
		$when_query[] = "WHEN `type` = 'os' AND var = 'Linux' THEN ".$Linux_hits."";
		$when_query[] = "WHEN `type` = 'os' AND var = 'Mac OS X' THEN ".$MacOSX_hits."";
		$when_query[] = "WHEN `type` = 'os' AND var = 'Others' THEN ".$Others_os_hits."";
		$when_query[] = "WHEN `type` = 'total' THEN ".$total_hits."";
		$when_query[] = "WHEN `type` = 'mosts' AND var = 'total' THEN ".$row['total']."";
		$when_query[] = "WHEN `type` = 'mosts' AND var = 'members' THEN ".$row['members']."";
		$when_query[] = "WHEN `type` = 'mosts' AND var = 'guests' THEN ".$row['nonmembers']."";
		
		if(!empty($when_query))
		{
			$when_query = implode("\n", $when_query);
			$db->query("UPDATE ".STATISTICS_COUNTER_TABLE." SET `count` = CASE 
				$when_query
			END
			WHERE `type` = 'mosts' OR `type` = 'total' OR `type` = 'os'  OR `type` = 'browser' AND var IN('total', 'members', 'guests', 'win 7','Linux','Opera','Others','Msie','Firefox','Opera')");
		}
	}

	$insert_query = array();
	$db->query("set names 'latin1'");
	$result = $db->query("SELECT
		p.*,
		GROUP_CONCAT(CONCAT(pd.optionText) SEPARATOR '|') as optionTextlist,
		GROUP_CONCAT(CONCAT(pd.optionCount) SEPARATOR '|') as optionCountlist,
		SUM(CONCAT(pd.optionCount) ) as optionCountSum
	FROM
		`".OLD_DB."`.`".OLD_DB_PREFIX."_poll_desc` as p
	INNER JOIN `".OLD_DB."`.`".OLD_DB_PREFIX."_poll_data` as pd ON p.pollID = pd.pollID AND optionText != ''
	GROUP BY p.pollID
	ORDER BY p.pollID ASC
	");

	if(intval($result->count()) > 0)
	{
		$rows = $result->results();
		
		foreach($rows as $key => $row)
		{
			$optionTextlist = explode("|", $row['optionTextlist']);
			$optionCountlist = explode("|", $row['optionCountlist']);
			
			$options = array();
			
			if(isset($optionTextlist) && !empty($optionTextlist))
			{
				foreach($optionTextlist as $op_key => $optionText)
				{
					if($optionText != '' && isset($optionCountlist[$op_key]))
						$options[] = array($optionText, $optionCountlist[$op_key]);
				}
			}
			$options = phpnuke_serialize($options);
			
			$pollUrl = trim(sanitize(str2url($row['pollTitle'])), "-");
			$pollUrl = get_unique_post_slug(SURVEYS_TABLE, "pollID", $row['pollID'], "pollUrl", $pollUrl, 'publish');
		
			$status = ($key == (sizeof($rows)-1)) ? 1:0;
			$insert_query[] = array($row['pollID'], $status, $default_admin, 1, 0, $row['pollTitle'],$pollUrl,$row['planguage'],$row['optionCountSum'],0,1,$row['comments'], 0, 0, 0, $options);
		}
			
		if(isset($insert_query) && !empty($insert_query))
		{
			$db->query("set names '$pn_dbcharset'");
			$db->table(SURVEYS_TABLE)->multiinsert(array("pollID","status","aid","canVote","main_survey","pollTitle","pollUrl","planguage","voters","to_main","allow_comment","comments","multi_vote","show_voters_num","permissions","options"), $insert_query);
			$db->query("INSERT INTO `".SURVEYS_CHECK_TABLE."` SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_poll_check`;");
		}
	}
	
	upgrade_header(7, 95);
	echo"<div class=\"wizard-card\">
		<h3>بروزرسانی سیستم</h3>
		<div class=\"wizard-input-section\">
			<p>سیستم در حال نصب می باشد شکیبا باشید<br /><br /></p>
			بروزرسانی تعدادی از جداول
			<div class=\"progress\">
				<div class=\"progress-bar progress-bar-info progress-bar-striped active\" role=\"progressbar\" aria-valuenow=\"100\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:100%\"></div>
			</div>
			کل عملیات بروزرسانی
			<div class=\"progress\">
				<div class=\"progress-bar progress-bar-primary progress-bar-striped active\" role=\"progressbar\" aria-valuenow=\"8\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:8%\"></div>
			</div>
		</div>
	</div>
	<meta http-equiv=\"refresh\" content=\"5;URL='install.php?op=comments'\" />";
	upgrade_footer();
}

function upgrade_comments($start = 0)
{
	global $db, $nuke_configs, $cache, $pn_dbcharset;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	// update nuke_comments
	$db->query("set names 'latin1'");
	$result1 = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_comments_config` WHERE code = '1'");
	if(intval($result1->count()) > 0)
	{
		$rows1 = $result1->results();
		foreach($rows1 as $row1)
		{
			if(preg_match("#name#isU", $row1['name']))
				$comments_config[$row1['cfid']] = 'name';
				
			if(preg_match("#email#isU", $row1['name']))
				$comments_config[$row1['cfid']] = 'email';
				
			if(preg_match("#website#isU", $row1['name']))
				$comments_config[$row1['cfid']] = 'url';
				
			if(preg_match("#url#isU", $row1['name']))
				$comments_config[$row1['cfid']] = 'url';
		}
	}

	$result2 = $db->query("SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_stories_comments_fildes`");
	if(intval($result2->count()) > 0)
	{
		$rows2 = $result2->results();
		foreach($rows2 as $row2)
		{
			if(!isset($comments_config[$row2['cfid']]))
				continue;
			$fields[$row2['tid']][$comments_config[$row2['cfid']]] = $row2['cfvalue'];
		}
	}

	$insert_query = array();
	$result = $db->query("SELECT t.*, s.title as post_title, (SELECT COUNT(tid) FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_stories_comments`) as total_rows FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_stories_comments` as t LEFT JOIN `".OLD_DB."`.`".OLD_DB_PREFIX."_stories` as s ON s.sid = t.sid ORDER BY tid ASC LIMIT $start, 500");
	$fetched_rows = intval($result->count());
	if($fetched_rows > 0)
	{
		$rows = $result->results();
		$all_rows = array();
		$total_rows_set = false;
		foreach($rows as $row)
		{
			if(!$total_rows_set)
			{
				$cache->store('total_rows', intval($row['total_rows']));
				unset($row['total_rows']);
				$total_rows_set = true;
			}
			$all_rows[$row['tid']] = $row;
		}
		
		unset($rows);
		
		if(!empty($all_rows))
		{
			foreach($all_rows as $row)
			{
				$main_parent = ($row['pid'] != 0) ? get_main_childs_parent($all_rows, $row['pid'], 'pid'):0;
				$insert_query[$row['tid']] = array(
					'tid' => $row['tid'],
					'pid' => $row['pid'], 
					'main_parent' => $main_parent, 
					'module' => 'Articles',
					'sid' => $row['sid'],
					'post_title' => $row['post_title'],
					'date' => $row['date'],
					'name' => ((isset($fields[$row['tid']]['name']) && $fields[$row['tid']]['name'] != '') ? $fields[$row['tid']]['name']:$row['name']),
					'email' => ((isset($fields[$row['tid']]['email']) && $fields[$row['tid']]['email'] != '') ? $fields[$row['tid']]['email']:$row['email']),
					'url' => ((isset($fields[$row['tid']]['url']) && $fields[$row['tid']]['url'] != '') ? $fields[$row['tid']]['url']:$row['url']),
					'host_name' => $row['host_name'],
					'comment' => $row['comment'],
					'score' => $row['score'],
					'reason' => $row['reason'],
					'act' => $row['act']
				);
				
			}
		}
		
		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query) && !empty($insert_query))
			$db->table(COMMENTS_TABLE)->multiinsert(array("cid","pid","main_parent","module","post_id","post_title","date","name","email","url","ip","comment","score","reason","status"),$insert_query);
	}
	
	$new_start = $start+500;
	$total_rows = $cache->retrieve('total_rows');
	
	upgrade_progress_output("انتقال نظرات", $total_rows, $fetched_rows, $start, "install.php?op=feedbacks", "install.php?op=comments&start=$new_start", 16, 500);
}

function upgrade_feedbacks($start)
{
	global $db, $nuke_configs, $cache, $pn_dbcharset, $transfer_counter;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	
	$transfer_counter = (isset($transfer_counter) && $transfer_counter != 0) ? $transfer_counter:500;
	// update nuke_feedbacks
	$db->query("set names 'latin1'");
	$insert_query = array();
	$result = $db->query("SELECT *, (SELECT COUNT(fid) FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_feedbacks`) as total_rows FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_feedbacks` ORDER BY fid ASC LIMIT $start, $transfer_counter");
	$fetched_rows = intval($result->count());
	if($fetched_rows > 0)
	{
		$rows = $result->results();
		
		if(!empty($rows))
			$total_rows_set = false;
			foreach($rows as $row)
			{
				if(!$total_rows_set)
				{
					$cache->store('total_rows', intval($row['total_rows']));
					unset($row['total_rows']);
					$total_rows_set = true;
				}
				$insert_query[] = array($row['fid'],$row['sender_name'],$row['sender_email'],$row['subject'],$row['message'],$row['responsibility'],$row['replys'],_NOWTIME);
			}

		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query) && !empty($insert_query))
			$db->table(FEEDBACKS_TABLE)->multiinsert(array("fid","sender_name","sender_email","subject","message","responsibility","replys","added_time"),$insert_query);
	}
		
	$errors = $db->getErrors('last');
	if(isset($errors['message']) && ($errors['message'] == "MySQL server has gone away" || stristr($errors['message'], "max_allowed_packet")))
	{
		upgrade_header(7, 95);
		echo"
		<form role=\"form\" class=\"form-horizontal\" id=\"nukeform\" action=\"install.php?op=feedbacks".((isset($start) && $start != 0) ? "&start=$start":"")."\" method=\"post\">
			<div class=\"wizard-card-container\" style=\"height: 326px;\">
				<div class=\"wizard-card\" data-cardname=\"group\">
					<h3>بروزرسانی سیستم</h3>
					<div class=\"wizard-error\">
						<div class=\"alert alert-danger\">	<strong>خطا :</strong> حجم اطلاعات شما بالا می باشد و سرور قادر به انتقال تعداد $transfer_counter پیام در هر بار نمی باشد. لطفاً تعداد انتقال را با توجه به حجم پیامهایتان پایین بیاورید. .</div>
					</div>
					<div class=\"wizard-input-section\">
						<div class=\"form-group\">
							<label class=\"control-label col-sm-4\" for=\"transfer_counter\">تعداد پیام قابل انتقال:</label>
							<div class=\"col-sm-8\">
								<input type=\"text\" class=\"form-control\" id=\"transfer_counter\" name=\"transfer_counter\" value=\"$transfer_counter\" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class=\"wizard-footer\">
				<div class=\"wizard-buttons-container\">
					<div class=\"btn-group-single pull-left\">
						<a href=\"javascript:history.go(-1)\"><button class=\"btn wizard-back\" type=\"button\">قبلی</button></a>
						<input class=\"btn wizard-next btn-primary\" type=\"submit\" value=\"بعدی\" />
					</div>
				</div>
			</div>
		</form>";
		upgrade_footer();
		die();
	}
	
	$new_start = $start+$transfer_counter;
	$total_rows = $cache->retrieve('total_rows');
	
	upgrade_progress_output("انتقال پیامهای ارتباط با ما", $total_rows, $fetched_rows, $start, "install.php?op=ipbans", "install.php?op=feedbacks&start=$new_start", 30, $transfer_counter);
}
/*
function upgrade_mtsn($start)
{
	global $db, $nuke_configs, $cache, $pn_dbcharset;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	// update nuke_mtsn
	$db->query("set names 'latin1'");
	$insert_query = array();
	$result = $db->query("SELECT *, (SELECT COUNT(id) FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_mtsn`) as total_rows FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_mtsn` ORDER BY id ASC LIMIT $start, 2000");
	$fetched_rows = intval($result->count());
	if($fetched_rows > 0)
	{
		$rows = $result->results();

		$total_rows_set = false;
		foreach($rows as $row)
		{
			if(!$total_rows_set)
			{
				$cache->store('total_rows', intval($row['total_rows']));
				unset($row['total_rows']);
				$total_rows_set = true;
			}
			$insert_query[] = array($row['id'],$row['server'],$row['ip'],$row['time'],$row['method']);
		}
		
		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query) && !empty($insert_query))
			$db->table(MTSN_TABLE)->multiinsert(array("id","server","ip","time","method"),$insert_query);
	}
	
	$new_start = $start+2000;
	$total_rows = $cache->retrieve('total_rows');
	
	upgrade_progress_output("انتقال اطلاعات حملات سایت", $total_rows, $fetched_rows, $start, "install.php?op=ipbans", "install.php?op=mtsn&start=$new_start", 32, 2000);
}
*/
function upgrade_ipbans($start)
{
	global $db, $nuke_configs, $cache, $pn_dbcharset;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	// update nuke_mtsn_ipban
	$db->query("set names 'latin1'");
	$insert_query = array();
	$result = $db->query("SELECT *, (SELECT COUNT(id) FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_mtsn_ipban`) as total_rows FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_mtsn_ipban` ORDER BY id ASC LIMIT $start, 2000");
	$fetched_rows = intval($result->count());
	if($fetched_rows > 0)
	{
		$rows = $result->results();

		$total_rows_set = false;
		foreach($rows as $row)
		{
			if(!$total_rows_set)
			{
				$cache->store('total_rows', intval($row['total_rows']));
				unset($row['total_rows']);
				$total_rows_set = true;
			}
			$insert_query[] = array($row['id'],'admin',$row['ipaddress'],$row['reason'],$row['time']);
		}
		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query) && !empty($insert_query))
			$db->table(MTSN_IPBAN_TABLE)->multiinsert(array("id","blocker","ipaddress","system","time"), $insert_query);
	}
	
	$new_start = $start+2000;
	$total_rows = $cache->retrieve('total_rows');
	
	upgrade_progress_output("انتقال آی پی های مسدود شده", $total_rows, $fetched_rows, $start, "install.php?op=reports", "install.php?op=ipbans&start=$new_start", 40, 2000);
}

function upgrade_reports($start)
{
	global $db, $nuke_configs, $cache, $pn_dbcharset;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	
	// update nuke_reports
	$db->query("set names 'latin1'");
	$insert_query = array();
	$result = $db->query("SELECT *, (SELECT COUNT(rid) FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_reports` WHERE rcode = '1') as total_rows FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_reports` WHERE rcode = '1' ORDER BY rid ASC LIMIT $start, 500");
	$fetched_rows = intval($result->count());
	if($fetched_rows > 0)
	{
		$rows = $result->results();
		
		$total_rows_set = false;
		foreach($rows as $row)
		{
			if(!$total_rows_set)
			{
				$cache->store('total_rows', intval($row['total_rows']));
				unset($row['total_rows']);
				$total_rows_set = true;
			}
			$insert_query[] = array($row['rid'], $row['sid'],$row['user_id'],$row['title'],$row['desc'],$row['time']);
		}
		
		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query) && !empty($insert_query))
			$db->table(REPORTS_TABLE)->multiinsert(array("rid","post_id","user_id","subject","message","time"), $insert_query);
	}
	
	$new_start = $start+500;
	$total_rows = $cache->retrieve('total_rows');
	
	upgrade_progress_output("انتقال گزارشات", $total_rows, $fetched_rows, $start, "install.php?op=scores", "install.php?op=reports&start=$new_start", 48, 500);
}

function upgrade_scores($start)
{
	global $db, $nuke_configs, $cache, $pn_dbcharset;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	
	// update nuke_scores
	$insert_query = array();
	$result = $db->query("SELECT s.*, u.user_id, (SELECT COUNT(id) FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_stories_score`) as total_rows FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_stories_score` as s LEFT JOIN `".OLD_DB."`.`".OLD_DB_PREFIX."_users` as u ON u.username = s.username ORDER BY s.id ASC LIMIT $start, 1000");
	$fetched_rows = intval($result->count());
	if($fetched_rows > 0)
	{
		$rows = $result->results();

		$total_rows_set = false;
		foreach($rows as $row)
		{
			if(!$total_rows_set)
			{
				$cache->store('total_rows', intval($row['total_rows']));
				unset($row['total_rows']);
				$total_rows_set = true;
			}
			$insert_query[] = array($row['id'], $row['sid'],'articles',$row['rating_ip'],$row['score'],$row['user_id'],$row['gust']);
		}
		
		if(isset($insert_query) && !empty($insert_query))
			$db->table(SCORES_TABLE)->multiinsert(array("id","post_id","db_table","rating_ip","score","user_id","gust"), $insert_query);
	}

	$new_start = $start+1000;
	$total_rows = $cache->retrieve('total_rows');
	
	upgrade_progress_output("انتقال امتیازات اخبار", $total_rows, $fetched_rows, $start, "install.php?op=statistics", "install.php?op=scores&start=$new_start", 56, 1000);
}

function upgrade_statistics($start = 0)
{
	global $db, $nuke_configs, $cache, $counter, $pn_dbcharset;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
		
	// update nuke_statistics
	$insert_query = array();
	$hourly_info = array();
	$counter = intval($counter);
	
	if($start == 0)
	{
		$db->query("DROP TABLE IF EXISTS `nuke_stats_hour`;");
		$db->query("CREATE TABLE IF NOT EXISTS `nuke_stats_hour` (
		  `j_year` smallint(6) NOT NULL default '0',
		  `j_month` tinyint(4) NOT NULL default '0',
		  `j_date` tinyint(4) NOT NULL default '0',
		  `j_hour` tinyint(4) NOT NULL default '0',
		  `j_hits` int(11) NOT NULL default '0',
		  `g_year` smallint(6) NOT NULL,
		  `g_month` tinyint(4) NOT NULL,
		  `g_date` tinyint(4) NOT NULL,
		  `g_hour` tinyint(4) NOT NULL,
		  `g_hits` int(11) NOT NULL,
		  `h_year` smallint(6) NOT NULL,
		  `h_month` tinyint(4) NOT NULL,
		  `h_date` tinyint(4) NOT NULL,
		  `h_hour` tinyint(4) NOT NULL,
		  `h_hits` int(11) NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		$db->query("INSERT INTO `nuke_stats_hour` SELECT * FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_stats_hour`;");
		$db->query("DELETE FROM `nuke_stats_hour` WHERE g_hits = '0'");
		$db->query("ALTER TABLE `nuke_stats_hour` ADD shid INT");
		$db->query("ALTER TABLE `nuke_stats_hour` MODIFY shid INT AUTO_INCREMENT PRIMARY KEY");
		$result1 = $db->query("SELECT shid FROM `nuke_stats_hour` ORDER BY shid DESC LIMIT 0,1");
		$results1 = $result1->results();
		$total_rows = $results1[0]['shid'];
		$cache->store('total_rows', intval($total_rows));
	}
	
	$result = $db->query("SELECT shid, g_year, g_month, g_date, g_hour, g_hits FROM `nuke_stats_hour` WHERE shid > '$start' ORDER BY shid ASC LIMIT 0,5000");

	$fetched_rows = intval($result->count());
	if($fetched_rows > 0)
	{
		$rows = $result->results();

		$hours = 0;
		
		foreach($rows as $row)
		{		
			if($row['g_year'] != 0 && $row['g_month'] != 0 && $row['g_date'] != 0)
			{
				$statistics[$row['g_year']][$row['g_month']][$row['g_date']][$row['g_hour']] = $row['g_hits'];
				
				$start_date = $row['g_year']."-".$row['g_month']."-".$row['g_date']."-".$row['g_hour']."-".$row['shid'];
			}
		}
		
		$start_date = explode("-", $start_date);
		if($start_date[3] != 23)
		{
			$new_start = $start_date[4]-$start_date[3]-1;
			unset($statistics[$start_date[0]][$start_date[1]][$start_date[2]]);
		}
		else
			$new_start = $start_date[4];

		foreach($statistics as $year => $year_data)
		{
			foreach($year_data as $month => $month_data)
			{
				foreach($month_data as $day => $day_data)
				{
					$zero_hour_hits = 0;
					foreach($day_data as $hour => $hits)
					{
						$key = $year."-".$month."-".$day;
						
						if(!array_key_exists($zero_hour_hits, $day_data))
						{
							for($i=$zero_hour_hits;$i<$hour;$i++)
							{
								$hourly_info[$key][$i] = 0;
							}
						}
						$zero_hour_hits = $hour+1;
						
						$hourly_info[$key][$hour] = (int) $hits;
					}
				}
			}
		}
		unset($statistics);
		
		foreach($hourly_info as $hourly_info_key => $hourly_info_value)
		{
			$hourly_info_key = explode("-", $hourly_info_key);
			$hits = 0;
			foreach($hourly_info_value as $key => $hourly_val)
				$hits = (int) ($hits+$hourly_val);
				
			$hourly_info_value = (string) json_encode($hourly_info_value);
			$insert_query[] = array($hourly_info_key[0], $hourly_info_key[1],$hourly_info_key[2],$hourly_info_value,$hits);
		}
		
		if(isset($insert_query) && !empty($insert_query))
			$db->table(STATISTICS_TABLE)->multiinsert(array("year","month","day","hourly_info","hits"), $insert_query);
	}
	
	if($fetched_rows == 0 || ($fetched_rows > 0 && $fetched_rows < 5000 && $start != 0))
		$db->query("DROP TABLE IF EXISTS `nuke_stats_hour`;");
	
	$total_rows = $cache->retrieve('total_rows');
	
	upgrade_progress_output("انتقال آمار سایت", $total_rows, $fetched_rows, $start, "install.php?op=tags", "install.php?op=statistics&start=$new_start", 64, 5000);
}

function upgrade_tags($start)
{
	global $db, $nuke_configs, $cache, $pn_dbcharset;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	
	// update nuke_tags
	$insert_query = array();
	$db->query("set names 'latin1'");
	$result = $db->query("SELECT *, (SELECT COUNT(tag_id) FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_tags`) as total_rows FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_tags` ORDER BY tag_id ASC LIMIT $start, 1000");
	$fetched_rows = intval($result->count());
	if($fetched_rows > 0)
	{
		$rows = $result->results();
		
		$total_rows_set = false;
		foreach($rows as $row)
		{
			if(!$total_rows_set)
			{
				$cache->store('total_rows', intval($row['total_rows']));
				unset($row['total_rows']);
				$total_rows_set = true;
			}
			$insert_query[] = array($row['tag_id'], $row['tag'],$row['counter']);
		}
		
		$db->query("set names '$pn_dbcharset'");
		if(isset($insert_query) && !empty($insert_query))
			$db->table(TAGS_TABLE)->multiinsert(array("tag_id","tag","counter"), $insert_query);
	}
	
	$new_start = $start+1000;
	$total_rows = $cache->retrieve('total_rows');
	
	upgrade_progress_output("انتقال کلمات کلیدی", $total_rows, $fetched_rows, $start, "install.php?op=articles", "install.php?op=tags&start=$new_start", 72, 1000);
}

function upgrade_articles($start)
{
	global $db, $nuke_configs, $cache, $transfer_counter, $pn_dbcharset, $pn_dbname;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}	
	
	// update nuke_articles
	$db->query("set names 'latin1'");
	$insert_query = array();
	$query_add = array();
	$extra_cols = array();
	
	$default_cols = array("sid","aid","title","time","hometext","bodytext","newslevel","news_group","newsurl","comments","counter","topic","informant","notes","ihome","alanguage","acomm","haspoll","pollID","score","ratings","rating_ip","position","story_pass","topic_link");
	
	$stories_cols = $cache->retrieve("stories_table_cols");
	
	if($stories_cols != '')
		$stories_cols = phpnuke_unserialize($stories_cols);

	if(empty($stories_cols))
	{
		$structure = $db->query("DESCRIBE `".OLD_DB."`.`".OLD_DB_PREFIX."_stories`");
		$str_rows = $structure->results();
		foreach($str_rows as $key => $val)
		{
			if(!in_array($val['Field'], $default_cols))
				$stories_cols[$val['Field']] = $val;
		}
		$cache->store('stories_table_cols', phpnuke_serialize($stories_cols));
	}
	
	$transfer_counter = (isset($transfer_counter) && $transfer_counter != 0) ? $transfer_counter:500;
		
	$result = $db->query("SELECT *, (SELECT COUNT(sid) FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_stories`) as total_rows FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_stories` ORDER BY sid ASC LIMIT $start, $transfer_counter");
	$fetched_rows = intval($result->count());
		
	if($fetched_rows > 0)
	{
		$rows = $result->results();

		if(!empty($rows))
		{
			$total_rows_set = false;
			foreach($rows as $row)
			{
				foreach($row as $col_name => $col_val)
				{
					if(!in_array($col_name, $default_cols) && $col_name != 'total_rows')
						$extra_cols[$col_name] = $col_val;
				}
			
				if(!$total_rows_set)
				{
					$cache->store('total_rows', intval($row['total_rows']));
					unset($row['total_rows']);
					$total_rows_set = true;
				}
				
				$newsurl = ($row['newsurl'] != '') ? $row['newsurl']:$row['title'];
				
				$article_url = trim(sanitize(str2url($newsurl)), "-");
				$article_url = get_unique_post_slug(ARTICLES_TABLE, "sid", $row['sid'], "article_url", $article_url, 'publish');
				if($row['notes'] != '') 
					$row['notes'] = str_replace(":", ",", $row['notes']);
				$row['comments'] = ($row['comments'] < 0) ? 0:$row['comments'];
				$row['ihome'] = ($row['ihome'] == 0) ? 1:0;
				$row['acomm'] = ($row['acomm'] == 0) ? 1:0;
				
				$insert_query[] = array_merge(array($row['sid'], 'publish', 'article', $row['aid'],$row['title'],$row['time'],$row['hometext'],$row['bodytext'],$article_url,$row['comments'],$row['counter'],$row['topic'],$row['informant'],$row['notes'],$row['ihome'],$row['alanguage'],$row['acomm'],$row['position'],$row['story_pass'],$row['topic_link'],$row['newslevel'],$row['score'],$row['ratings']), array_values($extra_cols));
			}

			if($start == 0 && !empty($extra_cols))
			{
				foreach($extra_cols as $col_name => $col_value)
				{
					$query_add[] = "ADD `".$col_name."` ".strtoupper($stories_cols[$col_name]['Type'])." ".(($stories_cols[$col_name]['Null'] == "NO") ? "NOT NULL":"NULL")."";
				}
				if(!empty($query_add))
					$db->query("ALTER TABLE `".ARTICLES_TABLE."` ".implode(", ", $query_add).""); 
			}
			
			$db->query("set names '$pn_dbcharset'");
			
			$new_cols = array_merge(array("sid","status","post_type","aid","title","time","hometext","bodytext","article_url","comments","counter","cat","informant","tags","ihome","alanguage","allow_comment","position","article_pass","cat_link","permissions","score","ratings"), array_keys($extra_cols));
			
			if(isset($insert_query) && !empty($insert_query))
				$db->table(ARTICLES_TABLE)->multiinsert($new_cols,$insert_query);
		}
	}
	
	$errors = $db->getErrors('last');
	if(isset($errors['message']) && ($errors['message'] == "MySQL server has gone away" || stristr($errors['message'], "max_allowed_packet")))
	{
		upgrade_header(7, 95);
		echo"
		<form role=\"form\" class=\"form-horizontal\" id=\"nukeform\" action=\"install.php?op=articles".((isset($start) && $start != 0) ? "&start=$start":"")."\" method=\"post\">
			<div class=\"wizard-card-container\" style=\"height: 326px;\">
				<div class=\"wizard-card\" data-cardname=\"group\">
					<h3>بروزرسانی سیستم</h3>
					<div class=\"wizard-error\">
						<div class=\"alert alert-danger\">	<strong>خطا :</strong> حجم اطلاعات شما بالا می باشد و سرور قادر به انتقال تعداد $transfer_counter مطلب در هر بار نمی باشد. لطفاً تعداد انتقال را با توجه به حجم مطالبتان پایین بیاورید. .</div>
					</div>
					<div class=\"wizard-input-section\">
						<div class=\"form-group\">
							<label class=\"control-label col-sm-4\" for=\"transfer_counter\">تعداد مطلب قابل انتقال:</label>
							<div class=\"col-sm-8\">
								<input type=\"text\" class=\"form-control\" id=\"transfer_counter\" name=\"transfer_counter\" value=\"$transfer_counter\" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class=\"wizard-footer\">
				<div class=\"wizard-buttons-container\">
					<div class=\"btn-group-single pull-left\">
						<a href=\"javascript:history.go(-1)\"><button class=\"btn wizard-back\" type=\"button\">قبلی</button></a>
						<input class=\"btn wizard-next btn-primary\" type=\"submit\" value=\"بعدی\" />
					</div>
				</div>
			</div>
		</form>";
		upgrade_footer();
		die();
	}
	$cache->store('last_article_sid', $db->lastInsertid());
	
	$new_start = $start+$transfer_counter;
	$total_rows = $cache->retrieve('total_rows');
	
	upgrade_progress_output("انتقال مطالب", $total_rows, $fetched_rows, $start, "install.php?op=staticpages", "install.php?op=articles&start=$new_start&transfer_counter=$transfer_counter", 80, $transfer_counter);
}

function upgrade_staticpages($start)
{
	global $db, $nuke_configs, $cache, $transfer_counter, $pn_dbcharset, $pn_dbname;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}	
	
	// update nuke_articles
	$db->query("set names 'latin1'");
	$insert_query = array();
	
	$transfer_counter = (isset($transfer_counter) && $transfer_counter != 0) ? $transfer_counter:500;
	
	$last_article_sid = $cache->retrieve('last_article_sid');

	$default_admin = $cache->retrieve('default_admin', false);
	
	$result = $db->query("SELECT *, (SELECT COUNT(pid) FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_staticpages`) as total_rows FROM `".OLD_DB."`.`".OLD_DB_PREFIX."_staticpages` ORDER BY pid ASC LIMIT $start, $transfer_counter");
	$fetched_rows = intval($result->count());
		
	if($fetched_rows > 0)
	{
		$rows = $result->results();

		if(!empty($rows))
		{
			$total_rows_set = false;
			foreach($rows as $row)
			{
				if(!$total_rows_set)
				{
					$cache->store('total_rows', intval($row['total_rows']));
					unset($row['total_rows']);
					$total_rows_set = true;
				}
				
				$last_article_sid++;
				$row['pid'] = $last_article_sid;
				
				$article_url = trim(sanitize(str2url($row['title'])), "-");
				$article_url = get_unique_post_slug(ARTICLES_TABLE, "sid", $row['pid'], "article_url", $article_url, 'publish');
				if($row['notes'] != '') 
					$row['notes'] = str_replace(":", ",", $row['notes']);
				$row['comments'] = ($row['comments'] < 0) ? 0:$row['comments'];
				$row['ihome'] =  1;
				$row['acomm'] = 1;
				
				$row['status'] = ($row['active'] == 1) ? "publish":"pending";
				
				$insert_query[] = array($row['pid'], $row['status'], 'static', $default_admin,$row['title'],_NOWTIME,'',$row['text'],$article_url,$row['comments'],$row['counter'],1,$default_admin,$row['notes'],$row['ihome'],$row['alanguage'],$row['acomm'],1,'',1,0,0,0);
			}
			
			$db->query("set names '$pn_dbcharset'");
			
			$new_cols = array("sid","status","post_type","aid","title","time","hometext","bodytext","article_url","comments","counter","cat","informant","tags","ihome","alanguage","allow_comment","position","article_pass","cat_link","permissions","score","ratings");
			
			if(isset($insert_query) && !empty($insert_query))
				$db->table(ARTICLES_TABLE)->multiinsert($new_cols,$insert_query);
		}
	}
	
	$errors = $db->getErrors('last');
	if(isset($errors['message']) && ($errors['message'] == "MySQL server has gone away" || stristr($errors['message'], "max_allowed_packet")))
	{
		upgrade_header(7, 95);
		echo"
		<form role=\"form\" class=\"form-horizontal\" id=\"nukeform\" action=\"install.php?op=staticpages".((isset($start) && $start != 0) ? "&start=$start":"")."\" method=\"post\">
			<div class=\"wizard-card-container\" style=\"height: 326px;\">
				<div class=\"wizard-card\" data-cardname=\"group\">
					<h3>بروزرسانی سیستم</h3>
					<div class=\"wizard-error\">
						<div class=\"alert alert-danger\">	<strong>خطا :</strong> حجم اطلاعات شما بالا می باشد و سرور قادر به انتقال تعداد $transfer_counter مطلب در هر بار نمی باشد. لطفاً تعداد انتقال را با توجه به حجم مطالبتان پایین بیاورید. .</div>
					</div>
					<div class=\"wizard-input-section\">
						<div class=\"form-group\">
							<label class=\"control-label col-sm-4\" for=\"transfer_counter\">تعداد مطلب قابل انتقال:</label>
							<div class=\"col-sm-8\">
								<input type=\"text\" class=\"form-control\" id=\"transfer_counter\" name=\"transfer_counter\" value=\"$transfer_counter\" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class=\"wizard-footer\">
				<div class=\"wizard-buttons-container\">
					<div class=\"btn-group-single pull-left\">
						<a href=\"javascript:history.go(-1)\"><button class=\"btn wizard-back\" type=\"button\">قبلی</button></a>
						<input class=\"btn wizard-next btn-primary\" type=\"submit\" value=\"بعدی\" />
					</div>
				</div>
			</div>
		</form>";
		upgrade_footer();
		die();
	}
	
	$cache->store('last_article_sid', $db->lastInsertid());
	$new_start = $start+$transfer_counter;
	$total_rows = $cache->retrieve('total_rows');
	
	upgrade_progress_output("انتقال صفحات ثابت", $total_rows, $fetched_rows, $start, "install.php?op=forum", "install.php?op=staticpages&start=$new_start&transfer_counter=$transfer_counter", 80, $transfer_counter);
}

function upgrade_forum()
{
	global $db, $nuke_configs, $cache, $pn_dbtype, $pn_dbfetch, $pn_dbcharset;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
	
	$install_options = $cache->retrieve('install_options');
	$install_options = phpnuke_unserialize($install_options);
	
	// Get a listing of all tables
	//$tables_result = $db->query("SHOW TABLES FROM `".OLD_DB."`");
	$tables_result = $db->query("SELECT TABLE_NAME FROM `information_schema`.`TABLES` WHERE TABLE_SCHEMA =  '".OLD_DB."'");

	if(!empty($tables_result))
	{
		// Loop through all tables
		foreach ($tables_result as $row)
		{
			//$forumtable = $row['Tables_in_'.OLD_DB];
			$forumtable = $row['TABLE_NAME'];
			
			if(!preg_match("#".OLD_DB_PREFIX."_bb3#isU", $forumtable))
				continue;
			
			$db->query("DROP TABLE IF EXISTS `".$install_options['db_info']['db_forumname']."`.`$forumtable`");
			
			$query = $db->query("SHOW CREATE TABLE `".OLD_DB."`.`$forumtable`");
			$query_results = $query->results();
			$query_result = $query_results[0];
			
			if($install_options['db_info']['db_forumprefix'] != OLD_DB_PREFIX.'_bb3')
				$new_forumtable = str_replace(OLD_DB_PREFIX.'_bb3', $install_options['db_info']['db_forumprefix']."_", $forumtable);
			
			$sql = str_replace("CREATE TABLE `$forumtable`", "CREATE TABLE `".$install_options['db_info']['db_forumname']."`.`$new_forumtable`", $query_result['Create Table']);
			$db->query($sql);
			
			$db->query("set names '$pn_dbcharset'");
			$db->query("INSERT INTO `".$install_options['db_info']['db_forumname']."`.`$new_forumtable` SELECT * FROM `".OLD_DB."`.`$forumtable`");
		}
				
		$old_users_table_name = "`".OLD_DB."`.`".OLD_DB_PREFIX."_users`";
		$new_users_table_name = "`".$install_options['db_info']['db_forumname']."`.`".(($install_options['db_info']['db_forumprefix'] != OLD_DB_PREFIX.'_bb3') ? $install_options['db_info']['db_forumprefix']."_users":OLD_DB_PREFIX."_bb3users")."`";		
		
		$db->query("set names 'latin1'");
		$db->query("DROP TABLE IF EXISTS $new_users_table_name");
		
		$users_query = $db->query("SHOW CREATE TABLE $old_users_table_name");
		$users_query_results = $users_query->results();
		$users_query_result = $users_query_results[0];
		$users_sql = str_replace("CREATE TABLE `".OLD_DB_PREFIX."_users`", "CREATE TABLE $new_users_table_name", $users_query_result['Create Table']);
		$db->query($users_sql);
		
		$delete_old_modes = $db->query("DELETE FROM `".$install_options['db_info']['db_forumname']."`.`".OLD_DB_PREFIX."_bbmodules` CONVERT(`module_langname` USING utf8) LIKE '%ACP_THANKS%' OR CONVERT(`module_langname` USING utf8) LIKE '%ACP_CAT_PHPBB_SEO%' OR CONVERT(`module_langname` USING utf8) LIKE '%ACP_MOD_REWRITE%' OR CONVERT(`module_basename` USING utf8) LIKE '%phpbb_seo%'");
		
		
		$db->query("set names '$pn_dbcharset'");
		$db->query("INSERT INTO $new_users_table_name SELECT * FROM $old_users_table_name");
		
		if(isset($install_options['db_info']['db_have_forum']) && $install_options['db_info']['db_have_forum'] != 0)
		{
		$config_data = "<?php
// phpBB 3.0.x auto-generated configuration file
// Do not change anything in this file!
".'$'."dbms = 'mysqli';
".'$'."dbhost = 'localhost';
".'$'."dbport = '';
".'$'."dbname = '".$install_options['db_info']['db_forumname']."';
".'$'."dbuser = '".$install_options['db_info']['db_username']."';
".'$'."dbpasswd = '".$install_options['db_info']['db_password']."';
".'$'."table_prefix = '".$install_options['db_info']['db_forumprefix']."_';
".'$'."phpbb_adm_relative_path = 'adm/';
".'$'."acm_type = 'phpbb\\cache\\driver\\file';

@define('PHPBB_INSTALLED', true);
// @define('PHPBB_DISPLAY_LOAD_TIME', true);
@define('PHPBB_ENVIRONMENT', 'production');
// @define('DEBUG_CONTAINER', true);

".'$'."queryString = ".'$'."_SERVER['QUERY_STRING'];
if ((stristr(".'$'."queryString,'%20union%20')) OR (stristr(".'$'."queryString,'%2f%2a')) OR (stristr(".'$'."queryString,'%2f*')) OR (stristr(".'$'."queryString,'/*')) OR (stristr(".'$'."queryString,'*/union/*')) OR (stristr(".'$'."queryString,'c2nyaxb0')) OR (stristr(".'$'."queryString,'+union+'))  OR ((stristr(".'$'."queryString,'cmd=')) AND (!stristr(".'$'."queryString,'&cmd'))) OR ((stristr(".'$'."queryString,'exec')) AND (!stristr(".'$'."queryString,'execu'))) OR (stristr(".'$'."queryString,'concat'))) {
die('Illegal Operation');
}
?>";
		if(isset($install_options['db_info']['db_forumpath']) && $install_options['db_info']['db_forumpath'] != '' && file_exists($install_options['db_info']['db_forumpath']."/config.php"))
		{
			$fp = fopen($install_options['db_info']['db_forumpath']."/config.php", 'w');
			fputs($fp, $config_data);
			fclose($fp);
		}
		}
	}
	
	upgrade_progress_output("انتقال جداول تالار", 100, 100, 0, "install.php?op=final", "", 90, 1000);
}

function upgrade_final()
{
	global $db, $nuke_configs, $cache, $Req_URL, $visitor_ip;
	
	if(!$cache->isCached('install_options'))
	{
		upgrade_header(7, 95);
		echo"<div class=\"wizard-card-container\" style=\"height: 326px;\">
			<div class=\"wizard-card\" data-cardname=\"group\">
				<h3>اطلاعات پایگاه داده</h3>
				<div class=\"wizard-input-section\">
					اطلاعات ارسالی از بخش اطلاعات دیتابیس ناقص است
				</div>
			</div>
		</div>";
		upgrade_footer();
		die();
	}
		
	$install_options = $cache->retrieve("install_options");
	$install_options = phpnuke_unserialize($install_options);
	
	$install_options['siteinfo']['nukeurl'] = (isset($install_options['db_info']['nukeurl'])) ? $install_options['db_info']['nukeurl']:$Req_URL;
	$install_options['siteinfo']['sitename'] = (isset($install_options['db_info']['sitename'])) ? $install_options['db_info']['sitename']:"PhpNuke 8.4";
	$install_options['db_info']['db_forumcms'] = (isset($install_options['db_info']['db_forumcms'])) ? $install_options['db_info']['db_forumcms']:"";
	$install_options['db_info']['db_forumprefix'] = (isset($install_options['db_info']['db_forumprefix'])) ? $install_options['db_info']['db_forumprefix']:"";
	$install_options['db_info']['db_forumname'] = (isset($install_options['db_info']['db_forumname'])) ? $install_options['db_info']['db_forumname']:"";
	$install_options['db_info']['db_forumpath'] = (isset($install_options['db_info']['db_forumpath'])) ? $install_options['db_info']['db_forumpath']:"";
		
	$db->query("UPDATE ".CONFIG_TABLE." SET config_value = '8.4.1' WHERE config_name = 'Version_Num'");
	$db->query("UPDATE ".CONFIG_TABLE." SET config_value = '".$install_options['siteinfo']['nukeurl']."' WHERE config_name = 'nukeurl'");
	$db->query("UPDATE ".CONFIG_TABLE." SET config_value = '".$install_options['siteinfo']['sitename']."' WHERE config_name = 'sitename'");
	$db->query("UPDATE ".CONFIG_TABLE." SET config_value = '".$install_options['db_info']['db_forumcms']."' WHERE config_name = 'forum_system'");
	$db->query("UPDATE ".CONFIG_TABLE." SET config_value = '".$install_options['db_info']['db_forumprefix']."_' WHERE config_name = 'forum_prefix'");
	$db->query("UPDATE ".CONFIG_TABLE." SET config_value = '".$install_options['db_info']['db_forumname']."' WHERE config_name = 'forum_db'");
	$db->query("UPDATE ".CONFIG_TABLE." SET config_value = '".$install_options['db_info']['db_forumpath']."' WHERE config_name = 'forum_path'");
	$db->query("UPDATE ".CONFIG_TABLE." SET config_value = '".$install_options['db_info']['db_forumunicode']."' WHERE config_name = 'forum_collation'");
	
	$db->query("UPDATE ".CONFIG_TABLE." SET config_value = '".$install_options['db_info']['db_have_forum']."' WHERE config_name = 'have_forum'");
	
	if(isset($install_options['db_info']['nukeurl']))
		$db->query("UPDATE ".CONFIG_TABLE." SET config_value = '1' WHERE config_name = 'lock_siteurl'");
		
	
	$pwd = phpnuke_hash_password($install_options['admininfo']['pwd']);
	
	if($install_options['mode'] == 'install')
	{
		// add new admin
		$db->table(AUTHORS_TABLE)
			->insert(array(
				'aid' => $install_options['admininfo']['aid'],
				'name' => 'God',
				'email' => $install_options['admininfo']['email'],
				'pwd' => $pwd,
				'counter' => 1,
				'realname' => $install_options['admininfo']['realname'],
			));
		
		// add new article
		$db->table(ARTICLES_TABLE)
			->insert(array(
				'status' => 'publish',
				'post_type' => 'article',
				'aid' => $install_options['admininfo']['aid'],
				'title' => 'مطلب ابتدایی',
				'time' => _NOWTIME,
				'ip' => $visitor_ip,
				'hometext' => addslashes('<p align="justify">آیا تا به حال به این فکر افتاده اید که برای خود یک وب سایت در دنیای اینترنت داشته باشید یا اینکه برای معرفی شرکت یا محصول خود از طریق اینترنت یک سایت اختصاصی ایجاد کنید.</p>\r\n<p>مشهدتیم آمادگی خود را برای ارائه سرویس های هاستینگ و ثبت دامنه با مناسب ترین قیمت و بهترین امکانات اعلام میکند . سرویس هاستینگ مشهدتیم با کادر مجرب بصورت 24 ساعته پاسخگوی شما خواهد بود . پس از اجاره فضا اگر شما تمایل به استفاده از نسخه های مختلف php-nuke فارسی را دارید گروه مشهدتیم این سیستم مدیریت محتوا را برای شما بصورت رایگان نصب و تنظیم مینماید ، برای کسب اطلاعات بیشتر به بخش <a href="http://www.phpnuke.ir/index.php?modname=hosting&amp;op=register">میزبانی وب</a> مراجعه فرمائید.</p>\r\n'),
				'article_url' => 'مطلب-ابتدایی',
				'ihome' => 1,
				'allow_comment' => 1,
				'cat_link' => 1,
				'cat_link' => '0',
				'permissions' => '0',
				'position' => 1,
			));
		
		// add new blocks
		$default_blocks = array(
			array(1, 'Articles tags', 'block-Articles_tags.php'),
			array(2, 'Languages', 'block-Languages.php'),
			array(3, 'Last 5 Articles', 'block-Last_5_Articles.php'),
			array(4, 'MT-Forums', 'block-MT-Forums.php'),
			array(5, 'MT-ForumsTabed', 'block-MT-ForumsTabed.php'),
			array(6, 'Search', 'block-Search.php'),
			array(7, 'User Info', 'block-User_Info.php'),
			array(8, 'archive', 'block-archive.php'),
			array(9, 'comments', 'block-comments.php'),
			array(10, 'surveys', 'block-surveys.php'),
			array(11, 'Last comments', 'block-Last_comments.php')
		);
		
		$db->table(BLOCKS_TABLE)
			->multiinsert(array('bid','title','blockfile'), $default_blocks);			
		
		$left_blocks_data = array(
			'7' => array(
				'title' => 'User Info',
				'lang_titles' => '',
				'blanguage' => '',
				'weight' => 1,
				'active' => 1,
				'time' => _NOWTIME,
				'permissions' => 0,
				'publish' => 0,
				'expire' => 0,
				'action' => '',
				'theme_block' => ''
			)
		);

		$right_blocks_data = array(
			'3' => array(
				'title' => 'Last 5 Articles',
				'lang_titles' => '',
				'blanguage' => '',
				'weight' => 1,
				'active' => 1,
				'time' => _NOWTIME,
				'permissions' => 0,
				'publish' => 0,
				'expire' => 0,
				'action' => '',
				'theme_block' => ''
			),
			'6' => array(
				'title' => 'Search',
				'lang_titles' => '',
				'blanguage' => '',
				'weight' => 2,
				'active' => 1,
				'time' => _NOWTIME,
				'permissions' => 0,
				'publish' => 0,
				'expire' => 0,
				'action' => '',
				'theme_block' => ''
			),
			'10' => array(
				'title' => 'surveys',
				'lang_titles' => '',
				'blanguage' => '',
				'weight' => 3,
				'active' => 1,
				'time' => _NOWTIME,
				'permissions' => 0,
				'publish' => 0,
				'expire' => 0,
				'action' => '',
				'theme_block' => ''
			),
			'8' => array(
				'title' => 'archive',
				'lang_titles' => '',
				'blanguage' => '',
				'weight' => 4,
				'active' => 1,
				'time' => _NOWTIME,
				'permissions' => 0,
				'publish' => 0,
				'expire' => 0,
				'action' => '',
				'theme_block' => ''
			)
		);
		
		$topcenter_blocks_data = array(
			'5' => array(
				'title' => 'MT-ForumsTabed',
				'lang_titles' => '',
				'blanguage' => '',
				'weight' => 1,
				'active' => 1,
				'time' => _NOWTIME,
				'permissions' => 0,
				'publish' => 0,
				'expire' => 0,
				'action' => '',
				'theme_block' => ''
			)
		);
		
		$left_blocks_data = addslashes(phpnuke_serialize($left_blocks_data));
		$right_blocks_data = addslashes(phpnuke_serialize($right_blocks_data));
		$topcenter_blocks_data = addslashes(phpnuke_serialize($topcenter_blocks_data));
		
		$db->table(BLOCKS_BOXES_TABLE)
			->where("box_id" , 'left')
			->update(array(
				'box_blocks' => '7',
				'box_blocks_data' => $left_blocks_data,
			));
			
		$db->table(BLOCKS_BOXES_TABLE)
			->where("box_id" , 'right')
			->update(array(
				'box_blocks' => '3,6,10,8',
				'box_blocks_data' => $right_blocks_data,
			));
		if($install_options['db_info']['db_have_forum'] == 1)
		{
			$db->table(BLOCKS_BOXES_TABLE)
				->where("box_id" , 'topcenter')
				->update(array(
					'box_blocks' => '5',
					'box_blocks_data' => $topcenter_blocks_data,
				));
		}	
		// add new uncategorized
		
		$default_cat = array(
			array(1,1,'Articles','uncategorized','a:2:{s:7:\"english\";s:13:\"uncategorized\";s:5:\"farsi\";s:19:\"بدون موضوع\";}')
		);
		$db->table(CATEGORIES_TABLE)
			->multiinsert(array('catid','type','module','catname','cattext'),$default_cat);
			
		// add new poll
		$db->table(SURVEYS_TABLE)
			->insert(array(
				'pollID' => 1,
				'status' => 1,
				'aid' => $install_options['admininfo']['aid'],
				'canVote' => 1,
				'to_main' => 1,
				'multi_vote' => 0,
				'show_voters_num' => 0,
				'permissions' => "0",
				'allow_comment' => 1,
				'main_survey' => 1,
				'pollTitle' => 'نظر شما در مورد این سایت ؟',
				'pollUrl' => 'نظر-شما-در-مورد-این-سایت',
				'start_time' => _NOWTIME,
				'options' => 'a:4:{i:0;a:2:{i:0;s:8:\"عالی\";i:1;i:0;}i:1;a:2:{i:0;s:8:\"خوبی\";i:1;i:0;}i:2;a:2:{i:0;s:10:\"متوسط\";i:1;i:0;}i:3;a:2:{i:0;s:8:\"ضعیف\";i:1;i:0;}}',
			));
	}
	else
	{
		$db->table(AUTHORS_TABLE)
			->where("pwd" , $install_options['admininfo']['pwd'])
			->update(array(
				'pwd' => $pwd,
			));
	}
			
	// add new nav menu data	
	$nav_menus_data = array(
		array(1, 1, 1, 0, 1, 'صفحه اصلی', 'index.html', 'a:4:{s:6:\"target\";s:0:\"\";s:3:\"xfn\";s:0:\"\";s:7:\"classes\";s:0:\"\";s:6:\"styles\";s:0:\"\";}', 'custom', '', 0),
		array(2, 1, 1, 0, 2, 'تماس با ما', 'index.php?modname=Feedback', 'a:4:{s:6:\"target\";s:0:\"\";s:3:\"xfn\";s:0:\"\";s:7:\"classes\";s:0:\"\";s:6:\"styles\";s:0:\"\";}', 'custom', '', 0),
		array(3, 1, 1, 0, 3, 'تالار گفتمان', 'Forum', 'a:4:{s:6:\"target\";s:0:\"\";s:3:\"xfn\";s:0:\"\";s:7:\"classes\";s:0:\"\";s:6:\"styles\";s:0:\"\";}', 'custom', '', 0),
		array(4, 1, 1, 0, 4, 'جستجو', 'index.php?modname=Search', 'a:4:{s:6:\"target\";s:0:\"\";s:3:\"xfn\";s:0:\"\";s:7:\"classes\";s:0:\"\";s:6:\"styles\";s:0:\"\";}', 'custom', '', 0)
	);
	
	$db->table(NAV_MENUS_DATA_TABLE)
		->multiinsert(array('nid','status','nav_id','pid','weight','title','url','attributes','type','module','part_id'),$nav_menus_data);
		
	$rename_error = '';
	if(!rename("install","install".rand(1,1000)) || !rename("install.php","install".rand(2000,9999).".php.back"))
	{
		$rename_error = "<br />فایل و پوشه install قابل تغییر نام نیستند. لطفاً ابتدا این فایل و پوشه را حذف یا تغییر نام دهید و سپس به بخشهای دیگر بروید";
	}	
	
	upgrade_progress_output("تکمیل ".(($install_options['mode'] == 'install') ? "نصب":"بروزرسانی").$rename_error, 1, 1, 0, "", "", 100, 1000);
	
	$cache->flush_caches();
	cache_system('all');
	die();

}

$op = (isset($op)) ? $op:"start";
$step = (isset($step)) ? $step:"";
$start = (isset($start)) ? intval($start):0;
$transfer_counter = (isset($transfer_counter)) ? intval($transfer_counter):0;
$counter = (isset($counter)) ? intval($counter):0;

$function = (isset($step) && $step != '') ? "step_".$step:"upgrade_".$op;

$function($start);

?>