<?php

if (!function_exists('curl_init')) {
    die("Script depends on curl");
}

function deleteDirectory($directory)
{
    if (!is_dir($directory)) {
        return;
    }

    $files = array_diff(scandir($directory), array('.', '..'));

    foreach ($files as $file) {
        $path = $directory . '/' . $file;

        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            unlink($path);
        }
    }

    rmdir($directory);
}

function copyDirectory($source, $destination)
{
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }

    $dir = opendir($source);
    if (!$dir) {
        die("Can't read assets dir: " . $source);
    }
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $sourcePath = $source . '/' . $file;
            $destinationPath = $destination . '/' . $file;

            if (is_dir($sourcePath)) {
                copyDirectory($sourcePath, $destinationPath);
            } else {
                copy($sourcePath, $destinationPath);
            }
        }
    }

    closedir($dir);
}

function doHTMLReplacements($html)
{
    global $iconCss, $wikiCss, $wikiJs;
    $html = str_replace($iconCss, "icons.css", $html);
    $html = str_replace($wikiCss, "wiki.css", $html);
    $html = str_replace($wikiJs, "wiki.js", $html);
    $html = str_replace("__WIKI_BUILD_VERSION__", time(), $html);
    $html = str_replace("url('/ABE", "url('ABE", $html);

    return $html;
}

function downloadWebPage($url, $outputFile)
{
    echo "Download " . $url . " Output: " . $outputFile;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($curl);
    echo "\n\r|" . $content . "|";
    curl_close($curl);
    return file_put_contents($outputFile, doHTMLReplacements($content));
}


function download($codename, $downloadUrl, $outputFolder)
{
    $rawName = str_replace("text_wiki_", "", $codename);
    $filename = $rawName . ".html";
    downloadWebPage($downloadUrl . $rawName, $outputFolder . $filename);
}

function makeDefaultFolders()
{
    if (!is_dir("wiki")) {
        mkdir("wiki", 0777, true);
    }
    if (!is_dir("wiki/ABE")) {
        mkdir("wiki/ABE", 0777, true);
    }
    if (!is_dir("wiki/ABE/deaddesert")) {
        mkdir("wiki/ABE/deaddesert", 0777, true);
    }
    if (!is_dir("wiki/ABE/deaddesert/png")) {
        mkdir("wiki/ABE/deaddesert/png", 0777, true);
    }
    if (!is_dir("raw")) {
        mkdir("raw", 0777, true);
    }
    if (!is_dir("img")) {
        mkdir("img", 0777, true);
    }
}

function downloadAllItems($downloadUrl, $outputFolder)
{
    global $_pa;

    $page = null;

    $downloadList = [];

    while ($_pa->_db->_assoc_into("select * from items_default where codename like 'text_wiki_%p'", $page)) {
        $downloadList[] = $page['codename'];
    }

    foreach ($downloadList as $codename) {
        download($codename, $downloadUrl, $outputFolder);
    }
}


function downloadExtras($downloadList, $outputFolder)
{

    foreach ($downloadList as $filename => $downloadUrl) {
        downloadWebPage($downloadUrl, $outputFolder . $filename);
    }
}
