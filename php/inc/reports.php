<?php

function load_transactions() {
    $file = __DIR__ . '/../data/transaksi.json';
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function get_date_for_tx($tx) {
    if (!empty($tx['tanggal'])) return new DateTime($tx['tanggal']);
    if (!empty($tx['createdAt'])) return new DateTime(substr($tx['createdAt'], 0, 10));
    return new DateTime();
}

function summarize_transactions($transactions, $mode = 'daily', $start = null, $end = null) {
    $groups = [];
    foreach ($transactions as $t) {
        $dt = get_date_for_tx($t);
        if ($start && $dt < new DateTime($start)) continue;
        if ($end && $dt > (new DateTime($end))->setTime(23,59,59)) continue;

        if ($mode === 'daily') {
            $key = $dt->format('Y-m-d');
            $label = $dt->format('Y-m-d');
        } elseif ($mode === 'weekly') {
            // ISO week: year-week
            $key = $dt->format('o-W');
            // derive week start and end for readable label
            list($y, $w) = explode('-', $key);
            $weekStart = (new DateTime())->setISODate((int)$y, (int)$w)->format('Y-m-d');
            $weekEnd = (new DateTime())->setISODate((int)$y, (int)$w)->modify('+6 days')->format('Y-m-d');
            $label = "{$weekStart} to {$weekEnd}";
        } else {    
            $key = $dt->format('Y-m');
            $label = $dt->format('Y-m');
        }

        if (!isset($groups[$key])) $groups[$key] = ['label' => $label, 'count' => 0, 'total' => 0.0, 'items' => []];
        $groups[$key]['count']++;
        $groups[$key]['total'] += floatval($t['nominal'] ?? 0);
        $groups[$key]['items'][] = $t;
    }
    ksort($groups);
    return $groups;
}

function csv_download_response($filename, $rows) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['periode', 'count', 'total']);
    foreach ($rows as $key => $r) {
        fputcsv($out, [$r['label'], $r['count'], number_format($r['total'], 2, '.', '')]);
    }
    fclose($out);
    exit;
}

?>