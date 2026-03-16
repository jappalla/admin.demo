<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Multi-page PDF generator using raw PDF 1.4 primitives.
 *
 * Produces A4 pages with a modern two-column layout:
 * left sidebar for contacts/skills, main body for profile/experiences.
 */
final class SimplePdf
{
    // ── Layout constants (A4: 595 x 842 pt) ──

    private const PAGE_W = 595.0;
    private const PAGE_H = 842.0;

    // Sidebar
    private const SIDEBAR_W = 175.0;
    private const SIDEBAR_BG = [0.11, 0.15, 0.22]; // dark navy
    private const SIDEBAR_TEXT = [0.82, 0.87, 0.95];
    private const SIDEBAR_HEADING = [0.45, 0.78, 0.95]; // cyan accent
    private const SIDEBAR_PAD_X = 18.0;

    // Main body
    private const BODY_LEFT = 195.0;
    private const BODY_RIGHT = 570.0;
    private const BODY_TOP = 790.0;
    private const BODY_BOTTOM = 50.0;
    private const BODY_PAD_X = 20.0;

    // Header
    private const HEADER_H = 90.0;

    // Footer
    private const FOOTER_Y = 25.0;

    // Colors
    private const COLOR_DARK = [0.13, 0.18, 0.28];
    private const COLOR_BODY = [0.25, 0.30, 0.40];
    private const COLOR_MUTED = [0.45, 0.50, 0.60];
    private const COLOR_ACCENT = [0.20, 0.56, 0.85];
    private const COLOR_WHITE = [1.0, 1.0, 1.0];

    public static function fromLines(array $lines, string $title = 'Curriculum Vitae'): string
    {
        $parsed = self::parseLines($lines);
        $pageStreams = self::buildPages($parsed, $title);
        return self::buildDocument($pageStreams, $title);
    }

    private static function parseLines(array $lines): array
    {
        $sections = [
            'title' => '',
            'subtitle' => '',
            'profile' => '',
            'experiences' => [],
            'skills' => '',
            'contacts' => [],
        ];

        $normalizedLines = [];
        foreach ($lines as $line) {
            $row = str_replace(["\r\n", "\r"], "\n", trim((string) $line));
            foreach (explode("\n", $row) as $segment) {
                $normalizedLines[] = trim($segment);
            }
        }

        if (isset($normalizedLines[0])) {
            $sections['title'] = str_replace('Curriculum Vitae - ', '', $normalizedLines[0]);
        }
        if (isset($normalizedLines[1])) {
            $sections['subtitle'] = $normalizedLines[1];
        }

        $currentSection = '';
        for ($i = 2, $count = count($normalizedLines); $i < $count; $i++) {
            $line = $normalizedLines[$i];

            if ($line === 'PROFILO') {
                $currentSection = 'profile';
                continue;
            }
            if ($line === 'ESPERIENZE') {
                $currentSection = 'experiences';
                continue;
            }
            if ($line === 'COMPETENZE') {
                $currentSection = 'skills';
                continue;
            }
            if ($line === 'CONTATTI') {
                $currentSection = 'contacts';
                continue;
            }

            if ($line === '') continue;

            switch ($currentSection) {
                case 'profile':
                    $sections['profile'] .= ($sections['profile'] !== '' ? ' ' : '') . $line;
                    break;
                case 'experiences':
                    if (str_starts_with($line, '- ')) {
                        $sections['experiences'][] = ['title' => substr($line, 2), 'description' => ''];
                    } elseif (str_starts_with($line, '  ') && $sections['experiences'] !== []) {
                        $last = array_key_last($sections['experiences']);
                        $sections['experiences'][$last]['description'] .=
                            ($sections['experiences'][$last]['description'] !== '' ? ' ' : '') . trim($line);
                    }
                    break;
                case 'skills':
                    $sections['skills'] .= ($sections['skills'] !== '' ? ' ' : '') . $line;
                    break;
                case 'contacts':
                    if (str_contains($line, ':')) {
                        [$key, $value] = explode(':', $line, 2);
                        $sections['contacts'][trim($key)] = trim($value);
                    }
                    break;
            }
        }

        return $sections;
    }

