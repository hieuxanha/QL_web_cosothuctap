<?php
$app_id = '67885791';
$app_key = 'b230494ae33397c8ef909565514b216d';
$country = 'vn'; // Việt Nam

$url = "https://api.adzuna.com/v1/api/jobs/$country/categories?app_id=$app_id&app_key=$app_key";

$response = file_get_contents($url);
$data = json_decode($response, true);

$labels = [];
$counts = [];

foreach ($data['results'] as $item) {
    $labels[] = $item['label'];
    $counts[] = $item['count'];
}

echo json_encode([
    'labels' => $labels,
    'data' => $counts
]);
