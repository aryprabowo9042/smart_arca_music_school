<?php
require_once('koneksi.php');
ob_clean();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';

// DETEKSI APAKAH KEY TERBACA
$key = getenv('GROQ_API_KEY');

if (!$key) {
    echo json_encode(['reply' => 'Error: Server Vercel tidak menemukan variabel GROQ_API_KEY. Pastikan sudah Redeploy.']);
    exit;
}

$ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . $key
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => "llama3-8b-8192",
    "messages" => [
        ["role" => "system", "content" => "Asisten Smart Arca Music School."],
        ["role" => "user", "content" => $message]
    ]
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error_curl = curl_error($ch);
curl_close($ch);

if ($httpCode == 403) {
    echo json_encode(['reply' => 'Pesan 403: Groq menolak API Key ini. Coba buat Key baru di dashboard Groq.']);
} elseif ($httpCode !== 200) {
    echo json_encode(['reply' => 'Error dari Groq (Kode: ' . $httpCode . '). ' . $error_curl]);
} else {
    $res = json_decode($response, true);
    echo json_encode(['reply' => $res['choices'][0]['message']['content']]);
}
