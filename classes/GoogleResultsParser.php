<?php declare(strict_types=1);

use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDomInterface;

class GoogleResultsParser
{
    private HtmlDomParser $dom;
    private array $results = [];

    public function __construct(string $html)
    {
        $this->dom = HtmlDomParser::str_get_html($html);
    }

    /**
     * returns all search results
     *
     * @param int $quantity
     *
     * @return array[array[title, href, text],...]
     */
    public function getSearchResults(int $quantity = INF): array
    {
        $elements = $this->dom->findMulti('.g');

        if ($elements->count()) {
            foreach ($elements as $element) {
                $result = $this->getSearchResult($element);

                if ( ! $result) {
                    continue;
                }

                $this->results[] = $result;

                if (count($this->results) >= $quantity) {
                    break;
                }
            }
        }

        return $this->results;
    }

    /**
     * checks if the given dom node is a valid search result
     * extracts title, href and text
     *
     * @param SimpleHtmlDomInterface $element
     *
     * @return bool|array[title, href, text]
     */
    private function getSearchResult(SimpleHtmlDomInterface $element): bool|array
    {
        if ( ! $element->hasAttribute("data-hveid")) {
            return false;
        }

        $titleNode = $element->findOneOrFalse("h3");
        if ($titleNode) {
            $result = [];

            $result["title"] = $titleNode->plaintext;

            $linkNode = $titleNode->parentNode();
            if ($linkNode->hasAttribute("href")) {
                $result["href"] = $linkNode->getAttribute("href");

                $textContainerNode = $linkNode->parentNode()->nextNonWhitespaceSibling();

                if ( ! $textContainerNode) {
                    $textContainerNode = $element->findOneOrFalse('div[data-content-feature="1"]');
                }

                $result["text"] = $textContainerNode->plaintext;
            }

            return $result;
        }

        return false;
    }
}