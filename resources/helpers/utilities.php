<?php


/**
 * Reads a CSV file and returns an array with header and rows.
 */
function readCSV(string $filename): array {
    if (!file_exists($filename)) {
        return ['header' => [], 'rows' => []];
    }
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data  = array_map('str_getcsv', $lines);
    return ['header' => array_shift($data), 'rows' => $data];
}

/**
 * Writes CSV data to a file.
 */
function writeCSV(string $filename, array $header, array $rows): bool {
    $fh = fopen('php://temp', 'rw');
    fputcsv($fh, $header);
    foreach ($rows as $row) {
        fputcsv($fh, $row);
    }
    rewind($fh);
    $csv = stream_get_contents($fh);
    fclose($fh);
    return file_put_contents($filename, $csv) !== false;
}

/**
 * Returns the next available ID based on the first column.
 */
function getNextId(array $rows): int {
    return $rows ? max(array_column($rows, 0)) + 1 : 1;
}

/**
 * Looks up a value by ID in CSV rows.
 */
function getLookupValue($id, array $data, int $col = 1): string {
    foreach ($data as $row) {
        if ($row[0] == $id) {
            return $row[$col];
        }
    }
    return '';
}

/**
 * Determines story type based on word count.
 */
function determineStoryType(int $wordCount): string {
    if ($wordCount < 2500)  return '';
    if ($wordCount <= 2500) return 'Flash Fiction';
    if ($wordCount <= 7500) return 'Short Story';
    if ($wordCount <= 17000)return 'Novellette';
    if ($wordCount <= 50000)return 'Novella';
    return 'Novel';
}

/**
 * Converts plain text URLs, emails, and twitter handles into clickable links.
 */
function linkifyText(string $text): string {
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $text = preg_replace_callback('/\b((https?:\/\/|www\.)[^\s<]+)/i', function ($m) {
        $url  = $m[1];
        $href = preg_match('/^https?:\/\//i', $url) ? $url : "http://$url";
        return "<a href=\"$href\" target=\"_blank\" rel=\"noopener noreferrer\">$url</a>";
    }, $text);
    $text = preg_replace_callback('/\b([\w.+-]+@[\w.-]+\.[A-Za-z]{2,})\b/', function ($m) {
        return "<a href=\"mailto:{$m[1]}\">{$m[1]}</a>";
    }, $text);
    $text = preg_replace_callback('/\B@([\w_]{1,15})\b/', function ($m) {
        return "<a href=\"https://twitter.com/{$m[1]}\" target=\"_blank\" rel=\"noopener noreferrer\">@{$m[1]}</a>";
    }, $text);
    return $text;
}

?>
