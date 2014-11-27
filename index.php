<?
require "r/scripts.php";

if($_SERVER["SERVER_NAME"] != "localhost") $js = <<<HTML
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
HTML;
else $js = <<<HTML
<script type="text/javascript" src="jquery.js"></script>
HTML;

chdir("c");
ob_start();
$chapters = json_decode(file_get_contents("chapters.json"),true);
$pageNum = 0;
foreach($chapters as $chapter => $sections){
    $nav .= <<<HTML
    <tr class="head"><th colspan="3">$chapter</th></tr>
HTML;
    foreach($sections as $id => $title){
        $pageNum++;
        $pageId = $id;
        $pageDescription = $title;
        $oldPageTopic = $pageTopic;
        
        if(is_array($title)){
            $pageTopic = $title[0];
            $pageDescription = $title[1];
        }
        $colon = (empty($pageTopic) ? "" : ":");
        
        $nav .= <<<HTML

    <tr>
    <td class="num"><a href="#$pageId">$pageNum</a></td>
    <td class="topic"><a href="#$pageId">$pageTopic&nbsp;</a></td>
    <td class="desc"><a href="#$pageId">$pageDescription</a></td>
    </tr>
HTML;
        
        $content .= <<<HTML
    <div id="$pageId"><h6>$chapter <span>$pageNum</span></h6>
    <h2><a href="#$id"><span>$pageTopic$colon</span> $pageDescription</a></h2>
HTML;
        include("$id.html");
        $content .= ob_get_contents();
        ob_clean();
        $content .= "\r\n</div>\r\n\r\n";
    }
}
ob_end_clean();
chdir("..");

echo <<<HTML

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<base target="_top" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Learn web design without a computer</title>
<link rel="stylesheet" href="indexcss.css" />
$js
<script type="text/javascript" src="indexjs.js"></script>
</head>
<body>

<div id="header">
<h1>Learn web design without a computer</h1>
<p><button type="button" onclick="window.print();">Print all $pageNum pages!</button></p>
<p>Something confusing? Left out? Ugly?</p>
<p>E-mail me:</p>
<p><a href="mailto:hello@robertakarobin.com" target="_blank">hello&#64;robertakarobin&#46;com</a></p>
</div>

<div id="contents">
<h2>Table of Contents</h2>
<table>

$nav

</table>
</div>

$content

</body>
</html>

HTML;
?>