    /**
     * @return string[] Array of content streams, one per page
     */
    private static function buildPages(array $data, string $title): array
    {
        $pages = [];
        $currentCommands = [];
        $pageNumber = 1;

        // Start first page
        self::addPageChrome($currentCommands, $data, $pageNumber);

        // --- Main body content ---
        $bodyX = self::BODY_LEFT + self::BODY_PAD_X;
        $bodyMaxW = self::BODY_RIGHT - $bodyX;
        $y = self::PAGE_H - self::HEADER_H - 20.0;

        // Profile section
        if ($data['profile'] !== '') {
            self::addSectionHeading($currentCommands, $bodyX, $y, 'PROFILO');
            $y -= 18.0;

            $maxChars = (int) floor($bodyMaxW / 5.2);
            $wrapped = self::wrapText($data['profile'], $maxChars);
            foreach ($wrapped as $wLine) {
                if ($y < self::BODY_BOTTOM) {
                    self::addPageFooter($currentCommands, $pageNumber);
                    $pages[] = implode("\n", $currentCommands) . "\n";
                    $pageNumber++;
                    $currentCommands = [];
                    self::addPageChrome($currentCommands, $data, $pageNumber);
                    $y = self::PAGE_H - 30.0;
                }
                self::addTextLine($currentCommands, $bodyX, $y, 'F1', 9, self::COLOR_BODY, $wLine);
                $y -= 13.0;
            }
            $y -= 8.0;
        }

        // Experiences section
        if ($data['experiences'] !== []) {
            if ($y < self::BODY_BOTTOM + 40.0) {
                self::addPageFooter($currentCommands, $pageNumber);
                $pages[] = implode("\n", $currentCommands) . "\n";
                $pageNumber++;
                $currentCommands = [];
                self::addPageChrome($currentCommands, $data, $pageNumber);
                $y = self::PAGE_H - 30.0;
            }

            self::addSectionHeading($currentCommands, $bodyX, $y, 'ESPERIENZE PROFESSIONALI');
            $y -= 18.0;

            $maxCharsTitle = (int) floor($bodyMaxW / 5.4);
            $maxCharsDesc = (int) floor($bodyMaxW / 5.0);

            foreach ($data['experiences'] as $exp) {
                if ($y < self::BODY_BOTTOM + 30.0) {
                    self::addPageFooter($currentCommands, $pageNumber);
                    $pages[] = implode("\n", $currentCommands) . "\n";
                    $pageNumber++;
                    $currentCommands = [];
                    self::addPageChrome($currentCommands, $data, $pageNumber);
                    $y = self::PAGE_H - 30.0;
                }

                // Accent dot + title
                self::addFilledCircle($currentCommands, $bodyX + 3.0, $y + 3.0, 2.5, self::COLOR_ACCENT);
                $wrappedTitle = self::wrapText($exp['title'], $maxCharsTitle - 4);
                foreach ($wrappedTitle as $idx => $tLine) {
                    $tx = $idx === 0 ? $bodyX + 10.0 : $bodyX + 10.0;
                    self::addTextLine($currentCommands, $tx, $y, 'F2', 9, self::COLOR_DARK, $tLine);
                    $y -= 13.0;
                }

                if ($exp['description'] !== '') {
                    $wrappedDesc = self::wrapText($exp['description'], $maxCharsDesc);
                    foreach ($wrappedDesc as $dLine) {
                        if ($y < self::BODY_BOTTOM) {
                            self::addPageFooter($currentCommands, $pageNumber);
                            $pages[] = implode("\n", $currentCommands) . "\n";
                            $pageNumber++;
                            $currentCommands = [];
                            self::addPageChrome($currentCommands, $data, $pageNumber);
                            $y = self::PAGE_H - 30.0;
                        }
                        self::addTextLine($currentCommands, $bodyX + 10.0, $y, 'F1', 8, self::COLOR_MUTED, $dLine);
                        $y -= 11.5;
                    }
                }

                $y -= 6.0;
            }
        }

        // Finalize last page
        self::addPageFooter($currentCommands, $pageNumber);
        $pages[] = implode("\n", $currentCommands) . "\n";

        // Replace total pages placeholder
        $totalPages = count($pages);
        foreach ($pages as &$stream) {
            $stream = str_replace('{{TOTAL_PAGES}}', (string) $totalPages, $stream);
        }
        unset($stream);

        return $pages;
    }

