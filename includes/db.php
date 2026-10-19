<?php
$json_path = __DIR__ . '/../data/store_data.json';

if (!file_exists(dirname($json_path))) {
    mkdir(dirname($json_path), 0777, true);
}

function get_store_data() {
    global $json_path;

    $default_data = [
        "creations" => [],
        "videos" => []
    ];

    if (!file_exists($json_path)) {
        save_store_data($default_data);
        return $default_data;
    }

    $content = file_get_contents($json_path);
    $decoded = json_decode($content, true);

    if (!is_array($decoded)) {
        return $default_data;
    }

    if (!isset($decoded['creations']) || !is_array($decoded['creations'])) {
        $decoded['creations'] = [];
    }

    if (!isset($decoded['videos']) || !is_array($decoded['videos'])) {
        $decoded['videos'] = [];
    }

    return $decoded;
}

function save_store_data($data) {
    global $json_path;
    file_put_contents($json_path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
?>