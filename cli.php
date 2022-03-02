<?php declare(strict_types=1);

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;

require_once "vendor/autoload.php";
require_once "classes/Curl.php";
require_once "classes/GoogleResultsParser.php";

$outputDir   = "output";
$url         = "https://www.google.de/search?q=";
$searchParam = "test";
$quantity    = 15;

if ( ! is_dir($outputDir)) {
    mkdir($outputDir, 755);
}

$currentPage = 1;
$results     = [];
while (count($results) < $quantity) {
    $queryUrl = $url.urlencode($searchParam)."&start=".($currentPage * 10 - 10);
    print "Querying: ".$queryUrl.PHP_EOL;

    $html = Curl::getHtmlResult($queryUrl);
    if ( ! $html) {
        print "An error querying $queryUrl occured".PHP_EOL;
        break;
    }

    $googleResultsParser = new GoogleResultsParser($html);
    $parserResults       = $googleResultsParser->getSearchResults($quantity - count($results));

    if (count($parserResults) == 0) {
        print "No search Results found".PHP_EOL;
        break;
    }

    $results = array_merge($results, $parserResults);
    $currentPage++;
}