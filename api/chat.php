<?php
require_once('koneksi.php');

header('Content-Type: application/json');

// Mengambil pesan dari user
$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Ada yang bisa Arca bantu, Kak?']);
    exit;
}

// Setting untuk API Groq
$apiKey = GROQ_API_KEY;
$model = "llama3-8b-8192"; // Model cepat dan pintar

$url = "https://api.groq.com/openai/v1/chat/completions";

$messages = [
    [
        "role" => "system",
        "content" => "Anda adalah asisten virtual Smart Arca Music School di Weleri, Kendal. Tugas Anda menjawab pertanyaan calon murid dengan ramah, ceria, dan informatif. Beritahu mereka bahwa kita punya kelas Drum, Keyboard, Gitar, Bas, dan Vokal. Jika ditanya pendaftaran, arahkan ke Mbak Fia di WA: 0895360796038."
    ],
    [
        "role" => "user",
        "content" => $userMessage
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => $model,
    "messages" => $messages
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$reply = $result['choices'][0]['message']['content'] ?? 'Maaf, otak Arca sedang beristirahat sebentar. Coba lagi ya!';

echo json_encode(['reply' => $reply]);
