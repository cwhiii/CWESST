<?php

// Process POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';
    $table  = $_POST['table']  ?? '';
    if (isset($tables[$table])) {
        $file = $tables[$table]['file'];
        $data = readCSV($file);
        $today = date('Y-m-d');

        switch ($action) {
            case 'add':
                $new = array_fill(0, count($data['header']), '');
                $new[0] = getNextId($data['rows']);
                for ($i = 1; $i < count($data['header']); $i++) {
                    $new[$i] = $_POST[$data['header'][$i]] ?? '';
                }
                if ($table === 'stories') {
                    $wcIndex   = array_search('WordCount', $data['header']);
                    $typeIndex = array_search('Type', $data['header']);
                    if ($wcIndex !== false && $typeIndex !== false) {
                        $wordCount = intval($new[$wcIndex]);
                        if ($wordCount > 0) {
                            $new[$typeIndex] = determineStoryType($wordCount);
                        }
                    }
                }
                // Update last column (e.g. DateRecordModified)
                $new[count($data['header']) - 1] = $today;
                $data['rows'][] = $new;
                break;

            case 'update':
                $updates = json_decode($_POST['updates'], true);
                $id      = $_POST['record_id'];
                foreach ($data['rows'] as &$row) {
                    if ($row[0] == $id) {
                        foreach ($updates as $col => $val) {
                            if (($idx = array_search($col, $data['header'])) !== false) {
                                $row[$idx] = $val;
                            }
                        }
                        if ($table === 'stories') {
                            $wcIndex   = array_search('WordCount', $data['header']);
                            $typeIndex = array_search('Type', $data['header']);
                            if ($wcIndex !== false && $typeIndex !== false && !empty($updates['WordCount'])) {
                                $row[$typeIndex] = determineStoryType(intval($updates['WordCount']));
                            }
                        }
                        $row[count($data['header']) - 1] = $today;
                        break;
                    }
                }
                break;

            case 'delete':
                $id = $_POST['record_id'];
                $data['rows'] = array_filter($data['rows'], fn($r) => $r[0] != $id);
                break;
        }
        die(json_encode([
            'status' => writeCSV($file, $data['header'], $data['rows']) ? 'success' : 'error'
        ]));
    }
}

$storiesData   = readCSV($tables['stories']['file'])['rows'];
$publishersData = readCSV($tables['publishers']['file'])['rows'];

?>