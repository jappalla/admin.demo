<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Multi-page PDF generator using raw PDF 1.4 primitives.
 *
 * Produces A4 pages with styled header, automatic page breaks,
 * page numbers and a consistent footer on every page.
 */
final class SimplePdf
{
    // ── Layout constants (A4: 595 x 842 pt) ──

    private const PAGE_W = 595.0;
    private const PAGE_H = 842.0;

    private const PANEL_X = 30.0;
    private const PANEL_Y = 26.0;
    private const PANEL_W = 535.0;
    private const PANEL_H = 790.0;

    private const HEADER_Y = 738.0;
    private const HEADER_H = 78.0;

    private const BODY_LEFT = 52.0;
    private const BODY_RIGHT = 543.0;
    private const BODY_TOP = 718.0;
    private const BODY_BOTTOM = 56.0;

    private const FOOTER_LINE_Y = 36.0;
    private const FOOTER_TEXT_Y = 23.0;

    public static function fromLines(array $lines, string $title = 'Curriculum Vitae'): string
    {
        $normalizedLines = self::normalizeLines($lines);
        if ($normalizedLines === []) {
            $normalizedLines = [$title];
        }

        $headerTitle = trim($normalizedLines[0]) !== '' ? trim($normalizedLines[0]) : $title;
        $headerSubtitle = isset($normalizedLines[1]) ? trim($normalizedLines[1]) : '';
        $contentLines = array_slice($normalizedLines, 2);

        $pageStreams = self::buildPages($headerTitle, $headerSubtitle, $contentLines);
        return self::buildDocument($pageStreams, $title);
    }

    // ── Multi-page layout engine ──

    /**
     * @return string[] Array of content streams, one per page
     */
    private static function buildPages(string $title, string $subtitle, array $contentLines): array
    {
        $maxBodyWidth = self::BODY_RIGHT - self::BODY_LEFT;
        $pages = [];
        $currentCommands = [];
        $y = self::BODY_TOP;
        $pageNumber = 1;

        // Start first page
        self::addPageBackground($currentCommands, $title, $subtitle);

        $lineIndex = 0;
        $totalLines = count($contentLines);

        while ($lineIndex < $totalLines) {
            $line = trim($contentLines[$lineIndex]);

            if ($line === '') {
                $y -= 8.0;
                $lineIndex++;
                continue;
            }

            if (self::isSectionHeading($line)) {
                // Section headings need 22pt + at least 30pt for next content line
                if ($y < self::BODY_BOTTOM + 52.0) {
                    self::addPageFooter($currentCommands, $pageNumber, $title);
                    $pages[] = implode("\n", $currentCommands) . "\n";
                    $pageNumber++;
                    $currentCommands = [];
                    self::addPageBackground($currentCommands, $title, $subtitle);
                    $y = self::BODY_TOP;
                }

                self::addFilledRect($currentCommands, 44.0, $y - 4.0, 505.0, 18.0, [0.9, 0.95, 1.0]);
                self::addTextLine($currentCommands, 50.0, $y + 1.0, 'F2', 11, [0.12, 0.24, 0.4], $line);
                self::addLine($currentCommands, 44.0, $y - 6.0, 549.0, $y - 6.0, [0.76, 0.84, 0.93], 0.7);
                $y -= 22.0;
                $lineIndex++;
                continue;
            }

            // Regular content line
            $isBullet = str_starts_with($line, '- ');
            $font = $isBullet ? 'F2' : 'F1';
            $fontSize = 10;
            $color = $isBullet ? [0.12, 0.19, 0.3] : [0.21, 0.27, 0.38];
            $lineHeight = $isBullet ? 14.0 : 13.5;
            $maxChars = max(30, (int) floor($maxBodyWidth / max(1.0, ($fontSize * 0.53))));
            $wrapped = self::wrapText($line, $maxChars);

            foreach ($wrapped as $wrappedLine) {
                if ($y < self::BODY_BOTTOM) {
                    self::addPageFooter($currentCommands, $pageNumber, $title);
                    $pages[] = implode("\n", $currentCommands) . "\n";
                    $pageNumber++;
                    $currentCommands = [];
                    self::addPageBackground($currentCommands, $title, $subtitle);
                    $y = self::BODY_TOP;
                }

                self::addTextLine($currentCommands, self::BODY_LEFT, $y, $font, $fontSize, $color, $wrappedLine);
                $y -= $lineHeight;
            }

            $lineIndex++;
        }

        // Finalize last page
        self::addPageFooter($currentCommands, $pageNumber, $title);
        $pages[] = implode("\n", $currentCommands) . "\n";

        // Add total page count to all footers
        $totalPages = count($pages);
        if ($totalPages > 1) {
            foreach ($pages as $idx => &$stream) {
                $stream = str_replace('{{TOTAL_PAGES}}', (string) $totalPages, $stream);
            }
            unset($stream);
        }

        return $pages;
    }