    private static function addPageChrome(array &$commands, array $data, int $pageNumber): void
    {
        // White background
        self::addFilledRect($commands, 0.0, 0.0, self::PAGE_W, self::PAGE_H, [0.98, 0.98, 1.0]);

        // Left sidebar (full height)
        self::addFilledRect($commands, 0.0, 0.0, self::SIDEBAR_W, self::PAGE_H, self::SIDEBAR_BG);

        // Accent strip at top of sidebar
        self::addFilledRect($commands, 0.0, self::PAGE_H - 4.0, self::SIDEBAR_W, 4.0, self::COLOR_ACCENT);

        // Header bar on right side
        $headerY = self::PAGE_H - self::HEADER_H;
        self::addFilledRect($commands, self::SIDEBAR_W, $headerY, self::PAGE_W - self::SIDEBAR_W, self::HEADER_H, [0.14, 0.19, 0.30]);

        // Name in header
        $titleName = $data['title'] !== '' ? $data['title'] : 'Curriculum Vitae';
        self::addTextLine($commands, self::BODY_LEFT + self::BODY_PAD_X, $headerY + 55.0, 'F2', 20, self::COLOR_WHITE, $titleName);

        // Subtitle
        if ($data['subtitle'] !== '') {
            self::addTextLine($commands, self::BODY_LEFT + self::BODY_PAD_X, $headerY + 35.0, 'F1', 9, [0.70, 0.75, 0.85], $data['subtitle']);
        }

        // Title "Full-Stack Developer" under name
        self::addTextLine($commands, self::BODY_LEFT + self::BODY_PAD_X, $headerY + 18.0, 'F2', 11, self::SIDEBAR_HEADING, 'Full-Stack Developer');

        // --- Sidebar content (only on page 1) ---
        if ($pageNumber === 1) {
            self::addSidebarContent($commands, $data);
        }
    }

    private static function addSidebarContent(array &$commands, array $data): void
    {
        $x = self::SIDEBAR_PAD_X;
        $y = self::PAGE_H - self::HEADER_H - 25.0;
        $maxW = self::SIDEBAR_W - (2 * self::SIDEBAR_PAD_X);
        $maxChars = (int) floor($maxW / 4.6);

        // CONTATTI section
        if ($data['contacts'] !== []) {
            self::addTextLine($commands, $x, $y, 'F2', 9, self::SIDEBAR_HEADING, 'CONTATTI');
            $y -= 4.0;
            self::addLine($commands, $x, $y, $x + 35.0, $y, self::SIDEBAR_HEADING, 1.5);
            $y -= 14.0;

            foreach ($data['contacts'] as $label => $value) {
                self::addTextLine($commands, $x, $y, 'F2', 7, self::SIDEBAR_HEADING, strtoupper((string) $label));
                $y -= 11.0;
                $wrappedValue = self::wrapText((string) $value, $maxChars);
                foreach ($wrappedValue as $vLine) {
                    self::addTextLine($commands, $x, $y, 'F1', 7, self::SIDEBAR_TEXT, $vLine);
                    $y -= 10.0;
                }
                $y -= 4.0;
            }
            $y -= 8.0;
        }

        // COMPETENZE section in sidebar
        if ($data['skills'] !== '') {
            self::addTextLine($commands, $x, $y, 'F2', 9, self::SIDEBAR_HEADING, 'COMPETENZE');
            $y -= 4.0;
            self::addLine($commands, $x, $y, $x + 35.0, $y, self::SIDEBAR_HEADING, 1.5);
            $y -= 14.0;

            $skillList = array_map('trim', explode(',', $data['skills']));
            foreach ($skillList as $skillName) {
                if ($skillName === '') continue;
                if ($y < 40.0) break;

                // Skill pill
                self::addFilledRoundRect($commands, $x, $y - 3.0, min(strlen($skillName) * 4.5 + 10, $maxW), 12.0, [0.18, 0.24, 0.34]);
                self::addTextLine($commands, $x + 5.0, $y, 'F1', 7, self::SIDEBAR_TEXT, $skillName);
                $y -= 16.0;
            }
        }
    }

    private static function addSectionHeading(array &$commands, float $x, float &$y, string $text): void
    {
        // Accent line
        self::addLine($commands, $x, $y - 2.0, $x + 45.0, $y - 2.0, self::COLOR_ACCENT, 2.0);
        // Heading text
        self::addTextLine($commands, $x + 50.0, $y, 'F2', 11, self::COLOR_DARK, $text);
        $y -= 6.0;
        // Thin separator
        self::addLine($commands, $x, $y - 2.0, self::BODY_RIGHT, $y - 2.0, [0.88, 0.90, 0.93], 0.5);
    }

