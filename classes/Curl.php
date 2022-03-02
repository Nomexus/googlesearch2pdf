<?php declare(strict_types=1);

class Curl {
    /**
     * helper function to quickly retrieve the html of a page
     *
     * @param string $url
     *
     * @return bool|string
     */
    public static function getHtmlResult(string $url): bool|string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch,
            CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.51 Safari/537.36"
        );

        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }
}