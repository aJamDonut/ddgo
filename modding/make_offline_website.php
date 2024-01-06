<?php
error_reporting(E_WARNING);
ini_set('error_reporting', E_ALL);

$cli_mode = false;
if (!isset($_pa)) {
    //CLI MODE
    $cli_mode = true;
    require("../config.php");
    require(ABS_DIR . "/classes/page.inc.php");
    $_pa = new Page(false);
}

require("offline_helper.inc.php");

$_pa->template = "none";

$downloadUrl = "http://127.0.0.1:32564/gd_cp_modules/http/modules/gd_mod_2d/pages/page.php?template=../../gd_cp_modules/templates/wiki&type=wiki&page=";
$outputFolder = "wiki/";
$assetsFolder = "../../../../../ABE/deaddesert/png/";
$assetsDestination = "wiki/ABE/deaddesert/png/";
$wikiImgFolder = "../pages/img/";
$wikiImgDestination = "wiki/img/";
$iconCss = "http://127.0.0.1:32564/gd_cp_modules/http/modules/gd_mod_2d/dev_stubs/ext_css.php";
$wikiCss  = "http://127.0.0.1:32564/gd_cp_modules/http/modules/gd_mod_2d/pages/wikicss.css";
$wikiJs  = "http://127.0.0.1:32564/gd_cp_modules/http/modules/gd_mod_2d/pages/wiki.js";
$wikiConfig  = "http://127.0.0.1:32564/gd_cp_modules/http/modules/gd_mod_2d/pages/app.json";

deleteDirectory($outputFolder);

makeDefaultFolders();

if ($cli_mode) {
    copyDirectory($assetsFolder, $wikiImgDestination);
}

copyDirectory($assetsFolder, $assetsDestination);

downloadWebPage($iconCss, "wiki/icons.css");
downloadWebPage($wikiCss, "wiki/wiki.css");
downloadWebPage($wikiJs, "wiki/wiki.js");
downloadWebPage($wikiConfig, "wiki/app.json");


//File => URL extras to download
$downloadList = [
    "item_report_swords.html" => "http://127.0.0.1:32564/gd_cp_modules/http/modules/gd_mod_2d/pages/items_report.php?addLinks=false&showEdit=false&show=swords&template=../../gd_cp_modules/templates/wiki"

];
downloadExtras($downloadList, $outputFolder);

//Download all items where codename like 'text_wiki_%p'
downloadAllItems($downloadUrl, $outputFolder);
