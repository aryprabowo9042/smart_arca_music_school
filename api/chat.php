<?php
require_once('koneksi.php');

// Pastikan tidak ada karakter keluar sebelum header ini
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Halo! Ada yang bisa Arca bantu mengenai kursus musik?']);
    exit;
}

$apiKey = GROQ_API_KEY;
$url = "https://api.groq.com/openai/v1/chat/completions";

$messages = [
    ["role" => "system", "content" => "Anda adalah Arca AI, asisten Smart Arca Music School Weleri. Ramah, ceria, dan informatif. Produk kita: Kelas Drum, Keyboard, Gitar Akustik, Gitar Elektrik, Bas, dan Vokal. Lokasi: Jl. Tamtama, Sekepel, Penyangkringan, Weleri. Admin WA: 0895360796038."],
    ["role" => "user", "content" => $userMessage]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["model" => "llama3-8b-8192", "messages" => $messages]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Authorization: Bearer $apiKey"]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$reply = $result['choices'][0]['message']['content'] ?? 'Maaf, Arca sedang latihan musik sebentar. Coba tanya lagi ya!';

echo json_encode(['reply' => $reply]);
