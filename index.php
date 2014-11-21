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

<header id="title">
<h1>Learn web design without a computer</h1>
<p><button type="button" onclick="window.print();">Print the whole thing!</button></p>
<div class="noprint">
<p>I'm working on this every day until it's perfect! </p>
<form id="email" action="email.php" target="emailFrame" method="post" enctype="multipart/form-data">
<textarea name="message" placeholder="Please write feedback here!"></textarea>
<input type="text" name="email" placeholder="Your e-mail address (Optional)" />
<input type="submit" value="Click to submit!" />
</form>
</div>
<p><span class="noprint">Or e-mail me: </span><a href="mailto:hello@robertakarobin.com" target="_blank">hello&#64;robertakarobin&#46;com</a></p>
</header>

<main>

<section id="contents">
<h2>Table of Contents</h2>
<nav>
<ol>
<?

chdir("c");
ob_start();
$chapters = json_decode(file_get_contents("chapters.json"),true);
foreach($chapters as $chapter => $sections){
    $nav .= "<li>$chapter\n\r<ol>\r\n";
    foreach($sections as $id => $title){
        $nav .= "<li><a href=\"#$id\">$title</a></li>\r\n";
        $content .= "<section id=\"$id\">\n\r<h2><a href=\"#$id\">$title</a></h2>\n\r";
        include("$id.html");
        $content .= ob_get_contents();
        ob_clean();
        $content .= "\n\r</section>";
    }
    $nav .= "</ol>\r\n</li>\r\n";
}
ob_end_clean();
chdir("..");

echo $nav;

?>
</ol>
</nav>
</section>

<? echo $content ?>

</main>

</body>
</html>