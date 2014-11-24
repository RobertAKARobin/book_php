<? require "r/scripts.php" ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<base target="_top" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Learn web design without a computer</title>
<link rel="stylesheet" href="indexcss.css" />
<? if($_SERVER["REMOTE_ADDR"] != "::1") : ?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<? else: ?>
<script type="text/javascript" src="jquery.js"></script>
<? endif ?>
<script type="text/javascript" src="indexjs.js"></script>
</head>
<body>

<div id="header">
<h1>Learn web design without a computer</h1>
<div class="noprint">
<p><button type="button" onclick="window.print();">Print the whole thing!</button></p>
<p>What's confusing? Poorly-designed? Completely left out?</p>
<form id="email" action="email.php" target="emailFrame" method="post" enctype="multipart/form-data">
<textarea name="message" placeholder="Write your feedback here!"></textarea>
<input type="text" name="email" placeholder="Your e-mail address (Optional)" />
<input type="submit" value="Click to submit!" />
</form>
</div>
<p><span class="noprint">Or e-mail me: </span><a href="mailto:hello@robertakarobin.com" target="_blank">hello&#64;robertakarobin&#46;com</a></p>
</div>

<div>
<h2>Table of Contents</h2>
<ul id="contents">
<?

chdir("c");
ob_start();
$chapters = json_decode(file_get_contents("chapters.json"),true);
$pageNum = 0;
foreach($chapters as $chapter => $sections){
    $nav .= "<li>$chapter\n\r<ul>\r\n";
    foreach($sections as $id => $title){
        $navHeader = $title;
        $pageHeader = $title;
        $pageNum++;
        
        if(is_array($title)){
            $navHeader = "$title[0]: $title[1]";
            $pageHeader = "<span>$title[0]: </span> $title[1]";
        }
        
        if(!empty($navHeader)) $nav .= "<li><a href=\"#$id\"><span>$pageNum</span>$navHeader</a></li>";
        
        $content .= "<div id=\"$id\"><h6>$chapter <span>$pageNum</span></h6>";
        if(!empty($pageHeader)) $content .= "<h2><a href=\"#$id\">$pageHeader</a></h2>";
        include("$id.html");
        $content .= ob_get_contents();
        ob_clean();
        $content .= "\r\n</div>\r\n\r\n";
    }
    $nav .= "</ul>\r\n</li>\r\n";
}
ob_end_clean();
chdir("..");

echo $nav;

?>
</ul>
</div>

<? echo $content ?>

</body>
</html>