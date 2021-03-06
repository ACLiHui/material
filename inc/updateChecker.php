<?php

function getLatestReleaseUrl() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/manyang901/material/releases/latest');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Material Update Checker/php-curl extension');
    $result = curl_exec($ch);

    curl_close($ch);
    $result = json_decode($result);
    return $result->assets[0]->browser_download_url;
}

function getFile () {
    global $theme_root;
    $fileurl = getLatestReleaseUrl();
    $output = fopen($theme_root . '/material.tar.gz', 'w+');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fileurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Material Update Checker/php-curl extension');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    fwrite($output, $result);
    fclose($output);
}

function update() {
    getFile();
    global $theme_root;
    $phar = new PharData('material.tar.gz');
    $phar->extractTo($theme_root,null,true);
    unlink($theme_root . '/material.tar.gz');
    updateVersion();
    echo 'Update Success!';
    return true;
}

function getLatestReleaseTime() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/manyang901/material/releases/latest');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Material Update Checker/php-curl extension');
    $result = curl_exec($ch);

    curl_close($ch);
    $result = json_decode($result);
    return $result->published_at;
}

function checkUpdate() {
    global $version_file_path;
    $version_filename = $version_file_path;
    $f = fopen($version_filename, 'r');
    $version = fgets($f);
    if ($version < getLatestReleaseTime()) {
        return true;
    } else {
        return false;
    }
}

function updateVersion() {
    global $version_file_path;  
    $version_filename = $version_file_path;
    $f = fopen($version_filename, 'w');
    fwrite($f, date('c'));
    fclose($f);
}


?>
