<?php declare(strict_types=1);

use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;

class PdfHelper
{
    /**
     * @param string $outputPath
     * @param array $replaces
     *
     * @return bool
     */
    public static function googleResultsToPdf(string $outputPath, array $replaces = []): bool
    {
        $html = file_get_contents("assets/pdf.html");
        $css  = file_get_contents("assets/pdf.css");

        try {
            $mpdf = new Mpdf();

            $mpdf->WriteHTML($css, HTMLParserMode::HEADER_CSS);

            if (count($replaces)) {
                foreach ($replaces as $replace => $value) {
                    $html = str_replace("{{".$replace."}}", $value, $html);
                }
            }

            $mpdf->SetHTMLFooter('<div class="footer">Seite {PAGENO} von {nbpg}</div>');
            $mpdf->WriteHTML($html);

            $fullPath = $outputPath."/".date("Y-m-d_H-i-s")."_search.pdf";
            $mpdf->Output($fullPath, Destination::FILE);

            print "File saved: $fullPath".PHP_EOL;

            return true;
        } catch (MpdfException $e) {
            print $e->getMessage();

            return false;
        }
    }

    /**
     * generates simple html blocks in google search result format
     *
     * @param array $result
     *
     * @return string
     */
    public static function resultToHtml(array $result): string
    {
        $html = "<div class='result'>";
        $html .= "<a href='{$result["href"]}'>";
        $html .= "<span class='link'>{$result["href"]}</span>";
        $html .= "</a>";
        $html .= "<h3 class='title'>";
        $html .= "<a href='{$result["href"]}'>";
        $html .= "{$result["title"]}";
        $html .= "</a>";
        $html .= "</h3>";
        $html .= "<div class='text'>";
        $html .= "{$result["text"]}";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }
}