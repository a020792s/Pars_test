<?php
/*ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
$start = microtime(true);
$scriptStart = date("Y-m-d H:i:s");

use Symfony\Component\DomCrawler\Crawler;

require_once('vendor/autoload.php');
try {
    $host = 'https://www.carid.com';
    $ch = curl::getInstance($host)
        ->ssl(0)
        ->follow(1)
        ->referer("https://carid.com")
        ->agent("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36")
        //->tor()     //uncomment it if you want to use TOR
    ;
    db::getInstance()->renameOldAndCreateNewTable();
    for ($j = 1; $j <= 5; $j++) {
        $result = $ch->request("/suspension-systems.html?page=" . $j);
        $crawler = new Crawler($result);
        $html = $crawler->filter('.lst_a');
        foreach ($html as $i => $node) {
            $result = $ch->request($node->getAttribute('href'));
            $product = new product($result, $host);
            db::getInstance()->insertProduct($product);
        }
    }
    $report = new reportMaker();
    $report->createAndSendReports($start, $scriptStart);
    //$ch->tor_new_identity();     //uncomment it if you want to use TOR
    //sleep(15);                   //uncomment it if you want to use TOR
} catch (Exception $e) {
    echo $e->getCode() . " - " . $e->getMessage();
}