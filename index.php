<?php
// readtext index.php

include 'app/hosts.php';
include 'app/readtext.php';
include 'app/Util.php';
//include 'app/content.php';
include 'app/proxy.php';
include 'app/Config.php';
include 'app/Source.php';

$config = new Config();
//Util::debug_log($config->settings);

$reader = new Reader();

$link = filter_input(INPUT_GET, 'link');
$redirect = filter_input(INPUT_GET, 'redirect');
$render = filter_input(INPUT_GET, 'render');
$utf8 = filter_input(INPUT_GET, 'utf8');
$refresh = filter_input(INPUT_GET, 'refresh');
$data = null;

// Fetch content
header("Access-Control-Allow-Origin: *");
$content = filter_input(INPUT_POST, 'content');
$title = filter_input(INPUT_POST, 'title');
$path =  isset($config->settings['target'])? $config->settings['target']: '';
//$target = $path . 'readtext.html';
$url = filter_input(INPUT_POST, 'url');
if ($content) {
    $target = $path . str_replace(' ', '_', $title) . '.txt';
    file_put_contents($target, $url . ';' . $title . ';'. $content);
    exit();
}
else if ($render) {
    $target = $path . str_replace(' ', '_', $render) . '.txt';
    $data = $reader->makeHtmlPage($target);
}
else if ($link) {
    //'http://www.smashingmagazine.com/2013/05/06/new-defaults-web-design/#more-91968';
    $data = $reader->read($link, $utf8, $refresh);
}
else if ($redirect) {
    $data = $reader->readRedirect($redirect, $utf8, $refresh);
}
else {
    // Redirect to usage page.
    header('Location: usage.php');
    //$data = $reader->makeHtmlPage($target);
}
/*
 * <!--
Author: jan
Date: 15-mei-2014
-->

 */
//header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?=$data['title']?></title>
        <link rel="stylesheet" href="css/style.css?v=1">
        <link rel="icon" href="<?=$data['scheme']?>://<?=$data['hostname']?>/favicon.ico">
        <?php if (!empty($data['cssPrint'])) { ?>
        <link rel="stylesheet" href="<?=$data['cssPrint']?>">
        <?php } ?>
        <?php if (!empty($data['css'])) { 
            if (is_array($data['css'])) foreach($data['css'] as $cssHref) { ?>
                <link rel="stylesheet" href="<?=$cssHref?>">
            <?php }
            else { ?>
                <link rel="stylesheet" href="<?=$data['css']?>">
            <?php } ?>
        <?php } ?>
        <?php if (!empty($data['style'])) { ?>
        <style><?=$data['style']?></style>
        <?php } ?>
        <?php if (!empty($data['script'])) { ?>
        <script><?=$data['script']?></script>
        <?php } ?>
        <!--suppress JSUnresolvedLibraryURL, HtmlUnknownTarget -->
        <script src="node_modules/jquery/dist/jquery.min.js"></script>
        <script>var host='<?=$data['hostname']?>';</script>
        <script src="js/script.js"></script>
        <?php if (!empty($data['js'])) {
            if (is_array($data['js'])) foreach($data['js'] as $jsSrc) { ?>
                <script src="<?=$jsSrc?>"></script
            <?php }
            else { ?>
                <script src="<?=$data['js']?>"></script
            <?php } ?>
        <?php } ?>
    </head>
    <body>
        <div class="container <?=$data['clshost']?>">
            <div class="main">
               <div class="main-head">
                   <div class="originallink">
                       <a href="<?=$data['originalUrl']?>"
                            title="<?=$data['originalUrl']?>"
                            class="<?=$data['unknown']?'unknown':''?>"><?=$data['hostname']?></a>
                       <br>
                       <?php if ($data['unknown'] && !isset($_REQUEST['utf8'])) { ?>
                           <a href="#" class="utf8">utf8</a>
                       <?php } ?>
                       <?php if (!isset($_REQUEST['refresh'])) { ?>
                           | <a href="#" class="refresh">refresh</a>
                       <?php } else { ?>
                           | refreshed
                       <?php } ?>
                   </div>
               </div>
       <?php if (!empty($data['logo'])) { ?>
                <img src="images/<?=$data['logo']?>" class="rw-logo">
       <?php } ?>
                            <?php if (!empty($data['header'])) { ?>
                <h1><?=$data['header']?></h1>
                            <?php } ?>
                <div class="text_body"><?=$data['content']?></div>
        <?php if (isset($data['error'])) { ?>
            <div class="error"><?=$data['error']?></div>
        <?php } ?>
            </div>
        </div>
        <div class="eop">
            <div class="end-of-page sprite"></div>
        </div>
    </body>
</html>
