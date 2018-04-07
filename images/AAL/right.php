<?php
$author_name = "Floppy";
$author_email = "floppydrivez@hotmail.com";
$author_homepage = "http://www.t3gamingcommunity.com";
$license = "GNU/GPL";
$download_location = "http://www.t3gamingcommunity.com/index.php?modname=Downloads";
$block_version = "V2";
$block_stages = "Released August 31, 2006";
$block_description = "AAL is fully fucntional and configurable admin login block with expandable menus.";

function copyright() {
    global $author_name, $author_email, $author_homepage, $license, $download_location, $block_version, $block_stages, $block_description;
    if ($author_name == "") { $author_name = "N/A"; }
    if ($author_email == "") { $author_email = "N/A"; }
    if ($author_homepage == "") { $author_homepage = "N/A"; }
    if ($license == "") { $license = "N/A"; }
    if ($download_location == "") { $download_location = "N/A"; }
    if ($block_version == "") { $block_version = "N/A"; }
    if ($block_stages == "") { $block_stages = "Released"; }
    if ($block_description == "") { $block_description = "N/A"; }
    $block_name = basename(dirname(__FILE__));
    $block_name = @str_replace("_", " ", $block_name);
    echo "<html>\n"
        ."<body bgcolor=\"#CFCFCF\" link=\"#363636\" alink=\"#363636\" vlink=\"#363636\">\n"
        ."<title>$block_name: Copyright Information</title>\n"
        ."<font size=\"2\" color=\"#363636\" face=\"Verdana, Helvetica\">\n"
        ."<div class=\"text-center\"><b>Block Copyright &copy; Information</b><br>"
        ."$block_name block for <a href=\"http://phpnuke.org\" target=\"new\">PHP-Nuke</a><br><br></div>\n"
        ."<img src=\"../../images/AAL/arrow.gif\" border=\"0\">&nbsp;<b>Block Name:</b> $block_name<br>\n"
        ."<img src=\"../../images/AAL/arrow.gif\" border=\"0\">&nbsp;<b>Block Version:</b> $block_version<br>\n"
        ."<img src=\"../../images/AAL/arrow.gif\" border=\"0\">&nbsp;<b>Block Status:</b> $block_stages<br>\n"
        ."<img src=\"../../images/AAL/arrow.gif\" border=\"0\">&nbsp;<b>Block Description:</b> $block_description<br>\n"
        ."<img src=\"../../images/AAL/arrow.gif\" border=\"0\">&nbsp;<b>License:</b> $license<br>\n"
        ."<img src=\"../../images/AAL/arrow.gif\" border=\"0\">&nbsp;<b>Author's Name:</b> $author_name<br>\n"
        ."<img src=\"../../images/AAL/arrow.gif\" border=\"0\">&nbsp;<b>Author's Email:</b> $author_email<br><br>\n"
        ."<div class=\"text-center\">[ <a href=\"$author_homepage\" target=\"new\">Author's HomePage</a> | <a href=\"$download_location\" target=\"new\">Block's Download</a> | <a href=\"javascript:void(0)\" onClick=javascript:self.close()>Close</a> ]</div>\n"
        ."</font>\n"
        ."</body>\n"
        ."</html>";
}

copyright();

?>