    private static function addPageBackground(array &$commands, string $title, string $subtitle): void
    {
        // Background
        self::addFilledRect($commands, 0.0, 0.0, self::PAGE_W, self::PAGE_H, [0.96, 0.98, 1.0]);
        // Card panel
        self::addFilledRect($commands, self::PANEL_X, self::PANEL_Y, self::PANEL_W, self::PANEL_H, [1.0, 1.0, 1.0]);
        self::addStrokedRect($commands, self::PANEL_X, self::PANEL_Y, self::PANEL_W, self::PANEL_H, [0.84, 0.89, 0.95], 1.0);
        // Header bar
        self::addFilledRect($commands, self::PANEL_X, self::HEADER_Y, self::PANEL_W, self::HEADER_H, [0.11, 0.2, 0.36]);
        self::addFilledRect($commands, self::PANEL_X, self::HEADER_Y, 6.0, self::HEADER_H, [0.24, 0.64, 0.92]);
        // Title
        self::addTextLine($commands, 48.0, 783.0, 'F2', 21, [1.0, 1.0, 1.0], $title);
        if ($subtitle !== '') {
            self::addTextLine($commands, 48.0, 764.0, 'F1', 10, [0.86, 0.91, 0.98], $subtitle);
        }
    }

    private static function addPageFooter(array &$commands, int $pageNumber, string $title): void
    {
        self::addLine($commands, 44.0, self::FOOTER_LINE_Y, 549.0, self::FOOTER_LINE_Y, [0.84, 0.89, 0.95], 0.8);
        self::addTextLine(
            $commands,
            48.0,
            self::FOOTER_TEXT_Y,
            'F1',
            8,
            [0.42, 0.5, 0.63],
            'Documento generato automaticamente dalla piattaforma CV'
        );
        // Page number (right-aligned)
        $pageLabel = 'Pag. ' . $pageNumber . ' / {{TOTAL_PAGES}}';
        self::addTextLine($commands, 490.0, self::FOOTER_TEXT_Y, 'F1', 8, [0.42, 0.5, 0.63], $pageLabel);
    }

    // ── PDF document builder ──