    private static function addPageFooter(array &$commands, int $pageNumber): void
    {
        self::addLine($commands, self::BODY_LEFT, self::FOOTER_Y + 10.0, self::BODY_RIGHT, self::FOOTER_Y + 10.0, [0.88, 0.90, 0.93], 0.5);
        self::addTextLine($commands, self::BODY_LEFT + self::BODY_PAD_X, self::FOOTER_Y, 'F1', 7, self::COLOR_MUTED, 'Generato dalla piattaforma CV di Antonio Trapasso');
        $pageLabel = 'Pag. ' . $pageNumber . ' / {{TOTAL_PAGES}}';
        self::addTextLine($commands, 510.0, self::FOOTER_Y, 'F1', 7, self::COLOR_MUTED, $pageLabel);
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

    private static function addFilledRoundRect(
        array &$commands,
        float $x,
        float $y,
        float $w,
        float $h,
        array $color
    ): void {
        $r = min(4.0, $w / 2, $h / 2);
        $k = 0.5523; // bezier approximation of quarter circle
        $commands[] = 'q';
        $commands[] = self::formatColor($color) . ' rg';
        $n = static fn(float $v) => self::formatNumber($v);
        $commands[] = $n($x + $r) . ' ' . $n($y) . ' m';
        $commands[] = $n($x + $w - $r) . ' ' . $n($y) . ' l';
        $commands[] = $n($x + $w - $r + $r * $k) . ' ' . $n($y) . ' ' . $n($x + $w) . ' ' . $n($y + $r - $r * $k) . ' ' . $n($x + $w) . ' ' . $n($y + $r) . ' c';
        $commands[] = $n($x + $w) . ' ' . $n($y + $h - $r) . ' l';
        $commands[] = $n($x + $w) . ' ' . $n($y + $h - $r + $r * $k) . ' ' . $n($x + $w - $r + $r * $k) . ' ' . $n($y + $h) . ' ' . $n($x + $w - $r) . ' ' . $n($y + $h) . ' c';
        $commands[] = $n($x + $r) . ' ' . $n($y + $h) . ' l';
        $commands[] = $n($x + $r - $r * $k) . ' ' . $n($y + $h) . ' ' . $n($x) . ' ' . $n($y + $h - $r + $r * $k) . ' ' . $n($x) . ' ' . $n($y + $h - $r) . ' c';
        $commands[] = $n($x) . ' ' . $n($y + $r) . ' l';
        $commands[] = $n($x) . ' ' . $n($y + $r - $r * $k) . ' ' . $n($x + $r - $r * $k) . ' ' . $n($y) . ' ' . $n($x + $r) . ' ' . $n($y) . ' c';
        $commands[] = 'f';
        $commands[] = 'Q';
    }

    private static function addFilledCircle(
        array &$commands,
        float $cx,
        float $cy,
        float $r,
        array $color
    ): void {
        $k = 0.5523 * $r;
        $n = static fn(float $v) => self::formatNumber($v);
        $commands[] = 'q';
        $commands[] = self::formatColor($color) . ' rg';
        $commands[] = $n($cx + $r) . ' ' . $n($cy) . ' m';
        $commands[] = $n($cx + $r) . ' ' . $n($cy + $k) . ' ' . $n($cx + $k) . ' ' . $n($cy + $r) . ' ' . $n($cx) . ' ' . $n($cy + $r) . ' c';
        $commands[] = $n($cx - $k) . ' ' . $n($cy + $r) . ' ' . $n($cx - $r) . ' ' . $n($cy + $k) . ' ' . $n($cx - $r) . ' ' . $n($cy) . ' c';
        $commands[] = $n($cx - $r) . ' ' . $n($cy - $k) . ' ' . $n($cx - $k) . ' ' . $n($cy - $r) . ' ' . $n($cx) . ' ' . $n($cy - $r) . ' c';
        $commands[] = $n($cx + $k) . ' ' . $n($cy - $r) . ' ' . $n($cx + $r) . ' ' . $n($cy - $k) . ' ' . $n($cx + $r) . ' ' . $n($cy) . ' c';
        $commands[] = 'f';
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
