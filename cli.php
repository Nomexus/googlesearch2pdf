<?php declare(strict_types=1);

require_once "vendor/autoload.php";
require_once "classes/CurlHelper.php";
require_once "classes/GoogleResultsParser.php";
require_once "classes/PdfHelper.php";

if ( ! isset($argv[1]) || ! isset($argv[2])) {
    print "Please provide at least two parameters: searchParam and quantity".PHP_EOL;
    exit;
}

$searchParam = $argv[1];
$quantity    = $argv[2];

if ( ! is_numeric($quantity) || $quantity <= 0) {
    print "The quantity parameter must be greater than 0".PHP_EOL;
    exit;
}

$outputDir = "output";
$url       = "https://www.google.de/search?q=";

if ( ! is_dir($outputDir)) {
    mkdir($outputDir, 755);
}

$currentPage = 1;
$results     = [];
while (count($results) < $quantity) {
    $queryUrl = $url.urlencode($searchParam)."&start=".($currentPage * 10 - 10);
    print "Querying: ".$queryUrl.PHP_EOL;

    $html = CurlHelper::getHtmlResult($queryUrl);
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

if (count($results) > 0) {
    $resultHtml = "";
    foreach ($results as $result) {
        $resultHtml .= PdfHelper::resultToHtml($result);
    }

    $replaces = [
        "searchParam" => $searchParam,
        "results"     => $resultHtml,
    ];

    PdfHelper::googleResultsToPdf(
        $outputDir,
        $replaces
    );
}