    /**
     * @param string[] $pageStreams
     */
    private static function buildDocument(array $pageStreams, string $title): string
    {
        $pageCount = count($pageStreams);

        // Replace {{TOTAL_PAGES}} placeholder if single page
        if ($pageCount === 1) {
            $pageStreams[0] = str_replace('{{TOTAL_PAGES}}', '1', $pageStreams[0]);
        }

        // Object IDs:
        // 1 = Catalog, 2 = Pages root, 3 = Font Helvetica, 4 = Font Helvetica-Bold, 5 = Info
        // Then for each page: pageObj, contentsObj (2 objects per page)
        // Page objects start at ID 6

        $fontRegularId = 3;
        $fontBoldId = 4;
        $infoId = 5;
        $firstPageId = 6;

        $objects = [];
        $pageRefList = [];

        // Font objects
        $objects[$fontRegularId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[$fontBoldId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';
        $objects[$infoId] = '<< /Title (' . self::escapePdfText($title) . ') /Producer (Antonio Trapasso CV Platform) >>';

        $nextId = $firstPageId;
        foreach ($pageStreams as $stream) {
            $pageId = $nextId;
            $contentsId = $nextId + 1;
            $nextId += 2;

            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 ' . $fontRegularId . ' 0 R /F2 ' . $fontBoldId . ' 0 R >> >> /Contents ' . $contentsId . ' 0 R >>';
            $objects[$contentsId] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
            $pageRefList[] = $pageId . ' 0 R';
        }

        // Build Pages root (object 2) and Catalog (object 1)
        $kidsString = implode(' ', $pageRefList);
        $objects[2] = '<< /Type /Pages /Kids [' . $kidsString . '] /Count ' . $pageCount . ' >>';
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';

        // Assemble PDF
        $totalObjects = $nextId - 1;
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];

        for ($id = 1; $id <= $totalObjects; $id++) {
            if (!isset($objects[$id])) {
                continue;
            }
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $objects[$id] . "\nendobj\n";
        }

        $xrefStart = strlen($pdf);
        $xrefCount = $totalObjects + 1;
        $pdf .= "xref\n0 " . $xrefCount . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($id = 1; $id <= $totalObjects; $id++) {
            if (isset($offsets[$id])) {
                $pdf .= sprintf('%010d 00000 n ' . "\n", $offsets[$id]);
            } else {
                $pdf .= "0000000000 65535 f \n";
            }
        }

        $pdf .= "trailer\n<< /Size " . $xrefCount . " /Root 1 0 R /Info " . $infoId . " 0 R >>\n";
        $pdf .= "startxref\n" . $xrefStart . "\n%%EOF";

        return $pdf;
    }

    // ── Helper methods ──

    private static function normalizeLines(array $lines): array
    {
        $normalized = [];

        foreach ($lines as $line) {
            $row = str_replace(["\r\n", "\r"], "\n", trim((string) $line));
            if ($row === '') {
                $normalized[] = ' ';
                continue;
            }

            $segments = explode("\n", $row);
            foreach ($segments as $segment) {
                $segment = trim($segment);
                if ($segment === '') {
                    $normalized[] = ' ';
                    continue;
                }

                $wrapped = wordwrap($segment, 92, "\n", true);
                foreach (explode("\n", $wrapped) as $wrappedLine) {
                    $normalized[] = trim($wrappedLine);
                }
            }
        }

        return $normalized;
    }

    private static function addTextLine(
        array &$commands,
        float $x,
        float $y,
        string $fontAlias,
        int $fontSize,
        array $color,
        string $text
    ): void {
        $commands[] = 'BT';
        $commands[] = self::formatColor($color) . ' rg';
        $commands[] = '/' . $fontAlias . ' ' . $fontSize . ' Tf';
        $commands[] = '1 0 0 1 ' . self::formatNumber($x) . ' ' . self::formatNumber($y) . ' Tm';
        $commands[] = '(' . self::escapePdfText($text) . ') Tj';
        $commands[] = 'ET';
    }

    private static function addFilledRect(
        array &$commands,
        float $x,
        float $y,
        float $w,
        float $h,
        array $color
    ): void {
        $commands[] = 'q';
        $commands[] = self::formatColor($color) . ' rg';
        $commands[] = self::formatNumber($x) . ' ' . self::formatNumber($y) . ' ' .
            self::formatNumber($w) . ' ' . self::formatNumber($h) . ' re f';
        $commands[] = 'Q';
    }

    private static function addStrokedRect(
        array &$commands,
        float $x,
        float $y,
        float $w,
        float $h,
        array $color,
        float $lineWidth
    ): void {
        $commands[] = 'q';
        $commands[] = self::formatColor($color) . ' RG';
        $commands[] = self::formatNumber($lineWidth) . ' w';
        $commands[] = self::formatNumber($x) . ' ' . self::formatNumber($y) . ' ' .
            self::formatNumber($w) . ' ' . self::formatNumber($h) . ' re S';
        $commands[] = 'Q';
    }

    private static function addLine(
        array &$commands,
        float $x1,
        float $y1,
        float $x2,
        float $y2,
        array $color,
        float $lineWidth
    ): void {
        $commands[] = 'q';
        $commands[] = self::formatColor($color) . ' RG';
        $commands[] = self::formatNumber($lineWidth) . ' w';
        $commands[] = self::formatNumber($x1) . ' ' . self::formatNumber($y1) . ' m';
        $commands[] = self::formatNumber($x2) . ' ' . self::formatNumber($y2) . ' l S';
        $commands[] = 'Q';
    }

    private static function formatColor(array $color): string
    {
        $r = isset($color[0]) ? (float) $color[0] : 0.0;
        $g = isset($color[1]) ? (float) $color[1] : 0.0;
        $b = isset($color[2]) ? (float) $color[2] : 0.0;

        return self::formatNumber($r) . ' ' . self::formatNumber($g) . ' ' . self::formatNumber($b);
    }

    private static function formatNumber(float $number): string
    {
        return rtrim(rtrim(number_format($number, 3, '.', ''), '0'), '.');
    }

    private static function isSectionHeading(string $value): bool
    {
        return preg_match('/^[A-Z0-9À-ÖØ-Þ \-]{3,40}$/u', $value) === 1;
    }

    private static function wrapText(string $text, int $maxChars): array
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return [''];
        }

        $wrapped = wordwrap($trimmed, $maxChars, "\n", true);
        $lines = array_filter(array_map(
            static fn(string $line): string => trim($line),
            explode("\n", $wrapped)
        ), static fn(string $line): bool => $line !== '');

        return array_values($lines);
    }

    private static function escapePdfText(string $value): string
    {
        $encoded = self::toPdfEncoding($value);
        $encoded = preg_replace('/[\x00-\x1F\x7F]/', '', $encoded) ?? '';

        return str_replace(
            ['\\', '(', ')'],
            ['\\\\', '\\(', '\\)'],
            $encoded
        );
    }

    private static function toPdfEncoding(string $value): string
    {
        $normalized = trim($value);
        if ($normalized === '') {
            return '';
        }

        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $normalized);
        if (is_string($converted) && $converted !== '') {
            return $converted;
        }

        return preg_replace('/[^\x20-\x7E]/', '', $normalized) ?? '';
    }
}
