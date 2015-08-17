<html>
<body>
<?php

require_once 'vendor/autoload.php';

use Sample\LinkCrawler;

function printNode($node)
{
    echo('<li>');
    if ( trim($node['title']) )
    {
        $title = $node['title'];
    }
    else
    {
        $title = '[NO TEXT]';
    }
    printf('%s - <a href="%s">%s</a>', $title, $node['url'], $node['url']);
    if ( $node['error'] )
    {
        printf(' - <span style="color: red">%s</span>', $node['error']);
    }
    if ( $node['descendants'] )
    {
        echo('<ul>');
        foreach ( $node['descendants'] as $d )
        {
            printNode($d);
        }
        echo('</ul>');
    }
    echo('</li>');
}

$n = $_GET['n'] ?: 1;
$url = $_GET['url'] ?: 'http://www.google.com';

if ( $n && $url )
{
    $linkCrawler = new LinkCrawler($n);

    $links = $linkCrawler->run($url);

    printf('<h1>%s, N = %d</h1>', $url, $n);
    echo('<ul>');
    printNode($links);
    echo('</ul>');
}

?>

<form method="GET">
    <label for="n">
        N: <input type="text" name="n" value="<?= $n ?>">
    </label>
    <label for="n">
        URL: <input type="text" name="url" value="<?= $url ?>">
    </label>
    <input type="submit" value="Submit">
</form>

</body>
</html>
